@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Plan')

@section('vendor-style')
        {{-- vednor files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/extensions/dataTables.checkboxes.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection
@section('mystyle')
  {{-- Page css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/pages/data-list-view.css')) }}">
  <style>
    .title_column {
      border-right: 2px solid #ddd;
      width: 15%;
    }
  </style>
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
    return ($value)?'<h3 class="text-success"><i class="feather icon-check"></i></h3>':'<h3 class="text-danger"><i class="feather icon-x"></i></h3>';
  }

  $full_cols = count($plans) +1;
@endphp
<section id="data-list-view" class="data-list-view-header">
  <div class="row py-2">
    <div class="col-md-12 text-center">
      <div class="btn-group btn-group-toggle" data-toggle="buttons">
        <label class="btn btn-lg btn-outline-primary active">
          <input type="radio" name="billling_type" id="Monthly" data-period="Monthly" autocomplete="off" checked value="Month"> Monthly
        </label>
        <label class="btn btn-lg btn-outline-primary">
          <input type="radio" name="billling_type" id="Annually" data-period="Annually" autocomplete="off" value="Year"> Annually
        </label>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-content">
          <div class="card-body">
            <table class="table px-5">
              <thead>
                <tr>
                  <th class=" border-bottom-0 border-top-0 title_column text-justify">
                    <h1>Plans</h1>
                    <h3>for every</h3>
                    <h4>size, shape</h4>
                    <h5>and sound</h5>
                  </th>
                  @forelse($plans as $plan)
                    <th class=" border-bottom-0 border-top-0 text-center">
                      <h2>{{$plan->name}}</h2>
                      <h1 class="display-1 text-primary"><i class="feather icon-{{$plan->icon}}"></i></h1>
                    </th>
                  @empty
                    <th class=" border-bottom-0 border-top-0 ">No Plan</th>
                  @endforelse
                </tr>
                <tr>
                  <td class="title_column border-bottom-0 border-top-0"><span class="billing_text">Monthly</span> Cost</td>
                  @forelse($plans as $plan)
                    <th class="text-center border-0 align-bottom">
                      <div class="billling_type Monthly">
                        @if($plan->promo_start <= date("Y-m-d") && $plan->promo_end >= date("Y-m-d") && $plan->monthly_cost != $plan->promo_monthly_cost)
                          <span class="text-secondary">{!!count_or_free($plan->monthly_cost, true)!!}</span>
                          <br>
                          <span class="text-success">{!!count_or_free($plan->promo_monthly_cost)!!}</span>
                        @else
                          <span>{!!count_or_free($plan->monthly_cost)!!}</span>
                        @endif
                      </div>
                      <div class="billling_type Annually" style="display: none">
                        @if($plan->promo_start <= date("Y-m-d") && $plan->promo_end >= date("Y-m-d") && $plan->yearly_cost != $plan->promo_yearly_cost)
                          <span class="text-secondary">{!!count_or_free($plan->yearly_cost, true)!!}</span>
                          <br>
                          <span class="text-success">{!!count_or_free($plan->promo_yearly_cost)!!}</span>
                        @else
                          <span>{!!count_or_free($plan->yearly_cost)!!}</span>
                        @endif
                      </div>
                      <p><b>Billed <span class="billing_text">Monthly</span></b></p>
                      <a href="{{ action('Admin\SubscriptionController@edit', $plan->id) }}" data-href="{{ action('Admin\SubscriptionController@edit', $plan->id) }}" class="btn btn-primary my-2 Subscribe_Btn">Edit Plan</a>
                    </th>
                  @empty
                    <th class=" border-0">No Plan</th>
                  @endforelse
                </tr>
                <tr>
                  <td class="title_column">Order Processing</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{!!count_or_unlimited($plan->order_processing)!!}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <td class="title_column">Sales Channels</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{{$plan->sales_channels}}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <td class="title_column">No. of Users</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{!!count_or_unlimited($plan->users)!!}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <td class="title_column">Accounts/Marketplace</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{!!count_or_unlimited($plan->accounts_marketplace)!!}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <td class="title_column">Return reconciliation</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{!!boolean_to_text($plan->return_recon)!!}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <td class="title_column">Payment reconciliation</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{!!boolean_to_text($plan->payment_recon)!!}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <td class="title_column">Shipping overcharge reconciliation</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{!!boolean_to_text($plan->shipping_overcharge_recon)!!}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <tr>
                    <th colspan="{{$full_cols}}"><h3>INVENTORY</h3></th>
                  </tr>
                </tr>
                <tr>
                  <td class="title_column">Inventory Management</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{!!boolean_to_text($plan->inventory_management)!!}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <td class="title_column">Sync inventory</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{!!boolean_to_text($plan->sync_inventory)!!}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <td class="title_column">No. of Warehouse</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{!!count_or_unlimited($plan->no_of_warehouse)!!}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <td class="title_column">Stock Transfer</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{!!boolean_to_text($plan->stock_transfer)!!}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <td class="title_column">Purchase Orders</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{!!boolean_to_text($plan->purchase_orders)!!}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <tr>
                    <th colspan="{{$full_cols}}"><h3>OFFLINE SALES</h3></th>
                  </tr>
                </tr>
                <tr>
                  <td class="title_column">Add Sales</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{!!boolean_to_text($plan->add_sales)!!}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <td class="title_column">Customers Management</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{!!boolean_to_text($plan->customers_management)!!}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <tr>
                    <th colspan="{{$full_cols}}"><h3>REPORTS</h3></th>
                  </tr>
                </tr>
                <tr>
                  <td class="title_column">Stock Alert Monitoring</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{!!boolean_to_text($plan->stock_alert_monitoring)!!}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <td class="title_column">Out of Stock</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{!!boolean_to_text($plan->out_of_stock)!!}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <td class="title_column">Items Not Moving</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{!!boolean_to_text($plan->items_not_moving)!!}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <td class="title_column">Daily Sales</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{!!boolean_to_text($plan->daily_sales)!!}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <td class="title_column">Monthly Sales</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{!!boolean_to_text($plan->monthly_sales)!!}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <td class="title_column">Top Selling Products</td>
                  @forelse($plans as $plan)
                    <td class=" border-bottom-0 border-top-0 text-center">
                      <span>{!!boolean_to_text($plan->top_selling_products)!!}</span>
                    </td>
                  @empty
                    <td class="border-bottom-0 border-top-0 ">--</td>
                  @endforelse
                </tr>
                <tr>
                  <td class="title_column border-bottom-0 border-top-0"><span class="billing_text">Monthly</span> Cost</td>
                  @forelse($plans as $plan)
                    <th class="text-center border-0 align-bottom">
                      <div class="billling_type Monthly">
                        @if($plan->promo_start <= date("Y-m-d") && $plan->promo_end >= date("Y-m-d") && $plan->monthly_cost != $plan->promo_monthly_cost)
                          <span class="text-secondary">{!!count_or_free($plan->monthly_cost, true)!!}</span>
                          <br>
                          <span class="text-success">{!!count_or_free($plan->promo_monthly_cost)!!}</span>
                        @else
                          <span>{!!count_or_free($plan->monthly_cost)!!}</span>
                        @endif
                      </div>
                      <div class="billling_type Annually" style="display: none">
                        @if($plan->promo_start <= date("Y-m-d") && $plan->promo_end >= date("Y-m-d") && $plan->yearly_cost != $plan->promo_yearly_cost)
                          <span class="text-secondary">{!!count_or_free($plan->yearly_cost, true)!!}</span>
                          <br>
                          <span class="text-success">{!!count_or_free($plan->promo_yearly_cost)!!}</span>
                        @else
                          <span>{!!count_or_free($plan->yearly_cost)!!}</span>
                        @endif
                      </div>
                      <p><b>Billed <span class="billing_text">Monthly</span></b></p>
                      <a href="{{ action('Admin\SubscriptionController@edit', $plan->id) }}" data-href="{{ action('Admin\SubscriptionController@edit', $plan->id) }}" class="btn btn-primary my-2 Subscribe_Btn">Edit Plan</a>
                    </th>
                  @empty
                    <th class=" border-0">No Plan</th>
                  @endforelse
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
  
@endsection
@section('vendor-script')
{{-- vednor js files --}}
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>-
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script>
  <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection
@section('myscript')
<script type="text/javascript">
  $(document).ready(function() {

    $(document).on('change', 'input[name=billling_type]', function() {
      var period = $(this).data('period');
      var val = $(this).val();
      $('.billing_text').html(period);
      $('.billling_type').hide();
      $('.billling_type.'+period).show('fast');
      $('.Subscribe_Btn').attr('href', $('.Subscribe_Btn').data('href')+"/"+val);
      $('.Subscribe_Btn').each(function( index,element ){
        $(element).attr('href', $(element).data('href')+"/"+val);
      });
    });

  });
</script>
@endsection
