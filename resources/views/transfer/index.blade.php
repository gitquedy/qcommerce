@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Transfers')

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
  <div class="filter-date">
    @include('reports.components.dateFilter')
  </div>
  {{-- DataTable starts --}}
  <div class="table-responsive">
    <table class="table data-list-view">
      <thead>
        <tr>
          <th>For Checkbox</th>
          <th>Date</th>
          <th>Reference No</th>
          <th>From Warehouse</th>
          <th>To Warehouse</th>
          <th>Note</th>
          <th>Status</th>
          <th>Created By</th>
          <th>Updated By</th>
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
  <script src="{{ asset('vendors/js/daterangepicker/daterangepicker.min.js') }}"></script>
  <script src="{{ asset('js/scripts/reports/daterange.js') }}"></script>
@endsection
@section('myscript')
  {{-- Page js files --}}
  <!-- datatables -->
  <script type="text/javascript">
  var columnns = [
            { data: 'id', name: 'id', orderable : false},
            { data: 'date', name: 'date' },
            { data: 'reference_no', name: 'reference_no' },
            { data: 'from_warehouse_name', name: 'from_warehouse_name' },
            { data: 'to_warehouse_name', name: 'to_warehouse_name' },
            { data: 'note', name: 'note' },
            { data: 'status', name: 'status' },
            { data: 'created_by_name', name: 'created_by_name' },
            { data: 'updated_by_name', name: 'updated_by_name' },
            { data: 'action', name: 'action', class: 'text-center' },
            
        ];
  var table_route = {
          url: '{{ route('transfer.index') }}',
          data: function (data) {
            data.daterange = $("#daterange").val();
          }
        };
  var buttons = [
          { text: "<i class='feather icon-plus'></i> Add New",
            action: function() {
                window.location = '{{ route('transfer.create') }}';
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
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
<script type="text/javascript">
  $(document).ready(function() {
      var filterDate = $(".filter-date");
      filterDate.insertAfter($(".top .actions .dt-buttons"));

      $(".filter-date .btn-group").removeClass("mb-1");

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
  });
</script> 
@endsection
