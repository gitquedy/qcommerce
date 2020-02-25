<div class="modal-dialog modal-lg" role="document">
  <form action="{{ action('OrderController@pickupDetailsPostShopee', [$order->id]) }}" method="POST" class="form" enctype='multipart/form-data'>
    @method('POST')
    @csrf
  <div class="modal-content">
    <div class="modal-header">
    <h4 class="modal-title" id="modal-title">Wait for the delivery service to pick up
    </h4>
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  </div>
  <div class="modal-body">
      <div class="row mg-b-20">
            <div class="col-sm-12">
                <ul class="nav nav-line" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link tx-bold active"  data-toggle="tab" href="#shop-17175" role="tab" aria-controls="home" aria-selected="true">Shopee
                            <label class="d-inline rounded-10 pd-x-10 pd-y-4 tx-sembold bg-lightblue tx-primary">
                                {{ $order->ordersn }}
                            </label>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="tab-content mg-b-20">
            <div class="tab-pane fade show  active " id="shop-17175" role="tabpanel" aria-labelledby="tab-shop-17175">
                <div class="table-responsive">
                    <table class="table table-borderless mg-b-0 ">
                        <thead>
                            <tr>
                                <th class="text-left">Items Quantity</th>
                                <th class="text-left">Warehouse</th>
                                <th class="text-left">Time Slot</th>
                                <th class="text-left">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="tx-medium text-left">
                                <div class="d-inline-block rounded-10 pd-x-10 pd-y-4 mg-t-5 tx-normal bg-lightblue tx-primary">
                                  {{ $order->items_count }}
                                </div>
                            </td>
                            <td class="text-left">
                                <select class="form-control form-control" name="address_id" id="address">
                                    @foreach($info['pickup']['address_list'] as $address)
                                      <option value="{{ $address['address_id'] }}" data-detail="{{ $address['address'] }} {{ $address['district'] }} {{ $address['city'] }}"> Pickup Address - {{ $loop->iteration }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="text-left wd-20p">
                                <select class="form-control form-controltime-slot select-timeslot" name="pickup_time_id">
                                    @foreach($info['pickup']['address_list'][0]['time_slot_list'] as $time)
                                      <option value="{{ $time['pickup_time_id'] }}">{{ $time['date'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="tx-medium text-left wd-30p text-address">
                              ------------------------------------------------------------------------------
                            </td>
                          </tr>
                        </tbody>
                    </table>
                </div>
            </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary no-print btn_save"><i class="fa fa-save"></i> Confirm
      </button>
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
    </div>
  </div>
  </form>
</div>
<script src="{{ asset('js/scripts/forms-validation/form-modal.js') }}"></script>

<script type="text/javascript">
$(".select2").select2({
  dropdownAutoWidth: true,
  width: '100%'
});
$(document).ready(function(){
  $('#address').change(function(){
    var details = $('#address').find(":selected").data('detail');
    $('.text-address').html(details);
  });
});
</script>