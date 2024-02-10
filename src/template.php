<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once("objects/response.php");

class TemplateGenerator 
{
    /*
        Functions for template information in web cookies
    */
    const GENERATE_TEMPLATE_ENDPOINT    = "https://backup.yomarket.info/template?items=";
    const RETRIEVE_TEMPLATE_ENDPOINT    = "https://backup.yomarket.info/get_template?name=";

    public static function GenerateTemplate(string $items, string $user): bool 
    {
        $api_resp = sendReq(self::GENERATE_TEMPLATE_ENDPOINT. $items, array("username" => $user));

        if(empty($api_resp))
            return (new Response(ResponseType::REQ_FAILED, $api_resp));

        if(str_contains($api_resp, "[ X ]"))
            return (new Response(ResponseType::NONE, $api_resp));

        if(str_contains($api_resp, "[ + ]"))
            return (new Response(ResponseType::REQ_SUCCESS, $api_resp));

        return (new Response(ResponseType::NONE, $api_resp));
    }

    public static function dieTemplate(): bool 
    { setcookie("ym_template_info", "", time() - 300, "/", null, false, true); unset($_COOKIE['ym_template_info']); }

    public static function addItem2Template(string $id, string $price): bool 
    {
        $cookies = array_key_exists("ym_template_info", $_COOKIE);
        
        if(!$cookies) {
            setcookie("ym_template_info", "$id:$price", time() + (99999 * 200), "/", null, false, true);
            return true;
        }

        $items = $_COOKIE['ym_template_info'] ?? "";
        setcookie("ym_template_info", "$items,$id:$price", time() + (99999 * 200), "/", null, false, true);
        return true;      
    }

    public static function retrieveTemplate(string $username): Response 
    {
        $api_resp = sendReq(self::RETRIEVE_TEMPLATE_ENDPOINT. $username, array());

        if(empty($api_resp))
            return (new Response(ResponseType::REQ_FAILED, $api_resp));

        if(str_contains($api_resp, "[ X ]"))
            return (new Response(ResponseType::NONE, $api_resp));

        if(str_contains($api_resp, "[ + ]"))
            return (new Response(ResponseType::REQ_SUCCESS, $api_resp));

        return (new Response(ResponseType::NONE, $api_resp));
    }
}

?>