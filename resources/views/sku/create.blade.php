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
</style>

<section class="card">
    <div class="card-content">
      <div class="card-body">
          
          @if ($errors->any())
              <div class="alert alert-danger">
                  <ul>
                      @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
              </div>
          @endif
          <form action="{{route('sku.add')}}" method="post">
          @csrf
          <div class="row">
              <div class="col-md-6 form-group">
                  <label>SKU Code</label>
                  <input class="form-control" name="code">
              </div>
              <div class="col-md-6 form-group">
                  <label>Product Name</label>
                  <input class="form-control" name="name">
              </div>
<!--           <div class="col-md-6 form-group">
                <label>Brand</label>
                <div class="form-group">
                    <div class="input-group">
                      <select class="form-control s280" id="brand" name="brand" style="width:80% !important">
                            <option value="" disabled selected></option>
                
                        </select>
                        @can('brand.manage')
                        <div class="input-group-append">
                          <button type="button" data-toggle="modal" data-target="#brand_modal" class="btn btn-outline-primary btn-flat"><i class="fa fa-plus"></i> Add new</button>
                        </div>
                      @endcan
                    </div>
                  </div>
            </div>
            <div class="col-md-6 form-group">
                <label>Category</label>
                <div class="form-group">
                    <div class="input-group">
                      <select class="form-control s280" name="category" id="category">
                            <option value="" disabled selected></option>
                     
                        </select>
                      @can('category.manage')
                        <div class="input-group-append">
                          <button type="button" data-toggle="modal" data-target="#category_modal" class="btn btn-outline-primary btn-flat"><i class="fa fa-plus"></i> Add new</button>
                        </div>
                      @endcan
                    </div>
                  </div>
            </div> -->
              <div class="col-md-6 form-group">
                  <label>Supplier</label>
                  <div class="form-group">
                      <div class="input-group">
                        <select class="form-control s280" name="supplier" id="supplier">
                              <option value="" disabled selected></option>
                              @foreach($Supplier as $SupplierVAL)
                              <option  value="{{$SupplierVAL->id}}">{{$SupplierVAL->company}}</option>
                              @endforeach
                          </select>
                        @can('supplier.manage')
                          <div class="input-group-append">
                            <button type="button" data-toggle="modal" data-target="#supplier_modal" class="btn btn-outline-primary btn-flat"><i class="fa fa-plus"></i> Add new</button>
                          </div>
                        @endcan
                      </div>
                    </div>
              </div>
              <div class="col-md-6 form-group">
                  <label>Cost</label>
                  <input type="number" step="any" class="form-control" name="cost">
              </div>
              <div class="col-md-6 form-group">
                  <label>Price</label>
                  <input type="number" step="any" class="form-control" name="price">
              </div>
              <div class="col-md-6 form-group">
                  <label>Alert Quantity</label>
                  <input type="number" class="form-control"  name="alert_quantity">
              </div>
              <div class="col-md-6 form-group">
                <label>Product Type</label>
                <div class="form-group">
                  <div class="input-group">
                    <select class="form-control s280" name="type" id="type">
                      <option value="" disabled selected></option>
                      <option  value="single">Single</option>
                      <option  value="set">Set</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="text-bold-600 font-medium-2 col-md-12 form-group set" style="display:none">
                Add Products to Set
                <hr>
                <div class="row">
                  <div class="text-bold-600 font-medium-1 col-md-6">
                    SKU:
                    <div class="form-group">
                      <select name="sku" id="ap_sku" class="select2 form-control ap_reset">
                        <option value="" disabled hidden selected></option>
                          @foreach($all_skus as $sku)
                            <option value="{{ $sku->id }}">{{ $sku->name . ' (' . $sku->code . ')' }}</option>
                          @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="text-bold-600 font-medium-1 col-md-4">
                    Quantity:
                    <div class="form-group">
                      <input type="number" class="form-control" name="quantity" id="quantity">
                    </div>
                  </div>
                  <div class="align-self-center col-md-2">
                    <button type="button" onclick="addProducts(event)" class="btn btn-primary no-print btn_save"><i class="fa fa-link"></i> Add Product </button>
                  </div>
                </div>
              </div>
              <div class="col-md-12 form-group set" id="products_list"></div>
              <div class="col-md-12 text-right">
                  <br/>
                  <button class="btn btn-primary">Save</button>
              </div>
          </div>
          </form>
      </div>
    </div>
  </section>
  {{-- Data list view end --}}
  



<!-- The Modal -->
<!-- <div class="modal" id="brand_modal">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title">Add New Brand</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
         @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
         <form  onsubmit="process_add_brand(event)" >
          <div class="row">
              <div class="col-md-12 form-group">
                  <label>Code</label>
                  <input class="form-control" id="brand_code" required>
              </div>
              <div class="col-md-12 form-group">
                  <label>Name</label>
                  <input class="form-control" id="brand_name" required>
              </div>
          </div>
          
      </div>

      <div class="modal-footer">
         <button class="btn btn-primary">Save</button>
         </form>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div> -->




<!-- The Modal -->
<!-- <div class="modal" id="category_modal">
  <div class="modal-dialog">
    <div class="modal-content">

      Modal Header
      <div class="modal-header">
        <h4 class="modal-title">Add New Category</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
         @if ($errors->any())
              <div class="alert alert-danger">
                  <ul>
                      @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
              </div>
          @endif
         <form  onsubmit="process_add_category(event)" >
          <div class="row">
              <div class="col-md-12 form-group">
                  <label>Code</label>
                  <input class="form-control" id="category_code" required>
              </div>
              <div class="col-md-12 form-group">
                  <label>Name</label>
                  <input class="form-control" id="category_name" required>
              </div>
              <div class="col-md-12 form-group">
                  <label>Parent Category</label>
                  <select class="form-control s2" name="category_parent" id="category2" >
                              <option value="">select</option>
                    
                  </select>
              </div>
          </div>
          
      </div>

      <div class="modal-footer">
         <button class="btn btn-primary">Save</button>
         </form>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div> -->

