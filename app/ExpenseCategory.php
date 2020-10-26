<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $table = 'expense_category';
    	
    
    protected $fillable = ['business_id','code', 'name','updated_at', 'created_at'];

    public function displayName(){
    	return $this->code . '[' . $this->name . ']';
    }
}
