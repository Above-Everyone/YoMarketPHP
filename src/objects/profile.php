<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("activity.php");
require_once("items.php");
require_once("fs.php");
require_once("wtb.php");
require_once("utils.php");

enum Settings_T
{
	case null;
	case username;
	case password;
	case yoworld;
	case yoworld_id;
	case net_worth;
	case discord;
	case discord_id;
	case facebook;
	case facebook_id;

	case display_badges;
	case display_worth;
	case display_invo;
	case display_fs;
	case display_wtb;
	case display_activity;

	case add_activity;

	case add_to_invo;
	case add_to_fs;
	case add_to_wtb;
	case rm_from_invo;
	case rm_from_fs;
	case rm_from_wtb;

    public static function action2str(Settings_T $act_t): string 
    {
        switch($act_t)
        {
            case Settings_T::add_to_fs:
                return "fs";
            case Settings_T::add_to_wtb:
                return "wtb";
            case Settings_T::add_to_invo:
                return "invo";
            case Settings_T::rm_from_fs:
                return "fs";
            case Settings_T::rm_from_wtb:
                return "wtb";
            case Settings_T::rm_from_invo:
                return "invo";
        }
        return "";
    }
}

enum Badges 
{
    case NONE;
    case VERIFIED;
    case TRUSTED;
    case LEGIT_COOKIE_BUYER;
    case LEGIT_COOKIE_SELLER;
    case REPUTATION;

    case AE;
    case PNKM;
    case NMDZ;

    case MANAGER;
    case ADMIN;
    case DEVELOPER;
    case OWNER;
}

class Profile
{
	public $username;           // string
    public $password;           // string
    public $ip;
	
    public $yoworld;            // string
	public $yoworld_id;         // string
	public $net_worth;          // string
	public $badges;             // Array[Badges]
	
    public $discord;            // string
	public $discord_id;         // string
	
    public $facebook;           // string
	public $facebook_id;        // string
	
    public $display_info;
    public $display_badges;     // bool
	public $display_worth;      // bool
	public $display_invo;       // bool
	public $display_fs;         // bool
	public $display_wtb;        // bool
	public $display_activity;   // bool
	
    public $activities;          // Array[Activity]
    public $invo;               // Array[Item]
	public $fs_list;            // Array[FS]
	public $wtb_list;           // Array[WTB]

    public $raw_data;
        
    function __construct(array | string $acc_info)
    {
        if(is_string($acc_info)) {
            $this->raw_data = $acc_info;
            $lines = explode("\n", $acc_info);
            $content = trim(str_replace($lines[0], "", $acc_info));
            $this->parse_content($content);
            $acc_info = explode(",", $lines[0]);
        }

        if(count($acc_info) < 8) {
            $this->username     = $acc_info[0] ?? "";
            $this->yoworld      = $acc_info[1] ?? "";
            $this->yoworld_id   = $acc_info[2] ?? "";
            $this->net_worth    = $acc_info[3] ?? "";
            $this->discord      = $acc_info[4] ?? "";
            $this->discord_id   = $acc_info[5] ?? "";
            $this->facebook     = $acc_info[6] ?? "";
            $this->facebook_id  = $acc_info[7] ?? "";
            return;
        }

        $this->username     = $acc_info[0] ?? "";
        $this->password     = $acc_info[1] ?? "";
        $this->yoworld      = $acc_info[2] ?? "";
        $this->yoworld_id   = $acc_info[3] ?? "";
        $this->net_worth    = $acc_info[4] ?? "";
        $this->discord      = $acc_info[5] ?? "";
        $this->discord_id   = $acc_info[6] ?? "";
        $this->facebook     = $acc_info[7] ?? "";
        $this->facebook_id  = $acc_info[8] ?? "";
    }

    /*
        parse_content(string $content): void

        Description:
            Breaking down and parsing the profile/auth endpoint
    */
    public function parse_content(string $content): void
    {
        $lines = explode("\n", $content);
        $this->set_displays(explode(",", $lines[0]));
        $this->set_badges(explode(",", remove_strings($lines[1], array(" "))));
        $this->parse_activities($lines);
        $this->parse_invo($lines);
        $this->parse_fs($lines);
        $this->parse_wtb($lines);
    }

    /* 
        set_displays(array $arr): void

        Description:
            Parsing the following line from the profile/auth endpoint via array

            false,false,false,false,false,false
    */
    public function set_displays(array $arr): void 
    {
        $this->display_info         = $arr[0];
        $this->display_badges       = $arr[1];  
        $this->display_worth        = $arr[2];   
        $this->display_invo         = $arr[3];    
        $this->display_fs           = $arr[4];      
        $this->display_wtb          = $arr[5];     
        $this->display_activity     = $arr[6];
    }

    /* 
        set_badges(array $arr): void 

        Description:
            Parsing the following line from the profile/auth endpoint via array

            trusted, admin, owner
    */
    public function set_badges(array $arr): void 
    {
        $this->badges = array();
        foreach($arr as $badge)
        {
            switch($badge)
            {
                case "trusted":
                    array_push($this->badges, Badges::TRUSTED);
                    break;
                case "admin":
                    array_push($this->badges, Badges::ADMIN);
                    break;
                case "owner":
                    array_push($this->badges, Badges::OWNER);
                    break;
                case "developer":
                    array_push($this->badges, Badges::DEVELOPER);
                    break;
                case "verfied":
                    array_push($this->badges, Badges::VERIFIED);
                    break;
                case "ae":
                    array_push($this->badges, Badges::AE);
                    break;
                case "pnkm":
                    array_push($this->badges, Badges::PNKM);
                    break;
                case "legit_cookie_seller":
                    array_push($this->badges, Badges::LEGIT_COOKIE_SELLER);
                    break;
                case "legit_cookie_buyer":
                    array_push($this->badges, Badges::LEGIT_COOKIE_BUYER);
                    break;
                case "REPUTATION":
                    array_push($this->badges, Badges::REPUTATION);
                    break;
            }
        }
    }

