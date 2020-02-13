@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Out of Stock')

@section('vendor-style')
        {{-- vednor files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/extensions/dataTables.checkboxes.css')) }}">
@endsection
@section('mystyle')
        {{-- Page css files --}}
        <link rel="stylesheet" href="{{ asset(mix('css/pages/data-list-view.css')) }}">
@endsection

@section('content')
<style>
    .product_image{
        width:100px;
        height:auto;
    }
    
    option[disabled]{
        background-color:#F8F8F8;
    }
</style>
{{-- Data list view starts --}}
<section id="data-list-view" class="data-list-view-header">
    <div class="table-responsive">
      <table class="table data-list-view">
        <thead>
          <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Brand</th>
            <th>Category</th>
            <th>Cost</th>
            <th>Price</th>
          </tr>
        </thead>
      </table>
    </div>
    {{-- DataTable ends --}}

    {{-- add new sidebar starts --}}
      @include('crud/sidebar-create')
    {{-- add new sidebar ends --}}
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
@endsection
@section('myscript')
  {{-- Page js files --}}
  <!-- datatables -->
  <script type="text/javascript">
  var columnns = [
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
            { data: 'cost', name: 'cost'},
            { data: 'price', name: 'price'}
        ];
  var table_route = {url: '{{ route('reports.outOfStock') }}'};
  var buttons = ['excel', 'pdf'];
  function created_row_function(row, data, dataIndex){
    $(row).attr('data-id', JSON.parse(data.id));
  }
  var aLengthMenu = [[4, 10, 15, 20],[4, 10, 15, 20]];
  var pageLength = 10;
  $(document).ready(function(){
      $('.view_modal').on('hidden.bs.modal', function () {
        table.ajax.reload();
      });
  });  
  </script>
  <script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
@endsection
