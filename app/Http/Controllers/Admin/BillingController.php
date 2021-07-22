<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Billing;
use App\Business;
use App\Bank;
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

    public function details() {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('Admin\BillingController@details'), 'name'=>"Billing"], ['name'=>"Bank Details"]
        ];

        if (request()->ajax()) {
            $bank = Bank::all();

            return DataTables::of($bank)
            ->addColumn('action', function(Bank $b) {
                return '<div class="btn-group dropup mr-1 mb-1">
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                Action<span class="sr-only">Toggle Dropdown</span></button>
                <div class="dropdown-menu">
                <a class="dropdown-item fa fa-edit" href="'.route('billing.details.edit',['id'=>$b->id]).'" > Edit</a>
                <a class="dropdown-item fa fa-trash confirm" href="#"  data-text="Are you sure to delete '. $b->account_name .' ?" data-text="This Action is irreversible." data-href="'.route('billing.details.delete',['id'=>$b->id]).'" > Delete</a>
                </div>
                </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        return view('admin.billing.details', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function create(Request $request) {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('Admin\BillingController@details'), 'name'=>"Bank Details"], ['name'=>"Add Bank"]
        ];

        return view('admin.billing.details.create', [
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    public function add(Request $request) {
        $request->validate([
            'bank' => 'required',
            'account_name' => 'required',
            'account_number' => 'required|numeric'
        ]);

        $bank = new Bank;
        $bank->bank = $request->bank;
        $bank->account_name = $request->account_name;
        $bank->account_number = $request->account_number;

        if ($bank->save()){
            $request->session()->flash('flash_success', 'Successfully added bank.');
        } else {
            $request->session()->flash('flash_error', 'Something went wrong!');
        }

        return redirect('admin/billing/details');
    }

    public function edit($id="", Request $request) {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('Admin\BillingController@details'), 'name'=>"Bank Details"], ['name'=>"Edit Bank"]
        ];

        $bank = Bank::find($id);

        return view('admin.billing.details.edit', [
            'breadcrumbs' => $breadcrumbs,
            'bank' => $bank
        ]);
    }

    public function update(Request $request) {
        $request->validate([
            'bank' => 'required',
            'account_name' => 'required',
            'account_number' => 'required|numeric'
        ]);

        $bank = Bank::find($request->id);
        $bank->bank = $request->bank;
        $bank->account_name = $request->account_name;
        $bank->account_number = $request->account_number;

        if ($bank->save()){
            $request->session()->flash('flash_success', 'Successfully updated bank.');
        } else {
            $request->session()->flash('flash_error', 'Something went wrong!');
        }

        return redirect('admin/billing/details');
    }

    public function delete($id, Request $request) {
        $bank = Bank::find($id);
        
        if ($bank->delete()) {
            $output = ['success' => 1, 'msg' => 'Success'];
        } else {
            $output = ['success' => 0, 'msg' => "Error!"];  
        }
        return response()->json($output);
    }
}