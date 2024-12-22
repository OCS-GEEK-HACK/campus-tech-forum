<?php
require_once("../lib/session-check.php"); // セッションチェック
require_once("../lib/connect-db.php");   // DB接続

// コメントの送信先イベントIDと内容のバリデーション
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['idea_id'], $_POST['content']) && is_numeric($_POST['idea_id']) && !empty($_POST['content'])) {
        $idea_id = $_POST['idea_id'];
        $content = trim($_POST['content']);
        $user_id = $_SESSION['user_id']; // セッションからユーザーIDを取得
        $display_name = $_SESSION['user_displayName']; // ユーザー名も取得

        if (!$content) {
            // 空白で投稿できない場合
            $_SESSION['errors'] = ['空白で投稿できません。'];
            header('Location: /idea/detail/index.php?idea_id=' . $idea_id);
            exit;
        }

        try {
            // イベントIDの存在確認
            $sql = "SELECT id FROM ideas WHERE id = :idea_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idea_id', $idea_id, PDO::PARAM_INT);
            $stmt->execute();
            $idea = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$idea) {
                // イベントが存在しない場合
                $_SESSION['errors'] = ['指定されたイベントは存在しません。'];
                header('Location: /idea/detail/index.php?idea_id=' . $idea_id);
                exit;
            }

            // コメントを `idea_comments` テーブルに挿入
            $sql = "INSERT INTO idea_comments (idea_id, user_id, content, created_at, updated_at) 
                    VALUES (:idea_id, :user_id, :content, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                    RETURNING id, content
                    ";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idea_id', $idea_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt->execute();

            // コメントの投稿が成功した場合
            $postData = $stmt->fetch(PDO::FETCH_ASSOC); // 投稿データを取得

            // コメントをSocket.ioサーバーへ送信
            $url = "http://express:3000/new_idea_comment"; // SocketサーバーのURL
            $data = json_encode([
                'idea_id' => $idea_id,
                'user_name' => htmlspecialchars($display_name),
                'content' => htmlspecialchars($postData["content"]),
                'created_at' => date('Y/m/d H:i', strtotime('now')),
                'comment_id' => $postData["id"]
            ]);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_exec($ch);
            curl_close($ch);

            // セッションメッセージ
            $_SESSION['success'] = ['コメントが投稿されました。'];
            header('Location: /idea/detail/index.php?idea_id=' . $idea_id); // イベント詳細ページへリダイレクト
            exit;

        } catch (PDOException $e) {
            // エラー発生時
            $_SESSION['errors'] = ['コメント投稿中にエラーが発生しました: ' . $e->getMessage()];
            header('Location: /idea/detail/index.php?idea_id=' . $idea_id);
            exit;
        }
    } else {
        // 必須項目が不足している場合
        $_SESSION['errors'] = ['不正な入力があります。'];
        header('Location: /idea/detail/index.php?idea_id=' . $_POST['idea_id']);
        exit;
    }
} else {
    // POSTメソッドでないアクセスはリダイレクト
    header('Location: /idea');
    exit;
}
?>
