<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
  'prefix' => 'auth'
], function () {
  Route::post('login', 'Api\AuthController@login');
  Route::post('register', 'Api\AuthController@register');
});

Route::group([
    'middleware' => 'auth:api'
  ], function() {
      // Route::get('logout', 'AuthController@logout');
      Route::get('user', 'Api\AuthController@user');
      Route::get('shop/create/links', 'Api\ShopController@links');
      Route::get('shop/getDashboardDetails/', 'Api\ShopController@getDashboardDetails');
      Route::resource('shop', 'Api\ShopController');
      Route::resource('order', 'Api\OrderController');
      Route::resource('product', 'Api\ProductController');
      Route::get('product/constant/statuses', 'Api\ProductController@statuses');
      Route::get('order/constant/statuses', 'Api\OrderController@statuses');
      Route::get('order/index/headers', 'Api\OrderController@headers');
  });



// Route::post('/register', 'Api\AuthController@register');
// Route::post('/login', 'Api\AuthController@login');



