<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once("objects/activity.php");
require_once("objects/utils.php");
require_once("objects/response.php");
require_once("objects/profile.php");
require_once("objects/fs.php");
require_once("objects/wtb.php");

class Profiles
{
    /*
        API Endpoints
    */
    const API                   = "https://api.yomarket.info/";
    const BACKUP_API            = "https://backup.yomarket.info/";
    const PROFILE_ENDPOINT      = "https://backup.yomarket.info/profile?username=";
    const AUTH_ENDPOINT         = "https://backup.yomarket.info/profile/auth?username=";
    const SETTINGS_ENDPOINT     = "https://backup.yomarket.info/profile/edit/settings?data=";
    const LIST_ADD_ENDPOINT     = "https://backup.yomarket.info/profile/edit/add?username=";
    const LIST_RM_ENDPOINT      = "https://backup.yomarket.info/profile/edit/rm?username=";
    CONST NEW_PROFILE_ENDPOINT  = "https://backup.yomarket.info/profile/create?username=";
    const ALL_USERS_ENDPOINT    = "https://backup.yomarket.info/profile/list_users";
    const ITEMS_FS_ENDPOINT     = "https://backup.yomarket.info/profile/list_items_fs";
    public $Response;

    public $profile;
    public $username;
    public function searchProfile(string $user, string $ip, string $viewed_by = ""): Response 
    {
        $parameters = array("ip" => $ip);

        if(!empty($viewed_by))
            $parameters = array("ip" => "$ip", "viewed_by" => "$viewed_by");
        
        $api_resp = sendReq(self::PROFILE_ENDPOINT. $user, $parameters);

        if(empty($api_resp))
            return (new Response(ResponseType::REQ_FAILED, 0));

        if($api_resp == "ERROR_MSG")
            return (new Response(ResponseType::NONE, 0));
        
        return (new Response(ResponseType::REQ_SUCCESS, (new Profile($api_resp))));
    }

    public function LoginAuth(string $user, string $password, string $ip): Response 
    {
        $api_resp = sendReq(self::AUTH_ENDPOINT. $user, array("password" => $password));

        if(empty($api_resp))
            return (new Response(ResponseType::REQ_FAILED, 0));

        if($api_resp == "ERROR_MSG")
            return (new Response(ResponseType::NONE, 0));

        $t = new Profile($api_resp);
        if($t->username != $user || $t->password != $password)
            return (new Response(ResponseType::INVALID_INFO, 0));
        
        return (new Response(ResponseType::LOGIN_SUCCESS, (new Profile($api_resp))));
    }

    public function createProfile(string $user, string $pword, string $ip, string $ywid): Response
    {
        $api_resp = sendReq(self::NEW_PROFILE_ENDPOINT. $user, array("password" => $pword, "id" => $ywid));

        if(empty($api_resp))
            return (new Response(ResponseType::REQ_FAILED, 0));

        if($api_resp === "ERROR_MSG")
            return (new Response(ResponseType::NONE, 0));

        if(str_contains($api_resp, "[ + ]"))
            return (new Response(ResponseType::REQ_SUCCESS, remove_strings($api_resp, array("[ + ] Account created....!"))));

        return (new Response(ResponseType::NONE, 0));
    }

    public function addItem(string $user, string $password, string $ip, string $itemID, string $price, Settings_T $list): Response
    {
        $list_t = Settings_T::action2str($list);

        $api_resp = sendReq(self::LIST_ADD_ENDPOINT. $user, array("password" => $password, "id" => $itemID, "price" => $price, "list" => $list_t));
        
        if(str_contains($api_resp, "[ X ]"))
            return (new Response(ResponseType::NONE, $api_resp));

        if(str_contains($api_resp, "[ + ] Item added!"))
            return (new Response(ResponseType::REQ_SUCCESS, $api_resp));

        return (new Response(ResponseType::NONE, $api_resp));
    }

    public function rmItem(string $user, string $password, string $ip, string $itemID, Settings_T $llist): Response
    {
        $listt_t = Settings_T::action2str($llist);
        
        $api_resp = sendReq(self::LIST_RM_ENDPOINT. $user, array("password" => $password, "id" => $itemID, "list" => $listt_t));

        if(empty($api_resp))
            return (new Response(ResponseType::REQ_FAILED, $api_resp));

        if(str_contains($api_resp, "[ X ]"))
            return (new Response(ResponseType::NONE, $api_resp));

        if(str_contains($api_resp, "[ + ] Item added!"))
            return (new Response(ResponseType::REQ_SUCCESS, "$api_resp $listt_t"));

        return (new Response(ResponseType::NONE, $api_resp));
    }

    public static function all_items_fs(): Response
    {
        $api_resp = sendReq(self::ITEMS_FS_ENDPOINT, array());

        if(empty($api_resp))
            return (new Response(ResponseType::REQ_FAILED, $api_resp));

        if(str_contains($api_resp, "[ X ]"))
            return (new Response(ResponseType::NONE, $api_resp));

        $lines = explode("\n", $api_resp);
        $items = array();

        foreach($lines as $line) {
            if(empty($line) || $line === " ") break;
            array_push($items, (new FS(explode(",", $line))));
        }

        return (new Response(ResponseType::REQ_SUCCESS, $items));
    }

    public static function list_users(): Response
    {
        $api_resp = sendReq(self::ALL_USERS_ENDPOINT, array());

        if(empty($api_resp))
            return (new Response(ResponseType::REQ_FAILED, 0));

        if(str_contains($api_resp, "[ X ]"))
            return (new Response(ResponseType::NONE, 0));

        $lines = explode("\n", $api_resp);

        $owner = array();
        $owner_capture = false;

        $c = 0;
        foreach($lines as $line) 
        {
            switch($line) {
                case "[@ADMINS]":
                    $owner_capture = false;
                    break;
                case "[@OWNER]":
                    $owner_capture = true;
                    continue 2;
                case ($owner_capture == true ? $line : "N/A"):
                    array_push($owner, $line);
                case "":
                    break;
            }
            $c++;
        }

        $admins = self::parse_admins($lines, $c);
        $users = self::parse_users($lines, $c);

        return (new Response(ResponseType::REQ_SUCCESS, array($owner, $admins, $users)));
    }

    public static function parse_admins(array $lines, int $c): array
    {
        $admins = array();
        $admin_capture = false;
        foreach($lines as $line) 
        {
            switch($line) {
                case "[@USERS]":
                    $admin_capture = false;
                    break;
                case "[@ADMINS]":
                    $admin_capture = true;
                    continue 2;
                case ($admin_capture == true ? $line : "N/A"):
                    array_push($admins, $line);
                case "":
                    break;
            }
            $c++;
        }
        return $admins;
    }

    public static function parse_users(array $lines, int $c): array 
    {
        $users = array();
        $user_capture = false;
        foreach($lines as $line) 
        {
            switch($line) {
                case "[@USERS]":
                    $user_capture = true;
                    continue 2;
                case ($user_capture == true ? $line : "N/A"):
                    array_push($users, $line);
                case "":
                    break;
            }
            $c++;
        }
        return $users;
    }
}

?>