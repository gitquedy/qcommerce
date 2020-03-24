@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Settings')

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
<section id="stacked-pills">
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-body">
          <form action="{{ action('SettingsController@store') }}" method="POST" class="form" enctype="multipart/form-data">
            @csrf
            <h3>Order Prefix</h3>
            <hr>
            <div class="row">
              <div class="col-md-6">
                  <div class="form-group">
                      <label>Sales Order</label>
                      <div class="position-relative has-icon-left">
                        <input type="text" class="form-control" name="sales_prefix" placeholder="SALE" value="{{$settings->sales_prefix}}">
                        <div class="form-control-position"> 
                          <i class="feather icon-hash"></i>
                        </div>
                      </div>
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="form-group">
                      <label>Quote</label>
                      <div class="position-relative has-icon-left">
                        <input type="text" class="form-control" name="quote_prefix" placeholder="QUOTE"  disabled value="{{$settings->quote_prefix}}">
                        <div class="form-control-position"> 
                          <i class="feather icon-hash"></i>
                        </div>
                      </div>
                  </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                  <div class="form-group">
                      <label>Purchase Order</label>
                      <div class="position-relative has-icon-left">
                        <input type="text" class="form-control" name="purchase_prefix" placeholder="PO"  disabled value="{{$settings->purchase_prefix}}">
                        <div class="form-control-position"> 
                          <i class="feather icon-hash"></i>
                        </div>
                      </div>
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="form-group">
                      <label>Transfer</label>
                      <div class="position-relative has-icon-left">
                        <input type="text" class="form-control" name="transfer_prefix" placeholder="TR"  disabled value="{{$settings->transfer_prefix}}">
                        <div class="form-control-position"> 
                          <i class="feather icon-hash"></i>
                        </div>
                      </div>
                  </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                  <div class="form-group">
                      <label>Delivery</label>
                      <div class="position-relative has-icon-left">
                        <input type="text" class="form-control" name="delivery_prefix" placeholder="DO"  disabled value="{{$settings->delivery_prefix}}">
                        <div class="form-control-position"> 
                          <i class="feather icon-hash"></i>
                        </div>
                      </div>
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="form-group">
                      <label>Payment</label>
                      <div class="position-relative has-icon-left">
                        <input type="text" class="form-control" name="payment_prefix" placeholder="PAY"  disabled value="{{$settings->payment_prefix}}">
                        <div class="form-control-position"> 
                          <i class="feather icon-hash"></i>
                        </div>
                      </div>
                  </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                  <div class="form-group">
                      <label>Return</label>
                      <div class="position-relative has-icon-left">
                        <input type="text" class="form-control" name="return_prefix" placeholder="SR"  disabled value="{{$settings->return_prefix}}">
                        <div class="form-control-position"> 
                          <i class="feather icon-hash"></i>
                        </div>
                      </div>
                  </div>
              </div>
            </div>
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                    <input type="submit" name="save" class="btn btn-primary mr-1 mb-1 btn_save" value="Save">
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
@section('vendor-script')
{{-- vednor js files --}}
  <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection
@section('myscript')
<script type="text/javascript">
</script>
@endsection
