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
  <div class="card-header">
    <h4 class="card-title">Filter </h4>
  </div>
    <div class="card-content">
      <div class="card-body">
        <div class="row">
          <div class="col-sm-12 shop_filter">
              <label for="site1" class="btn btn-lg btn-outline-primary mb-1 {{ $request->get("site") == "lazada" ?  "active" : ""}}">
                <img class="shop_logo" src="{{asset('images/shop/icon/lazada.png')}}" alt="">
                Lazada
                <span id="badge_lazada_total" class="badge badge-secondary"></span>
              </label>
              <label for="site2" class="btn btn-lg btn-outline-primary mb-1 {{ $request->get('site') == 'shopee' ?  'active' : ''}}">
                <img class="shop_logo" src="{{asset('images/shop/icon/shopee.png')}}" alt="">
                Shopee
                <span id="badge_shopee_total" class="badge badge-secondary"></span>
              </label>
              <input type="radio" id="site1" name="site" value="lazada"  {{ $request->get("site") == "lazada" ?  "checked" : ""}}>
              <input type="radio" id="site2" name="site" value="shopee"  {{ $request->get('site') == 'shopee' ?  'checked' : ''}}>
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
                  <input type="radio" name="status" id="status_{{ $status }}"  class="selectFilter" value="{{ $status }}"  {{ ($status == $selectedStatus) ? 'checked' : '' }} autocomplete="off"> {{ ucwords(str_replace("_"," ", $status)) }}
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
            @include('order.components.dateFilter')
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
            <!-- <th>Seller</th> -->
            <th>Order Date</th>
            <th>Last Update</th>
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
  <script type="text/javascript">
    function getHeaders(){
        $.ajax({
        method: "GET",
        url: "{{ action('OrderController@headers')  }}?site={{ $request->get('site') }}",
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
     var id = "{{ $request->get('site') == 'shopee' ?  'ordersn' : 'id'  }}"
  var columnns = [
            { data: id, name: id, orderable : false},
            { data: 'idDisplay', name: 'idDisplay'},
            { data: 'created_at_formatted', name: 'created_at' },
            { data: 'updated_at_at_human_read', name: 'updated_at_at_human_read' },
            { data: 'payment_method', name: 'payment_method' },
            { data: 'price', name: 'price' },
            { data: 'items_count', name: 'items_count' },
            { data: 'statusDisplay', name: 'status' },
            { data: 'actions', name: 'actions', orderable : false },
        ];
  var table_route = {
          url: '{{ route('order.index') }}',
          data: function (data) {
                data.shop = $("#shop").val();
                data.status = $("input[name=status]:checked").val();
                data.timings = $("#timings").val();
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
  }
  var aLengthMenu = [[20, 50, 100, 500],[20, 50, 100, 500]];
  var pageLength = 20;
</script>
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
      var str = $('#status').val(); 
      hideShippingStatus(str);
      $('input[name="site"]').change(function(){
        var site = $('input[name="site"]:checked').val();
        @if (isset($_GET['printed']))
          site += "&printed=false";
        @endif
        var default_status = '';
        if(site == 'lazada'){
          default_status = 'pending';
        }else{
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
