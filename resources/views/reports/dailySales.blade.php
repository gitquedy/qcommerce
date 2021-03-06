@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Daily Sales')

@section('vendor-style')
        {{-- vednor files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/extensions/dataTables.checkboxes.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
        <link rel="stylesheet" href="{{ asset('vendors/css/daterangepicker/daterangepicker.css') }}">
@endsection
@section('mystyle')
        {{-- Page css files --}}
        <link rel="stylesheet" href="{{ asset(mix('css/pages/data-list-view.css')) }}">
@endsection

@section('content')
{{-- Data list view starts --}}
<section>
  <div class="row">
    <div class="col-12">
      @include('reports.components.shopFilter')
      @include('reports.components.dateFilter')
      <div class="btn-group" id="chip_area_shop"></div>
      <div class="btn-group" id="chip_area_timings"></div>
    </div>
  </div>
</section>

<section id="apexchart">
  <!-- Mixed Chart -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Daily Sales</h4>
        </div>
        <div class="card-content">
          <div class="card-body">
            <h4><span id="total_sales">0</span> / <span id="total_units">0</span></h4>
            <div id="mixed-chart"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

<div class="row" id="table-hover-row">
  <div class="col-12">
      <div class="card">
          <div class="card-content">
              <div class="table-responsive">
                  <table class="table table-hover mb-0" id="report-table">
                      <thead>
                          <tr>
                              <th>Date</th>
                              <th>No. of Order</th>
                              <th>No. of Units</th>
                              <th>Sales</th>
                          </tr>
                      </thead>
                      <tbody>
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
  </div>
</div>
</section>

<!-- <section id="data-list-view" class="data-list-view-header">
    {{-- DataTable starts --}}
    <div class="table-responsive">
      <table class="table data-list-view">
        <thead>
          <tr>
            <th>Date</th>
            <th>No. of Order</th>
            <th>No. of Units</th>
            <th>Sale</th>
          </tr>
        </thead>
      </table>
    </div>
    {{-- DataTable ends --}}
  </section> -->
  {{-- Data list view end --}}  
@endsection
@section('vendor-script')
{{-- vednor js files --}}
  <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>-
  <!-- <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script> -->
  <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
  <script src="{{ asset('vendors/js/moment/moment.min.js') }}"></script>
  <script src="{{ asset('vendors/js/daterangepicker/daterangepicker.min.js') }}"></script>
  <script src="{{ asset('js/scripts/reports/daterange.js') }}"></script>
@endsection
@section('myscript')
  {{-- Page js files --}}
  <script src="{{ asset('js/scripts/reports/colors.js') }}"></script>
  <script type="text/javascript">
    function getParams(){
      var $params = '?shop=' + $("#shop").val() + '&daterange=' + $("#daterange").val();
      return $params;
    }
    var url = "{{ action('ReportsController@dailySales')  }}";
  </script>
  <script src="{{ asset('js/scripts/reports/dailySales.js') }}"></script>
  <script src="{{ asset('js/scripts/reports/filter.js') }}"></script>
  <script type="text/javascript"></script>
  <script type="text/javascript">
    $(document).ready(function(){
      getChart();
    });

    $(document).on('change', '.selectFilter', function() {
      getChart();
    });     
  </script>

  <!-- datatables -->
  <!-- <script type="text/javascript">
  var columnns = [
            { data: 'dateFormat', name: 'dateFormat', orderable: true},
            { data: 'total_orders', name: 'total_orders'},
            { data: 'total_quantity', name: 'total_quantity'},
            { data: 'total_sales', name: 'total_sales'},
        ];
  var table_route = {
          url: '{{ action("ReportsController@dailySales") }}',
          data: function (data) {
                data.shop = $("#shop").val();
                data.daterange = $("#daterange").val();
            }
        };
  var buttons = [
            ];
  var BInfo = true;
  var bFilter = true;
  function created_row_function(row, data, dataIndex){
  }
  var aLengthMenu = [[20, 50, 100, 500],[20, 50, 100, 500]];
  var pageLength = 20;
</script>
<script src="{{ asset(mix('js/scripts/ui/data-list-view.js')) }}"></script> -->
@endsection
