// Mixed Chart
  function getChart(){
    var $labels = [], $units = [], $sales = [];
    $total_sales = 0;
    $total_units = 0;
    $.ajax({
    method: "GET",
    url: url + getParams(),
    success: function success(result) {
        $('#mixed-chart').html('');
        $('#report-table tbody').empty();
        if(result.count == 0){
          toastr.warning('No data available');
          $('#total_sales').html($total_sales);
          $('#total_units').html($total_units);
          return;
        }
        $.each(result.report, function (i, item) {
            $labels.push(item.seller_sku);
            $units.push(item.total_quantity);
            $sales.push(parseFloat(item.total_price));
            $total_sales += parseFloat(item.total_price);
            $total_units += parseFloat(item.total_quantity);

            $table_row = '<tr><th>' + item.seller_sku + '</th><td>' + item.product_name + '</td><td>' + parseFloat(item.total_quantity)
                          + '</td><td>' + parseFloat(item.total_price)  +'</td></tr>';
            $('#report-table tbody').append($table_row);             
        });
        $total_sales = Number(($total_sales).toFixed(1)).toLocaleString();
        $('#total_sales').html($total_sales);
        $('#total_units').html($total_units);
        var mixedChartOptions = {
            chart: {
              height: 350,
              type: 'line',
              stacked: false,
            },
            colors: themeColors,
            stroke: {
              width: [0, 2, 5],
              curve: 'smooth'
            },
            plotOptions: {
              bar: {
                columnWidth: '50%'
              }
            },
            series: [{
              name: 'No. of Units',
              type: 'column',
              data: $units
            }, {
              name: 'Sales',
              type: 'area',
              data: $sales
            }],
            fill: {
              opacity: [0.85, 0.25, 1],
              gradient: {
                inverseColors: false,
                shade: 'light',
                type: "vertical",
                opacityFrom: 0.85,
                opacityTo: 0.55,
                stops: [0, 100, 100, 100]
              }
            },
            labels: $labels,
            markers: {
              size: 0
            },
            legend: {
              offsetY: -10
            },
            xaxis: {
              type: 'category'
            },
            yaxis: [{
              min: 0,
              tickAmount: 5,
              title: {
                text: 'No. of Units'
              }
            },
              {
              opposite: true,
              title: {
                text: "Sales"
              }
            }
            ],
            tooltip: {
              shared: true,
              intersect: false,
              y: {
                formatter: function (y) {
                  if (typeof y !== "undefined") {
                    // return y.toFixed(0) + "";
                    return Number((y).toFixed(1)).toLocaleString();
                  }
                  return y;
                }
              }
            }
          }
        var mixedChart = new ApexCharts(
          document.querySelector("#mixed-chart"),
          mixedChartOptions
        );
          mixedChart.render();
      },
    });   
  }