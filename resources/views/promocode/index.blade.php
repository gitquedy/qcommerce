@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Promocode Management')

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
            <th>Code</th>
            <th>Name</th>
            <th>Description</th>
            <th>Used</th>
            <th>Max use</th>
            <th>Max use per user</th>
            <th>Discount Amount</th>
            <th>Discount Type</th>
            <th>Validity Start</th>
            <th>Validity End</th>
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
  var columnns = [
            { data: 'id', name: 'id', orderable : false},
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { data: 'description', name: 'description' },
            { data: 'uses', name: 'uses'},
            { data: 'max_uses', name: 'max_uses'},
            { data: 'max_uses_business', name: 'max_uses_business'},
            { data: 'discount_amount', name: 'discount_amount'},
            { data: 'discount_type', name: 'discount_type'},
            { data: 'starts_at', name: 'starts_at'},
            { data: 'expires_at', name: 'expires_at'},
            { data: 'action', name: 'action', class: 'text-center'},
            
        ];
  var table_route = {
          url: '{{ route('promocode.index') }}',
          data: function (data) {
            }
        };
  var buttons = [
          { text: "<i class='feather icon-plus'></i> Add New",
            action: function() {
                window.location = '{{ route('promocode.create') }}';
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
</script> 
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
@endsection
