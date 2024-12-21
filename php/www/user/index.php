<?php
require_once("../lib/sesson-check.php");
require_once("../lib/connect-db.php");
require_once("../components/header/index.php");
require_once("../components/sidebar/index.php");
if(isset($_GET["user_id"])){
    $id = $_GET["user_id"];
}else{
    $id = $_SESSION["user_id"];
}
$stmt = $pdo->prepare("SELECT id, name, displayname, image, bio, github_url, x_url ,portfolie_url  FROM users WHERE id = :id LIMIT 1");
$stmt->bindValue(':id', $id, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>学内掲示板アプリ - プロフィール</title>
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
                <section class="mw-100 rounded p-3 mb-4 border">
                    <div class="container">
                        <div class="row">
                            <div class="col-2">
                            <?php if (empty($user['image'])):?>
                                <i class="fa-solid fa-user fs-1"></i>
                            <?php else:?>
                                <img src=<?=$user['image']?> class="rounded-circle border" alt="アイコン" width="100%" height="100%">
                            <?php endif;?>
                            </div>
                            <div class="col-10">
                                <h3><?php echo $user['displayname']?></h3>
                                <h7 class="text-secondary">@<?php echo $user['name']?></h7>
                                <p><?php echo $user['bio']?></p>
                                <ul class="list-group list-unstyled list-group-horizontal">
                                    <?php if (!empty($user['github_url'])):?>
                                    <li class="border rounded m-1"><i class="fa-brands fa-github"></i><?php echo $user['github_url']?></li>
                                    <?php endif;?>
                                    <?php if (!empty($user['x_url'])):?>
                                    <li class="border rounded m-1"><i class="fa-solid fa-x"></i><?php echo $user['x_url']?></li>
                                    <?php endif;?>
                                    <?php if (!empty($user['portfolie_url'])):?>
                                    <li class="border rounded m-1"><?php echo $user['portfolie_url']?></li>
                                    <?php endif;?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>
            </section>
        </div>
    </div>
</body>

</html>