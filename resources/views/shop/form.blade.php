@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Connect Shop')

@section('content')
<!-- // Basic Floating Label Form section start -->
<section id="floating-label-layouts" class="container justify-content-md-center">
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
                          <input type="hidden" name="shop_id" value="{{ $request->get('shop_id') }}">
                          <input type="hidden" name="shop" value="{{ $request->get('shop') }}">
                          <input type="hidden" name="hmac" value="{{ $request->get('hmac') }}">
                          <input type="hidden" name="timestamp" value="{{ $request->get('timestamp') }}">
                          <div class="form-body">
                              <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="data-name">Shop Name</label>
                                            <input type="text" class="form-control" name="name" placeholder="Shop Name">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="data-name">Short Name</label>
                                            <input type="text" class="form-control" name="short_name" placeholder="Short Name">
                                        </div>
                                    </div>
                                </div>
                                  <div class="row">
                                    <div class="col-6">
                                      <div class="form-group">
                                          <label>Warehouse</label>
                                          <select name="warehouse_id" id="select_warehouse" class="form-control select2" placeholder="Select Warehouse">
                                            <option value="" disabled selected></option>
                                            <option value="add_new">Add New Warehouse</option>
                                            @forelse($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                            @empty
                                            <option value="" disabled="">Please Add Warehouse</option>
                                            @endforelse
                                          </select>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="row">
                                    <div class="col-12">
                                      <fieldset>
                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                          <input type="checkbox" name="pro_status" value="1">
                                          <span class="vs-checkbox vs-checkbox-lg">
                                            <span class="vs-checkbox--check">
                                              <i class="vs-icon feather icon-check"></i>
                                            </span>
                                          </span>
                                          <span>Use password credentials to enable Qcommerce Pro’s advanced features including Seller Center, Chat management and automations.  <a href="#">Click here to learn how it works.</a></span>
                                        </div>
                                      </fieldset>
                                    </div>
                                  </div><br>

                                  <div class="hidden" id="activate-power-chat">
                                    <div class="row">
                                      <div class="col-12">
                                        <h5>Choose the way you use to login on your sales channel</h5>
                                        <ul class="list-unstyled mb-0">
                                          <li class="d-inline-block mr-2">
                                            <fieldset>
                                              <div class="vs-radio-con vs-radio-primary">
                                                <input type="radio" name="pro_authentication_type" value="account" checked>
                                                <span class="vs-radio vs-radio-lg">
                                                  <span class="vs-radio--border"></span>
                                                  <span class="vs-radio--circle"></span>
                                                </span>
                                                <span class="">Account</span>
                                              </div>
                                            </fieldset>
                                          </li>
                                          <li class="d-inline-block mr-2">
                                            <fieldset>
                                              <div class="vs-radio-con vs-radio-primary">
                                                <input type="radio" name="pro_authentication_type" value="phone_number">
                                                <span class="vs-radio vs-radio-lg">
                                                  <span class="vs-radio--border"></span>
                                                  <span class="vs-radio--circle"></span>
                                                </span>
                                                <span class="">Phone Number</span>
                                              </div>
                                            </fieldset>
                                          </li>
                                        </ul>
                                      </div>
                                    </div><br>
                                    <div class="row">
                                      <div class="col-6">
                                          <div class="form-group">
                                              <label for="data-name">Account</label>
                                              <input type="text" class="form-control" name="pro_username" placeholder="Account">
                                          </div>
                                      </div>
                                      <div class="col-6">
                                          <div class="form-group">
                                              <label for="data-name">Password</label>
                                              <input type="password" class="form-control" name="pro_password" placeholder="Password">
                                          </div>
                                      </div>
                                  </div>
                                  <div class="row">
                                   <div class="col-12">
                                     <h5><i class="vs-icon feather icon-info"></i> Your credentials are 100% secured with our <a href="#">data security standard</a> and <a href="#">privacy protection policy.</a></h5>
                                   </div>
                                  </div>    
                                </div>
                                  <br>
                                  <div class="row">
                                    <div class="col-6">
                                     <div class="col-12">
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

<!-- Use password credentials to enable PowerSell Pro’s advanced features including Seller Center, Chat management and automations. Click here to learn how it works . -->
<!-- // Basic Floating Label Form section end -->
<!--  Your credentials are 100% secured with our data security standard and privacy protection policy. -->
@endsection
@section('vendor-script')
  <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
@endsection
@section('myscript')
<script>
  $(document).ready(function() {
        $(".select2").select2({
            dropdownAutoWidth: true,
            width: '100%'
        });
        $('select[name=warehouse_id]').on('change', function() {
            var selected = $(this).find('option:selected').val();
            if(selected == 'add_new') {
              $.ajax({
                url :  "{{ route('warehouse.addWarehouseModal') }}",
                type: "POST",
                success: function (response) {
                  if(response) {
                    $(".view_modal").html(response).modal('show');
                  }
                }
              });
              $(this).val('').trigger('change');
            } 
        });
        $('[name="pro_status"]').on('change', function(){
          if($('[name="pro_status"]').prop('checked') === true){
            $("#activate-power-chat").removeClass("hidden");
          }else{
            $("#activate-power-chat").addClass("hidden");
          }
        });
  });
</script>
@endsection
