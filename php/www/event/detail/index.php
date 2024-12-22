<?php
require_once("../../lib/session-check.php");
require_once("../../lib/connect-db.php");
require_once("../../components/header/index.php");
require_once("../../components/sidebar/index.php");

// 初期化
$event = null;
$event_id = null;
$comments = null;
$is_participating = false;
$participants = [];

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

    if ($event_id && isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // 現在のユーザーが参加済みか確認
        $sql_check_participation = "SELECT id FROM event_participants WHERE event_id = :event_id AND user_id = :user_id LIMIT 1";
        $stmt_check = $pdo->prepare($sql_check_participation);
        $stmt_check->bindParam(':event_id', $event_id, PDO::PARAM_INT);
        $stmt_check->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_check->execute();
        $is_participating = $stmt_check->fetch(PDO::FETCH_ASSOC) !== false;
    }

    $sql_participants = "SELECT u.displayname, u.image, u.id FROM event_participants ep
                     JOIN users u ON ep.user_id = u.id
                     WHERE ep.event_id = :event_id";
    $stmt_participants = $pdo->prepare($sql_participants);
    $stmt_participants->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmt_participants->execute();
    $participants = $stmt_participants->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['errors'] = ['データベースエラーが発生しました: ' . $e->getMessage()];
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>オーシャン掲示板 - イベント詳細</title>
    <?php require_once('../../lib/bootstrap.php'); ?>
    <?php
    require_once('../../lib/socket.io-comments/index.php');
    $socket_io = new SocketIOMessage("event");
    $socket_io->render();
    ?>
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

                    <!-- 参加/キャンセルボタン -->
                    <form action="/actions/event-participation.php" method="POST" class="mb-3 text-end">
                        <input type="hidden" name="event_id" value="<?= $event_id; ?>">
                        <?php if ($is_participating): ?>
                            <button type="submit" name="action" value="cancel" class="btn btn-danger">
                                <i class="fas fa-times me-1"></i> キャンセルする
                            </button>
                        <?php else: ?>
                            <button type="submit" name="action" value="join" class="btn btn-primary">
                                <i class="fas fa-check me-1"></i> 参加する
                            </button>
                        <?php endif; ?>

                    </form>

                    <!-- 参加者一覧 -->
                    <h3 class="mt-4"><i class="fas fa-users me-2"></i>参加者一覧</h3>
                    <div class="d-flex flex-wrap gap-3">
                        <?php if ($participants): ?>
                            <?php foreach ($participants as $participant): ?>
                                <a href="/user/?user_id=<?= htmlspecialchars($participant['id'], ENT_QUOTES, 'UTF-8') ?>" class="text-dark text-decoration-none">
                                    <div class="d-flex justify-content-start align-items-center gap-2">
                                        <?php if (!empty($participant['image'])): ?>
                                            <img src="<?= htmlspecialchars($participant['image'], ENT_QUOTES, 'UTF-8') ?>" class="rounded-circle border object-fit-cover" style="width: 50px; height: 50px;" alt="<?= htmlspecialchars($participant['displayname'], ENT_QUOTES, 'UTF-8') ?>">
                                        <?php else: ?>
                                            <div class="rounded-circle border d-flex justify-content-center align-items-center" style="width: 50px; height: 50px; background-color: #e9ecef;">
                                                <i class="fas fa-user text-muted" style="font-size: 24px;"></i>
                                            </div>
                                        <?php endif; ?>
                                        <p class="small m-0"><i class="fas fa-user me-1"></i><?= htmlspecialchars($participant['displayname'], ENT_QUOTES, 'UTF-8') ?></p>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted"><i class="fas fa-info-circle me-2"></i>このイベントにはまだ参加者がいません。</p>
                        <?php endif; ?>
                    </div>

                    <!-- コメント一覧 -->
                    <h2 class="mt-5"><i class="fas fa-comments me-2"></i>コメント一覧</h2>
                    <div id="comments" class="mt-4">
                        <?php if ($comments): ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="card comment mb-3 p-3 border rounded">
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
                    <form action="/actions/event-comment.php" method="POST" class="mt-4">
                        <div class="mb-3">
                            <textarea name="content" class="form-control" rows="5" placeholder="コメントを入力してください..." required></textarea>
                        </div>
                        <input type="hidden" name="event_id" value="<?= $event_id; ?>">
                        <div class="d-flex justify-content-end gap-3">
                            <a href="/event" class="btn btn-secondary">
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
                    <?php if (!empty($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($_SESSION['success']) ?>
                            <?php unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- エラー表示 -->
                    <div class="alert alert-danger">
                        <strong>エラーが発生しました。</strong> イベント情報が見つかりませんでした。
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <template id="event-comment-template">
        <div class="card comment mb-3 p-3 border rounded">
            <strong class="comment-name"></strong>
            <pre class="comment-comment"></pre>
            <small class="comment-createdat text-muted"></small>
        </div>
    </template>

</body>

</html>