<!-- The Modal -->
<div class="modal" id="supplier_modal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add New Supplier</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
         @if ($errors->any())
              <div class="alert alert-danger">
                  <ul>
                      @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
              </div>
          @endif
         <form  onsubmit="process_add_supplier(event)" >
          <div class="row">
              <div class="col-md-12 form-group">
                  <label>Company</label>
                  <input class="form-control" id="company" required>
              </div>
              <div class="col-md-12 form-group">
                  <label>Contact Person</label>
                  <input class="form-control" id="contact_person" required>
              </div>
              <div class="col-md-12 form-group">
                  <label>Mobile Number</label>
                  <input class="form-control" id="phone">
              </div>
              <div class="col-md-12 form-group">
                  <label>Email</label>
                  <input class="form-control" id="email">
              </div>
          </div>
          
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
         <button class="btn btn-primary">Save</button>
         </form>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>


@endsection
@section('vendor-script')
{{-- vednor js files --}}
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>-
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script>
  <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
  <script src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>
@endsection
@section('myscript')
<script>




// function process_add_brand(e){
    
//     e.preventDefault();
//     $('#brand_modal').modal('hide');
    
//     $.post("{{route('brand.add_ajax')}}",
//       {
//         code: $('#brand_code').val(),
//         name: $('#brand_name').val()
//       },
//       function(data, status){
//          if(data.success==1){
//              var opstr = '<option selected value="'+data.id+'">'+$('#brand_code').val()+' - '+$('#brand_name').val()+'</option>';
//              $('#brand').append(opstr);
             
//              $(".s280").select2({
//                 dropdownAutoWidth: true,
//                 width: '70%'
//               });
              
//               Swal.fire(
//                   'success !',
//                   'Brand Added!',
//                   'success'
//                 );
             
//          }else{
//              Swal.fire(
//                   'Error !',
//                   'Brand Not Added !',
//                   'error'
//                 );
//          }
//       });
  
// }


// function process_add_category(e){
    
    
//     e.preventDefault();
//     $('#category_modal').modal('hide');
    
//     $.post("{{route('category.add_ajax')}}",
//       {
//         code: $('#category_code').val(),
//         name: $('#category_name').val(),
//         parent:$('#category2').val()
//       },
//       function(data, status){
//          if(data.success==1){
//              var opstr = '<option selected value="'+data.id+'">'+$('#category_code').val()+' - '+$('#category_name').val()+'</option>';
//              $('#category').append(opstr);
//              $('#category2').append(opstr);
             
//              $(".s280").select2({
//                 dropdownAutoWidth: true,
//                 width: '70%'
//               });
              
//               $(".s2").select2({
//                 dropdownAutoWidth: true,
//                 width: '100%'
//               });
              
//               Swal.fire(
//                   'success !',
//                   'Category Added!',
//                   'success'
//                 );
             
//          }else{
//              Swal.fire(
//                   'Error !',
//                   'Category Not Added !',
//                   'error'
//                 );
//          }
//       });
    
    
// }


function process_add_supplier(e){
  e.preventDefault();
  $('#supplier_modal').modal('hide');
  
  $.post("{{route('supplier.add_ajax')}}",
    {
      company: $('#company').val(),
      contact_person: $('#contact_person').val(),
      phone:$('#phone').val(),
      email:$('#email').val()
    },
    function(data, status){
       if(data.success==1){
           var opstr = '<option selected value="'+data.id+'">'+$('#company').val()+'</option>';
           $('#supplier').append(opstr);
           $('#supplier2').append(opstr);
           
           $(".s280").select2({
              dropdownAutoWidth: true,
              width: '70%'
            });
            
            $(".s2").select2({
              dropdownAutoWidth: true,
              width: '100%'
            });
            
            Swal.fire(
                'success !',
                'Supplier Added!',
                'success'
              );
           
       }else{
           Swal.fire(
                'Error !',
                'Supplier Not Added !',
                'error'
              );
       }
    });
}

$("#type").change(function () {
  if ($("#type option:selected").val() == 'set') {
    $('.set').css('display','block');
  }else{
    $('.set').css('display','none');
  }
});

function addProducts(e) {
  if ($("#ap_sku").val()){
    var product_row = '<div class="row product_row">'+
                        '<div class="form-group col-md-6 sku">'+
                          '<input type="hidden" class="form-control id" name="sku_id[]" value="'+$("#ap_sku").val()+'">'+
                          '<input class="form-control" name="sku_name[]" value="'+$("#ap_sku option:selected").text()+'" readonly>'+
                        '</div>'+
                        '<div class="form-group col-md-4"><input type="number" class="form-control" name="set_quantity[]" value="'+$("#quantity").val()+'"></div>'+
                        '<div class="col-md-2"><button type="button" onclick="removeProduct(event)" class="btn btn-danger remove">\u00D7</button></div>'+
                      '</div>';
    $('#products_list').append(product_row);
    $("#ap_sku option:selected").attr('disabled','disabled');
    $('#ap_sku').val(null).trigger('change');
    $('input[name=quantity]').val('');
  }
}

function removeProduct(e) {
  $(document).on('click', '.remove', function() {
    var sku_val = $(this).parent().siblings('.sku').children('.id').val();
    $("#ap_sku option[value="+sku_val+"]").removeAttr('disabled');
    $(this).parent().parent().remove();
  });
}

$(".select2").select2({
  dropdownAutoWidth: true,
  width: '100%'
});


</script>
 
@endsection
