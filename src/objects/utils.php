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

?>