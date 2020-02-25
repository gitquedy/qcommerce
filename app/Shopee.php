<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;

class Shopee extends Model
{
	public static function getAuthLink(){
    	// $redirect_uri = 'https://lazada.yuukihost.com/shop/form'; 
    	return 'https://partner.shopeemobile.com/api/v1/shop/auth_partner?id='. self::shopee_partner_id() .'&token='. self::auth_token() .'&redirect=' . self::redirect_uri();
    }

    public static function redirect_uri(){
    	return route('shop.form');
    }

    public static function auth_token(){
    	return hash('sha256', self::shopee_app_key() . self::redirect_uri());
    }
    
    public static function shopee_partner_id(){
    	return (int) env('shopee_partner_id');
    }

    public static function shopee_app_key(){
    	return env('shopee_app_key');
    }
    
}
