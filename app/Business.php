<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Shop;


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

}
