<?php
require_once("../lib/session-check.php"); // セッションチェック
require_once("../lib/connect-db.php");   // DB接続

// コメントの送信先イベントIDと内容のバリデーション
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['event_id'], $_POST['content']) && is_numeric($_POST['event_id']) && !empty($_POST['content'])) {
        $event_id = $_POST['event_id'];
        $content = trim($_POST['content']);
        $user_id = $_SESSION['user_id']; // セッションからユーザーIDを取得
        $display_name = $_SESSION['user_displayName']; // ユーザー名も取得

        if (!$content) {
            // 空白で投稿できない場合
            $_SESSION['errors'] = ['空白で投稿できません。'];
            header('Location: /event/detail/index.php?event_id=' . $event_id);
            exit;
        }

        try {
            // イベントIDの存在確認
            $sql = "SELECT id FROM events WHERE id = :event_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
            $stmt->execute();
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                // イベントが存在しない場合
                $_SESSION['errors'] = ['指定されたイベントは存在しません。'];
                header('Location: /event/detail/index.php?event_id=' . $event_id);
                exit;
            }

            // コメントを `event_comments` テーブルに挿入
            $sql = "INSERT INTO event_comments (event_id, user_id, content, created_at, updated_at) 
                    VALUES (:event_id, :user_id, :content, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                    RETURNING id, content
                    ";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt->execute();

            // コメントの投稿が成功した場合
            $postData = $stmt->fetch(PDO::FETCH_ASSOC); // 投稿データを取得

            // コメントをSocket.ioサーバーへ送信
            $url = "http://express:3000/new_event_comment"; // SocketサーバーのURL
            $data = json_encode([
                'event_id' => $event_id,
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
            header('Location: /event/detail/index.php?event_id=' . $event_id); // イベント詳細ページへリダイレクト
            exit;

        } catch (PDOException $e) {
            // エラー発生時
            $_SESSION['errors'] = ['コメント投稿中にエラーが発生しました: ' . $e->getMessage()];
            header('Location: /event/detail/index.php?event_id=' . $event_id);
            exit;
        }
    } else {
        // 必須項目が不足している場合
        $_SESSION['errors'] = ['不正な入力があります。'];
        header('Location: /event/detail/index.php?event_id=' . $_POST['event_id']);
        exit;
    }
} else {
    // POSTメソッドでないアクセスはリダイレクト
    header('Location: /event');
    exit;
}
?>
