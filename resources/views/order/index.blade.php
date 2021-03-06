@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Order Management')

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

        <style>
          .product_image{
              width:40px;
              height:auto;
          }
        </style>
@endsection

@section('content')
{{-- Data list view starts --}}
<section class="card">
  <div class="card-header">
    <h4 class="card-title">Filter </h4>
  </div>
  <div class="card-content">
    <div class="card-body">
      <div class="row">
        <div class="col-sm-12 shop_filter">
          @foreach($all_sites as $site)
            <label for="{{ $site }}" class="btn btn-lg btn-outline-primary mb-1 {{ $request->get('site') == $site ?  'active' : ''}}">
              <img class="shop_logo" src="{{asset('images/shop/icon/'.$site.'.png')}}" alt="">
              {{ ucfirst($site) }}
              <span id="badge_{{ $site }}_total" class="badge badge-secondary"></span>
            </label>
            <input type="radio" id="{{ $site }}" name="site" value="{{ $site }}"  {{ $request->get("site") == $site ?  "checked" : ""}}>
          @endforeach
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-sm-12">
          <div class="btn-group-toggle" data-toggle="buttons">
            <label class="btn px-1 btn-outline-primary {{ ('all' == $selectedStatus) ? 'active' : '' }}">
              <input type="radio" name="status" id="status_all" class="selectFilter" autocomplete="off" value="all" checked> All
            </label>
            @foreach($statuses as $status)
              <label class="btn px-1 btn-outline-primary {{ ($status == $selectedStatus) ? 'active' : '' }}">
                <input type="radio" name="status" id="status_{{ $status }}"  class="selectFilter" value="{{ $status }}"  {{ ($status == $selectedStatus) ? 'checked' : '' }} autocomplete="off"> {{ ucfirst(strtolower(str_replace("_"," ", $status))) }}
                <span id="badge_{{ $status }}" class="badge badge-secondary"></span>
              </label>
            @endforeach
          </div>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-12">
          @include('order.components.shopFilter')
          @include('reports.components.dateFilter')
          <div class="btn-group mb-1 shipping_status">
            <div class="dropdown ">
              <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Shipping Status
              </button>
              <div class="dropdown-menu" aria-labelledby="dropdownMenu1">
                <a class="dropdown-item filter_btn" href="#" data-target="shipping_status" data-type="single" data-value="All">All</a>
                <a class="dropdown-item filter_btn" href="#" data-target="shipping_status" data-type="single" data-value="to_process">To Process</a>
                <a class="dropdown-item filter_btn" href="#" data-target="shipping_status" data-type="single" data-value="processed">Processed</a>
              </div>
            </div>
          </div>
          <div class="btn-group" id="chip_area_shop"></div>
          <div class="btn-group" id="chip_area_timings"></div>
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
            <!--<a class="dropdown-item massAction" href="#" data-action="{{ route('crud.massDelete')}}">Delete</a>-->
            <a class="dropdown-item" onclick="print_label()" >Print Shipping Label</a>
            <a class="dropdown-item printPackingList" data-href="{{ action('OrderController@printPackingList') }}" >Print Packing List</a>
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
            <th>For Checkbox</th>
            <th> {{ $request->get('site') == 'shopee' ?  'Order SN' : 'Order Number'  }}</th>
            <th>Order Date</th>
            <th>Last Update</th>
            <th>Payment Method</th>
            <th>Price</th>
            <th>Item Count</th>
            <th>Status</th>
            <th>Printed</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody></tbody>
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
  <script src="{{ asset('vendors/js/moment/moment.min.js') }}"></script>
  <script src="{{ asset('vendors/js/daterangepicker/daterangepicker.min.js') }}"></script>
  <script src="{{ asset('js/scripts/reports/daterange.js') }}"></script>
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
    function showAllChild() {
        $('.data-list-view').DataTable().rows().every(function(){
            // If row has details collapsed
            if(!this.child.isShown()){
                // Open this row
                this.child(format(this.data())).show();
                $(this.node()).addClass('shown');
            }
        });
    }
  </script>
  <!-- datatables -->
  <script type="text/javascript">
     // var id = "{{ $request->get('site') == 'shopee' ?  'ordersn' : 'id'  }}"
  var columnns = [
            { data: 'ordersn', name: 'ordersn', orderable : false},
            // { data: 'item_list', name: 'item_list'},
            { data: 'idDisplay', name: 'idDisplay'},
            { data: 'created_at_formatted', name: 'created_at' },
            { data: 'updated_at_at_human_read', name: 'updated_at_at_human_read' },
            { data: 'payment_method', name: 'payment_method' },
            { data: 'price', name: 'price' },
            { data: 'items_count', name: 'items_count' },
            { data: 'statusDisplay', name: 'status' },
            { data: 'printedDisplay', name: 'printed' },
            { data: 'actions', name: 'actions', orderable : false },
        ];
  var table_route = {
          url: '{{ route('order.index') }}',
          data: function (data) {
                data.shop = $("#shop").val();
                data.status = $("input[name=status]:checked").val();
                data.daterange = $("#daterange").val();
                data.printed = $("#printed").val();
                data.site = $('input[name="site"]:checked').val();
                data.shipping_status =  $("#shipping_status").val();
            }
        };
  var buttons = [
            ];
  var BInfo = true;
  var bFilter = true;
  function created_row_function(row, data, dataIndex){
    $(row).attr('data-id', JSON.parse(data.id));
    $(row).attr('data-action', "{{route('barcode.viewBarcode')}}");
  }
  function draw_callback_function(settings){
    getHeaders();
    showAllChild();
  }
  var aLengthMenu = [[20, 50, 100, 500],[20, 50, 100, 500]];
  var pageLength = 20;
  function format ( d ) {
    var html = '<div class="card mb-0 border border-secondary"><div class="card-body w-50 py-1">'+d.item_list+'</div></div>';
    return html;
  }
</script>
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
      var str = $('#status').val(); 
      hideShippingStatus($("input[name=status]:checked").val());
      $('input[name="site"]').change(function(){
        var site = $('input[name="site"]:checked').val();
        @if (isset($_GET['printed']))
          site += "&printed=false";
        @endif
        var default_status = '';
        if(site == 'lazada'){
          default_status = 'pending';
        }else if(site == 'shopify'){
          default_status = 'Open';
        }else if(site == 'woocommerce') {
          default_status = 'processing';
        }
        else{
          default_status = 'READY_TO_SHIP'
        }
        url = "{{ action('OrderController@index')}}?site=" + site + "&status=" + default_status;
        window.location.href = url;
      });
      // $('#status').change(function(){
      //   var str = $(this).val(); 
      //   alert(str);
      //   hideShippingStatus(str);
      // });
      $(document).on('change', 'input[name=status]', function() {
          var str = $("input[name=status]:checked").val();
          hideShippingStatus(str);
      });
      
      function hideShippingStatus(str){
        if(str == 'READY_TO_SHIP'){
          $('.shipping_status').show();
        }else{
          $('.shipping_status').hide();
        }
      }
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
  $(document).on("click", '.printPackingList', function () {
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
    var url = $(this).data("href") + "?ids=" + selected_ids.toString();
    var container = $(".view_modal");
    $.ajax({
      method: "GET",
      url: url,
      dataType: "html",
      success: function success(result) {
        $(container).html(result).modal("show");
      },
      error: function error(jqXhr, json, errorThrown) {
        console.log(jqXhr);
        console.log(json);
        console.log(errorThrown);
      }
    });
  });
  
  </script>
@endsection