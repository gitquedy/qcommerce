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
use App\Library\Lazada\lazop\LazopRequest;
use App\Library\Lazada\lazop\LazopClient;
use App\Library\Lazada\lazop\UrlConstants;
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
        $this->authorize('is_included_in_plan', 'out_of_stock');
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('ReportsController@outOfStock'), 'name'=>"Reports"], ['name'=>"Out of Stock"]
        ];

            if ( request()->ajax()) {
                $business_id = Auth::user()->business_id;
                $Sku = Sku::where('business_id','=',$business_id)->where('quantity', 0)->orderBy('updated_at', 'desc');
                return Datatables::eloquent($Sku)
                    // ->addColumn('category_name', function(Sku $SKSU) {
                    //                 $category = Category::find($SKSU->category);
                    //                 if($category){
                    //                    return  $category->name;
                    //                 }
                    //             })
                    ->addColumn('image', function(Sku $SKSU) {
                                    return $SKSU->SkuImage();
                                })
                    // ->addColumn('brand_name', function(Sku $SKSU) {
                    //                 $Brand = Brand::find($SKSU->brand);
                    //                 if($Brand){
                    //                    return  $Brand->name;
                    //                 }
                    //                 else {
                    //                     return "--";
                    //                 }
                    //             })
                    ->addColumn('supplier_name', function(Sku $SKSU) {
                                    $Supplier = Supplier::find($SKSU->supplier);
                                    if($Supplier){
                                       return  $Supplier->company;
                                    }
                                    else {
                                        return "--";
                                    }
                                })
                    ->make(true);
            }
        return view('reports.out_of_stock', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function productAlert(){
        $this->authorize('is_included_in_plan', 'stock_alert_monitoring');
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('ReportsController@productAlert'), 'name'=>"Reports"], ['name'=>"Product Alert"]
        ];

            if ( request()->ajax()) {
                $business_id = Auth::user()->business_id;
                $Sku = Sku::where('business_id','=',$business_id)->whereRaw('quantity <= alert_quantity')->where('quantity','>', 0)->orderBy('updated_at', 'desc');
                return Datatables::eloquent($Sku)
                // ->addColumn('category_name', function(Sku $SKSU) {
                //                 $category = Category::find($SKSU->category);
                //                 if($category){
                //                    return  $category->name;
                //                 }
                //             })
                ->addColumn('image', function(Sku $SKSU) {
                                return $SKSU->SkuImage();
                            })
                // ->addColumn('brand_name', function(Sku $SKSU) {
                //                 $Brand = Brand::find($SKSU->brand);
                //                 if($Brand){
                //                    return  $Brand->name;
                //                 }
                //                 else {
                //                     return "--";
                //                 }
                //             })
                ->addColumn('supplier_name', function(Sku $SKSU) {
                                $Supplier = Supplier::find($SKSU->supplier);
                                if($Supplier){
                                   return  $Supplier->company;
                                }
                                else {
                                    return "--";
                                }
                            })
                ->make(true);
            }
        return view('reports.product_alert', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function itemsNotMoving(Request $request) {
        $this->authorize('is_included_in_plan', 'items_not_moving');
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('ReportsController@itemsNotMoving'), 'name'=>"Reports"], ['name'=>"Items Not Moving"]
        ];

        if($request->get('days')){
            $selectedDays = $request->get('days');
        }
        else {
            $selectedDays = 7;
        }

            if ( request()->ajax()) {
                $business_id = Auth::user()->business_id;

                $days = $request->get('days');

                $Sku = Sku::where('business_id','=',$business_id)->where('updated_at', '<', Carbon::now()->subDays($days)->format('Y-m-d'))->where('quantity', '!=', '0');

                return Datatables::eloquent($Sku)
                ->addColumn('image', function(Sku $SKSU) {
                                return $SKSU->SkuImage();
                            })
                ->addColumn('supplier_name', function(Sku $SKSU) {
                                $Supplier = Supplier::find($SKSU->supplier);
                                if($Supplier){
                                   return  $Supplier->company;
                                }
                                else {
                                    return "--";
                                }
                            })
                ->make(true);
            }
        return view('reports.itemsNotMoving', [
            'breadcrumbs' => $breadcrumbs,
            'selectedDays' => $selectedDays,
        ]);
    }

    public function topSellingProducts(Request $request){
        $this->authorize('is_included_in_plan', 'top_selling_products');
        $breadcrumbs = [['link'=>"/",'name'=>"Home"],['link'=> action('ReportsController@topSellingProducts'), 'name'=>"Reports"], ['name'=>"Top Selling Products"]];
        $all_shops = $request->user()->business->shops->where('active', '!=', 0);

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

            $Skus = Products::select('products.seller_sku_id', 'sku.code as sku_code', 'sku.name as sku_name', DB::raw('ROUND(SUM(order_item.price + order.shipping_fee)) as total_price'), DB::raw('SUM(order_item.quantity) as total_quantity'))
                            ->join('order_item', 'order_item.product_id','=','products.id')
                            ->join('order', 'order.id','=','order_item.order_id')
                            ->join('sku', 'sku.id','=','products.seller_sku_id')
                            ->whereIn('order.shop_id', $shop_ids)
                            ->whereNotIn('order.status', Order::statusNotIncludedInSales())
                            ->groupBy('products.seller_sku_id')
                            ->groupBy('sku.code')
                            ->groupBy('sku.name')
                            ->orderBy('total_quantity', 'desc')
                            ->take($no_of_products);

            $daterange = explode('/', $request->get('daterange'));
            if(count($daterange) == 2){
                if($daterange[0] == $daterange[1]){
                    $Skus->whereDate('order_item.created_at', [$daterange[0]]);
                }else{
                    $Skus->whereDate('order_item.created_at', '>=', $daterange[0])->whereDate('order_item.created_at', '<=', $daterange[1]);
                }
                
            }

            $Skus = $Skus->get();
            $data = ['count' => 0];
            $report = [];
            foreach($Skus as $sku){
                $report[$sku->sku_code]['seller_sku'] = $sku->sku_code;
                $report[$sku->sku_code]['product_name'] = $sku->sku_name;
                $report[$sku->sku_code]['total_price'] =  $sku->total_price;
                $report[$sku->sku_code]['total_quantity'] =  $sku->total_quantity;

                $data['count'] += 1;
            }

            if ($request->get('shop') == null || in_array('0', explode(',', $request->get('shop')))) {
                $pos_sales = Sales::where('business_id', Auth::user()->business_id)->where('status', '!=', 'canceled');
                if(count($daterange) == 2){
                    if($daterange[0] == $daterange[1]){
                        $pos_sales->where('date', [$daterange[0]]);
                    }else{
                        $pos_sales->where('date', '>=', $daterange[0])->whereDate('date', '<=', $daterange[1]);
                    }
                    
                }

                $pos_sales = $pos_sales->get();
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
            }
            uasort($report, function ($a, $b) {
                return $b['total_quantity'] <=> $a['total_quantity'];
            });
            $data['report'] = array_slice($report, 0, 10, true);
            return $data;
        }
        return view('reports.topSellingProducts', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => $all_shops,
        ]);
    }

    public function dailySales(Request $request){
        $this->authorize('is_included_in_plan', 'daily_sales');
        $breadcrumbs = [['link'=>"/",'name'=>"Home"],['link'=> action('ReportsController@dailySales'), 'name'=>"Daily Sales"], ['name'=>"Daily Sales"]];
        $all_shops = $request->user()->business->shops->where('active', '!=', 0);

        if ( request()->ajax()) {
            $shops = $request->user()->business->shops;
            if($request->get('shop') != ''){
                    $shops = $shops->whereIn('id', explode(",", $request->get('shop')));
            }
            $shop_ids = $shops->pluck('id');

            $order = Order::select(DB::raw('DATE(order.created_at) as date'), DB::raw('COUNT(DISTINCT order.id) as total_orders'), DB::raw('SUM(order.price + order.shipping_fee) as total_price'), DB::raw('SUM(order.items_count) as total_item_count'))
                ->whereIn('order.shop_id', $shop_ids)
                ->whereNotIn('order.status', Order::statusNotIncludedInSales())
                ->orderBy('date', 'asc')
                ->groupBy('date');

            $daterange = explode('/', $request->get('daterange'));
            if(count($daterange) == 2){
                if($daterange[0] == $daterange[1]){
                    $order->whereDate('order.created_at', [$daterange[0]]);
                }else{
                    $order->whereDate('order.created_at', '>=', $daterange[0])->whereDate('order.created_at', '<=', $daterange[1]);
                }
            }

            $order = $order->get();
            $data = ['count' => count($order)];
            $report = [];
            foreach($order as $order) {
                $date = $order->date;
                $report[$date]['total_orders'] = $order->total_orders;
                $report[$date]['total_quantity'] = $order->total_item_count;
                $report[$date]['total_sales'] = $order->total_price;
                $report[$date]['date'] = Utilities::format_date($order->date, 'M d, Y');
            }
            if ($request->get('shop') == null || in_array('0', explode(',', $request->get('shop')))) {
                $sales = $request->user()->business->sales()->select(DB::raw("DATE(sales.date) as date"), DB::raw('COUNT(DISTINCT sales.id) as total_sales'), DB::raw('SUM(sales.grand_total) as grand_total'))
                    // ->join('sale_items', 'sale_items.sales_id','=','sales.id')
                    ->where('status', '!=', 'canceled')
                    ->groupBy('date');
                    if(count($daterange) == 2){
                        if($daterange[0] == $daterange[1]){
                            $sales->whereDate('sales.date', [$daterange[0]]);
                        }else{
                            $sales->whereDate('sales.date', '>=', $daterange[0])->whereDate('sales.date', '<=', $daterange[1]);
                        }
                    }
                $sales = $sales->get();

                foreach ($sales as $sale) {
                    $date = $sale->date;
                    if (!isset($report[$date])) {
                        $report[$date] = [
                            'total_orders' => 0,
                            'total_sales' => 0,
                            'total_quantity' => 0,
                        ];    
                    }
                    $report[$date]['total_orders'] += $sale->total_sales;
                    $report[$date]['total_sales'] += $sale->grand_total;
                    // $report[$date]['total_quantity'] += $sale->total_items;
                    foreach ($sale->items as $item) {
                        $report[$date]['total_quantity'] += $item->quantity;
                    }
                    $report[$date]['date'] = Utilities::format_date($sale->date, 'M d, Y');
                }
                $data['count'] += count($sales);
            }
            $data['report'] = $report;
            return $data;
        }
            
        return view('reports.dailySales', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => $all_shops,
        ]);
    }

    public function monthlySales(Request $request){
        $this->authorize('is_included_in_plan', 'monthly_sales');
        $breadcrumbs = [['link'=>"/",'name'=>"Home"],['link'=> action('ReportsController@monthlySales'), 'name'=>"Monthly Sales"], ['name'=>"Monthly Sales"]];
        $all_shops = $request->user()->business->shops->where('active', '!=', 0);

        if ( request()->ajax()) {
            $shops = $request->user()->business->shops;
            if($request->get('shop') != ''){
                    $shops = $shops->whereIn('id', explode(",", $request->get('shop')));
            }
            $shop_ids = $shops->pluck('id');

            $order = Order::select(DB::raw("DATE_FORMAT(order.created_at, '%Y-%m') as monthly"), DB::raw('COUNT(DISTINCT order.id) as total_orders'), DB::raw('SUM(order.price + order.shipping_fee) as total_price'), DB::raw('SUM(order.items_count) as total_item_count'))
                ->whereIn('order.shop_id', $shop_ids)
                ->whereNotIn('order.status', Order::statusNotIncludedInSales())
                ->orderBy('monthly', 'asc')
                ->groupBy('monthly');

            $order = $order->get();
            $data = ['count' => count($order)];
            $report = [];
            foreach($order as $order) {
                $date = $order->monthly;
                $report[$date]['total_orders'] = $order->total_orders;
                $report[$date]['total_quantity'] = $order->total_item_count;
                $report[$date]['total_sales'] = $order->total_price;
                $report[$date]['date'] = Utilities::format_date($order->monthly, 'M Y');
            }
            if ($request->get('shop') == null || in_array('0', explode(',', $request->get('shop')))) {
                $sales = $request->user()->business->sales()->select(DB::raw("DATE_FORMAT(sales.date, '%Y-%m') as monthly"), DB::raw('COUNT(DISTINCT sales.id) as total_sales'), DB::raw('SUM(sales.grand_total) as grand_total'))
                    // ->join('sale_items', 'sale_items.sales_id','=','sales.id')
                    ->where('status', '!=', 'canceled')
                    ->groupBy('monthly')
                    ->get();
                foreach ($sales as $sale) {
                    $date = $sale->monthly;
                    if (!isset($report[$date])) {
                        $report[$date] = [
                            'total_orders' => 0,
                            'total_sales' => 0,
                            'total_quantity' => 0,
                        ];    
                    }
                    $report[$date]['total_orders'] += $sale->total_sales;
                    $report[$date]['total_sales'] += $sale->grand_total;
                    // $report[$date]['total_quantity'] += $sale->total_items;
                    foreach ($sale->items as $item) {
                        $report[$date]['total_quantity'] += $item->quantity;
                    }
                    $report[$date]['date'] = Utilities::format_date($sale->monthly, 'M Y');
                }
                $data['count'] += count($sales);
            }
            $report_length = 12;
            $report_offset = count($report)-$report_length;
            if ($report_offset < 0) {
                $report_offset = 0;
            }
            $data['report'] = array_slice($report, $report_offset, $report_length, true);
            return $data;
        }
            
        return view('reports.monthlySales', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => $all_shops,
        ]);
    }
}