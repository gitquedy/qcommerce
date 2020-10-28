@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Purchases Management')

@section('vendor-style')
        {{-- vednor files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/extensions/dataTables.checkboxes.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
        <link rel="stylesheet" href="{{ asset('vendors/css/daterangepicker/daterangepicker.css') }}">
@endsection
@section('mystyle')
        {{-- Page css files --}}
        <link rel="stylesheet" href="{{ asset(mix('css/pages/data-list-view.css')) }}">
@endsection

@section('content')
<section id="data-list-view" class="data-list-view-header">

    {{-- DataTable starts --}}
    <div class="table-responsive">
      <table class="table data-list-view">
        <thead>
          <tr>
            <th>For Checkbox</th>
            <th>Date</th>
            <th>Reference No</th>
            <th>Supplier</th>
            <th>Purchase Status</th>
            <th>Grand Total</th>
            <th>Paid</th>
            <th>Balance</th>
            <th>Payment Status</th>
            <th>Action</th>
          </tr>
        </thead>
      </table>
    </div>
    {{-- DataTable ends --}}
  </section>
  {{-- Data list view end --}}
  
@endsection
@section('vendor-script')
{{-- vednor js files --}}
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>-
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script>
  <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection
@section('myscript')
  {{-- Page js files --}}
  <script type="text/javascript">
    function getParams(){
            var $params = "?site={{ $request->get('site') }}" + "&daterange=" + $("#daterange").val();
            return $params;
          }
    function getHeaders(){
        $.ajax({
        method: "GET",
        url: "{{ action('OrderController@headers')  }}" + getParams(),
        success: function success(result) {     
            $.each(result.data, function (i, item) {
              $('#badge_' + i).html(item);
            });
          },
        });     
      }
  </script>
  <!-- datatables -->
  <script type="text/javascript">
  var id = "{{ $request->get('site') == 'shopee' ?  'ordersn' : 'id'  }}";
  var columnns = [
            { data: id, name: id, orderable : false},
            { data: 'date', name: 'date' },
            { data: 'reference_no', name: 'reference_no' },
            { data: 'supplier_name', name: 'supplier_name' },
            { data: 'status', name: 'status', class: 'text-right' },
            { data: 'grand_total', name: 'grand_total', class: 'text-right' },
            { data: 'paid', name: 'paid', class: 'text-right' },
            { data: 'balance', name: 'balance', class: 'text-right' },
            { data: 'payment_status', name: 'payment_status', class: 'text-center text-capitalize' },
            { data: 'action', name: 'action', class: 'text-center' },
            
        ];
  var table_route = {
          url: '{{ route('purchases.index') }}',
          data: function (data) {
            }
        };
  var buttons = [
          { text: "<i class='feather icon-plus'></i> Add New",
            action: function() {
                window.location = '{{ route('purchases.create') }}';
            },
            className: "btn-outline-primary margin-r-10"}
            ];
  var BInfo = true;
  var bFilter = true;
  function created_row_function(row, data, dataIndex){

  }
  var aLengthMenu = [[20, 50, 100, 500],[20, 50, 100, 500]];
  var pageLength = 20;

</script>
<script type="text/javascript">
    $(document).on('click', '.toggle_view_modal', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).data('action'),
            method: "POST",
            data: {},
            success:function(result)
            {
                $('.view_modal').html(result).modal();
            }
        });
    });
</script> 
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
<script type="text/javascript">
  $(document).ready(function() {
    table.order( [ 1, 'desc' ] ).draw();
    $(document).on('click', '.payment_delete', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        $.ajax({
          method: "DELETE",
          dataType: "JSON",
          url: href,
          success: function success(result) {     
              if(result.success == true){
                toastr.success(result.msg);
                table.ajax.reload();
              }else{
                if(result.msg){
                  toastr.error(result.msg);
                }
              }
          },
        }); 
    });
  }) 
</script>
@endsection
