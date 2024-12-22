<?php

class SocketIOMessage
{
    private $mode = "";
    public function __construct($mode)
    {
        $this->mode = $mode;
    }
    public function render()
    {
        $mode = $this->mode;
        include 'js.php';
    }
}
