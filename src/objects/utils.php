<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
?>