<?php
require_once("../../lib/sesson-check.php");
require_once("../../lib/connect-db.php");
require_once("../../components/header/index.php");
require_once("../../components/sidebar/index.php");

// 初期化
$event = null;
$event_id = null;
$comments = null;

// イベントIDが存在しなかった場合のエラー処理
if (!isset($_GET['event_id']) || !is_numeric($_GET['event_id'])) {
    $_SESSION['errors'] = ['不正なイベントIDです。'];
} else {
    $event_id = (int)$_GET['event_id'];
}

try {
    // イベント詳細のデータを取得するSQLクエリ
    $sql = "SELECT id, title, tags, event_date, location, description, user_id 
            FROM events WHERE id = :event_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmt->execute();
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    // イベントが存在しなかった場合のエラー処理
    if (!$event) {
        $_SESSION['errors'] = ['指定されたイベントは存在しません。'];
    }

    // コメント一覧を取得するSQLクエリ
    $sql_comments = "SELECT ec.content, ec.created_at, u.displayName 
                     FROM event_comments ec
                     JOIN users u ON ec.user_id = u.id
                     WHERE ec.event_id = :event_id
                     ORDER BY ec.created_at DESC";
    $stmt_comments = $pdo->prepare($sql_comments);
    $stmt_comments->bindParam(':event_id', $event_id, PDO::PARAM_INT);
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
    <title>イベント詳細</title>
    <?php require_once('../../lib/bootstrap.php'); ?>
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
                <?php if ($event): ?>
                    <!-- イベント詳細情報 -->
                    <h1 class="mb-4"><?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8'); ?></h1>

                    <!-- タグ部分 -->
                    <div class="mb-3">
                        <i class="fa-solid fa-tag"></i> <strong>タグ:</strong>
                        <?php
                        $tags = explode(',', trim($event['tags'], '{}')); // {}を削除してカンマで分割
                        foreach ($tags as $tag): ?>
                            <span class="badge bg-primary me-2"><?= htmlspecialchars(trim($tag), ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endforeach; ?>
                    </div>

                    <!-- 詳細情報 -->
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt me-2"></i><strong>場所:</strong> <?= htmlspecialchars($event['location'], ENT_QUOTES, 'UTF-8'); ?></li>
                        <li><i class="fas fa-calendar-alt me-2"></i><strong>日程:</strong> <?= htmlspecialchars($event['event_date'], ENT_QUOTES, 'UTF-8'); ?></li>
                    </ul>

                    <hr>

                    <p class="mt-4"><?= nl2br(htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8')); ?></p>

                    <hr>

                    <!-- コメント一覧 -->
                    <h2 class="mt-5"><i class="fas fa-comments me-2"></i>コメント一覧</h2>
                    <div class="comments mt-4">
                        <?php if ($comments): ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment mb-3 p-3 border rounded">
                                    <strong><?= htmlspecialchars($comment['displayname'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <p><?= nl2br(htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8')); ?></p>
                                    <small class="text-muted"><?= date('Y/m/d H:i', strtotime($comment['created_at'])); ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>まだコメントはありません。</p>
                        <?php endif; ?>
                    </div>

                    <!-- コメント投稿フォーム -->
                    <h2 class="mt-5"><i class="fas fa-pen me-2"></i>コメントを投稿する</h2>
                    <form action="/actions/event-comment.php" method="POST" class="mt-4">
                        <div class="mb-3">
                            <textarea name="content" class="form-control" rows="5" placeholder="コメントを入力してください..." required></textarea>
                        </div>
                        <input type="hidden" name="event_id" value="<?= $event_id; ?>">
                        <div class="d-flex justify-content-end gap-3">
                            <a href="/event" class="btn btn-secondary">
                                キャンセル
                            </a>
                            <button type="submit" class="btn btn-dark">投稿する</button>
                        </div>
                    </form>
                <?php else: ?>
                    <!-- エラー表示 -->
                    <div class="alert alert-danger">
                        <strong>エラーが発生しました。</strong> イベント情報が見つかりませんでした。
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>
