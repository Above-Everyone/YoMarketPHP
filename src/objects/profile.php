<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
}

class Profile
{
	public $username;           // string
    public $password;           // string
	
    public $yoworld;            // string
	public $yoworld_id;         // string
	public $net_worth;          // string
	public $badges;             // Array[Badges]
	
    public $discord;            // string
	public $discord_id;         // string
	
    public $facebook;           // string
	public $facebook_id;        // string
	
    public $display_badges;     // bool
	public $display_worth;      // bool
	public $display_invo;       // bool
	public $display_fs;         // bool
	public $display_wtb;        // bool
	public $display_activity;   // bool
	
    public $activites;          // Array[Activity]
    public $invo;               // Array[Item]
	public $fs_list;            // Array[FS]
	public $wtb_list;           // Array[WTB]
        
    function __construct(array | string $acc_info)
    {
        if(is_string($acc_info)) {
            $this->parse_content($acc_info);
            $acc_info = explode(",", explode("\n", $acc_info)[0]);
        }

        if(count($acc_info_arr) < 8)
            return;

        $this->username = $acc_info[0] ?? "";
        $this->password = $acc_info[1] ?? "";
        $this->yoworld = $acc_info[2] ?? "";
        $this->yoworld_id = $acc_info[3] ?? "";
        $this->net_worth = $acc_info[4] ?? "";
        $this->discord = $acc_info[5] ?? "";
        $this->discord_id = $acc_info[6] ?? "";
        $this->facebook = $acc_info[7] ?? "";
        $this->facebook_id = $acc_info[8] ?? "";
    }

    /*
        parse_content(string $content): void

        Description:
            Breaking down and parsing the profile/auth endpoint
    */
    public function parse_content(string $content): void
    {
        $lines = explode("\n", $content);
        $this->set_displays($lines);
        $this->parse_activities($lines);
    }

    /* 
        set_displays(array $arr): void

        Description:
            Parsing the following line from the profile/auth endpoint via array

            false,false,false,false,false,false
    */
    public function set_displays(array $arr): void 
    {
        $this->display_badges = arr[0];  
        $this->display_worth = arr[1];   
        $this->display_invo = arr[2];    
        $this->display_fs = arr[3];      
        $this->display_wtb = arr[4];     
        $this->display_activity = arr[5];
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
        $this->badges = array();
        $start = false;
        
        foreach($lines as $line)
        {
            if(str_starts_with($line, "@ACTIVITIES"))
                $start = true;
            
            if(str_starts_with($line, "@INVENTORY"))
                break;

            if($start)
                array_push($this->activity, (new Activity(explode(",", $line))));
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

