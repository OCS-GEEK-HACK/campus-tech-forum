<?php
require_once("./lib/session-check.php");
require_once("./lib/connect-db.php");
require_once("./components/header/index.php");
require_once("./components/sidebar/index.php");
require_once("./components/article-card/index.php");
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>学内掲示板アプリ</title>
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

            <!-- 最新のイベント -->
            <section class="mt-4 p-3">
                <h4 class="mb-3">最新のイベント</h4>
                <div class="row">
                    <?php for ($i = 0; $i < 3; $i++) : ?>
                        <?php
                            $card = new ArticleCard("プログラミングコンテスト", "学生向けプログラミングコンテスト。優勝者には豪華賞品あり！", "#", "2024年5月15日 @ オンライン");
                            $card->render();
                        ?>
                    <?php endfor; ?>
                </div>
            </section>

            <!-- 最新のアイデア -->
            <section class="mt-4 p-3">
                <h4 class="mb-3">最新のアイデア</h4>
                <div class="row">
                    <?php for ($i = 0; $i < 4; $i++) : ?>
                        <?php
                            $card = new ArticleCard("新しいアイデア", "学生同士で新しいプロジェクトのアイデアを共有しませんか？", "#", "2024年5月20日 @ 校内");
                            $card->render();
                        ?>
                    <?php endfor; ?>
                </div>
            </section>
        </div>
    </div>
</body>

</html>