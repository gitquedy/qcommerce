<?php

namespace App;

use App\Shop;
use App\User;
use App\Warehouse;
use Auth;
use Illuminate\Database\Eloquent\Model;


class Business extends Model
{
    protected $table = 'business';

    protected $fillable = [
		    	 'name', 'location', 'status'
			];


	public function users(){
		return $this->hasMany(User::class, 'business_id', 'id');
	}

	public function shops(){
		return $this->hasMany(Shop::class, 'business_id', 'id');
	}

	public function warehouse(){
		return $this->hasMany(Warehouse::class, 'business_id', 'id');
	}

	public function settings() {
		return $this->hasOne(Settings::class, 'business_id', 'id');
	}

    public function subscription() {
    	return Billing::where('business_id', Auth::user()->business_id)->where('paid_status', 1)->first();
    }

}
