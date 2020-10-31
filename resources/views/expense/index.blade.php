@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Expenses')

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
{{-- Data list view starts --}}
<style>
    .product_image{
        width:100px;
        height:auto;
    }
    
    option[disabled]{
        background-color:#F8F8F8;
    }
</style>


<section id="data-list-view" class="data-list-view-header">
    <div class="action-btns d-none">
      <div class="btn-dropdown mr-1 mb-1">
        <div class="btn-group dropdown actions-dropodown">
          <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Actions
          </button>
          <div class="dropdown-menu">
            <!--<a class="dropdown-item" onclick="mass_copy()"  > Duplicate</a>-->
            <!--<a class="dropdown-item" href="#">Print</a>-->
            <a class="dropdown-item massAction" href="#" data-action="{{ action('ExpenseController@massDelete') }}"> Delete</a>
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
            <th>Date</th>
            <th>Reference No</th>
            <th>Warehouse</th>
            <th>Biller</th>
            <th>Category</th>
            <th>Amount</th>
            <th>Paid</th>
            <th>Balance</th>
            <th>Payment Status</th>
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
  <!--<script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script>-->
  <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection
@section('myscript')
  {{-- Page js files --}}
  <!-- datatables -->
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
  <script type="text/javascript">
  var columnns = [
            { data: 'id',
            name: 'id' ,
            "render": function (){
                    return '<input type="checkbox" class="dt-checkboxes">';
                },
                className:'dt-checkboxes-cell'
                
            },
            { data: 'date_formatted', name: 'date_formatted' },
            { data: 'reference_no', name: 'reference_no' },
            { data: 'warehouse_name', name: 'warehouse_name' },
            { data: 'biller_name', name: 'biller_name' },
            { data: 'category_name', name: 'category_name' },
            { data: 'amount_formatted', name: 'amount_formatted' },
            { data: 'paid', name: 'paid' },
            { data: 'balance', name: 'balance' },
            { data: 'payment_status', name: 'payment_status' },
            { data: 'created_by_name', name: 'created_by_name' },
            { data: 'updated_by_name', name: 'updated_by_name' },
            { data: 'action', name: 'action', class: 'text-center' },
        ];
  var table_route = {
          url: '{{ action("ExpenseController@index") }}',
          data: function (data) {
                data.shop = $("#shop").val();
                data.status = $("#status").val();
                data.timings = $("#timings").val();
            }
        };
  var buttons = [
            { text: "<i class='feather icon-plus'></i> Add New",
            action: function() {
                window.location = '{{ action("ExpenseController@create") }}';
            },
            className: "btn-outline-primary margin-r-10"}
            ];
  var BInfo = true;
  var bFilter = true;
  function created_row_function(row, data, dataIndex){
    $(row).attr('data-id', JSON.parse(data.id));
  }
  var aLengthMenu = [[20, 50, 100, 500],[20, 50, 100, 500]];
  var pageLength = 20;
</script>
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
      $(".select2").select2({
        dropdownAutoWidth: true,
        width: '100%'
      });
  }); 

  </script>
@endsection
