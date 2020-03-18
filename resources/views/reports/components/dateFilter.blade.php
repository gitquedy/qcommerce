<div class="btn-group mb-1">
  <input type="hidden" id="timings" name="timings" class="selectFilter" value="Last_30_days">
  <div class="dropdown">
    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
     <i class="fa fa-filter"></i> Date Filter
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenu1">
      {{-- <a class="dropdown-item filter_btn" href="#" data-target="timings" data-type="single" data-value="All">All</a> --}}
      <a class="dropdown-item filter_btn" href="#" data-target="timings" data-type="single" data-value="Today">Today</a>
      <a class="dropdown-item filter_btn" href="#" data-target="timings" data-type="single" data-value="Yesterday">Yesterday</a>
      <a class="dropdown-item filter_btn" href="#" data-target="timings" data-type="single" data-value="Last_7_days">Last 7 days</a>
      <a class="dropdown-item filter_btn" href="#" data-target="timings" data-type="single" data-value="Last_30_days">Last 30 days</a>
      <a class="dropdown-item filter_btn" href="#" data-target="timings" data-type="single" data-value="This_Month">This Month</a>
    </div>
  </div>
</div>