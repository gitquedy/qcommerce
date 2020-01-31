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
      <!--     <div class="col-sm-4 col-12">
            <div class="text-bold-600 font-medium-2">
              Order ID:
            </div>
            <div class="form-group">
              <input type="text" id="search" class="form-control inputSearch" placeholder="Input order id here..">
            </div>
          </div> -->
          <div class="col-sm-4 col-12">
            <div class="text-bold-600 font-medium-2">
              Shop:
            </div>
            <div class="form-group">
              <select name="shop" id="shop" class="select2 form-control selectFilter">
                <option value="all">All</option>
                @foreach($all_shops as $shop)
                  <option value="{{ $shop->id }}">{{ $shop->name . ' (' . $shop->short_name . ')' }}</option>
                @endforeach
              </select>
            </div>
        </div>
        <div class="col-sm-4 col-12">
            <!--<div class="text-bold-600 font-medium-2">-->
            <!--  Status:-->
            <!--</div>-->
            <!--<div class="form-group">-->
            <!--  <select name="status[]" id="status" class="select2 form-control selectFilter" multiple="multiple">-->
            <!--    @foreach($statuses as $status)-->
            <!--      <option value="{{ $status }}" {{ $status == 'pending' || $status == 'ready_to_ship' || $status == 'shipped' ? 'selected' : '' }}>{{ ucwords(str_replace("_"," ", $status)) }}</option>-->
            <!--    @endforeach-->
            <!--  </select>-->
            <!--</div>-->
        </div>
        <div class="col-sm-4 col-12">
            <!--<div class="text-bold-600 font-medium-2">-->
            <!--  Date Filter:-->
            <!--</div>-->
            <!--<div class="form-group">-->
            <!--  <select name="timings[]" id="timings" class="select2 form-control selectFilter" >-->
            <!--    <option value="All">All</option>-->
            <!--    <option value="Today">Today</option>-->
            <!--    <option value="Yesterday">Yesterday</option>-->
            <!--    <option value="Last_7_days">Last 7 days</option>-->
            <!--    <option value="Last_30_days">Last 30 days</option>-->
            <!--    <option value="This_Month">This Month</option>-->
            <!--  </select>-->
            <!--</div>-->
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
            <a class="dropdown-item" onclick="mass_copy()"  > Duplicate</a>
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
            <th>Shop</th>
            <th>Model</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Actions</th>
            
          </tr>
        </thead>
      </table>
    </div>
    {{-- DataTable ends --}}
    
    
    <div class="modal" id="duplicate_modal">
      <div class="modal-dialog">
        <div class="modal-content">
    
          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title">Duplicate Product</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
    
          <!-- Modal body -->
          <div class="modal-body"> 
          
              <form action="{{route('product.process_duplicate_product')}}" method="post" >
                  @csrf
              <input type="hidden" id="product_id" name="product_id" >
              
              <label>Select Shop</label>
              <select class="form-control"  required name="shop_id" >
                  <option value="">select</option>
                  @foreach($all_shops as $shopVAL)
                  <option class="shop_option" value="{{$shopVAL->id}}">{{$shopVAL->name}}</option>
                  @endforeach
                 
              </select>
              <br/>
              <div class="text-right">
                  <button type="submit" class="btn btn-primary"> Duplicate</button>
                  
              </div>
              </form>
            
          </div>
    
    
        </div>
      </div>
    </div>

{{-- Modal ends --}}



<!-- The Modal -->
<div class="modal" id="mass_copy">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Duplicate Products</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <form action="{{route('product.mass_copy')}}" method="post" >
                  @csrf
              <input type="hidden" id="mass_products_copy" name="products" >
              
              <label>Select Shop</label>
              <select class="form-control"  required name="shop_id" >
                  <option value="">select</option>
                  @foreach($all_shops as $shopVAL)
                  <option  value="{{$shopVAL->id}}">{{$shopVAL->name}}</option>
                  @endforeach
                 
              </select>
              <br/>
              <div class="text-right">
                  <button type="submit" class="btn btn-primary"> Duplicate</button>
                  
              </div>
        </form>
      </div>


    </div>
  </div>
</div>




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
            { data: 'shop', name: 'shop'},
            { data: 'model', name: 'model'},
            { data: 'image', name: 'image',
            "render": function (data){
                    return '<img src="'+data+'" class="product_image">';
                },
                
            },
            { data: 'name', name: 'name' },
            { data: 'price', name: 'price'},
            { data: 'action', name: 'action'}
        ];
  var table_route = {
          url: '{{ route('product.index') }}',
          data: function (data) {
                data.shop = $("#shop").val();
                data.status = $("#status").val();
                data.timings = $("#timings").val();
            }
        };
  var buttons = [
            // { text: "<i class='feather icon-plus'></i> Add New",
            // action: function() {
            //     window.location = '{{ route('order.create') }}';
            // },
            // className: "btn-outline-primary margin-r-10"}
            ];
  var BInfo = true;
  var bFilter = true;
  function created_row_function(row, data, dataIndex){
    $(row).attr('data-id', JSON.parse(data.id));
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
  
  
  function mass_copy(){
      
      var selected_products = [];
      
       $(".dt-checkboxes").each(function(){
           if($(this).prop('checked')==true){
               selected_products.push($(this).parent().parent().data('id'));
           }
        });
     
         if(selected_products.length==0){
             Swal.fire(
              '',
              'Please Select atleast a product !',
              'warning'
            );
            return false;
         }
         
         $('#mass_products_copy').val(JSON.stringify(selected_products));
         $('#mass_copy').modal('show');
      
  }
  
  
 
 
  </script>
@endsection