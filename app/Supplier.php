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
        'comapny', 'contact_person', 'phone', 'email', 'user_id',
    ];


    public static function auth_supplier(){
        
        $user_id = Auth::user()->id;
        
        $result = DB::table('suppliers')->where('user_id','=',$user_id)->orderBy('updated_at', 'desc')->get();
        
        return $result;
        
    }
    

}
