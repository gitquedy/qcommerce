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
<script>
   $(document).ready(function(){
        $(".select2").select2({
          dropdownAutoWidth: true,
          width: '100%'
        });

       $(document).on('submit',"#add_customer_ajax",function(e) {
          e.preventDefault();
           $('.btn_save').prop('disabled', true);
            $.ajax({
              url : $(this).attr('action'),
              type : 'POST',
              data: new FormData(this),
              processData: false,
              contentType: false,
              success: function(result){
                if(result.success == true){
                  toastr.success(result.msg, '' , {positionClass : "toast-top-center", escapeHTML: false});
                  $('.view_modal').modal('toggle');
                  $('#select_customer').append('<option value="'+result.customer.id+'">'+result.customer.last_name+', '+result.customer.first_name+'</option>').val(result.customer.id).trigger('change');
                }else{
                  if(result.msg){
                    toastr.error(result.msg);
                  }
                   $('.error').remove();
                      $.each(result.error, function(index, val){
                      $('[name="'+ index +'"]').after('<label class="text-danger error">' + val + '</label>');
                      });
                }
                $('.btn_save').prop('disabled', false);
                 },
                error: function(jqXhr, json, errorThrown){
                  console.log(jqXhr);
                  console.log(json);
                  console.log(errorThrown);
                  $('.btn_save').prop('disabled', false);
                }
              });
       });

    }); 
</script>