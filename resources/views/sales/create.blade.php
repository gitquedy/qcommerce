@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Add Sale')

@section('mystyle')
<style>
    .product_image{
        width:70px;
        height:auto;
    }

    .input-group>.twitter-typeahead {
         position: relative;
         -ms-flex: 1 1 auto;
         -webkit-box-flex: 1;
         flex: 1 1 auto;
         width: 1%;
         margin-bottom: 0;
     }
     .input-group>.twitter-typeahead:not(:last-child) {
         border-top-left-radius: 0;
         border-bottom-left-radius: 0;
     }
     .input-group>.twitter-typeahead>.tt-input {
         border-top-left-radius: 0;
         border-bottom-left-radius: 0;
     }
     .form-control.tt-input:focus {
         z-index: 3
     }
     .form-control.tt-input {
         height: 100%;
     }


    input[type="date"]::-webkit-inner-spin-button,
    input[type="date"]::-webkit-calendar-picker-indicator {
        display: none;
        -webkit-appearance: none;
    }

     .form-control[readonly] {
         background-color: transparent;
      }
</style>
@endsection
@section('content')

<section id="floating-label-layouts">
  <div class="row match-height">
      <div class="col-md-12 col-12">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Sale Details</h4>
              </div>
              <div class="card-content">
                  <div class="card-body">
                      <form action="{{ action('SalesController@store') }}" method="POST" id="add_sale_form" class="form" enctype="multipart/form-data">
                          @csrf
                          <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control datepicker update_input" name="date" value="{{date('m/d/Y')}}" readonly>
                                      <div class="form-control-position"> 
                                        <i class="feather icon-calendar"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Referencce No.</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control update_input" name="reference_no" placeholder="Reference No.">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-hash"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Customer</label>
                                    <div class="position-relative has-icon-left">
                                      <select name="customer_id" id="select_customer" class="form-control select2 update_select" placeholder="Select Customer">
                                        <option value="" disabled selected></option>
                                        <option value="add_new">Add New Customer</option>
                                        @forelse($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->formatName() }}</option>
                                        @empty
                                        <option value="" disabled="">Please Add Customeer</option>
                                        @endforelse
                                      </select>
                                      <div class="form-control-position"> 
                                        <i class="feather icon-user"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                          </div>
                          <br>
                          <br>
                          <div class="row">
                            <div class="col-md-12">
                              <div class="card bg-white border-light">
                                <div class="card-body">
                                  <div class="input-group input-group-lg">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text btn-primary" id="inputGroup-sizing-lg"><i class="feather icon-list"></i></span>
                                    </div>
                                    <input type="text" class="form-control search-input" name="search_product" id="add_prodduct_input" aria-label="Large" aria-describedby="inputGroup-sizing-sm" placeholder="Please add products to order list" autofocus autocomplete="false">
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <br>
                          <div class="row">
                            <div class="col-md-12">
                              <div class="form-group">
                                  <label>Order Items</label>  
                                  <div class="table-responsive">
                                    <table class="table" id="sales_item_tables">
                                      <thead>
                                        <tr>
                                          <th class="text-center" width="55%">Product (Code - Name)</th>
                                          <th class="text-center" width="15%">Unit Price</th>
                                          <th class="text-center" width="10%">Quantity</th>
                                          <th class="text-center" width="15%">Subtotal (PHP)</th>
                                          <th class="text-center" width="5%"><i class="feather icon-trash"></i></th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        
                                      </tbody>
                                      <tfoot>
                                        <tr>
                                          <th class="text-center text-muted text-sm">[Product (Code - Name)]</th>
                                          <th class="text-center text-muted text-sm">[Unit Price]</th>
                                          <th class="text-center text-muted text-sm">[Quantity]</th>
                                          <th class="text-right font-weight-bold"><span class="mr-3">Total</span><span class="sales_total"></span></th>
                                          <th class="text-center text-muted"><i class="feather icon-trash"></i></th>
                                        </tr>
                                      </tfoot>
                                    </table>
                                  </div>
                              </div>
                            </div>
                          </div>
                          <br>
                          <br>
                          <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <div class="position-relative has-icon-left">
                                      <select name="status" class="form-control select2 update_select" placeholder="Select Status">
                                        <option value="completed">Completed</option>
                                        <option value="pending">Pending</option>
                                        <option value="canceled">Canceled</option>
                                      </select>
                                      <div class="form-control-position"> 
                                        <i class="feather icon-activity"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <label for="note">Note</label>
                              <textarea name="note" id="" cols="20" rows="5" class="form-control" placeholder="Note"></textarea>
                            </div>
                          </div>
                          <br>
                          <br>
                          <h4 class="card-title">Payment</h4>
                          <hr>
                          <div class="row">
                            <div class="col-md-4">
                              <div class="form-group">
                                  <label>Payment Referencce No.</label>
                                  <div class="position-relative has-icon-left">
                                    <input type="text" class="form-control update_input" name="payment_reference_no" placeholder="Reference No.">
                                    <div class="form-control-position"> 
                                      <i class="feather icon-hash"></i>
                                    </div>
                                  </div>
                              </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Paid Amount</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="number" name="paid" class="form-control update_input check_max_quantity" value="0" max="0">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-dollar-sign"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>Payment Type</label>
                                <div class="position-relative has-icon-left">
                                  <select name="payment_type" id="payment_type" class="form-control select2">
                                    <option value="cash">Cash</option>
                                    <option value="gift_certificate">Gift Card</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="deposit">Deposit</option>
                                    <option value="other">Other</option>
                                  </select>
                                  <div class="form-control-position"> 
                                    <i class="feather icon-credit-card"></i>
                                  </div>
                                </div>
                            </div>
                          </div>
                          <div class="credit_card payment_type_ext" style="display: none;">
                            <div class="row">
                              <div class="col-md-12">
                                <div class="card border-light">
                                  <div class="card-body">
                                    <div class="row">
                                      <div class="col-md-6">
                                        <label for="cc_no">Credit Card No.</label>
                                        <div class="position-relative has-icon-left">
                                          <input type="text" name="cc_no" class="form-control input-number" placeholder="Credit Card No.">
                                          <div class="form-control-position"> 
                                            <i class="feather icon-credit-card"></i>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-6">
                                        <label for="cc_no">Holder Name</label>
                                        <div class="position-relative has-icon-left">
                                          <input type="text" name="cc_holder" class="form-control" placeholder="Holder Name">
                                          <div class="form-control-position"> 
                                            <i class="feather icon-user"></i>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                      <div class="col-md-6">
                                        <label for="cc_type">Card Type</label>
                                        <div class="position-relative has-icon-left">
                                          <select name="cc_type" class="form-control select2">
                                            <option value="visa">Visa</option>
                                            <option value="mastercard">Master Card</option>
                                            <option value="amex">Amex</option>
                                            <option value="discover">Discover</option>
                                          </select>
                                          <div class="form-control-position"> 
                                            <i class="feather icon-credit-card"></i>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <label for="cc_no">Month</label>
                                        <div class="position-relative has-icon-left">
                                          <input type="text" name="cc_month" class="form-control input-number" placeholder="Month">
                                          <div class="form-control-position"> 
                                            <i class="feather icon-credit-card"></i>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <label for="cc_no">Year</label>
                                        <div class="position-relative has-icon-left">
                                          <input type="text" name="cc_year" class="form-control input-number" placeholder="Year">
                                          <div class="form-control-position"> 
                                            <i class="feather icon-credit-card"></i>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="cheque payment_type_ext" style="display: none;">
                            <div class="row">
                              <div class="col-md-12">
                                <div class="card border-light">
                                  <div class="card-body">
                                    <div class="row">
                                      <div class="col-md-6">
                                        <label for="cheque_no">Cheque No.</label>
                                        <div class="position-relative has-icon-left">
                                          <input type="text" name="cheque_no" class="form-control input-number" placeholder="Cheque No.">
                                          <div class="form-control-position"> 
                                            <i class="feather icon-credit-card"></i>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="gift_certificate payment_type_ext" style="display: none;">
                            <div class="row">
                              <div class="col-md-12">
                                <div class="card border-light">
                                  <div class="card-body">
                                    <div class="row">
                                      <div class="col-md-6">
                                        <label for="gift_card_no">Gift Card No.</label>
                                        <div class="position-relative has-icon-left">
                                          <input type="text" name="gift_card_no" class="form-control input-number" placeholder="Gift Card No.">
                                          <div class="form-control-position"> 
                                            <i class="feather icon-gift"></i>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <label for="note">Payment Note</label>
                              <textarea name="note" id="" cols="20" rows="5" class="form-control" placeholder="Note"></textarea>
                            </div>
                          </div>
                    <div class="form-group col-12">
                    </div>
                        <div class="row">
                          <div class="col-6">
                           <div class="col-12">
                                <input type="submit" name="save" class="btn btn-primary mr-1 mb-1 btn_save" value="Save">
                                <button type="reset" id="sale_reset" class="btn btn-danger mr-1 mb-1">Reset </button>
                            </div>
                          </div>
                        </div>
                      </form>
                  </div>

              </div>
          </div>
      </div>


  </div>
