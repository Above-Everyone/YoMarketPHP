<?php
require_once("yomarket/objects/profile.php");
require_once("yomarket/objects/response.php");

class Cookie 
{

    public static function retrieveCookie(array $v): Response
    {
        $cookies = $v;
    
        if($cookies === [] || $cookie === array())
            return (new Resposne(ResponseType::NONE, 0));
    
        foreach($cookies as $cookie)
        {
            if($cookie === "ym_user_info")
                return (new Response(ResponseType::EXACT, (new Profile($_COOKIE['ym_user_info']))));
        }
    
        return (new Response(ResponseType::NONE, 0));
    }

    public static function isInfoCookieSet(array $data): bool 
    { return array_key_exists("ym_user_info", $data); }

    public static function setProfileInfo(string $content): bool 
    {   
        unset($_COOKIE['ym_template_info']);
        setcookie("ym_user_info", $content, time() + (600 * 30), "/", null, false, true); 
    }
}
?>