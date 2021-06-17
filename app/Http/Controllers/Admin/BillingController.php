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
                            switch ($billing->paid_status) {
                                case 0:
                                    $status = '<span class="badge badge-pill badge-primary">Unpaid</span>';
                                    break;
                                case 1:
                                    $status = '<span class="badge badge-pill badge-success">Paid</span>';
                                    break;
                                case 2:
                                    $status = '<span class="badge badge-pill badge-danger">Failed</span>';
                                    break;
                                case 3:
                                    $status = '<span class="badge badge-pill badge-warning">Canceled</span>';
                                    break;
                                case 4:
                                    $status = '<span class="badge badge-pill badge-dark">Suspended</span>';
                                    break;
                                default:
                                    $status = '<span class="badge badge-pill badge-secondary">Unknown</span>';
                                    break;
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
            ->rawColumns(['paid_status'])
            ->make(true);
        }

        return view('admin.billing.overdue', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}