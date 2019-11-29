<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Crud extends Model
{
    protected $table = 'crud';

    protected $fillable = ['name', 'status'];
    
    public function getStatusColor(){
    	if($this->status == 'Active'){
    		return 'success';
    	}else if ($this->status == 'Archived'){
    		return 'warning';
    	}else{
    		return 'success';
    	}
    }
}
