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
    <h4 class="card-title">Your New Plan</h4>
  </div>
  <div class="card-content">
    Success Page....
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