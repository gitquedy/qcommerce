 @inject('request', 'Illuminate\Http\Request')
<div class="modal-dialog modal-lg" role="document">
	<form action="{{ action('CrudController@update', [$crud->id]) }}" method="POST" class="form" enctype='multipart/form-data'>
    @method('PUT')
		@csrf
  <div class="modal-content">
  	<div class="modal-header">
      <h4 class="modal-title" id="modal-title">Edit Crud
    </h4>
		<button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
<div class="modal-body">
       <div class="row">
          <div class="col-sm-4">
            <label>Name:*</label>
            <input type="text" class="form-control" name="name" value="{{ $crud->name }}">
          </div>
        </div>    
  </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary no-print btn_save"><i class="fa fa-save"></i> Update
      </button>
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
    </div>
  </div>
  </form>
</div>
<script src="{{ asset('js/scripts/forms-validation/form-modal.js') }}"></script>