@inject('request', 'Illuminate\Http\Request')
<section>
    <div class="settings-account">
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
                  <img  class="rounded-circle mr-2" src="{{ asset('images/profile/profile-picture/'.$request->user()->picture) }}" alt="Generic placeholder image"
                    height="64" width="64" />
                </a>
                <div class="media-body mt-1">
                 <h5 class="media-heading mb-0">{{$request->user()->formatName()}}</h5>
                  <a class="text-muted" href="#"><small>{{ $request->user()->email }}</small></a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div role="tabpanel" aria-labelledby="headingCollapse1" class="collapse show mt-2">
          <div class="card-content">
                <form class="form form-vertical" action="{{ action('UserController@updateProfile') }}" method="post" enctype="multipart/form-data">
                  @csrf
                  <div class="form-group">
                    <label>First Name</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" class="form-control" name="first_name" value="{{ $request->user()->first_name }}" placeholder="First Name">
                      <div class="form-control-position"> 
                        <i class="feather icon-user"></i>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Last Name</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" class="form-control" name="last_name" value="{{ $request->user()->last_name }}" placeholder="Last Name">
                      <div class="form-control-position"> 
                        <i class="feather icon-user"></i>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Email</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" class="form-control" name="email" value="{{ $request->user()->email }}" placeholder="Email">
                      <div class="form-control-position">
                        <i class="feather icon-mail"></i>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Phone</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" class="form-control" name="phone" value="{{ $request->user()->phone }}" placeholder="Phone Number">
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
                <input type="submit" name="save" class="btn btn-primary mt-1 mb-1 btn_save" value="Update">
             </form>
          </div>
        </div>
        </div>
       </div>
    </div>
</section>

