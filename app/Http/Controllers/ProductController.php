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
        $all_shops = $request->user()->shops;

        $shop_ids = $all_shops->pluck('id');
        $lazada_count = Products::whereIn('shop_id' , $shop_ids)->where('site', 'lazada')->count();
        $shopee_count = Products::whereIn('shop_id' , $shop_ids)->where('site', 'shopee')->count();

        if($request->get('site') == 'shopee'){
           $all_shops = $all_shops->where('site', 'shopee');
           $statuses = Products::$shopeeStatuses;
        }else{
           $statuses = Products::$lazadaStatuses;
           $all_shops = $all_shops->where('site', 'lazada');
        }

        if($request->get('status')){
          $selectedStatus = $request->get('status');
        }
        else {
          $selectedStatus = 'all';
        }
         $Products_unseen =  Products::whereIn('shop_id',$shop_ids)->update(['seen' => 'yes']);

        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('ProductController@index'), 'name'=>"Products"], ['name'=>"List of Products"]
        ];

        if ( request()->ajax()) {
               $Products = Products::with('shop')->orderBy('updated_at', 'desc');
               
               if($request->get('shop') != ''){
                    $Products->where('shop_id', $request->get('shop'));
               }else{
                   $Products->whereIn('shop_id', $shop_ids);
               }
               

               $status = $request->get('status');
               if($status != 'all') {
                  $Products->where('status', $status);
               }
                  
               $Products->where('site', $request->get('site', 'lazada'));
               
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
                ->addColumn('getImgAndIdDisplay', function(Products $product) {
                                return $product->getImgAndIdDisplay();
                 })
                ->addColumn('action', function(Products $product) {
                    $actions = '';
                    if($product->site == 'lazada'){
                        $actions = '<div class="btn-group dropup mr-1 mb-1">
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                        Action<span class="sr-only">Toggle Dropdown</span></button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="'.$product->Url.'"  target="_blank" ><i class="fa fa-folder-open aria-hidden="true""></i> View</a>
                            <a class="dropdown-item" href="'. action('ProductController@edit', $product->id).'" ><i class="fa fa-edit aria-hidden="true""></i> Edit</a>
                            <a class="dropdown-item modal_button" href="#" data-href="'. action('ProductController@duplicateForm') .'?ids='. $product->id .'"><i class="fa fa-copy aria-hidden="true""></i> Duplicate Product</a>
                        </div></div>';
                    }else if($product->site == 'shopee'){
                            $actions = '<div class="btn-group dropup mr-1 mb-1">
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                            Action<span class="sr-only">Toggle Dropdown</span></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="'.$product->Url.'"  target="_blank" ><i class="fa fa-folder-open aria-hidden="true""></i> View</a>
                                <a class="dropdown-item modal_button" href="#" data-href="'. action('ProductController@duplicateForm') .'?ids='. $product->id .'"><i class="fa fa-copy aria-hidden="true""></i> Duplicate Product</a>';
                            $actions .= '</div></div>';
                        }
                        return $actions;
                    })
                    ->rawColumns(['getImgAndIdDisplay', 'action'])
                    ->make(true);
            }
            return view('product.index', [
                'breadcrumbs' => $breadcrumbs,
                'all_shops' => $all_shops,
                'lazada_count' => $lazada_count,
                'shopee_count' => $shopee_count,
                'statuses' => $statuses,
                'selectedStatus' => $selectedStatus,
            ]);
        }
    
    
    public function edit(Products $product, Request $request){
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('ProductController@index'), 'name'=>"Product"], ['name'=>"Edit Product"]
        ];
        $product_details = $product->getDetails();
        if($product_details->code != "0"){
            $request->session()->flash('flash_error', 'Sorry something went wrong');
            return redirect(action('ProductController@index') . '?site=lazada');
        }
        return view('product.edit', [
            'breadcrumbs' => $breadcrumbs,
            'product' => $product,
            'product_details' => $product_details
        ]);
    }
    
    
    public function update(Request $request, Products $product){
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
            $response =  $product->product_update($xml);
            $product->sync();
            try {
                $obj = json_decode($response);
            }
            catch(Exception $e) {
                $request->session()->flash('flash_error', 'somthing went wrong !');
            }
            if(isset($obj->code)){
                if($obj->code==0){
                    $request->session()->flash('flash_success', $product->name . ' successfully updated');
                }else{
                    $request->session()->flash('flash_error', $obj->message);
                }
            }
        return redirect(action('ProductController@index') . '?site=' . $product->site );
        
    }
    
    public function upload_image(Request $request){
        
        $result =  Products::upload_image($request->shop_id,$request->base_64_image);
        
        echo $result;
        
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



    public function searchProduct(Request $request){

       $search = $request->get('search');
       $site = $request->get('site');
       $shop_ids = $request->user()->shops->pluck('id')->toArray();
       $product = Products::where('site', $site)->whereIn('shop_id', $shop_ids)->where('item_id', $search)->orWhere('SellerSku', $search)->orWhere('SkuId', $search)->first();
       if($product == null){
            $output = ['success' => 0,
                        'msg' => 'Product not found',
            ];
       }else{
            $output = ['success' => 1,
                        'msg' => 'Product Found',
                        'product' => $product,
                        'html' => '<tr>
                        <td><input type="hidden" name="product['. $product->id .']" class="item_ids" value="'. $product->id .'">'. $product->getImgAndIdDisplay() .'</td>
                        <td>'. $product->SellerSku .'</td>
                        <td>'. $product->name .'</td>
                        <td><button class="btn btn-danger remove_row"><i class="fa fa-trash"></i></button></td>
                      </tr>'
            ];
       }
       return response()->json($output);
    }

    public function duplicateForm(Request $request){
        $products = Products::whereIn('id',explode(',', $request->ids))->get();

        $shops = $request->user()->shops->where('site', $products->first()->site);
        
        return view('product.modal.duplicate', compact('products', 'shops'));
    }

    public function duplicateProudcts(Request $request){
        if($request->input(['product']) == null){
            return response()->json(['msg' => 'No products found please add products first.']);
        }
        $validator = Validator::make($request->all(), [
            'shop_id' => ['required', 'exists:shop,id'],
            'logistic_ids' => ['required_if:site,shopee']
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }
        try {
            DB::beginTransaction();

            $duplicate_to = Shop::find($request->shop_id);
            $products = Products::whereIn('id', $request->product)->whereIn('shop_id', $request->user()->shops->pluck('id')->toArray())->get();
            $site = $request->site;
            $msgs = [];
            if($request->site == 'lazada'){
                foreach($products as $product){
                    if($duplicate_to->site == $product->site){
                        $msgs[] = $product->duplicate_product($duplicate_to);
                    }
                }
            }else if($request->site == 'shopee'){
                foreach($products as $product){
                    if($duplicate_to->site == $product->site){
                        $msgs[] = $product->duplicate_product($duplicate_to, $request->logistic_ids);
                    }
                }
            }
            $msgs = implode('<br>', $msgs);
            DB::commit();
            $output = ['success' => 1,
                        'msg' => $msgs,
                    ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }
}
