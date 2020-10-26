
<div class="modal-dialog modal-lg" role="document">
  <style>
    .form-control[readonly] {
         background-color: transparent;
      }

    .product_image{
        width:70px;
        height:auto;
    }
  </style>
  <div class="modal-content">
    <div class="modal-header">
    <h4 class="modal-title" id="modal-title">View Expense {{$expense->reference_no}}</h4>
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  </div>
  <div class="modal-body"  id="print_div">
    <section class="card">
        <div class="card-content">
          <div class="card-body">
            <h4 class="card-title">Expense Details</h4>
            <div class="row">
              <div class="col-12">
                <table class="table">
                  <tbody>
                    <tr>
                      <td>Reference No.:</td>
                      <td>{{$expense->reference_no}}</td>
                    </tr>
                    <tr>
                      <td>Date:</td>
                      <td>{{date("F d, Y", strtotime($expense->date))}}</td>
                    </tr>
                    <tr>
                      <td>Warehouse:</td>
                      <td>{{isset($expense->warehouse->name)?$expense->warehouse->name:'[Deleted Warehouse]'}}</td>
                    </tr>
                    <tr>
                      <td>Category</td>
                      <td>{{ $expense->category ? $expense->category->displayName() : '' }}</td>
                    </tr>
                    <tr>
                      <td>Note</td>
                      <td>{{ $expense->note }}</td>
                    </tr>
                    <tr>
                      <td>Attachment:</td>
                      <td>
                        @if($expense->attachment)
                        <a target="_blank" href="{{ $expense->attachment_link() }}">{{ $expense->attachment }}</a>
                        @endif
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </section>
    <section id="data-list-view" class="data-list-view-header">
      {{-- DataTable starts --}}
      <div class="table-responsive">
        <table class="table data-list-view-modal">
          <thead>
            <tr>
              <th>Date</th>
              <th>Reference No</th>
              <th>Payment Type</th>
              <th>Amount</th>
              <th>Payment Status</th>
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
      <button type="button" id="print_btn" class="btn btn-outline-primary no-print">Print</button>
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>
<script>
    $("#print_btn").on('click', function() {
        $("#print_div").printThis({ 
            debug: false,              
            importCSS: true,             
            importStyle: true,         
            printContainer: true,
            pageTitle: "Print Sale",             
            removeInline: false,        
            printDelay: 333,            
            header: null,             
            formValues: true          
        }); 
    });
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
<script src="{{ asset('js/scripts/printThis/printThis.js') }}"></script>