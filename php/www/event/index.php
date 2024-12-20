<?php
require_once("../lib/sesson-check.php");
require_once("../lib/connect-db.php");
require_once("../components/header/index.php");
require_once("../components/sidebar/index.php");
require_once("../components/article-card/index.php");
require_once("../components/content-card/index.php");

// データベースからイベント情報を取得
try {
    $sql = "SELECT id, title, tags, event_date, location, description, user_id FROM events ORDER BY event_date DESC LIMIT 4"; // 最新4件のイベントを取得
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC); // イベント情報を配列として取得
} catch (PDOException $e) {
    $_SESSION['errors'] = ['データベースエラーが発生しました: ' . $e->getMessage()];
    header('Location: /event');
    exit;
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>学内掲示板アプリ - イベント共有</title>
    <?php require_once('../lib/bootstrap.php'); ?>
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
            <section class="p-4 d-flex flex-column gap-3">
                <div class="d-flex w-100 justify-content-between">
                    <h4 class="mb-0">最近のイベント</h4>
                    <a href="/event/create/" class="btn btn-dark d-md-block d-none"><i class="fas fa-plus"></i> 投稿する</a>
                </div>

                <?php if (empty($events)): ?>
                    <p>イベントはまだありません。</p>
                <?php else: ?>
                    <?php foreach ($events as $event): ?>
                        <?php
                        // イベント詳細を表示するためにContentCardを使う
                        $content_card = new ContentCard($event);
                        $content_card->render();
                        ?>
                    <?php endforeach; ?>
                <?php endif; ?>

            </section>
        </div>
    </div>
</body>

</html>