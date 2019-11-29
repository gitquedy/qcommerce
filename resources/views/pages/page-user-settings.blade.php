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
                  <img  class="rounded-circle mr-2" src="{{ asset('images/portrait/small/avatar-s-3.png') }}" alt="Generic placeholder image"
                    height="64" width="64" />
                </a>
                <div class="media-body mt-1">
                 <h5 class="media-heading mb-0">Tommy Sicilia</h5>
                  <a class="text-muted" href="#"><small>tommys@mail.com</small></a>
                </div>
                <a class="text-muted mt-2" target="_blank" href="auth-login.html">
                  <i class="fa fa-angle-right mr-1"></i>
                  <small class="text-capitalize">sign out</small>
                </a>
            </div>
            </div>
          </div>
          </div>
          <div id="collapse1" role="tabpanel" aria-labelledby="headingCollapse1" class="collapse mt-2">
            <div class="card-content">
                  <form class="form form-vertical">
                    <div class="form-group">
                      <label>First Name</label>
                      <div class="position-relative has-icon-left">
                        <input type="text" class="form-control" name="firstname" placeholder="First Name">
                        <div class="form-control-position">
                          <i class="feather icon-user"></i>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Last Name</label>
                      <div class="position-relative has-icon-left">
                        <input type="text" class="form-control" name="lastname" placeholder="Last Name">
                        <div class="form-control-position">
                          <i class="feather icon-user"></i>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Email</label>
                      <div class="position-relative has-icon-left">
                        <input type="text" class="form-control" name="email" placeholder="Email">
                        <div class="form-control-position">
                          <i class="feather icon-mail"></i>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Company</label>
                      <div class="position-relative has-icon-left">
                        <input type="text" class="form-control" name="company-name" placeholder="Company Name">
                        <div class="form-control-position">
                          <i class="feather icon-at-sign"></i>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Location</label>
                      <div class="position-relative has-icon-left">
                        <input type="text" class="form-control" name="location" placeholder="Location">
                        <div class="form-control-position">
                          <i class="feather icon-map-pin"></i>
                        </div>
                      </div>
                    </div>
                    <label>Profile picture</label>
                    <div class="form-group">
                      <div action="#" class="dropzone dropzone-area" id="profilePicUpload">
                        <div class="dz-message">Upload Profile Picture</div>
                      </div>
                    </div>
                    <fieldset class="checkbox">
                      <div class="vs-checkbox-con vs-checkbox-primary">
                        <input type="checkbox">
                        <span class="vs-checkbox">
                          <span class="vs-checkbox--check">
                            <i class="vs-icon feather icon-check"></i>
                          </span>
                        </span>
                        <span class="">Available for hire</span>
                      </div>
                    </fieldset>
                  <button type="button" class="btn btn-primary mt-1 mb-1">Update</button>
               </form>
            </div>
          </div>
          </div>
        <div class="d-flex border-top pt-2 pl-2 pr-2">
            <p>Available for hire</p>
            <div class="custom-control custom-switch custom-switch-primary ml-auto">
                <input type="checkbox" class="custom-control-input" id="customSwitch1" checked>
                <label class="custom-control-label" for="customSwitch1"></label>
              </div>
          </div>
       </div>
    </div>
  <!-- Account-end -->
  <!-- Notification-begins -->
    <div class="settings-notification mb-2">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title pb-2">Notification</h4>
        </div>
        <div class="card-content">
          <ul class="list-group notification">
            <li class="list-group-item d-flex pt-1 pb-1">
              <span>Anyone can see my profile page</span>
              <div class="custom-control custom-switch custom-switch-primary ml-auto">
                  <input type="checkbox" class="custom-control-input" id="customSwitch2">
                  <label class="custom-control-label" for="customSwitch2"></label>
                </div>
            </li>
            <li class="list-group-item d-flex pt-1 pb-1">
              <span>Anyone can follow me</span>
              <div class="custom-control custom-switch custom-switch-primary ml-auto">
                  <input type="checkbox" class="custom-control-input" id="customSwitch3" checked>
                  <label class="custom-control-label" for="customSwitch3"></label>
                </div>
            </li>
            <li class="list-group-item d-flex pt-1 pb-1">
              <span>Anyone can send me a message</span>
              <div class="custom-control custom-switch custom-switch-primary ml-auto">
                  <input type="checkbox" class="custom-control-input" id="customSwitch4" checked>
                  <label class="custom-control-label" for="customSwitch4"></label>
                </div>
            </li>
            <li class="list-group-item d-flex pt-1 pb-1">
              <span>Anyone can invite me to a group</span>
              <div class="custom-control custom-switch custom-switch-primary ml-auto">
                  <input type="checkbox" class="custom-control-input" id="customSwitch5">
                  <label class="custom-control-label" for="customSwitch5"></label>
                </div>
            </li>
            <li class="list-group-item d-flex pt-1 pb-1">
              <span>Update</span>
              <div class="custom-control custom-switch custom-switch-primary ml-auto">
                  <input type="checkbox" class="custom-control-input" id="customSwitch6" checked>
                  <label class="custom-control-label" for="customSwitch6"></label>
                </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  <!-- Notification-end -->
  <!-- Emails-begins -->
    <div class="settings-emails mb-2">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title pb-2">Emails</h4>
        </div>
        <div class="card-content">
          <ul class="list-group">
            <li class="list-group-item d-flex pt-1 pb-1">
              <span>Anyone can post a comment on my post</span>
              <div class="custom-control custom-switch custom-switch-primary ml-auto">
                  <input type="checkbox" class="custom-control-input" id="customSwitch7">
                  <label class="custom-control-label" for="customSwitch7"></label>
                </div>
            </li>
            <li class="list-group-item d-flex pt-1 pb-1">
              <span>Anyone can send me an email</span>
              <div class="custom-control custom-switch custom-switch-primary ml-auto">
                  <input type="checkbox" class="custom-control-input" id="customSwitch8" checked>
                  <label class="custom-control-label" for="customSwitch8"></label>
                </div>
            </li>
            <li class="list-group-item d-flex pt-1 pb-1">
              <span>Cras justo odio</span>
              <div class="custom-control custom-switch custom-switch-primary ml-auto">
                  <input type="checkbox" class="custom-control-input" id="customSwitch9">
                  <label class="custom-control-label" for="customSwitch9"></label>
                </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  <!-- Emails-end -->
  <!-- Security-begins -->
    <div class="settings-security">
        <div class="card collapse-icon accordion-icon-rotate">
          <div class="card-header">
            <h4 class="card-title">Security</h4>
          </div>
         <div class="card-body">
          <div class="card collapse-header">
            <div id="headingCollapse2" class="">
              <div class="lead collapse-title" data-toggle="collapse" role="button" data-target="#collapse2"
                aria-expanded="false" aria-controls="collapse1">
                <div class="change-password">
                <span>Change password</span>
              </div>
              </div>
            </div>
            </div>
            <div id="collapse2" role="tabpanel" aria-labelledby="headingCollapse1" class="collapse">
              <div class="card-content">
                    <form class="form form-vertical">
                      <div class="form-group">
                          <label>Old Password</label>
                          <div class="position-relative has-icon-left">
                            <input type="password" class="form-control" name="oldpassword" placeholder="Old Password">
                            <div class="form-control-position">
                              <i class="feather icon-at-sign"></i>
                            </div>
                          </div>
                      </div>
                      <div class="form-group">
                          <label>New Password</label>
                          <div class="position-relative has-icon-left">
                            <input type="password" class="form-control" name="newpassword" placeholder="New Password">
                            <div class="form-control-position">
                              <i class="feather icon-at-sign"></i>
                            </div>
                          </div>
                      </div>
                      <div class="form-group">
                          <label>New Password again</label>
                          <div class="position-relative has-icon-left">
                            <input type="password" class="form-control" name="confirm-password" placeholder="Confirm Password">
                              <div class="form-control-position">
                              <i class="feather icon-at-sign"></i>
                            </div>
                          </div>
                      </div>
                      <button type="button" class="btn btn-primary mr-1 mb-1">Update</button>
                    </form>
              </div>
            </div>
        <div class="delete-account border-top pt-1">
          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteForm">
            Delete Account ?
          </button>
          <div class="modal fade text-left" id="deleteForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title" id="myModalLabel33">Delete Account</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form action="#">
                  <div class="modal-body">
                    <h5>Are you sure to delete your account? </h5>
                    <button type="button" class="btn btn-danger mr-1 my-1" data-dismiss="modal">Yes</button>
                    <button type="button" class="btn btn-primary my-1" data-dismiss="modal">No</button>
                  </div>
                </form>
              </div>
            </div>
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
