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
    
    protected $fillable = ['item_id', 'shop_id', 'name', 'short_description', 'brand', 'model', 'quantity','site','SkuId','SellerSku','price','Images','Status','created_at','updated_at'];
    

    
    public function shop(){
		return $this->belongsTo(Shop::class, 'shop_id','id');
	}

    
    
    public static function syncProducts($date = '2018-01-01', $step = '+3 day'){

            
        $shops = Shop::where('site', 'lazada')->get();
        $dates = Utilities::getDaterange($date, Carbon::now()->addDays(1)->format('Y-m-d'), 'c', $step);
        $created_before_increment = 1;
        $orders = [];
        $length = count($dates);
        
        
        $products_all = array();
        
        foreach($shops as $shopsVAL){
            
            $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
                $r = new LazopRequest('/products/get','GET');
                $r->addApiParam('created_after', '2018-01-01T00:00:00+08:00');
                $r->addApiParam('created_before', date('Y-m-d').'T00:00:00+08:00');
                $result = $c->execute($r,$shopsVAL->access_token);
                $data = json_decode($result, true);
                if(isset($data['data']['total_products'])){
                    $products_all[$shopsVAL->id]['total'] = $data['data']['total_products'];
                }
        }
        

        
        
        foreach($shops as $shopsVAL){
            
            $products_all[$shopsVAL->id]['items'] = array();
            
            // if($shopsVAL->id!=1){  return false; }
            
            $limit = 10;
            
            $pages = 0;
            
            if(isset($products_all[$shopsVAL->id]['total'])){
                $pages = $products_all[$shopsVAL->id]['total']/$limit;
                $pages = round($pages,0,PHP_ROUND_HALF_UP)+1;
            }
            
            $offset = 0;
            
            for ($i=1; $i <$pages+1 ; $i++) { 
                
    			$c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
                $r = new LazopRequest('/products/get','GET');
                $r->addApiParam('created_after', '2018-01-01T00:00:00+08:00');
                $r->addApiParam('created_before', date('Y-m-d').'T00:00:00+08:00');
                $r->addApiParam('offset',$offset);
                $r->addApiParam('limit',$limit);
                $result = $c->execute($r,$shopsVAL->access_token);
                $data = json_decode($result, true);
            
                if(isset($data['data']['products'])){
                    foreach($data['data']['products'] as $Produ){
                        $products_all[$shopsVAL->id]['items'][] = $Produ;
                    }
                }
                $offset = $i*$limit-1;
    		}
    		
    		
    		
    		
    		foreach($products_all[$shopsVAL->id]['items'] as $productsVAL){
    		    
    		    $product_check = Products::where('item_id',$productsVAL['item_id'])->get();
    		    
    		    $item_primary = 0;
    		    
    		    foreach($product_check as $produc){
    		        $item_primary = $produc->id;
    		    }
    		    
    		    $productDATA = Products::find($item_primary);
    		    
    		    if(!$productDATA){
    		        $productDATA = new Products();
    		    }
    		    
    		    if(isset($productsVAL['item_id'])){
    		    $productDATA->item_id = $productsVAL['item_id'];    
    		    }
    		    
    		    if(isset($productsVAL['primary_category'])){
    		    $productDATA->primary_category = $productsVAL['primary_category'];    
    		    }
    		    
    		    $productDATA->shop_id = $shopsVAL->id;

                $productDATA->site = 'lazada';
    		    
    		    if(isset($productsVAL['attributes']['name'])){
    		    $productDATA->name = (string) $productsVAL['attributes']['name'];
    		    }
    		    if(isset($productsVAL['attributes']['short_description'])){
    		    $productDATA->short_description = (string) $productsVAL['attributes']['short_description'];
    		    }
    		    if(isset($productsVAL['attributes']['description'])){
    		    $productDATA->description = (string) $productsVAL['attributes']['description'];
    		    }
    		    if(isset($productsVAL['attributes']['brand'])){
    		    $productDATA->brand = (string) $productsVAL['attributes']['brand'];
    		    }
    		    if(isset($productsVAL['attributes']['model'])){
    		    $productDATA->model = $productsVAL['attributes']['model'];
    		    }
    		    if(isset($productsVAL['skus'][0]['quantity'])){
    		    $productDATA->quantity = $productsVAL['skus'][0]['quantity'];
    		    }
    		    if(isset($productsVAL['skus'][0]['SellerSku'])){
    		    $productDATA->SellerSku = $productsVAL['skus'][0]['SellerSku'];
    		    }
    		    if(isset($productsVAL['skus'][0]['SkuId'])){
    		    $productDATA->SkuId = $productsVAL['skus'][0]['SkuId'];
    		    }
    		    
    		    
    		    
    		    
    		    $P_images = array();
    		    
    		    
    		    if(isset($productsVAL['skus'][0]['Images'])){
    		        foreach($productsVAL['skus'][0]['Images'] as $img){
    		            if($img!=""){
        		        $P_images[] = $img;
    		            }
        		    }
    		    
    		    }
    		    if(isset($productsVAL['skus'][0]['max_delivery_time'])){
    		    $productDATA->max_delivery_time = $productsVAL['skus'][0]['max_delivery_time'];
    		    }
    		    if(isset($productsVAL['skus'][0]['min_delivery_time'])){
    		    $productDATA->min_delivery_time = $productsVAL['skus'][0]['min_delivery_time'];
    		    }
    		    if(isset($productsVAL['skus'][0]['Url'])){
    		    $productDATA->Url = $productsVAL['skus'][0]['Url'];
    		    }
    		    if(isset($productsVAL['skus'][0]['package_width'])){
    		    $productDATA->package_width = $productsVAL['skus'][0]['package_width'];
    		    }
    		    if(isset($productsVAL['skus'][0]['color_family'])){
    		    $productDATA->color_family = $productsVAL['skus'][0]['color_family'];
    		    }
    		    if(isset($productsVAL['skus'][0]['package_height'])){
    		    $productDATA->package_height = $productsVAL['skus'][0]['package_height'];
    		    }
    		    if(isset($productsVAL['skus'][0]['special_price'])){
    		    $productDATA->special_price = $productsVAL['skus'][0]['special_price'];
    		    }
    		    if(isset($productsVAL['skus'][0]['price'])){
    		        $productDATA->price = $productsVAL['skus'][0]['price'];

    		    }
    		    if(isset($productsVAL['skus'][0]['package_length'])){
    		        $productDATA->package_length = $productsVAL['skus'][0]['package_length'];
    		    }
    		    if(isset($productsVAL['skus'][0]['package_weight'])){
    		        $productDATA->package_weight = $productsVAL['skus'][0]['package_weight'];
    		    }
    		    if(isset($productsVAL['skus'][0]['Available'])){
    		        $productDATA->Available = $productsVAL['skus'][0]['Available'];
    		    }
    		    if(isset($productsVAL['skus'][0]['Status'])){
    		        $productDATA->Status = $productsVAL['skus'][0]['Status'];
    		    }
    		    $productDATA->Images = implode('|',$P_images);
    		    $productDATA->created_at = date('Y-m-d H:i:s');
    		    $productDATA->updated_at = date('Y-m-d H:i:s');
    		    $productDATA->save();
            }
    		
    		
    		
            
        }
        
        Products::delete_not_exist_products();
        
        
        
        // return $data;
    }
    
    
    public static function get_product_query(){
        

        $query = DB::table('products')
                ->leftjoin('shop', 'products.shop_id', '=', 'shop.id')
                ->select('products.*','shop.name as shop_name');
                
        return $query;
        
        
    }
    
    
    
    public static function product_update($access_token,$xml){
        
        $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
                $r = new LazopRequest('/product/update','POST');
                $r->addApiParam('payload', $xml);
              return  $c->execute($r,$access_token);
                
        
    }
    
    
    public static function sync_single_product($id=""){
        
        $product = Products::find($id);
        $access_token = Shop::find($product->shop_id)->access_token;
        
        $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
                $r = new LazopRequest('/product/item/get','GET');
                $r->addApiParam('item_id', $product->item_id);
                $result = $c->execute($r,$access_token);
                
                
                
                
                $js_object = json_decode($result);
                
                
                $product_details = $js_object->data;
                
    		    
    		    if(isset($product_details->attributes->name)){
    		    $product->name = $product_details->attributes->name;
    		    }
    		    if(isset($product_details->attributes->short_description)){
    		    $product->short_description =  $product_details->attributes->short_description;
    		    }
    		    if(isset($product_details->attributes->description)){
    		    $product->description =  $product_details->attributes->description;
    		    }
    		    if(isset($product_details->attributes->brand)){
    		    $product->brand = $product_details->attributes->brand;
    		    }
    		    if(isset($product_details->attributes->model)){
    		    $product->model = $product_details->attributes->model;
    		    }
    		    if(isset($product_details->skus[0]->quantity)){
    		    $product->quantity = $product_details->skus[0]->quantity;
    		    }
    		    if(isset($product_details->skus[0]->SellerSku)){
    		    $product->SellerSku = $product_details->skus[0]->SellerSku;
    		    }
    		    if(isset($product_details->skus[0]->SkuId)){
    		    $product->SkuId = $product_details->skus[0]->SkuId;
    		    }
    		    
    		    
    		    
    		    
    		    $P_images = array();
    		    
    		    
    		    if(isset($product_details->skus[0]->Images)){
    		        foreach($product_details->skus[0]->Images as $img){
    		            if($img!=""){
        		        $P_images[] = $img;
    		            }
        		    }
    		    
    		    }
    		    if(isset($product_details->skus[0]->max_delivery_time)){
    		    $product->max_delivery_time = $product_details->skus[0]->max_delivery_time;
    		    }
    		    if(isset($product_details->skus[0]->min_delivery_time)){
    		    $product->min_delivery_time = $product_details->skus[0]->min_delivery_time;
    		    }
    		    if(isset($product_details->skus[0]->Url)){
    		    $product->Url = $product_details->skus[0]->Url;
    		    }
    		    if(isset($product_details->skus[0]->package_width)){
    		    $product->package_width = $product_details->skus[0]->package_width;
    		    }
    		    if(isset($product_details->skus[0]->color_family)){
    		    $product->color_family = $product_details->skus[0]->color_family;
    		    }
    		    if(isset($product_details->skus[0]->package_height)){
    		    $product->package_height = $product_details->skus[0]->package_height;
    		    }
    		    if(isset($product_details->skus[0]->special_price)){
    		    $product->special_price = $product_details->skus[0]->special_price;
    		    }
    		    if(isset($product_details->skus[0]->price)){
    		        $product->price = $product_details->skus[0]->price;

    		    }
    		    if(isset($product_details->skus[0]->package_length)){
    		        $product->package_length = $product_details->skus[0]->package_length;
    		    }
    		    if(isset($product_details->skus[0]->package_weight)){
    		        $product->package_weight = $product_details->skus[0]->package_weight;
    		    }
    		    if(isset($product_details->skus[0]->Available)){
    		        $product->Available = $product_details->skus[0]->Available;
    		    }
    		    if(isset($product_details->skus[0]->Status)){
    		        $product->Status = $product_details->skus[0]->Status;
    		    }
    		    
    		    $product->Images = implode('|',$P_images);
    		    
    		    $product->updated_at = date('Y-m-d H:i:s');
    		    
    		    $product->save();
        
                
                
    }
    
    
    
    public static function upload_image($shop_id,$base_64_image){
        
        $access_token = Shop::find($shop_id)->access_token;
        
        
        $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
                $r = new LazopRequest('/image/upload');
                $r->addFileParam('image',file_get_contents($base_64_image));
                $result = $c->execute($r,$access_token);
                
                return $result;
        
    }
    
    
    public static function duplicate_product($id="",$shop_id=""){
        
        $product = Products::find($id);
        $access_token = Shop::find($product->shop_id)->access_token;
        
        $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
                $r = new LazopRequest('/product/item/get','GET');
                $r->addApiParam('item_id', $product->item_id);
                $result = $c->execute($r,$access_token);
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
    		        $short_desc = str_replace("<"," ",$product_details->attributes->short_description);
    		        $short_desc = str_replace(">"," ",$short_desc);
    		        $xml .= '<short_description>'.$short_desc.'</short_description>';
    		    }
    		    if(isset($product_details->attributes->description)){
    		    $xml .= '<description>'.$product_details->attributes->description.'</description>';
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
    		    
    		    $xml .= '</Sku>
                                    </Skus>
                                </Product>
                            </Request>';
                            
                            
                            
                $access_token_shop_next = Shop::find($shop_id)->access_token;
                            
                            
                            
                $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
                $r = new LazopRequest('/product/create','POST');
                $r->addApiParam('payload', $xml);
                $resultX = $c->execute($r,$access_token_shop_next);
                
                
                try {
                  $js_obj = json_decode($resultX);
                }
                catch(Exception $e) {
                  return $resultX;
                }
                
                if(!isset($js_obj->data->item_id)){
                    return $resultX;
                }
                
                
                $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
                    $r = new LazopRequest('/product/item/get','GET');
                    $r->addApiParam('item_id', $product->item_id);
                    $result = $c->execute($r,$access_token_shop_next);
                
                
                
                $js_object = json_decode($result);
                
                
                $product = new Products();
                
                
                $product_details = $js_object->data;
                
                $product->item_id = $js_obj->data->item_id;
                
                $product->shop_id = $shop_id;
                
    		    
    		    if(isset($product_details->attributes->name)){
    		    $product->name = $product_details->attributes->name;
    		    }
    		    if(isset($product_details->attributes->short_description)){
    		    $product->short_description =  $product_details->attributes->short_description;
    		    }
    		    if(isset($product_details->attributes->description)){
    		    $product->description =  $product_details->attributes->description;
    		    }
    		    if(isset($product_details->attributes->brand)){
    		    $product->brand = $product_details->attributes->brand;
    		    }
    		    if(isset($product_details->attributes->model)){
    		    $product->model = $product_details->attributes->model;
    		    }
    		    if(isset($product_details->skus[0]->quantity)){
    		    $product->quantity = $product_details->skus[0]->quantity;
    		    }
    		    if(isset($product_details->skus[0]->SellerSku)){
    		    $product->SellerSku = $product_details->skus[0]->SellerSku;
    		    }
    		    if(isset($product_details->skus[0]->SkuId)){
    		    $product->SkuId = $product_details->skus[0]->SkuId;
    		    }
    		    
    		    
    		    
    		    
    		    $P_images = array();
    		    
    		    
    		    if(isset($product_details->skus[0]->Images)){
    		        foreach($product_details->skus[0]->Images as $img){
    		            if($img!=""){
        		        $P_images[] = $img;
    		            }
        		    }
    		    
    		    }
    		    if(isset($product_details->skus[0]->max_delivery_time)){
    		    $product->max_delivery_time = $product_details->skus[0]->max_delivery_time;
    		    }
    		    if(isset($product_details->skus[0]->min_delivery_time)){
    		    $product->min_delivery_time = $product_details->skus[0]->min_delivery_time;
    		    }
    		    if(isset($product_details->skus[0]->Url)){
    		    $product->Url = $product_details->skus[0]->Url;
    		    }
    		    if(isset($product_details->skus[0]->package_width)){
    		    $product->package_width = $product_details->skus[0]->package_width;
    		    }
    		    if(isset($product_details->skus[0]->color_family)){
    		    $product->color_family = $product_details->skus[0]->color_family;
    		    }
    		    if(isset($product_details->skus[0]->package_height)){
    		    $product->package_height = $product_details->skus[0]->package_height;
    		    }
    		    if(isset($product_details->skus[0]->special_price)){
    		    $product->special_price = $product_details->skus[0]->special_price;
    		    }
    		    if(isset($product_details->skus[0]->price)){
    		        $product->price = $product_details->skus[0]->price;

    		    }
    		    if(isset($product_details->skus[0]->package_length)){
    		        $product->package_length = $product_details->skus[0]->package_length;
    		    }
    		    if(isset($product_details->skus[0]->package_weight)){
    		        $product->package_weight = $product_details->skus[0]->package_weight;
    		    }
    		    if(isset($product_details->skus[0]->Available)){
    		        $product->Available = $product_details->skus[0]->Available;
    		    }
    		    if(isset($product_details->skus[0]->Status)){
    		        $product->Status = $product_details->skus[0]->Status;
    		    }
    		    
    		    $product->Images = implode('|',$P_images);
    		    
    		    $product->updated_at = date('Y-m-d H:i:s');

                $product->site = 'lazada';
    		    
    		    $product->save();
                
                
                return $resultX;
                
                
    }
    
    
    public static function delete_not_exist_products(){
        
        $Products = Products::where('site', 'lazada')->get();
        
        foreach($Products as $ProductVAL){
            
            $shop = Shop::find($ProductVAL->shop_id);
            if($shop){
                $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
                $r = new LazopRequest('/product/item/get','GET');
                $r->addApiParam('item_id', $ProductVAL->item_id);
                $result = $c->execute($r,$shop->access_token);
                $obj = json_decode($result);
                if($obj->code!=0){
                    $current_product = Products::find($ProductVAL->id);
                    $current_product->delete();
                }
            }else{
                $current_product = Products::find($ProductVAL->id);
                $current_product->delete();
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
