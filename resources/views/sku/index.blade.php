@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Inventory')

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
    
    option[disabled]{
        background-color:#F8F8F8;
    }
</style>

<section class="card">
  <div class="card-header">
    <h4 class="card-title">Filter </h4>
  </div>
    <div class="card-content">
      <div class="card-body">
        <div class="row">
          <div class="col-sm-12 shop_filter">
            @foreach($all_sites as $site)
              <label for="{{ $site }}" class="btn btn-lg btn-outline-primary mb-1 {{ $request->get('site') == $site ?  'active' : ''}}">
                <img class="shop_logo" src="{{asset('images/shop/icon/'.$site.'.png')}}" alt="">
                {{ ucfirst($site) }}
                <span id="badge_{{ $site }}_total" class="badge badge-secondary"></span>
              </label>
              <input type="radio" id="{{ $site }}" name="site" value="{{ $site }}"  {{ $request->get("site") == $site ?  "checked" : ""}}>
            @endforeach
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-sm-12">
            @include('order.components.shopFilter')
            <div class="btn-group" id="chip_area_shop"></div>
          </div>
        </div>
      </div>
    </div>
  </section>

<section id="data-list-view" class="data-list-view-header">
    <div class="action-btns d-none">
      <div class="btn-dropdown mr-1 mb-1">
        <div class="btn-group dropdown actions-dropodown">
          <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Actions
          </button>
          <div class="dropdown-menu">
            <!--<a class="dropdown-item" onclick="mass_copy()"  > Duplicate</a>-->
            <!--<a class="dropdown-item" href="#">Print</a>-->
            <a class="dropdown-item massAction" href="#" data-action="{{ route('sku.bulkremove') }}"> Delete</a>
            <a class="dropdown-item massAction" href="#" data-action="{{ route('sku.syncSkuProducts') }}">Sync Products</a>
            <a class="dropdown-item" target="_blank" href="{{ route('sku.export') }}">Export Csv</a>
          </div>
        </div>
      </div>
      <div class="form-check form-check-inline filter-checkbox">
        <input class="form-check-input" type="checkbox" name="stocks" id="stocks" value="stocks">
        <label class="form-check-label" for="stocks">Show only items with stocks</label>
      </div>
    </div>


    <div class="additional_custom_filter">
      <div class="dataTables_length" id="DataTables_Table_0_warehouse">
        <label>
          <select name="warehouse" class="selectFilter custom-select custom-select-sm form-control form-control-sm" id="warehouse">
            <option value="">All Warehouse</option>
            @foreach($all_warehouse as $warehouse)
              <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
            @endforeach
          </select>
        </label>
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
            <th>SKU Code</th>
            <th>Image</th>
            <th>Product Name</th>
            <!-- <th>Brand</th> -->
            <!-- <th>Category</th> -->
            <!-- <th>Supplier</th> -->
            <th>Linked Shop</th>
            <th>Cost</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Warehouse Quantity</th>
            <th>Alert Quantity</th>
            <th>Product Type</th>
            <th>Actions</th>
            <th>Products Count</th>
            <th>Updated At</th>
          </tr>
        </thead>
      </table>
    </div>
    {{-- DataTable ends --}}
    


  </section>
  {{-- Data list view end --}}
@endsection
@section('vendor-script')
{{-- vednor js files --}}
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>-
  <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection
