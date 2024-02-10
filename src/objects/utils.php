<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('default_socket_timeout', 5);

function remove_strings(string $str, array $arr): string 
{
    $new = $str;
    foreach($arr as $i)
    { $new = str_replace("$i", "", $new); }
    return $new;
}

function sendReq(string $URL, array $EXTRA_PARM): string 
{
    $req_url = $URL;
    // Adding more GET parameters using $EXTRA_PARM
    if(count($EXTRA_PARM) > 0) {
        foreach($EXTRA_PARM as $parm_name => $parm_value)
        {
            $req_url = $req_url. "&$parm_name=$parm_value";
        }
    }
    
    try {
        $api_resp = file_get_contents($req_url);
        if(empty($api_resp))
           throw new Exception("failed to open stream ", 1);

        return $api_resp;
    } catch (Exception $e) {
        return "";
    }

    return "";
}

function isPriceValid(string $price): bool
{
    /*
        5m
        5.5m
        5-6.5m
    */

    $coin_value_type = array("c", "k", "m", "b");
    $price_coin_type = substr($price, -1);

    $actual_value = str_replace(substr($price, -1), "", $price); // 5, 5.5, 5-6.5

    if(!in_array($price_coin_type, $coin_value_type))
        return false;

    if(str_contains($price, "."))
        $actual_value = explode(".", $actual_value)[0];

    if(str_contains($price, "-"))
        $actual_value = explode("-", "", $actual_value)[1];

    if((int)$actual_value > 0)
        return true;

    return false;
}

function checkAPI(string $api): string 
{
    $api_resp = sendReq("https://api.yomarket.info", array());

    if(!empty($api_resp))
        return $api;
    
    ini_set('default_socket_timeout', 30);
    return str_replace("//api.", "//backup.", $api);
}
?>