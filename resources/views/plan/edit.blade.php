@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Customer')

@section('content')
<section id="floating-label-layouts">
  <div class="row match-height">
      <div class="col-md-12 col-12">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Customer Details</h4>
              </div>
              <div class="card-content">
                  <div class="card-body">
                      <form action="{{ action('CustomerController@update', $customer->id) }}" method="POST" class="form" enctype="multipart/form-data">
                        @method('put')
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control" name="first_name" placeholder="First Name" value="{{ $customer->first_name }}">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-user"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control" name="last_name" placeholder="Last Name" value="{{ $customer->last_name }}">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-user"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Email</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control" name="email" placeholder="Email Address" value="{{ $customer->email }}">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-mail"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Mobile</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control" name="phone" placeholder="Mobile Number"  value="{{ $customer->phone }}">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-phone"></i>
                                      </div>
                                    </div>  
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Price Group</label>
                                    <div class="position-relative has-icon-left">
                                      <select name="price_group" id="price_group" class="form-control select2" placeholder="Price Group">
                                        <option value="0">Default</option>
                                        @foreach($price_group as $pg)
                                        <option value="{{$pg->id}}" @if($customer->price_group == $pg->id) selected @endif>{{$pg->name}}</option>
                                        @endforeach
                                      </select>
                                      <div class="form-control-position"> 
                                        <i class="feather icon-users"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Address</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control" name="address" placeholder="Address" value="{{ $customer->address }}">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-home"></i>
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

