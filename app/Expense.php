<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'expense';

    protected $fillable = ['expense_category_id', 'warehouse_id', 'business_id', 'date', 'paid', 'payment_status', 'reference_no', 'amount', 'note', 'attachment', 'created_by', 'updated_by'];

    public function category(){
    	return $this->belongsTo(ExpenseCategory::class, 'expense_category_id', 'id');
    }

    public function attachment_link(){
    	return url('images/expenses/' . $this->attachment);
    }

    public function warehouse() {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    public function created_by_name(){
        return $this->belongsTo(User::class, 'created_by', 'id');
	}

    public function updated_by_name(){
        return $this->belongsTo(User::class, 'updated_by', 'id');
	}

    public function payments(){
        return $this->morphMany(Payment::class, 'payable', 'payable_type');
    }
}
