<?php

namespace App\Http\Controllers;

use App\Order;
use App\Shop;
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
use App\Products;
use App\Business;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use Auth;
use PDF;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('OrderController@index'), 'name'=>"Orders List"], ['name'=>"Orders All"]
        ];
        $all_shops = $request->user()->business->shops;
        $all_sites = array_values(array_unique($all_shops->pluck('site')->toArray()));
        if($request->get('site') == 'shopee'){
           $statuses = Order::$shopee_statuses;
           $all_shops = $all_shops->where('site', 'shopee');
           // $selectedStatuses = ['UNPAID','READY_TO_SHIP'];
        }else if ($request->get('site') == 'lazada'){
           $statuses = Order::$statuses;
           $all_shops = $all_shops->where('site', 'lazada');
           // $selectedStatuses = ['pending','ready_to_ship','shipped'];
        }else if ($request->get('site') == 'shopify'){
           $statuses = Order::$shopify_statuses;
           $all_shops = $all_shops->where('site', 'shopify');
           // $selectedStatuses = ['pending','ready_to_ship','shipped'];
        }else if ($request->get('site') == 'woocommerce'){
            $statuses = Order::$woocommerce_statuses;
            $all_shops = $all_shops->where('site', 'woocommerce');
            // $selectedStatuses = [];
         }
        if($request->get('status')){
          $selectedStatus = $request->get('status');
        }
        else {
          $selectedStatus = 'all';
        }

        $shop_ids = $all_shops->pluck('id')->toArray();
        $orders = Order::select('id')->whereIn('shop_id',$shop_ids)->update(['seen' => true]); 

     if ( request()->ajax()) {
           $shops = $request->user()->business->shops;
           $max_orders = Order::getMaxOrders();
           if ($max_orders == 0) {
                $shops_id = $shops->pluck('id')->toArray();
                $allshops_orders = Order::whereIn('shop_id', $shops_id)->pluck('id')->toArray();    //get max orders_processing from all shops before filtering
           }
           else {
                $shops_id = $shops->pluck('id')->toArray();
                $allshops_orders = Order::whereIn('shop_id', $shops_id)->orderBy('created_at', 'desc')->limit($max_orders)->pluck('id')->toArray();     //get max orders_processing from all shops before filtering
           }

           if($request->get('shop') != ''){
                $shops = $shops->whereIn('id', explode(",", $request->get('shop')));
           }
           $shops_id = $shops->pluck('id')->toArray();
           $status = $request->get('status');
           if($status == 'all') {
            //   $orders = Order::whereIn('shop_id', $shops_id); //with('shop')->
              $orders = Order::whereIn('shop_id', $shops_id)->whereIn('id', $allshops_orders);
           }
           else {
            //   $orders = Order::whereIn('shop_id', $shops_id)->where('status', $status); //with('shop')->
              $orders = Order::whereIn('shop_id', $shops_id)->whereIn('id', $allshops_orders)->where('status', $status);
           }

           $orders->where('site', $request->get('site', 'lazada'));
           $orders->orderBy('created_at', 'ASC');
           // $orders->orderBy('shop_id', 'ASC');
           
           if($request->get('site') == 'lazada'){
              $orders->orderByRaw('CASE WHEN status = "pending" THEN 1 WHEN status = "ready_to_ship" THEN 2 WHEN status = "shipped" THEN 3 else 4 END');
           }else if($request->get('site') == 'shopee'){
              $orders->orderByRaw('CASE WHEN status = "UNPAID" THEN 1 WHEN status = "READY_TO_SHIP" THEN 2 WHEN status = "SHIPPED" THEN 3 WHEN status = "COMPLETED" THEN 4 else 5 END');
              if($request->get('shipping_status') != 'ALL'){
                if($request->get('shipping_status') == 'to_process'){
                   $orders->where('tracking_no', '=', '');
                }else if($request->get('shipping_status') == 'processed'){
                  $orders->where('tracking_no', '!=', '');
                }
              }
           }else if ($request->get('site') == 'shopify'){
            $orders->orderByRaw('CASE WHEN status = "open" THEN 1 WHEN status = "closed" THEN 2 else 3 END');
           }

           if($request->printed){
            $orders->where('printed', 0);
           }

           $daterange = explode('/', $request->get('daterange'));
            if(count($daterange) == 2){
                if($daterange[0] == $daterange[1]){
                    $orders->whereDate('created_at', [$daterange[0]]);
                }else{
                    $orders->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
                }
            }
           
            return Datatables::eloquent($orders)
                ->addColumn('item_list', function(Order $order) {
                              $item_list = '';
                              foreach ($order->products as $item) {
                                if(isset($item->product)) {
                                  $images = explode("|", $item->product->Images);
                                  $image = $images[0];
                                  $name = $item->product->name;
                                  $sku = $item->product->SkuId;
                                }
                                else {
                                  $image = asset('images/pages/no-img.jpg');
                                  $name = 'Missing Product : '.$item->product_id;
                                  $sku = 'Missing Product';
                                }
                                $item_list .= '
                                      <div class="media">
                                        <img src="'.$image.'" alt="No Image Available" class="d-flex mr-1 product_image">
                                        <div class="media-body">
                                          <h6 class="mt-0">'.$name.'<span class="pull-right">x'.$item->quantity.'</span></h6>
                                          <p><small>['.$sku.']</small></p>
                                        </div>
                                      </div>';
                              }
                              return $item_list;
                                  })
                ->addColumn('idDisplay', function(Order $order) {
                              return $order->getImgAndIdDisplay();
                                  })
                ->addColumn('statusDisplay', function(Order $order) {
                            return ucwords(str_replace('_', ' ', $order->status));
                                })
                ->addColumn('printedDisplay', function(Order $order) {
                                if ($order->printed) {
                                    return '<h1><span style="color:green">&#10003;</span><h1>';
                                }
                                else {
                                    return '<h4><span style="color:red">&#10060;</span><h4>';
                                }
                            })
                ->addColumn('actions', function(Order $order) {
                            return $order->getActionsDropdown();
                                })
                ->addColumn('created_at_formatted', function(Order $order) {
                            return Utilities::format_date($order->created_at, 'M. d,Y H:i A');
                                })
                ->addColumn('created_at_human_read', function(Order $order) {
                            return Carbon::parse($order->created_at)->diffForHumans();
                                })
                ->addColumn('updated_at_at_human_read', function(Order $order) {
                            return Carbon::parse($order->updated_at)->diffForHumans();
                                })
                ->rawColumns(['actions', 'item_list', 'idDisplay', 'printedDisplay'])
                ->make(true);
         }
        
        return view('order.index', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => $all_shops,
            'all_sites' => $all_sites,
            'statuses' => $statuses,
            'selectedStatus' => $selectedStatus,
        ]);
    }

    public function cancelModal(Order $order){

      return view ('order.cancel', compact('order'));
    }

    public function cancelSubmit(Order $order, Request $request){
      try {
            DB::beginTransaction();
            $client =  $order->shop->shopeeGetClient();
            $result = $client->order->cancelOrder(['ordersn' => $order->ordersn, 'cancel_reason' => $request->reason])->getData();
            if(isset($result['modified_time'])){
              $order->update(['status' => 'CANCELLED']);
            }
            DB::commit();
            $output = ['success' => 1,
                        'msg' => $order->ordersn . ' Successfully cancelled!'
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



    public function cancel(Order $order,Request $request){
        try {
            $msg = $request->get('input');
            $items = $order->getOrderItems();
            $item_ids = $order->getItemIds($items);  
            $result = $order->cancel($item_ids, $msg);
            if(isset($result['message'])){
                $output = ['success' => 0,
                        'msg' => $result['message'],
                    ];
            }else{
                $output = ['success' => 1,
                    'msg' => 'Orders '. $order->id .' Canceled',
                ];
            }
            
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }    

    

   public function readyToShipShopee(Order $order,Request $request){
       return view ('order.ready_to_ship', compact('order'));
   }

   public function pickupDetailsShopee(Order $order,Request $request){
    $client = $order->shop->shopeeGetClient();
    $info_logistics = $client->logistics->getLogisticInfo(['ordersn' => $order->ordersn])->getData();
    $counter = 0;
    $info = [];
    foreach($info_logistics['pickup']['address_list'] as $i){
      $timeslot = 0;
      foreach($i['time_slot_list'] as $d){
        if(isset($d['date'])){
          $info['pickup']['address_list'][$counter]['time_slot_list'][$timeslot]['date'] = Carbon::createFromTimestamp($d['date'])->toDateString();
          $timeslot += 1;
        }
      }
      $counter += 1;
    }
    return view ('order.pickup_details', compact('order', 'info'));
   }
   public function pickupDetailsPostShopee(Order $order,Request $request){
    try {
      $client = $order->shop->shopeeGetClient();
      $params = ['ordersn' => $order->ordersn ,
       'pickup' => ['pickup_time_id' => $request->pickup_time_id, 'address_id' => (int)$request->address_id]
      ];
      $result = $client->logistics->init($params)->getData();
      $order->update(['tracking_no' => '123']);
      $output = ['success' => 1,
                    'msg' => 'Ready to ship Order Serial No: ' . $order->ordersn,
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
   

   public function readyToShipDropOff(Order $order,Request $request){
      $client = $order->shop->shopeeGetClient();
  
      $branch = $client->logistics->getBranch(['ordersn' => $order->ordersn])->getData();
      if(isset($branch['msg'])){
        $request->session()->flash('flash_error', $order->ordersn. ' ' .$branch['msg']);
          return redirect(action('OrderController@index'). '?site=shopee');
      }
      $params = ['ordersn' => $order->ordersn, 'dropoff' => ['branch_id' => $branch['branch']['branch_id']]];
      $result = $client->logistics->init($params)->getData();
      $request->session()->flash('flash_success', 'Ready to ship: Tracking No: ' . $result['tracking_number']);
      return redirect(action('OrderController@index'). '?site=shopee');
   }

    public function readyToShip(Order $order,Request $request){
        $order_id = $request->get('order_id');
        try {
            $items = $order->getOrderItems();
            $item_ids = $order->getItemIds($items);
            $result = $order->readyToShip($item_ids);
            if(isset($result['message'])){
                $order->updateTracking();
                $output = ['success' => 0,
                        'msg' => $result['message'],
                    ];
            }else{
                $output = ['success' => 1,
                    'msg' => 'Orders '. $order->ordersn .' Ready to Ship',
                ];
            }
            
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }

    public function readyToShipMultiple(Request $request){
        $order_ids = $request->ids;
        $error = 0;
        try {
            $orders = Order::whereIn('id', $order_ids)->get();
            foreach ($orders as &$order) {
              $items = $order->getOrderItems();
              $item_ids = $order->getItemIds($items);
              $tracking_code = $items['data'][0]['tracking_code'];
              $result = $order->readyToShip($item_ids, $tracking_code);
              if(isset($result['message'])){
                  $error++;
                  $output = ['success' => 0,
                          'msg' => $result['message'],
                      ];
              }else{
                  if(!$error){
                    $output = ['success' => 1,
                        'msg' => 'Orders '. $order->ordersn .' Ready to Ship',
                    ];
                  }
              }
            }
            
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }
    
    
    
    public function print_shipping($order_id="",Request $request){
            $order = Order::where('id', "=", $order_id)->first();
            if($order->site == 'lazada'){
              $order->update(['printed' => true]);
              // dd($order->ordersn);
              $Result = Order::get_shipping_level($order->ordersn);
              $jsOBJ = json_decode($Result);
              
              if($jsOBJ->code==0){
                  if(isset($jsOBJ->data->document->file)){
                      $document = base64_decode($jsOBJ->data->document->file);
                      echo $document;
                  }
              
              }else{
                  $request->session()->flash('flash_error',$Result);
                  return redirect('/order?site=lazada');
              }
            }else if ($order->site == 'shopee'){
              $client =$order->shop->shopeeGetClient();
              $order->update(['printed' => true]);
              $result = $client->logistics->getAirwayBill(['ordersn_list' => [$order->ordersn]])->getData();
              if(count($result['result']['airway_bills']) > 0){
                return redirect($result['result']['airway_bills'][0]['airway_bill']);
              }else{
                  $request->session()->flash('flash_error',$result['result']['errors'][0]['error_description']);
                  return redirect('/order?site=shopee');
              }
            }
            else if ($order->site == 'woocommerce') {
                $order->update(['printed' => true]);
                $client = new Party([
                    'name'          => $order->shop->name,
                    'address'       => $order->shop->warehouse->address,
                ]);

                if ($order->CustomerId == 0) {
                    $woocommerce = $order->shop->woocommerceGetClient();
                    $woo_order = $woocommerce->get('orders/'.$order->ordersn);

                    $customer = new Party([
                        'name'          => $woo_order->billing->first_name . ' ' . $woo_order->billing->last_name,
                        'address'       => $woo_order->billing->address_1.', '.(($woo_order->billing->address_2)?$woo_order->billing->address_2.', ':'').$woo_order->billing->city,
                        'email'         => ($woo_order->billing->email) ? $woo_order->billing->email : '',
                        'phone'         => ($woo_order->billing->phone) ? $woo_order->billing->phone : '',
                        'custom_fields' => [
                            'order number' => $order->ordersn,
                            'date' => Carbon::parse(date("m/d/Y",strtotime($woo_order->date_created) + 8 * 3600))->toDateTimeString(),
                        ],
                    ]);
                }
                else {
                    $customer = new Party([
                        'name'          => ($order->customer_first_name) ? $order->customer_first_name : '',
                        'address'       => ($order->customer->address) ? $order->customer->address : '',
                        'email'         => ($order->customer->email) ? $order->customer->email : '',
                        'phone'         => ($order->customer->mobile_num) ? $order->customer->mobile_num : '',
                        'custom_fields' => [
                            'order number' => $order->ordersn,
                            'date' => date('m/d/Y', strtotime($order->created_at)),
                        ],
                    ]);
                }

                $order_items = array();
                $total = 0;
                foreach ($order->products as $item) {
                    $order_items[] = (new InvoiceItem())
                        ->title((($item->product->SellerSku) ? '['.$item->product->SellerSku.'] - ' : '').$item->product->name)
                        ->pricePerUnit($item->price)
                        ->qty($item->quantity)
                        ->subTotalPrice($item->price*$item->quantity);

                    $total += $item->price*$item->quantity;
                }
                $invoice[$order->ordersn] = Invoice::make()
                ->seller($client)
                ->buyer($customer)
                ->addItems($order_items)
                ->totalAmount($total);

                return PDF::loadview('vendor.invoices.templates.default', ['invoices' => $invoice])->stream();
            }
    }
    
    public function print_shipping_mass(Request $request){
        $ids = json_decode($request->ids);
        foreach($ids as $orderVAL){
            $order = Order::where('id', "=", $orderVAL)->first();
            $order->printed = true;
            $order->save();

            if ($order->site == 'lazada') {
                $Result =    Order::get_shipping_level($order->ordersn);
                $jsOBJ = json_decode($Result);
                
                if($jsOBJ->code==0){
                    if(isset($jsOBJ->data->document->file)){
                        $document = base64_decode($jsOBJ->data->document->file);
                        echo $document."<hr/>";
                    }
                
                }else{
                    echo "For order# ".$orderVAL." Error - ".$Result."<hr/>";
                }
            }
            else if ($order->site == 'woocommerce') {
                $client = new Party([
                    'name'          => $order->shop->name,
                    'address'       => $order->shop->warehouse->address,
                ]);

                if ($order->CustomerId == 0) {
                    $woocommerce = $order->shop->woocommerceGetClient();
                    $woo_order = $woocommerce->get('orders/'.$order->ordersn);

                    $customer = new Party([
                        'name'          => $woo_order->billing->first_name . ' ' . $woo_order->billing->last_name,
                        'address'       => $woo_order->billing->address_1.', '.(($woo_order->billing->address_2)?$woo_order->billing->address_2.', ':'').$woo_order->billing->city,
                        'email'         => ($woo_order->billing->email) ? $woo_order->billing->email : '',
                        'phone'         => ($woo_order->billing->phone) ? $woo_order->billing->phone : '',
                        'custom_fields' => [
                            'order number' => $order->ordersn,
                            'date' => Carbon::parse(date("m/d/Y",strtotime($woo_order->date_created) + 8 * 3600))->toDateTimeString(),
                        ],
                    ]);
                }
                else {
                    $customer = new Party([
                        'name'          => ($order->customer_first_name) ? $order->customer_first_name : '',
                        'address'       => ($order->customer->address) ? $order->customer->address : '',
                        'email'         => ($order->customer->email) ? $order->customer->email : '',
                        'phone'         => ($order->customer->mobile_num) ? $order->customer->mobile_num : '',
                        'custom_fields' => [
                            'order number' => $order->ordersn,
                            'date' => date('m/d/Y', strtotime($order->created_at)),
                        ],
                    ]);
                }

                $order_items = array();
                $total = 0;
                foreach ($order->products as $item) {
                    $order_items[] = (new InvoiceItem())
                        ->title((($item->product->SellerSku) ? '['.$item->product->SellerSku.'] - ' : '').$item->product->name)
                        ->pricePerUnit($item->price)
                        ->qty($item->quantity)
                        ->subTotalPrice($item->price*$item->quantity);

                        $total += $item->price*$item->quantity;
                }
                $invoice[$order->ordersn] = Invoice::make()
                ->seller($client)
                ->buyer($customer)
                ->addItems($order_items)
                ->totalAmount($total);
            };
        }
        if ($order->site == 'woocommerce') {
            return PDF::loadview('vendor.invoices.templates.default', ['invoices' => $invoice])->stream();
        }
    }

    public function printPackingList(Request $request){
      $ids = explode(',',$request->get('ids'));
      $products = Order::whereIn('id', $ids)->get();
      $orders = ['shopee' => [], 'lazada' => [], 'woocommerce' => []];
      $counter = 0;
      foreach($products as $product){
        if($product->site == 'lazada'){
          $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
          // die(var_dump($product->id));
          $r = new LazopRequest('/order/items/get','GET');
          $r->addApiParam('order_id', $product->ordersn);
          $result = $c->execute($r,$product->shop->access_token);
          $item = json_decode($result, true);
          if($item['code'] == 0){
            $items = [];
            $items_sku = [];
            $orders['lazada'][$counter] = (array) $product->toArray();
           
            foreach ( $item['data'] as $item) {
              $sku = $item['sku'];
              if(!in_array($sku, $items_sku)) {
                  array_push($items_sku, $sku);
                  $items[$sku] = array(
                      'sku' => $sku,
                      'name' => $item['name'],
                      'qty' => 1,
                  );
              }
              else {
                  $items[$sku]['qty'] += 1;
              }
            }
            $orders['lazada'][$counter]['items'] = $items;
          }

        }else if($product->site == 'shopee'){
            $client = $product->shop->shopeeGetClient();
            $order_details = $client->order->getOrderDetails(['ordersn_list' => array_values([$product->ordersn])])->getData();
            if(isset($order_details['orders'][0])){
                $orders['shopee'][$counter] = $order_details['orders'][0];
                $orders['shopee'][$counter]['shop_name'] = $product->shop->name;
            }
        }else if($product->site == 'woocommerce') {
            $client = $product->shop->woocommerceGetClient();
            $order_details = $client->get('orders/'.$product->ordersn);
            $orders['woocommerce'][$counter] = $order_details;
            $orders['woocommerce'][$counter]->shop_name = $product->shop->name;
        }
         $counter += 1;
      }
      return view('order.modal.packing_list', compact('orders'));
    }



    public function headers(Request $request){
        $data = [];
        $shop_ids =  $request->user()->business->shops->pluck('id')->toArray();
        $daterange = explode('/', $request->get('daterange'));

        $max_orders = Order::getMaxOrders();
        if ($max_orders == 0) {
            $allshops_orders = Order::whereIn('shop_id', $shop_ids)->pluck('id')->toArray();    //get max orders_processing from all shops before filtering
        }
        else {
            $allshops_orders = Order::whereIn('shop_id', $shop_ids)->orderBy('created_at', 'desc')->limit($max_orders)->pluck('id')->toArray();     //get max orders_processing from all shops before filtering
        }
        
        if($request->site == 'lazada'){
            $lazada_statuses = Order::$statuses;
            foreach($lazada_statuses as $status){
                $orders = Order::whereIn('shop_id', $shop_ids)->whereIn('id', $allshops_orders)->where('site', 'lazada');
                if(count($daterange) == 2){
                    if($daterange[0] == $daterange[1]){
                        $orders = $orders->whereDate('created_at', [$daterange[0]]);
                    }else{
                        $orders = $orders->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
                        
                    }
                }
                $data[$status] = $orders->where('status', $status)->count();
            }
        }else if ($request->site == 'shopee') {
            $shopee_statuses = Order::$shopee_statuses;
            foreach($shopee_statuses as $status){
                $orders = Order::whereIn('shop_id', $shop_ids)->whereIn('id', $allshops_orders)->where('site', 'shopee');
                if(count($daterange) == 2){
                    if($daterange[0] == $daterange[1]){
                        $orders = $orders->whereDate('created_at', [$daterange[0]]);
                    }else{
                        $orders = $orders->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
                    }
                }
                $data[$status] = $orders->where('status', $status)->count();
            }
        }
        else if($request->site == 'woocommerce') {
            $woocommerce_statuses = Order::$woocommerce_statuses;
            foreach($woocommerce_statuses as $status){
                $orders = Order::whereIn('shop_id', $shop_ids)->whereIn('id', $allshops_orders)->where('site', 'woocommerce');
                if(count($daterange) == 2){
                    if($daterange[0] == $daterange[1]){
                        $orders = $orders->whereDate('created_at', [$daterange[0]]);
                    }else{
                        $orders = $orders->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
                        
                    }
                }
                $data[$status] = $orders->where('status', $status)->count();
            }
        }

        $data['lazada_total'] = Order::whereIn('shop_id', $shop_ids)->whereIn('id', $allshops_orders)->where('site', 'lazada')->whereIn('status', ['pending']);
        $data['shopee_total'] = Order::whereIn('shop_id', $shop_ids)->whereIn('id', $allshops_orders)->where('site', 'shopee')->whereIn('status', ['RETRY_SHIP', 'READY_TO_SHIP']);
        $data['woocommerce_total'] = Order::whereIn('shop_id', $shop_ids)->whereIn('id', $allshops_orders)->where('site', 'woocommerce')->whereIn('status', ['pending', 'processing']);
        if(count($daterange) == 2){
            if($daterange[0] == $daterange[1]){
                $data['lazada_total'] = $data['lazada_total']->whereDate('created_at', [$daterange[0]]);
                $data['shopee_total'] = $data['shopee_total']->whereDate('created_at', [$daterange[0]]);
                $data['woocommerce_total'] = $data['woocommerce_total']->whereDate('created_at', [$daterange[0]]);
            }else{
                $data['lazada_total'] = $data['lazada_total']->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
                $data['shopee_total'] = $data['shopee_total']->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
                $data['woocommerce_total'] = $data['woocommerce_total']->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
            }
        }
        $data['lazada_total'] = $data['lazada_total']->count();
        $data['shopee_total'] = $data['shopee_total']->count();
        $data['woocommerce_total'] = $data['woocommerce_total']->count();
        return response()->json(['data' => $data]); 
    }
}