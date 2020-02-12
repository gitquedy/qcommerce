<div class="modal-dialog modal-md" role="document">
	<form action="{{ route('sku.addproduct') }}" id="add_sku_product" method="POST" class="form" enctype='multipart/form-data'>
    @method('POST')
		@csrf
  <div class="modal-content">
  	<div class="modal-header">
		<h4 class="modal-title" id="modal-title">Link Product to {{ ucfirst($title) }}
		</h4>
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="modal-body">
      <input type="hidden" id="sku_id" name="sku_id" value="{{$id}}">
      <div class="text-bold-600 font-medium-2">
        Shop:
      </div>
      <div class="form-group">
        <select name="shop" id="ap_shop" class="select2 form-control ap_reset">
          <option value="" disabled hidden selected></option>
          @foreach($all_shops as $shop)
            <option value="{{ $shop->id }}">{{ $shop->name . ' (' . $shop->short_name . ')' }}</option>
          @endforeach
        </select>
      </div>
      <div class="text-bold-600 font-medium-2">
        Product:
      </div>
      <div class="form-group">
        <select name="product" id="ap_product" class="select2 form-control ap_reset">
        </select>
      </div>
	</div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary no-print btn_save"><i class="fa fa-link"></i> Link
      </button>
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

       $(".view_modal").one('change', '#ap_shop', function() {
          $("#ap_product").html('').trigger('change');
          $.ajax({
            url :  "{{ route('product.ajaxlistproduct') }}",
            type: "POST",
            dataType: "JSON",
            data: 'shop_id='+$(this).val(),
            success: function (response) {
              if(response) {
                $.each(response, function(k, prod) {
                    var disabled = (prod['seller_sku_id'])?'disabled':'';
                    var newOption = '<option value="'+prod['id']+'" '+disabled+'>'+prod['name']+'</option>';
                    $(".modal #ap_product").append(newOption).trigger('change');
                });
              }
            }
          });
       });

       $("#add_sku_product").submit(function(e) {
          e.preventDefault();
          $.ajax({
            url :  $(this).attr('action'),
            type: "POST",
            dataType: "JSON",
            data: $(this).serialize(),
            success: function (response) {
              if(response) {
                $('.view_modal').html('').modal('toggle');
              }
            }
          });
       });

    }); 
</script>
<script src="{{ asset('js/scripts/forms-validation/form-modal.js') }}"></script>