<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalenderAppController extends Controller
{
    // Calender App
    public function calendarApp(){
      $pageConfigs = [
          'pageHeader' => false
      ];

      return view('/pages/app-calender', [
          'pageConfigs' => $pageConfigs
      ]);
    }
}
