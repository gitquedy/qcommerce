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
            <!--<a class="dropdown-item massAction" href="#" data-action="{{ route('product.bulkremove') }}"> Delete</a>-->
            <!--<a class="dropdown-item" href="#">Another Action</a>-->
          </div>
        </div>
      </div>
    </div>

    {{-- DataTable starts --}}
    <div class="table-responsive">
        
      <textarea class="form-control" id="json_output">
          
      </textarea>
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
  
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
<script type="text/javascript">



var tables = [{'name':'amd','primary':'id','coloumn':['id','name','price',"butter"]},{'name':'jdc','primary':'id','coloumn':['id','name','price']}];
var records = {amd:[{'id':'1','name':'stark','price':'200'},{'id':'2','name':'stark Man','price':'300'}]};

function record_update(){
    
    $.each(tables, function( tablesINDEX, tablesVAL ) {
      if(records[tablesVAL.name]==undefined){
         records[tablesVAL.name] = [];
      }

              $.each(tablesVAL.coloumn, function( colIND, COLVAL ) {
        
                  $.each(records[tablesVAL.name], function( recINDEX, recVAL ) {
                       
                      if(records[tablesVAL.name][recINDEX][COLVAL]==undefined){
                          records[tablesVAL.name][recINDEX][COLVAL] = "";
                      }
                 });
             });
          
    });
    
}

record_update();

var database = {tables:tables,records:records};


$('#json_output').val(JSON.stringify(database));


  

 
  </script>
@endsection
