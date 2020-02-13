<?php

namespace App\Http\Controllers;

use App\Api;
use App\Order;
use App\Shop;
use App\Products;
use App\Sku;
use Illuminate\Http\Request;
use App\Lazop;
use Carbon\Carbon;
use App\Library\lazada\LazopRequest;
use App\Library\lazada\LazopClient;
use App\Library\lazada\UrlConstants;
use App\Http\Controllers\Controller;
use App\Utilities;
use Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"], ['name'=>"Reports"]
        ];

        return view('reports.index', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }


    public function outOfStock(){
        
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('ReportsController@index'), 'name'=>"Reports"], ['name'=>"Out of Stock"]
        ];

            if ( request()->ajax()) {
               $Products = Products::with('shop')->where('quantity', '=', 0)->orderBy('updated_at', 'desc');
               
                return Datatables::eloquent($Products)
                ->addColumn('shop', function(Products $product) {
                                return $product->shop ? $product->shop->short_name : '';
                })
                ->addColumn('image', function(Products $product) {
                    $image_url = '';
                    $imagres = explode("|",$product->Images);
                    if(isset($imagres[0])){
                        $image_url = $imagres[0];
                    }
                    return $image_url;
                })
                ->editColumn('updated_at', function(Products $product) {
                    return date('F d, Y h:i:s a', strtotime($product->updated_at));
                }) 
                ->make(true);
            }
        return view('reports.out_of_stock', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => array(),
            'statuses' => array(),
        ]);
    }

    public function productAlert(){
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('ReportsController@index'), 'name'=>"Reports"], ['name'=>"Product Alert"]
        ];

            if ( request()->ajax()) {
               $Sku = Sku::whereRaw('quantity <= alert_quantity')->orderBy('updated_at', 'desc')->get();
               $Sku_ids = array();
               foreach($Sku as $s) {
                    $Sku_ids[] = $s->id;
               }
               $Products = Products::with('shop')->whereIn('seller_sku_id', $Sku_ids)->orderBy('updated_at', 'desc');
               
                return Datatables::eloquent($Products)
                ->addColumn('shop', function(Products $product) {
                    return $product->shop ? $product->shop->short_name : '';
                })
                ->addColumn('image', function(Products $product) {
                    $image_url = '';
                    $imagres = explode("|",$product->Images);
                    if(isset($imagres[0])){
                        $image_url = $imagres[0];
                    }
                    return $image_url;
                })
                ->editColumn('updated_at', function(Products $product) {
                    return date('F d, Y h:i:s a', strtotime($product->updated_at));
                }) 
                ->make(true);
            }
        return view('reports.product_alert', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => array(),
            'statuses' => array(),
        ]);
    }
}