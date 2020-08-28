
<div class="modal-dialog modal-lg" role="document">
  <style>
    .form-control[readonly] {
         background-color: transparent;
      }
  </style>
  <div class="modal-content">
  	<div class="modal-header">
		<h4 class="modal-title" id="modal-title">Payments for {{$sales->reference_no}}</h4>
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="modal-body">
    <section id="data-list-view" >
      <div class="table-responsive">
        <table class="table data-list-view-modal">
          <thead>
            <tr>
              <th>Date</th>
              <th>Reference No</th>
              <th>Payment Type</th>
              <th>Amount</th>
              <th>Payment Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($payments as $pay)
              <tr>
                <td>{{$pay->date}}</td>
                <td>{{$pay->reference_no}}</td>
                <td>{{$pay->payment_type}}</td>
                <td>{{$pay->amount}}</td>
                <td>{{$pay->status}}</td>
                <td><a href="#" data-href="{{route('payment.delete', $pay)}}" class="btn btn-sm btn-primary text-white modal_button"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>
                </td>
              </tr>
            @empty
              <tr>
                <td class="text-center" colspan="5">No Payment Data</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      {{-- DataTable ends --}}
    </section>
    <div class="modal-footer">
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>