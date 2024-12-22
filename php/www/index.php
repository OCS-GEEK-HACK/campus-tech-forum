<?php
require_once("./lib/session-check.php");
require_once("./lib/connect-db.php");
require_once("./components/header/index.php");
require_once("./components/sidebar/index.php");
require_once("./components/article-card/index.php");

try {
    $sql = "SELECT id, title, tags, event_date, location, description, user_id FROM events ORDER BY event_date DESC LIMIT 3"; // 最新4件のイベントを取得
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC); // イベント情報を配列として取得
    $sql = "SELECT id, title, tags, description, user_id, updated_at FROM ideas ORDER BY updated_at DESC LIMIT 3"; // 最新4件のイベントを取得
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ideas = $stmt->fetchAll(PDO::FETCH_ASSOC); // イベント情報を配列として取得
} catch (PDOException $e) {
    $_SESSION['errors'] = ['データベースエラーが発生しました: ' . $e->getMessage()];
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>オーシャン掲示板</title>
    <?php require_once('./lib/bootstrap.php'); ?>
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
            <div class="container">
                <!-- 最新のイベント -->
                <section class="mt-4 p-3">
                    <h4 class="mb-3">最新のイベント</h4>
                    <div class="row">
                        <?php if (empty($events)): ?>
                            <p>イベントはまだありません。</p>
                        <?php else: ?>
                            <div id="content-card-container" class="row">
                                <?php foreach ($events as $event): ?>
                                    <?php
                                    $card = new ArticleCard($event["title"], $event["description"], "/event/detail?event_id=" . $event["id"], date('Y/m/d H:i', strtotime($event["event_date"])) . "@" . $event["location"]);
                                    $card->render();
                                    ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
                <section class="mt-4 p-3">
                    <h4 class="mb-3">最新のアイデア</h4>
                    <div class="row">
                        <?php if (empty($ideas)): ?>
                            <p>アイデアはまだありません。</p>
                        <?php else: ?>
                            <div id="content-card-container" class="row">
                                <?php foreach ($ideas as $idea): ?>
                                    <?php
                                    $card = new ArticleCard($idea["title"], $idea["description"], "/idea/detail?idea_id=" . $idea["id"], date('Y/m/d H:i', strtotime($idea["updated_at"])));
                                    $card->render();
                                    ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

            </div>
        </div>
    </div>
</body>

</html>