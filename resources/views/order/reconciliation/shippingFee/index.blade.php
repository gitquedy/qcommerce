@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Shipping Fee Reconciliation')

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

<section class="card">
    <div class="card-content">
      <div class="card-body">
        <div class="row">
          <div class="col-sm-12 shop_filter">
              <label class="btn btn-lg round btn-outline-primary {{ $request->get('tab') == 'all' ? 'active' : '' }}">
                <input type="radio" name="tab" value="all"  {{ $request->get('tab') == 'all' ? 'checked' : '' }}>
                <p>Total Overcharged</p>
                <p class="text-warning text-bold-400 font-large-1"><span id="header_total">0</span> Orders</p>
              </label>
              
              <label class="btn btn-lg round btn-outline-primary {{ $request->get('tab') == 'pending' ? 'active' : '' }}">
                <input type="radio" name="tab" value="pending" {{ $request->get('tab') == 'pending' ? 'checked' : '' }}>
                <p>Pending Overcharged</p>
                <p class="text-warning text-bold-400 font-large-1"><span id="header_pending">0</span> Orders</p>
              </label>
            
              <label class="btn btn-lg round btn-outline-primary {{ $request->get('tab') == 'filed' ? 'active' : '' }}">
                <input type="radio" name="tab" value="filed" {{ $request->get('tab') == 'filed' ? 'checked' : '' }}>
                <p>Filed Dispute</p>
                <p class="text-warning text-bold-400 font-large-1"><span id="header_filed">0</span> Orders</p>
              </label>

              <label class="btn btn-lg round btn-outline-primary {{ $request->get('tab') == 'resolved' ? 'active' : '' }}">
                <input type="radio" name="tab" value="resolved" {{ $request->get('tab') == 'resolved' ? 'checked' : '' }}>
                <p>Resolved Overcharged</p>
                <p class="text-warning text-bold-400 font-large-1"><span id="header_resolved">0</span> Orders</p>
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
      <div class="row">
        <div class="col-12">
          <a target="_blank" class="btn btn-info" href="https://xform.lazada.com.ph/form/show.do?spm=a2a15.helpcenter-psc-contact.new-navigation.8.2ef25331K09vKG&lang=en"><i class="fa fa-file"></i> File Dispute here</a>
        </div>
      </div>
    </div>
  </section>  

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
            <a class="dropdown-item reconcile" data-href="{{ action('ShippingFeeController@massReconcile') }}" data-action="filed"><i class="fa fa-file"></i> Filed</a>
            <a class="dropdown-item reconcile" data-href="{{ action('ShippingFeeController@massReconcile') }}" data-action="resolved"><i class="fa fa-handshake-o"></i> Resolved</a>
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
            <th>Order ID</th>
            <th>Payment Period</th>
            <th>Actual Shipping Fee</th>
            <th>Shipping Paid by Customer</th>
            <th>Shipping Paid by Seller</th>
            <th>Overcharge</th>
            <th>Status</th>
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
  <script type="text/javascript">
    function getHeaders(){
      function getParams(){
        var $params = "?shop=" + $("#shop").val() + "&daterange=" + $("#daterange").val();
        return $params;
      }
        $.ajax({
        method: "GET",
        url: "{{ action('ShippingFeeController@headers')  }}" + getParams(),
        success: function success(result) {
            $('#header_total').html(result.data.total);
            $('#header_pending').html(result.data.pending);
            $('#header_filed').html(result.data.filed);
            $('#header_resolved').html(result.data.resolved);
          },
        });     
      }
  </script>
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
            { data: 'idDisplay', name: 'idDisplay' ,orderable : false},
            { data: 'seller_payout_fees.statement', name: 'seller_payout_fees.statement' },
            { data: 'shipping_fee', name: 'shipping_fee' },
            { data: 'shipping_by_customer', name: 'shipping_by_customer' },
            { data: 'shipping_by_seller', name: 'shipping_by_seller' },
            { data: 'overcharge', name: 'overcharge' },
            { data: 'shipping_fee_reconciled', name: 'shipping_fee_reconciled', },
            { data: 'action', name: 'action' }
        ];
  var table_route = {
          url: '{{ route('shippingfee.index') }}',
          data: function (data) {
                data.shop = $("#shop").val();
                data.daterange = $("#daterange").val();
                data.tab = $('input[name="tab"]:checked').val();
            }
  };
  var buttons = [];
  function created_row_function(row, data, dataIndex){
    $(row).attr('data-id', JSON.parse(data.id));
    $(row).attr('data-action', "{{route('barcode.viewBarcode')}}");
  }
  var aLengthMenu = [[4, 10, 15, 20],[4, 10, 15, 20]];
  var pageLength = 10;

  function draw_callback_function(settings){
    getHeaders();
  }
</script>
  <script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
<script>
  $(document).ready(function(){
      $('.view_modal').on('hidden.bs.modal', function () {
        table.ajax.reload();
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

      $('input[name="tab"]').change(function(){
        var tab = $('input[name="tab"]:checked').val();
        url = "{{ action('ShippingFeeController@index')}}?tab=" + tab;
        window.location.href = url;
      });

      $(document).on('click', '.chip-closeable',function() {
        var target = $(this).data('target');
        if($(this).data('type') == "multiple") {
          var value = $("#"+target).val().split(',');
          const index = value.indexOf($(this).data('value').toString());
          if (index > -1) {
            value.splice(index, 1);
          }
          $("#"+target).val(value.join(',')).trigger('change');
        }
        else {
          $("#"+target).val('').trigger('change');
        }
      });

      getHeaders(); // on load get headers

  });  
  </script>
@endsection
