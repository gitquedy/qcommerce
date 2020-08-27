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
      $format = $this->business->settings->customer_name_format;
      switch ($format) {
        case "Lname, Fname":
            return $this->last_name.", ".$this->first_name;
          break;
        case "Fname Lname":
        default:
            return $this->first_name." ".$this->last_name;
          break;
      }
    }
    
    public function fullName(){
        return $this->first_name . ' ' . $this->last_name;
    }

    public function business(){
        return $this->belongsTo(Business::class, 'business_id', 'id');
    }
    
    public function price_group_data(){
        return $this->hasOne(PriceGroup::class, 'id', 'price_group');
    }
    
    public function sales(){
        return $this->hasMany(Sales::class, 'customer_id', 'id');
    }
    
    public function payments(){
        return $this->hasMany(Payment::class, 'customer_id', 'id');
    }

    public function available_deposit(){
        $total_payment_by_deposit = 0;
        foreach ($this->payments as $pays) {
          if($pays->payment_type == "deposit") {
            $total_payment_by_deposit += $pays->amount;
          }
        }
        $total_deposits = 0;
        foreach ($this->deposits as $dep) {
            $total_deposits += $dep->amount;
        }
        return $total_deposits - $total_payment_by_deposit;
    }

    public function deposits(){
        return $this->hasMany(Deposit::class, 'customer_id', 'id');
    }
}
