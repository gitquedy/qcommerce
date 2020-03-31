<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Package;

class ApplicationController extends Controller
{
    public function index(Request $request){
    	$packages = Package::all();

    	return view('application.list', compact('packages'));
    }

    public function show(Package $package){
    	$breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('ApplicationController@index'), 'name'=>"Application List"], ['name'=>"Payment"]
        ];
    	return view('application.show', compact('package', 'breadcrumbs'));
    }
}
