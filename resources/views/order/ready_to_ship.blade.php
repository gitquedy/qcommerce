 @inject('request', 'Illuminate\Http\Request')
 <style type="text/css">
   .opacity-disable{
    opacity: 0;
   }
   .wd-40p {
    width: 40%;
    }
    .pd-b-45 {
        padding-bottom: 45px;
    }
    .pd-t-30 {
        padding-top: 30px;
    }
    .ht-100p {
    height: 100%;
    }

    .shadow-sm {
        box-shadow: 0 .125rem .25rem rgba(0,28,68,.075)!important;
    }
 </style>
<div class="modal-dialog modal-lg" role="document">
    <input type="hidden" name="ordersn" value="{{ $order->ordersn }}">
  <div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title" id="modal-title">Delivery Method
    </h4>
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  </div>
<div class="modal-body">
       <div class="row mg-b-20">
                    <div class="col-12">
                        <h6>You can send to the nearest post office</h6>
                    </div>
                    <div class="col-6 cursor-pointer">
                        <div class="selectOption card ht-100p d-flex align-items-center shadow-sm">
                            <img class="card-img-top wd-40p pd-t-30 pd-b-45" src="{{ url('images/shop/orders/pickup1.svg') }}" alt="Responsive image">
                            <div class="card-body">
                                <h4 class="card-title tx-primary text-center">I will bring it to the post office by myself</h4>
                                <p class="card-text text-center mg-b-0">
                                    You can send to the nearest post office
                                </p>
                                <input type="radio" class="opacity-disable" name="fulfillment" value="fulfillment_by_seller">
                            </div>
                        </div>
                    </div>
                    <div class="col-6 cursor-pointer">
                        <div class="selectOption card ht-100p border-primary d-flex align-items-center shadow-sm">
                            <img class="card-img-top wd-40p pd-t-30 pd-b-45" src="{{ url('images/shop/orders/pickup2.svg') }}" alt="Responsive image">
                            <div class="card-body">
                                <h4 class="card-title tx-primary text-center">Delivered by delivery service</h4>
                                <p class="card-text text-center mg-b-0">
                                    The delivery service will pick up the goods you have confirmed
                                </p>
                                <input type="radio" checked="" class="opacity-disable" name="fulfillment" value="fulfillment_by_platform">
                            </div>
                        </div>
                    </div>
              </div>   
              <a href="#" id="pickupDetailsShopee" data-href="{{ action('OrderController@pickupDetailsShopee', $order->id) }}" class="modal_button"></a>
  </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary no-print btn_save"><i class="fa fa-check"></i> Confirm
      </button>
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    $(document).on('click', '.selectOption' , function(){
      $('.selectOption').removeClass('border-primary');
      $('input[name="fulfillment"]').prop("checked", false);
      $(this).addClass('border-primary');
      $(this).find('input[name="fulfillment"]').prop("checked", true);
    });
    $(document).on('click', '.btn_save', function(){
      $(this).prop('disabled', true);
      var radio = $('input[name="fulfillment"]:checked').val();
      if(radio == 'fulfillment_by_seller'){
        var url = "order/readyToShipDropOff/{{ $order->id }}";
        window.location.replace(url);
      }else if (radio == 'fulfillment_by_platform'){
        $('#pickupDetailsShopee').click();
      }else{
      }
      $(this).prop('disabled', false);
    });
  });
</script>