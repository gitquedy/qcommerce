@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Deposit')
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
<section id="floating-label-layouts">
  <div class="row match-height">
      <div class="col-md-12 col-12">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Deposit Details</h4>
              </div>
              <div class="card-content">
                  <div class="card-body">
                      <form action="{{ action('DepositController@update', $deposit->id) }}" method="POST" class="form" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="row">
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label>Customer</label>
                                  <div class="position-relative has-icon-left">
                                    <select name="customer_id" id="select_customer" class="form-control select2 update_select" placeholder="Select Customer">
                                      <option value="" disabled selected></option>
                                      <option value="add_new">Add New Customer</option>
                                      @forelse($customers as $customer)
                                      <option value="{{ $customer->id }}" @if($deposit->customer_id == $customer->id) selected @endif>{{ $customer->formatName() }}</option>
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
                                    <input type="text" class="form-control datepicker update_input" name="date" value="{{date('m/d/Y', strtotime($deposit->date))}}" readonly>
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
                                    <input type="text" class="form-control update_input" name="reference_no" placeholder="Reference No." value="{{$deposit->reference_no}}">
                                    <div class="form-control-position">
                                      <i class="feather icon-hash"></i>
                                    </div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-6">
                            <label>Amount</label>
                            <div class="position-relative has-icon-left">
                              <input type="number" name="amount" class="form-control" value="{{$deposit->amount}}">
                              <div class="form-control-position"> 
                                <i class="feather icon-dollar-sign"></i>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <label for="note">Note</label>
                            <textarea name="note" id="" cols="20" rows="5" class="form-control" placeholder="Note">{{$deposit->note}}</textarea>
                          </div>
                        </div>
                        <div class="form-group col-12"></div>
                        <div class="row">
                          <div class="col-6">
                           <div class="col-12">
                                <input type="submit" name="save" class="btn btn-primary mr-1 mb-1 btn_save" value="Save">
                               <!--  <button type="reset" class="btn btn-outline-warning mr-1 mb-1">Reset --> </button>
                            </div>
                          </div>
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
@section('vendor-script')
<script>
  $('.select2').select2();
</script>
  <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
@endsection

