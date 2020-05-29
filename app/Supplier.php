<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;

class Supplier extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company', 'contact_person', 'phone', 'email', 'business_id', 'created_at', 'updated_at'
    ];


    public static function auth_supplier(){
        
        $business_id = Auth::user()->business_id;
        
        $result = DB::table('suppliers')->where('business_id','=',$business_id)->orderBy('updated_at', 'desc')->get();
        
        return $result;
        
    }
    

}
