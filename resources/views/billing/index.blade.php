@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Billing')

@section('vendor-style')
        {{-- vendor files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection
@section('mystyle')
        {{-- Page css files --}}
        <link rel="stylesheet" href="{{ asset(mix('css/pages/data-list-view.css')) }}">
@endsection

@section('content')
<section>
  {{-- Data list view starts --}}
  <div id="data-list-view" class="data-list-view-header">
    {{-- DataTable starts --}}
    <div class="table-responsive">
      <table class="table data-list-view">
        <thead>
          <tr>
            <th class="dt-checkboxes-cell dt-checkboxes-select-all sorting_disabled">
                <input type="checkbox">
            </th>
            <th>Invoice No.</th>
            <th>Plan</th>
            <th>Promo Code</th>
            <th>Billing Period</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Payment Date</th>
            <th>Next Payment Date</th>
            <th>Created At</th>
            <th>Action</th>
          </tr>
        </thead>
      </table>
    </div>
    {{-- DataTable ends --}}
  </div>
  {{-- Data list view end --}}
</section>
@endsection

@section('vendor-script')
{{-- vednor js files --}}
<!-- vendor files -->
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
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
            { data: 'invoice_no', name: 'invoice_no'},
            { data: 'plan_id', name: 'plan_id'},
            { data: 'promocode', name: 'promocode'},
            { data: 'billing_period', name: 'billing_period'},
            { data: 'amount', name: 'amount'},
            { data: 'paid_status', name: 'paid_status'},
            { data: 'payment_date', name: 'payment_date'},
            { data: 'next_payment_date', name: 'next_payment_date'},
            { data: 'created_at', name: 'created_at', searchable: false, visible: false },
            { data: 'action', name: 'action', orderable: false }
        ];
    var table_route = {
            url: '{{ route('billing.index') }}'
            };
    var buttons = [];
    var order = [9, 'desc'];
    var BInfo = true;
    var bFilter = true;
    function created_row_function(row, data, dataIndex){
        $(row).attr('data-id', JSON.parse(data.id));
    }
    var aLengthMenu = [[20, 50, 100, 500],[20, 50, 100, 500]];
    var pageLength = 20;
</script>
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
<script>
  $(document).ready(function() {
    $(document).on('click', '.billing_view_details', function() {
      $.ajax({
          url: $(this).data('action'),
          method: "POST",
          data: {data:$(this).data('billing_id')},
          success:function(result)
          {
              $('.view_modal').html(result).modal();
          }
      });
  });
  });
</script>
@endsection