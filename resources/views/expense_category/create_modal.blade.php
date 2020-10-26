<div class="modal-dialog modal-md" role="document">
	<form action="{{ action('ExpenseCategoryController@storeModal') }}" id="add_warehouse_ajax" method="POST" class="form" enctype='multipart/form-data'>
    @method('POST')
		@csrf
    <input type="hidden" name="select_id" value="{{$select_id}}" readonly>
  <div class="modal-content">
  	<div class="modal-header">
		<h4 class="modal-title" id="modal-title">Add Expense Category
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
	</div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary no-print btn_save"> Submit </button>
      </form>
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>

<script src="{{ asset('js/scripts/forms-validation/form-modal.js') }}"></script>