<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Woocommerce extends Model
{
    public static function getDomain($id) {
        return DB::table('shop')->where('id', $id)->value('domain');
    }

    public static function getConsumerKey($id) {
        return DB::table('shop')->where('id', $id)->value('access_token');
    }

    public static function getConsumerSecret($id) {
        return DB::table('shop')->where('id', $id)->value('refresh_token');
    }
}
