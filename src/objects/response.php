<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

class Response 
{
    public $type;
    public $results;
    function __construct(ResponseType $t, array | Profile | Item | int | string $r)
    {
        $this->type = $t;
        $this->results = $r;
    }

    public function type2str(): string 
    {
        return ResponseType::r2str($this->type);
    }
    
    public function getResults(): array | Item
    {
        switch($this->type)
        {
            case ResponseType::EXACT:
                return $this->results[0];

            case ResponseType::EXTRA:
                return $this->results;
        }

        return array();
    }
}

enum ResponseType
{
    case NONE;

    /* Search Results Type */
    case EXACT;
    case EXTRA;

    /* Item Updating */
    case ITEM_UPDATED;
    case FAILED_TO_UPDATE;
    case INVALID_PERM;

    
    /* Login Result Type */
    case LOGIN_SUCCESS;
    case INVALID_INFO;
    case LOGIN_FAILED;

    /* API SIGNALS */
    case REQ_FAILED;
    case REQ_SUCCESS;

    public static function r2str(ResponseType $r): string 
    {
        switch($r)
        {
            case ResponseType::NONE:
                return "ResponseType::NONE";

            case ResponseType::EXACT:
                return "ResponseType::EXACT";

            case ResponseType::EXTRA:
                return "ResponseType::EXTRA";

            case ResponseType::ITEM_UPDATED:
                return "ResponseType::ITEM_UPDATED";

            case ResponseType::FAILED_TO_UPDATE:
                return "ResponseType::FAILED_TO_UPDATE";

            case ResponseType::REQ_FAILED:
                return "ResponseType::REQ_FAILED";

            case ResponseType::REQ_SUCCESS:
                return "ResponseType::REQ_SUCCESS";
        }
    }
}

?>