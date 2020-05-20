
@extends('layouts/contentLayoutMaster')

@section('title', 'Dashboard Analytics')

@section('vendor-style')
        <!-- vendor css files -->
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/tether-theme-arrows.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/tether.min.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/shepherd-theme-default.css')) }}">
@endsection
@section('page-style')
        <!-- Page css files -->
        <link rel="stylesheet" href="{{ asset(mix('css/pages/dashboard-analytics.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('css/pages/card-analytics.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('css/plugins/tour/tour.css')) }}">
  @endsection
<style>
    .shop_logo {
      width: 80px;
    }
    .shop_logo img {
      width: 100%;
    }

    <?php
    
    if(isset($colour)){
    
    foreach($colour as $colorDATA){ ?>
    .<?php echo $colorDATA;?>{
        color:#<?php echo $colorDATA;?>;
    }
    <?php } } ?>
</style>
  @section('content')
    {{-- Dashboard Analytics Start --}}
    <section id="dashboard-analytics">
      <div class="row">
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex flex-column align-items-start pb-0">
                    <div class="avatar bg-rgba-primary p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-users text-primary font-medium-5"></i>
                        </div>
                    </div>
                    <h2 class="text-bold-700 mt-1"> {!!$total_users!!}</h2>
                    <p class="mb-0">Total Users</p>
                </div>
                <div class="card-content">
                    <div id="total-user-chart"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex flex-column align-items-start pb-0">
                    <div class="avatar bg-rgba-success p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-credit-card text-success font-medium-5"></i>
                        </div>
                    </div>
                    <h2 class="text-bold-700 mt-1"> {!!$total_shops!!}</h2>
                    <p class="mb-0">Total Shops</p>
                </div>
                <div class="card-content">
                    <div id="total-shop-chart"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex flex-column align-items-start pb-0">
                    <div class="avatar bg-rgba-danger p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-shopping-cart text-danger font-medium-5"></i>
                        </div>
                    </div>
                    <h2 class="text-bold-700 mt-1">{!!$total_orders_today!!}</h2>
                    <p class="mb-0">Total Orders Today</p>
                </div>
                <div class="card-content">
                    <div id="total-orders-chart"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex flex-column align-items-start pb-0">
                    <div class="avatar bg-rgba-danger p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-shopping-cart text-danger font-medium-5"></i>
                        </div>
                    </div>
                    <h2 class="text-bold-700 mt-1">{!!$total_sales_today!!}</h2>
                    <p class="mb-0">Total Sales Today</p>
                </div>
                <div class="card-content">
                    <div id="total-sales-chart"></div>
                </div>
            </div>
        </div>
      </div>
    </section>

    <script>

        var total_user_count_label = [];
        var total_user_count_val = [];
        <?php if(isset($total_user_count)){ 
        foreach($total_user_count as $KeyUserCount => $VALUserCount){ ?>
        total_user_count_label.push('<?php echo $KeyUserCount; ?>');
        total_user_count_val.push(parseFloat("<?php echo $VALUserCount;?>"));
        
        <?php } }  ?>

        var total_shops_count_label = [];
        var total_shops_count_val = [];
        <?php if(isset($total_shops_count)){ 
        foreach($total_shops_count as $KeyShopsCount => $VALShopsCount){ ?>
        total_shops_count_label.push('<?php echo $KeyShopsCount; ?>');
        total_shops_count_val.push(parseFloat("<?php echo $VALShopsCount;?>"));
        
        <?php } }  ?>

        var orderSales_order_val = [];
        var orderSales_order_label = [];
        <?php if(isset($orderSales_orders)){ 
        foreach($orderSales_orders as $KeyHourORD => $VALHourORD){ ?>
        orderSales_order_val.push(parseFloat("<?php echo $VALHourORD;?>"));
        orderSales_order_label.push('<?php echo $KeyHourORD; ?>');
        
        <?php } }  ?>

        var orderSales_data_val = [];
        var orderSales_data_label = [];
        <?php if(isset($orderSales_datas)){ 
        foreach($orderSales_datas as $KeyHourORD => $VALHourORD){ ?>
        orderSales_data_val.push(parseFloat("<?php echo $VALHourORD;?>"));
        orderSales_data_label.push('<?php echo $KeyHourORD; ?>');
        
        <?php } }  ?>
      
</script>

  <!-- Dashboard Analytics end -->
  @endsection

@section('vendor-script')
        <!-- vendor files -->
        <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/extensions/tether.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/extensions/shepherd.min.js')) }}"></script>
@endsection
@section('myscript')
        <!-- Page js files -->
        <script src="{{ asset(mix('js/scripts/pages/dashboard-analytics.js')) }}"></script>
@endsection
