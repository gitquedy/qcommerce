@inject('request', 'Illuminate\Http\Request')
@if(isset($request->user()->business->company))
<section>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="settings-account">
     <div class="card user-form">
        <div class="card-header">
          <h4 class="card-title">Company Details</h4>
        </div>
        <div class="card-body">
        <div class="collapse-header">
          <div id="headingCollapse1">
            <div class="lead collapse-title">
              <div class="media">
                <a class="media-left" href="#">
                  <img  class="rounded-circle mr-2" src="{{ asset('images/profile/company-logo/'.$request->user()->business->company->logo) }}" alt="Generic placeholder image"
                    height="64" width="64" />
                </a>
                <div class="media-body mt-1">
                 <h5 class="media-heading mb-0">{{$request->user()->business->company->name}}</h5>
                  <!-- <a class="text-muted" href="#"><small>{{ $request->user()->email }}</small></a> -->
                </div>
              </div>
            </div>
          </div>
        </div>
        <div role="tabpanel" aria-labelledby="headingCollapse1" class="collapse show mt-2">
          <div class="card-content">
                <form class="form form-vertical" action="{{ action('UserController@updateCompany') }}" method="post" enctype="multipart/form-data">
                  @csrf
                  <div class="form-group">
                    <label>Company Name</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" class="form-control" name="name" value="{{ $request->user()->business->company->name }}" placeholder="Company Name">
                      <div class="form-control-position"> 
                        <i class="feather icon-user"></i>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Address</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" class="form-control" name="address" value="{{ $request->user()->business->company->address }}" placeholder="Address">
                      <div class="form-control-position"> 
                        <i class="feather icon-user"></i>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>VAT/TIN No.</label>
                    <div class="position-relative has-icon-left">
                      <input type="number" class="form-control" name="vat_tin_no" value="{{ $request->user()->business->company->vat_tin_no }}" placeholder="VAT/TIN No.">
                      <div class="form-control-position">
                        <i class="feather icon-mail"></i>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Phone</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" class="form-control" name="phone_no" value="{{ $request->user()->business->company->phone_no }}" placeholder="Phone Number">
                      <div class="form-control-position">
                        <i class="feather icon-phone"></i>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Company Logo</label>
                    <input  id="profilePicUpload" class="form-control" type="file" name="logo"/>
                  </div>
                  {{-- <div class="form-group">
                    <div action="#" class="dropzone dropzone-area">
                      <div class="dz-message">Upload Company Logo</div>
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
@else
<section>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="settings-account">
     <div class="card user-form">
        <div class="card-header">
          <h4 class="card-title">Company Details</h4>
        </div>
        <div class="card-body">
        <div class="collapse-header">
          <div id="headingCollapse1">
            <div class="lead collapse-title">
              <div class="media">
                <a class="media-left" href="#">
                  <!-- <img  class="rounded-circle mr-2" src="" alt="Generic placeholder image"
                    height="64" width="64" /> -->
                </a>
                <div class="media-body mt-1">
                 <h5 class="media-heading mb-0"></h5>
                  <!-- <a class="text-muted" href="#"><small></small></a> -->
                </div>
              </div>
            </div>
          </div>
        </div>
        <div role="tabpanel" aria-labelledby="headingCollapse1" class="collapse show mt-2">
          <div class="card-content">
                <form class="form form-vertical" action="{{ action('UserController@createCompany') }}" method="post" enctype="multipart/form-data">
                  @csrf
                  <div class="form-group">
                    <label>Company Name</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" class="form-control" name="name" placeholder="Company Name">
                      <div class="form-control-position"> 
                        <i class="feather icon-user"></i>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Address</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" class="form-control" name="address" placeholder="Address">
                      <div class="form-control-position"> 
                        <i class="feather icon-user"></i>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>VAT/TIN No.</label>
                    <div class="position-relative has-icon-left">
                      <input type="number" class="form-control" name="vat_tin_no" placeholder="VAT/TIN No.">
                      <div class="form-control-position">
                        <i class="feather icon-mail"></i>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Phone</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" class="form-control" name="phone_no" placeholder="Phone Number">
                      <div class="form-control-position">
                        <i class="feather icon-phone"></i>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Company Logo</label>
                    <input  id="profilePicUpload" class="form-control" type="file" name="logo"/>
                  </div>
                  {{-- <div class="form-group">
                    <div action="#" class="dropzone dropzone-area">
                      <div class="dz-message">Upload Company Logo</div>
                    </div>
                  </div> --}}
                <input type="submit" name="save" class="btn btn-primary mt-1 mb-1 btn_save" value="Save">
             </form>
          </div>
        </div>
        </div>
       </div>
    </div>
</section>
@endif