    /*
        parse_activities(array $lines): void

        Description:
            Parsing the following lines from the profile/auth endpoint via array of lines

            @ACTIVITIES
            1,item_sold,Cupids Bow and Arrow,26295,https://yw-web.yoworld.com/cdn/items/26/29/26295/26295_60_60.gif,950m,2024/01/28-10:54:06,550m,false,false,TIMESTAMP
            2,item_bought,Cupids Bow and Arrow,26295,https://yw-web.yoworld.com/cdn/items/26/29/26295/26295_60_60.gif,950m,2024/01/28-10:54:06,550m,false,false,TIMESTAMP
            3,item_viewed,Cupids Bow and Arrow,26295,https://yw-web.yoworld.com/cdn/items/26/29/26295/26295_60_60.gif,950m,2024/01/28-10:54:06,,TIMESTAMP
            4,price_change,Cupids Bow and Arrow,26295,https://yw-web.yoworld.com/cdn/items/26/29/26295/26295_60_60.gif,950m,2024/01/28-10:54:06,950m,TIMESTAMP
    */
    public function parse_activities(array $lines): void
    {
        $this->activities = array();
        $start = false;
        
        foreach($lines as $act_line)
        {
            if(remove_strings($act_line, array(" ", "\n")) === "@ACTIVITIES") {
                $start = true; }
            
            if(remove_strings($act_line, array(" ", "\n")) == "@INVENTORY" || strpos($act_line, "@INVENTORY") !== false) {
                break; }

            $line_info = explode(",", $act_line);
            if($start) {
                if(count($line_info) > 2) {
                    $num = (int)$line_info[0];
                    if(!Activity_T::isActValid($line_info[1]))
                        continue;
                    
                    if( $num > 0 ) { array_push($this->activities, (new Activity($line_info))); }
                }
            }
        }
    }

    /*
        parse_inv(array $lines): void

        Description:
            Parsing the following lines from the profile/auth endpoint via array of lines

            @Inventory
            Cupids Bow and Arrow,26295,https://yw-web.yoworld.com/cdn/items/26/29/26295/26295_60_60.gif,950m,2024/01/28-10:54:06
    */
    public function parse_invo(array $lines): void
    {
        $this->invo = array();
        $start = false;
        foreach($lines as $line)
        {
            if(remove_strings($line, array(" ", "\n")) == "@INVENTORY") {
                $start = true; }
            
            if(remove_strings($line, array(" ", "\n")) === "@FS" || strpos($line, "@FS") !== false) {
                break; }

            if($start) {
                if(count(explode(",", $line)) > 4) {
                    array_push($this->invo, (new Item(explode(",", $line))));
                }   
            }
        }
    }

    /*
        parse_fs(array $lines): void

        Description:
            Parsing the following lines from the profile/auth endpoint via array of lines

            @Inventory
            Cupids Bow and Arrow,26295,https://yw-web.yoworld.com/cdn/items/26/29/26295/26295_60_60.gif,950m,2024/01/28-10:54:06,0,0,false,,,,,UNCONFIRMED,TIMESTAMP
    */
    public function parse_fs(array $lines): void
    {
        $this->fs_list = array();
        $start = false;

        foreach($lines as $line)
        {
            if(remove_strings($line, array(" ", "\n")) === "@FS") {
                $start = true; }
            
            if(remove_strings($line, array(" ", "\n")) === "@WTB" || strpos($line, "@WTB") !== false) {
                break; }

            if($start) {
                $fs_item_info = explode(",", $line);
                // echo "\r\n\r\n";
                // var_dump($fs_item_info);
                if(count($fs_item_info) > 7) {
                    array_push($this->fs_list, (new FS(explode(",", $line))));
                }
            }
        }
    }

    /*
        parse_wtb(array $lines): void

        Description:
            Parsing the following lines from the profile/auth endpoint via array of lines

            @Inventory
            Cupids Bow and Arrow,26295,https://yw-web.yoworld.com/cdn/items/26/29/26295/26295_60_60.gif,950m,2024/01/28-10:54:06,0,0,false,,,,,UNCONFIRMED,TIMESTAMP
    */
    public function parse_wtb(array $lines): void
    {
        $this->wtb_list = array();
        $start = false;
        
        foreach($lines as $line)
        {
            if(remove_strings($line, array(" ", "\n")) === "@WTB") {
                $start = true; }
            
            if(remove_strings($line, array(" ", "\n")) === "") {
                break; }

            if($start) {
                $wtb_item_info = explode(",", $line);
                if(count($wtb_item_info) > 7) {
                    array_push($this->wtb_list, (new WTB($wtb_item_info)));
                }
            }
        }
    }

    /*
        info2cookie(): string

        Description:
            Using array format string to set and retrieve cookies
    */
    public function info2cookie(): string
    {
        return "$this->username,$this->password,$this->yoworld,$this->yoworld_id,$this->net_worth,$this->discord,$this->discord_id,$this->facebook,$this->facebook_id";
    }

    /*
        is_FbID(): bool

        Description:
            Validating FB ID or Username
    */
    public function is_FbID(): bool 
    {
        if((int)$this->facebook_id > 0)
            return true;
        return false;
    }

    /*
        act_t_2str(Activity_T $act_t): string

        Description:
            Convert an Activity_T property to string for front-end displays
    */
    public static function act_t_2str(Activity_T $act_t): string 
    {
        return Activity_T::type2str($act_t);
    }
}

