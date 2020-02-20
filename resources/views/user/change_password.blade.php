@extends('layouts/contentLayoutMaster')

@section('title', 'Change Password')

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
        <div class="card-header">
          <h4 class="card-title">Account</h4>
        </div>
        <div class="card-body">
        <div class="collapse-header">
          <div id="headingCollapse1">
            <div class="lead collapse-title">
              <div class="media">
                <a class="media-left" href="#">
                  <img  class="rounded-circle mr-2" src="{{ asset('images/profile/profile-picture/'.$user->picture) }}" alt="Generic placeholder image"
                    height="64" width="64" />
                </a>
                <div class="media-body mt-1">
                 <h5 class="media-heading mb-0">{{$user->formatName()}}</h5>
                  <a class="text-muted" href="#"><small>{{ $user->email }}</small></a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div role="tabpanel" aria-labelledby="headingCollapse1" class="collapse show mt-2">
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
                <form class="form form-vertical" action="{{route('user.updatePassword')}}" method="post" enctype="multipart/form-data">
                  @csrf
                  <div class="form-group">
                    <label>Old Password</label>
                    <div class="position-relative has-icon-left">
                      <input type="password" class="form-control" name="old_password" placeholder="Old Password">
                      <div class="form-control-position"> 
                        <i class="feather icon-lock"></i>
                      </div>
                    </div>
                  </div>
                  <br>
                  <div class="form-group">
                    <label>New Password</label>
                    <div class="position-relative has-icon-left">
                      <input type="password" class="form-control" name="password" placeholder="New Password">
                      <div class="form-control-position"> 
                        <i class="feather icon-lock"></i>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Confirm New Password</label>
                    <div class="position-relative has-icon-left">
                      <input type="password" class="form-control" name="password_confirm" placeholder="Confirm New Password">
                      <div class="form-control-position"> 
                        <i class="feather icon-lock"></i>
                      </div>
                    </div>
                  </div>
                <button type="submit" class="btn btn-primary mt-1 mb-1">Update</button>
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
@section('myscript')
        <script src="{{ asset(mix('js/scripts/pages/user-settings.js')) }}"></script>
@endsection
