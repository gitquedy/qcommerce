@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Connect Shop')

@section('content')
<!-- // Basic Floating Label Form section start -->
<section id="floating-label-layouts">
  <div class="row match-height">
      <div class="col-md-12 col-12">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Completed <small>You are now connected</small>
                    <br><br>
                    <small>Please enter shop name to distinguish from other shops</small>
                  </h4>
              </div>
              <div class="card-content">
                  <div class="card-body">
                      <form action="{{ action('ShopController@store') }}" method="POST" class="form" enctype="multipart/form-data">
                          @csrf
                          <input type="hidden" name="code" value="{{ $request->get('code') }}">
                          <div class="form-body">
                              <div class="row">
                                  <div class="col-6">
                                      <div class="col-sm-12 data-field-col">
                                          <label for="data-name">Shop Name</label>
                                          <input type="text" class="form-control" name="name" placeholder="Shop Name">
                                      </div>
                                  </div>
                                  <div class="col-6">
                                      <div class="col-sm-12 data-field-col">
                                          <label for="data-name">Short Name</label>
                                          <input type="text" class="form-control" name="short_name" placeholder="Short Name">
                                      </div>
                                  </div>
                                  <div class="form-group col-12">
                                  </div>
                                  <div class="col-6">
                                   <div class="col-12">
  <!--                                       <input type="submit" name="saveandadd" class="btn btn-warning mr-1 mb-1 btn_save" value="Save and Add Another"> -->
                                        <input type="submit" name="save" class="btn btn-primary mr-1 mb-1 btn_save" value="Save">
                                        <button type="reset" class="btn btn-outline-warning mr-1 mb-1">Reset </button>

                                    </div>
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

