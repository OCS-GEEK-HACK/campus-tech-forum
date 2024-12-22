<?php
require_once("../../lib/session-check.php");
require_once("../../lib/connect-db.php");
require_once("../../components/header/index.php");
require_once("../../components/sidebar/index.php");

// 初期化
$idea = null;
$idea_id = null;
$comments = null;

// イベントIDが存在しなかった場合のエラー処理
if (!isset($_GET['idea_id']) || !is_numeric($_GET['idea_id'])) {
    $_SESSION['errors'] = ['不正なアイデアIDです。'];
} else {
    $idea_id = (int)$_GET['idea_id'];
}

try {
    // イベント詳細のデータを取得するSQLクエリ
    $sql = "SELECT id, title, tags, description, user_id 
            FROM ideas WHERE id = :idea_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idea_id', $idea_id, PDO::PARAM_INT);
    $stmt->execute();
    $idea = $stmt->fetch(PDO::FETCH_ASSOC);

    // イベントが存在しなかった場合のエラー処理
    if (!$idea) {
        $_SESSION['errors'] = ['指定されたアイデアは存在しません。'];
    }

    // コメント一覧を取得するSQLクエリ
    $sql_comments = "SELECT ec.content, ec.created_at, u.displayName 
                     FROM idea_comments ec
                     JOIN users u ON ec.user_id = u.id
                     WHERE ec.idea_id = :idea_id
                     ORDER BY ec.created_at DESC";
    $stmt_comments = $pdo->prepare($sql_comments);
    $stmt_comments->bindParam(':idea_id', $idea_id, PDO::PARAM_INT);
    $stmt_comments->execute();
    $comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['errors'] = ['データベースエラーが発生しました: ' . $e->getMessage()];
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>オーシャン掲示板 - アイデア詳細</title>
    <?php require_once('../../lib/bootstrap.php'); ?>
    <?php require_once('../../lib/socket.io-comments.php'); ?>
    <link rel="stylesheet" href="/style/main.css">
</head>

<body>
    <div class="d-flex">
        <?php
        $sidebar = new Sidebar();
        $sidebar->render();
        ?>

        <!-- メインコンテンツ -->
        <div class="content w-100 ms-md-4">
            <?php
            $header = new Header();
            $header->render();
            ?>

            <div class="container py-3">
                <?php if ($idea): ?>
                    <!-- イベント詳細情報 -->
                    <h1 class="mb-4"><?= htmlspecialchars($idea['title'], ENT_QUOTES, 'UTF-8'); ?></h1>

                    <!-- タグ部分 -->
                    <div class="mb-3">
                        <i class="fa-solid fa-tag"></i> <strong>タグ:</strong>
                        <?php
                        $tags = explode(',', trim($idea['tags'], '{}')); // {}を削除してカンマで分割
                        foreach ($tags as $tag): ?>
                            <span class="badge bg-primary me-2"><?= htmlspecialchars(trim($tag), ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <hr>

                    <p class="mt-4"><?= nl2br(htmlspecialchars($idea['description'], ENT_QUOTES, 'UTF-8')); ?></p>

                    <hr>

                    <!-- コメント一覧 -->
                    <h2 class="mt-5"><i class="fas fa-comments me-2"></i>コメント一覧</h2>
                    <div id="comments" class="mt-4">
                        <?php if ($comments): ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment mb-3 p-3 border rounded">
                                    <strong><?= htmlspecialchars($comment['displayname'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <pre><?= htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8'); ?></pre>
                                    <small class="text-muted"><?= date('Y/m/d H:i', strtotime($comment['created_at'])); ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted"><i class="fas fa-info-circle me-2"></i>コメントはまだありません。最初のコメントを投稿しましょう！</p>
                        <?php endif; ?>
                    </div>

                    <!-- コメント投稿フォーム -->
                    <h2 class="mt-5"><i class="fas fa-pen me-2"></i>コメントを投稿する</h2>
                    <form action="/actions/idea-comment.php" method="POST" class="mt-4">
                        <div class="mb-3">
                            <textarea name="content" class="form-control" rows="5" placeholder="コメントを入力してください..." required></textarea>
                        </div>
                        <input type="hidden" name="idea_id" value="<?= $idea_id; ?>">
                        <div class="d-flex justify-content-end gap-3">
                            <a href="/idea" class="btn btn-secondary">
                                戻る
                            </a>
                            <button type="submit" class="btn btn-dark">投稿する</button>
                        </div>
                    </form>
                    <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                        <div class="alert alert-danger my-2">
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <p class="m-0"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endforeach; ?>
                            <?php unset($_SESSION['errors']); // 表示後にクリア 
                            ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- エラー表示 -->
                    <div class="alert alert-danger">
                        <strong>エラーが発生しました。</strong> アイデア情報が見つかりませんでした。
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <template id="idea-comment-template">
        <div class="comment mb-3 p-3 border rounded">
            <strong class="comment-name"></strong>
            <pre class="comment-comment"></pre>
            <small class="comment-createdat text-muted"></small>
        </div>
    </template>

</body>

</html>