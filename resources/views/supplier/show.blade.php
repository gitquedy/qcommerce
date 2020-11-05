@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'View Supplier/Biller')
@section('vendor-style')
        {{-- vednor files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/extensions/dataTables.checkboxes.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection
@section('mystyle')
<style>
  table.dataTable td.dataTables_empty {
      text-align: center;    
  }
</style>
<link rel="stylesheet" href="{{ asset(mix('css/pages/data-list-view.css')) }}">
@endsection
@section('content')
<section id="floating-label-layouts">
  <div class="row match-height">
      <div class="col-md-12 col-12">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Supplier/Biller Details</h4>
              </div>
              <hr>
              <div class="card-content">
                  <div class="card-body">
                      <h1>{{$supplier->company }}</h1>
                      <h4 class="text-default">{{$supplier->phone}}</h4>
                      <h4 class="text-primary">{{$supplier->email}}</h4>
                      <h4 class="text-secondary">{{$supplier->contact_person}}</h4>
                      <hr>
                      <div class="row text-center">
                        <div class="card col-md-4 btn-outline-warning mx-1 px-2 d-inline-block" style="max-width: 20rem;">
                            @php
                            $total_po = 0;
                            foreach ($supplier->purchases as $po) {
                                if (in_array($po->payment_status, ['pending', 'partial']) && $po->status == 'received') {
                                  $total_po += $po->grand_total;
                                }
                            }
                            @endphp
                            <p class="display-4" style="font-size: 2.5rem!important">{{number_format($total_po, 2)}}</p>
                            <p class="text-warning">Total Purchases</p>
                        </div>
                        <div class="card col-md-4 btn-outline-primary mx-1 px-2 d-inline-block" style="max-width: 20rem;">
                            @php
                            $total_expense = 0;
                            foreach ($supplier->expenses as $expense) {
                                if (in_array($expense->payment_status, ['pending', 'partial'])) {
                                  $total_expense += $expense->amount;
                                }
                            }
                            @endphp
                            <p class="display-4" style="font-size: 2.5rem!important">{{number_format($total_expense, 2)}}</p>
                            <p class="text-warning">Total Expenses</p>
                        </div>
                      </div>
                      <div class="row text-center">
                        <div class="card col-md-4 btn-outline-success mx-1 px-2 d-inline-block" style="max-width: 20rem;">
                            @php
                            $total_paid = 0;
                            foreach ($supplier->purchases as $po) {
                                if (in_array($po->payment_status, ['paid', 'partial']) && $po->status == 'received') {
                                  $total_paid += $po->paid;
                                }
                            }
                            foreach ($supplier->expenses as $expense) {
                                if (in_array($expense->payment_status, ['paid', 'partial'])) {
                                  $total_paid += $expense->paid;
                                }
                            }
                            @endphp
                            <p class="display-4" style="font-size: 2.5rem!important">{{number_format($total_paid, 2)}}</p>
                            <p class="text-success">Total Paid</p>
                        </div>
                        <div class="card col-md-4 btn-outline-danger mx-1 px-2 d-inline-block" style="max-width: 20rem;">
                            @php
                            $balance = 0;
                            foreach ($supplier->purchases as $po) {
                                if (in_array($po->payment_status, ['pending', 'partial']) && $po->status == 'received') {
                                  $balance += $po->grand_total - $po->paid;
                                }
                            }
                            foreach ($supplier->expenses as $expense) {
                                if (in_array($expense->payment_status, ['pending', 'partial'])) {
                                  $balance += $expense->amount - $expense->paid;
                                }
                            }
                            @endphp
                            <p class="display-4" style="font-size: 2.5rem!important">{{number_format($balance, 2)}}</p>
                            <p class="text-danger">Balance</p>
                        </div>
                      </div>
                      <br>
                      <ul class="nav nav-tabs" id="view_profile_tabs" role="tablist">
                        <li class="nav-item">
                          <a class="nav-link active" id="po-tab" data-toggle="tab" href="#tab_po" role="tab" aria-controls="Purchases" aria-selected="true">Purchases</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="payments-tab" data-toggle="tab" href="#tab_expenses" role="tab" aria-controls="tab_expenses" aria-selected="false">Expenses</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="payments-tab" data-toggle="tab" href="#tab_payments" role="tab" aria-controls="Payments" aria-selected="false">Payments</a>
                        </li>  
                      </ul>
                      <div class="tab-content" id="view_profile_tab_content">
                        <div class="tab-pane fade show active" id="tab_po" role="tabpanel" aria-labelledby="tab_po">
                          <div class="action-btns">
                            <div class="btn-dropdown mr-1 mb-1">
                              <div class="btn-group dropdown actions-dropodown">
                                <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light"
                                  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Actions
                                </button>
                                <div class="dropdown-menu">
                                  <a class="dropdown-item massAction" href="#" data-action_type="view_modal" data-action="{{ route('payment.addMultiPaymentModalPurchase', $supplier->id) }}"><i class="fa fa-dollar" aria-hidden="true"></i> Add MultiPayment</a>
                                </div>
                              </div>
                            </div>
                          </div>
                          <section id="data-list-view" class="data-list-view-header">
                            {{-- DataTable starts --}}
                            <div class="table-responsive">
                              <table class="table data-list-view">
                                <thead>
                                  <tr>
                                    <th></th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Reference No</th>
                                    <th class="text-center">Purchase Status</th>
                                    <th class="text-center">Grand Total</th>
                                    <th class="text-center">Paid</th>
                                    <th class="text-center">Balance</th>
                                    <th class="text-center">Payment Status</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach($supplier->purchases as $purchase)
                                    <tr data-id="{{$purchase->id}}">
                                      <td></td>
                                      <td class="text-center">{{$purchase->date}}</td>
                                      <td class="text-center"><a class="toggle_view_modal" href="" data-action="{{ action('SalesController@viewSalesModal', $purchase->id) }}">{{$purchase->reference_no}}</a></td>
                                      <td class="text-center">
                                          @php
                                          switch ($purchase->status) {
                                            case 'received':
                                                    echo '<span class="badge badge-pill badge-success">Received</span>';
                                                break;
                                            case 'ordered':
                                                    echo '<span class="badge badge-pill badge-warning">Ordered</span>';
                                                break;
                                            case 'pending':
                                                    echo '<span class="badge badge-pill badge-info">Pending</span>';
                                                break;
                                            
                                            default:
                                                    echo '<span class="badge badge-secondary">Unknown</span>';
                                                break;
                                          }
                                          @endphp
                                      </td>
                                      <td class="text-right">{{number_format($purchase->grand_total, 2)}}</td>
                                      <td class="text-right">{{number_format($purchase->paid, 2)}}</td>
                                      <td class="text-right">{{number_format($purchase->grand_total - $purchase->paid, 2)}}</td>
                                      <td class="text-center">
                                        @php
                                        switch ($purchase->payment_status) {
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

                        <div class="tab-pane fade show" id="tab_expenses" role="tabpanel" aria-labelledby="tab_expenses">
                          <div class="action-btns">
                            <div class="btn-dropdown mr-1 mb-1">
                              <div class="btn-group dropdown actions-dropodown">
                                <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light"
                                  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Actions
                                </button>
                                <div class="dropdown-menu">
                                  <a class="dropdown-item massAction" href="#" data-action_type="view_modal" data-action="{{ route('payment.addMultiPaymentModalExpense', $supplier->id) }}"><i class="fa fa-dollar" aria-hidden="true"></i> Add MultiPayment</a>
                                </div>
                              </div>
                            </div>
                          </div>
                          <section id="data-list-view" class="data-list-view-header">
                            {{-- DataTable starts --}}
                            <div class="table-responsive">
                              <table class="table data-list-view">
                                <thead>
                                  <tr>
                                    <th></th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Reference No</th>
                                    <th class="text-center">Grand Total</th>
                                    <th class="text-center">Paid</th>
                                    <th class="text-center">Balance</th>
                                    <th class="text-center">Payment Status</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach($supplier->expenses as $expense)
                                    <tr data-id="{{$expense->id}}">
                                      <td></td>
                                      <td class="text-center">{{$expense->date}}</td>
                                      <td class="text-center"><a class="toggle_view_modal" href="" data-action="{{ action('SalesController@viewSalesModal', $expense->id) }}">{{$expense->reference_no}}</a></td>
                                      <td class="text-right">{{number_format($expense->grand_total, 2)}}</td>
                                      <td class="text-right">{{number_format($expense->paid, 2)}}</td>
                                      <td class="text-right">{{number_format($expense->grand_total - $expense->paid, 2)}}</td>
                                      <td class="text-center">
                                        @php
                                        switch ($expense->payment_status) {
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
                          <div class="action-btns">
                            <div class="btn-dropdown mr-1 mb-1">
                              <div class="btn-group dropdown actions-dropodown">
                                <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light"
                                  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Actions
                                </button>
                                <div class="dropdown-menu">
                                  {{-- <a class="dropdown-item massAction" href="#" data-action=""> Add Payment</a> --}}
                                </div>
                              </div>
                            </div>
                          </div>
                          <section id="data-list-view" class="data-list-view-header">
                          {{-- DataTable starts --}}
                          <div class="table-responsive">
                            <table class="table datatables data-list-view">
                              <thead>
                                <tr>
                                  <th></th>
                                  <th class="text-center">Date</th>
                                  <th class="text-center">Reference No</th>
                                  <th class="text-center">Payment Type</th>
                                  <th class="text-center">Amount</th>
                                  <th class="text-center">Payment Status</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach($supplier->payments as $pay)
                                   <tr>
                                    <td></td>
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
    $('.data-list-view').DataTable({
      dom: '<"top"><"clear">rt<"bottom"<"actions">p>',
      order: [[ 1, "desc" ]],
      colimns: [{ data: 'id',
            name: 'id' ,
            "render": function (){
                    return '<input type="checkbox" class="dt-checkboxes">';
                },
                className:'dt-checkboxes-cell'
            }],
      responsive: !1,
        columnDefs: [{
            orderable: false,
            targets: 0,
            checkboxes: {
                selectRow: !0
            },
        }],
      select: {
          selector: "first-child",
          style: "multi",
      },
      initComplete: function(t, e) {
          $(".dt-buttons .btn").removeClass("btn-secondary");
      }
    });

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

    $(".dt-checkboxes").on("click", function () {
        if($(this).prop('checked')==true){
            $(this).closest("tr").addClass("selected");
        }else{
            $(this).closest("tr").removeClass("selected");
        }
    });

    $(".data-list-view, .data-thumb-view").on("click", "tbody td", function () {
      var dtCheckbox = $(this).parent("tr").find(".dt-checkboxes-cell .dt-checkboxes")
      $(this).closest("tr").toggleClass("selected");
      if($(this).closest("tr").hasClass("selected")==true){
          dtCheckbox.prop("checked",true);
      }else{
          dtCheckbox.prop("checked",false);
      }
    });

    $(".dt-checkboxes-select-all input").on("click", function () {
        if($(this).prop('checked')==true){
            $(".data-list-view").find("tbody tr").addClass("selected");
            $(".data-thumb-view").find("tbody tr").addClass("selected");
            $('.dt-checkboxes').prop('checked',true);
        }else{
            $(".data-list-view").find("tbody tr").removeClass("selected");
            $(".data-thumb-view").find("tbody tr").removeClass("selected");
            $('.dt-checkboxes').prop('checked',false);
        }
    });

    $(document).on('click', '.massAction', function(){
        var ids = [];
        var table_name = $(this).data('tablename');
        $('tr.selected').each(function(){
            ids.push($(this).data('id'));
        });
        if(ids.length > 0)
        {
          if($(this).data('action_type') == "view_modal") {
            $.ajax({
                url: $(this).data('action'),
                method: "POST",
                data: {ids:ids,table:table_name},
                success:function(result)
                {
                    $('.view_modal').html(result).modal();
                }
            });  
          }
          else {  
          }
        }
        else
        {
            alert("Please select atleast one checkbox");
        }
    });
  </script> 
<script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
@endsection

