@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Add Adjustment')

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
                  <h4 class="card-title">Adjustment Details</h4>
              </div>
              <div class="card-content">
                  <div class="card-body">
                      <form action="{{ action('AdjustmentController@store') }}" method="POST" id="add_adjustment_form" class="form" enctype="multipart/form-data">
                          @csrf
                          <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control datepicker update_input" name="date" value="{{date('m/d/Y')}}">
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
                                    <label>Warehouse</label>
                                    <div class="position-relative has-icon-left">
                                      <select name="warehouse_id" id="select_warehouse" class="form-control select2 update_select" placeholder="Select Warehouse">
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
                                    <table class="table" id="adjustment_item_tables">
                                      <thead>
                                        <tr>
                                          <th class="text-center" width="65%">Product (Code - Name)</th>
                                          <th class="text-center" width="10%">Current Stock</th>
                                          <th class="text-center" width="10%">Type</th>
                                          <th class="text-center" width="10%">Quantity</th>
                                          <th class="text-center" width="5%"><i class="feather icon-trash"></i></th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        
                                      </tbody>
                                      <tfoot>
                                        <tr>
                                          <th class="text-center text-muted text-sm">[Product (Code - Name)]</th>
                                          <th class="text-center text-muted text-sm">[Current Stock]</th>
                                          <th class="text-center text-muted text-sm">[Type]</th>
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
@section('vendor-script')
{{-- vednor js files --}}
<!-- vendor files -->
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
@endsection
@section('myscript')
<script type="text/javascript">
    jQuery(document).ready(function($) {
        var warehouse_reset = false;
        reloadAdjustment();

        $('#add_prodduct_input').on('focus', function() {
          var warehouse = $('#select_warehouse').val();
          if(!warehouse) {
            alert('Please select warehouse first!');
            $('#select_warehouse').focus();
          }
        });

        $('#select_warehouse').on('change', function() {
            if (warehouse_reset) {
              $("#adjustment_item_tables tbody").html('');
              localStorage.removeItem("adjustment_items");
            }
        });

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

        function reloadAdjustment() {
          reOrderItems("adjustment_items");
          warehouse_reset = false;
          var adjustment = JSON.parse(localStorage.getItem("adjustment"));
          if(adjustment) {
            $.each(adjustment, function(index, value){
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
            $('#add_adjustment_form').trigger('reset').trigger('change');
            $('.select2').trigger('change');
          }
          var adjustment_items = JSON.parse(localStorage.getItem("adjustment_items"));
          var html = '';
          $("#adjustment_item_tables tbody").html(html);
          $.each(adjustment_items.reverse(), function(i, data) {
            var qty = (data.quantity)?data.quantity:1;
            var select_addition = ' ';
            var select_subtraction = ' ';
            if (data.type == 'addition') {
              select_addition += ' selected';
            }
            else if(data.type == 'subtraction') {
              select_subtraction += ' selected';
            }
            if(data.max_quantity == 0) {
              select_subtraction += ' disabled';
            }
            html += '<tr data-id="'+i+'">'+
                      '<td>'+
                        '<div class="media">'+
                          '<img class="d-flex mr-1 product_image" src="'+data.image+'" alt="Generic placeholder image">'+
                          '<div class="media-body">'+
                            '<h5 class="mt-0">'+data.name+'</h5>'+
                            ((data.brand)?data.brand+'<br>':'')+
                            data.code+
                            '<input type="hidden" name="adjustment_item_array['+i+'][image]" value="'+data.image+'" />'+
                            '<input type="hidden" name="adjustment_item_array['+i+'][name]" value="'+data.name+'" />'+
                            '<input type="hidden" name="adjustment_item_array['+i+'][brand]" value="'+data.brand+'" />'+
                            '<input type="hidden" name="adjustment_item_array['+i+'][code]" value="'+data.code+'" />'+
                            '<input type="hidden" name="adjustment_item_array['+i+'][sku_id]" value="'+data.id+'" />'+
                          '</div>'+
                        '</div>'+
                      '</td>'+
                      '<td>'+
                        '<h4 class="text-center">'+data.max_quantity+'</h4>'+
                      '</td>'+
                      '<td>'+
                        '<div class="media">'+
                          '<select name="adjustment_item_array['+i+'][type]" data-array_name="type" class="form-control change_sku sku_select_tye" placeholder="Select Type">'+
                            '<option value="addition" '+select_addition+'>Addition</option>'+
                            '<option value="subtraction" '+select_subtraction+'>Subtraction</option>'+
                          '</select>'+
                        '</div>'+
                      '</td>'+
                      '<td>'+
                      '<div class="input-group">'+
                        '<div class="input-group-prepend d-none d-md-inline-block">'+
                          '<span class="input-group-text btn btn-sm btn-outline-secondary update_sku py-1" style="cursor:pointer" data-change="quantity" data-action="subtract"><i class="feather icon-minus" ></i></span>'+
                        '</div>'+
                        '<input type="number" name="adjustment_item_array['+i+'][quantity]" data-array_name="quantity" min="1" data-max="'+data.max_quantity+'" class="form-control text-right change_sku sku_input_quantity check_max_quantity" value="'+qty+'">'+
                        '<div class="input-group-append d-none d-md-inline-block h-100">'+
                          '<span class="input-group-text btn btn-sm btn-outline-secondary update_sku py-1" style="cursor:pointer" data-change="quantity" data-action="add"><i class="feather icon-plus" ></i></span>'+
                        '</div>'+
                      '</div>'+
                      '</td>'+
                      '<td><i class="feather icon-x remove_sku" style="cursor: pointer;"></i></td>'+
                    '</tr>';
          });
          $("#adjustment_item_tables tbody").prepend(html);
          warehouse_reset = true;
        }

        // Set the Options for "Bloodhound" suggestion engine
        var engine = new Bloodhound({
            remote: {
                url: '{{ route('sku.search_single') }}/%WAREHOUSE%/%QUERY%/none/false',
                replace: function(url, query) {
                    var wid = ($('#select_warehouse').val())?$('#select_warehouse').val():'none';
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
            var adjustment_items = {};
            if(localStorage.getItem("adjustment_items")) {
              adjustment_items = JSON.parse(localStorage.getItem("adjustment_items"));            
            }
            var item_index = adjustment_items.length;
            var list_item_index = Object.values(adjustment_items).findIndex((ai => ai.id == datum.id));
            if(list_item_index == -1) {
              adjustment_items[item_index] = {};
              adjustment_items[item_index]['id']  = datum.id;
              adjustment_items[item_index]['code']  = datum.code;
              adjustment_items[item_index]['name']  = datum.name;
              adjustment_items[item_index]['brand']  = datum.brand;
              adjustment_items[item_index]['quantity']  = 1;
              adjustment_items[item_index]['max_quantity']  = datum.quantity;
              adjustment_items[item_index]['image']  = datum.image;
              adjustment_items[item_index]['type']  = 'addition';
              item_index++;
            }
            else if(adjustment_items[list_item_index]['quantity'] < datum.quantity) {
              adjustment_items[list_item_index]['quantity']++;
            }
            localStorage.setItem("adjustment_items", JSON.stringify(adjustment_items));
            reloadAdjustment();
        });


        $(document).on('change', '.update_select', function() {
            var adjustment = {};
            if(localStorage.getItem("adjustment")) {
              adjustment = JSON.parse(localStorage.getItem("adjustment"));            
            }
            var name = $(this).attr('name');
            adjustment[name] = $(this).find('option:selected').val();
            localStorage.setItem("adjustment", JSON.stringify(adjustment)); 
        });

        $(document).on('change', '.update_input', function() {
            var adjustment = {};
            if(localStorage.getItem("adjustment")) {
              adjustment = JSON.parse(localStorage.getItem("adjustment"));            
            }
            var name = $(this).attr('name');
            adjustment[name] = $(this).val();
            localStorage.setItem("adjustment", JSON.stringify(adjustment));
        });

        $(document).on('change', '.change_sku', function() {
            var id = $(this).closest('tr').data('id');
            var input = $(this)
            var name = $(this).data('array_name');
            var val = $(this).val();
            var adjustment_items = JSON.parse(localStorage.getItem("adjustment_items")).reverse();
            adjustment_items[id][name] = val;
            localStorage.setItem("adjustment_items", JSON.stringify(adjustment_items.reverse()));
        });




        $(document).on('keyup change blur', '.sku_select_tye', function() {
            var type = $(this).closest('tr').find('input.check_max_quantity').trigger('change');
        });


        $(document).on('keyup change blur', '.check_max_quantity', function() {
            var max = parseInt($(this).data('max'));
            var val = parseInt($(this).val());
            var type = $(this).closest('tr').find('select.sku_select_tye').val();
            if(type == 'subtraction' && val > max) {
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
                if (type == 'subtraction') {
                  var max = input.data('max');
                  if(val < max) {
                    val++;
                  }
                }
                else {
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
            var adjustment_items = JSON.parse(localStorage.getItem("adjustment_items")).reverse();
            adjustment_items[id][change] = val;
            localStorage.setItem("adjustment_items", JSON.stringify(adjustment_items.reverse()));
        });

        $(document).on('click', '.remove_sku', function() {
            var id = $(this).closest('tr').data('id');
            var adjustment_items = JSON.parse(localStorage.getItem("adjustment_items")).reverse();
            delete adjustment_items[id];
            localStorage.setItem("adjustment_items", JSON.stringify(adjustment_items.reverse()));
            reloadAdjustment();
        });

        $('.datepicker').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true,
          minYear: 1901,
          maxYear: parseInt(moment().format('YYYY'),10)
        });

        $("#reset_btn").on('click', function() {
            localStorage.removeItem("adjustment_items");
            localStorage.removeItem("adjustment");
            reloadAdjustment();
        });

        $(".select2").select2({
            dropdownAutoWidth: true,
            width: '100%'
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
                      if(index == 'adjustment_item_array') {
                        $('#adjustment_item_tables').after('<label class="text-danger error">' + val + '</label>');
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
