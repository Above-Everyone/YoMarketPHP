<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once("items.php");

enum Activity_T 
{
    case null;

	case item_sold;
	case item_bought;
	case item_viewed;

	case price_change;
    case logged_in;

    case fs_posted;
    case wtb_posted;
    case invo_posted;

    public static function type2str(Activity_T $act_t): string 
    {
        switch($act_t) 
        {
            case Activity_T::item_sold:
                return "item_sold";
            case Activity_T::item_bought:
                return "item_bought";
            case Activity_T::item_viewed:
                return "item_viewed";
            case Activity_T::price_change:
                return "price_change";
            case Activity_T::fs_posted:
                return "fs_posted";
            case Activity_T::wtb_posted:
                return "wtb_posted";
            case Activity_T::invo_posted:
                return "invo_posted";
        }
        
        return "";
    }

    public static function type2humanstr(Activity_T $act_t): string 
    {
        switch($act_t) 
        {
            case Activity_T::item_sold:
                return "has sold";
            case Activity_T::item_bought:
                return "has bought";
            case Activity_T::item_viewed:
                return "has viewed";
            case Activity_T::price_change:
                return "has changed";
            case Activity_T::logged_in:
                return "has logged on";
            case Activity_T::fs_posted:
                return "is selling";
            case Activity_T::wtb_posted:
                return "is looking for";
            case Activity_T::invo_posted:
                return "has added";
        }
        
        return "";
    }
    
    public static function isActValid(string $q): bool 
    {
        $check = array("item_sold", "item_bought", "item_viewed", "price_change", "fs_posted", "wtb_posted", "logged_in", "invo_posted");
        if(in_array($q, $check))
            return true;

        return false;
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

    function __construct(array $arr)
    {
        /*
        1,SOLD,Cupids Bow and Arrow,26295,https://yw-web.yoworld.com/cdn/items/26/29/26295/26295_60_60.gif,950m,2024/01/28-10:54:06,550m,UNCONFIRMED,UNCONFIRMED,TIMESTAMP
        2,BOUGHT,Cupids Bow and Arrow,26295,https://yw-web.yoworld.com/cdn/items/26/29/26295/26295_60_60.gif,950m,2024/01/28-10:54:06,550m,UNCONFIRMED,UNCONFIRMED,TIMESTAMP
        3,VIEWED,Cupids Bow and Arrow,26295,https://yw-web.yoworld.com/cdn/items/26/29/26295/26295_60_60.gif,950m,2024/01/28-10:54:06,TIMESTAMP
        4,CHANGED,Cupids Bow and Arrow,26295,https://yw-web.yoworld.com/cdn/items/26/29/26295/26295_60_60.gif,950m,2024/01/28-10:54:06,950m,TIMESTAMP
        5,FS_POSTED,Cupids Bow and Arrow,26295,https://yw-web.yoworld.com/cdn/items/26/29/26295/26295_60_60.gif,950m,2024/01/28-10:54:06,950m,TIMESTAMP
        6,WTB_POSTED,Cupids Bow and Arrow,26295,https://yw-web.yoworld.com/cdn/items/26/29/26295/26295_60_60.gif,950m,2024/01/28-10:54:06,950m,TIMESTAMP
        */
        $this->i_idx = $arr[0];
        $this->act_t = Activity::str2type($arr[1]);
        $this->timestamp = $arr[count($arr)-1];

        if (count($arr) > 1) {
            switch(count($arr) > 7) 
            {
                case 9:
                    $this->price = $arr[count($arr)-2];
                case 11:
                    $this->seller_confirmation = $arr[count($arr)-3];
                    $this->buyer_confirmation = $arr[count($arr)-2];
                    $this->item = (new Item(array_slice($arr, 2, 6)));
                    $this->price = $arr[7];
                    break;
            } 
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
            case "logged_in":
                return Activity_T::logged_in;
            case "fs_posted":
                return Activity_T::fs_posted;
            case "wtb_posted":
                return Activity_T::wtb_posted;
            case "invo_posted":
                return Activity_T::invo_posted;
        }

        return Activity_T::null;
    }
}

?>