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
        <div class="form-group">
          <label>Name</label>
          <input type="text" class="form-control" name="name" value="{{ $shop->name }}">
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label>Short Name</label>
          <input type="text" class="form-control" name="short_name" value="{{ $shop->short_name }}">
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
            <label>Warehouse</label>
            <div class="position-relative has-icon-left">
              <select name="warehouse_id" id="select_warehouse" class="form-control select2 update_select" placeholder="Select Warehouse">
                @forelse($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" @if($shop->warehouse_id == $warehouse->id) selected @endif>{{ $warehouse->name }}</option>
                @empty
                <option value="" disabled="">Please Add Warehouse</option>
                @endforelse
              </select>
              <div class="form-control-position"> 
                <i class="feather icon-box"></i>
              </div>
            </div>
        </div>
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
<script>
   $(document).ready(function(){
        $(".select2").select2({
          dropdownAutoWidth: true,
          width: '100%'
        });
    }); 
</script>