<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Lazop;
use App\Order;
use App\Shop;
use App\Category;
use App\Brand;
use App\Utilities;
use Carbon\Carbon;
use App\Library\lazada\LazopRequest;
use App\Library\lazada\LazopClient;
use App\Library\lazada\UrlConstants;
use Auth;
use DB;

class Sku extends Model
{
    protected $table = 'sku';
    	
    
    protected $fillable = ['item_id', 'shop_id', 'SellerSku', 'primary_category', 'name', 'brand', 'cost', 'price', 'quantity', 'Available', 'updated_at', 'created_at'];
    
    
    
    public function category(){
		return $this->belongsTo(Category::class, 'category','id');
	}
	
	public function brand(){
		return $this->belongsTo(Brand::class, 'brand','id');
	}
	
    
    
    
    
    
    
}
