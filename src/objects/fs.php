<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once("items.php");

class FS 
{
    public $seller;
    public $item;
    public $fs_price;
	public $buyer_confirmation;
	public $seller_confirmation;
	public $confirmed_transaction;
    public $posted_timestamp;

    function __construct(array $item)
    {
        // Candy Blue Straight Hair,25318,https://yw-web.yoworld.com/cdn/items/25/31/25318/25318_60_60.gif,20-25m,2024/01/31-06:16:20,20m,false,false,2024/02/03-08:54:34
        //              0             1                                     2                                3             4           5    6      7        8
        $this->item                 = new Item($item);
        $this->fs_price             = $item[ count($item) - 4 ];
        $this->seller_confirmation  = $item[ count($item) - 3 ];
        $this->buyer_confirmation   = $item[ count($item) - 2 ];
        $this->posted_timestamp     = $item[ count($item) - 1 ];

        $this->confirmed_transaction = false;
        if($this->seller_confirmation != "false" && $this->buyer_confirmation != "false")
            $this->confirmed_transaction = true;
        
        if(count($item) == 10)
            $this->seller           = $item[ count($item) - 5 ];
    }
}

?>