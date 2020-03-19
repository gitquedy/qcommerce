<?php

namespace App\Http\Controllers;

use App\Sku;
use App\Products;
use App\Category;
use App\Brand;
use App\Shop;
use App\Supplier;
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
use Helper;
use Auth;
use App\OrderItem;

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
        $Suppliers = Supplier::auth_supplier();
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('ReportsController@outOfStock'), 'name'=>"Reports"], ['name'=>"Out of Stock"]
        ];

            if ( request()->ajax()) {
                $business_id = Auth::user()->business_id;
                $Sku = Sku::where('business_id','=',$business_id)->where('quantity', 0)->orderBy('updated_at', 'desc');
                return Datatables::eloquent($Sku)
                    ->addColumn('category_name', function(Sku $SKSU) {
                                    $category = Category::find($SKSU->category);
                                    if($category){
                                       return  $category->name;
                                    }
                                })
                    ->addColumn('image', function(Sku $SKSU) {
                                    $products = Products::where('seller_sku_id', $SKSU->id)->first();
                                    if($products){
                                       return  $products->Images;
                                    }
                                    else {
                                        return "https://place-hold.it/100&text=No_Image";
                                    }
                                })
                    ->addColumn('brand_name', function(Sku $SKSU) {
                                    $Brand = Brand::find($SKSU->brand);
                                    if($Brand){
                                       return  $Brand->name;
                                    }
                                    else {
                                        return "";
                                    }
                                })
                    ->addColumn('supplier_name', function(Sku $SKSU) {
                                    $Supplier = Supplier::find($SKSU->supplier);
                                    if($Supplier){
                                       return  $Supplier->company;
                                    }
                                    else {
                                        return "";
                                    }
                                })
                    ->make(true);
            }
        return view('reports.out_of_stock', [
            'breadcrumbs' => $breadcrumbs,
            'suppliers' => $Suppliers,
        ]);
    }

    public function productAlert(){
        $Suppliers = Supplier::auth_supplier();
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('ReportsController@productAlert'), 'name'=>"Reports"], ['name'=>"Product Alert"]
        ];

            if ( request()->ajax()) {
                $business_id = Auth::user()->business_id;
                $Sku = Sku::where('business_id','=',$business_id)->whereRaw('quantity <= alert_quantity')->where('quantity','>', 0)->orderBy('updated_at', 'desc');
                return Datatables::eloquent($Sku)
                ->addColumn('category_name', function(Sku $SKSU) {
                                $category = Category::find($SKSU->category);
                                if($category){
                                   return  $category->name;
                                }
                            })
                ->addColumn('image', function(Sku $SKSU) {
                                $products = Products::where('seller_sku_id', $SKSU->id)->first();
                                if($products){
                                   return  $products->Images;
                                }
                                else {
                                    return "https://place-hold.it/100&text=No_Image";
                                }
                            })
                ->addColumn('brand_name', function(Sku $SKSU) {
                                $Brand = Brand::find($SKSU->brand);
                                if($Brand){
                                   return  $Brand->name;
                                }
                                else {
                                    return "";
                                }
                            })
                ->addColumn('supplier_name', function(Sku $SKSU) {
                                $Supplier = Supplier::find($SKSU->supplier);
                                if($Supplier){
                                   return  $Supplier->company;
                                }
                                else {
                                    return "";
                                }
                            })
                ->make(true);
            }
        return view('reports.product_alert', [
            'breadcrumbs' => $breadcrumbs,
            'suppliers' => $Suppliers,
        ]);
    }
    public function topSellingProducts(Request $request){
        $breadcrumbs = [['link'=>"/",'name'=>"Home"],['link'=> action('ReportsController@topSellingProducts'), 'name'=>"Reports"], ['name'=>"Top Selling Products"]];
        $all_shops = $request->user()->business->shops;

        if (request()->ajax()) {
            $shops = $request->user()->business->shops();
            if($request->get('shop') != null){
               $shops = $shops->whereIn('id', explode(',', $request->get('shop')));
            }
            $shop_ids = $shops->pluck('id');

            $no_of_products = 10;
            if($request->get('no_of_products') != null){
                $no_of_products = $request->get('no_of_products');
            }

            $orderItems = OrderItem::join('products', 'products.id', '=', 'order_item.product_id')
                ->join('order', 'order.id', '=', 'order_item.order_id')
                ->select('order_item.product_id', DB::raw('ROUND(SUM(order_item.price)) as total_price'), DB::raw('SUM(order_item.quantity) as total_quantity'))
                ->whereIn('products.shop_id', $shop_ids)
                ->groupBy('order_item.product_id')
                ->orderBy('total_quantity', 'desc')->take($no_of_products);

            $daterange = explode('/', $request->get('daterange'));
            if(count($daterange) == 2){
                if($daterange[0] == $daterange[1]){
                    $orderItems->whereDate('order_item.created_at', [$daterange[0]]);
                }else{
                    $orderItems->whereDate('order_item.created_at', '>=', $daterange[0])->whereDate('order_item.created_at', '<=', $daterange[1]);
                }
                
            }

           $orderItems = $orderItems->get();

            $data = ['count' => 0];
            foreach($orderItems as $orderItem){
                $sku = $orderItem->product->SellerSku;
                $data['report'][$sku]['seller_sku'] = $orderItem->product->SellerSku;
                $data['report'][$sku]['product_name'] = $orderItem->product->name;
                $data['report'][$sku]['total_price'] =  $orderItem->total_price;
                $data['report'][$sku]['total_quantity'] =  $orderItem->total_quantity;

                $data['count'] += 1;
            }
            return $data;
        }
        return view('reports.topSellingProducts', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => $all_shops,
        ]);
    }
    public function dailySales(Request $request){
        $breadcrumbs = [['link'=>"/",'name'=>"Home"],['link'=> action('ReportsController@dailySales'), 'name'=>"Reports"], ['name'=>"Daily Sales"]];
       $all_shops = $request->user()->business->shops;

        if (request()->ajax()) {
            $shops = $request->user()->business->shops();
            if($request->get('shop') != null){
               $shops = $shops->whereIn('id', explode(',', $request->get('shop')));
            }
            $shop_ids = $shops->pluck('id');

            $no_of_products = 5;
            if($request->get('no_of_products') != null){
                $no_of_products = $request->get('no_of_products');
            }

            $orderItems = OrderItem::join('products', 'products.id', '=', 'order_item.product_id')
                ->join('order', 'order.id', '=', 'order_item.order_id')
                ->select('order_item.product_id', DB::raw('ROUND(SUM(order_item.price)) as total_price'), DB::raw('SUM(order_item.quantity) as total_quantity'))
                ->whereIn('products.shop_id', $shop_ids)
                ->groupBy('order_item.product_id')
                ->orderBy('total_quantity', 'desc')->take($no_of_products);
                
                
           if($request->get('timings')=="Today"){
               $orderItems->whereDate('order_item.created_at', '=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="Yesterday"){
                $date=date_create();
                date_modify($date,"-1 days");
               $orderItems->whereDate('order_item.created_at', '=', date_format($date,"Y-m-d"));
           }
           
           if($request->get('timings')=="Last_7_days"){
                $date=date_create();
                date_modify($date,"-7 days");
                $orderItems->where('order_item.created_at', '>=', date_format($date,"Y-m-d"));
                $orderItems->where('order_item.created_at', '<=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="This_Month"){
                $orderItems->where('order_item.created_at', '>=', date("Y-m-01"));
                $orderItems->where('order_item.created_at', '<=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="Last_30_days"){
                $date=date_create();
                date_modify($date,"-30 days");
                $orderItems->where('order_item.created_at', '>=', date_format($date,"Y-m-d"));
                $orderItems->where('order_item.created_at', '<=', date('Y-m-d'));
           }
           $orderItems = $orderItems->get();

            $data = ['count' => 0];
            foreach($orderItems as $orderItem){
                $sku = $orderItem->product->SellerSku;
                $data['report'][$sku]['seller_sku'] = $orderItem->product->SellerSku;
                $data['report'][$sku]['product_name'] = $orderItem->product->name;
                $data['report'][$sku]['total_price'] =  $orderItem->total_price;
                $data['report'][$sku]['total_quantity'] =  $orderItem->total_quantity;

                $data['count'] += 1;
            }
            return $data;
        }
        return view('reports.topSellingProducts', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => $all_shops,
        ]);
    }
}