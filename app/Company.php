<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'company_details';

    protected $fillable = ['business_id', 'name', 'address', 'vat_tin_no', 'phone_no', 'logo'];

    public function business(){
        return $this->belongsTo(Business::class, 'business_id', 'id');
    }

    public function imageUrl(){
        return asset('images/profile/company-logo/'. $this->logo);
    }
}
