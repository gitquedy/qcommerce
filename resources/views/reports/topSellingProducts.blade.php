@extends('layouts/contentLayoutMaster')

@section('title', 'Top Selling Products')

@section('vendor-style')
        <!-- vednor css files -->
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
        {{-- <link rel="stylesheet" href="{{ asset('vendors/css/daterangepicker/daterangepicker.css') }}"> --}}
@endsection
@section('content')
<div class="row">
  <div class="col-12">
    @include('reports.components.shopFilter')
    @include('reports.components.dateFilter')
    
    <input type="hidden" id="no_of_products" name="no_of_products" class="selectFilter">
    <div class="btn-group mb-1 hidden">
    <div class="dropdown">
      <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
       <i class="fa fa-dropbox"></i> No. of Products
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenu1">
        {{-- <a class="dropdown-item filter_btn" href="#" data-target="no_of_products" data-type="single" data-value="10">10</a> --}}
        <a class="dropdown-item filter_btn" href="#" data-target="no_of_products" data-type="single" data-value="20">20</a>
        <a class="dropdown-item filter_btn" href="#" data-target="no_of_products" data-type="single" data-value="30">30</a>
      </div>
    </div>
  </div>
    <div class="btn-group" id="chip_area_shop"></div>
    <div class="btn-group" id="chip_area_timings"></div>
    <div class="btn-group" id="chip_area_no_of_products"></div>
  </div>
</div>
<!-- apex charts section start -->
<section id="apexchart">
  <!-- Mixed Chart -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Top Selling Products</h4>
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
                              <th>SKU</th>
                              <th>Name</th>
                              <th>No. of Units</th>
                              <th>Sale</th>
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
<!-- // Apex charts section end -->
@endsection

@section('vendor-script')
        <!-- vednor files -->
        <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
        {{-- <script src="{{ asset('vendors/js/moment/moment.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('vendors/js/daterangepicker/daterangepicker.min.js') }}"></script> --}}
        <script src="{{ asset('js/scripts/reports/daterange.js') }}"></script>

@endsection
@section('myscript')

        <!-- Page js files -->
         <script src="{{ asset('js/scripts/reports/colors.js') }}"></script>

         
        <script type="text/javascript">
          function getParams(){
            var $params = '?shop=' + $("#shop").val() + '&daterange=' + $("#daterange").val() + "&no_of_products=" + $("#no_of_products").val();
            return $params;
          }
          var url = "{{ action('ReportsController@topSellingProducts')  }}";
        </script>
        <script src="{{ asset('js/scripts/reports/topSellingProduct.js') }}"></script>
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
@endsection
