
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
    <h4 class="modal-title" id="modal-title">View Transfer {{$transfer->reference_no}}</h4>
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  </div>
  <div class="modal-body"  id="print_div">
    <section class="card">
        <div class="card-content">
          <div class="card-body">
            <h4 class="card-title">Transfer Details</h4>
            <div class="row">
              <div class="col-12">
                <table class="table">
                  <tbody>
                    <tr>
                      <td>Reference No.:</td>
                      <td>{{$transfer->reference_no}}</td>
                    </tr>
                    <tr>
                      <td>Date:</td>
                      <td>{{date("F d, Y", strtotime($transfer->date))}}</td>
                    </tr>
                    <tr>
                      <td>Status:</td>
                      <td>
                        @php
                        switch ($transfer->status) {
                            case 'completed':
                                    echo '<span class="badge badge-success">Complete</span>';
                                break;
                            case 'pending':
                                    echo '<span class="badge badge-secondary">Pending</span>';
                                break;
                            case 'sent':
                                    echo '<span class="badge badge-warning">Sent</span>';
                                break;
                            
                            default:
                                    echo '<span class="badge badge-primary">Unknown</span>';
                                break;
                        }
                        @endphp
                      </td>
                    </tr>
                    <tr>
                      <td>From Warehouse:</td>
                      <td>{{isset($transfer->from_warehouse->name)?$transfer->from_warehouse->name:'[Deleted Warehouse]'}}</td>
                    </tr>
                    <tr>
                      <td>To Warehouse:</td>
                      <td>{{isset($transfer->to_warehouse->name)?$transfer->to_warehouse->name:'[Deleted Warehouse]'}}</td>
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
                    <th class="text-center" width="90%">Product (Code - Name)</th>
                    <th class="text-center" width="10%">Quantity</th>
                  </tr>
                </thead>
                <tbody id="items_list">
                  @forelse($transfer->items as $item)
                  <tr data-id="{{$item->sku_id}}">
                    <td>
                      <div class="media">
                        <img src="{{$item->image}}" alt="No Image Available" class="d-flex mr-1 product_image">
                        <div class="media-body">
                          <h5 class="mt-0">{{$item->sku_name}}</h5>
                          {{($item->brand)?$item->brand:''}}
                          {{$item->sku_code}}
                          <input type="hidden" name="transfer_item_array[{{$item->id}}][image]" class="original_sku_image" value="{{$item->image}}" />
                          <input type="hidden" name="transfer_item_array[{{$item->id}}][name]" class="original_sku_name" value="{{$item->sku_name}}" />
                          <input type="hidden" name="transfer_item_array[{{$item->id}}][brand]" class="original_sku_brand" value="{{$item->sku_brand}}" /> 
                          <input type="hidden" name="transfer_item_array[{{$item->id}}][code]" class="original_sku_code" value="{{$item->sku_code}}" />
                        </div>
                      </div>
                    </td>
                    <td class="text-center">
                      x{{$item->quantity}}
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td class="text-center" colspan="2">
                      <p class="text-danger">Missing Data!</p>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
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