<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;

class Shopee extends Model
{
    //
	public $url;

	public $httpMethod = "POST";

	public $requestParams = [];

	public $sign;

    public function __construct($uri){
    	$this->url = 'https://partner.shopeemobile.com/api/v1/' . $uri;
    }

    public function addApiParam($key, $value){

    	if(!is_string($key))
		{
			throw new Exception("api param key should be string");
		}

    	if(is_object($value))
		{
			$this->requestParams[$key] = json_decode($value);
		}
		else
		{
			$this->requestParams[$key] = $value;
		}
    }

    public function execute(){
    	$sysParams['time_stamp'] = $this->msectime();
    	$sysParams['partner_id'] = (int) $this->shopee_partner_id();

    	$form_params = array_merge($this->requestParams, $sysParams);
    	$this->sign = json_encode($form_params);

    	$Authorization = $this->generateSign();
    	// die(var_dump($form_params));
    	// die(var_dump());
    	$client = new Client();
    	$request = $client->post($this->url,[
    		'headers' => [
    			'Authorization' => $Authorization,
    		],
    		'form_params' => $form_params,
    		 // 'debug' => true
    	]);
    	// die(var_dump($request->hasHeader('partner_id')));
    	// die(var_dump($this->url));
    	die(var_dump($request->getBody()->getContents()));


    }

	public static function getAuthLink(){
    	// $redirect_uri = 'https://lazada.yuukihost.com/shop/form'; 
    	return 'https://partner.shopeemobile.com/api/v1/shop/auth_partner?id='. self::shopee_partner_id() .'&token='. self::auth_token() .'&redirect=' . self::redirect_uri();
    }

    public function generateSign(){
    	$string = $this->url . '|' . $this->sign;

    	return hash_hmac('sha256', $string, $this->shopee_app_key());
    }

    public static function redirect_uri(){
    	return route('shop.form');
    }

    public static function auth_token(){
    	return hash('sha256', self::shopee_app_key() . self::redirect_uri());
    }
    
    public static function shopee_partner_id(){
    	return env('shopee_partner_id');
    }

    public static function shopee_app_key(){
    	return env('shopee_app_key');
    }

    function msectime() {
	   list($msec, $sec) = explode(' ', microtime());
	   return $sec . '000';
	}

    
}
