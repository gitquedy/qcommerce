@extends('layouts/contentLayoutMaster')

@section('title', 'Top Selling Products')

@section('content')
<div class="row">
  <div class="col-12">
      <p>You can easily create reuseable chart components. Read full documnetation <a href="https://www.chartjs.org/docs/latest/getting-started/" target="_blank">here</a></p>
  </div>
</div>
<!-- line chart section start -->
<section id="chartjs-charts">
  <!-- Line Chart -->
  <div class="row">
      <!-- Bar Chart -->
      <div class="col-md-12">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Top Selling Products</h4>
              </div>
              <div class="card-content">
                  <div class="card-body pl-0">
                      <div class="height-300">
                          <canvas id="bar-chart"></canvas>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <div class="row">
  <!-- Horizontal Chart -->
      <div class="col-md-6">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Horizontal Bar Chart</h4>
              </div>
              <div class="card-content">
                  <div class="card-body pl-0">
                      <div class="height-300">
                          <canvas id="horizontal-bar"></canvas>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <!-- Pie Chart -->
      <div class="col-md-6">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Pie Chart</h4>
              </div>
              <div class="card-content">
                  <div class="card-body pl-0">
                      <div class="height-300">
                          <canvas id="simple-pie-chart"></canvas>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <div class="row">
      <!-- Doughnut Chart -->
      <div class="col-md-6">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Doughnut Chart</h4>
              </div>
              <div class="card-content">
                  <div class="card-body">
                      <div class="height-300">
                          <canvas id="simple-doughnut-chart"></canvas>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <!-- Radar Chart -->
      <div class="col-md-6">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Radar Chart</h4>
              </div>
              <div class="card-content">
                  <div class="card-body">
                      <div class="height-300">
                          <canvas id="radar-chart"></canvas>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

   <!-- Polar & Radar Chart -->
  <div class="row">
      <!-- Polar Chart -->
      <div class="col-md-6">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Polar Chart</h4>
              </div>
              <div class="card-content">
                  <div class="card-body">
                      <div class="height-300">
                          <canvas id="polar-chart"></canvas>
                      </div>
                  </div>
              </div>
          </div>
      </div>


      <!-- Bubble Chart -->
      <div class="col-md-6">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Bubble Chart</h4>
              </div>
              <div class="card-content">
                  <div class="card-body">
                      <div class="height-300">
                          <canvas id="bubble-chart" width="300"></canvas>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <!-- Scatter logX Chart -->
  <div class="row">
      <div class="col-12">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Scatter Chart</h4>
              </div>
              <div class="card-content">
                  <div class="card-body">
                      <div class="height-300">
                          <canvas id="scatter-chart"></canvas>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</section>
<!-- // line chart section end -->
@endsection

@section('vendor-script')
        <!-- vednor files -->
        <script src="{{ asset(mix('vendors/js/charts/chart.min.js')) }}"></script>
@endsection
@section('myscript')
        <!-- Page js files -->
        <!-- <script src="{{ asset(mix('js/scripts/charts/chart-chartjs.js')) }}"></script> -->
        <script src="{{ asset('js/scripts/reports/colors.js') }}"></script>
        <script type="text/javascript">
          $(window).on("load", function () {
            var barChartctx = $("#bar-chart");

            // Chart Options
            var barchartOptions = {
              // Elements options apply to all of the options unless overridden in a dataset
              // In this case, we are setting the border of each bar to be 2px wide
              elements: {
                rectangle: {
                  borderWidth: 2,
                  borderSkipped: 'left'
                }
              },
              responsive: true,
              maintainAspectRatio: false,
              responsiveAnimationDuration: 500,
              legend: { display: false },
              scales: {
                xAxes: [{
                  position: 'left',
                  display: true,
                  gridLines: {
                    color: grid_line_color,
                  },
                  scaleLabel: {
                    display: true,
                    labelString: 'SKU'
                  },
                  ticks: {
                    stepSize: 1000
                  },
                }],
                yAxes: [{
                stacked: true,
                id: 'sales',
                type: 'linear',
                position: 'left',
                scaleLabel: {
                    display: true,
                    labelString: 'Sales'
                  },
                ticks: {
                  stepSize: 10000,
                  min: 1000
                }
              }, {
                stacked: true,
                id: 'units',
                type: 'linear',
                position: 'right',
                scaleLabel: {
                    display: true,
                    labelString: 'No. of Units'
                  },
                ticks: {
                  stepSize: 10,
                  min: 10
                }
              }], 
              },
              title: {
                display: true,
                text: 'Sale / No. of Units'
              },
            };

            // Chart Data
            var barchartData = {
              labels: ['QD1001', 'QD1003', 'QD1002', 'QD1009', 'QD1006'],
              datasets: [{
                label: 'Sales',
                yAxisID: 'sales',
                data: [24000, 50000, 30000, 20000, 15000],
                backgroundColor: themeColors,
                borderColor: "transparent",
              }, {
                label: 'Units',
                yAxisID: 'units',
                data: [50, 40, 60, 80, 40],
                backgroundColor: themeColors,
                borderColor: "transparent",
                

              }],
              
            };
            var barChartconfig = {
              type: 'bar',
              // Chart Options
              options: barchartOptions,

              data: barchartData
            };

            // Create the chart
            var barChart = new Chart(barChartctx, barChartconfig);
          });
        </script>
@endsection
