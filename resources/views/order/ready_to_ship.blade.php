@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Order Management')

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
<section class="card">
    <div class="card-content">
      <div class="card-body">
        <div class="row">
      <!--     <div class="col-sm-4 col-12">
            <div class="text-bold-600 font-medium-2">
              Order ID:
            </div>
            <div class="form-group">
              <input type="text" id="search" class="form-control inputSearch" placeholder="Input order id here..">
            </div>
          </div> -->
          <div class="col-sm-4 col-12">
            <div class="text-bold-600 font-medium-2">
              Shop:
            </div>
            <div class="form-group">
              <select name="shop" id="shop" class="select2 form-control selectFilter">
                <option value="all">All</option>
                @foreach($all_shops as $shop)
                  <option value="{{ $shop->id }}">{{ $shop->name . ' (' . $shop->short_name . ')' }}</option>
                @endforeach
              </select>
            </div>
        </div>
        <div class="col-sm-4 col-12">
            <div class="text-bold-600 font-medium-2">
              Status:
            </div>
            <div class="form-group">
              <select name="status[]" id="status" class="select2 form-control selectFilter" multiple="multiple">
                @foreach($statuses as $status)
                @if($status=='ready_to_ship')
                  <option value="{{ $status }}" {{ $status == 'pending' || $status == 'ready_to_ship' || $status == 'shipped' ? 'selected' : '' }}>{{ ucwords(str_replace("_"," ", $status)) }}</option>
                @endif
                @endforeach
              </select>
            </div>
        </div>
        <div class="col-sm-4 col-12">
            <div class="text-bold-600 font-medium-2">
              Date Filter:
            </div>
            <div class="form-group">
              <select name="timings[]" id="timings" class="select2 form-control selectFilter" >
                <option value="All">All</option>
                <option value="Today">Today</option>
                <option value="Yesterday">Yesterday</option>
                <option value="Last_7_days">Last 7 days</option>
                <option value="Last_30_days">Last 30 days</option>
                <option value="This_Month">This Month</option>
              </select>
            </div>
        </div>
      </div>
    </div>
  </section>
<section id="data-list-view" class="data-list-view-header">
    <div class="action-btns d-none">
      <div class="btn-dropdown mr-1 mb-1">
        <div class="btn-group dropdown actions-dropodown">
          <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Actions
          </button>
          <div class="dropdown-menu">
            <a class="dropdown-item" onclick="print_label()" >Print Shipping Label</a>
          </div>
        </div>
      </div>
    </div>

    {{-- DataTable starts --}}
    <div class="table-responsive">
      <table class="table data-list-view">
        <thead>
          <tr>
            <th>For Checkbox</th>
            <th>Order Number</th>
            <th>Seller</th>
            <th>Creation Date</th>
            <!-- <th>Creation Date</th> -->
            <th>Payment Method</th>
            <th>Price</th>
            <th>Item Count</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
      </table>
    </div>
    {{-- DataTable ends --}}
  </section>
  {{-- Data list view end --}}
      <form action="{{route('order.print_shipping_mass')}}" method="post" id="mass_print_form">
      @csrf
      <input type="hidden" id="mass_print_val" name="ids">
  </form>
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
            { data: 'id', name: 'id'},
            { data: 'shop', name: 'shop.short_name'},
            { data: 'created_at_formatted', name: 'created_at' },
            // { data: 'created_at', name: 'created_at' },
            { data: 'payment_method', name: 'payment_method' },
            { data: 'price', name: 'price' },
            { data: 'items_count', name: 'items_count' },
            { data: 'statusDisplay', name: 'status' },
            { data: 'actions', name: 'actions', orderable : false},
        ];
  var table_route = {
          url: '{{ route('order.ready_to_ship') }}',
          data: function (data) {
                data.shop = $("#shop").val();
                data.status = $("#status").val();
                data.timings = $("#timings").val();
            }
        };
  var buttons = [
            // { text: "<i class='feather icon-plus'></i> Add New",
            // action: function() {
            //     window.location = '{{ route('order.create') }}';
            // },
            // className: "btn-outline-primary margin-r-10"}
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
  
  
    function print_label(){
      var selected_ids = [];
      
    $(".dt-checkboxes").each(function(){
       if($(this).prop('checked')==true){
           selected_ids.push($(this).parent().parent().data('id'));
       }
    });
    
    if(selected_ids.length==0){
          Swal.fire(
          'Warning !',
          'Please select a order !',
          'warning'
        );
        return false;
      }
    
    $('#mass_print_val').val(JSON.stringify(selected_ids));
    $('#mass_print_form').submit();
    
  }
  
  </script>
@endsection
