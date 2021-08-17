<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Billing;
use App\Bank;
use App\ProofOfPayment;
use Yajra\DataTables\Facades\DataTables;
use Validator;

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
                                    return '<span class="badge badge-pill badge-dark">Canceled</span>';
                                    break;
                                case 4:
                                    return '<span class="badge badge-pill badge-dark">Suspended</span>';
                                    break;
                                case 5:
                                    return '<span class="badge badge-pill badge-warning">Pending</span>';
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

    public function selectBank($billing_id) {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('BillingController@index'), 'name'=>"Billing"], ['name'=>"Bank List"]
        ];

        if (request()->ajax()) {
            $bank = Bank::all();

            return DataTables::of($bank)
            ->addColumn('action', function(Bank $b) use ($billing_id) {
                return '<a class="btn btn-primary no-print" href="'.route('billing.proofOfPayment', ['billing_id' => $billing_id, 'bank_id' => $b->id]).'"> Pay to this Bank</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        $billing = Billing::find($billing_id);

        return view('billing.selectbank', [
            'breadcrumbs' => $breadcrumbs,
            'billing' => $billing
        ]);
    }

    public function proofOfPayment($billing_id, $bank_id) {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('BillingController@index'), 'name'=>"Billing"], ['name'=>"Proof of Payment"]
        ];

        $billing = Billing::find($billing_id);
        $bank = Bank::find($bank_id);

        return view('billing.proofofpayment', [
            'breadcrumbs' => $breadcrumbs,
            'billing' => $billing,
            'bank' => $bank
        ]);
    }

    public function storeProof(Request $request) {
        $request->validate([
            'date' => 'required|date',
            'transaction_reference_no' => 'required|string|max:255',
            'bank_name' => 'nullable|string',
            'account_name' => 'nullable|string',
            'account_no' => 'nullable|numeric',
            'receipt' => 'image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        $proof = new ProofOfPayment;
        $proof->billing_id = $request->billing_id;
        $proof->bank_id = $request->bank_id;
        $proof->date = date("Y-m-d H:i:s", strtotime($request->date));
        $proof->transaction_reference_no = $request->transaction_reference_no;
        $proof->bank_name = $request->bank_name;
        $proof->account_name = $request->account_name;
        $proof->account_no = $request->account_no;

        $billing = Billing::find($request->billing_id);
        
        if ($request->hasFile('receipt')) {
            $receiptName = 'invoice_'.$billing->invoice_no.'.'.request()->receipt->getClientOriginalExtension();
            $request->receipt->move(public_path('images/profile/proof-of-payment'), $receiptName);
            $proof->receipt = $receiptName;
        }
        
        if($proof->save()) {
            $billing->paid_status = 5;
            $billing->save();
            $request->session()->flash('flash_success', 'Proof of Payment sent successfully. Wait for admin\'s confirmation of your payment');
        }
        else {
            $request->session()->flash('flash_error',"Something went wrong !");
        }

        return redirect()->action('BillingController@index');
    }

    public function viewProofOfPayment($billing_id) {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('BillingController@index'), 'name'=>"Billing"], ['name'=>"Proof of Payment"]
        ];

        $billing = Billing::find($billing_id);
        $proof = $billing->proof;
        $bank = $proof->bank;

        return view('billing.viewproofofpayment', [
            'breadcrumbs' => $breadcrumbs,
            'billing' => $billing,
            'bank' => $bank,
            'proof' => $proof
        ]);
    }
}
