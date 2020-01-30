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

class Brand extends Model
{
    protected $table = 'brand';
    	
    
    protected $fillable = ['code', 'name','updated_at', 'created_at'];
    
    
    public static function auth_brand(){
        
        $user_id = Auth::user()->id;
        
        $result = DB::table('brand')->where('user_id','=',$user_id)->orderBy('updated_at', 'desc')->get();
        
        return $result;
        
    }
    
    
    
    
    
}
