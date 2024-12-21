<?php
// セッションチェック & DB接続
require_once("../lib/session-check.php");
require_once("../lib/connect-db.php");

// POSTリクエストのチェック
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /event');
    exit;
}

// POSTデータの受け取り
$title = trim($_POST['title'] ?? '');
$tags = trim($_POST['tags'] ?? '');
$event_date = trim($_POST['event_date'] ?? '');
$location = trim($_POST['location'] ?? '');
$description = trim($_POST['description'] ?? '');

// バリデーション
$errors = [];
if (empty($title)) $errors['title'] = 'タイトルは必須項目です。';
if (empty($tags)) $errors['tags'] = 'タグは必須項目です。';
if (empty($event_date)) {
    $errors['event_date'] = '日程は必須項目です。';
} elseif (!strtotime($event_date)) {
    $errors['event_date'] = '日程の形式が不正です。';
}
if (empty($location)) $errors['location'] = '場所は必須項目です。';
if (empty($description)) $errors['description'] = '内容は必須項目です。';

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $_POST;
    header('Location: /event/create');
    exit;
}

// ユーザーIDの取得
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    $_SESSION['errors'] = ['ログインが必要です。'];
    header('Location: /auth/signin');
    exit;
}

// タグを配列形式に変換し、PostgreSQL形式に整形
$tags_array = explode(',', $tags);
$tags_array = array_map('trim', $tags_array);
$tags_sql_array = '{' . implode(',', $tags_array) . '}';

try {
    // トランザクションの開始
    $pdo->beginTransaction();

    // イベントのINSERT
    $sql = "INSERT INTO events (user_id, title, tags, event_date, location, description) 
            VALUES (:user_id, :title, :tags, :event_date, :location, :description) RETURNING id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':tags', $tags_sql_array, PDO::PARAM_STR);
    $stmt->bindParam(':event_date', $event_date, PDO::PARAM_STR);
    $stmt->bindParam(':location', $location, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->execute();

    // 挿入されたイベントIDを取得
    $event_id = $stmt->fetchColumn();

    // イベント作成者をデフォルトで参加者に追加
    $participant_sql = "INSERT INTO event_participants (event_id, user_id) VALUES (:event_id, :user_id)";
    $participant_stmt = $pdo->prepare($participant_sql);
    $participant_stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $participant_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $participant_stmt->execute();

    // トランザクションをコミット
    $pdo->commit();

    // Socket.ioサーバーへの通知処理
    $eventData = [
        'id'          => $event_id,
        'title'       => htmlspecialchars($title),
        'tags'        => htmlspecialchars($tags),
        'event_date'  => htmlspecialchars($event_date),
        'location'    => htmlspecialchars($location),
        'description' => htmlspecialchars($description),
        'createdAt'   => date('Y/m/d H:i'),
        'createdBy'   => $_SESSION['user_displayName']
    ];
    $url = "http://express:3000/new_event";
    $data = json_encode($eventData);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_exec($ch);
    curl_close($ch);

    // 成功メッセージとリダイレクト
    $_SESSION['success'] = 'イベントを作成しました！';
    header('Location: /event');
    exit;
} catch (PDOException $e) {
    // トランザクションのロールバック
    $pdo->rollBack();
    $_SESSION['errors'] = ['データベースエラーが発生しました: ' . $e->getMessage()];
    header('Location: /event/create');
    exit;
}
