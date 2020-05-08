@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Import Adjustment')

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

<section class="card">
    <div class="card-content">
      <div class="card-body">
          <form action="{{ action('AdjustmentController@submitImport') }}" class="form"  method="POST" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Date</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" class="form-control datepicker update_input" name="date" value="{{date('m/d/Y')}}" readonly>
                      <div class="form-control-position"> 
                        <i class="feather icon-calendar"></i>
                      </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Referencce No.</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" class="form-control update_input" name="reference_no" placeholder="Reference No.">
                      <div class="form-control-position"> 
                        <i class="feather icon-hash"></i>
                      </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Warehouse</label>
                    <div class="position-relative has-icon-left">
                      <select name="warehouse_id" id="select_warehouse" class="form-control select2 update_select" placeholder="Select Warehouse">
                        <option value="" disabled selected></option>
                        <option value="add_new">Add New Warehouse</option>
                        @forelse($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @empty
                        <option value="" disabled="">Please Add Warehouse</option>
                        @endforelse
                      </select>
                      <div class="form-control-position"> 
                        <i class="feather icon-box"></i>
                      </div>
                    </div>
                </div>
            </div>
          </div>
          <br>
          <br>
          <div class="row">
            <div class="col-md-12 form-group">
              <p>The first line in downloaded file should remain as it is. Please do not change the order of columns.</p>
              <p>The correct column order is <b>(sku_code, quantity, type)</b> & you must follow this. </p>
              <a href="{{asset('file/sample_adjustment.csv')}}" class="btn btn-primary">Download CSV Template</a>
              <a href="{{asset('file/sample_adjustment.xlsx')}}" class="btn btn-primary">Download XLSX Template</a>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 form-group">
              <div class="custom-file">
                <input type="file" class="custom-file-input" id="customFile" name="file">
                <label class="custom-file-label" for="customFile">Choose file</label>
              </div>
            </div>
          </div>
          <br>
          <br>
          <div class="row">
            <div class="col-md-12">
              <label for="note">Note</label>
              <textarea name="note" id="" cols="20" rows="5" class="form-control" placeholder="Note"></textarea>
            </div>
          </div>
          <br>
          <br>
          <div class="row">
            <div class="col-md-12">
              <input type="submit" name="import" class="btn btn-primary mr-1 mb-1 btn_save" value="Import Adjustment">
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
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection
@section('myscript')
<script>
  $(".select2").select2({
      dropdownAutoWidth: true,
      width: '100%'
  });

  $('select[name=warehouse_id]').on('change', function() {
      var selected = $(this).find('option:selected').val();
      if(selected == 'add_new') {
        $.ajax({
          url :  "{{ route('warehouse.addWarehouseModal') }}",
          type: "POST",
          success: function (response) {
            if(response) {
              $(".view_modal").html(response).modal('show');
            }
          }
        });
        $(this).val('').trigger('change');
      } 
  });

  $('.datepicker').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    minYear: 1901,
    maxYear: parseInt(moment().format('YYYY'),10)
  });

$(function() {
  var button = 'save';
  $('input[type="submit"]').on('click', function(){
       button = this.name;
  });
  $(".form").submit(function(e) {
    e.preventDefault(); 
    
    if($('.btn_save').prop('disabled') == true){
      return false;
    }
     $('.btn_save').prop('disabled', true);
      $.ajax({
        url : $(this).attr('action'),
        type : 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function(result){  
          console.log(result);
          if(result.success == true){
            toastr.success(result.msg);
            $('.error').remove();
            $('.form')[0].reset();
              setTimeout(function(){
                  window.location.replace(result.redirect);
              }, 500);
          }else{
            if(result.msg){
                  toastr.error(result.msg);
            }
            $('.error').remove();
            $.each(result.error, function(index, val){
              var elem = $('[name="'+ index +'"]');
              if(index == 'adjustment_item_array') {
                $('#customFile').after('<label class="text-danger error">' + val + '</label>');
              }
              else if(elem.hasClass('select2-hidden-accessible')) {
                new_elem = elem.parent().find('span.select2.select2-container')
                new_elem.after('<label class="text-danger error">' + val + '</label>');
              }
              else {
                elem.after('<label class="text-danger error">' + val + '</label>');
              }
            });
          }
          $('.btn_save').prop('disabled', false);
           },
          error: function(jqXhr, json, errorThrown){
            console.log(jqXhr);
            console.log(json);
            console.log(errorThrown);
            $('.btn_save').prop('disabled', false);
          }
      });
  });
});
</script>
 
@endsection
