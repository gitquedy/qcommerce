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
            <th>Next Payment Date</th>
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
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>
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
            { data: 'paid_status', name: 'paid_status', className: 'quick_update_box'},
            { data: 'payment_date', name: 'payment_date'},
            { data: 'next_payment_date', name: 'next_payment_date'},
            { data: 'created_at', name: 'created_at', searchable: false, visible: false }
        ];
    var table_route = {
            url: '{{ route('billing.index') }}'
            };
    var buttons = [];
    var order = [10, 'desc'];
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

        const swal2 = Swal.mixin({
            customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        })


        $(document).on('click', '.quick_update_box', function() {
            var td = $(this);
            td.find("p").hide();
            td.find('input').show().focus().on('keypress',function(e) {
                if(e.which == 13) {
                    $(this).trigger('focusout');
                }
            });
            td.find('input').show().focus().on('focusout', function() {
                if($(this).val() != $(this).data('defval')) {
                    var name = $(this).data('name');
                    var defval = $(this).data('defval');
                    var status = $(this).val();
                    var billing_id = $(this).data('billing_id');
                    Swal.fire({
                        title: 'Update '+name+' ?',
                        text: "Change value from "+defval+" to "+$(this).val()+" ?",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Change it!'
                    }).then((result) => {
                        if (result.value) {
                            $.ajax({
                                type: "POST",
                                url: '{{ route('billing.quickUpdate') }}',
                                data: {'billing_id': billing_id, 'name': name, 'status': td.find("input").val()},
                                dataType: "JSON",
                                cache: false,
                                success: function (res) {
                                    if (res) {
                                        td.find('input').attr('data-defval', status).data('defval', status).hide();
                                        td.find("p").html(function () {
                                            if (td.find("input").val() == 0) {
                                                return '<span class="badge-pill badge-primary">Unpaid</span>';
                                            }
                                            else if (td.find("input").val() == 1) {
                                                return '<span class="badge badge-pill badge-success">Paid</span>';
                                            }
                                            else if (td.find("input").val() == 2) {
                                                return '<span class="badge badge-pill badge-danger">Failed</span>';
                                            }
                                            else if (td.find("input").val() == 3) {
                                                return '<span class="badge badge-pill badge-warning">Canceled</span>';
                                            }
                                            else if (td.find("input").val() == 4) {
                                                return '<span class="badge badge-pill badge-dark">Suspended</span>';
                                            }
                                            else {
                                                return '<span class="badge badge-pill badge-secondary">Unknown</span>';
                                            }
                                        }).show();
                                        $('#errors').html('');
                                    }
                                    else {
                                        swal2.fire(
                                        'Warning',
                                        'Something went wrong :(',
                                        'error'
                                        );
                                        td.find('input').val(defval).hide();
                                        td.find("p").show();
                                    }
                                },
                                error: function(jqXhr, json, errorThrown){
                                    td.find('input').val(defval).hide();
                                    td.find("p").show();
                                    $('#errors').html('');
                                    $.each(jqXhr.responseJSON.errors, function(key, value) {
                                        $('#errors').append('<div class="alert alert-danger">'+value+'</div');
                                    }); 
                                }
                            });
                        }
                        else {
                        td.find('input').val(defval).hide();
                        td.find("p").show();
                        }
                    })
                }
                else {
                td.find('input').hide();
                td.find("p").show();
                }
            });
        });
    }); 
</script>
@endsection