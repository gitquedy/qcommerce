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
        <div class="card-header">
          <h4 class="card-title">Account</h4>
        </div>
        <div class="card-body">
        <div class="collapse-header">
          <div id="headingCollapse1">
            <div class="lead collapse-title" data-toggle="collapse" role="button" data-target="#collapse1"
              aria-expanded="false" aria-controls="collapse1">
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
                <form class="form form-vertical" action="{{route('user.updateProfile')}}" method="post" enctype="multipart/form-data">
                  @csrf
                  <div class="form-group">
                    <label>First Name</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" class="form-control" name="first_name" value="{{ $user->first_name }}" placeholder="First Name">
                      <div class="form-control-position"> 
                        <i class="feather icon-user"></i>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Last Name</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" class="form-control" name="last_name" value="{{ $user->last_name }}" placeholder="Last Name">
                      <div class="form-control-position"> 
                        <i class="feather icon-user"></i>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Email</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" class="form-control" name="email" value="{{ $user->email }}" placeholder="Email">
                      <div class="form-control-position">
                        <i class="feather icon-mail"></i>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Phone</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" class="form-control" name="phone" value="{{ $user->phone }}" placeholder="Phone Number">
                      <div class="form-control-position">
                        <i class="feather icon-phone"></i>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Profile picture</label>
                    <input  id="profilePicUpload" class="form-control" type="file" name="picture"/>
                  </div>
                  {{-- <div class="form-group">
                    <div action="#" class="dropzone dropzone-area">
                      <div class="dz-message">Upload Profile Picture</div>
                    </div>
                  </div> --}}
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
@section('vendor-script')
{{-- vednor files --}}
        {{-- <script src="{{ asset(mix('vendors/js/extensions/dropzone.min.js')) }}"></script> --}}
@endsection
@section('myscript')
{{-- Page js files --}}
        {{-- <script src="{{ asset(mix('js/scripts/extensions/dropzone.js')) }}"></script> --}}
        {{-- <script src="{{ asset(mix('js/scripts/pages/user-settings.js')) }}"></script> --}}
@endsection
