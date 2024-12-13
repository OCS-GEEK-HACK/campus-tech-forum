<?php

// .env ファイルから環境変数を読み込む
$databaseUrl = getenv('DATABASE_URL');  // DATABASE_URLを取得

// DATABASE_URLから接続情報をパース
$parsedUrl = parse_url($databaseUrl);

$host = $parsedUrl['host'];
$db = ltrim($parsedUrl['path'], '/');
$user = $parsedUrl['user'];
$pass = $parsedUrl['pass'];

// PostgreSQLへの接続
$dsn = "pgsql:host=$host;dbname=$db";
try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "接続失敗: " . $e->getMessage();
    exit;
}