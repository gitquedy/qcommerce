@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Add New Crud')

@section('content')
<!-- // Basic Floating Label Form section start -->
<section id="floating-label-layouts">
  <div class="row match-height">
      <div class="col-md-6 col-12">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Crud Details</h4>
              </div>
              <div class="card-content">
                  <div class="card-body">
                      <form action="{{ action('CrudController@store') }}" method="POST" class="form" enctype="multipart/form-data">
                          @csrf
                          <div class="form-body">
                              <div class="row">
                                  <div class="col-12">
                                      <div class="form-label-group position-relative has-icon-left">
                                          <input type="text" name="name" class="form-control" placeholder="Name">
                                          <div class="form-control-position">
                                            <i class="feather icon-user"></i>
                                          </div>
                                          <label for="first-name-floating-icon">Name</label>
                                      </div>
                                  </div>
                                  <div class="form-group col-12">
                                  </div>
                                 <div class="col-12">
                                      <input type="submit" name="saveandadd" class="btn btn-warning mr-1 mb-1 btn_save" value="Save and Add Another">
                                      <input type="submit" name="save" class="btn btn-primary mr-1 mb-1 btn_save" value="Save">

                                      <input type="reset" class="btn btn-outline-warning mr-1 mb-1" value="Reset">
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

