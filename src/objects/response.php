<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Response 
{
    public $type;
    public $result;
    function __construct(ResponseType $t, array | Item | int | string $r)
    {
        $this->type = $t;
        $this->result = $r;
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
                return $this->result[0];

            case ResponseType::EXTRA:
                return $this->result;
        }

        return array();
    }
}

enum ResponseType
{
    case NONE;
    case EXACT;
    case EXTRA;
    case ITEM_UPDATED;
    case FAILED_TO_UPDATE;
    case API_FAILURE;
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

            case ResponseType::API_FAILED:
                return "ResponseType::API_FAILED";

            case ResponseType::REQ_SUCCESS:
                return "ResponseType::REQ_SUCCESS";
        }
    }
}

?>