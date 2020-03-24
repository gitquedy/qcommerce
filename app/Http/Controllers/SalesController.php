<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\Sales;
use App\SaleItems;
use App\Customer;
use App\OrderRef;
use App\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SalesController@index'), 'name'=>"Sales"], ['name'=>"Sales List"]
        ];
        if ( request()->ajax()) {
           $sales = Sales::orderBy('updated_at', 'desc');
           // return $sales->get();
            return Datatables($sales)   
            ->addColumn('customer_name', function(Sales $sales) {
                if($sales->customer_id) {
                    return $sales->customer->formatName();
                }
                else {
                    return $sales->customer_last_name.', '.$sales->customer_first_name;
                }
            })   
            ->addColumn('balance', function(Sales $sales) {
                return number_format($sales->grand_total - $sales->paid, 2);
            })
            ->editColumn('grand_total', function(Sales $sales) {
                return number_format($sales->grand_total, 2);
            })
            ->editColumn('paid', function(Sales $sales) {
                return number_format($sales->paid, 2);
            })
            ->editColumn('status', function(Sales $sales) {
                switch ($sales->status) {
                    case 'completed':
                            return '<span class="badge badge-success">Complete</span>';
                        break;
                    case 'pending':
                            return '<span class="badge badge-warning">Pending</span>';
                        break;
                    case 'canceled':
                            return '<span class="badge badge-danger">Canceled</span>';
                        break;
                    
                    default:
                            return '<span class="badge badge-secondary">Unknown</span>';
                        break;
                }
            })
            ->addColumn('action', function(Sales $sales) {
                    $actions = '<div class="btn-group dropup mr-1 mb-1">
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                    Action<span class="sr-only">Toggle Dropdown</span></button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="'. action('SalesController@edit', $sales->id) .'"><i class="fa fa-edit aria-hidden="true""></i> Edit</a>
                        <a class="dropdown-item modal_button " href="#" data-href="'. action('SalesController@delete', $sales->id).'" ><i class="fa fa-trash aria-hidden="true""></i> Delete</a>
                    </div></div>';
                    return $actions;
             })
            ->rawColumns(['action','status'])
            ->make(true);
        }
        return view('sales.index', [
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
            ['link'=>"/",'name'=>"Home"],['link'=> action('SalesController@index'), 'name'=>"Sales List"], ['name'=>"Add Sales"]
        ];
        $customers = Customer::where('business_id', Auth::user()->business_id)->get();
        return view('sales.create', compact('breadcrumbs','customers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'date' => 'required|date',
            'reference_no' => 'nullable|string|max:255',
            'customer_id' => 'required',
            'status' => 'required',
            'note' => 'nullable|string|max:255',
            'sales_item_array' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $customer = Customer::where('business_id', $user->business_id)->where('id', $request->customer_id)->first();
            $genref = Settings::where('business_id', Auth::user()->business_id)->first();
            $total = 0;
            $sales = new Sales;
            $sales->business_id = $user->business_id;
            $sales->customer_id = $request->customer_id;
            $sales->customer_first_name = $customer->first_name;
            $sales->customer_last_name = $customer->last_name;
            $sales->date = date("Y-m-d H:i:s", strtotime($request->date));
            $sales->reference_no = ($request->reference_no)?$request->reference_no:$genref->getReference_so();
            $sales->note = $request->note;
            $sales->status = $request->status;
            $sales->discount = ($request->discount)?$request->discount:0;
            $sales->paid = $request->paid;
            $sales->created_by = $user->id;
            foreach ($request->sales_item_array as $item) {
                $sub_total = $item['price'] * $item['quantity'];
                $total+= $sub_total;
            }

            if($total == $request->paid) {
                $payment_status = "paid";
            }
            else if($request->paid == 0) {
                $payment_status = "pending";
            }
            else if($request->paid > 0 && $request->paid < $total) {
                $payment_status = "partial";
            }

            $sales->payment_status = $payment_status;
            $sales->total = $total;
            $sales->grand_total = $total - $request->discount;
            $sales->save();
            $sales_items = [];
            foreach ($request->sales_item_array as $id => $item) {
                $sales_item = [];
                $sales_item['sales_id'] = $sales->id;
                $sales_item['sku_id'] = $id;
                $sales_item['sku_code'] = $item['code'];
                $sales_item['sku_name'] = $item['name'];
                $sales_item['image'] = $item['image'];
                $sales_item['unit_price'] = $item['price'];
                $sales_item['quantity'] = $item['quantity'];
                $sales_item['discount'] = 0;
                $sales_item['subtotal'] = $item['price'] * $item['quantity'];
                $sales_item['real_unit_price'] = $item['real_unit_price'];
                $sales_items[] = $sales_item;

                if($request->status == 'completed' && FALSE) {
                    $sku = Sku::where('business_id','=', $user->business_id)->where('id','=', $id)->first();
                    $all_shops = Shop::where('business_id', $user->business_id)->orderBy('updated_at', 'desc')->get();
                    $Shop_array = array();
                    foreach($all_shops as $all_shopsVAL){
                        $Shop_array[] = $all_shopsVAL->id;
                    }
                    $Sku_prod = Products::with('shop')->whereIn('shop_id', $Shop_array)->where('seller_sku_id','=',$id)->orderBy('updated_at', 'desc')->get();
                    if($sku){
                        $sku->quantity -= $item['quantity'];
                        $result = $sku->save();
                        foreach ($Sku_prod as $prod) {
                            $shop_id = $prod->shop_id;
                            $access_token = Shop::find($shop_id)->access_token;

                            $prod = Products::where('id', $prod->id)->first();
                            $prod->quantity = $sku->quantity;
                            $prod->save();
                                $xml = '<?xml version="1.0" encoding="UTF-8" ?>
                                <Request>
                                    <Product>
                                        <Skus>
                                            <Sku>
                                                <SellerSku>'.$prod->SellerSku.'</SellerSku>
                                                <quantity>'.$sku->quantity.'</quantity>
                                            </Sku>
                                        </Skus>
                                    </Product>
                                </Request>';
                            if(env('lazada_sku_sync', true)){
                                if($prod->site == 'lazada'){
                                    $response = Products::product_update($access_token,$xml);
                                }
                            }
                        }
                    }
                }
            }
            $sales_items_query = SaleItems::insert($sales_items);
            if (!$request->reference_no) {
                $increment = OrderRef::where('settings_id', $genref->id)->update(['so' => DB::raw('so + 1')]);
            }
            $output = ['success' => 1,
                'msg' => 'Sale added successfully!',
                'redirect' => action('SalesController@index')
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $sales = Sales::findOrFail($id);
        $customers = Customer::where('business_id', Auth::user()->business_id)->get();
        if($sales->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this sales');
        }
        // print json_encode($sales->items);die();
        return view('sales.edit', compact('sales', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $sales = Sales::findOrFail($id);
        $old_status = $sales->status;
        if($sales->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this customer');
        }

        $validator = Validator::make($request->all(),[
            'date' => 'required|date',
            'reference_no' => 'nullable|string|max:255',
            'customer_id' => 'required',
            'status' => 'required',
            'note' => 'nullable|string|max:255',
            'sales_item_array' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $customer = Customer::where('business_id', $user->business_id)->where('id', $request->customer_id)->first();
            $genref = Settings::where('business_id', Auth::user()->business_id)->first();
            $total = 0;
            $sales->business_id = $user->business_id;
            $sales->customer_id = $request->customer_id;
            $sales->customer_first_name = $customer->first_name;
            $sales->customer_last_name = $customer->last_name;
            $sales->date = date("Y-m-d H:i:s", strtotime($request->date));
            if ($request->reference_no) {
                $sales->reference_no = $request->reference_no;
            }
            $sales->note = $request->note;
            $sales->status = $request->status;
            $sales->discount = ($request->discount)?$request->discount:0;
            $sales->paid = $request->paid;
            $sales->updated_by = $user->id;

            //future :: return all stocks then delete
                //return stocks code here
            SaleItems::where('sales_id', $sales->id)->delete();
            foreach ($request->sales_item_array as $item) {
                $sub_total = $item['price'] * $item['quantity'];
                $total+= $sub_total;
            }

            if($total == $request->paid) {
                $payment_status = "paid";
            }
            else if($request->paid == 0) {
                $payment_status = "pending";
            }
            else if($request->paid > 0 && $request->paid < $total) {
                $payment_status = "partial";
            }

            $sales->payment_status = $payment_status;
            $sales->total = $total;
            $sales->grand_total = $total - $request->discount;
            $sales->save();
            $sales_items = [];
            foreach ($request->sales_item_array as $id => $item) {
                $sales_item = [];
                $sales_item['sales_id'] = $sales->id;
                $sales_item['sku_id'] = $id;
                $sales_item['sku_code'] = $item['code'];
                $sales_item['sku_name'] = $item['name'];
                $sales_item['image'] = $item['image'];
                $sales_item['unit_price'] = $item['price'];
                $sales_item['quantity'] = $item['quantity'];
                $sales_item['discount'] = 0;
                $sales_item['subtotal'] = $item['price'] * $item['quantity'];
                $sales_item['real_unit_price'] = $item['real_unit_price'];
                $sales_items[] = $sales_item;

                if($old_status != 'completed' && $request->status == 'completed' && FALSE) {
                    $sku = Sku::where('business_id','=', $user->business_id)->where('id','=', $id)->first();
                    $all_shops = Shop::where('business_id', $user->business_id)->orderBy('updated_at', 'desc')->get();
                    $Shop_array = array();
                    foreach($all_shops as $all_shopsVAL){
                        $Shop_array[] = $all_shopsVAL->id;
                    }
                    $Sku_prod = Products::with('shop')->whereIn('shop_id', $Shop_array)->where('seller_sku_id','=',$id)->orderBy('updated_at', 'desc')->get();
                    if($sku){
                        $sku->quantity -= $item['quantity'];
                        $result = $sku->save();
                        foreach ($Sku_prod as $prod) {
                            $shop_id = $prod->shop_id;
                            $access_token = Shop::find($shop_id)->access_token;

                            $prod = Products::where('id', $prod->id)->first();
                            $prod->quantity = $sku->quantity;
                            $prod->save();
                                $xml = '<?xml version="1.0" encoding="UTF-8" ?>
                                <Request>
                                    <Product>
                                        <Skus>
                                            <Sku>
                                                <SellerSku>'.$prod->SellerSku.'</SellerSku>
                                                <quantity>'.$sku->quantity.'</quantity>
                                            </Sku>
                                        </Skus>
                                    </Product>
                                </Request>';
                            if(env('lazada_sku_sync', true)){
                                if($prod->site == 'lazada'){
                                    $response = Products::product_update($access_token,$xml);
                                }
                            }
                        }
                    }
                }
            }
            SaleItems::insert($sales_items);
            $output = ['success' => 1,
                'msg' => 'Sale updated successfully!',
                'redirect' => action('SalesController@index')
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sales = Sales::findOrFail($id);
        if($sales->business_id != Auth::user()->business_id){
            abort(401, 'You don\'t have access to edit this sale');
        }
        try {
            DB::beginTransaction();
            $sales->delete();
            DB::commit();
            $output = ['success' => 1,
                        'msg' => 'Sale successfully deleted!'
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



    public function delete(Sales $sales, Request $request){
      if($sales->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this sales');
      }
        $action = action('SalesController@destroy', $sales->id);
        $title = 'Sale ' . $sales->reference_no;
        return view('layouts.delete', compact('action' , 'title'));
    }
}
