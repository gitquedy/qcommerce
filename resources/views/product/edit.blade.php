@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Product Management')

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
    
</style>

<section class="card">
    <div class="card-content">
      <div class="card-body">
          
          
           
          
          
          
          
          
          @if($product)
          <form action="{{route('product.update')}}"  onsubmit="validate(event)" method="post" >
              @csrf
              
              
              
              
              <style>
                  .image_uploader{
                      background-color:#F1F1F1;
                      min-height:200px;
                      border-radius:5px;
                      padding:10px;
                      
                  }
                  .inner_card{
                      background-color:white;
                      min-height:150px;
                      box-shadow: 0px 0px 4px 0px rgba(0,0,0,0.75);
                  }
                  .flex{
                      display:flex;
                  }
                  .upload_btn{
                      border:2px dashed #F1F1F1;
                  }
                  .upload_items{
                      padding:10px;
                  }
                  .image_divr{
                      border:2px dashed #F1F1F1;
                      margin:2px;
                  }
                  .x_button{
                      
                      border:none;
                      width:100%;
                  }
                  .product_image{
                    width:150px;
                    height:auto;
                }
              </style>
              
              
              
              
              <div class="image_uploader">
                  <div class="inner_card flex">
                      <div id="sortable" class="upload_items flex">
                  <?php 
                  $exp = array();
              if($product->Images!=''){
                $exp = explode("|",$product->Images);  
              }
              foreach($exp as $expVAL){ ?>
                  <div class="image_divr ui-state-default">
                      
                          <img draggable="false" src="<?php echo $expVAL;?>" class="product_image">
                          <input type="hidden" value="<?php echo $expVAL;?>" name="Image[]">
                          <br/>
                          <button type="button"  class="x_button" onclick="remove_self(this)"  ><i class="fa fa-times"></i></button>
                          
                  </div>
                  
                  <?php  } ?>
                  </div>
                      <div class="upload_items">
                          <button class="btn upload_btn" type="button" onclick="$('#file').click()"><i class="fa fa-upload"></i> Upload</button>
                          <input type="file" onchange="encodeImageFileAsURL(this)" class="form-control" id="file" style="display:none;"/>
                      </div>
                  </div>
              </div>
              
              


              
              <input type="hidden" name="SellerSku" value="{!! $product->SellerSku!!}">
              <input type="hidden" name="shop_id" id="shop_id" value="{!! $product->shop_id!!}">
              <input type="hidden" name="id" value="{!! $product->id!!}">
              
          
          
          <div classs="form-group">
              <label>Name</label>
              <input type="text" class="form-control"  name="name" value="{!! $product->name!!}">
          </div>
          <br/>
          <div classs="form-group">
              <label>short_description</label>
              <textarea type="text" class="form-control" id="short_description" name="short_description" rows="4" >{!! $product->short_description!!}</textarea>
          </div>
          <br/>
          <div classs="form-group">
              <label>description</label>
              <textarea type="text" id="description" class="form-control"  name="description"  rows="4" >{!! $product->description!!}</textarea>
          </div>
          
          <br/>
          <div classs="form-group">
              <label>brand</label>
              <input type="text" class="form-control" name="brand" value="{!! $product->brand!!}">
          </div>
          
          <br/>
          <div classs="form-group">
              <label>model</label>
              <input type="text" class="form-control" name="model" value="{!! $product->model!!}">
          </div>
          
          <br/>
          <div classs="form-group">
              <label>quantity</label>
              <input type="text" class="form-control" name="quantity" value="{!! $product->quantity!!}">
          </div>
          <br/>
          <div classs="form-group">
              <label>min_delivery_time</label>
              <input type="text" class="form-control" name="min_delivery_time" value="{!! $product->min_delivery_time!!}">
          </div>
          <br/>
          <div classs="form-group">
              <label>package_width</label>
              <input type="text" class="form-control" name="package_width" value="{!! $product->package_width!!}">
          </div>
          
          <br/>
          <div classs="form-group">
              <label>color_family</label>
              <input type="text" class="form-control" name="color_family" value="{!! $product->color_family!!}">
          </div>
          
          <br/>
          <div classs="form-group">
              <label>package_length</label>
              <input type="package_length" class="form-control" name="package_length" value="{!! $product->package_length!!}">
          </div>
          
          <br/>
          <div classs="form-group">
              <label>package_height</label>
              <input type="text" class="form-control" name="package_height" value="{!! $product->package_height!!}">
          </div>
          <br/>
          <div classs="form-group">
              <label>special_price</label>
              <input type="text" class="form-control" name="special_price" value="{!! $product->special_price!!}">
          </div>
          <br/>
          <div classs="form-group">
              <label>price</label>
              <input type="text" class="form-control" name="price" value="{!! $product->price!!}">
          </div>
          
          <br/>
          <div classs="form-group">
              <label>package_weight</label>
              <input type="text" class="form-control" name="package_weight" value="{!! $product->package_weight!!}">
          </div>
          
          <br/>
          <div classs="form-group">
              <label>Available</label>
              <input type="text" class="form-control" name="Available" value="{!! $product->Available!!}">
          </div>
          <br/>
          
          

         
          <br/>
          <div classs="form-group">
              <label>Images</label>
              <?php
              
              $exp = array();
              
              if($product->Images!=''){
                $exp = explode("|",$product->Images);  
              }
              
              
              
              
              
              
              ?>
              
              
             
              
              
              
          </div>
          <br/>
          <div class="text-right">
              <button type="submit" class="btn btn-primary">Save</button>
          </div>
          
          
           </form>
          
          

          @endif
        
        
        
      </div>
    </div>
  </section>
  {{-- Data list view end --}}
  
  <!-- The Modal -->
