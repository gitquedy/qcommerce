<?php

namespace App\Http\Controllers;
use App\Settings;
use App\User;
use Auth;

use Illuminate\Http\Request;

class CalenderAppController extends Controller
{
    // Calender App
    public function calendarApp(){
      $pageConfigs = [
          'pageHeader' => false
      ];

      $setting = Settings::where('business_id', Auth::user()->business_id)->first();
      $filter = explode(',', $setting->calendar_filter);
      $users = User::where('business_id', Auth::user()->business_id)->get();

      return view('/pages/app-calender', [
          'pageConfigs' => $pageConfigs,
          'filter' => $filter,
          'users' => $users
      ]);
    }
}
