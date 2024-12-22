<?php

class ContentCard {
    private $idea;

    // コンストラクタでイベント情報を受け取る
    public function __construct($idea) {
        $this->idea = $idea;
    }

    // イベントカードをレンダリング
    public function render() {
        $idea = $this->idea;
        include 'element.php';  // element.phpで表示
    }
}

?>