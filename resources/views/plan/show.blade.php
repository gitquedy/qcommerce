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

<section class="card">
  <div class="card-header">
    <h4 class="card-title">Payment</h4>
  </div>
  <div class="card-content">
    <form action="{{ action('PayPalController@payment', $plan->id) }}" method="POST" class="form" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="billing" value="{{$billing}}">
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>Plan Name:</label>
              <input type="text" name="" class="form-control" disabled="disabled" value="{{ $plan->name }}">
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label>Price:</label>
              @php 
              if($billing == 'Monthly') {
                if($plan->promo_start <= date("Y-m-d") && $plan->promo_end >= date("Y-m-d") && $plan->monthly_cost != $plan->promo_monthly_cost) {
                  $total = $plan->promo_monthly_cost;
                }
                else {
                  $total = $plan->monthly_cost;
                }
              }
              elseif($billing == 'Annually') {
                if ($plan->promo_start <= date("Y-m-d") && $plan->promo_end >= date("Y-m-d") && $plan->yearly_cost != $plan->promo_yearly_cost) {
                  $total = $plan->promo_yearly_cost;
                }
                else {
                  $total = $plan->yearly_cost;
                }
              }
              @endphp
              <input type="text" name="" class="form-control" disabled="disabled" value="PHP {{ $total }}">
            </div>
          </div>
        </div>
        <div class="form-group col-12"></div>
        <div class="row">
          <div class="col-6">
           <div class="col-12">
                <input type="submit" name="save" class="btn btn-primary mr-1 mb-1 btn_save" value="Proceed to Paypal Checkout">
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
</script>
@endsection