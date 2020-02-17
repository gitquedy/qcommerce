@extends('layouts/contentLayoutMaster')

@section('title', 'User Settings')

@section('vendor-style')
        {{-- vednor css files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/file-uploaders/dropzone.min.css')) }}">
@endsection

@section('mystyle')
        {{-- Page Css files --}}
        <link rel="stylesheet" href="{{ asset(mix('css/pages/user-settings.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('css/plugins/file-uploaders/dropzone.css')) }}">
@endsection
@section('content')
<!--Settings-begins -->
<section>
  <!-- Account-begins -->
    <div class="settings-account">
     <!-- <h6 class="mb-1">Account</h6> -->
     <div class="card user-form">
        <div class="card-body">
        <div class="collapse-header">
        <div id="collapse1" role="tabpanel" aria-labelledby="headingCollapse1" class="collapse show mt-2">
            <div class="card-content">
              <div class="">
              @if ($message = Session::get('success'))

                  <div class="alert alert-success alert-block">

                      <button type="button" class="close" data-dismiss="alert">×</button>

                      <strong>{{ $message }}</strong>

                  </div>

              @endif

              @if (count($errors) > 0)
                  <div class="alert alert-danger">
                      <strong>Whoops!</strong> There were some problems with your input.<br><br>
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
              @endif
            </div>
            <form class="dropzone">
              <label>Profile picture</label>
              <div class="form-group">
                <div class="fallback">
                  <input name="file" type="file" multiple />
                </div>
                <div action="#" class="dropzone dropzone-area" id="profilePicUpload">
                  <div class="dz-message">Upload Profile Picture</div>
                </div>
              </div>
            </form>
          </div>
        </div>
        </div>
       </div>
    </div>
  <!-- Security-end -->
  </section>
  <!-- Settings-end -->
@endsection
@section('vendor-script')
{{-- vednor files --}}
        <script src="{{ asset(mix('vendors/js/extensions/dropzone.min.js')) }}"></script>
@endsection
@section('myscript')
{{-- Page js files --}}
        <script src="{{ asset(mix('js/scripts/extensions/dropzone.js')) }}"></script>
        <script src="{{ asset(mix('js/scripts/pages/user-settings.js')) }}"></script>
@endsection
