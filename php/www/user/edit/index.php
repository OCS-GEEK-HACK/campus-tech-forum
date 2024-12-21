<?php
require_once("../../lib/session-check.php");
require_once("../../lib/connect-db.php");
require_once("../../components/header/index.php");
require_once("../../components/sidebar/index.php");
$id = $_SESSION["user_id"];
$stmt = $pdo->prepare("SELECT id, name, displayname, image, bio, github_url, x_url, portfolie_url FROM users WHERE id = :id LIMIT 1");
$stmt->bindValue(':id', $id, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>学内掲示板アプリ - プロフィール編集</title>
    <?php require_once('../../lib/bootstrap.php'); ?>
    <link rel="stylesheet" href="/style/main.css">
    <script rel="stylesheet" src="/js/profile-edit.js"></script>
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
                <?php if (empty($user)) : ?>
                    <div class="alert alert-danger">
                        <strong>エラー:</strong> ユーザーが存在しません。
                    </div>
                <?php else : ?>
                    <section class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <form action="/actions/user-update.php" method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="displayname" class="form-label w-100">表示名</label>
                                    <input type="text" class="form-control" id="displayname" name="displayname" value="<?= htmlspecialchars($user['displayname']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="bio" class="form-label w-100">自己紹介</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="4"><?= !empty($user['bio']) ? htmlspecialchars($user['bio']) : "" ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="image" class="form-label w-100">プロフィール画像</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    <input type="hidden" name="image-base64" id="image-base64" value="<?= !empty($user['image']) ? htmlspecialchars($user['image']) : "" ?>">
                                    <div class="col-auto m-auto d-flex py-2 gap-3">
                                        <?php if (empty($user['image'])): ?>
                                            <div id="image-placeholder" class="rounded-circle bg-light d-flex align-items-center justify-content-center border"
                                                style="width: 100px; height: 100px;">
                                                <i class="fa-solid fa-user fs-1 text-muted"></i>
                                            </div>
                                            <img id="image-preview" src=""
                                                class="rounded-circle border object-fit-cover d-none"
                                                alt="アイコン"
                                                style="width: 100px; height: 100px;"
                                                onerror="this.onerror=null;this.src='/default-avatar.png';">
                                        <?php else: ?>
                                            <div id="image-placeholder" class="rounded-circle bg-light d-flex align-items-center justify-content-center border d-none"
                                                style="width: 100px; height: 100px;">
                                                <i class="fa-solid fa-user fs-1 text-muted"></i>
                                            </div>
                                            <img id="image-preview" src="<?= !empty($user['image']) ? htmlspecialchars($user['image']) : "" ?>"
                                                class="rounded-circle border object-fit-cover"
                                                alt="アイコン"
                                                style="width: 100px; height: 100px;"
                                                onerror="this.onerror=null;this.src='/default-avatar.png';">
                                        <?php endif; ?>
                                        <div class="my-auto">
                                            <button type="button" id="reset-image" class="btn btn-outline-secondary">画像をリセット</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="github_url" class="form-label w-100">GitHub URL</label>
                                    <input type="url" class="form-control" id="github_url" name="github_url" value="<?= !empty($user['github_url']) ? htmlspecialchars($user['github_url']) : "" ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="x_url" class="form-label w-100">X（旧Twitter）URL</label>
                                    <input type="url" class="form-control" id="x_url" name="x_url" value="<?= !empty($user['x_url']) ? htmlspecialchars($user['x_url']) : "" ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="portfolie_url" class="form-label w-100">ポートフォリオ URL</label>
                                    <input type="url" class="form-control" id="portfolie_url" name="portfolie_url" value="<?= !empty($user['portfolie_url']) ? htmlspecialchars($user['portfolie_url']) : "" ?>">
                                </div>
                                <div class="d-flex justify-content-end gap-3 mb-3">
                                    <a href="/user" class="btn btn-secondary">
                                        キャンセル
                                    </a>
                                    <button type="submit" class="btn btn-dark">保存</button>
                                </div>
                                <?php if (!empty($_SESSION['errors'])): ?>
                                    <?php foreach ($_SESSION['errors'] as $error): ?>
                                        <div class="alert alert-danger">
                                            <p class="m-0"><?= htmlspecialchars($error) ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php unset($_SESSION['errors']); ?>
                                <?php endif; ?>
                                <?php if (!empty($_SESSION['success'])): ?>
                                    <div class="alert alert-success">
                                        <?= htmlspecialchars($_SESSION['success']) ?>
                                        <?php unset($_SESSION['success']); ?>
                                    </div>
                                <?php endif; ?>
                            </form>
                        </div>
                    </section>
                <?php endif; ?>
            </section>
        </div>
    </div>
</body>

</html>