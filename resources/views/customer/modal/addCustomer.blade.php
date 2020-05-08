<div class="modal-dialog modal-md" role="document">
	<form action="{{ route('customer.addCustomerAjax') }}" id="add_customer_ajax" method="POST" class="form" enctype='multipart/form-data'>
    @method('POST')
		@csrf
  <div class="modal-content">
  	<div class="modal-header">
		<h4 class="modal-title" id="modal-title">Add Customer
		</h4>
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="modal-body">
      <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>First Name</label>
                <div class="position-relative has-icon-left">
                  <input type="text" class="form-control" name="first_name" placeholder="First Name">
                  <div class="form-control-position">
                    <i class="feather icon-user"></i>
                  </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Last Name</label>
                <div class="position-relative has-icon-left">
                  <input type="text" class="form-control" name="last_name" placeholder="Last Name">
                  <div class="form-control-position"> 
                    <i class="feather icon-user"></i>
                  </div>
                </div>
            </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Email</label>
                <div class="position-relative has-icon-left">
                  <input type="text" class="form-control" name="email" placeholder="Email Address">
                  <div class="form-control-position"> 
                    <i class="feather icon-mail"></i>
                  </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Mobile</label>
                <div class="position-relative has-icon-left">
                  <input type="text" class="form-control" name="phone" placeholder="Mobile Number">
                  <div class="form-control-position"> 
                    <i class="feather icon-phone"></i>
                  </div>
                </div>  
            </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Price Group</label>
                <div class="position-relative has-icon-left">
                  <select name="price_group" id="price_group" class="form-control select2" placeholder="Price Group">
                      <option value="0">Default</option>
                      @foreach($price_group as $pg)
                      <option value="{{$pg->id}}">{{$pg->name}}</option>
                      @endforeach
                  </select>
                  <div class="form-control-position"> 
                    <i class="feather icon-users"></i>
                  </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Address</label>
                <div class="position-relative has-icon-left">
                  <input type="text" class="form-control" name="address" placeholder="Address">
                  <div class="form-control-position"> 
                    <i class="feather icon-home"></i>
                  </div>
                </div>  
            </div>
        </div>
      </div>
	</div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary no-print"> Submit </button>
      </form>
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>

<script src="{{ asset('js/scripts/forms-validation/form-modal.js') }}"></script>
<script>
   $(document).ready(function(){
        $(".select2").select2({
          dropdownAutoWidth: true,
          width: '100%'
        });
    }); 
</script>