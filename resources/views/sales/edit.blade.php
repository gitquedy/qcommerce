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
                      <form action="{{ action('SalesController@update', $sales->id) }}" method="POST" id="add_sale_form" class="form" enctype="multipart/form-data">
                          @method('PUT')
                          @csrf
                          <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control datepicker update_input" name="date" value="{{ date('m/d/Y', strtotime($sales->date)) }}" readonly>
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
                                      <input type="text" class="form-control update_input" name="reference_no" placeholder="Reference No." value="{{$sales->reference_no}}">
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
                                        <option value="{{ $customer->id }}" @if($sales->customer_id == $customer->id) selected @endif>{{ $customer->formatName() }}</option>
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
                                        @foreach($sales->items as $item)
                                        <tr data-id="{{$item->sku_id}}">
                                          <td>
                                            <div class="media">
                                              <img src="{{$item->image}}" alt="No Image Available" class="d-flex mr-1 product_image">
                                              <div class="media-body">
                                                <h5 class="mt-0">{{$item->sku_name}}</h5>
                                                {{($item->brand)?$item->sku_name:''}}
                                                {{$item->sku_code}}
                                                <input type="hidden" name="sales_item_array[{{$item->id}}][image]" class="original_sku_image" value="{{$item->image}}" />
                                                <input type="hidden" name="sales_item_array[{{$item->id}}][name]" class="original_sku_name" value="{{$item->sku_name}}" />
                                                <input type="hidden" name="sales_item_array[{{$item->id}}][brand]" class="original_sku_brand" value="{{$item->sku_brand}}" /> 
                                                <input type="hidden" name="sales_item_array[{{$item->id}}][code]" class="original_sku_code" value="{{$item->sku_code}}" />
                                              </div>
                                            </div>
                                          </td>
                                          <td class="text-right p-4">
                                            <input type="hidden" name="sales_item_array[{{$item->id}}][real_unit_price]" class="original_sku_real_unit_price" value="{{$item->unit_price}}" />
                                            <input type="number" name="sales_item_array[{{$item->id}}][price]" class="form-control change_sku text-right sku_input_price original_sku_price" value="{{$item->unit_price}}">
                                          </td>
                                          <td>
                                            <div class="input-group">
                                              <div class="input-group-prepend d-none d-md-inline-block">
                                                <span class="input-group-text btn btn-sm btn-outline-secondary update_sku py-1" style="cursor:pointer" data-change="quantity" data-action="subtract"><i class="feather icon-minus" ></i></span>
                                              </div>
                                              <input type="number" name="sales_item_array[{{$item->id}}][quantity]" min="1" max="{{$item->sku->quantity + $item->quantity}}" class="form-control text-right change_sku sku_input_quantity check_max_quantity original_sku_quantity" value="{{$item->quantity}}">
                                              <div class="input-group-append d-none d-md-inline-block h-100">
                                                <span class="input-group-text btn btn-sm btn-outline-secondary update_sku py-1" style="cursor:pointer" data-change="quantity" data-action="add"><i class="feather icon-plus" ></i></span>
                                              </div>
                                            </div>
                                          </td>
                                        </tr>
                                        @endforeach
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
                            @if($sales->status == 'pending')
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <div class="position-relative has-icon-left">
                                      <select name="status" class="form-control select2 update_select" placeholder="Select Status">
                                        <option value="completed" @if($sales->status == "completed") selected @endif>Completed</option>
                                        <option value="pending" @if($sales->status == "pending") selected @endif>Pending</option>
                                        <option value="canceled" @if($sales->status == "canceled") selected @endif>Canceled</option>
                                      </select>
                                      <div class="form-control-position"> 
                                        <i class="feather icon-activity"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            @endif
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
        localStorage.removeItem("edit_items");
        localStorage.removeItem("edit_sales");
        localStorage.removeItem("original_edit_items");
        first_run();
        function first_run() {
          $('input.update_input').each(function() {
              var sales = {};
              if(localStorage.getItem("edit_sales")) {
                sales = JSON.parse(localStorage.getItem("edit_sales"));            
              }
              var name = $(this).attr('name');
              sales[name] = $(this).val();
              localStorage.setItem("edit_sales", JSON.stringify(sales));
          });
          $('select.update_select').each(function() {
              var sales = {};
              if(localStorage.getItem("edit_sales")) {
                sales = JSON.parse(localStorage.getItem("edit_sales"));            
              }
              var name = $(this).attr('name');
              sales[name] = $(this).find('option:selected').val();
              localStorage.setItem("edit_sales", JSON.stringify(sales)); 
          });
          $("#sales_item_tables > tbody > tr").each(function() {
            var items = {};
            if(localStorage.getItem("edit_items")) {
              items = JSON.parse(localStorage.getItem("edit_items"));            
            }
            var i = $(this).data('id');
            if(!items[i]) {
              items[i] = {};
              items[i]['id']  = i;
              items[i]['code']  = $(this).find('input.original_sku_code').val();
              items[i]['name']  = $(this).find('input.original_sku_name').val();
              items[i]['brand']  = $(this).find('input.original_sku_brand').val();
              items[i]['cost']  = $(this).find('input.original_sku_cost').val();
              items[i]['price']  = $(this).find('input.original_sku_price').val();
              items[i]['quantity']  = $(this).find('input.original_sku_quantity').val();
              items[i]['max_quantity']  = $(this).find('input.original_sku_quantity').attr("max");
              items[i]['image']  = $(this).find('img.product_image').attr('src');
            }
            localStorage.setItem("edit_items", JSON.stringify(items));
            localStorage.setItem("original_edit_items", JSON.stringify(items));
            reloadSales();
          })
        }


        $(window).keydown(function(event){
          if(event.keyCode == 13) {
            event.preventDefault();
            return false;
          }
        });

        function reloadSales() {
          var sales = JSON.parse(localStorage.getItem("edit_sales"));
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
          var items = JSON.parse(localStorage.getItem("edit_items"));
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
            minLength: 1,
        }, {
            source: engine.ttAdapter(),
            name: 'search-input',
            templates: {
                empty: [
                    '<div class="list-group search-results-dropdown"><div class="list-group-item">Nothing found.</div></div>'
                ],
                header: [
                    '<ul class="list-group search-results-dropdown w-100">'
                ],
                footer: [
                    '</ul>'
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
            if(localStorage.getItem("edit_items")) {
              items = JSON.parse(localStorage.getItem("edit_items"));            
            }
            if(localStorage.getItem("original_edit_items")) {
              original_items = JSON.parse(localStorage.getItem("original_edit_items"));            
            }
            var i = datum.id;
            if(original_items[i]) {
              var add_qty = original_items[i]['quantity'];
            }else {
              var add_qty = 0;
            }

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
            else if(items[i]['quantity'] < (parseInt(datum.quantity) + parseInt(add_qty))) {
              items[i]['quantity']++;
            }
            else {
            }
            localStorage.setItem("edit_items", JSON.stringify(items));
            reloadSales();
        });

        $(document).on('change', '.update_select', function() {
            var sales = {};
            if(localStorage.getItem("edit_sales")) {
              sales = JSON.parse(localStorage.getItem("edit_sales"));            
            }
            var name = $(this).attr('name');
            sales[name] = $(this).find('option:selected').val();
            localStorage.setItem("edit_sales", JSON.stringify(sales)); 
        });

        $(document).on('change', '.update_input', function() {
            var sales = {};
            if(localStorage.getItem("edit_sales")) {
              sales = JSON.parse(localStorage.getItem("edit_sales"));            
            }
            var name = $(this).attr('name');
            sales[name] = $(this).val();
            localStorage.setItem("edit_sales", JSON.stringify(sales));
        });

        $(document).on('change', '.change_sku', function() {
            var id = $(this).closest('tr').data('id');
            var input = $(this)
            var name = $(this).attr('name');
            var val = $(this).val();
            var items = JSON.parse(localStorage.getItem("edit_items"));
            items[id][name] = val;
            localStorage.setItem("edit_items", JSON.stringify(items));
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
            var items = JSON.parse(localStorage.getItem("edit_items"));
            items[id][change] = val;
            localStorage.setItem("edit_items", JSON.stringify(items));
            recalculate($(this).closest('tr'));
        });

        $(document).on('click', '.remove_sku', function() {
            var id = $(this).closest('tr').data('id');
            var items = JSON.parse(localStorage.getItem("edit_items"));
            delete items[id];
            localStorage.setItem("edit_items", JSON.stringify(items));
            reloadSales();
        });

        $('.datepicker').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true,
          minYear: 1901,
          maxYear: parseInt(moment().format('YYYY'),10)
        });

        $("#sale_reset").on('click', function() {
            localStorage.removeItem("edit_items");
            localStorage.removeItem("edit_sales");
            var origitems = JSON.parse(localStorage.getItem("original_edit_items"));
            localStorage.setItem("edit_items", JSON.stringify(origitems));
            reloadSales();
        });

        $('.select2').select2();

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
