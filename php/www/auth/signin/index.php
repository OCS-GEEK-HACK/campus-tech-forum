<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // セッションがある場合はホームへリダイレクト
    header("Location: /");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>学内掲示板アプリ - サインイン</title>
    <?php require_once('../../lib/bootstrap.php'); ?>
</head>

<body  class="vh-100">
    <div class="container  h-100 d-flex justify-content-center align-items-center">
        <!-- フォーム部分 -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-center">
                <h2>学内掲示板アプリ</h2>
                <h6>アカウントを作成またはログインしてください</h6>
                </div>
                <form method="POST" action="../../actions/signin.php">
                    <!-- メールアドレス -->
                    <div class="mb-3">
                        <label for="email" class="form-label w-100">メールアドレス</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="example@domain.com" required>
                    </div>
                    <!-- パスワード -->
                    <div class="mb-3">
                        <label for="password" class="form-label w-100">パスワード</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="********" required>
                    </div>
                    <?php if (!empty($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger">
                            <strong>エラー:</strong> <?= htmlspecialchars($_SESSION['error_message']) ?>
                        </div>
                        <?php unset($_SESSION['error_message']); ?>
                    <?php endif; ?>

                    <!-- 送信ボタン -->
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-dark  w-100">
                            サインイン
                        </button>
                    </div>
                    <a href="../signup/" class="text-decoration-none"><h6 class="text-center mt-3">アカウントをお持ちでない方</h6></a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>

