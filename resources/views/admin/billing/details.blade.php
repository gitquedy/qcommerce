@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Bank Details')

@section('vendor-style')
        {{-- vendor files --}}
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
{{-- Data list view starts --}}

<section id="data-list-view" class="data-list-view-header">

    {{-- DataTable starts --}}
    <div class="table-responsive">
        
      <table class="table data-list-view">
        <thead>
          <tr>
            <th class="dt-checkboxes-cell dt-checkboxes-select-all sorting_disabled">
                <input type="checkbox">
            </th>
            <th>Bank</th>
            <th>Account Name</th>
            <th>Account Number</th>
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
{{-- vendor js files --}}
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>
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
            { data: 'id',
            name: 'id' ,
            "render": function (){
                    return '<input type="checkbox" class="dt-checkboxes">';
                },
                className:'dt-checkboxes-cell'
            },
            { data: 'bank', name: 'bank'},
            { data: 'account_name', name: 'account_name'},
            { data: 'account_number', name: 'account_number'},
            { data: 'action', name: 'action', orderable : false}
        ];
    var table_route = {
            url: '{{ route('billing.details') }}'
            };
    var buttons = [
            { text: "<i class='feather icon-plus'></i> Add New",
                action: function() {
                    window.location = '{{ route('billing.details.create') }}';
                },
            className: "btn-outline-primary margin-r-10" }
        ];
    var order = [];
    var BInfo = true;
    var bFilter = true;
    function created_row_function(row, data, dataIndex){
        $(row).attr('data-id', JSON.parse(data.id));
    }
    var aLengthMenu = [[20, 50, 100, 500],[20, 50, 100, 500]];
    var pageLength = 20;
</script>
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
@endsection