<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class YW_INFO_LOGS
{		
    public $price;
    public $approve;
    public $approved_by;
    public $timestamp;

    function __construct(array $arr)
    {
        $this->price        = $arr[0];
        $this->approve      = $arr[1];
        $this->approved_by  = $arr[2];
        $this->timestamp    = $arr[3];
    }

    /* Parsing Yoworld.Info's Prices After Item Information */
    public static function parse_prices(string $content): array
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