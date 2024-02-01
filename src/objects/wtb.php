<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("items.php");

class WTB
{
    public $item;
    public $wtb_price;
	public $buyer_confirmation;
	public $seller_confirmation;
	public $confirmed_transaction;
    public $posted_timestamp;

    function __construct(array $item)
    {
        $this->item                 = new Item($item);
        $this->wtb_price            = $item[ count($item) - 4 ];
        $this->seller_confirmation  = $item[ count($item) - 3 ];
        $this->buyer_confirmation   = $item[ count($item) - 2 ];
        $this->posted_timestamp     = $item[ count($item) - 1 ];

        $this->confirmed_transaction = false;
        if($this->seller_confirmation != "false" && $this->buyer_confirmation != "false")
            $this->confirmed_transaction = true;
    }
}

?>