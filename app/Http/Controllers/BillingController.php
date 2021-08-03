<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Billing;
use Yajra\DataTables\Facades\DataTables;

class BillingController extends Controller
{
    public function index(Request $request){
        if (request()->ajax()) {
            $billing = Billing::where('business_id', $request->user()->business_id);
    
            return Datatables::eloquent($billing)
            ->editColumn('plan_id', function(Billing $billing) {
                            return $billing->plan->name;
                        })
            ->editColumn('paid_status', function(Billing $billing) {
                            switch ($billing->paid_status) {
                                case 0:
                                    return '<span class="badge badge-pill badge-primary">Unpaid</span>';
                                    break;
                                case 1:
                                    return '<span class="badge badge-pill badge-success">Paid</span>';
                                    break;
                                case 2:
                                    return '<span class="badge badge-pill badge-danger">Failed</span>';
                                    break;
                                case 3:
                                    return '<span class="badge badge-pill badge-warning">Canceled</span>';
                                    break;
                                case 4:
                                    return '<span class="badge badge-pill badge-dark">Suspended</span>';
                                    break;
                                default:
                                    return '<span class="badge badge-pill badge-secondary">Unknown</span>';
                                    break;
                            }
                        })
            ->addColumn('action', function (Billing $billing) {
                        return '<button type="button" class="btn btn-primary billing_view_details" data-billing_id="'.$billing->id.'" data-action="'.route('billing.viewInvoice').'" >View detail</button>';
                    })
            ->rawColumns(['paid_status', 'action'])
            ->make(true);
        }
        return view('billing.index');
    }

    public function viewInvoice(Request $request) {
        $billing = Billing::find($request->data);
        return view('billing.modal.viewdetails',[
            'billing' => $billing
        ]);
    }
}
