@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Payout Reconciliation')

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
{{-- Data list view starts --}}
<section class="card">
    <div class="card-content">
      <div class="card-body">
        <div class="row">
          <div class="col-sm-12 shop_filter">
              <a href="{{ action('PayoutController@indexLaz') }}?tab=all" class="btn btn-lg btn-outline-primary mb-1 {{ $request->segment(4) == 'laz' ?  'active' : ''}}">
                <img class="shop_logo" src="{{asset('images/shop/icon/lazada.png')}}" alt="">
                Lazada
              </a>
              <a href="{{ action('PayoutController@indexShopee') }}?tab=all" class="btn btn-lg btn-outline-primary mb-1 {{ $request->segment(4) == 'shopee' ?  'active' : ''}}">
                <img class="shop_logo" src="{{asset('images/shop/icon/shopee.png')}}" alt="">
                Shopee
              </a>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 shop_filter">
              <label class="btn btn-lg round btn-outline-primary {{ $request->get('tab') == 'all' ? 'active' : '' }}">
                <input type="radio" name="tab" value="all"  {{ $request->get('tab') == 'all' ? 'checked' : '' }}>
                <p>Total Payout</p>
                <p class="text-warning text-bold-400 font-large-1"><span id="header_total">0</span> Payouts</p>
              </label>
              
              <label class="btn btn-lg round btn-outline-primary {{ $request->get('tab') == 'not_confirm' ? 'active' : '' }}">
                <input type="radio" name="tab" value="not_confirm"  {{ $request->get('tab') == 'not_confirm' ? 'checked' : '' }}>
                <p>Unconfirmed Payout</p>
                <p class="text-warning text-bold-400 font-large-1"><span id="header_unconfirmed">0</span> Payouts</p>
              </label>
              
              <label class="btn btn-lg round btn-outline-primary {{ $request->get('tab') == 'confirm' ? 'active' : '' }}">
                <input type="radio" name="tab" value="confirm"  {{ $request->get('tab') == 'confirm' ? 'checked' : '' }}>
                <p>Confirmed Payout</p>
                <p class="text-warning text-bold-400 font-large-1"><span id="header_confirmed">0</span> Payouts</p>
              </label>
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-12">
            @include('order.components.shopFilter')
            @include('reports.components.dateFilter')
            <div class="btn-group" id="chip_area_shop"></div>
            <div class="btn-group" id="chip_area_timings"></div>
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
            <a class="dropdown-item reconcile" data-href="{{ action('PayoutController@payoutReconcileShopee') }}" data-action="Confirm">Confirm Payout</a>
            <a class="dropdown-item reconcile" data-href="{{ action('PayoutController@payoutReconcileShopee') }}" data-action="Unconfirm">Unconfirm Payout</a>
          </div>
        </div>
    </div>

    {{-- DataTable starts --}}
      </div>
    <div class="table-responsive">
      <table class="table data-list-view">
        <thead>
          <tr>
            <th>For Checkbox</th>
            <th>Shop</th>
            <th>Payout Date</th>
            <th>Amount</th>
            <th>Reconciled</th>
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
  <script src="{{ asset('vendors/js/moment/moment.min.js') }}"></script>
  <script src="{{ asset('vendors/js/daterangepicker/daterangepicker.min.js') }}"></script>
  <script src="{{ asset('js/scripts/reports/daterangeOneYear.js') }}"></script>
@endsection
@section('myscript')
  {{-- Page js files --}}
  <!-- datatables -->
  <script type="text/javascript">
    function getParams(){
      var $params = "?shop=" + $("#shop").val() + "&daterange=" + $("#daterange").val();
      return $params;
    }
    function getHeaders(){
        $.ajax({
        method: "GET",
        url: "{{ action('PayoutController@headersShopee')  }}" + getParams(),
        success: function success(result) {
            $('#header_total').html(result.data.total);
            $('#header_unconfirmed').html(result.data.unconfirmed);
            $('#header_confirmed').html(result.data.confirmed);
          },
        });     
      }
  </script>
  <script type="text/javascript">
  var columnns = [
            { data: 'id', name: 'id', orderable : false},
            { data: 'shopDisplay', name: 'shopDisplay'},
            { data: 'payout_date', name: 'payout_date'},
            { data: 'amount', name: 'amount'},
            { data: 'reconciledDisplay', name: 'reconciledDisplay' },
            { data: 'actions', name: 'actions', orderable : false },
        ]; 
  var table_route = {
          url: '{{ action("PayoutController@indexShopee") }}',
          data: function (data) {
                data.shop = $("#shop").val();
                data.daterange = $("#daterange").val();
                data.tab = $('input[name="tab"]:checked').val();
            }
        };
  var buttons = [];
  var BInfo = true;
  var bFilter = true;
  function created_row_function(row, data, dataIndex){
    $(row).attr('data-id', JSON.parse(data.id));
  }
  function draw_callback_function(settings){
    getHeaders();
  }
  var aLengthMenu = [[20, 50, 100, 500],[20, 50, 100, 500]];
  var pageLength = 20;
</script>
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
      getHeaders(); // on load get headers
      $('input[name="tab"]').change(function(){
        var tab = $('input[name="tab"]:checked').val();
        url = "{{ action('PayoutController@indexShopee')}}?tab=" + tab;
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
        if(result.success == 1){
          toastr.success(result.msg);
        }
        $('.data-list-view').DataTable().ajax.reload();
        getHeaders();
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
