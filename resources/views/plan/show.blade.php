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
  function count_or_free($value, $strike = false) {
    if($value) {
      if ($strike) {
        $output = '<del><small>PHP '.number_format($value, 2).'</small></del>';
      }
      else {
        $output = '<h2 style="color:inherit"><small>PHP</small> '.number_format($value, 2).'</h2 style="color:inherit">';
      }
    }
    else {
      $output = '<h2 style="color:inherit">FREE</h2>';
    }
    return $output;
  }
  function count_or_unlimited($value) {
    return ($value)?number_format($value):'Unlimited';
  }
  function boolean_to_text($value) {
    return ($value)?'<span class="text-success"><i class="feather icon-check"></i></span>':'<span class="text-danger"><i class="feather icon-x"></i></span>';
  }
@endphp
<section class="card">
  <div class="card-header">
    <h4 class="card-title">Payment</h4>
  </div>
  <div class="card-content">
    <form action="{{ action('PayPalController@payment', $plan->id) }}" method="POST" class="form" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="billing" value="{{$billing}}">
      <input type="hidden" id="promocode" name="promocode" value="">
      <div class="card-body">
        <div class="row text-center">
          <div class="col-md-3">
            <div class="form-group">
              <h1>{{$plan->name}}</h1>
              <h1 class="display-1 text-primary"><i class="feather icon-{{$plan->icon}}"></i></h1>
                @if($billing == 'Month')
                  @if($plan->promo_start <= date("Y-m-d") && $plan->promo_end >= date("Y-m-d") && $plan->monthly_cost != $plan->promo_monthly_cost)
                    <span class="text-secondary">{!!count_or_free($plan->monthly_cost, true)!!}</span>
                    <br>
                    <span class="text-success">{!!count_or_free($plan->promo_monthly_cost)!!}</span>
                  @else
                    <span>{!!count_or_free($plan->monthly_cost)!!}</span>
                  @endif
                  <p><b>Billed <span class="billing_text">Monthly</span></b></p>
                @elseif($billing == 'Year')
                  @if($plan->promo_start <= date("Y-m-d") && $plan->promo_end >= date("Y-m-d") && $plan->yearly_cost != $plan->promo_yearly_cost)
                    <span class="text-secondary">{!!count_or_free($plan->yearly_cost, true)!!}</span>
                    <br>
                    <span class="text-success">{!!count_or_free($plan->promo_yearly_cost)!!}</span>
                  @else
                    <span>{!!count_or_free($plan->yearly_cost)!!}</span>
                  @endif
                  <p><b>Billed <span class="billing_text">Annually</span></b></p>
                @endif
            </div>
            @if($plan->id != 1)
            <div class="form-group">
              <label for="">Promocode</label>
              {{-- <input type="text" class="form-control" name="promocode" disabled=""> --}}
              <div class="input-group mb-3">
                <input type="text" id="promocode_input" class="form-control" placeholder="Promocode" aria-label="Promocode" aria-describedby="apply-button">
                <div class="input-group-append">
                  <button class="btn btn-primary apply_promocode" type="button">Apply</button>
                </div>
              </div>
              <p class="promocode_warning"></p>
            </div>
            @endif
          </div>
          <div class="col-md-3">
            <ul class="list-group">
              <li class="list-group-item">Order Processing : <b>{!!count_or_unlimited($plan->order_processing)!!}</b></li>
              <li class="list-group-item">Sales Channels : <b>{{$plan->sales_channels}}</b></li>
              <li class="list-group-item">No. of Users : <b>{!!count_or_unlimited($plan->users)!!}</b></li>
              <li class="list-group-item">Accounts/Marketplace : <b>{!!count_or_unlimited($plan->accounts_marketplace)!!}</b></li>
              <li class="list-group-item">Return reconciliation : <b>{!!boolean_to_text($plan->return_recon)!!}</b></li>
              <li class="list-group-item">Payment reconciliation : <b>{!!boolean_to_text($plan->payment_recon)!!}</b></li>
              <li class="list-group-item">Shipping overcharge reconciliation : <b>{!!boolean_to_text($plan->shipping_overcharge_recon)!!}</b></li>
            </ul>
          </div>
          <div class="col-md-3">
            <ul class="list-group">
              <li class="list-group-item"><b>INVENTORY</b></li>
              <li class="list-group-item">Inventory Management : <b>{!!boolean_to_text($plan->inventory_management)!!}</b></li>
              <li class="list-group-item">Sync inventory : <b>{!!boolean_to_text($plan->sync_inventory)!!}</b></li>
              <li class="list-group-item">No. of Warehouse : <b>{!!count_or_unlimited($plan->no_of_warehouse)!!}</b></li>
              <li class="list-group-item">Stock Transfer : <b>{!!boolean_to_text($plan->stock_transfer)!!}</b></li>
              <li class="list-group-item">Purchase Orders : <b>{!!boolean_to_text($plan->purchase_orders)!!}</b></li>
            </ul>
          </div>
          <div class="col-md-3">
            <ul class="list-group">
              <li class="list-group-item"><b>OFFLINE SALES</b></li>
              <li class="list-group-item">Add Sales : <b>{!!boolean_to_text($plan->add_sales)!!}</b></li>
              <li class="list-group-item">Customers Management : <b>{!!boolean_to_text($plan->customers_management)!!}</b></li>
            </ul>
            <ul class="list-group">
              <li class="list-group-item"><b>REPORTS</b></li>
              <li class="list-group-item">Stock Alert Monitoring : <b>{!!boolean_to_text($plan->stock_alert_monitoring)!!}</b></li>
              <li class="list-group-item">Out of Stock : <b>{!!boolean_to_text($plan->out_of_stock)!!}</b></li>
              <li class="list-group-item">Items Not Moving : <b>{!!boolean_to_text($plan->items_not_moving)!!}</b></li>
              <li class="list-group-item">Daily Sales : <b>{!!boolean_to_text($plan->daily_sales)!!}</b></li>
              <li class="list-group-item">Monthly Sales : <b>{!!boolean_to_text($plan->monthly_sales)!!}</b></li>
              <li class="list-group-item">Top Selling Products : <b>{!!boolean_to_text($plan->top_selling_products)!!}</b></li>
            </ul>
          </div>
        </div>
        <div class="form-group col-12"></div>
        <div class="row">
          <div class="col-6">
           <div class="col-12">
                @if($business->subscription() !== NULL && $business->subscription()->plan->id == $plan->id && $billing == $business->subscription()->billing_period)
                    <h3>You are currently subscribed to this plan.</h3>
                @elseif($business->subscription() !== NULL && $business->subscription()->plan->id == $plan->id && $billing != $business->subscription()->billing_period)
                    @if($business->subscription()->billing_period == "Year")
                      <h3>You are currently subscribed to this plan with Annual billing cycle.</h3>
                      <br>
                      <input type="submit" name="save" class="btn btn-primary mr-1 mb-1 btn_save" value="Change Billing Cycle to Monthly">
                    @elseif($business->subscription()->billing_period == "Month")
                      <h3>You are currently subscribed to this plan with Monthly billing cycle.</h3>
                      <br>
                      <input type="submit" name="save" class="btn btn-primary mr-1 mb-1 btn_save" value="Change Billing Cycle to Yearly">
                    @else
                    --
                    @endif
                @elseif($business->subscription() !== NULL &&  $business->subscription()->plan->monthly_cost > $plan->monthly_cost)
                    <h3>You are currently subscribed to a higher plan.</h3>
                @elseif($plan->id != 1)
                    <input type="submit" name="save" class="btn btn-primary mr-1 mb-1 btn_save" value="Subscribe">
                @endif
                </form>
               <!--  <button type="reset" class="btn btn-outline-warning mr-1 mb-1">Reset --> 
            </div>
          </div>
        </div>
      </div>
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
      html: 'while we process your paypal link',
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

  $('.apply_promocode').on('click', function() {
      $('.promocode_warning').html('').removeClass().addClass('promocode_warning');
      $('#promocode').val('');
      var promocode = $("#promocode_input").val();
      $.ajax({
        url :  "{{ route('promocode.checkPromocode') }}",
        type: "POST",
        data: {'code':promocode},
        success: function (result) {
          if(result.success == true){
            $('#promocode').val(result.promocode);
            $('.promocode_warning').html(result.msg).addClass('text-success');
          }
          else {
            $('.promocode_warning').html(result.msg).addClass('text-danger');
          }
        }
      });

  });
</script>
@endsection