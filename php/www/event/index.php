<?php
require_once("../lib/session-check.php");
require_once("../lib/connect-db.php");
require_once("../components/header/index.php");
require_once("../components/sidebar/index.php");
require_once("../components/content-card/index.php");

// データベースからイベント情報を取得
try {
    $sql = "SELECT id, title, tags, event_date, location, description, user_id FROM events ORDER BY event_date DESC"; // 最新4件のイベントを取得
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC); // イベント情報を配列として取得
} catch (PDOException $e) {
    $_SESSION['errors'] = ['データベースエラーが発生しました: ' . $e->getMessage()];
    exit;
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>オーシャン掲示板 - イベント共有</title>
    <?php require_once('../lib/bootstrap.php'); ?>
    <?php
    require_once('../lib/socket.io/index.php');
    $socket_io = new SocketIO("event");
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
            <section class="p-4 d-flex flex-column gap-3 container">
                <?php if (!empty($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                        <?php unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                <div class="d-flex w-100 justify-content-between">
                    <h4 class="mb-0">イベント一覧</h4>
                    <a href="/event/create/" class="btn btn-dark d-block"><i class="fas fa-plus"></i> 投稿する</a>
                </div>

                <?php if (empty($events)): ?>
                    <p>イベントはまだありません。</p>
                <?php else: ?>
                    <div id="content-card-container">
                        <?php foreach ($events as $event): ?>
                            <?php
                            // イベント詳細を表示するためにContentCardを使う
                            $content_card = new ContentCard($event);
                            $content_card->render();
                            ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </section>
        </div>
    </div>

    <template id="event-template">
        <a href="" class="text-decoration-none text-dark">
            <section class="mw-100 card rounded p-3 mb-2 border">
                <h5 class="m-0"></h5>

                <h6 class="m-0 pt-2 fw-light"><i class="fa-solid fa-tag"></i> </h6>

                <p class="w-75 pt-2 m-0"></p>
                <h7 class="pt-2"></h7>
            </section>
        </a>
    </template>
</body>

</html>