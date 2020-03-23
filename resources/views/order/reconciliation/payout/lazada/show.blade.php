<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
  	<div class="modal-header">
		<h4 class="modal-title" id="modal-title">Payout Details for {{ $LazadaPayout->statement_number }}
		</h4>
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="modal-body">
    <section class="card">
        <div class="card-content">
          <div class="card-body">
            <h4 class="card-title">Account Statement</h4>
            <div class="row">
              <div class="col-12">
                <table class="table text-left">
                  <thead>
                    <tr>
                      <th>Title</th>
                      <th>Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Opening Balance</td>
                      <td>{{ number_format($LazadaPayout->opening_balance, 2) }}</td>
                    </tr>
                    <tr><td></td><td></td></tr>
                    <tr>
                      <td>Item Price Credit</td>
                      <td>{{ number_format($LazadaPayout->item_revenue, 2) }}</td>
                    </tr>
                    <tr>
                      <td>Shipping Fee (Paid By Customer)</td>
                      <td>{{ number_format($LazadaPayout->shipment_fee_credit, 2) }}</td>
                    </tr>
                    <tr>
                      <td>Payment Fee</td>
                      <td>{{ number_format($LazadaPayout->getPaymentFee(), 2) }}</td>
                    </tr>
                    <tr>
                      <td>Shipping Fee Paid by Seller</td>
                      <td>{{ number_format($LazadaPayout->shipment_fee, 2) }}</td>
                    </tr>
                    <tr><td></td><td></td></tr>
                    <tr>
                      <td>Subtotal (PHP)</td>
                      <td>{{ number_format($LazadaPayout->closing_balance, 2) }}</td>
                    </tr>
                    <tr>
                      <td>Closing Balance (PHP)</td>
                      <td>{{ number_format($LazadaPayout->closing_balance, 2) }}</td>
                    </tr>
                    <tr><td></td><td></td></tr>
                    <tr>
                      <td><h4>Payout Amount (PHP)</h4></td>
                      <td><h4>{{ number_format($LazadaPayout->closing_balance, 2) }}</h4></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
    </section>
	</div>
    <div class="modal-footer">
      <button type="button" class="btn btn-primary no-print" aria-label="Print" 
      onclick="$(this).closest('div.modal-content').printThis();"><i class="fa fa-print"></i> Print
      </button>
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>