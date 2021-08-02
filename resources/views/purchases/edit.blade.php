@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Purchase')

@section('vendor-style')
        {{-- vendor files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

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
                  <h4 class="card-title">Purchase Details</h4>
              </div>
              <div class="card-content">
                  <div class="card-body">
                      <form action="{{ action('PurchasesController@update', $purchase->id) }}" method="POST" id="add_purchase_form" class="form" enctype="multipart/form-data">
                          @csrf
                          @method('put')
                          <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control datepicker update_input" name="date" value="{{ date('m/d/Y', strtotime($purchase->date)) }}" readonly>
                                      <div class="form-control-position"> 
                                        <i class="feather icon-calendar"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Referencce No.</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control update_input" name="reference_no" placeholder="Reference No." value="{{$purchase->reference_no}}">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-hash"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Warehouse</label>
                                    <div class="position-relative has-icon-left">
                                      <select name="warehouse_id" id="select_warehouse" class="form-control select2 update_select" placeholder="Select Warehouse">
                                        <option value="" disabled selected></option>
                                        <option value="add_new">Add New Warehouse</option>
                                        @forelse($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ $warehouse->id == $purchase->warehouse_id ? 'selected' : ''  }}>{{ $warehouse->name }}</option>
                                        @empty
                                        <option value="" disabled="">Please Add Warehouse</option>
                                        @endforelse
                                      </select>
                                      <div class="form-control-position"> 
                                        <i class="feather icon-box"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                          <!--   <div class="col-md-3">
                                <div class="form-group">
                                    <label>Supplier</label>
                                    <div class="position-relative has-icon-left">
                                      <select name="supplier_id" id="select_supplier" class="form-control select2 update_select" placeholder="Select Supplier">
                                        <option value="" disabled selected></option>
                                        <option value="add_new">Add New Supplier</option>
                                        @forelse($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->company }}</option>
                                        @empty
                                        <option value="" disabled="">Please Add Supplier</option>
                                        @endforelse
                                      </select>
                                      <div class="form-control-position"> 
                                        <i class="feather icon-user"></i>
                                      </div>
                                    </div>
                                </div>
                            </div> -->
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
                                          <th class="text-center" width="15%">Unit Cost</th>
                                          <th class="text-center" width="10%">Quantity</th>
                                          <th class="text-center" width="15%">Subtotal (PHP)</th>
                                          <th class="text-center" width="5%"><i class="feather icon-trash"></i></th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        @foreach($purchase->items as $index => $item)
                                        <tr data-id="{{$index}}">
                                          <td>
                                            <div class="media">
                                              <img src="{{$item->image}}" alt="No Image Available" class="d-flex mr-1 product_image">
                                              <div class="media-body">
                                                <h5 class="mt-0">{{$item->sku_name}}</h5>
                                                {{($item->brand)?$item->sku_name:''}}
                                                {{$item->sku_code}}
                                                <input type="hidden" name="purchases_items_array[{{$index}}][image]" class="original_sku_image" value="{{$item->image}}" />
                                                <input type="hidden" name="purchases_items_array[{{$index}}][name]" class="original_sku_name" value="{{$item->sku_name}}" />
                                                <input type="hidden" name="purchases_items_array[{{$index}}][brand]" class="original_sku_brand" value="{{$item->sku_brand}}" /> 
                                                <input type="hidden" name="purchases_items_array[{{$index}}][code]" class="original_sku_code" value="{{$item->sku_code}}" />
                                                <input type="hidden" name="purchases_items_array[{{$index}}][sku_id]" class="original_sku_sku_id" value="{{$item->sku_id}}" />
                                              </div>
                                            </div>
                                          </td>
                                          <td class="text-right p-4">
                                            <input type="hidden" name="purchases_items_array[{{$index}}][real_unit_price]" class="original_sku_real_unit_price" value="{{$item->unit_price}}" />
                                            <input type="number" name="purchases_items_array[{{$index}}][price]" class="form-control change_sku text-right sku_input_price original_sku_price" value="{{$item->unit_price}}">
                                          </td>
                                          <td>
                                            <div class="input-group">
                                              <div class="input-group-prepend d-none d-md-inline-block">
                                                <span class="input-group-text btn btn-sm btn-outline-secondary update_sku py-1" style="cursor:pointer" data-change="quantity" data-action="subtract"><i class="feather icon-minus" ></i></span>
                                              </div>
                                              <input type="number" name="purchases_items_array[{{$index}}][quantity]" min="1" class="form-control text-right change_sku sku_input_quantity original_sku_quantity" value="{{$item->quantity}}">
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
                                          <th class="text-center text-muted text-sm">[Unit Cost]</th>
                                          <th class="text-center text-muted text-sm">[Quantity]</th>
                                          <th class="text-right font-weight-bold"><span class="mr-3">Total</span><span class="sales_total"></span></th>
                                          <th class="text-center text-muted"><i class="feather icon-trash"></i></th>
                                        </tr>
                                        <tr>
                                          <th class="text-center text-muted text-sm"></th>
                                          <th class="text-center text-muted text-sm"></th>
                                          <th class="text-center text-muted text-sm"></th>
                                          <th class="text-right font-weight-bold"><span class="mr-3">Shipping Fee</span><span class="shipping_fee"></span></th>
                                        </tr>
                                        <tr>
                                          <th class="text-center text-muted text-sm"></th>
                                          <th class="text-center text-muted text-sm"></th>
                                          <th class="text-center text-muted text-sm"></th>
                                          <th class="text-right font-weight-bold"><span class="mr-3">Other Fees</span><span class="other_fee"></span></th>
                                        </tr>
                                        <tr>
                                          <th class="text-center text-muted text-sm"></th>
                                          <th class="text-center text-muted text-sm"></th>
                                          <th class="text-center text-muted text-sm"></th>
                                          <th class="text-right font-weight-bold"><span class="mr-3">Discount</span><span class="discount_fee"></span></th>
                                        </tr>
                                        <tr>
                                          <th class="text-center text-muted text-sm"></th>
                                          <th class="text-center text-muted text-sm"></th>
                                          <th class="text-center text-muted text-sm"></th>
                                          <th class="text-right font-weight-bold"><span class="mr-3">Grand Total</span><span class="grand_total"></span><input type="hidden" name="grand_total"></th>
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
                                        <option value="received" {{ $purchase->status == 'received' ? 'selected' : '' }}>Received</option>
                                        <option value="pending" {{ $purchase->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="ordered" {{ $purchase->status == 'ordered' ? 'selected' : '' }}>Ordered</option>
                                      </select>
                                      <div class="form-control-position"> 
                                        <i class="feather icon-activity"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Shipping Fee</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="number" name="shipping_fee" class="form-control update_input update_footer" placeholder="Shipping Fee" value="{{ $purchase->shipping_fee }}">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-truck"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Other Fee's</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="number" name="other_fees" class="form-control update_input update_footer" placeholder="Other Fee's" value="{{ $purchase->other_fees }}">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-dollar-sign"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                              <div class="form-group">
                                    <label>Discount Amount</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="number" name="discount" class="form-control update_input update_footer" placeholder="Discount Amount" value="{{ $purchase->discount }}">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-dollar-sign"></i>
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
@section('vendor-script')
{{-- vednor js files --}}
<!-- vendor files -->
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
@endsection
@section('myscript')
<script type="text/javascript">
    jQuery(document).ready(function($) {
        var warehouse_reset = false;

        $('#add_prodduct_input').on('focus', function() {
          var supplier = $('#select_supplier').val();
          var warehouse = $('#select_warehouse').val();
          if(!supplier && !warehouse) {
            alert('Please select supplier and warehouse first!');
            $('#select_warehouse').focus();
          }
          else {
            if(!supplier) {
              alert('Please select supplier first!');
              $('#select_supplier').focus();
            }
            if(!warehouse) {
              alert('Please select warehouse first!');
              $('#select_warehouse').focus();
            }
          }
        });

        $('#select_warehouse').on('change', function() {
            if (warehouse_reset) {
              $("#sales_item_tables tbody").html('');
              localStorage.removeItem("edit_purchase_items");
            }
        });

        $('#select_warehouse').on('change', function() {
            $("#adjustment_item_tables tbody").html('');
        });

        $('#payment_type').on('change', function() {
          $('.payment_type_ext').hide('fast');
          $('.'+$(this).val()).show('fast');
        });

        localStorage.removeItem("edit_purchase_items");
        localStorage.removeItem("edit_purchase");
        first_run();
        function first_run(){
          $('input.update_input').each(function() {
              var purchase = {};
              if(localStorage.getItem("edit_purchase")) {
                purchase = JSON.parse(localStorage.getItem("edit_purchase"));            
              }
              var name = $(this).attr('name');
              purchase[name] = $(this).val();
              localStorage.setItem("edit_purchase", JSON.stringify(purchase));
          });
          $('select.update_select').each(function() {
              var purchase = {};
              if(localStorage.getItem("edit_purchase")) {
                purchase = JSON.parse(localStorage.getItem("edit_purchase"));            
              }
              var name = $(this).attr('name');
              purchase[name] = $(this).find('option:selected').val();
              localStorage.setItem("edit_purchase", JSON.stringify(purchase)); 
          });

          $("#sales_item_tables > tbody > tr").each(function() {
            var items = {};
            if(localStorage.getItem("edit_purchase_items")) {
              items = JSON.parse(localStorage.getItem("edit_purchase_items"));            
            }
            var i = $(this).data('id');
            if(!items[i]) {
              items[i] = {};
              items[i]['id']  = $(this).find('input.original_sku_sku_id').val();
              items[i]['sku_id']  = $(this).find('input.original_sku_sku_id').val();
              items[i]['code']  = $(this).find('input.original_sku_code').val();
              items[i]['name']  = $(this).find('input.original_sku_name').val();
              items[i]['brand']  = $(this).find('input.original_sku_brand').val();
              items[i]['cost']  = $(this).find('input.original_sku_price').val();
              items[i]['price']  = $(this).find('input.original_sku_price').val();
              items[i]['quantity']  = $(this).find('input.original_sku_quantity').val();
              items[i]['image']  = $(this).find('img.product_image').attr('src');
            }
            localStorage.setItem("edit_purchase_items", JSON.stringify(items));
            reloadSales();
          })
          warehouse_reset = true;
        }

        


        $(window).keydown(function(event){
          if(event.keyCode == 13) {
            event.preventDefault();
            return false;
          }
        });

        function reOrderItems(stored_items) {
          var items = JSON.parse(localStorage.getItem(stored_items));
          var item_list = []
          var index = 0;
          $.each(items, function(i, data) {
              if(data) {
                item_list[index] = data;
                index++;
              }
          });
          localStorage.setItem(stored_items, JSON.stringify(item_list));
        }

        function reloadSales() {
          warehouse_reset = false;
          var purchase = JSON.parse(localStorage.getItem("edit_purchase"));
          if(purchase) {
            $.each(purchase, function(index, value){
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
            $('#add_purchase_form').trigger('reset').trigger('change');
            $('.select2').trigger('change');
          }
          reOrderItems("edit_purchase_items");
          var edit_purchase_items = JSON.parse(localStorage.getItem("edit_purchase_items"));
          var html = '';
          $("#sales_item_tables tbody").html(html);
          console.log(edit_purchase_items);
          $.each(edit_purchase_items.reverse(), function(i, data) {
            var qty = (data.quantity)?data.quantity:1;
            var cost = data.cost;
            var sub_total = cost * qty;
            html += '<tr data-id="'+i+'">'+
                      '<td>'+
                        '<div class="media">'+
                          '<img class="d-flex mr-1 product_image" src="'+data.image+'" alt="Generic placeholder image">'+
                          '<div class="media-body">'+
                            '<h5 class="mt-0">'+data.name+'</h5>'+
                            ((data.brand)?data.brand+'<br>':'')+
                            data.code+
                            '<input type="hidden" name="purchases_items_array['+i+'][image]" value="'+data.image+'" />'+
                            '<input type="hidden" name="purchases_items_array['+i+'][name]" value="'+data.name+'" />'+
                            '<input type="hidden" name="purchases_items_array['+i+'][brand]" value="'+data.brand+'" />'+
                            '<input type="hidden" name="purchases_items_array['+i+'][code]" value="'+data.code+'" />'+
                            '<input type="hidden" name="purchases_items_array['+i+'][sku_id]" value="'+data.id+'" />'+
                          '</div>'+
                        '</div>'+
                      '</td>'+
                      '<td class="text-right p-4">'+
                        // '<label class="label-price">'+data.price+'</label>'+
                        '<input type="hidden" name="purchases_items_array['+i+'][real_unit_price]" value="'+data.cost+'" />'+
                        '<input type="number" name="purchases_items_array['+i+'][price]" data-array_name="cost" class="form-control change_sku text-right sku_input_price" value="'+data.cost+'">'+
                      '</td>'+
                      '<td>'+
                      '<div class="input-group">'+
                        '<div class="input-group-prepend d-none d-md-inline-block">'+
                          '<span class="input-group-text btn btn-sm btn-outline-secondary update_sku py-1" style="cursor:pointer" data-change="quantity" data-action="subtract"><i class="feather icon-minus" ></i></span>'+
                        '</div>'+
                        '<input type="number" name="purchases_items_array['+i+'][quantity]" data-array_name="quantity" min="1" class="form-control text-right change_sku sku_input_quantity" value="'+qty+'">'+
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
          warehouse_reset = true;
        }

        function recalculate(tr) {
          var quantity = tr.find('input.sku_input_quantity').val();
          var cost = tr.find('input.sku_input_price').val();
          var sub_total = cost * quantity;
          tr.find('.sub_total').html(addCommas(sub_total.toFixed(2)));
          recalculateTotal();
        }

        function recalculateTotal() {
          var total = 0;
          $("#sales_item_tables > tbody > tr").each(function() {
            var i = $(this).data('id');
            var cost = ($(this).find('input.sku_input_price').val())?$(this).find('input.sku_input_price').val():1;
            var qty = $(this).find('input.sku_input_quantity').val();
            var sub_total = cost * qty;
            total += sub_total;
          })
          var shipping_fee = isNaN(parseFloat($("input[name=shipping_fee]").val())) == true ? '' : parseFloat($("input[name=shipping_fee]").val());
          var other_fees = isNaN(parseFloat($("input[name=other_fees]").val())) == true ? '' : parseFloat($("input[name=other_fees]").val());
          var discount = isNaN(parseFloat($("input[name=discount]").val())) == true ? '' : parseFloat($("input[name=discount]").val());
          var grand_total = (total + shipping_fee + other_fees) - discount;

          $('input[name=grand_total]').val(grand_total);
          $(".shipping_fee").html("+" + addCommas(shipping_fee.toFixed(2)));
          $(".other_fee").html("+" + addCommas(other_fees.toFixed(2)));
          $(".discount_fee").html("-" + addCommas(discount.toFixed(2)));
          $(".grand_total").html(addCommas(grand_total.toFixed(2)));
          $("input[name=paid]").attr('max', grand_total).trigger('change');
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
                url: '{{ route('sku.searchPurchase') }}/%QUERY%/',
                replace: function(url, query) {
                    var wid = ($('#select_warehouse').val())?$('#select_warehouse').val():'none';
                    var cid = ($('#select_supplier').val())?$('#select_supplier').val():'none';
                    return url.replace('%QUERY%', query);
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
            var edit_purchase_items = {};
            if(localStorage.getItem("edit_purchase_items")) {
              edit_purchase_items = JSON.parse(localStorage.getItem("edit_purchase_items"));            
            }
            var item_index = edit_purchase_items.length;
            var list_item_index = Object.values(edit_purchase_items).findIndex((si => si.id == datum.id));
            if(list_item_index == -1) {
              edit_purchase_items[item_index] = {};
              edit_purchase_items[item_index]['id']  = datum.id;
              edit_purchase_items[item_index]['code']  = datum.code;
              edit_purchase_items[item_index]['name']  = datum.name;
              edit_purchase_items[item_index]['brand']  = datum.brand;
              edit_purchase_items[item_index]['cost']  = datum.cost;
              edit_purchase_items[item_index]['price']  = datum.price;
              edit_purchase_items[item_index]['quantity']  = 1;
              edit_purchase_items[item_index]['image']  = datum.image;
            }
            else if(edit_purchase_items[list_item_index]['quantity'] < datum.quantity) {
              edit_purchase_items[list_item_index]['quantity']++;
            }
            localStorage.setItem("edit_purchase_items", JSON.stringify(edit_purchase_items));
            reloadSales();
        });

        $(document).on('change', '.update_select', function() {
            var purchase = {};
            if(localStorage.getItem("edit_purchase")) {
              purchase = JSON.parse(localStorage.getItem("edit_purchase"));            
            }
            var name = $(this).attr('name');
            purchase[name] = $(this).find('option:selected').val();
            localStorage.setItem("purchase", JSON.stringify(purchase)); 
        });

        $(document).on('change', '.update_input', function() {
            var purchase = {};
            if(localStorage.getItem("edit_purchase")) {
              purchase = JSON.parse(localStorage.getItem("edit_purchase"));            
            }
            var name = $(this).attr('name');
            purchase[name] = $(this).val();
            localStorage.setItem("purchase", JSON.stringify(purchase));
        });

        $(document).on('change', '.change_sku', function() {
            var id = $(this).closest('tr').data('id');
            var input = $(this)
            var name = $(this).data('array_name');
            var val = $(this).val();
            var edit_purchase_items = JSON.parse(localStorage.getItem("edit_purchase_items")).reverse();
            edit_purchase_items[id][name] = val;
            localStorage.setItem("edit_purchase_items", JSON.stringify(edit_purchase_items.reverse()));
            recalculate($(this).closest('tr'));
        });


        $(document).on('keyup change blur', '.check_max_quantity', function() {
            var max = parseInt($(this).data('max'));
            var val = parseInt($(this).val());
            if(val > max) {
              $(this).val(max).trigger('change');
              console.log("check_max_quantity");
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
                    val++;
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
            input.val(val).trigger('change');
            var edit_purchase_items = JSON.parse(localStorage.getItem("edit_purchase_items")).reverse();
            edit_purchase_items[id][change] = val;
            localStorage.setItem("edit_purchase_items", JSON.stringify(edit_purchase_items.reverse()));
            recalculate($(this).closest('tr'));
        });

        $(document).on('click', '.remove_sku', function() {
            var id = $(this).closest('tr').data('id');
            var edit_purchase_items = JSON.parse(localStorage.getItem("edit_purchase_items")).reverse();
            delete edit_purchase_items[id];
            localStorage.setItem("edit_purchase_items", JSON.stringify(edit_purchase_items.reverse()));
            reloadSales();
        });

        $('.datepicker').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true,
          minYear: 1901,
          maxYear: parseInt(moment().format('YYYY'),10)
        });

        $("#sale_reset").on('click', function() {
            localStorage.removeItem("edit_purchase_items");
            localStorage.removeItem("edit_purchase");
            reloadSales();
        });

        $(".select2").select2({
            dropdownAutoWidth: true,
            width: '100%'
        });

        $('select[name=supplier_id]').on('change', function() {
            var selected = $(this).find('option:selected').val();
            if(selected == 'add_new') {
              $.ajax({
                url :  "{{ route('supplier.addSupplierModal') }}",
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

        
        $('select[name=warehouse_id]').on('change', function() {
            var selected = $(this).find('option:selected').val();
            if(selected == 'add_new') {
              $.ajax({
                url :  "{{ route('warehouse.addWarehouseModal') }}",
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

        $('.update_footer').change(function(){
          recalculateTotal();
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
            beforeSend:  function() {
              Swal.fire({
                title: 'Please Wait !',
                html: 'Updating Purchases',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                    Swal.showLoading()
                },
              });
            },
            success: function(result){  
              Swal.close();
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
                      if(index == 'purchases_items_array') {
                        $('#sales_item_tables').after('<label class="text-danger error">' + val + '</label>');
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
                Swal.close();
                toastr.error(json+': '+errorThrown);
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