@section('myscript')
  {{-- Page js files --}}
  <script type="text/javascript">
    function getHeaders(){
        $.ajax({
        method: "GET",
        url: "{{ action('SkuController@headers')  }}?site={{ $request->get('site') }}&shops=" + $("#shop").val(),
        success: function success(result) {
          console.log(result.data);
            $.each(result.data, function (i, item) {
              $('#badge_' + i).html(item);
            });
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
            { data: 'code', name: 'code'},
            { data: 'image', name: 'image', orderable : false,
              "render": function (data){
                    if(data){
                      return '<img src="'+data+'" class="product_image">';
                    }
                    else {
                      return "--No Available--";
                    }
              },
            },
            { data: 'name', name: 'name'},
            { data: 'link_shop', name: 'products.shop_id'},
            { data: 'cost', name: 'cost', className: 'quick_update_box'},
            { data: 'price', name: 'price', className: 'quick_update_box'},
            { data: 'quantity', name: 'quantity'},
            { data: 'warehouse_quantity', name: 'temp_wquantity_sort', visible: false},
            { data: 'alert_quantity', name: 'alert_quantity', className: 'quick_update_box'},
            { data: 'type', name: 'type'},
            { data: 'action', name: 'action', orderable : false},
            { data: 'products_count', name: 'products_count', searchable: false, visible: false },
            { data: 'updated_at', name: 'updated_at', searchable: false, visible: false }
        ];
  var table_route = {
          url: '{{ route('sku.index') }}',
          data: function (data) {
                data.warehouse = $("#warehouse").val();
                data.stocks = $('input[name=stocks][value=stocks]').is(":checked") ? 'with_stocks_only' : 'all';
                data.site = $('input[name="site"]:checked').val();
                data.shop = $("#shop").val();
            }
        };
  var buttons = [
            { text: "<i class='feather icon-plus'></i> Add New",
            action: function() {
                window.location = '{{ route('sku.create') }}';
            },
            className: "btn-outline-primary margin-r-10"}
            ];
  var order = [13, 'desc'];
  var BInfo = true;
  var bFilter = true;
  function created_row_function(row, data, dataIndex){
    $(row).attr('data-id', JSON.parse(data.id));
    if(data['products_count'] < 1){
        $(row).addClass('text-danger bold font-weight-bold');
    }
  }
  function draw_callback_function(settings){
    getHeaders();
  }
  var aLengthMenu = [[20, 50, 100, 500],[20, 50, 100, 500]];
  var pageLength = 20;
</script>
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
<script type="text/javascript">
  $(document).ready(function(){
      var additional_custom_filter = $(".additional_custom_filter").html();
      $(".action-filters").prepend(additional_custom_filter);
      $(".additional_custom_filter").html('');

      var filterCheckbox = $(".filter-checkbox");
      filterCheckbox.insertAfter($(".top .actions .dt-buttons"));

      $(".select2").select2({
        dropdownAutoWidth: true,
        width: '100%'
      });

      const swal2 = Swal.mixin({
        customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
      })


      $(document).on('click', '.quick_update_box', function() {
          var td = $(this);
          td.find("p").hide();
          td.find('input').show().focus().on('keypress',function(e) {
              if(e.which == 13) {
                  $(this).trigger('focusout');
              }
          });
          td.find('input').show().focus().on('focusout', function() {
            if($(this).val() != $(this).data('defval')) {
              var name = $(this).data('name');
              var defval = $(this).data('defval');
              var val = $(this).val();
              var sku_id = $(this).data('sku_id');
              Swal.fire({
                title: 'Update '+name+' ?',
                text: "Change value from "+defval+" to "+$(this).val()+" ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Change it!'
              }).then((result) => {
                if (result.value) {
                  $.ajax({
                    type: "POST",
                    url: '{{ route('sku.quickUpdate') }}',
                    data: {'sku': sku_id, 'name': name, 'val': td.find("input").val()},
                    dataType: "JSON",
                    cache: false,
                    success: function (res) {
                      if (res) {
                        td.find('input').attr('data-defval', val).data('defval', val).hide();
                        td.find("p").html(td.find("input").val()).show();
                      }
                      else {
                         swal2.fire(
                          'Warning',
                          'Something went wrong :(',
                          'error'
                        );
                        td.find('input').val(defval).hide();
                        td.find("p").show();
                      }
                    } 
                  });
                }
                else {
                  td.find('input').val(defval).hide();
                  td.find("p").show();
                }
              })
            }
            else {
              td.find('input').hide();
              td.find("p").show();
            }
          });

      });
      
      $(document).on('change', $('input[name=stocks][value=stocks]'), function() {
        table.ajax.reload();
      });

      $(document).on('change', $("#warehouse"), function() {
        if ($("#warehouse").val() != "") {
          table.column(7).visible(false);
          table.column(8).visible(true);
        }
        else {
          table.column(8).visible(false);
          table.column(7).visible(true);
        }
      });

      $('input[name="site"]').change(function(){
        var site = $('input[name="site"]:checked').val();
        url = "{{ action('SkuController@index')}}?site=" + site;
        window.location.href = url;
      });


  }); 
  
  
  var city = [{id:1,name:'indb',value:5}];
  var mains = [{id:1,city_id:1,namex:"plc"}];
  
  var rst = {};
  
  
  var result = mains.map(function(data){
      
      var city_data ="";
      $.each(city, function( index, value ) {
          if(value.id==data.city_id){
              city_data = value.name;
          }
        });
     

     
     data.city_data = city_data;
     return data;
     
  });
  
  
  console.log(result);

  

 
  </script>
@endsection