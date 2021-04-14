<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Utilities extends Model
{
    public static function format_date($date, $format = 'm-d-Y'){
		return date_format(date_create($date), $format);
	}

	public static function viewButtonHref($action){
		return'<a target="_blank" href="'. $action .'" data-toggle="tooltip" data-placement="top" title="View"" class="btn btn-primary btn-sm margin-r-10"><i class="fa fa-eye"></i>';
	}

	public static function viewButton($action){
		return'<a href="#" data-toggle="tooltip" data-placement="top" title="View"" data-href="'. $action . '" class="btn btn-primary btn-sm margin-r-10 modal_button"><i class="fa fa-eye"></i>';
	}

	public static function editButton($action){
		return'<a href="#" data-toggle="tooltip" data-placement="top" title="Edit"" data-href="'. $action . '" class="btn btn-primary btn-sm margin-r-10 modal_button"><i class="fa fa-edit"></i>';
	}

    public static function deleteButton($action){
    	return '<a href="#" data-toggle="tooltip" data-placement="top" title="Delete" data-href="'. $action . '" class="btn btn-danger btn-sm modal_button"><i class="fa fa-trash"></i>';
    }

    public static function nameLink($action, $name){
    	return '<a target="_blank" href="'. $action .'"> '. $name .' <a/>';
    }

    public static function getDaterange($first, $last, $output_format = 'd/m/Y', $step = '+5 day') {

    $dates = array();
    $current = strtotime($first);
    $last = strtotime($last);

    while( $current <= $last ) {

        $dates[] = date($output_format, $current);
        $current = strtotime($step, $current);
    }

    if (!in_array(date($output_format, $last), $dates)) {
        $dates[] = date($output_format, $last);
    }

    return $dates;
    }

    public static function getToday24Hours(){
        $now = Carbon::now()->startOfDay();
        $data = [];
        $data[] = $now->toDateTimeString();
        for($i = 1; $i < 24; $i++){
            $data[] = $now->addHour(1)->toDateTimeString();
        }
        $data[] =  $now->endOfDay()->toDateTimeString();
        return $data;
    }

    public static function getMonthsDates($last = 7){
        // last 6 months dates

        $now = Carbon::now()->startOfMonth();
        $data[] = Carbon::now()->endOfMonth()->toDateTimeString();
        $data[] = $now->toDateTimeString();
        for($i = 1; $i < $last; $i++){
            $data[] = $now->subMonths(1)->toDateTimeString();
        }
        $data = array_reverse($data);
        return $data;
    }
}
