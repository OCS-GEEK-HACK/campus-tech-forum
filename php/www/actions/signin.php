<?php
session_start(); // セッション開始
require_once('../lib/connect-db.php'); // DB接続処理 (PDOインスタンスを返すファイル)

// POSTリクエストの処理
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // フォームから受け取ったデータを取得
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // 入力バリデーション
    if (empty($email) || empty($password)) {
        $_SESSION['error_message'] = "メールアドレスとパスワードを入力してください。";
        header("Location: /auth/signin");
        exit();
    }

    try {
        // メールアドレスでユーザー情報を取得
        $stmt = $pdo->prepare("SELECT id, name, displayname, email, password FROM users WHERE email = :email LIMIT 1");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // ユーザー情報が存在し、パスワードが一致するか確認
        if ($user && password_verify($password, $user['password'])) {
            // 認証成功: セッションにユーザー情報を保存
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_displayName'] = $user['displayname'];
            $_SESSION['user_email'] = $user['email'];

            // エラーメッセージをリセット
            $_SESSION['error_message'] = '';

            //ログイン後のリダイレクト
            header("Location: /");
            exit();
        } else {
            $_SESSION['error_message'] = "メールアドレスまたはパスワードが間違っています。";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "データベースエラーが発生しました。";
    }

    // エラーがある場合はサインイン画面にリダイレクト
    header("Location: /auth/signin");
    exit();
}