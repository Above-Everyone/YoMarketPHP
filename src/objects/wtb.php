<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("item.php");

class WTB
{
    public $posted_timestamp;
    public $fs_price;
    public $item;

    function WTB(string $t, string $fs, Item $item)
    {
        $this->posted_timestamp = $t;
        $this->fs_price = $fs;
        $this->item = $item;
    }
}

?>