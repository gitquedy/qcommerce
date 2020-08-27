@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'View Customer')
@section('mystyle')
<style>
  table.dataTable td.dataTables_empty {
      text-align: center;    
  }
</style>
@endsection
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
                            <p class="display-4">{{number_format($customer->available_deposit(), 2)}}</p>
                            <p class="text-primary">Deposit</p>
                        </div>
                      </div>
                      <br>
                      <ul class="nav nav-tabs" id="view_profile_tabs" role="tablist">
                        <li class="nav-item">
                          <a class="nav-link active" id="sales-tab" data-toggle="tab" href="#tab_sales" role="tab" aria-controls="Sales" aria-selected="true">Sales</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="payments-tab" data-toggle="tab" href="#tab_payments" role="tab" aria-controls="Payments" aria-selected="false">Payments</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="deposits-tab" data-toggle="tab" href="#tab_deposits" role="tab" aria-controls="Deposits" aria-selected="false">Deposits</a>
                        </li>
                      </ul>
                      <div class="tab-content" id="view_profile_tab_content">
                        <div class="tab-pane fade show active" id="tab_sales" role="tabpanel" aria-labelledby="tab_sales">
                          <section id="data-list-view" class="data-list-view-header">
                            {{-- DataTable starts --}}
                            <div class="table-responsive">
                              <table class="table datatables">
                                <thead>
                                  <tr>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Reference No</th>
                                    <th class="text-center">Sales Status</th>
                                    <th class="text-center">Grand Total</th>
                                    <th class="text-center">Paid</th>
                                    <th class="text-center">Balance</th>
                                    <th class="text-center">Payment Status</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach($customer->sales as $sale)
                                    <tr>
                                      <td class="text-center">{{$sale->date}}</td>
                                      <td class="text-center"><a class="toggle_view_modal" href="" data-action="{{ action('SalesController@viewSalesModal', $sale->id) }}">{{$sale->reference_no}}</a></td>
                                      <td class="text-center">
                                          @php
                                          switch ($sale->status) {
                                            case 'completed':
                                                    echo '<span class="badge badge-success">Complete</span>';
                                                break;
                                            case 'pending':
                                                    echo '<span class="badge badge-warning">Pending</span>';
                                                break;
                                            case 'canceled':
                                                    echo '<span class="badge badge-danger">Canceled</span>';
                                                break;
                                            
                                            default:
                                                    echo '<span class="badge badge-secondary">Unknown</span>';
                                                break;
                                          }
                                          @endphp
                                      </td>
                                      <td class="text-right">{{number_format($sale->grand_total, 2)}}</td>
                                      <td class="text-right">{{number_format($sale->paid, 2)}}</td>
                                      <td class="text-right">{{number_format($sale->grand_total - $sale->paid, 2)}}</td>
                                      <td class="text-center">
                                        @php
                                        switch ($sale->payment_status) {
                                            case 'paid':
                                                    echo '<span class="badge badge-pill badge-success">Paid</span>';
                                                break;
                                            case 'pending':
                                                    echo '<span class="badge badge-pill badge-warning">Pending</span>';
                                                break;
                                            case 'partial':
                                                    echo '<span class="badge badge-pill badge-info">Partial</span>';
                                                break;
                                            case 'due':
                                                    echo '<span class="badge badge-pill badge-danger">Due</span>';
                                                break;
                                            
                                            default:
                                                    echo '<span class="badge badge-pill badge-secondary">Unknown</span>';
                                                break;
                                        }
                                        @endphp
                                      </td>
                                    </tr>
                                  @endforeach
                                </tbody>
                              </table>
                            </div>
                            {{-- DataTable ends --}}
                          </section>
                        </div>
                        <div class="tab-pane fade" id="tab_payments" role="tabpanel" aria-labelledby="tab_payments">
                          <section id="data-list-view" class="data-list-view-header">
                          {{-- DataTable starts --}}
                          <div class="table-responsive">
                            <table class="table datatables">
                              <thead>
                                <tr>
                                  <th class="text-center">Date</th>
                                  <th class="text-center">Reference No</th>
                                  <th class="text-center">Payment Type</th>
                                  <th class="text-center">Amount</th>
                                  <th class="text-center">Payment Status</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach($customer->payments as $pay)
                                  <tr>
                                    <td class="text-center">{{$pay->date}}</td>
                                    <td class="text-center">{{$pay->reference_no}}</td>
                                    <td class="text-center">{{ucwords($pay->payment_type)}}</td>
                                    <td class="text-right">{{number_format($pay->amount,2)}}</td>
                                    <td class="text-center">@php
                                        switch ($pay->status) {
                                            case 'received':
                                                    echo '<span class="badge badge-pill badge-success">Received</span>';
                                                break;
                                            case 'pending':
                                                    echo '<span class="badge badge-pill badge-warning">Pending</span>';
                                                break;
                                            
                                            default:
                                                    echo '<span class="badge badge-pill badge-secondary">Unknown</span>';
                                                break;
                                        }
                                        @endphp</td>
                                  </tr>
                                @endforeach
                              </tbody>
                            </table>
                          </div>
                          {{-- DataTable ends --}}
                        </div>
                        <div class="tab-pane fade" id="tab_deposits" role="tabpanel" aria-labelledby="tab_deposits">
                          {{-- DataTable starts --}}
                          <div class="table-responsive">
                            <table class="table datatables">
                              <thead>
                                <tr>
                                  <th>Date</th>
                                  <th>Bank Reference No</th>
                                  <th>Amount</th>
                                  <th>Note</th>
                                  <th>Created By</th>
                                  <th>Updated By</th>
                                </tr>
                              </thead>
                              <tbody>
                                  @foreach($customer->deposits as $deposit)
                                    <tr>
                                      <td>{{$deposit->date}}</td>
                                      <td>{{$deposit->reference_no}}</td>
                                      <td>{{number_format($deposit->amount, 2)}}</td>
                                      <td>{{$deposit->note}}</td>
                                      <td>{{$deposit->created_by_name->formatName()}}</td>
                                      <td>{{($deposit->updated_by_name)?$deposit->updated_by_name->formatName():'--'}}</td>
                                    </tr>
                                  @endforeach
                              </tbody>
                            </table>
                          </div>
                          {{-- DataTable ends --}}
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
{{-- vednor js files --}}
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script>
  <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection
@section('myscript')
  {{-- Page js files --}}
  <script type="text/javascript">
    $('.select2').select2();
    $('.datatables').DataTable({
      dom: '<"top"><"clear">rt<"bottom"<"actions">p>',
      order: [[ 0, "desc" ]]
    });
    function created_row_function(row, data, dataIndex){

    }
    $(document).on('click', '.toggle_view_modal', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).data('action'),
            method: "POST",
            data: {},
            success:function(result)
            {
                $('.view_modal').html(result).modal();
            }
        });
    });
  </script> 
<script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
@endsection

