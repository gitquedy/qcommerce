@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'View Warehouse')

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

<section id="floating-label-layouts">
  <div class="row match-height">
      <div class="col-md-12 col-12">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Warehouse Details</h4>
              </div>
              <hr>
              <div class="card-content">
                  <div class="card-body">
                      <h1>{{$warehouse->name}} <small>({{$warehouse->code}})</small> </h1>
                      <h4 class="text-default">{{$warehouse->phone}}</h4>
                      <h4 class="text-primary">{{$warehouse->email}}</h4>
                      <h4 class="text-secondary">{{$warehouse->address}}</h4>
                      <hr>
                      <div class="row text-center">
                        <div class="table-responsive">
                          <table class="table datatables">
                            <thead>
                              <tr>
                                <th>Sku Code</th>
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                              </tr>
                            </thead>
                            <tbody>
                              @forelse($warehouse->items as $wsku) 
                                <tr>
                                  <td>{{$wsku->sku->code}}</td>
                                  <td>
                                    @if($wsku->sku->image)
                                      <img src="{{$wsku->sku->image}}" class="product_image">
                                    @else
                                      <img src="{{asset('images/pages/no-img.jpg')}}" class="product_image">
                                    @endif
                                    
                                  </td>
                                  <td>{{$wsku->sku->name}}</td>
                                  <td>{{$wsku->quantity}}</td>
                                </tr>
                              @empty
                              <tr>
                                <td colspan="4">Empty.</td>
                              </tr>
                              @endforelse
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <br>
                  </div>
              </div>
          </div>
      </div>
  </div>
</section>
<!-- // Basic Floating Label Form section end -->
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
@endsection
@section('myscript')
  {{-- Page js files --}}
  <script type="text/javascript">
    $('.select2').select2();
    $('.datatables').DataTable({
      dom: '<"top"><"clear">rt<"bottom"<"actions">p>',
    });
  </script>
<script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
@endsection