<div class="modal" id="image_upload_mdl">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Upload Image</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        
        <input type="file" onchange="encodeImageFileAsURL(this)" class="form-control" />

        <br/>
        
        <div id="loading_div" class="text-info text-center"  style="display:none;"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
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
<script
  src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
  integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
  crossorigin="anonymous"></script>
<script>
 CKEDITOR.replace( 'short_description', {
      uiColor: '#ffffff',
      height: 200
    } );
    
CKEDITOR.replace( 'description', {
      uiColor: '#ffffff',
      height: 500
    } );
    
    
    function validate(eve){
        
        
    }
    
    
    function encodeImageFileAsURL(element) {
      var file = element.files[0];
      var reader = new FileReader();
      reader.onloadend = function() {
          upload_api(reader.result);
      }
      reader.readAsDataURL(file);
    }
    
    
    function upload_api(base_64_image){
        
        $('#loading_div').show();
        
         $.post("{{route('product.upload_image')}}",
        {
          shop_id: $('#shop_id').val(),
          base_64_image:base_64_image
        },
        function(data,status){
            
            $('#image_upload_mdl').modal('hide');
            $('#loading_div').hide();
            
            
            
            try {
                var OBJ =  JSON.parse(data);
                }
                catch(err) {
                  Swal.fire(
                  'Error !',
                  'Error in Parse Json Response',
                  'error'
                )
                  return false;
                }
            if(OBJ.code==302){
                Swal.fire(
                  'Error !',
                  OBJ.detail[0]['message'],
                  'error'
                )
                return false;
            }
            
            
            if(OBJ.code==0){
                var image_url = OBJ.data.image.url;
            }
            
            var image_string = '<div class="image_divr ui-state-default ui-sortable-handle">'+
                          '<img draggable="false" src="'+image_url+'" class="product_image">'+
                          '<input type="hidden" value="'+image_url+'" name="Image[]">'+
                          '<br>'+
                          '<button type="button" class="x_button" onclick="remove_self(this)"><i class="fa fa-times"></i></button>'+
                  '</div>';
            
            // var image_string = '<tr class="image_row">'+
            //           '<td>'+
            //               '<a href="'+image_url+'" target="_blank"><img src="'+image_url+'" class="product_image"></a>'+
            //               '<input type="hidden" value="'+image_url+'" name="Image[]">'+
            //           '</td>'+
            //           '<td>'+
            //               '<button type="button" onclick="remove_self(this)" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></button>'+
            //           '</td>'+
            //       '</tr>';
                  
            $('#sortable').append(image_string);
            console.log(OBJ);
                

        });
    }
    
    
    
    function remove_self(ele){
        $(ele).parent().remove()
    }
    
    
    $( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
  } );
    
    
 </script>
 
@endsection
