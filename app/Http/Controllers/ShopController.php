<?php

namespace App\Http\Controllers;

use Auth;
use App\Shop;
use App\Products;
use App\Warehouse;
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
use App\Shopee;
use Oseintow\Shopify\Facades\Shopify;


class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('ShopController@index'), 'name'=>"Shop List"], ['name'=>"Shops"]
        ];
        if ( request()->ajax()) {
           $shop = $request->user()->business->shops()->orderBy('shop.updated_at', 'desc');
            return Datatables::eloquent($shop)
            ->editColumn('site', function(Shop $shop) {
                            return '<img src="'.asset('images/shop/icon/'.$shop->site.'.png').'" style="display:block; width:100%; height:auto;">';
                        })
            ->addColumn('warehouse_name', function(Shop $shop) {
                return isset($shop->warehouse->name)?$shop->warehouse->name:'[Deleted Warehouse]';
            })
            // ->addColumn('reSync', function(Shop $shop) {
            //                $actions = '<div class="btn-group dropright mr-1 mb-1">
            //                             <button type="button" class="btn btn-primary round dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            //                             <i class="fa fa-refresh"> Resync</i><span class="sr-only">Toggle Dropdown</span></button>
            //                             <div class="dropdown-menu">
            //                                 <a class="dropdown-item ajax" href="#" data-href="'. action('ShopController@reSyncProducts', $shop->id) .'"><i class="feather icon-package  aria-hidden="true""></i> Products</a>
            //                                 <a class="dropdown-item ajax" href="#" data-href="'. action('ShopController@reSyncOrders', $shop->id) .'"><i class="fa fa-shopping-cart aria-hidden="true""></i> Orders</a>
            //                             </div></div>';
            //         return $actions;
            //             })
            ->addColumn('statusChip', function(Shop $shop) {
                            $html = '';
                            if($shop->active == 1){
                                $html = '<div class="chip chip-primary"><div class="chip-body"><div class="chip-text">Active</div></div></div>';
                            }else if($shop->active == 2){
                                $html = '<div class="chip chip-info"><div class="chip-body"><div class="chip-text">Syncing</div></div></div>';
                            }
                           return $html;
                        })
            ->addColumn('orders', function(Shop $shop) {
                            $html = $shop->orders()->whereDate('created_at','=',date('Y-m-d'))->count();
                            $html = '<div class="chip chip-info"><div class="chip-body"><div class="chip-text">'.$html.'</div></div></div>';
                           return $html;
                        })
            ->addColumn('pending_count', function(Shop $shop) {
                           return '<div class="chip chip-danger"><div class="chip-body"><div class="chip-text">'.
                        $shop->orders('pending')->count().'</div></div></div>';
                        })
            ->addColumn('ready_to_ship_count', function(Shop $shop) {
                           return '<div class="chip chip-warning"><div class="chip-body"><div class="chip-text">'.
                        $shop->orders('ready_to_ship')->count().'</div></div></div>';
                        })
            ->addColumn('shipped_count', function(Shop $shop) {
                           return '<div class="chip chip-success"><div class="chip-body"><div class="chip-text">'.
                        $shop->orders('shipped')->count().'</div></div></div>';
                        })
            ->addColumn('delivered_count', function(Shop $shop) {
                           return '<div class="chip chip-success"><div class="chip-body"><div class="chip-text">'.
                        $shop->orders('delivered')->count().'</div></div></div>';
                        })
            ->addColumn('products', function(Shop $shop) {
                           $product_count =  Products::where('shop_id','=',$shop->id)->get()->count();
                           return '<div class="chip chip-info"><div class="chip-body"><div class="chip-text">'.$product_count.'</div></div></div>';
                        })
            ->addColumn('action', function(Shop $shop) {
                    $actions = '<div class="btn-group dropup mr-1 mb-1">
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                    Action<span class="sr-only">Toggle Dropdown</span></button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item modal_button" href="#" data-href="'. action('ShopController@edit', $shop->id) .'"><i class="fa fa-edit aria-hidden="true""></i> Edit</a>
                    </div></div>';
                    return $actions;
             })
            ->rawColumns(['site', 'shipped_count', 'pending_count', 'ready_to_ship_count', 'delivered_count', 'statusChip','orders','products', 'action', 'reSync'])
            ->make(true);
        }
        return view('shop.index', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {  
        $breadcrumbs = [
          ['link'=>"/",'name'=>"Home"], ['link'=> route('shop.create'),'name'=>"Add Shop"], ['name'=>"Shop"]
        ];
        $warehouses = Warehouse::where('business_id', Auth::user()->business_id)->get();
        // die(var_dump(request()->session()));
        return view('shop.create', [
          'breadcrumbs' => $breadcrumbs,
          'warehouses' => $warehouses
        ]);
    }

    public function form(Request $request){
        $breadcrumbs = [
          ['link'=>"/",'name'=>"Home"], ['link'=> route('shop.create'),'name'=>"Add Shop"], ['name'=>"Shop"]
        ];

        if($request->input('code') == null && $request->input('shop_id') == null && $request->input('shop') == null){
            $request->session()->flash('alert-class', 'error');
            $request->session()->flash('status', 'Invalid parameters.');
            return redirect(action('ShopController@create'));
        }
        $warehouses = Warehouse::where('business_id', Auth::user()->business_id)->get();
        return view('shop.form', [
          'breadcrumbs' => $breadcrumbs,
          'warehouses' => $warehouses
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), ['name' => ['required', 'regex:/^[\pL\s\-]+$/u'], 'short_name' => 'required'], ['name.regex' => 'Only character\'s are allowed']);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
            }
        try {
            $data = $request->all();
            DB::beginTransaction();
            if($data['code'] != null && $data['shop_id'] == null && $data['shop'] == null){ // lazada
                $client = new LazopClient("https://auth.lazada.com/rest", Lazop::get_api_key(), Lazop::get_api_secret());
                $r = new LazopRequest("/auth/token/create");
                $r->addApiParam("code", $data['code']);
                $response = $client->execute($r);

                $responseData = json_decode($response, true);
            
                if(! array_key_exists('account', $responseData)){
                    $output = ['success' => 0,
                        'msg' => 'Sorry something went wrong, please try again later. [ '. $responseData['message'] .' ]'
                    ];
                    return response()->json($output);
                }
                if(Shop::where('email', $responseData['account'])->count() >= 1){
                    $output = ['success' => 0,
                            'msg' => 'Shop '. $responseData['account'] .' already exists!',
                            'redirect' => action('ShopController@index')
                        ];
                    return response()->json($output);
                }
                $data['refresh_token'] = $responseData['refresh_token'];
                $data['access_token'] = $responseData['access_token'];
                $data['email'] = $responseData['account'];
                $data['expires_in'] = Carbon::now()->addDays(6);
                $data['business_id'] = $request->user()->business_id;
                $data['warehouse_id'] = $request->warehouse_id;
                $data['site'] = 'lazada';
                $shop = Shop::create($data);
                $output = ['success' => 1,
                    'msg' => 'Shop added successfully!',
                    'redirect' => action('ShopController@index')
                ];
            }else if($data['shop_id'] != null){ //shopee

                $client = new \Shopee\Client([
                    'secret' => Shopee::shopee_app_key(),
                    'partner_id' => Shopee::shopee_partner_id(),
                    'shopid' => (int) $data['shop_id'],
                ]);
                $client = $client->shop->getShopInfo()->getData();
                if(array_key_exists('error', $client)){
                    $output = ['success' => 0,
                        'msg' => 'Sorry something went wrong, please try again later. '. $client['error'] .': [ '. $client['msg'] .' ]',
                        'redirect' => action('ShopController@index')
                    ];
                    return response()->json($output);
                }
                $shop = Shop::where('shop_id', $data['shop_id'])->where('business_id', $request->user()->business_id)->first();
                if($shop != null){
                    $output = ['success' => 0,
                            'msg' => 'Shop '. $shop->shop_id .' '. $client['shop_name'] .' already exists!',
                            'redirect' => action('ShopController@index')
                        ];
                    return response()->json($output);
                }
                $data = [
                    'expires_in' => Carbon::now()->addDays(364),
                    'site' => 'shopee',
                    'shop_id' => $client['shop_id'],
                    'name' => $data['name'],
                    'short_name' => $data['short_name'],
                    'business_id' => $request->user()->business_id,
                    'warehouse_id' => $request->warehouse_id,
                ];
                $shop = Shop::create($data);
                $output = ['success' => 1,
                        'msg' => 'Shop added successfully!',
                        'redirect' => action('ShopController@index')
                    ];
            }else if($data['code'] != null && $data['shop'] != null && $data['shop_id'] == null){ // shopify
                if(Shopify::verifyRequest($request->only(['code','shop', 'hmac', 'timestamp']))){
                    $accessToken = Shopify::setShopUrl($data['shop'])->getAccessToken($data['code']);
                    $data = [
                        'expires_in' => Carbon::now()->addDays(364),
                        'domain' => $data['shop'],
                        'access_token' => $accessToken,
                        'site' => 'shopify',
                        'name' => $data['name'],
                        'short_name' => $data['short_name'],
                        'business_id' => $request->user()->business_id,
                        'warehouse_id' => $request->warehouse_id,
                    ];
                    $shop = Shop::updateOrCreate(['domain' => $data['domain']],$data);
                    $output = ['success' => 1,
                            'msg' => 'Shop added successfully!',
                            'redirect' => action('ShopController@index')
                        ];
                }else{
                    $output = ['success' => 0,
                            'msg' => 'Verification Failed. Please try again',
                            'redirect' => action('ShopController@create')
                        ];
                }
               
            }
            DB::commit();
          
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }

    public function edit(Shop $shop){
        $warehouses = Warehouse::where('business_id', Auth::user()->business_id)->get();
        return view('shop.edit', compact('shop', 'warehouses'));
    }

    public function update(Shop $shop, Request $request){
        $validator = Validator::make($request->all(), [
                'name' => ['required', 'regex:/^[\pL\s\-]+$/u'],
                'short_name' => 'required',
                'warehouse_id' => 'required'
            ], 
        ['name.regex' => 'Only character\'s are allowed']);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors','error' => $validator->errors()]);
        }
        try {
            $shop->update($request->only(['short_name', 'name', 'warehouse_id']));

            $output = ['success' => 1,
                'msg' => 'Shop updated successfully!',
            ];
            DB::commit();
          
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }

    public function shopeeGetLogistics(Request $request, Shop $shop){
        $logistics = $shop->shopeeGetLogistics();
        $data = [];
        foreach($logistics['logistics'] as $logistic){
            if($logistic['enabled']){
                $data[] =  [
                    'weight_limits' => $logistic['weight_limits'],
                    'logistic_name' => $logistic['logistic_name'],
                    'logistic_id' => $logistic['logistic_id'],
                    'preferred' => $logistic['preferred'],
                ];
            }
        }
        return response()->json(['logistics' => $data]);
    }

    public function reSyncProducts(Request $request, Shop $shop){
        try {
              
              if($shop->site == 'shopee'){
                $shop->syncShopeeProducts(Carbon::now()->subDays(30)->format('Y-m-d'));
              }else if($shop->site == 'lazada'){
                $shop->syncLazadaProducts();
              }else if($shop->site == 'shopify'){
                $shop->syncShopifyProducts(Carbon::now()->subDays(30)->format('Y-m-d'));
              }

              $output = ['success' => 1,
                  'msg' => 'Products '. $shop->name .'['. $shop->short_name . '] successfully synced',
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

    public function massResyncProducts(Request $request, Shop $shop){
        $shops = Shop::where('business_id', $request->user()->business_id)->whereIn('id', $request->get('ids'))->get();
        foreach($shops as $shop){
            try {
            // $ids 
              if($shop->site == 'shopee'){
                $shop->syncShopeeProducts(Carbon::now()->subDays(30)->format('Y-m-d'));
              }else if($shop->site == 'lazada'){
                $shop->syncLazadaProducts();
              }else if($shop->site == 'shopify'){
                $shop->syncShopifyProducts(Carbon::now()->subDays(30)->format('Y-m-d'));
              }

              $output = ['success' => 1,
                  'msg' => 'Products successfully synced',
              ];
            
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
                $output = ['success' => 0,
                            'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                        ];
                 DB::rollBack();
            }
        }
        
        return response()->json($output);
    }

    public function massResyncOrders(Request $request){
        $shops = Shop::where('business_id', $request->user()->business_id)->whereIn('id', $request->get('ids'))->get();
        foreach($shops as $shop){
            try {
                  $shop->syncOrders(Carbon::now()->subDays(30)->format('Y-m-d'));
                  $output = ['success' => 1,
                      'msg' => 'Orders successfully synced',
                  ];
                
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
                $output = ['success' => 0,
                            'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                        ];
                 DB::rollBack();
            }
        }
        return response()->json($output);
    }


    
// massResyncOrders

    public function reSyncOrders(Request $request, Shop $shop){
         try {
              if($shop->site == 'shopee'){
                $shop->syncShopeeOrders(Carbon::now()->subDays(30)->format('Y-m-d'));
              }else if($shop->site == 'lazada'){
                $shop->syncLazadaOrders(Carbon::now()->subDays(30)->format('Y-m-d'));
              }
              $output = ['success' => 1,
                  'msg' => 'Orders '. $shop->name .'['. $shop->short_name . '] successfully synced',
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
