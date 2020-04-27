@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Add Promocode')

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
{{-- Data list view starts --}}
<section class="card">
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
          <form action="{{ action('SalesController@store') }}" method="post">
          @csrf
          <div class="row">
              <div class="col-md-4 form-group">
                  <lable>Code</lable>
                  <div class="input-group">
                    <input type="text" id="promocode_input" class="form-control text-uppercase" placeholder="Promocode" aria-label="Promocode" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary" id="generate_code_btn" type="button">Generate</button>
                    </div>
                  </div>
              </div>
              <div class="col-md-4 form-group">
                  <lable>Name</lable>
                  <input class="form-control" name="name">
              </div>
              <div class="col-md-4 form-group">
                  <lable>Maximum Uses</lable>
                  <input type="number" class="form-control text-right" name="max_uses" value="1">
              </div>
              <div class="col-md-4 form-group">
                  <lable>Maximum Use Per Business</lable>
                  <input type="number" class="form-control text-right" name="max_uses_business" value="1">
              </div>
              <div class="col-md-4 form-group">
                  <lable>Discount Amount</lable>
                  <input type="number" class="form-control text-right" aria-label="Discount Amount">
              </div>
              <div class="col-md-4 form-group">
                  <lable>Discount Type</lable>
                  <select name="discount_type" class="form-control">
                    <option value="percentage">Percentage (%)</option>
                    <option value="fixed">Fixed (.00)</option>
                  </select>
              </div>
              <div class="col-md-4">
                  <div class="form-group">
                      <label>Start Date</label>
                      <div class="position-relative has-icon-left">
                        <input type="text" class="form-control datepicker update_input" name="starts_at" value="{{date('m/d/Y')}}" readonly>
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
                        <input type="text" class="form-control datepicker update_input" name="expires_at" value="{{date('m/d/Y')}}" readonly>
                        <div class="form-control-position"> 
                          <i class="feather icon-calendar"></i>
                        </div>
                      </div>
                  </div>
              </div>
              <div class="col-md-12">
                  <lable>Description</lable>
                  <textarea name="description" class="form-control" id="" cols="30" rows="10"></textarea>
              </div>
              <div class="col-md-12 text-right">
                  <br/>
                  <button class="btn btn-primary">Save</button>
              </div>
          </div>
          </form>
      </div>
    </div>
  </section>
  {{-- Data list view end --}}
  
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
  <script src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>
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
    $("#promocode_input").val('TEST123');

      //ref::    https://codepen.io/yy/pen/rVXJrR
  })
});
</script>
 
@endsection
