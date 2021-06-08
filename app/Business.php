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

	public function sales(){
		return $this->hasMany(Sales::class, 'business_id', 'id');
	}

	public function purchases(){
		return $this->hasMany(Purchases::class, 'business_id', 'id');
	}

	public function suppliers(){
		return $this->hasMany(Supplier::class, 'business_id', 'id');
	}

	public function shops(){
		$user = Auth::user();

		if($user->role == 'Staff'){
			return $this->hasMany(Shop::class, 'business_id', 'id')->whereIn('id', $user->shopPermissions->pluck('shop_id'));
		}else{
			return $this->hasMany(Shop::class, 'business_id', 'id');
		}
		
	}

	public function warehouse(){
		$user = Auth::user();

		if($user->role == 'Staff'){
			return $this->hasMany(Warehouse::class, 'business_id', 'id')->whereIn('id', $user->warehousePermissions->pluck('warehouse_id'));
		}else{
			return $this->hasMany(Warehouse::class, 'business_id', 'id');
		}
	}

	public function settings() {
		return $this->hasOne(Settings::class, 'business_id', 'id');
	}

    public function subscription() {
    	return Billing::where('business_id', Auth::user()->business_id)->where('paid_status', 1)->orderBy('created_at', 'desc')->first();
    }

    public function expense_categories(){
    	return $this->hasMany(ExpenseCategory::class, 'business_id', 'id');
    }

}