</section>
<!-- // Basic Floating Label Form section end -->
@endsection
@section('myscript')
<script type="text/javascript">
    jQuery(document).ready(function($) {
        reloadSales();

        $('#payment_type').on('change', function() {
          $('.payment_type_ext').hide('fast');
          $('.'+$(this).val()).show('fast');
        });

        $(window).keydown(function(event){
          if(event.keyCode == 13) {
            event.preventDefault();
            return false;
          }
        });

        function reloadSales() {
          var sales = JSON.parse(localStorage.getItem("sales"));
          if(sales) {
            $.each(sales, function(index, value){
                $('input[name='+index+']').val(value);
                $('select[name='+index+']').val(value).trigger('change');
                $('.datepicker').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    minYear: 1901,
                    maxYear: parseInt(moment().format('YYYY'),10),
                    setDate: null
                });
            });
          }
          else {
            $('#add_sale_form').trigger('reset').trigger('change');
            $('.select2').trigger('change');
          }
          var items = JSON.parse(localStorage.getItem("items"));
          var html = '';
          $("#sales_item_tables tbody").html(html);
          $.each(items, function(i, data) {
            var qty = (data.quantity)?data.quantity:1;
            var price = data.price;
            var sub_total = price * qty;
            html += '<tr data-id="'+i+'">'+
                      '<td>'+
                        '<div class="media">'+
                          '<img class="d-flex mr-1 product_image" src="'+data.image+'" alt="Generic placeholder image">'+
                          '<div class="media-body">'+
                            '<h5 class="mt-0">'+data.name+'</h5>'+
                            ((data.brand)?data.brand+'<br>':'')+
                            data.code+
                            '<input type="hidden" name="sales_item_array['+i+'][image]" value="'+data.image+'" />'+
                            '<input type="hidden" name="sales_item_array['+i+'][name]" value="'+data.name+'" />'+
                            '<input type="hidden" name="sales_item_array['+i+'][brand]" value="'+data.brand+'" />'+
                            '<input type="hidden" name="sales_item_array['+i+'][code]" value="'+data.code+'" />'+
                          '</div>'+
                        '</div>'+
                      '</td>'+
                      '<td class="text-right p-4">'+
                        // '<label class="label-price">'+data.price+'</label>'+
                        '<input type="hidden" name="sales_item_array['+i+'][real_unit_price]" value="'+data.price+'" />'+
                        '<input type="number" name="sales_item_array['+i+'][price]" class="form-control change_sku text-right sku_input_price" value="'+data.price+'">'+
                      '</td>'+
                      '<td>'+
                      '<div class="input-group">'+
                        '<div class="input-group-prepend d-none d-md-inline-block">'+
                          '<span class="input-group-text btn btn-sm btn-outline-secondary update_sku py-1" style="cursor:pointer" data-change="quantity" data-action="subtract"><i class="feather icon-minus" ></i></span>'+
                        '</div>'+
                        '<input type="number" name="sales_item_array['+i+'][quantity]" min="1" max="'+data.max_quantity+'" class="form-control text-right change_sku sku_input_quantity check_max_quantity" value="'+qty+'">'+
                        '<div class="input-group-append d-none d-md-inline-block h-100">'+
                          '<span class="input-group-text btn btn-sm btn-outline-secondary update_sku py-1" style="cursor:pointer" data-change="quantity" data-action="add"><i class="feather icon-plus" ></i></span>'+
                        '</div>'+
                      '</div>'+
                      '</td>'+

                      '<td class="text-right"><label class="sub_total">'+addCommas(sub_total.toFixed(2))+'</label></td>'+
                      '<td><i class="feather icon-x remove_sku" style="cursor: pointer;"></i></td>'+
                    '</tr>';
          });
          $("#sales_item_tables tbody").append(html);
          recalculateTotal();
        }

        function recalculate(tr) {
          var quantity = tr.find('input.sku_input_quantity').val();
          var price = tr.find('input.sku_input_price').val();
          var sub_total = price * quantity;
          tr.find('.sub_total').html(addCommas(sub_total.toFixed(2)));
          recalculateTotal();
        }

        function recalculateTotal() {
          var total = 0;
          $("#sales_item_tables > tbody > tr").each(function() {
            var i = $(this).data('id');
            var qty = ($(this).find('input.sku_input_price').val())?$(this).find('input.sku_input_price').val():1;
            var price = $(this).find('input.sku_input_quantity').val();
            var sub_total = price * qty;
            total += sub_total;
          })
          $("input[name=paid]").attr('max', total).trigger('change');
          $(".sales_total").html(addCommas(total.toFixed(2)));
        }


        function addCommas(x) {
            var parts = x.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return parts.join(".");
        }

        // Set the Options for "Bloodhound" suggestion engine
        var engine = new Bloodhound({
            remote: {
                url: '{{ route('sku.search') }}/%QUERY%/%CID%',
                replace: function(url, query) {
                    $cid = ($('#select_customer').val())?$('#select_customer').val():'none';
                    return url.replace('%QUERY%', query).replace('%CID%', $cid);
                }
            },
            datumTokenizer: Bloodhound.tokenizers.whitespace('search_product'),
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });

        $(".search-input").typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            source: engine.ttAdapter(),

            // This will be appended to "tt-dataset-" to form the class name of the suggestion menu.
            name: 'search-input',

            // the key from the array we want to display (name,id,email,etc...)
            templates: {
                empty: [
                    '<div class="list-group search-results-dropdown"><div class="list-group-item">Nothing found.</div></div>'
                ],
                header: [
                    '<ul class="list-group search-results-dropdown w-100">'
                ],
                suggestion: function (data) {
                    return '<li class="list-group-item list-group-item-action w-100">'+
                              '<div class="media">'+
                                '<img class="d-flex mr-1 product_image" src="'+data.image+'" alt="Generic placeholder image">'+
                                '<div class="media-body">'+
                                  '<h5 class="mt-0">'+data.name+'</h5>'+
                                  data.code+
                                '</div>'+
                              '</div>'+
                            '</li>'
          }
            }
        });

        $(".search-input").on('typeahead:selected', function (event, datum, name) {
            $(this).typeahead("val", "");
            var items = {};
            if(localStorage.getItem("items")) {
              items = JSON.parse(localStorage.getItem("items"));            
            }
            var i = datum.id;
            if(!items[i]) {
              items[i] = {};
              items[i]['id']  = datum.id;
              items[i]['code']  = datum.code;
              items[i]['name']  = datum.name;
              items[i]['brand']  = datum.brand;
              items[i]['cost']  = datum.cost;
              items[i]['price']  = datum.price;
              items[i]['quantity']  = 1;
              items[i]['max_quantity']  = datum.quantity;
              items[i]['image']  = datum.image;
            }
            else if(items[i]['quantity'] < datum.quantity) {
              items[i]['quantity']++;
            }
            else {
            }
            localStorage.setItem("items", JSON.stringify(items));
            reloadSales();
        });

        $(document).on('change', '.update_select', function() {
            var sales = {};
            if(localStorage.getItem("sales")) {
              sales = JSON.parse(localStorage.getItem("sales"));            
            }
            var name = $(this).attr('name');
            sales[name] = $(this).find('option:selected').val();
            localStorage.setItem("sales", JSON.stringify(sales)); 
        });

        $(document).on('change', '.update_input', function() {
            var sales = {};
            if(localStorage.getItem("sales")) {
              sales = JSON.parse(localStorage.getItem("sales"));            
            }
            var name = $(this).attr('name');
            sales[name] = $(this).val();
            localStorage.setItem("sales", JSON.stringify(sales));
        });

        $(document).on('change', '.change_sku', function() {
            var id = $(this).closest('tr').data('id');
            var input = $(this)
            var name = $(this).attr('name');
            var val = $(this).val();
            var items = JSON.parse(localStorage.getItem("items"));
            items[id][name] = val;
            localStorage.setItem("items", JSON.stringify(items));
            recalculate($(this).closest('tr'));
        });


        $(document).on('keyup change blur', '.check_max_quantity', function() {
            var max = parseInt($(this).attr('max'));
            var val = parseInt($(this).val());
            if(val > max) {
              $(this).val(max).trigger('change');
            }
        });

        $(document).on('click', '.update_sku', function() {
            var id = $(this).closest('tr').data('id');
            var change = $(this).data('change');
            var action = $(this).data('action')?$(this).data('action'):null;
            var input = $(this).closest('tr').find('input.sku_input_'+change);
            var val = input.val();
            switch(action) {
              case 'add': 
                var max = input.attr('max');
                  if(val < max) {
                    val++;
                  }
                break;
              case 'subtract':
                  if(val > 1) {
                    val--;
                  }
                break;
              case 'replace': 
                  val = $(this).val();
                break;
              default:
                break;
            }
            input.val(val);
            var items = JSON.parse(localStorage.getItem("items"));
            items[id][change] = val;
            localStorage.setItem("items", JSON.stringify(items));
            recalculate($(this).closest('tr'));
        });

        $(document).on('click', '.remove_sku', function() {
            var id = $(this).closest('tr').data('id');
            var items = JSON.parse(localStorage.getItem("items"));
            delete items[id];
            localStorage.setItem("items", JSON.stringify(items));
            reloadSales();
        });

        $('.datepicker').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true,
          minYear: 1901,
          maxYear: parseInt(moment().format('YYYY'),10)
        });

        $("#sale_reset").on('click', function() {
            localStorage.removeItem("items");
            localStorage.removeItem("sales");
            reloadSales();
        });

        $(".select2").select2({
            dropdownAutoWidth: true,
            width: '100%'
        });

        $('select[name=customer_id]').on('change', function() {
            var selected = $(this).find('option:selected').val();
            if(selected == 'add_new') {
              $.ajax({
                url :  "{{ route('customer.addCustomerModal') }}",
                type: "POST",
                success: function (response) {
                  if(response) {
                    $(".view_modal").html(response).modal('show');
                  }
                }
              });
              $(this).val('').trigger('change');
            } 
        });

    });


    $(function() {
      var button = 'save';
      $('input[type="submit"]').on('click', function(){
           button = this.name;
      });
      $(".form").submit(function(e) {
        e.preventDefault(); 
        
        if($('.btn_save').prop('disabled') == true){
          return false;
        }
         $('.btn_save').prop('disabled', true);
          $.ajax({
            url : $(this).attr('action'),
            type : 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(result){  
              console.log(result);
              if(result.success == true){
                toastr.success(result.msg);
                $('.error').remove();
                $("#sale_reset").trigger('click');
                setTimeout(function(){
                    window.location.replace(result.redirect);
                }, 1500);
              }else{
                if(result.msg){
                  toastr.error(result.msg);
                }
                 $('.error').remove();
                    $.each(result.error, function(index, val){
                      var elem = $('[name="'+ index +'"]');
                      if(index == 'sales_item_array') {
                        $('#sales_item_tables').after('<label class="text-danger error">Order Items are required to make a sale, Please add an item.</label>');
                      }
                      else if(elem.hasClass('select2-hidden-accessible')) {
                        new_elem = elem.parent().find('span.select2.select2-container')
                        new_elem.after('<label class="text-danger error">' + val + '</label>');
                      }
                      else {
                        elem.after('<label class="text-danger error">' + val + '</label>');
                      }
                    });
              }
              $('.btn_save').prop('disabled', false);
               },
              error: function(jqXhr, json, errorThrown){
                console.log(jqXhr);
                console.log(json);
                console.log(errorThrown);
                $('.btn_save').prop('disabled', false);
              }
          });
      });
    });
</script>
@endsection
