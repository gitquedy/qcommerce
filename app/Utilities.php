<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    return $dates;
}
}
