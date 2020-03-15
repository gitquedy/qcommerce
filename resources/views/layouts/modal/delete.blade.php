<div class="modal-dialog padding-t-10" role="document">
	<form action="{{ $action }}" method="POST" class="form" enctype='multipart/form-data'>
    @method('DELETE')
		@csrf
  <div class="modal-content">
  	<div class="modal-header">
		<button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="modalTitle">Delete {{ ucfirst($title) }}
		</h4>
	</div>
	<div class="modal-body">
    Are you sure to delete this {{ $title }}?
	</div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-danger no-print btn_save"><i class="fa fa-trash"></i> Delete
      </button>
      </form>
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>

<script src="{{ asset('js/forms/form-modal.js') }}"></script>