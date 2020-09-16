<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarehousePermission extends Model
{
    protected $table = 'warehouse_permission';

    protected $fillable = [
    	'user_id',
    	'warehouse_id'
    ];}
