@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Items Not Moving')

@section('vendor-style')
        {{-- vendor files --}}
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

<div class="row">
    <div class="col-12">
        <div class="btn-group-toggle actions-dropodown" data-toggle="buttons">
            <label class="btn px-1 btn-outline-primary {{ (7 == $selectedDays) ? 'active' : '' }}">
                <input type="radio" name="days" id="7days"  class="selectFilter" value=7 autocomplete="off" checked>
                <i class='feather icon-calendar'></i> 7 Days
                <span id="badge_7days" class="badge badge-secondary"></span>
            </label>
            <label class="btn px-1 btn-outline-primary {{ (30 == $selectedDays) ? 'active' : '' }}">
                <input type="radio" name="days" id="30days"  class="selectFilter" value=30 autocomplete="off" {{ (30 == $selectedDays) ? 'checked' : '' }}>
                <i class='feather icon-calendar'></i> 30 Days
                <span id="badge_30days" class="badge badge-secondary"></span>
            </label>
            <label class="btn px-1 btn-outline-primary {{ (60 == $selectedDays) ? 'active' : '' }}">
                <input type="radio" name="days" id="60days"  class="selectFilter" value=60 autocomplete="off" {{ (60 == $selectedDays) ? 'checked' : '' }}>
                <i class='feather icon-calendar'></i> 60 Days
                <span id="badge_60days" class="badge badge-secondary"></span>
            </label>
            <label class="btn px-1 btn-outline-primary {{ (90 == $selectedDays) ? 'active' : '' }}">
                <input type="radio" name="days" id="90days"  class="selectFilter" value=90 autocomplete="off" {{ (90 == $selectedDays) ? 'checked' : '' }}>
                <i class='feather icon-calendar'></i> 90 Days
                <span id="badge_90days" class="badge badge-secondary"></span>
            </label>
        </div>
    </div>
</div>
<section id="data-list-view" class="data-list-view-header">

    {{-- DataTable starts --}}
    <div class="table-responsive">
        
      <table class="table data-list-view">
        <thead>
          <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Supplier</th>
            <th>Cost</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Alert Quantity</th>
            <th>SupplierID</th>
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
  <!--<script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script>-->
  <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>
@endsection
@section('myscript')
  {{-- Page js files --}}
  <!-- datatables -->
  <script type="text/javascript">
    function getBase64Image(img) {
        var canvas = document.createElement("canvas");
        canvas.width = img.width;
        canvas.height = img.height;
        var ctx = canvas.getContext("2d");
        ctx.drawImage(img, 0, 0);
        return canvas.toDataURL("image/png");
    }
  var columnns = [
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
            // { data: 'brand_name', name: 'brand_name'},
            { data: 'supplier_name', name: 'supplier_name'},
            // { data: 'category_name', name: 'category_name'},
            { data: 'cost', name: 'cost'},
            { data: 'price', name: 'price'},
            { data: 'quantity', name: 'quantity'},
            { data: 'alert_quantity', name: 'alert_quantity'},
            { data: 'supplier', name: 'supplier', visible: false},
            { data: 'updated_at', name: 'updated_at', searchable: false, visible: false }
        ];
  var table_route = {
          url: '{{ route('reports.itemsNotMoving') }}',
          data: function (data) {
                data.days = $("input[name=days]:checked").val();
            }
        };
  var buttons = [];
  var order = [8, 'desc'];
  var BInfo = true;
  var bFilter = true;
  function created_row_function(row, data, dataIndex){
     $(row).attr('data-id', JSON.parse(data.id));
  }
  var aLengthMenu = [[20, 50, 100, 500],[20, 50, 100, 500]];
  var pageLength = 20;
</script>
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
@endsection