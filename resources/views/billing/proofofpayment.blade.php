@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Proof of Payment')

@section('vendor-style')
    {{-- vendor files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

@section('mystyle')
    {{-- Page css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/pages/data-list-view.css')) }}">
@endsection

@section('content')
<section class="row match-height">
    <div class="col-xl-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Invoice #{{ $billing->invoice_no }}</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="mr-1">Invoice Date: {{ isset($billing->payment_date)
                                                    ?
                                                        Carbon\Carbon::parse($billing->payment_date)->subDays(1)->toFormattedDateString()
                                                        :
                                                        (isset($previous_billing->next_payment_date)
                                                            ?
                                                                Carbon\Carbon::parse($previous_billing->next_payment_date)->subDays(1)->toFormattedDateString()
                                                                :
                                                                null) }}</div>
                        <div class="ml-1">Invoice Due Date: {{ isset($billing->payment_date)
                                                        ?
                                                            Carbon\Carbon::parse($billing->payment_date)->toFormattedDateString()
                                                            :
                                                            (isset($previous_billing->next_payment_date)
                                                                ?
                                                                    Carbon\Carbon::parse($previous_billing->next_payment_date)->toFormattedDateString()
                                                                    :
                                                                    null) }}</div>
                    </div>
                    <br>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{$billing->plan->name}} Subscription Plan ({{ isset($billing->payment_date)
                                                                                ?
                                                                                    Carbon\Carbon::parse($billing->payment_date)->toFormattedDateString()
                                                                                    :
                                                                                    (isset($previous_billing->next_payment_date)
                                                                                        ?
                                                                                            Carbon\Carbon::parse($previous_billing->next_payment_date)->toFormattedDateString()
                                                                                            :
                                                                                            null) }} - 
                                                                            {{ isset($billing->next_payment_date)
                                                                                ?
                                                                                    Carbon\Carbon::parse($billing->next_payment_date)->subDays(1)->toFormattedDateString()
                                                                                    :
                                                                                    (isset($previous_billing->next_payment_date)
                                                                                        ?
                                                                                            Carbon\Carbon::parse($previous_billing->next_payment_date)->addMonth(1)->toFormattedDateString()
                                                                                            :
                                                                                            null) }})</td>
                                <td>Php{{$billing->amount}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-content">
                <div class="card-header">
                    <h4 class="card-title">{{ $bank->bank }}</h4>
                </div>
                <div class="card-body">
                    <div>Account Name: {{ $bank->account_name }}</div>
                    <div>Account No.: {{ $bank->account_number }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-9 col-md-6 col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Proof of Payment Details</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('billing.storeProof') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="billing_id" name="billing_id" value="{{$billing->id}}">
                        <input type="hidden" id="bank_id" name="bank_id" value="{{$bank->id}}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date of Payment</label>
                                    <div class="position-relative has-icon-left">
                                        <input type="text" class="form-control datepicker" name="date" value="{{date('m/d/Y')}}">
                                        <div class="form-control-position"> 
                                            <i class="feather icon-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Transaction No. / Reference No.</label>
                                    <div class="position-relative has-icon-left">
                                        <input type="text" class="form-control" name="transaction_reference_no" placeholder="Transaction No. / Reference No.">
                                        <div class="form-control-position"> 
                                            <i class="feather icon-hash"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Bank Name</label>
                                    <div class="position-relative has-icon-left">
                                        <input type="text" class="form-control" name="bank_name" placeholder="Bank Name">
                                        <div class="form-control-position"> 
                                            <i class="feather icon-file-text"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Account Name</label>
                                    <div class="position-relative has-icon-left">
                                        <input type="text" class="form-control" name="account_name" placeholder="Account Name">
                                        <div class="form-control-position"> 
                                            <i class="feather icon-user"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Account No.</label>
                                    <div class="position-relative has-icon-left">
                                        <input type="number" class="form-control" name="account_no" placeholder="Account No.">
                                        <div class="form-control-position"> 
                                            <i class="feather icon-credit-card"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Receipt</label>
                                    <input  id="receiptUpload" class="form-control" type="file" name="receipt"/>
                                </div>
                            </div>
                            <div class="col-md-12 text-right">
                                <br>
                                <button class="btn btn-primary">Send</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</section>
@endsection

@section('vendor-script')
    {{-- vednor js files --}}
    <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection
@section('myscript')
    <script>
        //
    </script>
@endsection