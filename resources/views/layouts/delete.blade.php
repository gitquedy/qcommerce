<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
	<form action="{{ $action }}" method="POST" class="form" enctype='multipart/form-data'>
    @method('DELETE')
		@csrf
  <div class="modal-content">
  	<div class="modal-header">
		<h4 class="modal-title" id="modal-title">Delete {{ ucfirst($title) }}
		</h4>
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="modal-body">
    <h5>Are you sure to delete this {{ $title }}?</h5>
     <br> <small>Note: This action is irreversible.</small>
	</div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-danger no-print btn_save"><i class="fa fa-trash"></i> Delete
      </button>
      </form>
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>

<script src="{{ asset('js/scripts/forms-validation/form-modal.js') }}"></script>