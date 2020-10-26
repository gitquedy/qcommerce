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
      <div class="card-header">
              <h4 class="card-title">Edit Expense {{ $expense->reference_no }}</h4>
            </div>
      <div class="card-body">
          <form action="{{ action('ExpenseController@update' , $expense->id) }}" method="post" class="form">
          @method('put')
                @csrf
                <div class="row">
                  <div class="col-md-4">
                      <div class="form-group">
                          <label>Date</label>
                          <div class="position-relative has-icon-left">
                            <input type="text" class="form-control datepicker" name="date" value="{{ Carbon\Carbon::parse($expense->date)->format('m/d/Y') }}">
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
                            <input type="text" class="form-control" name="reference_no" placeholder="Reference No." value="{{ $expense->reference_no }}">
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
                              <option value="{{ $warehouse->id }}" {{ $expense->warehouse_id == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
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
                <div class="row">
                  <div class="col-md-4">
                      <div class="form-group">
                          <label>Amount</label>
                          <div class="position-relative has-icon-left">
                            <input type="text" class="form-control" name="amount" placeholder="Amount" value="{{ $expense->amount }}">
                            <div class="form-control-position"> 
                              <i class="feather icon-dollar-sign"></i>
                            </div>
                          </div>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="form-group">
                          <label>Category</label>
                          <div class="position-relative has-icon-left">
                            <select name="expense_category_id" class="form-control select2" placeholder="Select Warehouse">
                              <option disabled selected hidden></option>
                              @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $expense->expense_category_id == $category->id ? 'selected' : '' }}>{{ $category->displayName() }}</option>
                              @endforeach
                            </select>
                            <div class="form-control-position"> 
                              <i class="feather icon-list"></i>
                            </div>
                          </div>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="form-group">
                          <label>Attachment</label>
                          <label>Currently: <a target="_blank" href="{{ $expense->attachment_link() }}"> {{ $expense->attachment }}</a></label>
                          <div class="position-relative has-icon-left">
                            <input type="file" class="form-control" name="attachment" placeholder="Attachment">
                            <div class="form-control-position"> 
                              <i class="feather icon-file"></i>
                            </div>
                          </div>
                      </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                      <div class="form-group">
                          <label>Note:</label>
                          <div class="position-relative has-icon-left">
                            <textarea type="text" class="form-control" name="note" placeholder="Note">{{ $expense->note }}</textarea>
                            <div class="form-control-position"> 
                              <i class="feather icon-file-text"></i>
                            </div>
                      </div>
                  </div>
                </div>
            </div>
            <div class="row">
              <div class="col-6">
               <div class="col-12">
                  <input type="submit" name="save" class="btn btn-primary mr-1 mb-1 btn_save" value="Save">
                  <button type="reset" id="reset_btn" class="btn btn-danger mr-1 mb-1">Reset </button>
                </div>
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
    $('.datepicker').daterangepicker({
      singleDatePicker: true,
      showDropdowns: true,
      minYear: 1901,
      maxYear: parseInt(moment().format('YYYY'),10),
      setDate: null
  });

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
 </script>
 <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
 
@endsection
