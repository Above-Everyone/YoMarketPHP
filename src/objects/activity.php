<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("item.php");

enum Activity_T 
{
    case null;
	case item_sold;
	case item_bought;
	case item_viewed;
	case price_change;

    public static function type2str(Activity_T $act_t): string 
    {
        switch($typ) 
        {
            case Activity_T::item_sold:
                return "item_sold";
            case Activity_T::item_bought:
                return "item_bought";
            case Activity_T::item_viewed:
                return "item_viewed";
            case Activity_T::price_change:
                return "price_change";
        }
        
        return "";
    }
}

class Activity 
{
    public $i_idx;
    public $act_t;
    public $item;
    public $price;
    public $timestamp;
    
    // validating trades
    public $seller_confirmation;
    public $buyer_confirmation;

    public Activity(array $arr)
    {
        $this->i_idx = $arr[0];
        $this->act_t = str2type($arr[1]);

        switch($acc_c) {
            case 9:
                $this->item = (new Item(array_slice($arr, 2, 6)));
                $this->price = $arr[7];
                $this->timestamp = $arr[8];
                break;
            case
        }
    }

    /*
        str2type(string $typ): Activity_T

        Description:
            Parsing API activity type from string to object 
    */
    public static function str2type(string $typ): Activity_T 
    {
        switch($typ) 
        {
            case "item_sold":
                return Activity_T::item_sold;
            case "item_bought":
                return Activity_T::item_bought;
            case "item_viewed": 
                return Activity_T::item_viewed;
            case "price_change":
                return Activity_T::price_change;
        }

        return Activity_T::null;
    }
}

?>