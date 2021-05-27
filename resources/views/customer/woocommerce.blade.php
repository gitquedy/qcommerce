@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'WooCommerce Customers')

@section('vendor-style')
    {{-- vendor files --}}
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
<section class="card">
    <div class="card-content">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12 shop_filter">
                    <label for="qcommerce" class="btn btn-lg btn-outline-primary mb-1">
                        <img class="shop_logo" src="{{asset('images/shop/icon/qcommerce.png')}}" alt="">
                        Customers
                    </label>
                    <input type="radio" id="qcommerce" name="site" value="customers">
                    <label for="woocommerce" class="btn btn-lg btn-outline-primary mb-1 active">
                        <img class="shop_logo" src="{{asset('images/shop/icon/woocommerce.png')}}" alt="">
                        WooCommerce
                    </label>
                    <input type="radio" id="woocommerce" name="site" value="woocommerce">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-sm-12">
                    @include('order.components.shopFilter')
                    <div class="btn-group" id="chip_area_shop"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="data-list-view" class="data-list-view-header">
    {{-- DataTable starts --}}
    <div class="table-responsive">
        <table class="table data-list-view">
        <thead>
            <tr>
            <th>For Checkbox</th>
            <th>Shop</th>
            <th>Name</th>
            <th>Address</th>
            <th>Email</th>
            <th>Mobile Number</th>
            <th>Number of Orders</th>
            <th>Total Worth of Orders</th>
            <th>Updated At</th>
            </tr>
        </thead>
        </table>
    </div>
    {{-- DataTable ends --}}
</section>
{{-- Data list view end --}}


<form action="{{route('order.print_shipping_mass')}}" method="post" id="mass_print_form">
    @csrf
    <input type="hidden" id="mass_print_val" name="ids">
</form>

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
<!-- datatables -->
<script type="text/javascript">
    var id = "{{ $request->get('site') == 'shopee' ?  'ordersn' : 'id'  }}"
    var columnns = [
            { data: id, name: id, orderable : false},
            { data: 'shop.short_name', name: 'shop.short_name' },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'address', name: 'address' },
            { data: 'email', name: 'email' },
            { data: 'mobile_num', name: 'mobile_num' },
            { data: 'orders_count', name: 'orders_count' },
            { data: 'orders_worth', name: 'orders_worth' },
            { data: 'updated_at', name: 'updated_at', searchable: false, visible: false }
        ];
    var table_route = {
            url: '{{ route('customer.woocommerce') }}',
            data: function (data) {
            }
        };
    var buttons = [
            
            ];
    var order = [8, 'desc'];
    var BInfo = true;
    var bFilter = true;
function created_row_function(row, data, dataIndex){

}
var aLengthMenu = [[20, 50, 100, 500],[20, 50, 100, 500]];
var pageLength = 20;
</script>
<script type="text/javascript">
    $(document).on('click', '.toggle_view_modal', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).data('action'),
            method: "POST",
            data: {},
            success:function(result)
            {
                $('.view_modal').html(result).modal();
            }
        });
    });

    $('#qcommerce').click(function() {
      window.location = '{{ route('customer.index') }}';
    });
</script> 
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
@endsection
