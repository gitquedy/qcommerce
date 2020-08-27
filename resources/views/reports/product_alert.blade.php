@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Product Alert')

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

    {{-- DataTable starts --}}
    <div class="table-responsive">
        
      <table class="table data-list-view">
        <thead>
          <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Brand</th>
            <th>Supplier</th>
            <th>Category</th>
            <th>Cost</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Alert Quantity</th>
            <th>SupplierID</th>
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
            { data: 'brand_name', name: 'brand_name'},
            { data: 'supplier_name', name: 'supplier_name'},
            { data: 'category_name', name: 'category_name'},
            { data: 'cost', name: 'cost'},
            { data: 'price', name: 'price'},
            { data: 'quantity', name: 'quantity'},
            { data: 'alert_quantity', name: 'alert_quantity'},
            { data: 'supplier', name: 'supplier', visible: false}
        ];
  var table_route = {
          url: '{{ route('reports.productAlert') }}',
          data: function (data) {
                data.shop = $("#shop").val();
                data.status = $("#status").val();
                data.timings = $("#timings").val();
            }
        };
  var buttons = [{ extend: 'copy',
                   text: "<i class='feather icon-copy'></i> Copy",
                   className: "btn-outline-primary"
                 },
                 { extend: 'csv',
                   text: "<i class='feather icon-file-plus'></i> CSV",
                   className: "btn-outline-primary"
                 },
                 { extend:'pdfHtml5',
                    text: "<i class='feather icon-file-text'></i> PDF",
                    orientation:'portrait',
                    customize : function(doc){
                        var colCount = new Array();
                        $(".table").find('tbody tr:first-child td').each(function(){
                            if($(this).attr('colspan')){
                                for(var i=1;i<=$(this).attr('colspan');$i++){
                                    colCount.push('*');
                                }
                            }else{ colCount.push('*'); }
                        });
                        doc.content[1].table.widths = colCount;
                    },
                    className: "btn-outline-primary"
                 },
                 { extend: 'print',
                   text: "<i class='feather icon-printer'></i> Print",
                   className: "btn-outline-primary"
                 }
                ];
  var BInfo = true;
  var bFilter = true;
  function created_row_function(row, data, dataIndex){
     $(row).attr('data-id', JSON.parse(data.id));
  }
  var aLengthMenu = [[20, 50, 100, 500],[20, 50, 100, 500]];
  var pageLength = 20;

  
  var table = '';
$(document).ready(function () {
  "use strict";
    table = $(".data-list-view").DataTable({
        processing: true,
        serverSide: false,
        ajax: table_route,
        columns: columnns,
        createdRow: created_row_function,
        drawCallback: (typeof draw_callback_function === "function")  ? draw_callback_function : function(){},
        responsive: !1,
        columnDefs: [{
            orderable: false,
            targets: 0,
            checkboxes: {
                selectRow: !0
            },
        }],
        dom: '<"top"<"actions action-btns"B><"action-filters"lf>><"clear">rt<"bottom"<"actions">p>',
        oLanguage: {
            sLengthMenu: "_MENU_",
            sSearch: ""
        },
        aLengthMenu: aLengthMenu,
        select: {
            selector: "first-child",
            style: "multi",
        },
        bInfo: typeof BInfo !== 'undefined' ? BInfo : true,
        bFilter: typeof bFilter !== 'undefined' ? bFilter : true,
        pageLength: pageLength,
        "aaSorting": [],
        // order: [[1, 'asc']],
        buttons: buttons,
        initComplete: function(t, e) {
            $(".dt-buttons .btn").removeClass("btn-secondary")
            this.api().columns(2).every( function () {
              var column = this;
              $('.data-list-view .head .head_hide').html('');

              $('<div class="btn-group dropdown column-filter" style="box-shadow: none" ></div>')
                  .appendTo( $(".actions.action-btns"));

              var select = $('<select id="SupplierFilter" class="form-control"><option value="">All</option></select>')
                  .appendTo( $(".column-filter"))
                  .on( 'change', function () {
                      var val = $.fn.dataTable.util.escapeRegex(
                          $(this).val()
                      );
                      column
                          .search( val ? '^'+val+'$' : '', true, false )
                          .draw();
                  });

              column.data().unique().sort().each( function ( d, j ) {
                  select.append( '<option value="'+d+'">'+d+'</option>' )
              });
            }); 
        }
    });

  // To append actions dropdown before add new button
  var actionDropdown = $(".actions-dropodown")
  actionDropdown.insertBefore($(".top .actions .dt-buttons"))
  var columnFilter = $(".column-filter")
  columnFilter.insertAfter($(".top .actions .dt-buttons"))

  // Scrollbar
  if ($(".data-items").length > 0) {
    new PerfectScrollbar(".data-items", { wheelPropagation: false });
  }

  // Close sidebar
  $(".hide-data-sidebar, .cancel-data-btn").on("click", function () {
    $(".add-new-data").removeClass("show");
    $(".overlay-bg").removeClass("show");
    $("#data-name, #data-price").val("");
    $("#data-category, #data-status").prop('selectedIndex', 0);
  });
});
</script>
@endsection