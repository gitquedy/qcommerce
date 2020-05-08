@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Warehouse Management')

@section('vendor-style')
        {{-- vednor files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/extensions/dataTables.checkboxes.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
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
            <th>Code</th>
            <th>Name</th>
            <th>Address</th>
            <th>Phone</th>
            <th>Email</th>
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
  <!-- datatables -->
  <script type="text/javascript">
  var columnns = [
            { data: 'id', name: 'id', orderable : false},
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { data: 'address', name: 'address' },
            { data: 'phone', name: 'phone' },
            { data: 'email', name: 'email' },
            { data: 'action', name: 'action' }
        ];
  var table_route  = {
          url: '{{ route('warehouse.index') }}',
            data: function (data) {
            }
          };
  var buttons = [
          { text: "<i class='feather icon-plus'></i> Add New",
            action: function() {
                window.location = '{{ route('warehouse.create') }}';
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
@endsection
