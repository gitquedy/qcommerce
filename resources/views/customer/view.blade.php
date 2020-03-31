@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'View Customer')

@section('content')
<section id="floating-label-layouts">
  <div class="row match-height">
      <div class="col-md-12 col-12">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Customer Details</h4>
              </div>
              <hr>
              <div class="card-content">
                  <div class="card-body">
                      <h1>{{$customer->formatName()}}</h1>
                      <h4 class="text-default">{{$customer->phone}}</h4>
                      <h4 class="text-primary">{{$customer->email}}</h4>
                      <h4 class="text-secondary">{{$customer->address}}</h4>
                      <hr>
                      <div class="row text-center">
                        <div class="card col-md-4 btn-outline-warning mx-1 px-2 d-inline-block" style="max-width: 20rem;">
                            @php
                            $total_sales = 0;
                            foreach ($customer->sales as $sale) {
                                if (in_array($sale->payment_status, ['pending', 'partial']) && $sale->status == 'completed') {
                                  $total_sales += $sale->grand_total;
                                }
                            }
                            @endphp
                            <p class="display-4">{{number_format($total_sales, 2)}}</p>
                            <p class="text-warning">Total Sales</p>
                        </div>
                        <div class="card col-md-4 btn-outline-success mx-1 px-2 d-inline-block" style="max-width: 20rem;">
                            @php
                            $total_paid = 0;
                            foreach ($customer->sales as $sale) {
                                if (in_array($sale->payment_status, ['pending', 'partial']) && $sale->status == 'completed') {
                                  $total_paid += $sale->paid;
                                }
                            }
                            @endphp
                            <p class="display-4">{{number_format($total_paid, 2)}}</p>
                            <p class="text-success">Total Paid</p>
                        </div>
                        <div class="card col-md-4 btn-outline-danger mx-1 px-2 d-inline-block" style="max-width: 20rem;">
                            @php
                            $balance = 0;
                            foreach ($customer->sales as $sale) {
                                if (in_array($sale->payment_status, ['pending', 'partial']) && $sale->status == 'completed') {
                                  $balance += $sale->grand_total - $sale->paid;
                                }
                            }
                            @endphp
                            <p class="display-4">{{number_format($balance, 2)}}</p>
                            <p class="text-danger">Balance</p>
                        </div>
                        <div class="card col-md-4 btn-outline-primary mx-1 px-2 d-inline-block" style="max-width: 20rem;">
                            @php
                            
                            @endphp
                            <p class="display-4">{{number_format(0, 2)}}</p>
                            <p class="text-primary">Deposit</p>
                        </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</section>
<!-- // Basic Floating Label Form section end -->
@endsection
@section('vendor-script')
<script>
  $('.select2').select2();
</script>
  <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
@endsection

