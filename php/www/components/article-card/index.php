<?php

class ArticleCard
{
    private $title = "";
    private $content = "";
    private $link = "#";
    private $date = "";

    public function __construct(
        $title,
        $content,
        $link,
        $date
    ) {
        $this->title = $title;
        $this->content = $content;
        $this->link = $link;
        $this->date = $date;
    }
    public function render()
    {
        // includeで渡す値を変数に格納
        $title = $this->title;
        $content = $this->content;
        $link = $this->link;
        $date = $this->date;
        include 'element.php';
    }
}
