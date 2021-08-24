@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'View Inventory')

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
                  <h4 class="card-title">Warehouse Inventory</h4>
              </div>
              <hr>
              <div class="card-content">
                  <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <h1>{{$warehouse->name}} <small>({{$warehouse->code}})</small> </h1>
                          <h4 class="text-default">{{$warehouse->phone}}</h4>
                          <h4 class="text-primary">{{$warehouse->email}}</h4>
                          <h4 class="text-secondary">{{$warehouse->address}}</h4>
                        </div>
                        <div class="col text-right">
                        <a class="btn btn-primary no-print" href="{{ route('warehouse.printInventoryReport', ['id' => $warehouse->id]) }}">Print Inventory Report</a>
                        </div>
                      </div>
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
                              @foreach($warehouse->items as $wsku)
                                @if($wsku->quantity != 0)
                                <tr>
                                  <td>{{$wsku->sku->code}}</td>
                                  <td><img src="{{$wsku->sku->SkuImage()}}" class="product_image"></td>
                                  <td>{{$wsku->sku->name}}</td>
                                  <td>{{$wsku->quantity}}</td>
                                </tr>
                               @endif
                              @endforeach
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
    $('.pagination').addClass('justify-content-center');
  </script>
<script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
@endsection

