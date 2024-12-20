<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    // セッションがない場合はサインインへリダイレクト
    header("Location: /auth/signin");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>学内掲示板 - サインアウト</title>
    <?php require_once('../../lib/bootstrap.php'); ?>
</head>

<body  class="vh-100">
    <div class="container  h-100 d-flex justify-content-center align-items-center">
        <!-- フォーム部分 -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-center">
                <h2>学内掲示板アプリ</h2>
                <h6>サインアウトしますか？</h6>
                </div>
                <div class="d-flex justify-content-center mb-3">
                        <button type="button" onclick="location.href='../../actions/signout.php'" class="btn btn-dark  w-100">
                            サインイン
                        </button>
                </div>
                <div class="d-flex justify-content-center">
                        <button type="button" onclick="history.back()" class="btn btn-light  w-100">
                            キャンセル
                        </button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

