@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Shop List')

@section('vendor-style')
        {{-- vednor files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/extensions/dataTables.checkboxes.css')) }}">
@endsection
@section('mystyle')
      <style>
        .shop_logo {
          width: 80px;
        }
        .shop_logo img {
          width: 100%;
        }
      </style>
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
            <th>ID</th>
            <th>Site</th>
            <th>NAME</th>
            <th>Warehouse</th>
            <th>Status</th>
            <th>Resync</th>
            <th>Products</th>
            <th>Pending</th>
            <th>Ready to Ship</th>
            <th>Shipped</th>
            <th>UPDATED AT</th>
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
            orderable : false,

            "render": function (){
                    return '<input type="checkbox" class="dt-checkboxes">';
                },
                className:'dt-checkboxes-cell'
                
            },
            { data: 'id', name: 'id'},
            { data: 'site', name: 'site', orderable : false, className: 'shop_logo'},
            { data: 'name', name: 'name' },
            { data: 'warehouse_name', name: 'warehouse_name' },
            { data: 'statusChip', name: 'statusChip' },
            { data: 'reSync', name: 'reSync' },
            { data: 'products', name: 'products' },
            { data: 'pending_count', name: 'pending_count' },
            { data: 'ready_to_ship_count', name: 'ready_to_ship_count' },
            { data: 'shipped_count', name: 'shipped_count' },
            { data: 'updated_at', name: 'updated_at' },
            { data: 'action', name: 'action' },
        ];
  var table_route = '{{ route('shop.index') }}';
  var buttons = [
            { text: "<i class='feather icon-plus'></i> Add New",
            action: function() {
                window.location = '{{ route('shop.create') }}';
            },
            className: "btn-outline-primary margin-r-10"}
            ];
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
