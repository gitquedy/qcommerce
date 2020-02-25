 @inject('request', 'Illuminate\Http\Request')
<div class="modal-dialog modal-lg" role="document">
	<form action="{{ action('OrderController@cancelSubmit', [$order->id]) }}" method="POST" class="form" enctype='multipart/form-data'>
    @method('POST')
		@csrf
    <input type="hidden" name="ordersn" value="{{ $order->ordersn }}">
  <div class="modal-content">
  	<div class="modal-header">
      <h4 class="modal-title" id="modal-title">Cancel {{ $order->ordersn}}
    </h4>
		<button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
<div class="modal-body">
       <div class="row">
        <div class="col-sm-4"></div>
          <div class="col-sm-4">
            <label>Name:*</label>
            <select class="select2 form-control" name="reason">
              <!-- <option value="OUT_OF_STOCK">Out of Stock</option> -->
              <option value="CUSTOMER_REQUEST">Customer Request</option>
              <option value="COD_NOT_SUPPORTED">COD not Supported</option>
              <option value="UNDELIVERABLE_AREA">Undeliverable Area</option>
            </select>
          </div>
        </div>    
  </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary no-print btn_save"><i class="fa fa-save"></i> Cancel This Order
      </button>
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
    </div>
  </div>
  </form>
</div>
<script src="{{ asset('js/scripts/forms-validation/form-modal.js') }}"></script>