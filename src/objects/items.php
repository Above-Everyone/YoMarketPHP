<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Item 
{
	/*
		General Item Information
	*/
	public $name;
	public $id;
	public $url;
	public $price;
	public $update;

	/*
		Actions you h the ITEM
	*/
	public $is_tradable;
	public $is_giftable;

	/*
		In-store Inf
	*/
	public $in_store;
	public $store_price;
	public $gender;
	public $xp;
	public $category;

	/*
		Extra Info
	*/
	public $ywinfo_prices;

    function __construct(array $arr)
    {
        if(count($arr) > 4) {
            $this->name = $arr[0] ?? "";
            $this->id = $arr[1] ?? ""; 
            $this->url = $arr[2] ?? "";
            $this->price = $arr[3] ?? ""; 
            $this->update = $arr[4] ?? "";
            $this->is_tradable = $arr[5] ?? "";
            $this->is_giftable = $arr[6] ?? ""; 
            $this->in_store = $arr[7] ?? "";
            $this->store_price = $arr[8] ?? ""; 
            $this->gender = trim($arr[9]) ?? ""; 
            $this->xp = trim($arr[10]) ?? "";
            $this->category = trim($arr[11]) ?? "";
        }
    }
}

class price_log
{
    public $app_t;
    public $user;
    public $addy;
    public $itemID;
    public $old_price;
    public $new_price;
    public $timestamp;
    
    function __construct(array $arr)
    {
        if(count($arr) != 6)
            return;

        $this->app_t = $arr[0]; 
        $this->user = $arr[1]
        $this->addy = $arr[2];
        $this->query = $arr[3]; 
        $this->old_price = $arr[4];
        $this->new_price = $arr[5]; 
        $this->timestamp = $arr[6];
    }
}
?>