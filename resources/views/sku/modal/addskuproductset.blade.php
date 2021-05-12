<div class="modal-dialog modal-md" role="document">
	<form action="{{ route('sku.addproductset') }}" id="add_sku_product_set" method="POST" class="form" enctype='multipart/form-data'>
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
      SKU:
    </div>
    <div class="form-group">
      <select name="sku" id="ap_sku" class="select2 form-control ap_reset">
        <option value="" disabled hidden selected></option>
          @foreach($all_skus as $sku)
            <option value="{{ $sku->id }}">{{ $sku->name . ' (' . $sku->code . ')' }}</option>
          @endforeach
      </select>
    </div>
    <div class="text-bold-600 font-medium-2">
      Quantity:
    </div>
    <div class="form-group">
      <input type="number" class="form-control" name="quantity">
    </div>
  </div>
	<div>
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

      //  $(".view_modal").on('change', '#ap_sku', function() {
      //     $("#ap_product").html('').trigger('change');
      //     $.ajax({
      //       url :  "{{ route('sku.ajaxlistsku') }}",
      //       type: "POST",
      //       dataType: "JSON",
      //       data: 'shop_id='+$(this).val(),
      //       success: function (response) {
      //         if(response) {
      //           $("#ap_product").html('').trigger('change');
      //           $.each(response, function(k, prod) {
      //               var disabled = (prod['seller_sku_id'])?'disabled':'';
      //               var newOption = '<option value="'+prod['id']+'" '+disabled+'>'+prod['name']+'</option>';
      //               $(".modal #ap_product").append(newOption).trigger('change');
      //           });
      //         }
      //       }
      //     });
      //  });

       $("#add_sku_product_set").submit(function(e) {
          e.preventDefault();
          $.ajax({
            url :  $(this).attr('action'),
            type: "POST",
            dataType: "JSON",
            data: $(this).serialize(),
            success: function (response) {
              if(response) {
                $('.view_modal').html('').modal('toggle');
                location.reload();
              }
            }
          });
       });

    }); 
</script>