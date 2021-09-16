<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = ['id', 'business_id', 'title', 'label', 'start', 'end', 'url', 'guests', 'location', 'description'];

    public function business() {
        return $this->belongsTo(Business::class, 'business_id', 'id');
    }
}
