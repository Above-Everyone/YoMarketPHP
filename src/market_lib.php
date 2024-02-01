<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
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
    const PROFILE_ENDPOINT      = "https://api.yomarket.info/profile?username=";
    const AUTH_ENDPOINT         = "https://api.yomarket.info/profile/auth?username=";
    const SETTINGS_ENDPOINT     = "https://api.yomarket.info/profile/edit/settings?data=";
    const LIST_ADD_ENDPOINT     = "https://api.yomarket.info/profile/edit/add?username=";
    const LIST_RM_ENDPOINT      = "https://api.yomarket.info/profile/edit/rm?username=";
    public $Response;

    public $profile;
    public $username;
    function Profiles(string $user)
    {
        $this->username = $user;
    }

    public function searchProfile(string $user, string $ip, string $viewed_by = ""): Response 
    {
        $parameters = array("ip" => $ip);

        if(!empty($viewed_by))
        {
            echo "HERE";
            $parameters = array("ip" => "$ip", "viewed_by" => "$viewed_by");
        }
        
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

    public function addItem(string $user, string $password, string $ip, string $itemID, string $price, Settings_T $list): Response
    {
        $list_t = Settings_T::action2str($list);

        $api_resp = sendReq(self::LIST_ADD_ENDPOINT. $user, array("password" => $password, "id" => $itemID, "price" => $price, "list" => $list_t));
        
        if(str_contains($api_resp, "[ X ]"))
            return (new Response(ResponseType::NONE, 0));

        if(str_contains($api_resp, "[ + ] Item added!"))
            return (new Response(ResponseType::REQ_SUCCESS, true));

        return (new Response(ResponseType::NONE, 0));
    }

    public function rmItem(string $user, string $password, string $ip, string $itemID, Settings_T $llist): Response
    {
        $list_t = Settings_T::action2str($llist);

        $api_resp = sendReq(self::LIST_RM_ENDPOINT. $user, array("password" => $password, "id" => $itemID, "list" => $list_t));

        if(str_contains($api_resp, "[ X ]"))
            return (new Response(ResponseType::NONE, 0));

        if(str_contains($api_resp, "[ + ] Item added!"))
            return (new Response(ResponseType::REQ_SUCCESS, true));

        return (new Response(ResponseType::NONE, 0));
    }
}

?>