@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Add Price Group')
@section('mystyle')
<style>
    .product_image{
        width:70px;
        height:auto;
    }
</style>
@endsection
@section('content')
<section id="floating-label-layouts">
  <div class="row match-height">
      <div class="col-md-12 col-12">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Price Group Details</h4>
              </div>
              <div class="card-content">
                  <div class="card-body">
                      <form action="{{ action('PriceGroupController@update', $price_group->id) }}" method="POST" class="form" enctype="multipart/form-data">
                          @method('PUT')
                          @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name</label>
                                    <div class="position-relative has-icon-left">
                                      <input type="text" class="form-control" name="name" placeholder="Group Name" value="{{$price_group->name}}">
                                      <div class="form-control-position"> 
                                        <i class="feather icon-user"></i>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group text-right">
                                  <label></label>
                                    <div class="position-relative">
                                        <input type="submit" name="save" class="btn btn-primary mr-1 mb-1 btn_save" value="Save">
                                    </div>
                                </div>
                            </div>
                        </div>   
                        <div class="form-group col-12">
                          <table class="table datatable">
                            <thead>
                              <tr>
                                <th class="text-center">Item</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Enable</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($sku as $item)
                                <tr data-id="{{$item->id}}">
                                  <td>
                                    <div class="media">
                                      <img src="{{$item->image}}" alt="No Image Available" class="d-flex mr-1 product_image">
                                      <div class="media-body">
                                        <h5 class="mt-0">{{$item->name}}</h5>
                                        {{($item->brand)?$item->name:''}}
                                        {{$item->code}}
                                      </div>
                                    </div>
                                  </td>
                                  <td class="text-right p-4">
                                    @php
                                    $val = 0;
                                    $disabled = "";
                                    $checked = "";
                                    if($item->pg_price) {
                                      $val = $item->pg_price;
                                      $checked = "checked";
                                    }
                                    else{
                                      $val = $item->price;
                                      $disabled = "disabled";
                                    }
                                    @endphp
                                    <input type="number" name="item_array[{{$item->id}}][price]" class="form-control text-right input_price" data-original="{{$item->price}}" value="{{$val}}" {{$disabled}}>
                                  </td>
                                  <td class="text-center">
                                    <div class="custom-control custom-switch custom-control-inline">
                                      <input type="checkbox" class="custom-control-input sku_switch" data-sku_id="{{$item->id}}" id="sku_switch_{{$item->id}}" {{$checked}}>
                                      <label class="custom-control-label" for="sku_switch_{{$item->id}}">
                                      </label>
                                    </div>
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                        <div class="form-group col-12"></div>
                        {{-- <div class="row">
                          <div class="col-6">
                           <div class="col-12">
                                <input type="submit" name="save" class="btn btn-primary mr-1 mb-1 btn_save" value="Save">
                               <!--  <button type="reset" class="btn btn-outline-warning mr-1 mb-1">Reset --> </button>
                            </div>
                          </div>
                        </div> --}}
                      </form>
                  </div>

              </div>
          </div>
      </div>


  </div>
</section>
<!-- // Basic Floating Label Form section end -->
@endsection
@section('vendor-script')
<script>
  $(document).on('click', '.sku_switch', function() {
      var status = $(this). is(":checked");
      var tr = $(this).closest('tr');
      if(status) {
        tr.find('.input_price').attr('disabled', false);
      }
      else {
        var orig = tr.find('.input_price').data('original');
        tr.find('.input_price').val(orig).attr('disabled', true); 
      }
  });
</script>
  <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
@endsection

