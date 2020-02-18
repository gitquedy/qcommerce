@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Inventory')

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
{{-- Data list view starts --}}

<style>
    .product_image{
        width:100px;
        height:auto;
    }
</style>

<section class="card">
    <div class="card-content">
      <div class="card-body">
          
          
          <form action="{{route('supplier.store')}}" method="post">
          @csrf
          <div class="row">
              <div class="col-md-6 form-group">
                  <lable>Company</lable>
                  <input class="form-control" name="company" required>
              </div>
              <div class="col-md-6 form-group">
                  <lable>Contact Person</lable>
                  <input class="form-control" name="contact_person" required>
              </div>
              <div class="col-md-6 form-group">
                  <lable>Mobile Number</lable>
                  <input type="text" class="form-control" name="phone">
              </div>
              <div class="col-md-6 form-group">
                  <lable>Email</lable>
                  <input type="email" class="form-control" name="email">
              </div>
          </div>
          <div class="col-md-12 text-right">
              <br/>
              <button class="btn btn-primary">Save</button>
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




</script>
 
@endsection
