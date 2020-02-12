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
        width:80px;
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
  <form id="packed_form" method="POST" class="form" enctype='multipart/form-data'>
    @method('POST')
    @csrf
    <input type="hidden" name="shop_id" id="shop_id">
  <section class="card">
    <div class="card-content">
      <div class="card-body">
        <h4 class="card-title">Customer Name</h4>
        <div class="row">
          <div class="col-12">
            <table class="table">
              <thead>
                <tr>
                  {{-- 
                  <th>Customer Name</th>
                  <th>Date</th>
                  <th>Payment</th>
                  <th>Price</th>
                  <th>Item Count</th> --}}
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td id="customer_name" class="clear_order"></td>
                  {{-- 
                  <td id="order_number" class="clear_order"></td>
                  <td id="date" class="clear_order"></td>
                  <td id="payment" class="clear_order"></td>
                  <td id="price" class="clear_order"></td>
                  <td id="item_count" class="clear_order"></td> --}}
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
  <section id="packed_div" class="card" style="display: none;">
    <div class="card-content">
      <div class="card-body">
        <button type="submit" id="packed_button" class="btn btn-lg btn-primary btn-block">Packed</button>
      </div>
    </div>
  </section>
</form>
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

  $("#packed_button").on('click', function(e) {
      e.preventDefault();
      const packed_alert = Swal.mixin({
        customClass: {
          confirmButton: 'btn btn-lg btn-primary',
          cancelButton: 'btn btn-lg btn-danger'
        },
        buttonsStyling: true
      })

      packed_alert.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, items are Packed!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
      }).then((result) => {
        if (result.value) {
          $.ajax({
            type: "POST",
            url: '{{ route('barcode.packedItems') }}',
            data: $("#packed_form").serialize(),
            dataType: "JSON",
            cache: false,
            success: function (result) {
                packed_alert.fire(
                  'Packed!',
                  'Item(s) have beed deducted on SKU Quantity.',
                  'success'
                )
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) { 
                packed_alert.fire(
                  'Warning',
                  'Something went wrong :(',
                  'error'
                )
            }  
          });
          
        }
      })
      

  });

  // $("#packed_button").on('click', function() {
  //   var order_id = $(this).data('id');
  //     const packed_alert = Swal.mixin({
  //       customClass: {
  //         confirmButton: 'btn btn-lg btn-primary',
  //         cancelButton: 'btn btn-lg btn-danger'
  //       },
  //       buttonsStyling: true
  //     })

  //     packed_alert.fire({
  //       title: 'Are you sure?',
  //       text: "You won't be able to revert this!",
  //       icon: 'warning',
  //       showCancelButton: true,
  //       confirmButtonText: 'Yes, items are Packed!',
  //       cancelButtonText: 'No, cancel!',
  //       reverseButtons: true
  //     }).then((result) => {
  //       if (result.value) {
  //         packed_alert.fire(
  //           'Packed!',
  //           'Item(s) have beed deducted on SKU Quantity.',
  //           'success'
  //         )
  //       } else if (
  //         /* Read more about handling dismissals below */
  //         result.dismiss === Swal.DismissReason.cancel
  //       ) {
  //         packed_alert.fire(
  //           'Cancelled',
  //           'Your imaginary file is safe :)',
  //           'error'
  //         )
  //       }
  //     })
  // });

  $("#barcode").on('change', function() {
    $("#packed_button").attr('data-id', '');
    $("#packed_div").hide();
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
          $("#customer_name").html("<h5>"+order.customer_first_name+"</h5>");
          $("#shop_id").val(order.shop_id);
          // $("#order_number").html("<h5>"+order.id+"</h5>");
          // $("#date").html("<h5>"+order.created_at+"</h5>");
          // $("#payment").html("<h5>"+order.payment_method+"</h5>");
          // $("#price").html("<h5>"+order.price+"</h5>");
          // $("#item_count").html("<h5>"+order.items_count+"</h5>");
          $("#packed_button").attr('data-id', order.id);
          $("#packed_div").show();
          $.each(items, function(index, item) {
            $("#items_list").append(
              '<tr><td><img src="'+item.pic+'" class="product_image"></td>'+
              '<td><p>'+item.name+'</p></td>'+
              '<td><h4><input type="hidden" name="items['+item.sku+']" value="'+item.qty+'" />x'+item.qty+'</h4></td></tr>'
              );
          });
        }
      }
    });
  });
</script>
@endsection
