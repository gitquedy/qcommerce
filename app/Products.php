<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Lazop;
use App\Order;
use App\Shop;
use App\Utilities;
use Carbon\Carbon;
use App\Library\lazada\LazopRequest;
use App\Library\lazada\LazopClient;
use App\Library\lazada\UrlConstants;
use Auth;
use DB;

class Products extends Model
{
    protected $table = 'products';
    
    protected $fillable = ['item_id', 'shop_id', 'name','site','SkuId','SellerSku','price','Images','Status', 'Url','created_at','updated_at'];
    
    public function shop(){
		return $this->belongsTo(Shop::class, 'shop_id','id');
	}

    public function getImgAndIdDisplay(){
        return '<div class="text-primary font-medium-2 text-bold-600">'. $this->item_id .' </div>' . $this->shop->getImgSiteDisplay();
    }
    
    public static function get_product_query(){
        $query = DB::table('products')
                ->leftjoin('shop', 'products.shop_id', '=', 'shop.id')
                ->select('products.*','shop.name as shop_name');
                
        return $query;
    }

    public function sync(){
        if($this->site == 'lazada'){
            $c = $this->shop->lazadaGetClient();
            $r = new LazopRequest('/product/item/get','GET');
            $r->addApiParam('item_id', $this->item_id);
            $result = $c->execute($r,$this->shop->access_token);
            $data = json_decode($result, true);
            if($data['code'] == "0"){
                $product_details = $data['data'];
                $product_details = [
                    'SkuId' => $product_details['skus'][0]['SkuId'],
                    'SellerSku' => $product_details['skus'][0]['SellerSku'],
                    'item_id' => $product_details['item_id'],
                    'price' =>  $product_details['skus'][0]['price'],
                    'Images' => implode('|', array_filter($product_details['skus'][0]['Images'])),
                    'name' => $product_details['attributes']['name'],
                    'Status' => $product_details['skus'][0]['Status'],
                    'Url' => $product_details['skus'][0]['Url'],
                    ];
                $this->update($product_details);
                $this->touch();
            }
        }
    }

    public function product_update($xml){
        $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
        $r = new LazopRequest('/product/update','POST');
        $r->addApiParam('payload', $xml);
        return  $c->execute($r,$this->shop->access_token);
    }
    
    
    public function getDetails(){
        $access_token = Shop::find($this->shop_id)->access_token;
        $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
        $r = new LazopRequest('/product/item/get','GET');
        $r->addApiParam('item_id', $this->item_id);
        $result = $c->execute($r,$access_token);
        $data = json_decode($result);
        return $data;
    }

    public static function upload_image($shop_id,$base_64_image){
        
        $access_token = Shop::find($shop_id)->access_token;
        
        
        $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
                $r = new LazopRequest('/image/upload');
                $r->addFileParam('image',file_get_contents($base_64_image));
                $result = $c->execute($r,$access_token);
                
                return $result;
        
    }

