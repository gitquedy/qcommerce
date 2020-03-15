@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Products')

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
    <div class="card-content">
      <div class="card-body">
        <div class="row">
          <div class="col-sm-12 shop_filter">
              <label for="site1" class="btn btn-lg btn-outline-primary mb-1 {{ $request->get("site") == "lazada" ?  "active" : ""}}">
                <img class="shop_logo" src="{{asset('images/shop/icon/lazada.png')}}" alt="">
                Lazada
                <span id="badge_lazada_total" class="badge badge-secondary"></span>
              </label>
              <label for="site2" class="btn btn-lg btn-outline-primary mb-1 {{ $request->get('site') == 'shopee' ?  'active' : ''}}">
                <img class="shop_logo" src="{{asset('images/shop/icon/shopee.png')}}" alt="">
                Shopee
                <span id="badge_shopee_total" class="badge badge-secondary"></span>
              </label>
              <input type="radio" id="site1" name="site" value="lazada"  {{ $request->get("site") == "lazada" ?  "checked" : ""}}>
              <input type="radio" id="site2" name="site" value="shopee"  {{ $request->get('site') == 'shopee' ?  'checked' : ''}}>
          </div>
        </div>
        {{-- <div class="row">
          <div class="col-sm-4">
            <ul class="list-unstyled mb-0">
              <li class="d-inline-block mr-2">
                <fieldset>
                 <div class="vs-radio-con">
                    <input type="radio" id="site" name="site"  value="lazada" {{ $request->get("site") == "lazada" ?  "checked" : ""}}>
                    <span class="vs-radio">
                      <span class="vs-radio--border"></span>
                      <span class="vs-radio--circle"></span>
                    </span>
                    <span class="">Lazada</span>
                  </div>
                </fieldset>
              </li>
              <li class="d-inline-block mr-2">
                <fieldset>
                  <div class="vs-radio-con">
                    <input type="radio" id="site" name="site" value="shopee" {{ $request->get('site') == 'shopee' ?  'checked' : ''}}>
                    <span class="vs-radio">
                      <span class="vs-radio--border"></span>
                      <span class="vs-radio--circle"></span>
                    </span>
                    <span class="">Shopee</span>
                  </div>
                </fieldset>
              </li>
            </ul>
          </div>
        </div> --}}
        <br>
        <div class="row">
        <div class="col-sm-12">
          <div class="btn-group-toggle" data-toggle="buttons">
              <label class="btn px-1 btn-outline-primary {{ ('all' == $selectedStatus) ? 'active' : '' }}">
                <input type="radio" name="status" id="status_all" class="selectFilter" autocomplete="off" value="all" checked> All
              </label>
              @foreach($statuses as $status)
                <label class="btn px-1 btn-outline-primary {{ ($status == $selectedStatus) ? 'active' : '' }}">
                  <input type="radio" name="status" id="status_{{ $status }}"  class="selectFilter" value="{{ $status }}"  {{ ($status == $selectedStatus) ? 'checked' : '' }} autocomplete="off"> {{ ucfirst(strtolower($status)) }}
                   <span id="badge_{{ $status }}" class="badge badge-secondary"></span>
                </label>
              @endforeach
            </div>
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
            <a class="dropdown-item duplicateForm" data-href="{{ action('ProductController@duplicateForm') }}"  > Duplicate Products</a>
            <!--<a class="dropdown-item" href="#">Print</a>-->
            <a class="dropdown-item massAction" href="#" data-action="{{ route('product.bulkremove') }}"> Delete</a>
            <!--<a class="dropdown-item" href="#">Another Action</a>-->
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
            <th>Item ID</th>
            <th>Sku</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Available</th>
            <th>Status</th>
            <th>Actions</th>
            
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
  <script type="text/javascript">
    function getHeaders(){
        $.ajax({
        method: "GET",
        url: "{{ action('ProductController@headers')  }}?site={{ $request->get('site') }}&shops=" + $("#shop").val(),
        success: function success(result) {     
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
            { data: 'getImgAndIdDisplay', name: 'getImgAndIdDisplay'},
            { data: 'SellerSku', name: 'SellerSku'},
            { data: 'image', name: 'image', orderable : false,
            "render": function (data){
                    return '<img src="'+data+'" class="product_image">';
                },
            },
            { data: 'name', name: 'name' },
            { data: 'price', name: 'price'},
            { data: 'quantity', name: 'quantity'},
            { data: 'status_display', name: 'status_display'},
            { data: 'action', name: 'action', orderable : false}
        ];
  var table_route = {
          url: '{{ route('product.index') }}',
          data: function (data) {
                data.shop = $("#shop").val();
                data.status = $("input[name=status]:checked").val();
                data.timings = $("#timings").val();
                data.site = $('input[name="site"]:checked').val();
            }
        };
  var buttons = [

            ];
  var BInfo = true;
  var bFilter = true;
  function created_row_function(row, data, dataIndex){
    $(row).attr('data-id', JSON.parse(data.id));
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
      $(".select2").select2({
        dropdownAutoWidth: true,
        width: '100%'
      });
      $('input[name="site"]').change(function(){
        var site = $('input[name="site"]:checked').val();
        url = "{{ action('ProductController@index')}}?site=" + site;
        window.location.href = url;
      });
  }); 

  
  
  var shop_json = '<?php echo json_encode($all_shops)?>';
  
  try {
    var shop_obj = JSON.parse(shop_json);
    }
    catch(err) {
         Swal.fire(
          'Error !',
          'Json Shop Parse Error !',
          'error'
        );
    }
  
  var shops = [];
  
  function duplicate_product(ids,shop_id){
      
      $('#product_id').val(ids);
       $(".shop_option").each(function(){
        if($(this).attr('value')==shop_id){  
            $(this).attr('disabled','true');
        }
      });
      
      $('#duplicate_modal').modal('show');
      
      
  }
  
  $(document).on("click", '.duplicateForm', function () {

   var selected_ids = [];
    
   $(".dt-checkboxes").each(function(){
      if($(this).prop('checked')==true){
          selected_ids.push($(this).parent().parent().data('id'));
      }
   });
    
   if(selected_ids.length==0){
         Swal.fire(
         'Warning!',
         'Please select atleast one order',
         'warning'
       );
       return false;
     }
    var url = $(this).data("href") + "?ids=" + selected_ids.toString();
    var container = $(".view_modal");
    $.ajax({
      method: "GET",
      url: url,
      dataType: "html",
      success: function success(result) {
        $(container).html(result).modal("show");
      },
      error: function error(jqXhr, json, errorThrown) {
        console.log(jqXhr);
        console.log(json);
        console.log(errorThrown);
      }
    });
  });
  </script>
@endsection
