<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("objects/utils.php");
require_once("objects/items.php");
require_once("objects/response.php");
require_once("objects/yw_info.php");

class Items
{
    /*
        API Endpoints
    */
    const STATISTICS_ENDPOINT       = "https://api.yomarket.info/statistics";

    const SEARCH_ENDPOINT           = "https://api.yomarket.info/search?q=" ;
    const CHANGE_ENDPOINT           = "https://api.yomarket.info/change?id=";
    const PRICE_LOGS_ENDPOINT       = "https://api.yomarket.info/price_logs";
    const SUGGESTION_LOGS_ENDPOINT  = "https://api.yomarket.info/all_suggestion";
    const SAVE_ENDPOINT             = "https://api.yomarket.info/save";

    const PROFILE_ENDPOINT          = "https://api.yomarket.info/profile?username=";
    const AUTH_ENDPOINT             = "https://api.yomarket.info/auth?username=";

    // Items->Response->getResults(); // array | Item
    // Items->Response->type2str(); // string
    public $Respone;

    public $query;
    function Items(string $q) {
        $this->query = $q;
    }

    /*
        searchItem(string $query, string $ip): Response

        Description:
            Web requesting YoMarket's search endpoint and parsing the response
    */
    public function searchItem(string $query, string $ip): Response 
    {
        $new_query = str_replace(" ", "%20", $query);
        $api_resp = sendReq(self::SEARCH_ENDPOINT. $new_query, array("ip" => $ip));

        if(empty($api_resp))
            return (new Response(ResponseType::REQ_FAILED, 0));

        $searchErrors = array("[ X ] Error, You must enter an Item name or ID", 
                              "[ X ] Error, No item was found for ${new_query}");

        if(in_array($api_resp, $searchErrors))
            return (new Response(ResponseType::NONE, 0));

        if(!str_contains($api_resp, "\n"))
            return (new Response(ResponseType::EXACT, (new Item(explode(",", remove_strings($api_resp, array("'", "]", "[")))))));

        /* Split String (Used as an if since there are no exception) */
        $lines = explode("\n", $api_resp);

        if(str_contains($api_resp, "\n"))
        {
            if(count(explode(",", $lines[1])) == 4) {
            /* Remove Item Info On First Line for Yoworld.Info's Price Logs */
                $content = str_replace($lines[0], "", $api_resp);
                
                $item_info = explode(",", remove_strings($lines[0], array("[", "]", "'")));

                $item = new Item($item_info);
                $item->ywinfo_prices = YW_INFO_LOGS::parse_prices($content);

                return (new Response(ResponseType::EXACT, $item));
            }
        }
        
        $this->found = array();
        foreach($lines as $line)
        {
            $info = explode(",", remove_strings($line, array("'", "[", "]")));
            if(count($info) < 5) break;
            if(count($info) >= 5)
                array_push($this->found, (new Item($info)));
        }

        if(count($this->found) == 1)
            return (new Response(ResponseType::EXACT, $this->found[0]));

        if(count($this->found) > 1)
            return (new Response(ResponseType::EXTRA, $this->found));

        return (new Response(ResponseType::NONE, 0));
    }

    /*
        changePrice(Item $item, string $price, string $ip): Response

        Description:
            Web requesting YoMarket's change endpoint and parsing the response for SUCCESS/FAIL signals
    */
    public function changePrice(Item $item, string $price, string $username, string $ip): Response
    {
        $api_resp = sendReq(self::CHANGE_ENDPOINT. $item->id, array("user" => $username, "price" => $price, "ip" => $ip));

        if(empty($api_resp))
            return (new Response(ResponseType::REQ_FAILED, 0));

        if($api_resp === "[ X ] Error, You aren't a manager to change price. Price has been suggested to admins to investigate....")
            return (new Response(ResponseType::INVALID_PERM, 0));

        if($api_resp === "[ X ] Error, failed to change price on ". $item->id. " ". $price. "...!")
            return (new Response(ResponseType::FAILED_TO_UPDATE, 0));

        if(str_contains($api_resp, "[ + ]") && str_contains($api_resp, "successfully"))
            return (new Response(ResponseType::ITEM_UPDATED, 0));

        return (new Response(ResponseType::NONE, 0));
    }

    /*
        reqSuggestions(): Response

        Description:
            Web requesting YoMarket's Price Suggestion Logs
    */
    public function reqSuggestions(): Response
    {
        $api_resp = sendReq(self::SUGGESTION_LOGS_ENDPOINT, array());

        if(empty($api_resp))
            return (new Response(ResponseType::REQ_FAILED, 0));
        
        $this->found = array();
        $lines = explode("\n", remove_strings($api_resp, array("'", "(", ")", "[", "]")));

        foreach($lines as $line)
        {
            /* ('APP_TYPE','IP_ADDR','ITEM_ID','OLD_PRICE','SUGGESTED_PRICE','TIMESTAMP') */
            $info = explode(",", $line);
            if(empty($line)) break;

            if(count($info) <= 6)
                array_push($this->found, (new PriceLog($info)));
        }

        if(count($this->found) > 0)
            return (new Response(ResponseType::REQ_SUCCESS, $this->found));

        return (new Response(ResponseType::NONE, 0));
    }

    /*
        reqPriceLogs(): Response

        Description:
            Web requesting YoMarket's Price Change Logs
    */
    public function reqPriceLogs(): Response
    {
        $api_resp = sendReq(self::PRICE_LOGS_ENDPOINT, array());

        if(empty($api_resp))
            return (new Response(ResponseType::REQ_FAILED, 0));

        $this->found = array();
        $lines = explode("\n", remove_strings($api_resp, array("'", "(", ")", "[", "]")));

        foreach($lines as $line)
        {
            /* ('APP_TYPE','IP_ADDR','ITEM_ID','OLD_PRICE','SUGGESTED_PRICE','TIMESTAMP') */
            $info = explode(",", $line);
            if(count($info) <= 6)
                array_push($this->found, (new PriceLog($info)));
        }

        if(count($this->found) > 0)
            return (new Response(ResponseType::REQ_SUCCESS, $this->found));

        return (new Response(ResponseType::NONE, 0));
    }

    /*
        ::reqStats(): string 

        Description:
            Web requesting YoMarket's Statistics
    */
    public static function reqStats(): Response 
    {
        $api_resp = sendReq(self::STATISTICS_ENDPOINT, array());

        if(empty($api_resp))
            return (new Response(ResponseType::REQ_FAILED, 0));

        return (new Response(ResponseType::REQ_SUCCESS, explode(",", $api_resp)));
    }
}

?>