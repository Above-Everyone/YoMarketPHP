<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("utils.php");

class YW_INFO_LOGS
{		
    public $price;
    public $approve;
    public $approved_by;
    public $timestamp;

    function YW_INFO_LOGS(string $p, bool $a, string $a_by, string $time)
    {
        $this->price = $p;
        $this->approve = $a;
        $this->approved_by = $a_by;
        $this->timestamp = $time;
    }

    /* Parsing Yoworld.Info's Prices After Item Information */
    public static function parse_prices(string $content): YW_INFO_LOGS
    {
        $yw_db_price = array();
        $lines = explode("\n", $content);

        foreach($lines as $line) 
        {
            if(strlen($line) > 3)
                array_push($yw_db_price, (new YW_INFO_LOGS(explode(",", $line))));
        }

        return $yw_db_price;
    }
}

?>