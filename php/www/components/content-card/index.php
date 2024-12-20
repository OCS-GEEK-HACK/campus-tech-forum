<?php

class ContentCard {
    private $event;

    // コンストラクタでイベント情報を受け取る
    public function __construct($event) {
        $this->event = $event;
    }

    // イベントカードをレンダリング
    public function render() {
        $event = $this->event;
        include 'element.php';  // element.phpで表示
    }
}

?>
