<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
  	<div class="modal-header">
		<h4 class="modal-title" id="modal-title">Packing List for {{ Carbon\Carbon::now()->format('d M Y H:i') }}
		</h4>
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="modal-body">
    @foreach($orders['shopee'] as $order)
      <section class="card">
        <div class="card-content">
          <div class="card-body">
            <h4 class="card-title">Order Details</h4>
            <div class="row">
              <div class="col-12">
                <table class="table text-center">
                  <thead>
                    <tr>
                      <th>{{ $order['shop_name'] }}</th>
                      <th>{{ Carbon\Carbon::createFromTimestamp($order['create_time'])->toDateTimeString() }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>{{ $order['ordersn'] }}</td>
                      <td>{{ $order['recipient_address']['name'] }}</td>
                      <td>Platform: Shopee</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      <div class="card-content">
        <div class="card-body">
          <h4 class="card-title">Items</h4>
          <div class="row">
            <div class="col-12">
              <table class="table text-center">
                <thead>
                  <tr>
                    <th>Check</th>
                    <th>Quantity</th>
                    <th >Name</th>
                  </tr>
                </thead>
                <tbody id="items_list">
                  @foreach($order['items'] as $item)
                    <tr>
                      <td><i class="fa fa-circle-o"></i></td>
                      <td>{{ $item['variation_quantity_purchased'] }}</td>
                      <td>{{ $item['item_name'] }}({{ $item['item_sku'] }})</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
    <hr style="display: block; color:black !important; background-color:black !important;" />
    @endforeach

    @foreach($orders['lazada'] as $order)
      <section class="card">
        <div class="card-content">
          <div class="card-body">
            <h4 class="card-title">Order Details</h4>
            <div class="row">
              <div class="col-12">
                <table class="table text-center">
                  <thead>
                    <tr>
                      <th>{{ $order['shop']['name'] }}</th>
                      <th>{{ App\Utilities::format_date($order['created_at'], 'M. d,Y H:i A') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>{{ $order['id'] }}</td>
                      <td>{{ $order['customer_first_name'] }} {{ $order['customer_last_name'] }} </td>
                      <td>Platform: Lazada</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      <div class="card-content">
        <div class="card-body">
          <h4 class="card-title">Items</h4>
          <div class="row">
            <div class="col-12">
              <table class="table text-center">
                <thead>
                  <tr>
                    <th>Check</th>
                    <th>Quantity</th>
                    <th >Name</th>
                  </tr>
                </thead>
                <tbody id="items_list">
                  @foreach($order['items'] as $item)
                    <tr>
                      <td><i class="fa fa-circle-o"></i></td>
                      <td>{{ $item['qty'] }}</td>
                      <td>{{ $item['name'] }}({{ $item['sku'] }})</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
    <hr style="display: block; color:black !important; background-color:black !important;" />
    @endforeach
	</div>
    <div class="modal-footer">
      <button type="button" class="btn btn-primary no-print" aria-label="Print" 
      onclick="$(this).closest('div.modal-content').printThis();"><i class="fa fa-print"></i> Print
      </button>
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>