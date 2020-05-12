@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Promocode')
@section('mystyle')
  {{-- Page css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/pages/data-list-view.css')) }}">
  <style>
  input[type="date"]::-webkit-inner-spin-button,
  input[type="date"]::-webkit-calendar-picker-indicator {
      display: none;
      -webkit-appearance: none;
  }

   .form-control[readonly] {
       background-color: transparent;
    }
  </style>
@endsection
@section('content')
<section id="floating-label-layouts">
  <div class="row match-height">
      <div class="col-md-12 col-12">
          <div class="card">
              <div class="card-content">
                  <div class="card-body">
                      <form action="{{ action('PromocodeController@update', $promocode->id) }}" method="POST" class="form" enctype="multipart/form-data">
                        @method('put')
                        @csrf
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <lable>Code</lable>
                                <div class="input-group">
                                  <input type="text" id="promocode_input" name="code" class="form-control text-uppercase" placeholder="Promocode" aria-label="Promocode" aria-describedby="basic-addon2" value="{{$promocode->code}}">
                                  <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" id="generate_code_btn" type="button">Generate</button>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <lable>Name</lable>
                                <input class="form-control" name="name" placeholder="Promocode Name" value="{{$promocode->name}}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 form-group">
                                <lable>Maximum Uses</lable>
                                <input type="number" class="form-control text-right" name="max_uses" value="{{($promocode->max_uses)?$promocode->max_uses:1}}">
                            </div>
                            <div class="col-md-2 form-group">
                                <lable>Discount Range</lable>
                                <select name="discount_range" class="form-control">
                                  <option value="first" @if($promocode->discount_range == "first") selected @endif>First Payment</option>
                                  <option value="all" @if($promocode->discount_range == "all") selected @endif>All Payments</option>
                                </select>
                            </div>
                            <div class="col-md-2 form-group">
                                <lable>Discount Amount</lable>
                                <input type="number" class="form-control text-right" aria-label="Discount Amount" name="discount_amount" value="{{$promocode->discount_amount}}">
                            </div>
                            <div class="col-md-2 form-group">
                                <lable>Discount Type</lable>
                                <select name="discount_type" class="form-control">
                                  <option value="percentage" @if($promocode->discount_type == "percentage") selected @endif>Percentage (%)</option>
                                  <option value="fixed" @if($promocode->discount_type == "fixed") selected @endif>Fixed (.00)</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Start Date</label>
                                <div class="position-relative has-icon-left">
                                  <input type="text" class="form-control datepicker" name="starts_at" value="{{$promocode->starts_at?date('m/d/Y', strtotime($promocode->starts_at)):date('m/d/Y')}}" readonly>
                                  <div class="form-control-position"> 
                                    <i class="feather icon-calendar"></i>
                                  </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>End Date</label>
                                <div class="position-relative has-icon-left">
                                  <input type="text" class="form-control datepicker" name="expires_at" value="{{$promocode->expires_at?date('m/d/Y', strtotime($promocode->expires_at)):date('m/d/Y')}}" readonly>
                                  <div class="form-control-position"> 
                                    <i class="feather icon-calendar"></i>
                                  </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <lable>Description</lable>
                            <textarea name="description" class="form-control" id="" cols="30" rows="10">{{$promocode->description}}</textarea>
                        </div>
                        <div class="col-md-12 text-right">
                            <br/>
                            <button class="btn btn-primary">Save</button>
                        </div>
                      </form>
                  </div>
              </div>
          </div>
      </div>
  </div>
</section>
<!-- // Basic Floating Label Form section end -->
@endsection
@section('myscript')
<script>
$(document).ready(function() {
  $('.datepicker').daterangepicker({
      singleDatePicker: true,
      showDropdowns: true,
      minYear: 1901,
      maxYear: parseInt(moment().format('YYYY'),10),
      setDate: null
  });

  $('#generate_code_btn').on('click', function() {
    var generate = "";
    var possible  = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    for ( var i=0; i < 10; i++ ) {
      generate += possible.charAt(Math.floor(Math.random() * possible.length));
    }
    $("#promocode_input").val(generate);
      //ref::    https://codepen.io/yy/pen/rVXJrR
  })
});
</script>
 
@endsection
@section('vendor-script')
  <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
@endsection

