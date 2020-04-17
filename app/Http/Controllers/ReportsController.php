<?php

namespace App\Http\Controllers;

use App\Sku;
use App\Products;
use App\Category;
use App\Brand;
use App\Shop;
use App\Order;
use App\Sales;
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
                ->whereIn('order.shop_id', $shop_ids)
                ->whereNotIn('order.status', Order::statusNotIncludedInSales())
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

            $pos_sales = Sales::where('business_id', Auth::user()->business_id)->where('status', '!=', 'canceled');
            if(count($daterange) == 2){
                if($daterange[0] == $daterange[1]){
                    $pos_sales->where('date', [$daterange[0]]);
                }else{
                    $pos_sales->where('date', '>=', $daterange[0])->whereDate('date', '<=', $daterange[1]);
                }
                
            }

           $orderItems = $orderItems->get();
           $pos_sales = $pos_sales->get();
            $data = ['count' => 0];
            $report = [];
            foreach($orderItems as $orderItem){
                $sku = $orderItem->product->SellerSku;
                $report[$sku]['seller_sku'] = $orderItem->product->SellerSku;
                $report[$sku]['product_name'] = $orderItem->product->name;
                $report[$sku]['total_price'] =  $orderItem->total_price;
                $report[$sku]['total_quantity'] =  $orderItem->total_quantity;

                $data['count'] += 1;
            }
            foreach($pos_sales as $pos_sale){

                foreach ($pos_sale->items as $key => $sale_items) {
                    $sku = $sale_items->sku_code;
                    if(isset($report[$sku])) {
                        $report[$sku]['total_price'] +=  $sale_items->subtotal;
                        $report[$sku]['total_quantity'] +=  $sale_items->quantity;
                    }
                    else {
                        $report[$sku]['seller_sku'] = $sale_items->sku_code;
                        $report[$sku]['product_name'] = $sale_items->sku_name;
                        $report[$sku]['total_price'] =  $sale_items->subtotal;
                        $report[$sku]['total_quantity'] =  $sale_items->quantity;
                    }
                    $data['count'] += $sale_items->quantity;;
                }
            }
            uasort($report, function ($a, $b) {
                return $b['total_quantity'] <=> $a['total_quantity'];
            });
            // print json_encode($report);die();
            $data['report'] = array_slice($report, 0, 10, true);
            // $data['report'] = $report;
            return $data;
        }
        return view('reports.topSellingProducts', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => $all_shops,
        ]);
    }
    public function dailySales(Request $request){
         $breadcrumbs = [['link'=>"/",'name'=>"Home"],['link'=> action('ReportsController@dailySales'), 'name'=>"Daily Sales"], ['name'=>"Daily Sales"]];
         $all_shops = $request->user()->business->shops;
         if ( request()->ajax()) {
               $shops = $request->user()->business->shops;
               if($request->get('shop') != ''){
                    $shops = $shops->whereIn('id', explode(",", $request->get('shop')));
               }
               $shop_ids = $shops->pluck('id')->toArray();
                $daterange = explode('/', $request->get('daterange'));
                $order = Order::join('order_item', 'order.id', '=', 'order_item.order_id', 'left')
                ->select(DB::raw('DATE(order.created_at) as date'), DB::raw('COUNT(DISTINCT order.id) as total_orders'), DB::raw('ROUND(SUM(order_item.price)) as total_price'), DB::raw('SUM(order_item.quantity) as total_quantity'))
                ->whereIn('order.shop_id', $shop_ids)
                ->whereNotIn('order.status', Order::statusNotIncludedInSales())
                ->orderBy('date', 'desc')
                ->groupBy('date');
                if(count($daterange) == 2){
                    if($daterange[0] == $daterange[1]){
                        $order->whereDate('order.created_at', [$daterange[0]]);
                    }else{
                        $order->whereDate('order.created_at', '>=', $daterange[0])->whereDate('order.created_at', '<=', $daterange[1]);
                    }
                }

                return Datatables::eloquent($order)
                    ->editColumn('total_orders', function($order) {
                            $sales = Sales::where('date', $order->date)->where('business_id', Auth::user()->business_id)->where('status', '!=', 'canceled')->get()->count();
                            return $order->total_orders + $sales;
                        })
                    ->editColumn('total_quantity', function($order) {
                            $sales = Sales::where('date', $order->date)->where('business_id', Auth::user()->business_id)->where('status', '!=', 'canceled')->get();
                            $count = 0;
                            foreach ($sales as $sale) {
                                foreach ($sale->items as $item) {
                                    $count += $item->quantity;
                                }
                            }
                            return $order->total_quantity + $count;
                        })
                    ->addColumn('dateFormat', function(Order $order) {
                            return Utilities::format_date($order->date, 'M d,Y');
                        })
                    ->addColumn('total_sales', function(Order $order) {
                            $sales = Sales::select(DB::raw('SUM(grand_total) as total'))->where('date', $order->date)->where('business_id', Auth::user()->business_id)->where('status', '!=', 'canceled')->first();
                            return 'PHP ' . number_format($order->total_price + $sales->total, 2);
                        })
                    ->make(true);
             }
             
            return view('reports.dailySales', [
                'breadcrumbs' => $breadcrumbs,
                'all_shops' => $all_shops,
            ]);
    }
}