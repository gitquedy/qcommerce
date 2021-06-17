@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Billing')

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

    <div id="errors"></div>

    {{-- DataTable starts --}}
    <div class="table-responsive">
        
      <table class="table data-list-view">
        <thead>
          <tr>
            <th class="dt-checkboxes-cell dt-checkboxes-select-all sorting_disabled">
                <input type="checkbox">
            </th>
            <th>Invoice No.</th>
            <th>Business</th>
            <th>Plan</th>
            <th>Promo Code</th>
            <th>Billing Period</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Payment Date</th>
            <th>Due Date</th>
            <th>Created At</th>
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
  <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection
@section('myscript')
{{-- Page js files --}}
<!-- datatables -->
<script type="text/javascript">
    var columnns = [
            { data: 'id',
            name: 'id' ,
            "render": function (){
                    return '<input type="checkbox" class="dt-checkboxes">';
                },
                className:'dt-checkboxes-cell'
            },
            { data: 'invoice_no', name: 'invoice_no'},
            { data: 'business_id', name: 'business_id'},
            { data: 'plan_id', name: 'plan_id'},
            { data: 'promocode', name: 'promocode'},
            { data: 'billing_period', name: 'billing_period'},
            { data: 'amount', name: 'amount'},
            { data: 'paid_status', name: 'paid_status'},
            { data: 'payment_date', name: 'payment_date'},
            { data: 'next_payment_date', name: 'next_payment_date'},
            { data: 'created_at', name: 'created_at', searchable: false, visible: false }
        ];
    var table_route = {
            url: '{{ route('billing.overdue') }}'
            };
    var buttons = [];
    var order = [10, 'desc'];
    var BInfo = true;
    var bFilter = true;
    function created_row_function(row, data, dataIndex){
        $(row).attr('data-id', JSON.parse(data.id));
        if(data['products_count'] < 1){
            $(row).addClass('text-danger bold font-weight-bold');
        }
    }

    var aLengthMenu = [[20, 50, 100, 500],[20, 50, 100, 500]];
    var pageLength = 20;
</script>
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        var additional_custom_filter = $(".additional_custom_filter").html();
        $(".action-filters").prepend(additional_custom_filter);
        $(".additional_custom_filter").html('');


        $(".select2").select2({
            dropdownAutoWidth: true,
            width: '100%'
        });

        const swal2 = Swal.mixin({
            customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        });
    }); 
</script>
@endsection