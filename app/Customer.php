<?php

namespace App;

// use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Utilities;

class Customer extends Model
{
    protected $table = 'customer';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_id', 'first_name', 'last_name', 'phone', 'price_group', 'address', 'email'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    public function formatName() {
        return $this->last_name.", ".$this->first_name;
    }

    public function business(){
        return $this->belongsTo(Business::class, 'business_id', 'id');
    }
    
    public function fullName(){
        return $this->first_name . ' ' . $this->last_name;
    }
}
