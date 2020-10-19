@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Add Transfer')

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
                      <form action="{{ action('TransferController@store') }}" method="POST" id="add_transfer_form" class="form" enctype="multipart/form-data">
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
                                    <label>Status</label>
                                    <div class="position-relative has-icon-left">
                                      <select name="status" id="status" class="form-control select2 update_select" placeholder="Status">
                                        <option value="completed">Completed</option>
                                        <option value="pending">Pending</option>
                                        <option value="sent">Sent</option>
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
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
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
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
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
                                          <th class="text-center" width="80%">Product (Code - Name)</th>
                                          <th class="text-center" width="10%">Current Stock</th>
                                          <th class="text-center" width="15%">Quantity</th>
                                          <th class="text-center" width="5%"><i class="feather icon-trash"></i></th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        
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
                              <textarea name="note" id="" cols="20" rows="5" class="form-control" placeholder="Note"></textarea>
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
                            <button type="reset" id="reset_btn" class="btn btn-danger mr-1 mb-1">Reset </button>
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
        var warehouse_reset = false;
        reloadTransfer();

        $('#add_prodduct_input').on('focus', function() {
          var warehouse = $('#select_from_warehouse').val();
          if(!warehouse) {
            alert('Please select from warehouse first!');
            $('#select_from_warehouse').focus();
          }
        });

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

        $(window).keydown(function(event){
          if(event.keyCode == 13) {
            event.preventDefault();
            return false;
          }
        });

        function reloadTransfer() {
          warehouse_reset = false;
          var transfer = JSON.parse(localStorage.getItem("transfer"));
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
          var transfer_items = JSON.parse(localStorage.getItem("transfer_items"));
          var html = '';
          $("#transfer_item_tables tbody").html(html);
          $.each(transfer_items, function(i, data) {
            var qty = (data.quantity)?data.quantity:1;
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
                        '<h4 class="text-center">'+data.max_quantity+'</h4>'+
                      '</td>'+
                      '<td>'+
                      '<div class="input-group">'+
                        '<div class="input-group-prepend d-none d-md-inline-block">'+
                          '<span class="input-group-text btn btn-sm btn-outline-secondary update_sku py-1" style="cursor:pointer" data-change="quantity" data-action="subtract"><i class="feather icon-minus" ></i></span>'+
                        '</div>'+
                        '<input type="number" name="transfer_item_array['+i+'][quantity]" data-array_name="quantity" min="1" data-max="'+data.max_quantity+'" class="form-control text-right change_sku sku_input_quantity check_max_quantity" value="'+qty+'">'+
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

        // Set the Options for "Bloodhound" suggestion engine
        var engine = new Bloodhound({
            remote: {
                url: '{{ route('sku.search') }}/%WAREHOUSE%/%QUERY%/none/true',
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
            var transfer_items = {};
            if(localStorage.getItem("transfer_items")) {
              transfer_items = JSON.parse(localStorage.getItem("transfer_items"));            
            }
            var i = datum.id;
            if(!transfer_items[i]) {
              transfer_items[i] = {};
              transfer_items[i]['id']  = datum.id;
              transfer_items[i]['code']  = datum.code;
              transfer_items[i]['name']  = datum.name;
              transfer_items[i]['brand']  = datum.brand;
              transfer_items[i]['quantity']  = 1;
              transfer_items[i]['max_quantity']  = datum.quantity;
              transfer_items[i]['image']  = datum.image;
              transfer_items[i]['type']  = 'addition';
            }
            else if(transfer_items[i]['quantity'] < datum.quantity) {
              transfer_items[i]['quantity']++;
            }
            else {
            }
            localStorage.setItem("transfer_items", JSON.stringify(transfer_items));
            reloadTransfer();
        });

        $(document).on('change', '.update_select', function() {
            var transfer = {};
            if(localStorage.getItem("transfer")) {
              transfer = JSON.parse(localStorage.getItem("transfer"));            
            }
            var name = $(this).attr('name');
            transfer[name] = $(this).find('option:selected').val();
            localStorage.setItem("transfer", JSON.stringify(transfer)); 
        });

        $(document).on('change', '.update_input', function() {
            var transfer = {};
            if(localStorage.getItem("transfer")) {
              transfer = JSON.parse(localStorage.getItem("transfer"));            
            }
            var name = $(this).attr('name');
            transfer[name] = $(this).val();
            localStorage.setItem("transfer", JSON.stringify(transfer));
        });

        $(document).on('change', '.change_sku', function() {
            var id = $(this).closest('tr').data('id');
            var input = $(this)
            var name = $(this).data('array_name');
            var val = $(this).val();
            var transfer_items = JSON.parse(localStorage.getItem("transfer_items"));
            transfer_items[id][name] = val;
            localStorage.setItem("transfer_items", JSON.stringify(transfer_items));
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
            var type = $(this).closest('tr').find('select.sku_select_tye').val();
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
            var transfer_items = JSON.parse(localStorage.getItem("transfer_items"));
            transfer_items[id][change] = val;
            localStorage.setItem("transfer_items", JSON.stringify(transfer_items));
        });

        $(document).on('click', '.remove_sku', function() {
            var id = $(this).closest('tr').data('id');
            var transfer_items = JSON.parse(localStorage.getItem("transfer_items"));
            delete transfer_items[id];
            localStorage.setItem("transfer_items", JSON.stringify(transfer_items));
            reloadTransfer();
        });

        $('.datepicker').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true,
          minYear: 1901,
          maxYear: parseInt(moment().format('YYYY'),10)
        });

        $("#reset_btn").on('click', function() {
            localStorage.removeItem("transfer_items");
            localStorage.removeItem("transfer");
            reloadTransfer();
        });

        $(".select2").select2({
            dropdownAutoWidth: true,
            width: '100%'
        });

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
            success: function(result){  
              console.log(result);
              if(result.success == true){
                toastr.success(result.msg);
                $('.error').remove();
                $("#reset_btn").trigger('click');
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
