<form action="{{ action('ShopController@update', $shop->id) }}" class="form" method="POST">
  @method('put')
  @csrf 
<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
  	<div class="modal-header">
		<h4 class="modal-title" id="modal-title">Edit Shop {{ $shop->name }}
		</h4>
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="modal-body">
    <div class="row">
      <div class="col-md-6">
        <label>Name</label>
        <input type="text" class="form-control" name="name" value="{{ $shop->name }}">
      </div>
      <div class="col-md-6">
        <label>Short Name</label>
        <input type="text" class="form-control" name="short_name" value="{{ $shop->short_name }}">
      </div>
    </div>
 
	</div>
    <div class="modal-footer">
          <button type="submit" class="btn btn-primary btn_save no-print"><i class="fa fa-save"></i> Save
          </button>
          <button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
        </div>
  </div>
</div>
</form>
<script src="{{ asset('js/scripts/forms-validation/form-modal.js') }}"></script>