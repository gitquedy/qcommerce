<?php

namespace App\Http\Controllers;

use App\Products;
use App\Shop;
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

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $Products_unseen =  Products::where('seen','=',0);
        
        
        
        
        $all_shops = Shop::where('user_id', $request->user()->id)->orderBy('updated_at', 'desc')->get();
        $Shop_array = array();
        foreach($all_shops as $all_shopsVAL){
            $Shop_array[] = $all_shopsVAL->id;
        }
        
        $Products_unseen =  Products::whereIn('shop_id',$Shop_array)->where('seen','=',0)->get();
        
        foreach($Products_unseen as $Products_unseenVAL){
            $tmp_pro = Products::find($Products_unseenVAL->id);
            $tmp_pro->seen = 1;
            $tmp_pro->save();
            
        }
        
        
        
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('ProductController@index'), 'name'=>"Products"], ['name'=>"list of Products"]
        ];
        
        
        
        
        $statuses = array();

        
    if ( request()->ajax()) {
        
           
           $Products = Products::with('shop')->orderBy('updated_at', 'desc');
           
           if($request->get('shop', 'all') != 'all'){
                $Products->where('shop_id', $request->get('shop'));
           }else{
               $Products->whereIn('shop_id', $Shop_array);
           }
           
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
            ->addColumn('action', function(Products $product) {
                            return '<div class="btn-group dropup mr-1 mb-1">
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                    Action<span class="sr-only">Toggle Dropdown</span></button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="'.$product->Url.'"  target="_blank" ><i class="fa fa-folder-open aria-hidden="true""></i> View</a>
                        <a class="dropdown-item" href="'.route('product.edit',array('id'=>$product->id)).'" ><i class="fa fa-edit aria-hidden="true""></i> Edit</a>
                        <a class="dropdown-item" onclick="duplicate_product('.$product->id.','.$product->shop_id.')" ><i class="fa fa-copy aria-hidden="true""></i> Duplicate Product</a>
                        
                    </div></div>';
                                })
                ->make(true);
        }
        
        return view('product.index', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => $all_shops,
            'statuses' => $statuses,
        ]);
    }
    
    
    public function edit($id=""){
        
        Products::sync_single_product($id);

        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('ProductController@index'), 'name'=>"Product"], ['name'=>"Product Edit"]
        ];
        
        $product = Products::find($id);
        
        return view('product.edit', [
            'breadcrumbs' => $breadcrumbs,
            'product' => $product,
        ]);
        
        
        
    }
    
    
    public function update(Request $request){
        
        // echo "<pre>";
        
        // print_r($request->input());
        
        // die();
        
        
        
        $shop_id = $request->shop_id;
        
        $access_token = Shop::find($shop_id)->access_token;
        
        $images = $request->input('Image');
        
        $image_xml = '<Images>';
        
        foreach($images as $imagesVAL){
            $image_xml .= '<Image>'.$imagesVAL.'</Image>';
        }
        
       $short_description =  Helper::minifier($request->short_description);
       $description = Helper::minifier($request->description);
       

        
        
        $image_xml .= '</Images>';
                        
        
        
                $xml = '<?xml version="1.0" encoding="UTF-8" ?>
                    <Request>
                        <Product>
                            <Attributes>
                                <name>'.$request->name.'</name>
                                <short_description><![CDATA['.$short_description.']]></short_description>
                                <description><![CDATA['.$description.']]></description>
                                <brand>'.$request->brand.'</brand>
                                <model>'.$request->model.'</model>
                            </Attributes>
                            <Skus>
                                <Sku>
                                    <SellerSku>'.$request->SellerSku.'</SellerSku>
                                    
                                    <package_length>'.$request->package_length.'</package_length>
                                    <package_height>'.$request->package_height.'</package_height>
                                    <package_weight>'.$request->package_weight.'</package_weight>
                                    <package_width>'.$request->package_width.'</package_width>
                                    '.$image_xml.'
                                    <quantity>'.$request->quantity.'</quantity>
                                    <max_delivery_time>'.$request->max_delivery_time.'</max_delivery_time>
                                    <min_delivery_time>'.$request->min_delivery_time.'</min_delivery_time>
                                    <package_width>'.$request->package_width.'</package_width>
                                    <color_family>'.$request->color_family.'</color_family>
                                    <package_height>'.$request->package_height.'</package_height>
                                    <special_price>'.$request->special_price.'</special_price>
                                    <price>'.$request->price.'</price>
                                    <package_length>'.$request->package_length.'</package_length>
                                    <package_weight>'.$request->package_weight.'</package_weight>
                                    <Available>'.$request->Available.'</Available>
                                    <Status>'.$request->Status.'</Status>
                                    
                    
                                    
                                </Sku>
                            </Skus>
                        </Product>
                    </Request>';
                    
                    
                    

                    
            
            $response =  Products::product_update($access_token,$xml);
            
            try {
                
                $obj = json_decode($response);
                
            }
            catch(Exception $e) {
                $request->session()->flash('flash_error', 'somthing went wrong !');
            }
            
            if(isset($obj->code)){
                if($obj->code==0){
                    $request->session()->flash('flash_success', 'Product Update Success !');
                }else{
                    $request->session()->flash('flash_error', $obj->message);
                }
            }
            

            
            Products::sync_single_product($request->id);
            
            
        return redirect('/product');
        
    }
    
    public function upload_image(Request $request){
        
        $result =  Products::upload_image($request->shop_id,$request->base_64_image);
        
        echo $result;
        
    }
    
    public function process_duplicate_product(Request $request){
        
        $response = Products::duplicate_product($request->product_id,$request->shop_id);
        
        try {
                
                $obj = json_decode($response);
                
            }
            catch(Exception $e) {
                $request->session()->flash('flash_error', $response);
            }
            
            if(isset($obj->code)){
                if($obj->code==0){
                    $request->session()->flash('flash_success', 'Product Duplicated Success !');
                }else{
                    $request->session()->flash('flash_error', $response);
                }
            }
         
        
        return redirect('/product');
    }
    
    
    
    
    public function mass_copy(Request $request){
        

        
        try {
            $products = json_decode($request->products);
        }
        catch(Exception $e) {
            $request->session()->flash('flash_error', 'Invalid Products ');
            return redirect('/product');
        }
        
        $feedback = array();
        $count_success = 0;
        $count_failed = 0;
        
        foreach($products as $productsVAL){
         
          $response = Products::duplicate_product($productsVAL,$request->shop_id);
          
            try {
                
                $obj = json_decode($response);
                
            }
            catch(Exception $e) {
                $feedback[] = $response;
            }
            
            if(isset($obj->code)){
                if($obj->code==0){
                    $count_success++;
                }else{
                    $count_failed++;
                    $feedback[] = $response;
                }
            }
         
        }
        
        $flash_message  = "Success : ".$count_success." Failed : ".$count_failed." Logs : ".implode("----",$feedback);
        
        
        
        $request->session()->flash('flash_success', $flash_message);
        
        return redirect('/product');
    }
    
    
    
    public function bulkremove(Request $request){
        
        
        $x = Products::remove_product($request->ids);
        
        $output = ['success' => 1,
                        'msg' => implode("------",$x),
                    ];
        echo json_encode($output);
        
    }
    

    public function ajaxlistproduct(Request $request) {
        $output = Products::where('shop_id',$request->shop_id)->get();
        echo json_encode($output);
    }
    
    
    
    
    
    



}
