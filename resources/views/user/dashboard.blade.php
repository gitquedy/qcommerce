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
                    <h2 class="text-bold-700 mt-1 monthlySalesValue">₱ 0</h2>
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
                    <h2 class="text-bold-700 mt-1 todaySalesValue">₱ 0</h2>
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
                    <h2 class="text-bold-700 mt-1 todayOrderCount" >0</h2>
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
                    <h2 class="text-bold-700 mt-1 shippedCounter">0</h2>
                    <p class="mb-0">Pending Order</p>
                </div>
                <div class="card-content">
                    <div id="line-area-chart-4"></div>
                </div>
            </div>
        </div>
      </div>
      
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
                                      <span class="text-success currentMonthlySale">0</span>
                                  </h2>
                              </div>
                              <div class="mr-2">
                                  <p class="mb-50 text-bold-600">Last Month</p>
                                  <h2 class="text-bold-400">
                                      <sup class="font-medium-1">₱</sup>
                                      <span class="preMonthSale">0</span>
                                  </h2>
                              </div>
                              <div class="ml-auto">
                                  <p class="mb-50 text-bold-600"></p>
                                  <h2 class="text-bold-400">
                                      <sup class="font-medium-5 arrow"></sup>
                                      <span class="percentageIncrease">0%</span>
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
                              <table class="table shopInfoTable" id="shopInfoTable">
                                <thead>
                                  <tr>
                                    <th colspan="2">Shop Information</th>
                                    <th class="text-right">Today</th>
                                    <th class="text-right">Yesterday</th>
                                    <th class="text-right">This Week</th>
                                    <th class="text-right">This Month</th>
                                    <th class="text-right">Last Month</th>
                                  </tr>
                                </thead>
                                <tbody class="shopInfoTableTbody">
                                  @foreach($Shop as $s)
                                      <tr>
                                        <td class="shop_logo" ><img src="{{ asset('images/shop/icon/'.$s->site.'.png') }}" alt=""></td>
                                        <td><b>{{$s->name}}</b><br><small><span class="text-seccondary">{{$s->short_name}}</span> @if($s->active) <span class="text-success ml-1">Active</span> @else <span class="text-danger ml-1">Inactive</span> @endif</small></td>
                                        <td class="text-right pr-1 shop_info_data_today{{ $s->id }}">{{$s->shop_info_data_today}}</td>
                                        <td class="text-right pr-1 shop_info_data_yesterday{{ $s->id }}">{{$s->shop_info_data_yesterday}}</td>
                                        <td class="text-right pr-1 shop_info_data_week{{ $s->id }}">{{$s->shop_info_data_week}}</td>
                                        <td class="text-right pr-1 shop_info_data_month{{ $s->id }}">{{$s->shop_info_data_month}}</td>
                                        <td class="text-right pr-1 shop_info_data_last_month{{ $s->id }}">{{$s->shop_info_data_last_month}}</td>
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

                      <li class="list-group-item d-flex justify-content-between">
                          <div class="series-info">
                              <i class="fa fa-circle font-small-3 "></i>
                              <span class="text-bold-600"></span>
                          </div>
                          <div class="product-result">
                              <span>
                              </span>
                          </div>
                      </li>
                      
                    </ul>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex justify-content-between pb-0">
                    <h4 class="card-title">Warehouse Stocks Value </h4>
                    <div class="dropdown chart-dropdown">
                        <button class="btn btn-sm border-0 dropdown-toggle px-0" type="button" id="dropdownItem4"
                          data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          All
                        </button>
                        <div class="dropdown-menu dropdown-menu-right warehouseStocksDropDown" aria-labelledby="dropdownItem4">
                          @foreach ($Warehouses as $w) 
                            <a class="dropdown-item"  onclick="refresh_stock_pie(this,{{$w->id}})" >{{$w->name}}</a>
                          @endforeach
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body py-0">
                        <div id="warhouse_stocks_chart"></div>
                    </div>
                    <ul class="list-group list-group-flush stock-info"  id="warehouses_area">
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
function number_format (number, decimals, dec_point, thousands_sep) {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
</script>


{{-- vednor files --}}
        <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
@endsection
@section('myscript')
        {{-- Page js files --}}
        <script type="text/javascript">
          var $primary = '#7367F0';
          var $success = '#28C76F';
          var $danger = '#EA5455';
          var $warning = '#FF9F43';
          var $info = '#00cfe8';
          var $primary_light = '#A9A2F6';
          var $danger_light = '#f29292';
          var $success_light = '#55DD92';
          var $warning_light = '#ffc085';
          var $info_light = '#1fcadb';
          var $strok_color = '#b9c3cd';
          var $label_color = '#e7e7e7';
          var $white = '#fff';
          var shop_pie_data = [];
          var warehouse_pie_data = [];
          function refresh_pie(ele,type){
            if(ele==undefined){
                $('#dropdownItem3').html("Today");
            }else{
               $('#dropdownItem3').html($(ele).html()); 
            }
            
            if(type==undefined){
                type = 'Today';
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


        function refresh_stock_pie(ele,type){
        
        if(ele==undefined){
            $('#dropdownItem4').html("All");
        }else{
           $('#dropdownItem4').html($(ele).html()); 
        }
        
        if(type==undefined){
            type = 'all';
        }
        
        $('#warhouse_stocks_chart').html('');
        
        
        var li_string = '';
        
        var warehouse_names = [];
        var warehouse_values = [];
        $.each(warehouse_pie_data, function( index, valuePIE ) {
            if(type == index || type == "all") {
                warehouse_names.push(valuePIE.name);
                warehouse_values.push(parseFloat(valuePIE.total));

                li_string += '<li class="list-group-item d-flex justify-content-between ">'+
                                  '<div class="series-info">'+
                                      '<i class="fa fa-circle font-small-3 "></i>'+
                                      '<span class="text-bold-600"> '+valuePIE.name +'</span>'+
                                  '</div>'+
                                  '<div class="product-result">'+
                                      '<span>'+valuePIE.total_formatted+
                                      '</span>'+
                                  '</div>'+
                              '</li>';  
            }
            else {
            }
        });
        warehouse_pie_chart_labels = warehouse_names;
        warehouse_pie_chart_serize = warehouse_values;
        $('#warehouses_area').html(li_string);
        
        var StocksChartoptions = {
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
          labels: warehouse_pie_chart_labels,
          series: warehouse_pie_chart_serize,
          dataLabels: {
            enabled: false,
          },
          legend: { show: false },
          stroke: {
            width: 5
          },
          fill: {
            type: 'gradient'
          },
          tooltip: {
            enabled: true,
            y: {
              formatter: function(val) {
                 val = Number(val).toFixed(2);
                 val += '';
                 var x = val.split('.');
                 var x1 = x[0];
                 var x2 = x.length > 1 ? '.' + x[1] : '';
                 var rgx = /(\d+)(\d{3})/;
                 while (rgx.test(x1)) {
                  x1 = x1.replace(rgx, '$1' + ',' + '$2');
                 }
                 return x1 + x2;
              }
            }
          }
        }
    
      var warehouseStocksChart = new ApexCharts(
        document.querySelector("#warhouse_stocks_chart"),
        StocksChartoptions
      );
      warehouseStocksChart.render();
    }

    function refreshRevenueCharts(chart_days, line1, line2){
      if(chart_days.length==0){
         chart_days = ['01', '05', '09', '13', '17', '21', '26', '31'];  
      }
      if(line1.length==0){
         line1 = [80, 47000, 44800, 47500, 45500, 48000, 46500, 48600];  
      }
      if(line2.length==0){
         line2 = [70, 48000, 45500, 46600, 44500, 46500, 45000, 47000]; 
      }

      $('#revenue-chart').html('');

      var revenueChartoptions = {
      chart: {
        height: 270,
        toolbar: { show: false },
        type: 'line',
      },
      stroke: {
        curve: 'smooth',
        dashArray: [0, 8],
        width: [4, 2],
      },
      grid: {
        borderColor: $label_color,
      },
      legend: {
        show: false,
      },
      colors: [$danger_light, $strok_color],

      fill: {
        type: 'gradient',
        gradient: {
          shade: 'dark',
          inverseColors: false,
          gradientToColors: [$primary, $strok_color],
          shadeIntensity: 1,
          type: 'horizontal',
          opacityFrom: 1,
          opacityTo: 1,
          stops: [0, 100, 100, 100]
        },
      },
      markers: {
        size: 0,
        hover: {
          size: 5
        }
      },
      xaxis: {
        labels: {
          style: {
            colors: $strok_color,
          }
        },
        axisTicks: {
          show: false,
        },
        categories: chart_days,
        axisBorder: {
          show: false,
        },
        tickPlacement: 'on',
      },
      yaxis: {
        tickAmount: 5,
        decimalsInFloat: false,
        labels: {
          style: {
            color: $strok_color,
          },
          formatter: function (val) {
              if(val!=undefined){
              return (val).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
              }
            
          }
        }
      },
      tooltip: {
        x: { show: false }
      },
      series: [{
        name: "This Month",
        data: line1
      },
      {
        name: "Last Month",
        data: line2
      }
      ],

    }

    var revenueChart = new ApexCharts(
      document.querySelector("#revenue-chart"),
      revenueChartoptions
    );

    revenueChart.render();
    }

    // here

    function refreshGainedLineChart(line_chart_1_vals, line_chart_1_cats ){
      $('#line-area-chart-1').html('');
      var gainedlineChartoptions = {
      chart: {
        height: 100,
        type: 'area',
        toolbar: {
          show: false,
        },
        sparkline: {
          enabled: true
        },
        grid: {
          show: false,
          padding: {
            left: 0,
            right: 0
          }
        },
      },
      colors: [$primary],
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'smooth',
        width: 2.5
      },
      fill: {
        type: 'gradient',
        gradient: {
          shadeIntensity: 0.9,
          opacityFrom: 0.7,
          opacityTo: 0.5,
          stops: [0, 80, 100]
        }
      },
      series: [{
        name: 'Monthly Sales',
        data: line_chart_1_vals
      }],

      xaxis: {
        categories: line_chart_1_cats
      },
      yaxis: [{
        y: 0,
        offsetX: 0,
        offsetY: 0,
        padding: { left: 0, right: 0 },
        
        
      }],
      tooltip: {
        x: { show: true },
        y: {formatter: function (val) {
              return (val).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            
          }}
      }
    }

    var gainedlineChart = new ApexCharts(
      document.querySelector("#line-area-chart-1"),
      gainedlineChartoptions
    );

    gainedlineChart.render();
    }



  // Line Area Chart - 2
  // ----------------------------------

  function refreshRevenueChart(hour_sales_val, hour_sales_label){
    $('#line-area-chart-2').html('');
    var revenuelineChartoptions = {
    chart: {
      height: 100,
      type: 'area',
      toolbar: {
        show: false,
      },
      sparkline: {
        enabled: true
      },
      grid: {
        show: false,
        padding: {
          left: 0,
          right: 0
        }
      },
    },
    colors: [$success],
    dataLabels: {
      enabled: false
    },
    stroke: {
      curve: 'smooth',
      width: 2.5
    },
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 0.9,
        opacityFrom: 0.7,
        opacityTo: 0.5,
        stops: [0, 80, 100]
      }
    },
    series: [{
      name: 'Hourly sales',
      data: hour_sales_val
    }],

     xaxis: {
            categories: hour_sales_label,
            },
    yaxis: [{
      y: 0,
      offsetX: 0,
      offsetY: 0,
      padding: { left: 0, right: 0 },
    }],
    tooltip: {
      x: { show: true },
      y: {formatter: function (val) {
            return (val).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
          
        }}
    },
  }

  var revenuelineChart = new ApexCharts(
    document.querySelector("#line-area-chart-2"),
    revenuelineChartoptions
  );

  revenuelineChart.render();
  }
  
  


  // Line Area Chart - 3
  // ----------------------------------

  function refreshSalesChart(hour_order_val, hour_order_label){
    $('#line-area-chart-3').html('');
    var saleslineChartoptions = {
    chart: {
      height: 100,
      type: 'area',
      toolbar: {
        show: false,
      },
      sparkline: {
        enabled: true
      },
      grid: {
        show: false,
        padding: {
          left: 0,
          right: 0
        }
      },
    },
    colors: [$danger],
    dataLabels: {
      enabled: false
    },
    stroke: {
      curve: 'smooth',
      width: 2.5
    },
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 0.9,
        opacityFrom: 0.7,
        opacityTo: 0.5,
        stops: [0, 80, 100]
      }
    },
    series: [{
      name: 'Hourly Orders',
      data: hour_order_val
    }],

    xaxis: {
            categories: hour_order_label,
            },
    yaxis: [{
      y: 0,
      offsetX: 0,
      offsetY: 0,
      padding: { left: 0, right: 0 },
    }],
    tooltip: {
      x: { show: true }
    },
  }

  var saleslineChart = new ApexCharts(
    document.querySelector("#line-area-chart-3"),
    saleslineChartoptions
  );

  saleslineChart.render();
  }

  // Line Area Chart - 4
  // ----------------------------------

  function refreshOrderLineChart(hour_order_val, hour_order_label){
    $('#line-area-chart-4').html('');
      var orderlineChartoptions = {
      chart: {
        height: 100,
        type: 'area',
        toolbar: {
          show: false,
        },
        sparkline: {
          enabled: true
        },
        grid: {
          show: false,
          padding: {
            left: 0,
            right: 0
          }
        },
      },
      colors: [$warning],
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'smooth',
        width: 2.5
      },
      fill: {
        type: 'gradient',
        gradient: {
          shadeIntensity: 0.9,
          opacityFrom: 0.7,
          opacityTo: 0.5,
          stops: [0, 80, 100]
        }
      },
      series: [{
        name: 'Hourly Orders',
        data: hour_order_val
      }],

      xaxis: {
              categories: hour_order_label,
              },
      yaxis: [{
        y: 0,
        offsetX: 0,
        offsetY: 0,
        padding: { left: 0, right: 0 },
      }],
      tooltip: {
        x: { show: false }
      },
    }

    var orderlineChart = new ApexCharts(
      document.querySelector("#line-area-chart-4"),
      orderlineChartoptions
    );

    orderlineChart.render();
  }



    // to here
