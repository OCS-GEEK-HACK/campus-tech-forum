<?php
require_once("../../lib/sesson-check.php");
require_once("../../lib/connect-db.php");
require_once("../../components/header/index.php");
require_once("../../components/sidebar/index.php");

// 初期化
$event = null;
$event_id = null;

// イベントIDが存在しなかった場合のエラー処理
if (!isset($_GET['event_id']) || !is_numeric($_GET['event_id'])) {
    $_SESSION['errors'] = ['不正なイベントIDです。'];
} else {
    $event_id = (int)$_GET['event_id'];
}

try {
    // イベント詳細のデータを取得するSQLクエリ
    $sql = "SELECT id, title, tags, event_date, location, description, user_id 
            FROM events WHERE id = :event_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmt->execute();
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    // イベントが存在しなかった場合のエラー処理
    if (!$event) {
        $_SESSION['errors'] = ['指定されたイベントは存在しません。'];
    }
} catch (PDOException $e) {
    $_SESSION['errors'] = ['データベースエラーが発生しました: ' . $e->getMessage()];
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>イベント詳細</title>
    <?php require_once('../../lib/bootstrap.php'); ?>
    <link rel="stylesheet" href="/style/main.css">
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
            <div class="container mt-5">
                <?php if ($event): ?>
                    <h1 class="mb-4"><?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p><strong>タグ:</strong> <?= htmlspecialchars(implode(', ', explode(',', $event['tags'])), ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>場所:</strong> <?= htmlspecialchars($event['location'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>日程:</strong> <?= htmlspecialchars($event['event_date'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><?= nl2br(htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8')); ?></p>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <strong>エラーが発生しました。</strong> イベント情報が見つかりませんでした。
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>