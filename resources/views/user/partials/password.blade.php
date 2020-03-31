@inject('request', 'Illuminate\Http\Request')
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
                <form class="form form-vertical" action="{{ action('UserController@updatePassword') }}" method="post" enctype="multipart/form-data">
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
                <input type="submit" name="saveandadd" class="btn btn-primary mt-1 mb-1 btn_save" value="Update">
             </form>
          </div>
        </div>
        </div>
       </div>
    </div>
</section>