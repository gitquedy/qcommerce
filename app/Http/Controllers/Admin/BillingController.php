<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Billing;
use App\Business;
use Validator;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class BillingController extends Controller {

    public function index() {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('Admin\BillingController@index'), 'name'=>"Billing"], ['name'=>"Invoice List"]
        ];

        if (request()->ajax()) {
            $billing = Billing::select('*');

            return Datatables::eloquent($billing)
            ->editColumn('business_id', function(Billing $billing) {
                            return Business::find($billing->business_id)->name;
                        })
            ->editColumn('plan_id', function(Billing $billing) {
                            return $billing->plan->name;
                        })
            ->editColumn('paid_status', function(Billing $billing) {
                            if ($billing->paid_status == 0) {
                                $status = 'unpaid';
                            }
                            else if ($billing->paid_status == 1) {
                                $status = 'paid';
                            }
                            else if ($billing->paid_status == 2) {
                                $status = 'failed';
                            }
                            else if ($billing->paid_status == 3) {
                                $status = 'cancelled';
                            }
                            else if ($billing->paid_status == 4) {
                                $status = 'suspended';
                            }
                            return '<p>'.$status.'</p><input type="number" class="form-control" data-defval="'.$billing->paid_status.'" data-name="paid_status" value="'.$billing->paid_status.'" data-billing_id="'.$billing->id.'" style="display:none;">';
                        })
            ->rawColumns(['paid_status'])
            ->make(true);
        }

        return view('admin.billing.index', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function quickUpdate(Request $request){
        $request->validate([
            'status' => 'required|numeric|min:0|max:4'
        ]);
        $column = $request->name;
        $billing = Billing::find($request->billing_id);
        $billing->$column = $request->status;
        $result = $billing->save();
        echo json_encode($result);
    }

    public function overdue() {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('Admin\BillingController@overdue'), 'name'=>"Billing"], ['name'=>"Overdue Invoices"]
        ];

        if (request()->ajax()) {
            $billing = Billing::where('paid_status', 0)->where('next_payment_date', '<', Carbon::now()->toDateString());

            return Datatables::eloquent($billing)
            ->editColumn('business_id', function(Billing $billing) {
                            return Business::find($billing->business_id)->name;
                        })
            ->editColumn('plan_id', function(Billing $billing) {
                            return $billing->plan->name;
                        })
            ->editColumn('paid_status', function(Billing $billing) {
                            if ($billing->paid_status == 0) {
                                $status = 'unpaid';
                            }
                            else if ($billing->paid_status == 1) {
                                $status = 'paid';
                            }
                            else if ($billing->paid_status == 2) {
                                $status = 'failed';
                            }
                            else if ($billing->paid_status == 3) {
                                $status = 'cancelled';
                            }
                            else if ($billing->paid_status == 4) {
                                $status = 'suspended';
                            }
                            return $status;
                        })
            ->make(true);
        }

        return view('admin.billing.overdue', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}