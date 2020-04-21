@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Add Deposit')

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
          <form action="{{ action('DepositController@store') }}" method="POST" class="form" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Customer</label>
                    <div class="position-relative has-icon-left">
                      <select name="customer_id" id="select_customer" class="form-control select2 update_select" placeholder="Select Customer">
                        <option value="" disabled selected></option>
                        <option value="add_new">Add New Customer</option>
                        @forelse($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->formatName() }}</option>
                        @empty
                        <option value="" disabled="">Please Add Customeer</option>
                        @endforelse
                      </select>
                      <div class="form-control-position"> 
                        <i class="feather icon-user"></i>
                      </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
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
          </div>
          <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Bank Reference No.</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" class="form-control update_input" name="reference_no" placeholder="Reference No.">
                      <div class="form-control-position">
                        <i class="feather icon-hash"></i>
                      </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
              <label>Amount</label>
              <div class="position-relative has-icon-left">
                <input type="number" name="amount" class="form-control">
                <div class="form-control-position"> 
                  <i class="feather icon-dollar-sign"></i>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <label for="note">Note</label>
              <textarea name="note" id="" cols="20" rows="5" class="form-control" placeholder="Note"></textarea>
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
 

<!-- The Modal -->
<div class="modal" id="supplier_modal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add New Supplier</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
         @if ($errors->any())
              <div class="alert alert-danger">
                  <ul>
                      @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
              </div>
          @endif
         <form  onsubmit="process_add_supplier(event)" >
          <div class="row">
              <div class="col-md-12 form-group">
                  <lable>Company</lable>
                  <input class="form-control" id="company" required>
              </div>
              <div class="col-md-12 form-group">
                  <lable>Contact Person</lable>
                  <input class="form-control" id="contact_person" required>
              </div>
              <div class="col-md-12 form-group">
                  <lable>Mobile Number</lable>
                  <input class="form-control" id="phone">
              </div>
              <div class="col-md-12 form-group">
                  <lable>Email</lable>
                  <input class="form-control" id="email">
              </div>
          </div>
          
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
         <button class="btn btn-primary">Save</button>
         </form>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>


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

    $('select[name=customer_id]').on('change', function() {
        var selected = $(this).find('option:selected').val();
        if(selected == 'add_new') {
          $.ajax({
            url :  "{{ route('customer.addCustomerModal') }}",
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
    
  });



</script>
 
@endsection
