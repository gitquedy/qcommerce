@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Link SKU Products')

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

<section>
    <div class="row match-height">
      <div class="col-xl-3 col-md-6 col-sm-12">
        <div class="card">
          <div class="card-content">
            <img class="card-img-top img-fluid" src="{{ $sku->SkuImage() }}"
              alt="Card image cap">
            <div class="card-body">
              <h5>{{ $sku->code }} - {{ $sku->name }}</h5>
              <table class="table">
                <thead>
                  <tr class="text-center">
                    <th>Cost</th>
                    <th>Price</th>
                    <th>Total Quantity</th>
                    <th>Alert Qty</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><p class="card-text text-center text-danger mb-0"><b>{{ number_format($sku->cost, 2) }}</b></p></td>
                    <td><p class="card-text text-center text-success mb-0"><b>{{ number_format($sku->price, 2) }}</b></p></td>
                    <td><p class="card-text text-center text-primary mb-0"><b>{{ number_format($sku->quantity, 2) }}</b></p></td>
                    <td><p class="card-text text-center text-warning mb-0"><b>{{ number_format($sku->alert_quantity, 2) }}</b></p></td>
                  </tr>
                </tbody>
              </table> 

              <table class="table">
                <thead>
                  <tr class="text-center">
                    <th>Warehouse</th>
                    <th>Quantity</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($sku->warehouse_items as $items)
                  <tr>
                    <td><p class="card-text text-center text-default mb-0"><b>{{$items->warehouse->name}}</b></p></td>
                    <td><p class="card-text text-center text-primary mb-0"><b>{{ number_format($items->quantity, 2) }}</b></p></td>
                  </tr>
                  @endforeach
                </tbody>
              </table> 
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-9 col-md-6 col-sm-12 data-list-view-header">
        <div class="action-btns d-none">
          <div class="btn-dropdown mr-1 mb-1">
            <div class="btn-group dropdown actions-dropodown">
              <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Actions
              </button>
              <div class="dropdown-menu">
                <!--<a class="dropdown-item" href="#">Print</a>-->
                <a class="dropdown-item massAction" href="#" data-action="{{route('sku.removeskuproduct')}}"> Unlink</a> {{-- {{ route('sku.bulkremove') }} --}}
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
                <!-- <th>Model</th> -->
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Actions</th>
                
              </tr>
            </thead>
          </table>
        </div>
        {{-- DataTable ends --}}
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
            // { data: 'model', name: 'model'},
            { data: 'image', name: 'image', orderable : false,
            "render": function (data){
                    return '<img src="'+data+'" class="product_image">';
                },
                
            },
            { data: 'name', name: 'name' },
            { data: 'price', name: 'price'},
            { data: 'quantity', name: 'quantity'},
            { data: 'action', name: 'action', orderable : false}
        ];
  var table_route = {
          url: '{{ route('sku.skuproducts', $sku->id) }}',
          data: function (data) {
                // data.timings = $("#timings").val();
            }
        };
  var buttons = [
            { text: "<i class='feather icon-link'></i> Link Products",
            action: function() {
              $.ajax({
                url :  "{{ route('sku.addproductmodal') }}",
                type: "POST",
                data: 'id={{$sku->id}}',
                success: function (response) {
                  if(response) {
                    $(".view_modal").html(response).modal('show');
                  }
                }
              });
            },
            className: "btn-outline-primary margin-r-10"}
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
 
  </script>
@endsection