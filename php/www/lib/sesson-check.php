<?php
function session_check() {
    // セッションの開始
    if(!isset($_SESSION)){ session_start(); }
    // セッションにuser_idが存在するかチェック
    if (!isset($_SESSION['user_id'])) {
        // セッションがない場合はサインインへリダイレクト
        header("Location: /auth/signin");
        exit;
    }
    // セッションがある場合は何も処理しない
};

session_check();