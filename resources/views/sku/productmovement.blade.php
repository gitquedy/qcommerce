@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Product Movement')

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

    <style>
    .product_image{
        width:100px;
        height:auto;
    }
    
    option[disabled]{
        background-color:#F8F8F8;
    }
    </style>
@endsection

@section('content')
<section>
    <div class="row match-height">
        <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-content">
                    <img class="card-img-top img-fluid" src="{{ $sku->SkuImage() }}" alt="Card image cap">
                    <div class="card-body">
                        <h5>{{ $sku->code }} - {{ $sku->name }}</h5>
                        <table class="table">
                            <thead>
                                <tr class="text-center">
                                    <th>Cost</th>
                                    <th>Price</th>
                                    <th>Total Quantity</th>
                                    <th>Alert Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><p class="card-text text-center text-danger mb-0"><b>{{ number_format($sku->cost, 2) }}</b></p></td>
                                    <td><p class="card-text text-center text-success mb-0"><b>{{ number_format($sku->price, 2) }}</b></p></td>
                                    <td><p class="card-text text-center text-primary mb-0"><b>{{ number_format($sku->quantity, 2) }}</b></p></td>
                                    <td><p class="card-text text-center text-warning mb-0"><b>{{ number_format($sku->alert_quantity, 2) }}</b></p></td>
                                </tr>
                            </tbody>
                        </table> 
                        <table class="table">
                            <thead>
                                <tr class="text-center">
                                    <th>Warehouse</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sku->warehouse_items as $items)
                                    <tr>
                                        <td><p class="card-text text-center text-default mb-0"><b>{{$items->warehouse->name}}</b></p></td>
                                        <td><p class="card-text text-center text-primary mb-0"><b>{{ number_format($items->quantity, 2) }}</b></p></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table> 
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="filter-checkbox shop_filter row">
            @foreach($all_warehouse as $warehouse)
                <div class="col">
                    <label for="{{ $warehouse->id }}" class="btn btn-outline-primary">
                        {{ ucfirst($warehouse->name) }}
                    </label>
                    <input type="radio" id="{{ $warehouse->id }}" name="warehouse" value="{{ $warehouse->id }}">
                </div>
            @endforeach
        </div> -->
        <div class="additional_custom_filter">
            <div class="dataTables_length" id="DataTables_Table_0_warehouse">
                <label>
                    <select name="warehouse" class="selectFilter custom-select custom-select-sm form-control form-control-sm w-100" id="warehouse">
                        @foreach($all_warehouse as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </div>
        {{-- Data list view starts --}}
        <div id="data-list-view" class="col-xl-9 col-md-6 col-sm-12 data-list-view-header">
            {{-- DataTable starts --}}
            <div class="table-responsive">
                <table class="table data-list-view">
                    <thead>
                        <tr>
                            <th class="dt-checkboxes-cell dt-checkboxes-select-all sorting_disabled">
                                <input type="checkbox">
                            </th>
                            <th>Date</th>
                            <th>Ref No. / Order No.</th>
                            <th>Adjustment ID</th>
                            <th>Sales ID</th>
                            <th>Transfer ID</th>
                            <th>Purchase ID</th>
                            <th>Order ID</th>
                            <th>Type</th>
                            <th>Warehouse</th>
                            <th>Quantity Changed</th>
                            <th>Items Remaining</th>
                        </tr>
                    </thead>
                </table>
            </div>
            {{-- DataTable ends --}}
        </div>
        {{-- Data list view end --}}
    </div>
</section>
@endsection

@section('vendor-script')
    {{-- vendor js files --}}
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
            { data: 'date', name: 'date'},
            { data: 'ref_order_no', name: 'ref_order_no'},
            { data: 'adjustment_id', name: 'adjustment_id', visible: false},
            { data: 'sales_id', name: 'sales_id', visible: false},
            { data: 'transfer_id', name: 'transfer_id', visible: false},
            { data: 'purchase_id', name: 'purchase_id', visible: false},
            { data: 'order_id', name: 'order_id', visible: false},
            { data: 'type', name: 'type' },
            { data: 'warehouse', name: 'warehouse_id' },
            { data: 'quantity', name: 'quantity' },
            { data: 'items_remaining', name: 'new_quantity' },
        ];
        var table_route = {
          url: '{{ route('sku.productmovement', $sku->id) }}',
          data: function (data) {
            // data.warehouse = $('input[name=warehouse]:checked').val();
            data.warehouse = $("#warehouse").val();
          }
        };
        var buttons = [];
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
            var additional_custom_filter = $(".additional_custom_filter").html();
            $(".action-filters").prepend(additional_custom_filter);
            $(".additional_custom_filter").html('');

            // var filterCheckbox = $(".filter-checkbox");
            // filterCheckbox.insertAfter($(".top .actions .dt-buttons"));

            // $(".top").addClass("row align-items-center");
            // $(".action-btns").addClass("col");
            // $(".action-filters").addClass("col-md-auto");
            
            // $("input[name=warehouse]:first").attr('checked', true);
            // var warehouse = $('input[name=warehouse]:checked').val();
            // $('label[for="'+warehouse+'"]').addClass('active');
            // table.ajax.reload();
            
            // $('input[name=warehouse]').change(function() {
            //     $('input[name=warehouse]').map(function () {
            //         if ($(this).val() == $('input[name=warehouse]:checked').val()) {
            //             $('label[for="'+$(this).val()+'"]').addClass('active');
            //         }
            //         else {
            //             $('label[for="'+$(this).val()+'"]').removeClass('active');
            //         }
            //     });
            //     table.ajax.reload();
            // });
        });
    </script>
@endsection