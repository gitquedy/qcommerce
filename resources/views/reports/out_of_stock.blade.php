@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Out of Stock')

@section('vendor-style')
        {{-- vednor files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/extensions/dataTables.checkboxes.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">
    
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
        
      </div>
    </div>

    {{-- DataTable starts --}}
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
    


  </section>
  {{-- Data list view end --}}
@endsection
@section('vendor-script')
{{-- vednor js files --}}
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>
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
    // imageToBase64 = (URL) => {
    //     let image;
    //     image = new Image();
    //     image.crossOrigin = 'Anonymous';
    //     image.addEventListener('load', function() {
    //         let canvas = document.createElement('canvas');
    //         let context = canvas.getContext('2d');
    //         canvas.width = image.width;
    //         canvas.height = image.height;
    //         context.drawImage(image, 0, 0);
    //         try {
    //             localStorage.setItem('saved-image-example', canvas.toDataURL('image/png'));
    //         } catch (err) {
    //             console.error(err)
    //         }
    //     });
    //     image.src = URL;
    // };
  var columnns = [
            { data: 'image', name: 'image', orderable : false,
              "render": function (data){
                    if(data){
                      return '<img src="'+data+'" class="product_image">';
                    }
                    else {
                      return "--No Available--";
                    }
              }
            },
            { data: 'name', name: 'name'},
            { data: 'brand_name', name: 'brand_name'},
            { data: 'category_name', name: 'category_name'},
            { data: 'cost', name: 'cost'},
            { data: 'price', name: 'price'}
        ];
  var table_route = {
          url: '{{ route('reports.outOfStock') }}',
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

                        // for (var i = 1; i < doc.content[1].table.body.length; i++) {
                        //   if (doc.content[1].table.body[i][0].text.indexOf('<img src=') !== -1) {
                        //       html = doc.content[1].table.body[i][0].text;

                        //       var regex = /<img.*?src=['"](.*?)['"]/;
                        //       var src = regex.exec(html)[1];

                        //       var tempImage = new Image();
                        //       tempImage.src = src;
                        //       doc.images[src] = getBase64Image(tempImage)

                        //       delete doc.content[1].table.body[i][0].text;
                        //       doc.content[1].table.body[i][0].image = src;
                        //       doc.content[1].table.body[i][0].fit = [50, 50];
                        //   }

                        //   //here i am removing the html links so that i can use stripHtml: true,
                        //   if (doc.content[1].table.body[i][2].text.indexOf('<a href="details.php?') !== -1) {
                        //       html = $.parseHTML(doc.content[1].table.body[i][2].text);
                        //       delete doc.content[1].table.body[i][0].text;
                        //       doc.content[1].table.body[i][2].text = html[0].innerHTML;
                        //   }
                        // }
                    },
                    // exportOptions : {
                    //     stripHtml: false
                    // },
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
</script>
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
<script type="text/javascript">
  $(document).ready(function(){
  });
  </script>
@endsection