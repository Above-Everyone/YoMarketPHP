<?php

require_once("src/market.php");

$profiles = new Profiles("Billy");
$profile = $profiles->searchProfile("Billy");

var_dump($profile);
?>