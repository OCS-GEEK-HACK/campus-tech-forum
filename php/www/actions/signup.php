<?php
session_start(); // セッション開始
require_once('../lib/connect-db.php'); // DB接続処理 (PDOインスタンスを返すファイル)

// POSTリクエストの処理
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // フォームから受け取ったデータを取得
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    // $password_confirm = $_POST['password_confirm'] ?? '';

    // 入力バリデーション
    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error_message'] = "すべての項目を入力してください。";
        header("Location: /auth/signup/");
        exit();
    }

    // if ($password !== $password_confirm) {
    //     $_SESSION['error_message'] = "パスワードが一致しません。";
    //     header("Location: /auth?mode=signup");
    //     exit();
    // }

    try {
        // メールアドレスの重複確認
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $_SESSION['error_message'] = "このメールアドレスはすでに登録されています。";
            header("Location: /auth/signup/");
            exit();
        }

        // パスワードのハッシュ化 (bcryptを使ったPostgreSQLのハッシュ方式に合わせる)
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // ユーザーの新規登録
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->execute();

        // 登録成功時はエラーメッセージをリセット
        $_SESSION['error_message'] = '';

        // ログインしてトップページにリダイレクト
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;

        header("Location: /");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "データベースエラーが発生しました。";
    }

    // エラーがある場合はサインアップ画面にリダイレクト
    header("Location: /auth/signup/");
    exit();
}