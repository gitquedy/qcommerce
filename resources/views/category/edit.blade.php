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
          
          @if(isset($Category))
          <form action="{{route('category.update')}}" method="post">
          @csrf
          <input type="hidden" name="id" value="{!!$Category->id!!}">
          <div class="row">
              <div class="col-md-12 form-group">
                  <lable>Code</lable>
                  <input class="form-control" value="{!!$Category->code!!}" name="code" required>
              </div>
              <div class="col-md-12 form-group">
                  <lable>Name</lable>
                  <input class="form-control" value="{!!$Category->name!!}" name="name" required>
              </div>
              <div class="col-md-12 form-group">
                  <lable>Parent Category</lable>
                  <select class="form-control s2" name="parent">
                      <option value="">Select</option>
                      @foreach($CategoryALL as $CategoryVAL)
                      <option @if($Category->parent==$CategoryVAL->id) {{'selected'}}  @endif value="{{$CategoryVAL->id}}">{{$CategoryVAL->code." - ".$CategoryVAL->name}}</option>
                      @endforeach
                  </select>
              </div>
              <div class="col-md-12 text-right">
                  <br/>
                  <button class="btn btn-primary">Save</button>
              </div>
              
          </div>
          
          </form>
          @endif
          
          
          
          
          
          
        
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
