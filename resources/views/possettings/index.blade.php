@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'POS Settings')

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
  <div class="row match-height">
    <div class="col-sm-12">
      <div class="row">
        <div class="col-sm-3">
          <div class="card">
            <div class="card-body">
              <div class="row">
                  <ul class="nav nav-pills w-100 flex-column">
                    <li class="nav-item">
                      <a class="nav-link p-2  active" id="stacked-pill-0" data-toggle="pill" href="#vertical-pill-0"
                        aria-expanded="true">
                        POS Settings
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link p-2" id="stacked-pill-1" data-toggle="pill" href="#vertical-pill-1"
                        aria-expanded="true">
                        Order Prefix
                      </a>
                    </li>
                    <li class="nav-item hidden">
                      <a class="nav-link p-2" id="stacked-pill-2" data-toggle="pill" href="#vertical-pill-2"
                        aria-expanded="false">
                        --2
                      </a>
                    </li>
                    <li class="nav-item hidden">
                      <a class="nav-link p-2" id="stacked-pill-3" data-toggle="pill" href="#vertical-pill-3"
                        aria-expanded="false">
                        --3
                      </a>
                    </li>
                  </ul>              
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-9">
          <div class="card">
            <div class="card-body">
              <div class="tab-content">
                <div class="tab-pane active" id="vertical-pill-0" role="tabpanel" aria-labelledby="stacked-pill-0"
                  aria-expanded="false">
                  <h3>POS Settings</h3>
                  <hr>
                </div>
                <div class="tab-pane" id="vertical-pill-1" role="tabpanel" aria-labelledby="stacked-pill-1"
                  aria-expanded="false">
                  <h3>Order Prefix</h3>
                  <hr>
                  <form action="{{ action('PosSettingsController@store') }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                      <div class="col-md-6">
                          <div class="form-group">
                              <label>Sales Order</label>
                              <div class="position-relative has-icon-left">
                                <input type="text" class="form-control" name="sales_prefix" placeholder="SALE" value="{{$order_ref->sales_prefix}}">
                                <div class="form-control-position"> 
                                  <i class="feather icon-hash"></i>
                                </div>
                              </div>
                          </div>
                      </div>
                      <div class="col-md-6 hidden">
                          <div class="form-group">
                              <label>Quote</label>
                              <div class="position-relative has-icon-left">
                                <input type="text" class="form-control" name="quote_prefix" placeholder="QUOTE" value="{{$order_ref->quote_prefix}}">
                                <div class="form-control-position"> 
                                  <i class="feather icon-hash"></i>
                                </div>
                              </div>
                          </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6 hidden">
                          <div class="form-group">
                              <label>Purchase Order</label>
                              <div class="position-relative has-icon-left">
                                <input type="text" class="form-control" name="purchase_prefix" placeholder="PO" value="{{$order_ref->purchase_prefix}}">
                                <div class="form-control-position"> 
                                  <i class="feather icon-hash"></i>
                                </div>
                              </div>
                          </div>
                      </div>
                      <div class="col-md-6 hidden">
                          <div class="form-group">
                              <label>Transfer</label>
                              <div class="position-relative has-icon-left">
                                <input type="text" class="form-control" name="transfer_prefix" placeholder="TR" value="{{$order_ref->transfer_prefix}}">
                                <div class="form-control-position"> 
                                  <i class="feather icon-hash"></i>
                                </div>
                              </div>
                          </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6 hidden">
                          <div class="form-group">
                              <label>Delivery</label>
                              <div class="position-relative has-icon-left">
                                <input type="text" class="form-control" name="delivery_prefix" placeholder="DO" value="{{$order_ref->delivery_prefix}}">
                                <div class="form-control-position"> 
                                  <i class="feather icon-hash"></i>
                                </div>
                              </div>
                          </div>
                      </div>
                      <div class="col-md-6 hidden">
                          <div class="form-group">
                              <label>Payment</label>
                              <div class="position-relative has-icon-left">
                                <input type="text" class="form-control" name="payment_prefix" placeholder="PAY" value="{{$order_ref->payment_prefix}}">
                                <div class="form-control-position"> 
                                  <i class="feather icon-hash"></i>
                                </div>
                              </div>
                          </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6 hidden">
                          <div class="form-group">
                              <label>Return</label>
                              <div class="position-relative has-icon-left">
                                <input type="text" class="form-control" name="return_prefix" placeholder="SR" value="{{$order_ref->return_prefix}}">
                                <div class="form-control-position"> 
                                  <i class="feather icon-hash"></i>
                                </div>
                              </div>
                          </div>
                      </div>
                    </div>
                    <div class="form-group col-12"></div>
                    <div class="row">
                      <div class="col-6">
                        <div class="col-12">
                            <input type="submit" name="save" class="btn btn-primary mr-1 mb-1 btn_save" value="Save">
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
                <div class="tab-pane" id="vertical-pill-2" role="tabpanel" aria-labelledby="stacked-pill-2"
                  aria-expanded="false">
                  ---2
                </div>
                <div class="tab-pane" id="vertical-pill-3" role="tabpanel" aria-labelledby="stacked-pill-3"
                  aria-expanded="false">
                  ---3
                </div>
              </div>
            </div>
          </div>
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
  {{-- Page js files --}}
  <!-- datatables -->
<script type="text/javascript">  
</script>
@endsection
