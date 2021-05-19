<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class SetItem extends Model
{
	protected $table = 'set_item';

    protected $fillable = ['sku_set_id', 'sku_single_id', 'code', 'name', 'set_quantity', 'unit_price', 'created_at' , 'updated_at'];

    // public $timestamps = false;

    public function sku(){
    	return $this->belongsTo(Sku::class, 'sku_set_id', 'id');
    }

    public static function get_set_item_query(){
        $query = DB::table('set_item')
                    ->leftjoin('sku', 'set_item.sku_single_id', '=', 'sku.id')
                    ->select('set_item.*', 'sku.quantity as single_quantity');
        return $query;
    }

    // public function toArray(){
    // 	$data = parent::toArray();
    // 	if($this->sku){
    // 		$data['item_details'] = $this->sku;
    // 	}
    // 	return $data;
    // }
}
