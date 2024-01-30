<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("objects/profile.php");

class YoMarket
{
    public $Respone;

    public $profile;
    public $query;
    function YoMarket(string | Profile $profile, string $query) {
        $this->profile = $profile;

        if(is_string($profile)) {
            $this->profile = (new Profile($profile));
        }


    }
}

?>