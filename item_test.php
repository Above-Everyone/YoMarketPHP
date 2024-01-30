<?php

require_once("src/item_lib.php");

$guide = new Items();

$item_search = $guide->searchItem("26295", "5.5.5.5");
var_dump($item_search);

$change = $guide->changePrice($item_search->getResults(), "999m", "Billy", "5.5.5.5");
var_dump($change);

$price_logs = $guide->reqPriceLogs();
var_dump($price_logs);

$suggestions = $guide->reqSuggestions();
var_dump($suggestions);

?>