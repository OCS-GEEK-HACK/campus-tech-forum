<?php
require_once("../lib/connect-db.php");
require_once("../components/header/index.php");
require_once("../components/sidebar/index.php");
require_once("../components/article-card/index.php");
require_once("../components/content-card/index.php");
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>学内掲示板</title>
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

            <section class="p-4">
                <h4>最近の投稿</h4>
                <?php
                    for ($i = 0; $i < 4; $i++):
                        $input_box = new ContentCard();
                        $input_box->render();
                    endfor;
                ?>
            </section>


        </div>
    </div>
</body>

</html>