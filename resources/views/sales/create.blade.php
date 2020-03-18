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

     /*.list-group-item.tt-suggestion.tt-selectable:hover {
         background-color: #007fff;
     }*/
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
                      <form action="{{ action('SalesController@store') }}" method="POST" class="form" enctype="multipart/form-data">
                          @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="date" class="form-control" name="date" placeholder="Date">
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
                                      <input type="text" class="form-control" name="reference_no" placeholder="Reference No.">
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
                                      <select name="customer_id" class="form-control" placeholder="Select Customer">
                                        <option value=""></option>
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
                                    <table class="table data-list-view" id="sku_tables">
                                      <thead>
                                        <tr>
                                          <th width="55%">Product (Code - Name)</th>
                                          <th width="15%">Unit Price</th>
                                          <th width="10%">Quantity</th>
                                          <th width="15%">Subtotal (PHP)</th>
                                          <th width="5%"><i class="feather icon-trash"></i></th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        
                                      </tbody>
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
                                    <select name="status" class="form-control" placeholder="Select Status">
                                      <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Payment Status</label>
                                    <select name="status" class="form-control" placeholder="Select Status">
                                      <option value=""></option>
                                    </select>
                                </div>
                            </div>
                          </div>
                    <div class="form-group col-12">
                    </div>
                        <div class="row">
                          <div class="col-6">
                           <div class="col-12">
                                {{-- <input type="submit" name="save" class="btn btn-primary mr-1 mb-1 btn_save" value="Save"> --}}
                               <!--  <button type="reset" class="btn btn-outline-warning mr-1 mb-1">Reset --> </button>
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
  <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
@endsection
@section('myscript')
<script type="text/javascript">
    jQuery(document).ready(function($) {
        reloadTable();

        function reloadTable() {
          var items = JSON.parse(localStorage.getItem("items"));
          var html = '';
          $("#sku_tables tbody").html(html);
          $.each(items, function(i, data) {
            var qty = (data.quantity)?data.quantity:1;
            var price = (data.custom_price)?data.custom_price:data.price;
            var sub_total = price * qty;
            html += '<tr data-id="'+i+'">'+
                      '<td>'+
                        '<div class="media">'+
                          '<img class="d-flex mr-1 product_image" src="'+data.image+'" alt="Generic placeholder image">'+
                          '<div class="media-body">'+
                            '<h5 class="mt-0">'+data.name+'</h5>'+
                            ((data.brand)?data.brand+'<br>':'')+
                            data.code+
                          '</div>'+
                        '</div>'+
                      '</td>'+
                      '<td class="text-right p-4">'+
                        '<label class="label-price">'+data.price+'</label>'+
                        '<input type="hidden" name="price" class="form-control text-right" value="'+data.price+'">'+
                      '</td>'+
                      '<td>'+
                      '<div class="input-group">'+
                        '<div class="input-group-prepend d-none d-md-inline-block">'+
                          '<span class="input-group-text btn btn-sm btn-outline-secondary update_sku py-1" data-change="quantity" data-action="subtract"><i class="feather icon-minus" ></i></span>'+
                        '</div>'+
                        '<input type="number" name="quantity" min="1" max="'+data.quantity+'" class="form-control text-right change_sku" value="'+qty+'">'+
                        '<div class="input-group-append d-none d-md-inline-block h-100">'+
                          '<span class="input-group-text btn btn-sm btn-outline-secondary update_sku py-1" data-change="quantity" data-action="add"><i class="feather icon-plus" ></i></span>'+
                        '</div>'+
                      '</div>'+
                      '</td>'+

                      '<td class="text-right"><label class="sub_total">'+sub_total+'</label></td>'+
                      '<td><i class="feather icon-x remove_sku" style="cursor: pointer;"></i></td>'+
                    '</tr>';
          });
          $("#sku_tables tbody").append(html);
        }

        function recalculate(tr) {
          var quantity = tr.find('input[name=quantity]').val();
          var price = tr.find('input[name=price]').val();
          var sub_total = price * quantity;
          tr.find('.sub_total').html(sub_total);
        }
        // Set the Options for "Bloodhound" suggestion engine
        var engine = new Bloodhound({
            remote: {
                url: '{{ route('sku.search') }}/%QUERY%',
                wildcard: '%QUERY%'
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
            items = JSON.parse(localStorage.getItem("items"));
            var i = datum.id;
            if(items[i]) {
              items[i]['quantity']++;
            }
            else {
              items[i] = {};
              items[i]['id']  = datum.id;
              items[i]['code']  = datum.code;
              items[i]['name']  = datum.name;
              items[i]['brand']  = datum.brand;
              items[i]['cost']  = datum.cost;
              items[i]['price']  = datum.price;
              items[i]['quantity']  = 1;
              items[i]['image']  = datum.image;
            }
            localStorage.setItem("items", JSON.stringify(items));
            reloadTable();
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

        $(document).on('click', '.update_sku', function() {
            var change = $(this).data('change');
            var action = $(this).data('action')?$(this).data('action'):null;
            var id = $(this).closest('tr').data('id');
            var input = $(this).closest('tr').find('input[name='+change+']');
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
            reloadTable();
        });
    });
</script>
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
@endsection
