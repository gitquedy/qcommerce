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
          </div>
        </div>
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
            <th>Code</th>
            <th>Image</th>
            <th>Name</th>
            <th>Brand</th>
            <th>Category</th>
            <th>Cost</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Alert Quantity</th>
            <th>Actions</th>
            <th>Products Count</th>
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
  <!--<script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script>-->
  <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection
@section('myscript')
  {{-- Page js files --}}
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
            { data: 'image', name: 'image',
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
            { data: 'brand_name', name: 'brand_name'},
            { data: 'category_name', name: 'category_name'},
            { data: 'cost', name: 'cost', className: 'quick_update_box'},
            { data: 'price', name: 'price', className: 'quick_update_box'},
            { data: 'quantity', name: 'quantity', className: 'quick_update_box'},
            { data: 'alert_quantity', name: 'alert_quantity', className: 'quick_update_box'},
            { data: 'action', name: 'action'},
            { data: 'products_count', name: 'products_count', searchable: false, visible: false }
        ];
  var table_route = {
          url: '{{ route('sku.index') }}',
          data: function (data) {
                data.shop = $("#shop").val();
                data.status = $("#status").val();
                data.timings = $("#timings").val();
            }
        };
  var buttons = [
            { text: "<i class='feather icon-plus'></i> Add New",
            action: function() {
                window.location = '{{ route('sku.create') }}';
            },
            className: "btn-outline-primary margin-r-10"}
            ];
  var BInfo = true;
  var bFilter = true;
  function created_row_function(row, data, dataIndex){
    $(row).attr('data-id', JSON.parse(data.id));
    if(data['products_count'] < 1){
        $(row).addClass('text-danger bold font-weight-bold');
    }

  }
  var aLengthMenu = [[20, 50, 100, 500],[20, 50, 100, 500]];
  var pageLength = 20;
</script>
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
<script type="text/javascript">
  $(document).ready(function(){
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