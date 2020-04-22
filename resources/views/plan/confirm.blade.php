@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Payment')

@section('vendor-style')
        {{-- vednor files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection
@section('mystyle')
    <link rel="stylesheet" href="{{ asset('css/pages/app-list.css') }}">
@endsection

@section('content')
@php
function count_or_unlimited($value) {
  return ($value)?number_format($value):'Unlimited';
}
function boolean_to_text($value) {
  return ($value)?'<span class="text-success"><i class="feather icon-check"></i></span>':'<span class="text-danger"><i class="feather icon-x"></i></span>';
}
@endphp
<section class="card">
  <div class="card-header">
    <h4 class="card-title">Order Details</h4>
  </div>
  <div class="card-content">
    <form action="{{ action('PayPalController@confirm', $billing->id) }}" method="POST" class="form" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="token" value="{{$response['TOKEN']}}">
      <input type="hidden" name="payer_id" value="{{$response['PAYERID']}}">
      <input type="hidden" name="desc" value="{{$response['DESC']}}">
      <div class="card-body">
        <div class="row text-center">
          <div class="col-md-6">
            <div class="form-group">
              <h1 class="display-1 text-primary"><i class="feather icon-{{$billing->plan->icon}}"></i></h1>
              <h1>{{$billing->plan->name}} Plan</h1>
              <br>
              <h3>{{$response['DESC']}}</h3>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Billing Cycle:</label>
              @if($billing->billing_period == 'Month')
                <h3>Billed <span class="billing_text">Monthly</span></h3>
              @elseif($billing->billing_period == 'Year')
                <h3>Billed <span class="billing_text">Annually</span></h3>
              @endif
            </div>
            <div class="form-group">
              <label>Billing Amount:</label>
              <h3>PHP {{ number_format($billing->amount, 2) }}</h3>
            </div>
            <div class="form-group">
              <label>Billing Account:</label>
              <h3>{{ucfirst($response['LASTNAME'])}}, {{ucfirst($response['FIRSTNAME'])}}</h3>
            </div>
            <div class="form-group">
              <label>Email:</label>
              <h3>{{$response['EMAIL']}}</h3>
            </div>
          </div>
        </div>
        <hr>
        <div class="row hidden">
          <div class="col-md-3">
            <ul class="list-group">
              <li class="list-group-item">Order Processing : <b>{!!count_or_unlimited($billing->plan->order_processing)!!}</b></li>
              <li class="list-group-item">Sales Channels : <b>{{$billing->plan->sales_channels}}</b></li>
              <li class="list-group-item">No. of Users : <b>{!!count_or_unlimited($billing->plan->users)!!}</b></li>
              <li class="list-group-item">Accounts/Marketplace : <b>{!!count_or_unlimited($billing->plan->accounts_marketplace)!!}</b></li>
              <li class="list-group-item">Return reconciliation : <b>{!!boolean_to_text($billing->plan->return_recon)!!}</b></li>
              <li class="list-group-item">Payment reconciliation : <b>{!!boolean_to_text($billing->plan->payment_recon)!!}</b></li>
              <li class="list-group-item">Shipping overcharge reconciliation : <b>{!!boolean_to_text($billing->plan->shipping_overcharge_recon)!!}</b></li>
            </ul>
          </div>
          <div class="col-md-3">
            <ul class="list-group">
              <li class="list-group-item"><b>INVENTORY</b></li>
              <li class="list-group-item">Inventory Management : <b>{!!boolean_to_text($billing->plan->inventory_management)!!}</b></li>
              <li class="list-group-item">Sync inventory : <b>{!!boolean_to_text($billing->plan->sync_inventory)!!}</b></li>
              <li class="list-group-item">No. of Warehouse : <b>{!!count_or_unlimited($billing->plan->no_of_warehouse)!!}</b></li>
              <li class="list-group-item">Stock Transfer : <b>{!!boolean_to_text($billing->plan->stock_transfer)!!}</b></li>
              <li class="list-group-item">Purchase Orders : <b>{!!boolean_to_text($billing->plan->purchase_orders)!!}</b></li>
            </ul>
          </div>
          <div class="col-md-3">
            <ul class="list-group">
              <li class="list-group-item"><b>OFFLINE SALES</b></li>
              <li class="list-group-item">Add Sales : <b>{!!boolean_to_text($billing->plan->add_sales)!!}</b></li>
              <li class="list-group-item">Customers Management : <b>{!!boolean_to_text($billing->plan->customers_management)!!}</b></li>
            </ul>
          </div>
          <div class="col-md-3">
            <ul class="list-group">
              <li class="list-group-item"><b>REPORTS</b></li>
              <li class="list-group-item">Stock Alert Monitoring : <b>{!!boolean_to_text($billing->plan->stock_alert_monitoring)!!}</b></li>
              <li class="list-group-item">Out of Stock : <b>{!!boolean_to_text($billing->plan->out_of_stock)!!}</b></li>
              <li class="list-group-item">Daily Sales : <b>{!!boolean_to_text($billing->plan->daily_sales)!!}</b></li>
              <li class="list-group-item">Top Selling Products : <b>{!!boolean_to_text($billing->plan->top_selling_products)!!}</b></li>
            </ul>
          </div>
        </div>
        <div class="form-group col-12"></div>
        <div class="row">
          <div class="col-6">
           <div class="col-12">
                <input type="submit" name="save" class="btn btn-primary mr-1 mb-1 btn_save" value="Confirm">
               <!--  <button type="reset" class="btn btn-outline-warning mr-1 mb-1">Reset --> 
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
@endsection

@section('vendor-script')
{{-- vednor js files --}}
<script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
<script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
<script type="text/javascript">
  $('.btn_save').click(function(){
   let timerInterval
    Swal.fire({
      title: 'Please Wait',
      html: '',
      timer: 5000,
      timerProgressBar: true,
      showCancelButton: false,
      showConfirmButton: false,
      onClose: () => {
        clearInterval(timerInterval)
      }
    }).then((result) => {
      /* Read more about handling dismissals below */
      if (result.dismiss === Swal.DismissReason.timer) {
        console.log('I was closed by the timer')
      }
    })
  });
</script>
@endsection