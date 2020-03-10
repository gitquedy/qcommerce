<div class="modal-dialog modal-lg" role="document">

  <div class="modal-content">
  	<div class="modal-header">
		<h4 class="modal-title" id="modal-title">Duplicate Products
		</h4>
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="modal-body">
      <section class="card">
          <div class="card-content">
            <div class="card-body">
              <h4 class="card-title"></h4>
              <div class="row">
                <div class="col-12">
                  <fieldset class="form-group position-relative has-icon-left">
                      <input type="text" class="form-control round" id="searchProduct" placeholder="Enter Sku/Item ID" value="">
                      <div class="form-control-position">
                          <i class="feather icon-search px-1"></i>
                      </div>
                  </fieldset>
                </div>
              </div>
            </div>
          </div>
      </section>

<form action="{{ action('ProductController@duplicateProudcts') }}" class="form" method="POST">
  @csrf 
  <input type="hidden" name="site" value="{{ $products->first()->site }}">
      <section class="card">
        <div class="card-content">
          <div class="card-body">
            <h4 class="card-title">Product Details</h4>
            <div class="row">
              <div class="col-12">
                <table class="table text-center" id="product_table">
                  <thead>
                    <tr>
                      <th>Item ID</th>
                      <th>Sku</th>
                      <th>Name</th>
                      <th><i class="fa fa-trash"></i></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($products as $product)
                      <tr>
                        <td><input type="hidden" name="product[{{$product->id}}]" class="item_ids" value="{{ $product->id }}">{!! $product->getImgAndIdDisplay() !!}</td>
                        <td>{{ $product->SellerSku }}</td>
                        <td>{{ $product->name }}</td>
                        <td><button class="btn btn-danger remove_row"><i class="fa fa-trash"></i></button></td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
    </section>
    

    <section class="card">
          <div class="card-content">
            <div class="card-body">
              <div class="row">
                  <div class="col-md-6">
                    <label class="card-title">Shop</label>
                    <select class="form-control s2" name="shop_id" id="shop_id">
                      <option hidden selected disabled></option>
                      @foreach($shops as $shop)
                        <option value="{{ $shop->id }}">{!! $shop->getImgSiteDisplayWithFullName() !!}</option>
                      @endforeach
                    </select>
                </div>
                @if($products->first()->site == 'shopee')
                  <div class="col-md-6">
                     <label class="card-title">Logistics</label>
                     <select class="form-control s2" multiple="multiple" name="logistic_ids[]" id="logistic_ids">
                     </select>
                  </div>
                @endif
              </div>
            </div>
          </div>
      </section>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary btn_save no-print"><i class="fa fa-save"></i> Save
          </button>
          <button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
        </div>
       </form>

	</div>
    
  </div>
</div>


<script src="{{ asset('js/scripts/forms-validation/form-modal.js') }}"></script>
<script type="text/javascript">
  $(document).ready(function(){
      $(".s2").select2({
        dropdownAutoWidth: true,
        width: '100%'
      });
      @if($products->first()->site == 'shopee')
      $(document).on('change', '#shop_id', function(){
        var url = "{{ action('ShopController@shopeeGetLogistics', ':id') }}";
        url = url.replace(':id', $(this).val());
        $.ajax({
            method: "GET",
            url: url,
            success: function success(result) {
              $('#logistic_ids').find('option').remove()
              $.each(result.logistics, function (i, item) {
                $('#logistic_ids').append($('<option>', { 
                    value: item.logistic_id,
                    text : item.logistic_name,
                    selected: item.preferred
                  }));
              });
            },
            error: function error(jqXhr, json, errorThrown) {
              console.log(jqXhr);
              console.log(json);
              console.log(errorThrown);
            }
          });
      })
      @endif
  }); 
  // $('#searchProduct').keypress(function (e) {
    $('#searchProduct').keyup(function (e) {
       if(e.keyCode === 13){
         if($('.btn_save').prop('disabled') == true){
            return false;
          }
          $('.btn_save').prop('disabled', true);
          var selected_ids = [];
           $(".item_ids").each(function(){
                  selected_ids.push($(this).val());
           });
            $.ajax({
            method: "GET",
            url: "{{ action('ProductController@searchProduct') }}?site={{ $products->first()->site }}&search=" + $(this).val(),
            success: function success(result) {
              $('.btn_save').prop('disabled', false);
              if(result.success == true){
                toastr.success(result.msg);
                var id = result.product.id
                var duplicate = selected_ids.includes(id.toString());
                if(! duplicate){
                  $('#product_table tr:last').after(result.html);
                }
              }else{
                toastr.error(result.msg);
              }
              $('#searchProduct').val("");
              $("#searchProduct").focus();
            },
            error: function error(jqXhr, json, errorThrown) {
              console.log(jqXhr);
              console.log(json);
              console.log(errorThrown);
            }
          });
       }   
  });
  $(document).on('click', '.remove_row', function(){
    $(this).closest('tr').remove();
  });
// });


</script>

