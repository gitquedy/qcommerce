@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Shipping Fee Reconciliation')

@section('vendor-style')
        {{-- vednor files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/extensions/dataTables.checkboxes.css')) }}">
@endsection
@section('mystyle')
        {{-- Page css files --}}
        <link rel="stylesheet" href="{{ asset(mix('css/pages/data-list-view.css')) }}">
@endsection

@section('content')
{{-- Data list view starts --}}
<section id="data-list-view" class="data-list-view-header">
    <div class="action-btns d-none">
      <div class="btn-dropdown mr-1 mb-1">
        <div class="btn-group dropdown actions-dropodown">
          <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Actions
          </button>
          <div class="dropdown-menu">
            <a class="dropdown-item massAction" href="#" data-action="{{ route('crud.massDelete')}}" data-tablename="shop">Delete</a>
            <!--<a class="dropdown-item" href="#">Print</a>-->
            <!--<a class="dropdown-item massAction" href="#" data-action="{{ route('crud.massArchived') }}">Archive</a>-->
            <!--<a class="dropdown-item" href="#">Another Action</a>-->
          </div>
        </div>
      </div>
    </div>
    {{-- DataTable starts --}}
    <div class="table-responsive">
      <table class="table data-list-view">
        <thead>
          <tr>
            <th class="dt-checkboxes-cell dt-checkboxes-select-all sorting_disabled">
                <input type="checkbox">
            </th>
            <th>Order ID</th>
            <th>Payment Period</th>
            <th>Shipping Paid by Customer</th>
            <th>Shipping Paid by Seller</th>
            <th>Overcharge</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
      </table>
    </div>
    {{-- DataTable ends --}}

    {{-- add new sidebar starts --}}
      @include('crud/sidebar-create')
    {{-- add new sidebar ends --}}
  </section>
  {{-- Data list view end --}}
@endsection
@section('vendor-script')
{{-- vednor js files --}}
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>-
  <!--<script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script>-->
  <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
@endsection
@section('myscript')
  {{-- Page js files --}}
  <!-- datatables -->
  <script type="text/javascript">
  var columnns = [
            { data: 'id',
            name: 'id' ,
            "render": function (){
                    return '<input type="checkbox" class="dt-checkboxes">';
                },
                className:'dt-checkboxes-cell'
                
            },
            { data: 'id', name: 'id' ,orderable : false},
            { data: 'seller_payout_fees.statement', name: 'seller_payout_fees.statement' },
            { data: 'customer_payout_fees.amount', name: 'customer_payout_fees.amount' },
            { data: 'seller_payout_fees.amount', name: 'seller_payout_fees.amount' },
            { data: 'overcharge', name: 'overcharge' },
            { data: 'shipping_fee_reconciled', name: 'shipping_fee_reconciled', "render": function(order){
                if (order.shipping_fee_reconciled == 1) {
                  return "Over Charged";
                }
                else if(order.shipping_fee_reconciled == 2) {
                  return "Filed Discpute";
                }
                else if(order.shipping_fee_reconciled == 3) {
                  return "Resolved";
                }
                else {
                  return "Pending";
                }
            } },
            { data: 'action', name: 'action' }
        ];
  var table_route = '{{ route('shippingfee.index') }}';
  var buttons = [];
  function created_row_function(row, data, dataIndex){
    $(row).attr('data-id', JSON.parse(data.id));
  }
  var aLengthMenu = [[4, 10, 15, 20],[4, 10, 15, 20]];
  var pageLength = 10;
  $(document).ready(function(){
      $('.view_modal').on('hidden.bs.modal', function () {
        table.ajax.reload();
      });
  });  
  </script>
  <script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
@endsection
