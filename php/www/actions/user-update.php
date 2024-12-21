<?php
session_start();
require_once("../lib/session-check.php");
require_once("../lib/connect-db.php");

$errors = [];
$userId = $_SESSION["user_id"];

// POSTリクエストかチェック
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['errors'] = ["このリクエストは許可されていません。"];
    header("Location: /user/edit");
    exit;
}

define("MAX_IMAGE_SIZE", 2 * 1024 * 1024); // 2MB

$displayName = $_POST['displayname'] ?? '';
$bio = $_POST['bio'] ?? '';
$githubUrl = $_POST['github_url'] ?? '';
$xUrl = $_POST['x_url'] ?? '';
$portfolioUrl = $_POST['portfolie_url'] ?? '';
$imageBase64 = $_POST['image-base64'] ?? '';

if (empty(trim($displayName))) {
    $errors[] = "表示名を入力してください。";
}

if (!empty($imageBase64)) {
    $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageBase64), true);
    if ($decodedImage === false) {
        $errors[] = "無効な画像形式です。";
    } elseif (strlen($decodedImage) > MAX_IMAGE_SIZE) {
        $errors[] = "画像サイズは2MB以下にしてください。";
    }
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: /user/edit");
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE users SET displayname = :displayname, bio = :bio, github_url = :github_url, x_url = :x_url, portfolie_url = :portfolie_url, image = :image WHERE id = :id");
    $stmt->execute([
        ':displayname' => trim($displayName),
        ':bio' => trim($bio),
        ':github_url' => trim($githubUrl),
        ':x_url' => trim($xUrl),
        ':portfolie_url' => trim($portfolioUrl),
        ':image' => $imageBase64,
        ':id' => $userId
    ]);

    $_SESSION['success'] = "プロフィールが更新されました。";
    header("Location: /user/edit");
    exit;
} catch (PDOException $e) {
    $_SESSION['errors'] = ["更新に失敗しました。再度お試しください。"];
    header("Location: /user/edit");
    exit;
}
