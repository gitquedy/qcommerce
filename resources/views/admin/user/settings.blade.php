@extends('layouts/contentLayoutMaster')

@section('title', 'Account Settings')

@section('vendor-style')
        <!-- vendor css files -->
        <link rel='stylesheet' href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
        <link rel='stylesheet' href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
@endsection
@section('page-style')
        <!-- Page css files -->
        <link rel="stylesheet" href="{{ asset(mix('css/plugins/extensions/noui-slider.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('css/core/colors/palette-noui.css')) }}">
@endsection

@section('content')
<!-- account setting page start -->
<section id="page-account-settings">
    <div class="row">
      <!-- left menu section -->
      <div class="col-md-3 mb-2 mb-md-0">
        <ul class="nav nav-pills flex-column mt-md-0 mt-1">
          <li class="nav-item">
            <a class="nav-link d-flex py-75 active" id="account-pill-general" data-toggle="pill"
              href="#account-vertical-general" aria-expanded="true">
              <i class="feather icon-globe mr-50 font-medium-3"></i>
              General
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link d-flex py-75" id="account-pill-password" data-toggle="pill"
              href="#account-vertical-password" aria-expanded="false">
              <i class="feather icon-lock mr-50 font-medium-3"></i>
              Change Password
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link d-flex py-75" id="account-pill-billing" data-toggle="pill"
              href="#account-vertical-billing" aria-expanded="false">
              <i class="feather icon-file-text mr-50 font-medium-3"></i>
              Billing / Invoices
            </a>
          </li>
        </ul>
      </div>
      <!-- right content section -->
      <div class="col-md-9">
        <div class="card">
          <div class="card-content">
            <div class="card-body">
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="account-vertical-general"
                  aria-labelledby="account-pill-general" aria-expanded="true">
                  @include('user.partials.profile')
                </div>
                <div class="tab-pane fade " id="account-vertical-password" role="tabpanel"
                  aria-labelledby="account-pill-password" aria-expanded="false">
                  @include('user.partials.password')
                </div>
                <div class="tab-pane fade " id="account-vertical-billing" role="tabpanel"
                  aria-labelledby="account-pill-billing" aria-expanded="false">
                  @include('user.partials.billing')
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>
<!-- account setting page end -->
@endsection

@section('vendor-script')
        <!-- vendor files -->
        <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/forms/validation/jqBootstrapValidation.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/extensions/dropzone.min.js')) }}"></script>
        <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
@endsection
@section('page-script')
        <!-- Page js files -->
        
@endsection