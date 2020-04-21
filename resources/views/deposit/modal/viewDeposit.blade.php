
<div class="modal-dialog modal-lg" role="document">
  <style>
    .form-control[readonly] {
         background-color: transparent;
      }
  </style>
  <div class="modal-content">
    <div class="modal-header">
    <h4 class="modal-title" id="modal-title">Deposits By {{$customer->formatName()}}</h4>
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  </div>
  <div class="modal-body">
    <section id="data-list-view" class="data-list-view-header">
      {{-- DataTable starts --}}
      <div class="table-responsive">
        <table class="table data-list-view-modal">
          <thead>
            <tr>
              <th>Date</th>
              <th>Bank Reference No</th>
              <th>Amount</th>
              <th>Note</th>
              <th>Created By</th>
              <th>Updated By</th>
            </tr>
          </thead>
          <tbody>
            @forelse($customer->deposits as $deposit)
              <tr>
                <td>{{$deposit->date}}</td>
                <td>{{$deposit->reference_no}}</td>
                <td>{{number_format($deposit->amount, 2)}}</td>
                <td>{{$deposit->note}}</td>
                <td>{{$deposit->created_by_name->formatName()}}</td>
                <td>{{($deposit->updated_by_name)?$deposit->updated_by_name->formatName():'--'}}</td>
              </tr>
            @empty
              <tr>
                <td class="text-center" colspan="6">No Deposit</td>
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
<script>
    $(".data-list-view-modal").DataTable({
        processing: true,
        serverSide: false,
        columns: [
              { data: 'date', name: 'date' },
              { data: 'reference_no', name: 'reference_no' },
              { data: 'payment_type', name: 'payment_type' },
              { data: 'amount', name: 'amount', class: 'text-right' },
              { data: 'payment_status', name: 'payment_status', class: 'text-center' },
              { data: 'action', name: 'action', class: 'text-center' },
              
          ],
        createdRow: created_row_function,
        drawCallback: (typeof draw_callback_function === "function")  ? draw_callback_function : function(){},
        responsive: !1,
        columnDefs: [{
            orderable: false,
            targets: 0,
            checkboxes: {
                selectRow: !0
            },
        }],
        dom: '<"top"<"actions action-btns"B><"action-filters"lf>><"clear">rt<"bottom"<"actions">p>',
        oLanguage: {
            sLengthMenu: "_MENU_",
            sSearch: ""
        },
        aLengthMenu: [[20, 50, 100, 500],[20, 50, 100, 500]],
        select: {
            selector: "first-child",
            style: "multi",
        },
        bInfo: true,
        bFilter: true,
        pageLength: 20,
        "aaSorting": [],
        initComplete: function(t, e) {
            $(".dt-buttons .btn").removeClass("btn-secondary")
        }
    });
</script>