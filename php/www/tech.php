<?php
require_once("./lib/connect-db.php");
require_once("./components/header/index.php");
require_once("./components/sidebar/index.php");
require_once("./components/article-card/index.php");
require_once("./components/input-box/index.php");
require_once("./components/input-content/index.php");
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>学内掲示板</title>
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
        <div class="content w-100 ms-4">
            <?php 
                $header = new Header();
                $header->render();
            ?>

            <section>
                <h4>技術探求掲示板</h4>
                <?php 
                    $input_box = new InputBox();
                    $input_box->render();
                ?>
                <?php 
                    $input_content = new InputContent();
                    $input_content->render();
                ?>
            </section>


        </div>
    </div>
</body>

</html>