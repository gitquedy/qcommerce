<div class="modal-dialog modal-md" role="document">
	<form action="{{ route('warehouse.addWarehouseAjax') }}" id="add_warehouse_ajax" method="POST" class="form" enctype='multipart/form-data'>
    @method('POST')
		@csrf
    <input type="hidden" name="select_id" value="{{$select_id}}" readonly>
  <div class="modal-content">
  	<div class="modal-header">
		<h4 class="modal-title" id="modal-title">Add Warehouse
		</h4>
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="modal-body">
      <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Code</label>
                <div class="position-relative has-icon-left">
                  <input type="text" class="form-control" name="code" placeholder="Code">
                  <div class="form-control-position"> 
                    <i class="feather icon-box"></i>
                  </div>
                </div>
            </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Name</label>
                <div class="position-relative has-icon-left">
                  <input type="text" class="form-control" name="name" placeholder="Name">
                  <div class="form-control-position"> 
                    <i class="feather icon-box"></i>
                  </div>
                </div>
            </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
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
      <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Phone</label>
                <div class="position-relative has-icon-left">
                  <input type="text" class="form-control" name="phone" placeholder="Phone Number">
                  <div class="form-control-position"> 
                    <i class="feather icon-phone"></i>
                  </div>
                </div>  
            </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Email</label>
                <div class="position-relative has-icon-left">
                  <input type="text" class="form-control" name="email" placeholder="Email">
                  <div class="form-control-position"> 
                    <i class="feather icon-mail"></i>
                  </div>
                </div>  
            </div>
        </div>
      </div>
	</div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary no-print btn_save"> Submit </button>
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