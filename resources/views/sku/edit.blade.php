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
          
          @if($Sku)
          <form action="{{route('sku.update')}}" method="post">
          @csrf
          <input type="hidden" name="id" value="{!!$Sku->id!!}">
          <div class="row">
              <div class="col-md-6 form-group">
                  <lable>Code</lable>
                  <input class="form-control" value="{!!$Sku->code!!}" name="code" required>
              </div>
              <div class="col-md-6 form-group">
                  <lable>Name</lable>
                  <input class="form-control" value="{!!$Sku->name!!}" name="name" required>
              </div>
              <div class="col-md-6 form-group">
                  <lable>Brand</lable>
                  <div class="form-group">
                      <div class="input-group">
                        <select class="form-control s280" id="brand" name="brand" style="width:80% !important">
                              <option value="" disabled selected></option>
                              @foreach($Brand as $BrandVAL)
                              <option @if($Sku->brand==$BrandVAL->id) {{'selected'}} @endif  value="{{$BrandVAL->id}}">{{$BrandVAL->code." - ".$BrandVAL->name}}</option>
                              @endforeach
                          </select>
                        <div class="input-group-append">
                          <button type="button" data-toggle="modal" data-target="#brand_modal" class="btn btn-outline-primary btn-flat"><i class="fa fa-plus"></i> Add new</button>
                        </div>
                      </div>
                    </div>
              </div>
              <div class="col-md-6 form-group">
                  <lable>Category</lable>
                  <div class="form-group">
                      <div class="input-group">
                        <select class="form-control s280" name="category" id="category">
                              <option value="" disabled selected></option>
                              @foreach($Category as $CategoryVAL)
                              <option @if($Sku->category==$CategoryVAL->id) {{'selected'}} @endif  value="{{$CategoryVAL->id}}">{{$CategoryVAL->code." - ".$CategoryVAL->name}}</option>
                              @endforeach
                          </select>
                        <div class="input-group-append">
                          <button type="button" data-toggle="modal" data-target="#category_modal" class="btn btn-outline-primary btn-flat"><i class="fa fa-plus"></i> Add new</button>
                        </div>
                      </div>
                    </div>
              </div>
              <div class="col-md-6 form-group">
                  <lable>Supplier</lable>
                  <div class="form-group">
                      <div class="input-group">
                        <select class="form-control s280" name="supplier" id="supplier">
                              <option value="" disabled selected></option>
                              @foreach($Supplier as $SupplierVAL)
                              <option @if($Sku->supplier==$SupplierVAL->id) {{'selected'}} @endif  value="{{$SupplierVAL->id}}">{{$SupplierVAL->company}}</option>
                              @endforeach
                          </select>
                        <div class="input-group-append">
                          <button type="button" data-toggle="modal" data-target="#supplier_modal" class="btn btn-outline-primary btn-flat"><i class="fa fa-plus"></i> Add new</button>
                        </div>
                      </div>
                    </div>
              </div>
              <div class="col-md-6"></div>
              <div class="col-md-6 form-group">
                  <lable>Cost</lable>
                  <input type="number" step="any" class="form-control" value="{!!$Sku->cost!!}" name="cost" required>
              </div>
              <div class="col-md-6 form-group">
                  <lable>Price</lable>
                  <input type="number" step="any" class="form-control" value="{!!$Sku->price!!}" name="price" required>
              </div>
              <div class="col-md-6 form-group">
                  <lable>Quantity</lable>
                  <input type="number" class="form-control"  name="quantity" value="{!!$Sku->quantity!!}" required>
              </div>
              <div class="col-md-6 form-group">
                  <lable>Alert Quantity</lable>
                  <input type="number" class="form-control"  name="alert_quantity" value="{!!$Sku->alert_quantity!!}" required>
              </div>
              <div class="col-md-12 text-right">
                  <br/>
                  <button class="btn btn-primary">Save</button>
              </div>
              
          </div>
          
          </form>
          @endif
          
          
          
          
          
          
        
      </div>
    </div>
  </section>
  {{-- Data list view end --}}
  
  
  
  <!-- The Modal -->
<div class="modal" id="brand_modal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add New Brand</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
         
         <form  onsubmit="process_add_brand(event)" >
          <div class="row">
              <div class="col-md-12 form-group">
                  <lable>Code</lable>
                  <input class="form-control" id="brand_code" required>
              </div>
              <div class="col-md-12 form-group">
                  <lable>Name</lable>
                  <input class="form-control" id="brand_name" required>
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




<!-- The Modal -->
<div class="modal" id="category_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add New Category</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
         <form  onsubmit="process_add_category(event)" >
          <div class="row">
              <div class="col-md-12 form-group">
                  <lable>Code</lable>
                  <input class="form-control" id="category_code" required>
              </div>
              <div class="col-md-12 form-group">
                  <lable>Name</lable>
                  <input class="form-control" id="category_name" required>
              </div>
              <div class="col-md-12 form-group">
                  <lable>Parent Category</lable>
                  <select class="form-control s2" name="category_parent" id="category2" >
                              <option value="">select</option>
                              @foreach($Category as $CategoryVAL)
                              <option  value="{{$CategoryVAL->id}}">{{$CategoryVAL->code." - ".$CategoryVAL->name}}</option>
                              @endforeach
                  </select>
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
         <form  onsubmit="process_add_supplier(event)" >
          <div class="row">
              <div class="col-md-12 form-group">
                  <lable>Company</lable>
                  <input class="form-control" id="company" required>
              </div>
              <div class="col-md-12 form-group">
                  <lable>Contact Person</lable>
                  <input class="form-control" id="contact_person" required>
              </div>
              <div class="col-md-12 form-group">
                  <lable>Mobile Number</lable>
                  <input class="form-control" id="phone">
              </div>
              <div class="col-md-12 form-group">
                  <lable>Email</lable>
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



function process_add_brand(eve){
    
    eve.preventDefault();
    $('#brand_modal').modal('hide');
    
    $.post("{{route('brand.add_ajax')}}",
      {
        code: $('#brand_code').val(),
        name: $('#brand_name').val()
      },
      function(data, status){
         if(data.success==1){
             var opstr = '<option selected value="'+data.id+'">'+$('#brand_code').val()+' - '+$('#brand_name').val()+'</option>';
             $('#brand').append(opstr);
             
             $(".s280").select2({
                dropdownAutoWidth: true,
                width: '70%'
              });
              
              Swal.fire(
                  'success !',
                  'Brand Added!',
                  'success'
                );
             
         }else{
             Swal.fire(
                  'Error !',
                  'Brand Not Added !',
                  'error'
                );
         }
      });
  
}


function process_add_category(eve){
    
    
    eve.preventDefault();
    $('#category_modal').modal('hide');
    
    $.post("{{route('category.add_ajax')}}",
      {
        code: $('#category_code').val(),
        name: $('#category_name').val(),
        parent:$('#category2').val()
      },
      function(data, status){
         if(data.success==1){
             var opstr = '<option selected value="'+data.id+'">'+$('#category_code').val()+' - '+$('#category_name').val()+'</option>';
             $('#category').append(opstr);
             $('#category2').append(opstr);
             
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
                  'Category Added!',
                  'success'
                );
             
         }else{
             Swal.fire(
                  'Error !',
                  'Category Not Added !',
                  'error'
                );
         }
      });
    
    
}


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
                'Category Added!',
                'success'
              );
           
       }else{
           Swal.fire(
                'Error !',
                'Category Not Added !',
                'error'
              );
       }
    });
}


    
 </script>
 
@endsection