</script>
        <script>

      function refreshAll(){
          $.ajax({
          url : "{{ action('DashboardController@index') }}",
          type : 'GET',
          success: function(result){  
            console.log(result);
            $('.monthlySalesValue').html(result.monthly_sales);
            $('.todaySalesValue').html(result.today_sales);
            $('.todayOrderCount').html(result.today_order_count);
            $('.shippedCounter').html(result.shipped_counter);

            var pie_chart_labels = [];
            var pie_chart_serize = [];
            $.each(result.Shop, function( index, value ) {
              var total = 0;
              $('.shop_info_data_today'+ value.id).html(value.shop_info_data_today);
              $('.shop_info_data_yesterday'+ value.id).html(value.shop_info_data_yesterday);
              $('.shop_info_data_week'+ value.id).html(value.shop_info_data_week);
              $('.shop_info_data_month'+ value.id).html(value.shop_info_data_month);
              $('.shop_info_data_last_month'+ value.id).html(value.shop_info_data_last_month);

              var today = parseFloat(value.shop_info_data_today);
              var yesterday = parseFloat(value.shop_info_data_yesterday);
              if (yesterday == 0) {
                yesterday++;
                today++;
              }
              var percentage_increase_today = ((today - yesterday) / yesterday) * 100;
              if (percentage_increase_today > 0) {
                $('.shop_info_data_today'+ value.id).append('<span style="color:green"> &#8593;'+number_format(percentage_increase_today, 0)+'%</span>');
              }
              else if (percentage_increase_today < 0) {
                percentage_increase_today = percentage_increase_today * -1;
                $('.shop_info_data_today'+ value.id).append('<span style="color:red"> &#8595;'+number_format(percentage_increase_today, 0)+'%</span>');
              }
              
              var month = parseFloat(value.shop_info_data_month);
              var last_month = parseFloat(value.shop_info_data_last_month);
              if (last_month == 0) {
                last_month++;
                month++;
              }
              var percentage_increase_month = ((month - last_month) / last_month) * 100;
              if (percentage_increase_month > 0) {
                $('.shop_info_data_month'+ value.id).append('<span style="color:green"> &#8593;'+number_format(percentage_increase_month, 0)+'%</span>');
              }
              else if (percentage_increase_month < 0) {
                percentage_increase_month = percentage_increase_month * -1;
                $('.shop_info_data_month'+ value.id).append('<span style="color:red"> &#8595;'+number_format(percentage_increase_month, 0)+'%</span>');
              }
              $.each(result.monthly, function( i, monthly ) {
                if(value.id==monthly.shop_id){
                  pie_chart_labels.push(value.short_name);
                  pie_chart_serize.push(parseFloat(total));
                }
              });
            });

            var currentMonthlySale = 0;
            var pre_month_sale = 0; 
            var chart_days = [];
            var line1 = [];
            var line2 = [];
            var current_sale = 0;

            if (typeof result.combine_chart.current !== 'undefined') {
                $.each(result.combine_chart.current, function( index, value ) {
                  currentMonthlySale += value;
                  if(parseInt(index) <= "{{ date('d') }}"){
                      current_sale += value;
                      line1.push(current_sale);
                  }
                });
            }

            if (typeof result.combine_chart.pre !== 'undefined') {
                $.each(result.combine_chart.pre, function( index, value ) {
                  pre_month_sale += value;
                  chart_days.push(index);
                  line2.push(pre_month_sale);
                });
            }
            $('.currentMonthlySale').html(number_format(currentMonthlySale, 2));
            $('.preMonthSale').html(number_format(pre_month_sale, 2));
            if (pre_month_sale == 0) {
              pre_month_sale++;
              currentMonthlySale++;
            }
            var percentage_increase = ((currentMonthlySale - pre_month_sale) / pre_month_sale) * 100;
            if (percentage_increase > 0) {
              $('.arrow').css('color', 'green');
              $('.arrow').html('&#8593;');
            }
            else if (percentage_increase < 0) {
              $('.arrow').css('color', 'red');
              $('.arrow').html('&#8595;');
              percentage_increase = percentage_increase * -1;
            }
            $('.percentageIncrease').html(number_format(percentage_increase, 2) + '%');
      
              
              var system_colours = [];
         

              if (typeof result.colour !== 'undefined') {
                $.each(result.colour, function( index, value ) {
                  system_colours.push(value);
                });
              }
              
              var warehouse_pie_chart_labels = [];
              var warehouse_pie_chart_serize = [];
              $.each(result.warehouses, function( index, value ) {
                var total = 0;
                $.each(result.items, function( i_index, i_value ) {
                  total += i_value.sku.price * i_value.quantity;
                });
                 warehouse_pie_chart_labels.push(value.name);
                 warehouse_pie_chart_serize.push(parseFloat(total));
              });
              
            var line_chart_1_vals = [];
            var line_chart_1_cats = [];
            
            if (typeof result.six_month_data !== 'undefined') {
              $.each(result.six_month_data, function( index, value ) {
                line_chart_1_vals.push(parseFloat(value));
                line_chart_1_cats.push(index);
              });
            }

            var hour_sales_val = [];
            var hour_sales_label = [];

            if (typeof result.hour_data !== 'undefined') {
              $.each(result.hour_data, function( index, value ) {
                hour_sales_val.push(parseFloat(value));
                hour_sales_label.push(index);
              });
            }

            var hour_order_val = [];
            var hour_order_label = [];
                
                
                
            if (typeof result.hour_orders !== 'undefined') {
              $.each(result.hour_orders, function( index, value ) {
                hour_order_val.push(parseFloat(value));
                hour_order_label.push(index);

              });
            }

            

            if (typeof result.Shop_pie !== 'undefined') {
              var shop_pie_json = JSON.stringify(result.Shop_pie);;
              shop_pie_data = JSON.parse(shop_pie_json);
            }


            

            if (typeof result.Warehouse_stocks_pie !== 'undefined') {
              var warehouse_pie_json = JSON.stringify(result.Warehouse_stocks_pie);;
              warehouse_pie_data = JSON.parse(warehouse_pie_json);
            }

            refresh_pie();
            refresh_stock_pie();
            refreshRevenueCharts(chart_days, line1, line2);

            refreshGainedLineChart(line_chart_1_vals, line_chart_1_cats );
            refreshRevenueChart(hour_sales_val, hour_sales_label);
            refreshSalesChart(hour_order_val, hour_order_label);
            refreshOrderLineChart(hour_order_val, hour_order_label);
          },
            error: function(jqXhr, json, errorThrown){
              console.log(jqXhr);
              console.log(json);
              console.log(errorThrown);
          }
        });
      }
        
        $(document).ready(function(){
          refreshAll();
          window.setInterval(refreshAll, 100000);
        });
</script>
<!-- <script src="{{ asset(mix('js/scripts/pages/dashboard-ecommerce.js')) }}"></script> -->
@endsection






