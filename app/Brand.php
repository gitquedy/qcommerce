<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Utilities;
use Carbon\Carbon;
use App\Library\lazada\lazop\LazopRequest;
use App\Library\lazada\lazop\LazopClient;
use App\Library\lazada\lazop\UrlConstants;
use Auth;
use DB;

class Brand extends Model
{
    protected $table = 'brand';
    	
    
    protected $fillable = ['business_id','code', 'name','updated_at', 'created_at'];
    
    
    public static function auth_brand(){
        
        $business_id = Auth::user()->business_id;
        
        $result = DB::table('brand')->where('business_id','=',$business_id)->orderBy('updated_at', 'desc')->get();
        
        return $result;
        
    }
    
    
    
    
    
}
