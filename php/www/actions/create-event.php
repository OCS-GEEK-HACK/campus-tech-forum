<?php
// セッションチェック & DB接続
require_once("../lib/sesson-check.php");
require_once("../lib/connect-db.php");

// POSTリクエストのチェック
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /event');
    exit;
}

// POSTデータの受け取り
$title = trim($_POST['title'] ?? '');
$tags = trim($_POST['tags'] ?? '');  // 入力されたタグ
$event_date = trim($_POST['event_date'] ?? '');
$location = trim($_POST['location'] ?? '');
$description = trim($_POST['description'] ?? '');

// バリデーション
$errors = [];

// タイトルのバリデーション
if (empty($title)) {
    $errors['title'] = 'タイトルは必須項目です。';
}

// タグのバリデーション
if (empty($tags)) {
    $errors['tags'] = 'タグは必須項目です。';
}

// 日程のバリデーション
if (empty($event_date)) {
    $errors['event_date'] = '日程は必須項目です。';
} elseif (!strtotime($event_date)) {
    $errors['event_date'] = '日程の形式が不正です。';
}

// 場所のバリデーション
if (empty($location)) {
    $errors['location'] = '場所は必須項目です。';
}

// 内容のバリデーション
if (empty($description)) {
    $errors['description'] = '内容は必須項目です。';
}

// エラーメッセージがある場合は、前のページにリダイレクトしてエラーメッセージを表示
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $_POST; // 入力内容をセッションに保存
    header('Location: /event/create');
    exit;
}

// ユーザーIDの取得（セッションから取得）
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    $_SESSION['errors'] = ['ログインが必要です。'];
    header('Location: /auth/signin');
    exit;
}

// タグを配列に変換し、PostgreSQLの配列形式に変換
$tags_array = explode(',', $tags);  // カンマ区切りで分割
$tags_array = array_map('trim', $tags_array);  // 各タグをトリム

// PostgreSQLの配列形式に変換
$tags_sql_array = '{' . implode(',', $tags_array) . '}';

// DB接続
try {
    // SQLの作成 (イベントのINSERT文)
    $sql = "INSERT INTO events (user_id, title, tags, event_date, location, description) 
            VALUES (:user_id, :title, :tags, :event_date, :location, :description)";
    
    // プリペアドステートメントの作成
    $stmt = $pdo->prepare($sql);

    // パラメータをバインド
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':tags', $tags_sql_array, PDO::PARAM_STR);  // 配列形式のタグ
    $stmt->bindParam(':event_date', $event_date, PDO::PARAM_STR);
    $stmt->bindParam(':location', $location, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);

    // SQLを実行
    $stmt->execute();

    // イベントの詳細情報を取得
    $eventData = [
        'title'       => htmlspecialchars($title),
        'tags'        => htmlspecialchars($tags),
        'event_date'  => htmlspecialchars($event_date),
        'location'    => htmlspecialchars($location),
        'description' => htmlspecialchars($description),
        'createdAt'   => date('Y/m/d H:i'),
        'createdBy'   => $_SESSION['user_displayName']
    ];

    // 新しいイベントをSocket.ioサーバーに送信
    $url = "http://express:3000/new_event";  // Socket.ioサーバーのURL
    $data = json_encode($eventData);

    // cURLでSocket.ioサーバーにデータを送信
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_exec($ch);
    curl_close($ch);

    // 正常終了したら、イベント一覧ページにリダイレクト
    $_SESSION['success'] = 'イベントを作成しました！';
    header('Location: /event');
    exit;
} catch (PDOException $e) {
    // 例外処理（DBエラーが発生した場合）
    $_SESSION['errors'] = ['データベースエラーが発生しました: ' . $e->getMessage()];
    header('Location: /event/create');
    exit;
}
