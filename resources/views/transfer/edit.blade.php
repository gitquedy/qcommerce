@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Transfer')

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
                  <h4 class="card-title">Transfer Details</h4>
              </div>
              <div class="card-content">
                  <div class="card-body">
                      <form action="{{ action('TransferController@update', $transfer->id) }}" method="POST" id="add_transfer_form" class="form" enctype="multipart/form-data">
                          @method('PUT')
                          @csrf
                          <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control datepicker update_input" name="date" value="{{ date('m/d/Y', strtotime($transfer->date)) }}" readonly>
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
                                      <input type="text" class="form-control update_input" name="reference_no" placeholder="Reference No." value="{{$transfer->reference_no}}">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-hash"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <div class="position-relative has-icon-left">
                                      <select name="status" id="status" class="form-control select2 update_select" placeholder="Status">
                                        <option value="completed" @if($transfer->status == 'completed') selected @endif>Completed</option>
                                        <option value="pending" @if($transfer->status == 'pending') selected @endif>Pending</option>
                                        <option value="sent" @if($transfer->status == 'sent') selected @endif>Sent</option>
                                      </select>
                                      <div class="form-control-position"> 
                                        <i class="feather icon-activity"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            <div class="offset-md-4 col-md-4">
                                <div class="form-group">
                                    <label>From Warehouse</label>
                                    <div class="position-relative has-icon-left">
                                      <select name="from_warehouse_id" id="select_from_warehouse" class="form-control select2 warehouse_select update_select" placeholder="Select Warehouse">
                                        <option value="" disabled selected></option>
                                        <option value="add_new">Add New Warehouse</option>
                                        @forelse($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" @if($transfer->from_warehouse_id == $warehouse->id) selected @endif>{{ $warehouse->name }}</option>
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>To Warehouse</label>
                                    <div class="position-relative has-icon-left">
                                      <select name="to_warehouse_id" id="select_to_warehouse" class="form-control select2 warehouse_select update_select" placeholder="Select Warehouse">
                                        <option value="" disabled selected></option>
                                        <option value="add_new">Add New Warehouse</option>
                                        @forelse($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" @if($transfer->to_warehouse_id == $warehouse->id) selected @endif>{{ $warehouse->name }}</option>
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
                                    <table class="table" id="transfer_item_tables">
                                      <thead>
                                        <tr>
                                          <th class="text-center" width="65%">Product (Code - Name)</th>
                                          <th class="text-center" width="15%">Current Stock</th>
                                          <th class="text-center" width="15%">Quantity</th>
                                          <th class="text-center" width="5%"><i class="feather icon-trash"></i></th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        @foreach($transfer->items as $item)
                                        <tr data-id="{{$item->sku_id}}">
                                          <td>
                                            <div class="media">
                                              <img src="{{$item->image}}" alt="No Image Available" class="d-flex mr-1 product_image">
                                              <div class="media-body">
                                                <h5 class="mt-0">{{$item->sku_name}}</h5>
                                                {{($item->brand)?$item->sku_name:''}}
                                                {{$item->sku_code}}
                                                <input type="hidden" name="transfer_item_array[{{$item->id}}][image]" class="original_sku_image" value="{{$item->image}}" />
                                                <input type="hidden" name="transfer_item_array[{{$item->id}}][name]" class="original_sku_name" value="{{$item->sku_name}}" />
                                                <input type="hidden" name="transfer_item_array[{{$item->id}}][brand]" class="original_sku_brand" value="{{$item->sku_brand}}" /> 
                                                <input type="hidden" name="transfer_item_array[{{$item->id}}][code]" class="original_sku_code" value="{{$item->sku_code}}" />
                                              </div>
                                            </div>
                                          </td>
                                          <td>
                                            @php
                                              $warehouse_qty = App\WarehouseItems::where('warehouse_id', $transfer->from_warehouse_id)->where('sku_id', $item->sku->id)->first()->quantity;
                                                $datamax = $warehouse_qty + $item->quantity;
                                            @endphp
                                            <h4 class="text-center"> {{$datamax}} </h4>
                                          </td>
                                          <td>
                                            <div class="input-group">
                                              <div class="input-group-prepend d-none d-md-inline-block">
                                                <span class="input-group-text btn btn-sm btn-outline-secondary update_sku py-1" style="cursor:pointer" data-change="quantity" data-action="subtract"><i class="feather icon-minus" ></i></span>
                                              </div>
                                              <input type="number" name="transfer_item_array[{{$item->id}}][quantity]" min="1" data-max="{{$item->from_warehouse_item->quantity + $item->quantity}}" class="form-control text-right change_sku sku_input_quantity check_max_quantity original_sku_quantity" value="{{$item->quantity}}">
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
                                          <th class="text-center text-muted text-sm">[Current Stock]</th>
                                          <th class="text-center text-muted text-sm">[Quantity]</th>
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
                            <div class="col-md-12">
                              <label for="note">Note</label>
                              <textarea name="note" id="" cols="20" rows="5" class="form-control" placeholder="Note">{{$transfer->note}}</textarea>
                            </div>
                          </div>
                          <br>
                          <br>
                    <div class="form-group col-12">
                    </div>
                        <div class="row">
                          <div class="col-6">
                           <div class="col-12">
                                <input type="submit" name="save" class="btn btn-primary mr-1 mb-1 btn_save" value="Save">
                                <button type="reset" id="transfer_reset" class="btn btn-danger mr-1 mb-1">Reset </button>
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
        localStorage.removeItem("edit_transfer_items");
        localStorage.removeItem("edit_transfer");
        localStorage.removeItem("original_edit_transfer_items");
        var warehouse_reset = false;
        first_run();

        $('.warehouse_select').on('change', function() {
            if (warehouse_reset) {
              if($('#select_to_warehouse').val() == $('#select_from_warehouse').val()) {
                $('#select_to_warehouse').val('').trigger('change');
                alert('Please select different warehouse');
              }
              $("#transfer_item_tables tbody").html('');
              localStorage.removeItem("transfer_items");
            }
        });

        function first_run() {
          $('input.update_input').each(function() {
              var transfer = {};
              if(localStorage.getItem("edit_transfer")) {
                transfer = JSON.parse(localStorage.getItem("edit_transfer"));            
              }
              var name = $(this).attr('name');
              transfer[name] = $(this).val();
              localStorage.setItem("edit_transfer", JSON.stringify(transfer));
          });
          $('select.update_select').each(function() {
              var transfer = {};
              if(localStorage.getItem("edit_transfer")) {
                transfer = JSON.parse(localStorage.getItem("edit_transfer"));            
              }
              var name = $(this).attr('name');
              transfer[name] = $(this).find('option:selected').val();
              localStorage.setItem("edit_transfer", JSON.stringify(transfer)); 
          });
          $("#transfer_item_tables > tbody > tr").each(function() {
            var items = {};
            if(localStorage.getItem("edit_transfer_items")) {
              items = JSON.parse(localStorage.getItem("edit_transfer_items"));            
            }
            var i = $(this).data('id');
            if(!items[i]) {
              items[i] = {};
              items[i]['id']  = i;
              items[i]['code']  = $(this).find('input.original_sku_code').val();
              items[i]['name']  = $(this).find('input.original_sku_name').val();
              items[i]['brand']  = $(this).find('input.original_sku_brand').val();
              items[i]['quantity']  = $(this).find('input.original_sku_quantity').val();
              items[i]['max_quantity']  = $(this).find('input.original_sku_quantity').data('max');
              items[i]['image']  = $(this).find('img.product_image').attr('src');
            }
            localStorage.setItem("edit_transfer_items", JSON.stringify(items));
            localStorage.setItem("original_edit_transfer_items", JSON.stringify(items));
            reloadTransfer();
          })
          warehouse_reset = true;
        }


        $(window).keydown(function(event){
          if(event.keyCode == 13) {
            event.preventDefault();
            return false;
          }
        });

        function reloadTransfer() {
          warehouse_reset = false;
          var transfer = JSON.parse(localStorage.getItem("edit_transfer"));
          if(transfer) {
            $.each(transfer, function(index, value){
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
            $('#add_transfer_form').trigger('reset').trigger('change');
            $('.select2').trigger('change');
          }
          var items = JSON.parse(localStorage.getItem("edit_transfer_items"));
          var html = '';
          $("#transfer_item_tables tbody").html(html);
          $.each(items, function(i, data) {
            var qty = (data.quantity)?data.quantity:1;
            var datamax = parseInt(data.max_quantity);
            html += '<tr data-id="'+i+'">'+
                      '<td>'+
                        '<div class="media">'+
                          '<img class="d-flex mr-1 product_image" src="'+data.image+'" alt="Generic placeholder image">'+
                          '<div class="media-body">'+
                            '<h5 class="mt-0">'+data.name+'</h5>'+
                            ((data.brand)?data.brand+'<br>':'')+
                            data.code+
                            '<input type="hidden" name="transfer_item_array['+i+'][image]" value="'+data.image+'" />'+
                            '<input type="hidden" name="transfer_item_array['+i+'][name]" value="'+data.name+'" />'+
                            '<input type="hidden" name="transfer_item_array['+i+'][brand]" value="'+data.brand+'" />'+
                            '<input type="hidden" name="transfer_item_array['+i+'][code]" value="'+data.code+'" />'+
                          '</div>'+
                        '</div>'+
                      '</td>'+
                      '<td>'+
                        '<h4 class="text-center">'+((datamax > 0)?datamax:0)+'</h4>'+
                      '</td>'+
                      '<td>'+
                      '<div class="input-group">'+
                        '<div class="input-group-prepend d-none d-md-inline-block">'+
                          '<span class="input-group-text btn btn-sm btn-outline-secondary update_sku py-1" style="cursor:pointer" data-change="quantity" data-action="subtract"><i class="feather icon-minus" ></i></span>'+
                        '</div>'+
                        '<input type="number" name="transfer_item_array['+i+'][quantity]" data-array_name="quantity" min="1" data-max="'+datamax+'" class="form-control text-right change_sku sku_input_quantity check_max_quantity" value="'+qty+'">'+
                        '<div class="input-group-append d-none d-md-inline-block h-100">'+
                          '<span class="input-group-text btn btn-sm btn-outline-secondary update_sku py-1" style="cursor:pointer" data-change="quantity" data-action="add"><i class="feather icon-plus" ></i></span>'+
                        '</div>'+
                      '</div>'+
                      '</td>'+
                      '<td><i class="feather icon-x remove_sku" style="cursor: pointer;"></i></td>'+
                    '</tr>';
          });
          $("#transfer_item_tables tbody").append(html);
          warehouse_reset = true;
        }

        function addCommas(x) {
            var parts = x.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return parts.join(".");
        }

        // Set the Options for "Bloodhound" suggestion engine
        var engine = new Bloodhound({
            remote: {
                url: '{{ route('sku.search_single') }}/%WAREHOUSE%/%QUERY%/none/true',
                replace: function(url, query) {
                    var wid = ($('#select_from_warehouse').val())?$('#select_from_warehouse').val():'none';
                    return url.replace('%WAREHOUSE%', wid).replace('%QUERY%', query);
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
            if(localStorage.getItem("edit_transfer_items")) {
              items = JSON.parse(localStorage.getItem("edit_transfer_items"));            
            }
            if(localStorage.getItem("original_edit_transfer_items")) {
              original_items = JSON.parse(localStorage.getItem("original_edit_transfer_items"));            
            }
            var i = datum.id;
            if(original_items[i]) {
              datum.quantity = parseInt(original_items[i]['max_quantity']);
            }

            if(!items[i]) {
              items[i] = {};
              items[i]['id']  = datum.id;
              items[i]['code']  = datum.code;
              items[i]['name']  = datum.name;
              items[i]['brand']  = datum.brand;
              items[i]['cost']  = datum.cost;
              items[i]['quantity']  = 1;
              items[i]['max_quantity']  = datum.quantity;
              items[i]['image']  = datum.image;
            }
            else if(items[i]['quantity'] < parseInt(datum.quantity)) {
              items[i]['quantity']++;
            }
            else {
            }
            localStorage.setItem("edit_transfer_items", JSON.stringify(items));
            reloadTransfer();
        });

        $(document).on('change', '.update_select', function() {
            var transfer = {};
            if(localStorage.getItem("edit_transfer")) {
              transfer = JSON.parse(localStorage.getItem("edit_transfer"));            
            }
            var name = $(this).attr('name');
            transfer[name] = $(this).find('option:selected').val();
            localStorage.setItem("edit_transfer", JSON.stringify(transfer)); 
        });

        $(document).on('change', '.update_input', function() {
            var transfer = {};
            if(localStorage.getItem("edit_transfer")) {
              transfer = JSON.parse(localStorage.getItem("edit_transfer"));            
            }
            var name = $(this).attr('name');
            transfer[name] = $(this).val();
            localStorage.setItem("edit_transfer", JSON.stringify(transfer));
        });

        $(document).on('change', '.change_sku', function() {
            var id = $(this).closest('tr').data('id');
            var input = $(this)
            var name = $(this).data('array_name');
            var val = $(this).val();
            var transfer_items = JSON.parse(localStorage.getItem("edit_transfer_items"));
            transfer_items[id][name] = val;
            localStorage.setItem("edit_transfer_items", JSON.stringify(transfer_items));
        });

        $(document).on('keyup change blur', '.sku_select_tye', function() {
            var type = $(this).closest('tr').find('input.check_max_quantity').trigger('change');
        });

        $(document).on('keyup change blur', '.check_max_quantity', function() {
            var max = parseInt($(this).data('max'));
            var val = parseInt($(this).val());
            var type = $(this).closest('tr').find('select.sku_select_tye').val();
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
                  var max = input.data('max');
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
            input.val(val).trigger('change');
            var transfer_items = JSON.parse(localStorage.getItem("edit_transfer_items"));
            transfer_items[id][change] = val;
            localStorage.setItem("edit_transfer_items", JSON.stringify(transfer_items));
        });

        $(document).on('click', '.remove_sku', function() {
            var id = $(this).closest('tr').data('id');
            var items = JSON.parse(localStorage.getItem("edit_transfer_items"));
            delete items[id];
            localStorage.setItem("edit_transfer_items", JSON.stringify(items));
            reloadTransfer();
        });

        $('.datepicker').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true,
          minYear: 1901,
          maxYear: parseInt(moment().format('YYYY'),10)
        });

        $("#transfer_reset").on('click', function() {
            localStorage.removeItem("edit_transfer_items");
            localStorage.removeItem("edit_transfer");
            var origitems = JSON.parse(localStorage.getItem("original_edit_transfer_items"));
            localStorage.setItem("edit_transfer_items", JSON.stringify(origitems));
            reloadTransfer();
        });

        $('.select2').select2();
        $('.warehouse_select').on('change', function() {
            var selected = $(this).find('option:selected').val();
            if(selected == 'add_new') {
              $.ajax({
                url :  "{{ route('warehouse.addWarehouseModal') }}",
                type: "POST",
                data: {id: $(this).attr('id')},
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
            beforeSend:  function() {
              Swal.fire({
                title: 'Please Wait !',
                html: 'Transfering Items',// add html attribute if you want or remove
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
                $("#transfer_reset").trigger('click');
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
                      if(index == 'transfer_item_array') {
                        $('#transfer_item_tables').after('<label class="text-danger error">' + val + '</label>');
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
                toastr.error('error');
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
