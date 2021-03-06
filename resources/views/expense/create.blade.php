@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Create Expense')

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
              <h4 class="card-title">Add Expense</h4>
            </div>
      <div class="card-body">
        <form action="{{ action('ExpenseController@store') }}" method="post" class="form">
          @csrf
          <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Date</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" class="form-control datepicker" name="date" value="{{date('m/d/Y')}}">
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
                      <input type="text" class="form-control" name="reference_no" placeholder="Reference No.">
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
          <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Amount</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" class="form-control" name="amount" placeholder="Amount" id="amount">
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
                      <select name="expense_category_id" id="select_category" class="form-control select2" placeholder="Select Warehouse">
                        <option value="" disabled selected></option>
                        <option value="add_new">Add New Expense Category</option>
                        @forelse($categories as $category)
                          <option value="{{ $category->id }}">{{ $category->displayName() }}</option>
                        @empty
                        <option value="" disabled="">Please Add Expense Category</option>
                        @endforelse
                      </select>
                      <div class="form-control-position"> 
                        <i class="feather icon-list"></i>
                      </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Biller</label>
                <div class="position-relative has-icon-left">
                  <select name="supplier_id" id="select_supplier" class="form-control select2 update_select" placeholder="Select Biller">
                    <option value="" disabled selected></option>
                    <option value="add_new">Add New Supplier/Biller</option>
                    @forelse($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->company }}</option>
                    @empty
                    <option value="" disabled="">Please Add Supplier/Biller</option>
                    @endforelse
                  </select>
                  <div class="form-control-position"> 
                    <i class="feather icon-user"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label>Note:</label>
                    <div class="position-relative has-icon-left">
                      <textarea type="text" class="form-control" name="note" placeholder="Note"></textarea>
                      <div class="form-control-position"> 
                        <i class="feather icon-file-text"></i>
                      </div>
                </div>
            </div>
          </div>
          <div class="col-md-4">
                <div class="form-group">
                    <label>Attachment</label>
                    <div class="position-relative has-icon-left">
                      <input type="file" class="form-control" name="attachment" placeholder="Attachment">
                      <div class="form-control-position"> 
                        <i class="feather icon-file"></i>
                      </div>
                    </div>
                </div>
            </div>
      </div>
      <br>
      <br>
      <h4 class="card-title">Payment</h4>
      <hr>
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
              <label>Payment Referencce No.</label>
              <div class="position-relative has-icon-left">
                <input type="text" class="form-control update_input" name="payment_reference_no" placeholder="Reference No.">
                <div class="form-control-position"> 
                  <i class="feather icon-hash"></i>
                </div>
              </div>
          </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Paid Amount</label>
                <div class="position-relative has-icon-left">
                  <input type="number" name="paid" class="form-control update_input check_max_quantity" value="0" max="0">
                  <div class="form-control-position"> 
                    <i class="feather icon-dollar-sign"></i>
                  </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <label>Payment Type</label>
            <div class="position-relative has-icon-left">
              <select name="payment_type" id="payment_type" class="form-control select2">
                <option value="cash">Cash</option>
                <option value="gift_certificate">Gift Card</option>
                <option value="credit_card">Credit Card</option>
                <option value="cheque">Cheque</option>
                <option value="deposit">Deposit</option>
                <option value="other">Other</option>
              </select>
              <div class="form-control-position"> 
                <i class="feather icon-credit-card"></i>
              </div>
            </div>
        </div>
      </div>
      <div class="credit_card payment_type_ext" style="display: none;">
        <div class="row">
          <div class="col-md-12">
            <div class="card border-light">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <label for="cc_no">Credit Card No.</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" name="cc_no" class="form-control input-number" placeholder="Credit Card No.">
                      <div class="form-control-position"> 
                        <i class="feather icon-credit-card"></i>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label for="cc_no">Holder Name</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" name="cc_holder" class="form-control" placeholder="Holder Name">
                      <div class="form-control-position"> 
                        <i class="feather icon-user"></i>
                      </div>
                    </div>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-6">
                    <label for="cc_type">Card Type</label>
                    <div class="position-relative has-icon-left">
                      <select name="cc_type" class="form-control select2">
                        <option value="visa">Visa</option>
                        <option value="mastercard">Master Card</option>
                        <option value="amex">Amex</option>
                        <option value="discover">Discover</option>
                      </select>
                      <div class="form-control-position"> 
                        <i class="feather icon-credit-card"></i>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <label for="cc_no">Month</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" name="cc_month" class="form-control input-number" placeholder="Month">
                      <div class="form-control-position"> 
                        <i class="feather icon-credit-card"></i>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <label for="cc_no">Year</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" name="cc_year" class="form-control input-number" placeholder="Year">
                      <div class="form-control-position"> 
                        <i class="feather icon-credit-card"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="cheque payment_type_ext" style="display: none;">
        <div class="row">
          <div class="col-md-12">
            <div class="card border-light">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <label for="cheque_no">Cheque No.</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" name="cheque_no" class="form-control input-number" placeholder="Cheque No.">
                      <div class="form-control-position"> 
                        <i class="feather icon-credit-card"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="gift_certificate payment_type_ext" style="display: none;">
        <div class="row">
          <div class="col-md-12">
            <div class="card border-light">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <label for="gift_card_no">Gift Card No.</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" name="gift_card_no" class="form-control input-number" placeholder="Gift Card No.">
                      <div class="form-control-position"> 
                        <i class="feather icon-gift"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <label for="note">Payment Note</label>
          <textarea name="payment_note" id="" cols="20" rows="5" class="form-control" placeholder="Note"></textarea>
        </div>
      </div>
      <br><br>
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
  </section>
@endsection

@section('vendor-script')
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

  $('select[name=expense_category_id]').on('change', function() {
      var selected = $(this).find('option:selected').val();
      if(selected == 'add_new') {
        $.ajax({
          url :  "{{ action('ExpenseCategoryController@createModal') }}",
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

  $('#payment_type').on('change', function() {
      $('.payment_type_ext').hide('fast');
      $('.'+$(this).val()).show('fast');
    });

  $('#amount').on('change', function() {
      $("input[name=paid]").attr('max', $(this).val()).trigger('change');
    });


  $('select[name=supplier_id]').on('change', function() {
        var selected = $(this).find('option:selected').val();
        if(selected == 'add_new') {
          $.ajax({
            url :  "{{ route('supplier.addSupplierModal') }}",
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
