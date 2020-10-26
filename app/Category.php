<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Utilities;
use Carbon\Carbon;
use App\Library\lazada\LazopRequest;
use App\Library\lazada\LazopClient;
use App\Library\lazada\UrlConstants;
use Auth;
use DB;

class Category extends Model
{
    protected $table = 'category';
    	
    
    protected $fillable = ['business_id','code', 'name','updated_at', 'created_at'];
    
    public static function auth_category(){
        
        $business_id = Auth::user()->business_id;
        
        $result = DB::table('category')->where('business_id','=',$business_id)->orderBy('updated_at', 'desc')->get();
        
        return $result;
        
    }
    
    
    
    
    
    
}
