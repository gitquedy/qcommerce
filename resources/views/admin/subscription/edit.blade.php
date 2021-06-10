@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Subcription Inclusions')

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
    .product_image{
        width:100px;
        height:auto;
    }
</style>

<section class="card">
    <div class="card-header">
        <h4 class="card-title">Plan Name</h4>
    </div>
    <div class="card-content">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{route('subscription.update')}}" method="post">
                @csrf
                <input type="hidden" name="id" value="{!!$subs->id!!}">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Order Processing</label>
                        <input type="number" class="form-control" value="{!!$subs->order_processing!!}" name="order_processing">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Sales Channel</label>
                        <div class="form-control">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="sales_channels[]" id="lazada" value="Lazada">
                                <label class="form-check-label" for="lazada">Lazada</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="sales_channels[]" id="shopee" value="Shopee">
                                <label class="form-check-label" for="shopee">Shopee</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="sales_channels[]" id="woocommerce" value="Woocommerce">
                                <label class="form-check-label" for="woocommerce">WooCommerce</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>No. of Users</label>
                        <input type="number" class="form-control" value="{!!$subs->users!!}" name="users">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Accounts/Marketplace</label>
                        <input type="number" class="form-control" value="{!!$subs->accounts_marketplace!!}" name="accounts_marketplace">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Return Reconcillation</label>
                        <div class="form-control">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="return_recon" id="return_yes" value=1>
                                <label class="form-check-label" for="return_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="return_recon" id="return_no" value=0>
                                <label class="form-check-label" for="return_no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Payment Reconcillation</label>
                        <div class="form-control">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment_recon" id="payment_yes" value=1>
                                <label class="form-check-label" for="payment_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment_recon" id="payment_no" value=0>
                                <label class="form-check-label" for="payment_no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Shipping Overcharge Reconcillation</label>
                        <div class="form-control">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="shipping_overcharge_recon" id="shipping_yes" value=1>
                                <label class="form-check-label" for="shipping_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="shipping_overcharge_recon" id="shipping_no" value=0>
                                <label class="form-check-label" for="shipping_no">No</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="card-header">
                        <h4 class="card-title">Inventory</h4>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Inventory Management</label>
                        <div class="form-control">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inventory_management" id="inventory_yes" value=1>
                                <label class="form-check-label" for="inventory_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inventory_management" id="inventory_no" value=0>
                                <label class="form-check-label" for="inventory_no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Sync Inventory</label>
                        <div class="form-control">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="sync_inventory" id="sync_yes" value=1>
                                <label class="form-check-label" for="sync_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="sync_inventory" id="sync_no" value=0>
                                <label class="form-check-label" for="sync_no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>No. of Warehouses</label>
                        <input type="number" class="form-control" value="{!!$subs->no_of_warehouse!!}" name="no_of_warehouse">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Stock Transfer</label>
                        <div class="form-control">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="stock_transfer" id="transfer_yes" value=1>
                                <label class="form-check-label" for="transfer_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="stock_transfer" id="transfer_no" value=0>
                                <label class="form-check-label" for="transfer_no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Purchase Orders</label>
                        <div class="form-control">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="purchase_orders" id="purchase_yes" value=1>
                                <label class="form-check-label" for="purchase_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="purchase_orders" id="purchase_no" value=0>
                                <label class="form-check-label" for="purchase_no">No</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="card-header">
                        <h4 class="card-title">Offline Sales</h4>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Add Sales</label>
                        <div class="form-control">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="add_sales" id="sales_yes" value=1>
                                <label class="form-check-label" for="sales_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="add_sales" id="sales_no" value=0>
                                <label class="form-check-label" for="sales_no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Customers Management</label>
                        <div class="form-control">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="customers_management" id="customers_yes" value=1>
                                <label class="form-check-label" for="customers_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="customers_management" id="customers_no" value=0>
                                <label class="form-check-label" for="customers_no">No</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="card-header">
                        <h4 class="card-title">Reports</h4>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Stock Alert Monitoring</label>
                        <div class="form-control">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="stock_alert_monitoring" id="alert_yes" value=1>
                                <label class="form-check-label" for="alert_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="stock_alert_monitoring" id="alert_no" value=0>
                                <label class="form-check-label" for="alert_no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Out of Stock</label>
                        <div class="form-control">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="out_of_stock" id="outofstock_yes" value=1>
                                <label class="form-check-label" for="outofstock_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="out_of_stock" id="outofstock_no" value=0>
                                <label class="form-check-label" for="outofstock_no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Items Not Moving</label>
                        <div class="form-control">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="items_not_moving" id="notmoving_yes" value=1>
                                <label class="form-check-label" for="notmoving_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="items_not_moving" id="notmoving_no" value=0>
                                <label class="form-check-label" for="notmoving_no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Daily Sales</label>
                        <div class="form-control">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="daily_sales" id="daily_yes" value=1>
                                <label class="form-check-label" for="daily_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="daily_sales" id="daily_no" value=0>
                                <label class="form-check-label" for="daily_no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Monthly Sales</label>
                        <div class="form-control">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="monthly_sales" id="monthly_yes" value=1>
                                <label class="form-check-label" for="monthly_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="monthly_sales" id="monthly_no" value=0>
                                <label class="form-check-label" for="monthly_no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Top Selling Products</label>
                        <div class="form-control">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="top_selling_products" id="topselling_yes" value=1>
                                <label class="form-check-label" for="topselling_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="top_selling_products" id="topselling_no" value=0>
                                <label class="form-check-label" for="topselling_no">No</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 text-right">
                    <br><button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@section('vendor-script')
{{-- vendor js files --}}
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
<script type="text/javascript">
    var channel = {!!json_encode($subs->sales_channels)!!};
    channel_array = channel.split("/");
    channel_array.map(function(shop) {
        $("input[name='sales_channels[]'][value="+shop+"]").prop("checked",true);
    });
    $("input[name=return_recon][value={!!$subs->return_recon!!}]").prop("checked",true);
    $("input[name=payment_recon][value={!!$subs->payment_recon!!}]").prop("checked",true);
    $("input[name=shipping_overcharge_recon][value={!!$subs->shipping_overcharge_recon!!}]").prop("checked",true);
    $("input[name=inventory_management][value={!!$subs->inventory_management!!}]").prop("checked",true);
    $("input[name=sync_inventory][value={!!$subs->sync_inventory!!}]").prop("checked",true);
    $("input[name=stock_transfer][value={!!$subs->stock_transfer!!}]").prop("checked",true);
    $("input[name=purchase_orders][value={!!$subs->purchase_orders!!}]").prop("checked",true);
    $("input[name=add_sales][value={!!$subs->add_sales!!}]").prop("checked",true);
    $("input[name=customers_management][value={!!$subs->customers_management!!}]").prop("checked",true);
    $("input[name=stock_alert_monitoring][value={!!$subs->stock_alert_monitoring!!}]").prop("checked",true);
    $("input[name=out_of_stock][value={!!$subs->out_of_stock!!}]").prop("checked",true);
    $("input[name=items_not_moving][value={!!$subs->items_not_moving!!}]").prop("checked",true);
    $("input[name=daily_sales][value={!!$subs->daily_sales!!}]").prop("checked",true);
    $("input[name=monthly_sales][value={!!$subs->monthly_sales!!}]").prop("checked",true);
    $("input[name=top_selling_products][value={!!$subs->top_selling_products!!}]").prop("checked",true);
</script>
@endsection
