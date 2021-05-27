@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Customer Management')

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
<section class="card">
  <div class="card-content">
    <div class="card-body">
      <div class="row">
        <div class="col-sm-12 shop_filter">
            <label for="site1" class="btn btn-lg btn-outline-primary mb-1 active">
              <img class="shop_logo" src="{{asset('images/shop/icon/qcommerce.png')}}" alt="">
              Customers
            </label>
            <label for="site4" class="btn btn-lg btn-outline-primary mb-1">
              <img class="shop_logo" src="{{asset('images/shop/icon/woocommerce.png')}}" alt="">
              WooCommerce
            </label>
            <input type="radio" id="site1" name="site" value="customers">
            <input type="radio" id="site4" name="site" value="woocommerce">
        </div>
      </div>
    </div>
  </div>
</section>

<section id="data-list-view" class="data-list-view-header">
<!--     <div class="action-btns d-none">
      <div class="btn-dropdown mr-1 mb-1">
        <div class="btn-group dropdown actions-dropodown">
          <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Actions
          </button>
          <div class="dropdown-menu">
           
          </div>
        </div>
      </div>
    </div> -->

    {{-- DataTable starts --}}
    <div class="table-responsive">
      <table class="table data-list-view">
        <thead>
          <tr>
            <th>For Checkbox</th>
            <th>Name</th>
            <th>Email</th>
            <th>Price Group</th>
            <th>Last Update</th>
            <th>Balance</th>
            <th>Deposit</th>
            <th>Action</th>
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
            { data: 'customer_name', name: 'customer_name' },
            { data: 'email', name: 'email' },
            { data: 'price_group_name', name: 'price_group_name' },
            { data: 'updated_at', name: 'updated_at' },
            { data: 'balance', name: 'balance' },
            { data: 'total_deposits', name: 'total_deposits' },
            { data: 'action', name: 'action' },
        ];
  var table_route = {
          url: '{{ route('customer.index') }}',
          data: function (data) {
            }
        };
  var buttons = [
          { text: "<i class='feather icon-plus'></i> Add New",
            action: function() {
                window.location = '{{ route('customer.create') }}';
            },
            className: "btn-outline-primary margin-r-10"}
            ];
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

    $('#site4').click(function() {
      window.location = '{{ route('customer.woocommerce') }}';
    });
</script> 
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
@endsection
