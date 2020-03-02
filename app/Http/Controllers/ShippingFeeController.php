<?php

namespace App\Http\Controllers;

use App\Order;
use App\ShippingFee;
use App\Utilities;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Shopee;

class ShippingFeeController extends Controller
{
    public function index(Request $request)
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"], ['name'=>"Shipping Fee Reconciliation"]
        ];
		 if (request()->ajax()) {
                $order = Order::where('shipping_fee_reconciled','!=',0)
                                ->with('seller_payout_fees')
                                ->with('customer_payout_fees')
                                ->orderBy('order.updated_at', 'desc');
	            return Datatables::eloquent($order)
                ->addColumn('overcharge', function (Order $order) {
                            if($order->seller_payout_fees->amount > $order->customer_payout_fees->amount) {
                                return $order->seller_payout_fees->amount - $order->customer_payout_fees->amount;
                            }
                            else {
                                return $order->customer_payout_fees->amount - $order->seller_payout_fees->amount;
                            }
                })
                ->addColumn('action', function(Order $order) {
                               return 'Test Action';
                            })
	            ->make(true);
	        }

        return view('shipping.index', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

}
