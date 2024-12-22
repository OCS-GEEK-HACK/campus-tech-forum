<?php
require_once("../lib/session-check.php");
require_once("../lib/connect-db.php");
require_once("../components/header/index.php");
require_once("../components/sidebar/index.php");
require_once("../components/content-card-idea/index.php");
try {
    $sql = "SELECT id, title, tags, description, user_id FROM ideas ORDER BY updated_at DESC"; // 最新4件のイベントを取得
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
    <title>オーシャン掲示板 - アイデア共有</title>
    <?php require_once('../lib/bootstrap.php'); ?>
    <?php
    require_once('../lib/socket.io/index.php');
    $socket_io = new SocketIO("idea");
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
                    <h4>アイデア一覧</h4>
                    <a href="/idea/create/" class="btn btn-dark d-block"><i class="fas fa-plus"></i> 投稿する</a>
                </div>

                <?php if (empty($ideas)): ?>
                    <p>アイデアはまだありません。</p>
                <?php else: ?>
                    <div id="content-card-container">
                        <?php foreach ($ideas as $idea): ?>
                            <?php
                            $content_card = new ContentCard($idea);
                            $content_card->render();
                            ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </section>

        </div>
    </div>

    <template id="idea-template">
        <a href="" class="text-decoration-none text-dark">
            <section class="mw-100 rounded p-3 mb-2 border">
                <h5 class="m-0"></h5>

                <h6 class="m-0 pt-2 fw-light"><i class="fa-solid fa-tag"></i> </h6>

                <p class="w-75 pt-2 m-0"></p>
            </section>
        </a>
    </template>
</body>

</html>