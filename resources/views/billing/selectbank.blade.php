@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Pay')

@section('vendor-style')
    {{-- vendor files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection
@section('mystyle')
    {{-- Page css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/pages/data-list-view.css')) }}">
@endsection

@section('content')
<section class="row match-height">
    <div class="col-xl-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Invoice #{{ $billing->invoice_no }}</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <input type="hidden" id="billing_id" name="billing_id" value="{{$billing->id}}">
                    <br>
                    <div class="d-flex justify-content-between">
                        <div class="mr-1">Invoice Date: {{isset($billing->payment_date)?Carbon\Carbon::parse($billing->payment_date)->subDays(1)->toFormattedDateString():'Month dd, yyyy'}}</div>
                        <div class="ml-1">Invoice Due Date: {{ isset($billing->payment_date)?Carbon\Carbon::parse($billing->payment_date)->toFormattedDateString():'Month dd, yyyy' }}</div>
                    </div>
                    <br>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{$billing->plan->name}} Subscription Plan<br>({{ isset($billing->payment_date)?Carbon\Carbon::parse($billing->payment_date)->toFormattedDateString():'mm/dd/yy' }} - {{ isset($billing->next_payment_date)?Carbon\Carbon::parse($billing->next_payment_date)->subDays(1)->toFormattedDateString():'mm/dd/yy'}})</td>
                                <td>Php{{$billing->amount}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Data list view starts --}}
    <div id="data-list-view" class="data-list-view-header col-xl-9 col-md-6 col-sm-12">
        {{-- DataTable starts --}}
        <div class="table-responsive">
            <table class="table data-list-view">
                <thead>
                    <tr>
                        <th class="dt-checkboxes-cell dt-checkboxes-select-all sorting_disabled">
                            <input type="checkbox">
                        </th>
                        <th>Bank</th>
                        <th>Account Name</th>
                        <th>Account Number</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
        {{-- DataTable ends --}}
    </div>
    {{-- Data list view end --}}
</section>
@endsection

@section('vendor-script')
    {{-- vednor js files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection
@section('myscript')
    {{-- Page js files --}}
    <script type="text/javascript">
        var columnns = [
            { data: 'id',
            name: 'id' ,
            "render": function (){
                    return '<input type="checkbox" class="dt-checkboxes">';
                },
                className:'dt-checkboxes-cell',
            },
            { data: 'bank', name: 'bank'},
            { data: 'account_name', name: 'account_name'},
            { data: 'account_number', name: 'account_number'},
            { data: 'action', name: 'action', orderable : false}
        ];
        var table_route = {
                url: '{{ route('billing.pay', ['billing_id' => $billing->id]) }}'
            };
        var buttons = [];
        var order = [];
        var BInfo = true;
        var bFilter = true;
        function created_row_function(row, data, dataIndex){
            $(row).attr('data-id', JSON.parse(data.id));
        }
        var aLengthMenu = [[20, 50, 100, 500],[20, 50, 100, 500]];
        var pageLength = 20;
    </script>
    <script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script>
@endsection