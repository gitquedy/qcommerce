<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Library\lazada\LazopRequest;
use App\Library\lazada\LazopClient;
use App\Library\lazada\UrlConstants;

class Lazop extends Model
{

    public static function getAuthLink(){
    	// $redirect_uri = 'https://lazada.yuukihost.com/shop/form'; 
        $redirect_uri =  route('shop.form'); 
    	return 'https://auth.lazada.com/oauth/authorize?response_type=code&force_auth=true&redirect_uri='. $redirect_uri .'&client_id=' . env('lazada_app_key');
    }
    
    public static function get_api_key(){
    	return env('lazada_app_key');
    }

    public static function get_api_secret(){
    	return env('lazada_app_secret');
    }

    public static function getCategoryTree(){
        $c = new LazopClient(UrlConstants::getPH(),self::get_api_key(),self::get_api_secret());
        $request = new LazopRequest('/category/tree/get','GET');
        $result = $c->execute($request);
        $data = json_decode($result, true);
        return $data;
    }
}
