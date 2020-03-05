@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Return Reconciliation')

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
          <div class="col-sm-12 shop_filter">
              <label class="btn btn-lg round btn-outline-primary {{ $request->get('tab') == 'all' ? 'active' : '' }}">
                <input type="radio" name="tab" value="all"  {{ $request->get('tab') == 'all' ? 'checked' : '' }}>
                <p>Total Returned Orders</p>
                <p class="text-warning text-bold-400 font-large-1"><span>{{ $totals['total'] }}</span> Orders</p>
              </label>
              
              <label class="btn btn-lg round btn-outline-primary {{ $request->get('tab') == 'not_confirm' ? 'active' : '' }}">
                <input type="radio" name="tab" value="not_confirm"  {{ $request->get('tab') == 'not_confirm' ? 'checked' : '' }}>
                <p>Unconfirmed Return</p>
                <p class="text-warning text-bold-400 font-large-1"><span>{{ $totals['unconfirmed'] }}</span> Orders</p>
              </label>
              
              <label class="btn btn-lg round btn-outline-primary {{ $request->get('tab') == 'confirm' ? 'active' : '' }}">
                <input type="radio" name="tab" value="confirm"  {{ $request->get('tab') == 'confirm' ? 'checked' : '' }}>
                <p>Confirmed Return</p>
                <p class="text-warning text-bold-400 font-large-1"><span>{{ $totals['confirmed'] }}</span> Orders</p>
              </label>
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-12">
            <div class="btn-group mb-1">
              <input type="hidden" id="shop" name="shop" class="selectFilter">
              <input type="hidden" id="timings" name="timings" class="selectFilter">
              <input type="hidden" id="shipping_status" name="shipping_status" class="selectFilter">
              <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                 <i class="fa fa-shopping-cart"></i> All Shop
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    {{-- <a class="dropdown-item shop_filter_btn" href="#" data-shop_id="all">All Shop</a> --}}
                  @foreach($all_shops as $shop)
                    <a class="dropdown-item filter_btn" href="#" data-target="shop" data-type="multiple" data-value="{{ $shop->id }}">{!! $shop->getImgSiteDisplayWithFullName() !!}</a>
                  @endforeach
                </div>
              </div>
            </div>
            <div class="btn-group mb-1">
              <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                 <i class="fa fa-filter"></i> Date Filter
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenu1">
                  {{-- <a class="dropdown-item filter_btn" href="#" data-target="timings" data-type="single" data-value="All">All</a> --}}
                  <a class="dropdown-item filter_btn" href="#" data-target="timings" data-type="single" data-value="Today">Today</a>
                  <a class="dropdown-item filter_btn" href="#" data-target="timings" data-type="single" data-value="Yesterday">Yesterday</a>
                  <a class="dropdown-item filter_btn" href="#" data-target="timings" data-type="single" data-value="Last_7_days">Last 7 days</a>
                  <a class="dropdown-item filter_btn" href="#" data-target="timings" data-type="single" data-value="Last_30_days">Last 30 days</a>
                  <a class="dropdown-item filter_btn" href="#" data-target="timings" data-type="single" data-value="This_Month">This Month</a>
                </div>
              </div>
            </div>
            <div class="btn-group" id="chip_area_shop"></div>
            <div class="btn-group" id="chip_area_timings"></div>
          </div>
      </div>
      <div class="row">
        <div class="col-sm-4 col-12">
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
            <a class="dropdown-item reconcile" data-href="{{ action('OrderController@returnReconcile') }}" data-action="Confirm">Reconcile Orders</a>
            <a class="dropdown-item reconcile" data-href="{{ action('OrderController@returnReconcile') }}" data-action="Unconfirm">Remove Reconciliation</a>
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
            <!-- <th>Shop</th> -->
            <th>Created At</th>
            <th>Last Update</th>
            <th>Amount</th>
            <th>Actions</th>
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
     var id = "{{ $request->get('site') == 'shopee' ?  'ordersn' : 'id'  }}"
  var columnns = [
            { data: id, name: id, orderable : false},
            { data: 'idDisplay', name: 'idDisplay'},
            // { data: 'shop', name: 'shop.short_name'},
            { data: 'created_at_formatted', name: 'created_at_formatted' },
            { data: 'updated_at_formatted', name: 'updated_at_formatted' },
            { data: 'price', name: 'price' },
            { data: 'actions', name: 'actions', orderable : false },
        ]; 
  var table_route = {
          url: '{{ route('order.returnReconciliation') }}',
          data: function (data) {
                data.shop = $("#shop").val();
                data.timings = $("#timings").val();
                data.tab = $('input[name="tab"]:checked').val();
            }
        };
  var buttons = [];
  var BInfo = true;
  var bFilter = true;
  function created_row_function(row, data, dataIndex){
    $(row).attr('data-id', JSON.parse(data.id));
    $(row).attr('data-action', "{{route('barcode.viewBarcode')}}");
  }
  var aLengthMenu = [[20, 50, 100, 500],[20, 50, 100, 500]];
  var pageLength = 20;
</script>
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
      $('input[name="tab"]').change(function(){
        var tab = $('input[name="tab"]:checked').val();
        url = "{{ action('OrderController@returnReconciliation')}}?tab=" + tab;
        window.location.href = url;
      });
      $(".select2").select2({
        dropdownAutoWidth: true,
        width: '100%'
      });  
  $(document).on("click", '.reconcile', function () {
   var selected_ids = [];
   $(".dt-checkboxes").each(function(){
      if($(this).prop('checked')==true){
          selected_ids.push($(this).parent().parent().data('id'));
      }
   });
    
   if(selected_ids.length==0){
         Swal.fire(
         'Warning!',
         'Please select atleast one order',
         'warning'
       );
       return false;
     }
    var url = $(this).data("href") + "?ids=" + selected_ids.toString() + "&action=" + $(this).data('action');
    $.ajax({
      method: "POST",
      url: url,
      success: function success(result) {
        $('.data-list-view').DataTable().ajax.reload();
      },
      error: function error(jqXhr, json, errorThrown) {
        console.log(jqXhr);
        console.log(json);
        console.log(errorThrown);
      }
    });
  });
});
  </script>
@endsection
