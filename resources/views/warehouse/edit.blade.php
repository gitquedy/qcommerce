@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Warehouse')

@section('content')
<section id="floating-label-layouts">
  <div class="row match-height">
      <div class="col-md-12 col-12">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Warehouse Details</h4>
              </div>
              <div class="card-content">
                  <div class="card-body">
                      <form action="{{ action('WarehouseController@update', $warehouse->id) }}" method="POST" class="form" enctype="multipart/form-data">
                        @method('put')
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Code</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control" name="code" placeholder="Code" value="{{ $warehouse->code }}">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-box"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control" name="name" placeholder="Name" value="{{ $warehouse->name }}">
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
                                    <label>Address</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control" name="address" placeholder="Address" value="{{ $warehouse->address }}">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-home"></i>
                                      </div>
                                    </div>  
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control" name="phone" placeholder="Phone Number"  value="{{ $warehouse->phone }}">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-phone"></i>
                                      </div>
                                    </div>  
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Email</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control" name="email" placeholder="Email Address" value="{{ $warehouse->email }}">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-mail"></i>
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

