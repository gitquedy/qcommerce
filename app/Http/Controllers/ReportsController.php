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
        if ( request()->ajax()) {
            $products = Products::all();
            return Datatables::eloquent($products)
            ->make(true);
        }
        return view('reports.topSellingProducts', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
    public function dailySales(Request $request){
        $breadcrumbs = [['link'=>"/",'name'=>"Home"],['link'=> action('ReportsController@dailySales'), 'name'=>"Reports"], ['name'=>"Daily Sales"]];
        if ( request()->ajax()) {
            return Datatables::eloquent($Sku)
            ->make(true);
        }
        return view('reports.dailySales', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}