    public function getXML(){
        $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
        $r = new LazopRequest('/product/item/get','GET');
        $r->addApiParam('item_id', $this->item_id);
        $result = $c->execute($r,$this->shop->access_token);
        $js_object = json_decode($result);
        $product_details = $js_object->data;
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
                    <Request>
                        <Product>';           
        if(isset($product_details->primary_category)){
        $xml .= '<PrimaryCategory>'.$product_details->primary_category.'</PrimaryCategory>';
        }
        $xml .= '<SPUId></SPUId>
                            <AssociatedSku></AssociatedSku>
                            <Attributes>';             
        if(isset($product_details->attributes->name)){
        $xml .= '<name>'.$product_details->attributes->name.'</name>';
        }
        if(isset($product_details->attributes->short_description)){
            $xml .= '<short_description><![CDATA['.$product_details->attributes->short_description.']]></short_description>';
        }
        if(isset($product_details->attributes->description)){
        $xml .= '<description><![CDATA['.$product_details->attributes->description.']]></description>';
        }
        if(isset($product_details->attributes->brand)){
        $xml .= '<brand>'.$product_details->attributes->brand.'</brand>';
        }
        if(isset($product_details->attributes->model)){
        $xml .= '<model>'.$product_details->attributes->model.'</model>';
        }
        $xml .= '</Attributes>
                            <Skus>
                                <Sku>';                  
        if(isset($product_details->skus[0]->quantity)){
            $xml .= '<quantity>'.$product_details->skus[0]->quantity.'</quantity>';
        }
        if(isset($product_details->skus[0]->SellerSku)){
            $xml .= '<SellerSku>'.$product_details->skus[0]->SellerSku.'</SellerSku>';
        }
        if(isset($product_details->skus[0]->color_family)){
        $xml .= '<color_family>'.$product_details->skus[0]->color_family.'</color_family>';
        }
        if(isset($product_details->skus[0]->Images)){
            $xml .= '<Images>';
            foreach($product_details->skus[0]->Images as $img){
                if($img!=""){
                $xml .= '<Image>'.$img.'</Image>';
                }
            }
            $xml .= '</Images>';
        }
        if(isset($product_details->skus[0]->max_delivery_time)){
            $xml .= '<max_delivery_time>'.$product_details->skus[0]->max_delivery_time.'</max_delivery_time>';
        }
        if(isset($product_details->skus[0]->min_delivery_time)){
            $xml .= '<min_delivery_time>'.$product_details->skus[0]->min_delivery_time.'</min_delivery_time>';
        }
        if(isset($product_details->skus[0]->package_width)){
            $xml .= '<package_width>'.$product_details->skus[0]->package_width.'</package_width>';
        }
        
        if(isset($product_details->skus[0]->package_height)){
        $xml .= '<package_height>'.$product_details->skus[0]->package_height.'</package_height>';
        }
        if(isset($product_details->skus[0]->special_price)){
        $xml .= '<special_price>'.$product_details->skus[0]->special_price.'</special_price>';
        }
        if(isset($product_details->skus[0]->price)){
            $xml .= '<price>'.$product_details->skus[0]->price.'</price>';
        }
        if(isset($product_details->skus[0]->package_length)){
            $xml .= '<package_length>'.$product_details->skus[0]->package_length.'</package_length>';
        }
        if(isset($product_details->skus[0]->package_weight)){
            $xml .= '<package_weight>'.$product_details->skus[0]->package_weight.'</package_weight>';
        }
        if(isset($product_details->skus[0]->special_from_date)){
            $xml .= '<special_from_date>'.$product_details->skus[0]->special_from_date.'</special_from_date>';
        }
        if(isset($product_details->skus[0]->special_to_date)){
            $xml .= '<special_to_date>'.$product_details->skus[0]->special_to_date.'</special_to_date>';
        }
        $xml .= '<package_content>This is what\'s in the box </package_content>';
        
        $xml .= '</Sku></Skus></Product></Request>';
        return $xml;
    }
    
    public function duplicate_product(Shop $duplicate_to){
        
        if($duplicate_to->site == 'lazada'){
            $xml = $this->getXML();
            $access_token_shop_next = $duplicate_to->access_token;         
            $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
            $r = new LazopRequest('/product/create','POST');
            $r->addApiParam('payload', $xml);
            $resultX = $c->execute($r,$duplicate_to->access_token);

            $c = $this->shop->lazadaGetClient();
            $r = new LazopRequest('/product/item/get','GET');
            $r->addApiParam('item_id', $this->item_id);
            $result = $c->execute($r,$this->shop->access_token);
            $data = json_decode($result, true);
            if($data['code'] == "0"){
                $product_details = $data['data'];
                $product_details = [
                    'shop_id' => $duplicate_to->id,
                    'site' => 'lazada',
                    'SkuId' => $product_details['skus'][0]['SkuId'],
                    'SellerSku' => $product_details['skus'][0]['SellerSku'],
                    'item_id' => $product_details['item_id'],
                    'price' =>  $product_details['skus'][0]['price'],
                    'Images' => implode('|', array_filter($product_details['skus'][0]['Images'])),
                    'name' => $product_details['attributes']['name'],
                    'Status' => $product_details['skus'][0]['Status'],
                    'Url' => $product_details['skus'][0]['Url'],
                    ];
                $record = Products::updateOrCreate(
                    ['shop_id' => $product_details['shop_id'], 'item_id' => $product_details['item_id']], $product_details);
            }
        }
    }
    
    public static function remove_product($products=array()){
        $feed_back = array();   
        foreach($products as $productsVAL){
            $Products_tmp = Products::find($productsVAL);
            $token = 0;
            $seller_sku = array($Products_tmp->SellerSku);
            $json_sku = json_encode($seller_sku);
            
            if($Products_tmp){ 
                $shop_id = $Products_tmp->shop_id;
                $shop = Shop::find($shop_id);
                $token = $shop->access_token;
            }
            
            $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
                    $r = new LazopRequest('/product/remove');
                    $r->addApiParam('seller_sku_list',$json_sku);
                    $result = $c->execute($r,$token);
                    
                    $feed_back[] = $result;
                    
            $Products_tmp->delete();
        }
        return $feed_back;
    }
}
