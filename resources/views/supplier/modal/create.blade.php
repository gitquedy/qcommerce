<div class="modal-dialog modal-md" role="document">
	<form action="{{ route('supplier.addSupplierAjax') }}" id="add_customer_ajax" method="POST" class="form" enctype='multipart/form-data'>
    @method('POST')
		@csrf
  <div class="modal-content">
  	<div class="modal-header">
		<h4 class="modal-title" id="modal-title">Add Supplier/Biller
		</h4>
    <input type="hidden" name="select_id" value="{{$select_id}}" readonly>
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="modal-body">
      <div class="row">
          <div class="col-md-6 form-group">
              <lable>Company</lable>
              <input class="form-control" name="company">
          </div>
          <div class="col-md-6 form-group">
              <lable>Contact Person</lable>
              <input class="form-control" name="contact_person">
          </div>
          <div class="col-md-6 form-group">
              <lable>Mobile Number</lable>
              <input type="text" class="form-control" name="phone">
          </div>
          <div class="col-md-6 form-group">
              <lable>Email</lable>
              <input type="email" class="form-control" name="email">
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