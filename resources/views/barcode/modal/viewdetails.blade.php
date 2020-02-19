@php
@endphp
<style>
    .product_image{
        width:80px;
        height:auto;
    }
</style>
<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
  	<div class="modal-header">
		<h4 class="modal-title" id="modal-title">Order Details
		</h4>
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="modal-body">
      <section class="card">
        <div class="card-content">
          <div class="card-body">
            <h4 class="card-title">Customer Name</h4>
            <div class="row">
              <div class="col-12">
                <table class="table">
                  <thead>
                    <tr>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td id="customer_name">{{$order->customer_first_name." ".$order->customer_last_name}}</td>
                    </tr>
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
          <h4 class="card-title">Items</h4>
          <div class="row">
            <div class="col-12">
              <table class="table">
                <thead>
                  <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Quantity</th>
                  </tr>
                </thead>
                <tbody id="items_list">
                  @foreach($items as $id => $i)
                    <tr>
                      <td><img src="{{$i['pic']}}" class="product_image"></td>
                      <td>{{$i['name']}}</td>
                      <td>x{{$i['qty']}}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
	</div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>