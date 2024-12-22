<?php
require_once("../../lib/session-check.php");
require_once("../../lib/connect-db.php");
require_once("../../components/header/index.php");
require_once("../../components/sidebar/index.php");
require_once("../../components/article-card/index.php");
require_once("../../components/content-card/index.php");
$errors = $_SESSION['errors'] ?? [];
$old_input = $_SESSION['old'] ?? []; // 入力されたデータを保持しておくための変数
unset($_SESSION['errors'], $_SESSION['old']); // 1回だけ使うので、ここで消す
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>オーシャン掲示板 - アイデア作成</title>
    <?php require_once('../../lib/bootstrap.php'); ?>
    <link rel="stylesheet" href="/style/main.css">
    <style>
        @media (max-width: 767px) {
            form.shadow {
                box-shadow: none !important;
            }
        }
    </style>
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
                    <h2>アイデアを作成</h2>
                </div>

                <!-- アイデア作成フォーム -->
                <form action="/actions/create-idea.php" method="POST" class="card p-none shadow rounded">
                    <!-- タイトル -->
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label w-100">アイデアタイトル</label>
                            <input type="text" name="title" id="title" class="form-control"
                                placeholder="例: PostgreSQLハンズオンセミナー"
                                value="<?= htmlspecialchars($old_input['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            <?php if (!empty($errors['title'])): ?>
                                <p class="text-danger"><?= htmlspecialchars($errors['title'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                        </div>
                        <!-- タグ -->
                        <div class="mb-3">
                            <label for="tags" class="form-label w-100">タグ</label>
                            <input type="text" name="tags" id="tags" class="form-control"
                                placeholder="例: セミナー, ハンズオン, PostgreSQL"
                                value="<?= htmlspecialchars($old_input['tags'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            <small class="form-text text-muted">カンマ区切りで複数のタグを入力してください。</small>
                            <?php if (!empty($errors['tags'])): ?>
                                <p class="text-danger"><?= htmlspecialchars($errors['tags'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                        </div>
                        <!-- 内容 -->
                        <div class="mb-3">
                            <label for="description" class="form-label w-100">内容</label>
                            <textarea name="description" id="description" class="form-control" rows="5"
                                placeholder="イベントの詳細を入力してください" required><?= htmlspecialchars($old_input['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                            <?php if (!empty($errors['description'])): ?>
                                <p class="text-danger"><?= htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                        </div>
                        <!-- 送信ボタン -->
                        <div class="d-flex justify-content-end gap-3">
                            <a href="/event" class="btn btn-secondary">
                                キャンセル
                            </a>
                            <button type="submit" class="btn btn-dark">
                                <i class="fas fa-plus"></i> アイデアを作成
                            </button>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</body>

</html>