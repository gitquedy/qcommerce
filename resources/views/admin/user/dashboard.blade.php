@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')
@section('title', 'Dashboard')

@section('vendor-style')
        {{-- vednor css files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
@endsection
@section('mystyle')
        {{-- Page css files --}}
        <link rel="stylesheet" href="{{ asset(mix('css/pages/dashboard-ecommerce.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('css/pages/card-analytics.css')) }}">
@endsection

@section('content')
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
  {{-- Dashboard Ecommerce Starts --}}
  <section id="dashboard-ecommerce">
      <div class="row">
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex flex-column align-items-start pb-0">
                    <div class="avatar bg-rgba-primary p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-users text-primary font-medium-5"></i>
                        </div>
                    </div>
                    <h2 class="text-bold-700 mt-1">₱ {!!$monthly_sales!!}</h2>
                    <p class="mb-0">Sales This Month</p>
                </div>
                <div class="card-content">
                    <div id="line-area-chart-1"></div>
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
                    <h2 class="text-bold-700 mt-1">₱ {!!$today_sales!!}</h2>
                    <p class="mb-0">Sales Today</p>
                </div>
                <div class="card-content">
                    <div id="line-area-chart-2"></div>
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
                    <h2 class="text-bold-700 mt-1">{!!$today_order_count!!}</h2>
                    <p class="mb-0">Orders Today</p>
                </div>
                <div class="card-content">
                    <div id="line-area-chart-3"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex flex-column align-items-start pb-0">
                    <div class="avatar bg-rgba-warning p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-package text-warning font-medium-5"></i>
                        </div>
                    </div>
                    <h2 class="text-bold-700 mt-1">{!!$shipped_counter!!}</h2>
                    <p class="mb-0">Pending Order</p>
                </div>
                <div class="card-content">
                    <div id="line-area-chart-4"></div>
                </div>
            </div>
        </div>
      </div>
      
      <?php
      
      
      $pre_month_sale = 0;
      $current_month_sale = 0;
    
      if(isset($combine_chart['pre'])){
          foreach($combine_chart['pre'] as $preVAL){
              $pre_month_sale += $preVAL;
          }
      }
      
      if(isset($combine_chart['current'])){
          foreach($combine_chart['current'] as $currentVAL){
              $current_month_sale += $currentVAL;
          }
      }
      
      
      
      ?>
      <div class="row">
          <div class="col-lg-8 col-md-6 col-12">
              <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-end">
                      <h4 class="card-title">Sales Overview</h4>
                      <p class="font-medium-5 mb-0"><i class="feather icon-settings text-muted cursor-pointer"></i></p>
                  </div>
                  <div class="card-content">
                      <div class="card-body pb-0">
                          <div class="d-flex justify-content-start">
                              <div class="mr-2">
                                  <p class="mb-50 text-bold-600">This Month</p>
                                  <h2 class="text-bold-400">
                                      <sup class="font-medium-1">₱</sup>
                                      <span class="text-success"><?php echo number_format($current_month_sale); ?></span>
                                  </h2>
                              </div>
                              <div>
                                  <p class="mb-50 text-bold-600">Last Month</p>
                                  <h2 class="text-bold-400">
                                      <sup class="font-medium-1">₱</sup>
                                      <span><?php echo number_format($pre_month_sale); ?></span>
                                  </h2>
                              </div>

                          </div>
                          <div id="revenue-chart"></div>
                      </div>
                  </div>
              </div>
              <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-end">
                      <h4 class="card-title">Shop Performance</h4>
                      <p class="font-medium-5 mb-0"><i class="feather icon-settings text-muted cursor-pointer"></i></p>
                  </div>
                  <div class="card-content">
                      <div class="card-body pb-0">
                          <div class="d-flex justify-content-start">
                              <table class="table">
                                <thead>
                                  <tr>
                                    <th colspan="2">Shop Information</th>
                                    <th class="text-right">Today</th>
                                    <th class="text-right">Yesterday</th>
                                    <th class="text-right">This Week</th>
                                    <th class="text-right">This Month</th>
                                  </tr>
                                </thead>
                                <tbody>
                                    @foreach($Shop as $s)
                                      <tr>
                                        <td class="shop_logo" ><img src="{{ asset('images/shop/icon/'.$s->site.'.png') }}" alt=""></td>
                                        <td><b>{{$s->name}}</b><br><small><span class="text-seccondary">{{$s->short_name}}</span> @if($s->active) <span class="text-success ml-1">Active</span> @else <span class="text-danger ml-1">Inactive</span> @endif</small></td>
                                        <td class="text-right pr-1">{{$s->shop_info_data_today}}</td>
                                        <td class="text-right pr-1">{{$s->shop_info_data_yesterday}}</td>
                                        <td class="text-right pr-1">{{$s->shop_info_data_week}}</td>
                                        <td class="text-right pr-1">{{$s->shop_info_data_month}}</td>
                                      </tr>
                                    @endforeach
                                </tbody>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="col-lg-4 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between pb-0">
                    <h4 class="card-title">Orders </h4>
                    <div class="dropdown chart-dropdown">
                        <button class="btn btn-sm border-0 dropdown-toggle px-0" type="button" id="dropdownItem3"
                          data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          Today
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownItem3">
                          <a class="dropdown-item"  onclick="refresh_pie(this,'Today')" >Today</a>
                          <a class="dropdown-item" onclick="refresh_pie(this,'Yesterday')">Yesterday</a>
                          <a class="dropdown-item" onclick="refresh_pie(this,'Last_7_Days')">Last 7 Days</a>
                          <a class="dropdown-item" onclick="refresh_pie(this,'Last_30_Days')">Last 30 Days</a>
                          <a class="dropdown-item" onclick="refresh_pie(this,'This_Month')">This Month</a>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body py-0">
                        <div id="customer-chart"></div>
                    </div>
                    <ul class="list-group list-group-flush customer-info"  id="shop_area">
                        @foreach($Shop as $shop_key => $ShopsVAL)
                      <li class="list-group-item d-flex justify-content-between ">
                          <div class="series-info">
                              <i class="fa fa-circle font-small-3 "></i>
                              <span class="text-bold-600">{!!$ShopsVAL->name!!}</span>
                          </div>
                          <div class="product-result">
                              <span>
                                  <?php
                                  $total = 0;
                                      foreach($monthly as $MONTH_VAL){
                                        if($ShopsVAL->id==$MONTH_VAL->shop_id){
                                            $total++;
                                        }
                                      }
                                      
                                      echo $total;
                                  ?>
                              </span>
                          </div>
                      </li>
                      @endforeach
                      
                    </ul>
                </div>
            </div>
        </div>
      </div>
        
      </div>
  </section>
  {{-- Dashboard Ecommerce ends --}}
@endsection






@section('vendor-script')

<script>


    //laravel_vars 
    


    var chart_days = [];
    var line1 = [];
    var line2 = [];
    
    <?php
    $tmp_pre_val = 0;
    if(isset($combine_chart['pre'])){
          foreach($combine_chart['pre'] as $key => $preVAL){
              $tmp_pre_val += $preVAL;
              ?>
              chart_days.push(<?php echo $key;?>);
              line2.push(<?php echo $tmp_pre_val;?>);
              <?php
          }
     }
     
     $tmp_current_val = 0;
     
     if(isset($combine_chart['current'])){
          foreach($combine_chart['current'] as $key_cur => $currentVAL){ 
              if($key_cur<=date('d')){
              $tmp_current_val += $currentVAL;
              ?>
              line1.push(<?php echo $tmp_current_val;?>);
         <?php } }
      }
      ?>
      
      
      var system_colours = [];
      
      <?php
      
      if(isset($colour)){
          foreach($colour as $colourVAL){ 
              ?>
              system_colours.push('#<?php echo $colourVAL;?>');
         <?php } 
      }
      ?>
      
      
      var pie_chart_labels = [];
      var pie_chart_serize = [];
      
      <?php 
      
      foreach($Shop as $shop_key => $ShopsVAL){ 
          
      $total = 0;
          foreach($monthly as $MONTH_VAL){
            if($ShopsVAL->id==$MONTH_VAL->shop_id){
                $total++;
            }
          }
          ?>
          pie_chart_labels.push('<?php echo $ShopsVAL->short_name;?>');
          pie_chart_serize.push(parseFloat('<?php echo $total;?>'));
                                      
    <?php } ?>
    
    
    
    
    
    var line_chart_1_vals = [];
    var line_chart_1_cats = [];
    
    
    <?php if(isset($six_month_data)){ 
        foreach($six_month_data as $sixKEY => $six_month_dataVAL){ ?>
        
        line_chart_1_vals.push(parseFloat("<?php echo $six_month_dataVAL;?>"));
        line_chart_1_cats.push('<?php echo $sixKEY; ?>');
        
        <?php } }  ?>
        
        
        var hour_sales_val = [];
        var hour_sales_label = [];
        
        
        
    <?php if(isset($hour_data)){ 
        foreach($hour_data as $KeyHourSales => $VALHourSales){ ?>
        
        hour_sales_val.push(parseFloat("<?php echo $VALHourSales;?>"));
        hour_sales_label.push('<?php echo $KeyHourSales; ?>');
        
        <?php } }  ?>
        
        var hour_order_val = [];
        var hour_order_label = [];
        
        
        
        
        <?php if(isset($hour_orders)){ 
        foreach($hour_orders as $KeyHourORD => $VALHourORD){ ?>
        
        hour_order_val.push(parseFloat("<?php echo $VALHourORD;?>"));
        hour_order_label.push('<?php echo $KeyHourORD; ?>');
        
        <?php } }  ?>
        
        
        
        
        var shop_pie_data = [];
        
        <?php if(isset($Shop_pie)){ ?>
        
        var shop_pie_json = '<?php echo json_encode($Shop_pie);?>';
        
        shop_pie_data = JSON.parse(shop_pie_json);
        
        console.log(shop_pie_data);
        
        <?php } ?>
      
</script>


{{-- vednor files --}}
        <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
@endsection
@section('myscript')
        {{-- Page js files --}}
        <script src="{{ asset(mix('js/scripts/pages/dashboard-ecommerce.js')) }}"></script>
        <script>



    function refresh_pie(ele,type){
        
        if(ele==undefined){
            $('#dropdownItem3').html("This Month");
        }else{
           $('#dropdownItem3').html($(ele).html()); 
        }
        
        if(type==undefined){
            type = 'This_Month';
        }
        
        
        $('#customer-chart').html('');
        
        
        var li_string = '';
        
        var store_names = [];
        var store_values = [];
        
        $.each(shop_pie_data, function( index, valuePIE ) {
            
            var main_amount = 0;
            
            if(type=='Today'){
                main_amount = valuePIE.today;
            }
            if(type=='This_Month'){
                main_amount = valuePIE.monthly;
            }
            if(type=='Last_7_Days'){
                main_amount = valuePIE.last7;
            }
            if(type=='Last_30_Days'){
                main_amount = valuePIE.last30;
            }
            
            if(type=='Yesterday'){
                main_amount = valuePIE.yesterday;
            }
            
            store_names.push(valuePIE.name);
            store_values.push(parseFloat(main_amount));

          li_string += '<li class="list-group-item d-flex justify-content-between ">'+
                              '<div class="series-info">'+
                                  '<i class="fa fa-circle font-small-3 "></i>'+
                                  '<span class="text-bold-600">'+valuePIE.name +'</span>'+
                              '</div>'+
                              '<div class="product-result">'+
                                  '<span>'+main_amount+
                                  '</span>'+
                              '</div>'+
                          '</li>';
          
        });
        
         pie_chart_labels = store_names;
         pie_chart_serize = store_values;
        
        $('#shop_area').html(li_string);
        
                var customerChartoptions = {
        chart: {
          type: 'pie',
          height: 330,
          dropShadow: {
            enabled: false,
            blur: 5,
            left: 1,
            top: 1,
            opacity: 0.2
          },
          toolbar: {
            show: false
          }
        },
        labels: pie_chart_labels,
        series: pie_chart_serize,
        dataLabels: {
          enabled: false
        },
        legend: { show: false },
        stroke: {
          width: 5
        },
        fill: {
          type: 'gradient'
        }
      }
    
      var customerChart = new ApexCharts(
        document.querySelector("#customer-chart"),
        customerChartoptions
      );
    
      customerChart.render();
                
    
            }
    
            
         
        
        
        
        
        $(document).ready(function(){
          refresh_pie();
          notification();
        });
    
</script>
@endsection






