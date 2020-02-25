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
          <div class="col-sm-4">
            <ul class="list-unstyled mb-0">
              <li class="d-inline-block mr-2">
                <fieldset>
                 <div class="vs-radio-con">
                    <input type="radio" id="site" name="site"  value="lazada" {{ $request->get("site") == "lazada" ?  "checked" : ""}}>
                    <span class="vs-radio">
                      <span class="vs-radio--border"></span>
                      <span class="vs-radio--circle"></span>
                    </span>
                    <span class="">Lazada</span>
                  </div>
                </fieldset>
              </li>
              <li class="d-inline-block mr-2">
                <fieldset>
                  <div class="vs-radio-con">
                    <input type="radio" id="site" name="site" value="shopee" {{ $request->get('site') == 'shopee' ?  'checked' : ''}}>
                    <span class="vs-radio">
                      <span class="vs-radio--border"></span>
                      <span class="vs-radio--circle"></span>
                    </span>
                    <span class="">Shopee</span>
                  </div>
                </fieldset>
              </li>
            </ul>
          </div>
        </div>
        <br><div class="row">
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
                  <option value="{{ $status }}" {{ in_array($status, $selectedStatuses) ? 'selected' : '' }}>{{ ucwords(str_replace("_"," ", $status)) }}</option>
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
            <input type="hidden" id="printed" value="{{ $request->get('printed') ? true : false }}">
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4 col-12">
        </div>
        <div class="col-sm-4 col-12 shipping_status">
            <div class="text-bold-600 font-medium-2">
              Shipping Status:
            </div>
            <div class="form-group">
              <select name="shipping_status" id="shipping_status" class="select2 form-control selectFilter">
                <option value="All">All</option>
                <option value="to_process">To Process</option>
                <option value="processed">Processed</option>
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
     var id = "{{ $request->get('site') == 'shopee' ?  'ordersn' : 'id'  }}"
  var columnns = [
            { data: id, name: id, orderable : false},
            { data: id, name: id},
            { data: 'shop', name: 'shop.short_name'},
            { data: 'created_at_formatted', name: 'created_at' },
            // { data: 'created_at', name: 'created_at' },
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
                data.status = $("#status").val();
                data.timings = $("#timings").val();
                data.printed = $("#printed").val();
                data.site = $('input[name="site"]:checked').val();
                data.shipping_status =  $("#shipping_status").val();
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
    $(row).attr('data-action', "{{route('barcode.viewBarcode')}}");
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
        url = "{{ action('OrderController@index')}}?site=" + site;
        window.location.href = url;
      });
      $(".select2").select2({
        dropdownAutoWidth: true,
        width: '100%'
      });

      $('#status').change(function(){
        var str = $(this).val(); 
        hideShippingStatus(str);
      });

      function hideShippingStatus(str){
        if(str.includes('READY_TO_SHIP') == true){
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
