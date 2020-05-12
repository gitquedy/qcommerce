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

      // stand alone (without permission)
    Route::group(['prefix' => 'user'], function()
    {
      Route::get('', 'Api\AuthController@user');
      Route::get('permissions', 'Api\User\UserController@permission');
    });
    Route::get('shop/getDashboardDetails/', 'Api\ShopController@getDashboardDetails');

    //end stand alone (without permission)

    Route::group(['middleware' => 'permission:shop.manage'], function()
      {
        Route::resource('shop', 'Api\ShopController');

        Route::get('shop/create/links', 'Api\ShopController@links');
      });

    Route::group(['middleware' => 'permission:order.manage'], function()
    {
        Route::resource('order', 'Api\OrderController');
        Route::get('order/constant/statuses', 'Api\OrderController@statuses');
        Route::get('order/index/headers', 'Api\OrderController@headers');
    });


    Route::group(['middleware' => 'permission:product.manage'], function()
    {
      Route::resource('product', 'Api\ProductController');
      Route::get('product/constant/statuses', 'Api\ProductController@statuses');
    });

  
      Route::group(['middleware' => 'permission:returnRecon.manage', 'prefix' => 'reconciliation/return'], function()
      {
        Route::get('', 'Api\Reconciliation\ReturnController@index');
        Route::get('reconcile', 'Api\Reconciliation\ReturnController@reconcile');
        Route::get('headers', 'Api\Reconciliation\ReturnController@headers');
      });

      Route::group(['middleware' => 'permission:shippingFeeRecon.manage', 'prefix' => 'reconciliation/shippingfee'], function()
      {
        Route::get('', 'Api\Reconciliation\ShippingFeeController@index');
        Route::get('reconcile', 'Api\Reconciliation\ShippingFeeController@reconcile');
        Route::get('headers', 'Api\Reconciliation\ShippingFeeController@headers');
        Route::get('reconciliation_link', 'Api\Reconciliation\ShippingFeeController@reconciliation_link');

         
      });

     
  });





