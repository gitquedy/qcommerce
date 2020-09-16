@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Edit User')

@section('content')
<section id="floating-label-layouts" class="container justify-content-md-center">
  <div class="row match-height">
      <div class="col-md-12 col-12">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">User Details</h4>
              </div>
              <div class="card-content">
                  <div class="card-body">
                      <form action="{{ action('UserController@update', $user->id) }}" method="POST" class="form" enctype="multipart/form-data">
                        @method('put')
                        @csrf
                        <div class="row justify-content-md-center">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control" name="first_name" placeholder="First Name" value="{{ $user->first_name }}">
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
                                      <input type="text" class="form-control" name="last_name" placeholder="Last Name" value="{{ $user->last_name }}">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-user"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                          </div>
                          <div class="row justify-content-md-center">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Email</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control" name="email" placeholder="Email Address" value="{{ $user->email }}">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-mail"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Profile Picture</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="file" class="form-control" name="picture">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-file"></i>
                                      </div>
                                    </div>  
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="card-header">
                              <h4 class="card-title">Change Password:</h4>
                            </div>
                          </div><br>
                          <div class="row justify-content-md-center"> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Password</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="password" class="form-control" name="password" placeholder="Password">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-lock"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Confirm Password</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-lock"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                          </div>
                      @include('user.partials.edit_permissions', ['user' => $user, 'shops' => $shops, 'warehouses' => $warehouses])
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
  <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
@endsection

