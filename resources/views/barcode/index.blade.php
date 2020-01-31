@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Barcode')

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
</style>
  <section class="card">
    <div class="card-content">
      <div class="card-body">
        <div class="row">
        <div class="col-12">
            <div class="text-bold-600 font-medium-2">
             Scan Barcode:
            </div>
            <div class="form-group">
              <input type="text" id="barcode" class="form-control" autofocus>
              <p class="text-danger bolder clear_order" id="error_text"></p>
            </div>
        </div>
      </div>
    </div>
  </section>
  <section class="card">
    <div class="card-content">
      <div class="card-body">
        <h4 class="card-title">Order Details</h4>
        <div class="row">
          <div class="col-12">
            <table class="table">
              <thead>
                <tr>
                  <th>Order Number</th>
                  <th>Date</th>
                  <th>Payment</th>
                  <th>Price</th>
                  <th>Item Count</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td id="order_number" class="clear_order"></td>
                  <td id="date" class="clear_order"></td>
                  <td id="payment" class="clear_order"></td>
                  <td id="price" class="clear_order"></td>
                  <td id="item_count" class="clear_order"></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="card">
    <div class="card-content">
      <div class="card-body">
        <h4 class="card-title">Items</h4>
        <div class="row">
          <div class="col-12">
            <table class="table">
              <thead>
                <tr>
                  <th>Model</th>
                  <th>Image</th>
                  <th>Name</th>
                  <th>Quantity</th>
                </tr>
              </thead>
              <tbody id="items_list" class="clear_order">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
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
<script type="text/javascript">
  $("#barcode").on('focusout', function() {
    $(this).focus();
  });

  $("#barcode").on('change', function() {
    var val = $(this).val();
    $(".clear_order").html('');
    $(this).val('');
    $.ajax({
      type: "POST",
      url: '{{ route('barcode.checkBarcode') }}',
      data: {data:val},
      cache: false,
      success: function (result) {
        if(result.error) {
          $("#error_text").html(result.error);
        }
        else {
          var order = result.data.order;
          var items = result.data.items;
          console.log(items);
          $("#order_number").html("<h5>"+order.id+"</h5>");
          $("#date").html("<h5>"+order.created_at+"</h5>");
          $("#payment").html("<h5>"+order.payment_method+"</h5>");
          $("#price").html("<h5>"+order.price+"</h5>");
          $("#item_count").html("<h5>"+order.items_count+"</h5>");

          $.each(items, function(index, item) {
            $("#items_list").append(
              '<tr><td><h5>'+item.model+'</h5></td>'+
              '<td><img src="'+item.pic+'" class="product_image"></td>'+
              '<td><h5>'+item.name+'</h5></td>'+
              '<td><h4>x'+item.qty+'</h4></td></tr>'
              );
          });
        }
      }
    });
  });
</script>
@endsection