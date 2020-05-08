@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Import SKU')

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
@endsection

@section('content')

<section class="card">
    <div class="card-content">
      <div class="card-body">
          <form action="{{ action('SkuController@submitImport') }}" class="form"  method="POST" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-md-12 form-group">
              <p>The first line in downloaded file should remain as it is. Please do not change the order of columns.</p>
              <p>The correct column order is <b>(code, name, brand, category, supplier, cost, price, alert_quantity)</b> & you must follow this. </p>
              <a href="{{asset('file/sample_sku.csv')}}" class="btn btn-primary">Download CSV Template</a>
              <a href="{{asset('file/sample_sku.xlsx')}}" class="btn btn-primary">Download XLSX Template</a>
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
          <div class="row">
            <div class="col-md-12">
              <input type="submit" name="import" class="btn btn-primary mr-1 mb-1 btn_save" value="Import SKU">
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
            $('.form')[0].reset();
              setTimeout(function(){
                  window.location.replace(result.redirect);
              }, 500);
          }else{
            if(result.msg){
              toastr.error(result.msg);
            }
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
