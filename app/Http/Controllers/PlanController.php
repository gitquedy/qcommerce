<?php

namespace App\Http\Controllers;

use Auth;
use App\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('PlanController@index'), 'name'=>"Plan"], ['name'=>"Plan List"]
        ];
        $user = Auth::user();
        $plans = Plan::where('status', 1)->get();
        return view('plan.index', [
            'breadcrumbs' => $breadcrumbs,
            'plans' => $plans,
        ]);
    }

    public function show(Plan $plan){
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('PlanController@index'), 'name'=>"Plan List"], ['name'=>"Payment"]
        ];
        return view('plan.show', compact('plan', 'breadcrumbs'));
    }
}
