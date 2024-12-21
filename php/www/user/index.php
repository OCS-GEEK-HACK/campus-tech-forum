<?php
require_once("../lib/session-check.php");
require_once("../lib/connect-db.php");
require_once("../components/header/index.php");
require_once("../components/sidebar/index.php");
if (isset($_GET["user_id"]) && is_numeric($_GET['user_id'])) {
    $id = $_GET["user_id"];
} else {
    $id = $_SESSION["user_id"];
}
$stmt = $pdo->prepare("SELECT id, name, displayname, image, bio, github_url, x_url ,portfolie_url FROM users WHERE id = :id LIMIT 1");
$stmt->bindValue(':id', $id, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>オーシャン掲示板 - プロフィール</title>
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
            <section class="p-4 container">
                <?php if (empty($user)) : ?>
                    <div class="alert alert-danger">
                        <strong>エラー:</strong> ユーザーが存在しません。
                    </div>
                <?php else : ?>
                    <section class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex flex-<?= $id === $_SESSION["user_id"] ? "md" : "sm" ?>-row flex-column gap-3 align-items-start">
                                <!-- プロフィール画像またはアイコン -->
                                <div class="col-auto m-auto">
                                    <?php if (empty($user['image'])): ?>
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border"
                                            style="width: 100px; height: 100px;">
                                            <i class="fa-solid fa-user fs-1 text-muted"></i>
                                        </div>
                                    <?php else: ?>
                                        <img src="<?= htmlspecialchars($user['image']) ?>"
                                            class="rounded-circle border object-fit-cover"
                                            alt="アイコン"
                                            style="width: 100px; height: 100px;"
                                            onerror="this.onerror=null;this.src='/default-avatar.png';">
                                    <?php endif; ?>
                                </div>
                                <!-- ユーザー情報 -->
                                <div class="col">
                                    <h3 class="mb-0"><?= htmlspecialchars($user['displayname']) ?></h3>
                                    <p class="text-secondary mb-1">@<?= htmlspecialchars($user['name']) ?></p>
                                    <p class="mb-2 text-break"><?= !empty($user['bio']) ? nl2br(htmlspecialchars($user['bio'])) : "" ?></p>
                                    <ul class="d-flex flex-wrap mb-0 p-0 gap-2">
                                        <?php if (!empty($user['github_url'])): ?>
                                            <li class="list-inline-item m-0">
                                                <a class="btn btn-outline-dark btn-sm" href="<?= htmlspecialchars($user['github_url']) ?>" target="_blank">
                                                    <i class="fa-brands fa-github"></i> GitHub
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (!empty($user['x_url'])): ?>
                                            <li class="list-inline-item m-0">
                                                <a class="btn btn-outline-dark btn-sm" href="<?= htmlspecialchars($user['x_url']) ?>" target="_blank">
                                                    <i class="fa-solid fa-x"></i> X
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (!empty($user['portfolie_url'])): ?>
                                            <li class="list-inline-item m-0">
                                                <a class="btn btn-outline-dark btn-sm" href="<?= htmlspecialchars($user['portfolie_url']) ?>" target="_blank">
                                                    <i class="fa-solid fa-globe"></i> ポートフォリオ
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <?php if ($id === $_SESSION["user_id"]): ?>
                                    <!-- 編集ボタン -->
                                    <div class="m-md-auto ms-auto">
                                        <a href="/user/edit" class="btn btn-primary">
                                            <i class="fa-solid fa-pen-to-square me-2"></i> プロフィールを編集
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>
            </section>
        </div>
    </div>
</body>

</html>