<?php
require_once("../lib/session-check.php");
require_once("../lib/connect-db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'], $_POST['action'])) {
    $event_id = (int)$_POST['event_id'];
    $user_id = $_SESSION['user_id'];

    try {
        if ($_POST['action'] === 'join') {
            // 参加処理
            $sql_join = "INSERT INTO event_participants (event_id, user_id) VALUES (:event_id, :user_id)";
            $stmt_join = $pdo->prepare($sql_join);
            $stmt_join->bindParam(':event_id', $event_id, PDO::PARAM_INT);
            $stmt_join->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt_join->execute();
            $_SESSION['messages'] = ['イベントに参加しました！'];
        } elseif ($_POST['action'] === 'cancel') {
            // キャンセル処理
            $sql_cancel = "DELETE FROM event_participants WHERE event_id = :event_id AND user_id = :user_id";
            $stmt_cancel = $pdo->prepare($sql_cancel);
            $stmt_cancel->bindParam(':event_id', $event_id, PDO::PARAM_INT);
            $stmt_cancel->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt_cancel->execute();
            $_SESSION['messages'] = ['イベント参加をキャンセルしました。'];
        }
    } catch (PDOException $e) {
        $_SESSION['errors'] = ['操作に失敗しました: ' . $e->getMessage()];
    }
}

// 元のページにリダイレクト
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
