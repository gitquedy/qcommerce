<div class="modal-dialog modal-md" role="document">
    <form id="pay_invoice" action="{{ action('PayPalController@payment', $billing->plan_id) }}" method="POST" class="form" enctype='multipart/form-data'>
        @method('POST')
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title">Invoice #{{ $billing->invoice_no }}</h4>
                <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="billing_id" name="billing_id" value="{{$billing->id}}">
                <div class="d-flex justify-content-between">
                    <div class="">Invoice Date: {{isset($billing->payment_date)?Carbon\Carbon::parse($billing->payment_date)->subDays(1)->toFormattedDateString():'Month dd, yyyy'}}</div>
                    <div class="">Invoice Due Date: {{ isset($billing->payment_date)?Carbon\Carbon::parse($billing->payment_date)->toFormattedDateString():'Month dd, yyyy' }}</div>
                </div>
                <br>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$billing->plan->name}} Subscription Plan ({{ isset($billing->payment_date)?Carbon\Carbon::parse($billing->payment_date)->toFormattedDateString():'mm/dd/yy' }} - {{ isset($billing->next_payment_date)?Carbon\Carbon::parse($billing->next_payment_date)->subDays(1)->toFormattedDateString():'mm/dd/yy'}})</td>
                            <td>Php{{$billing->amount}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary no-print" href="{{ route('billing.viewProof', ['billing_id' => $billing->id]) }}" {{($billing->proof)?'':'hidden'}}> View Proof of Payment</a>
                <a class="btn btn-primary no-print" href="{{ route('billing.pay', ['billing_id' => $billing->id]) }}" {{($billing->proof)?'hidden':''}}><i class="fa fa-dollar"></i> Pay Through Bank</a>
                <button type="submit" class="btn btn-primary no-print btn_save" {{($billing->proof)?'hidden':''}}><i class="fa fa-dollar"></i> Pay With PayPal</button>
                <button type="button" class="btn btn-danger no-print" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </form>
</div>

<script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
<script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
<script type="text/javascript">
  $('.btn_save').click(function(){
   let timerInterval
    Swal.fire({
      title: 'Please Wait',
      html: 'while we process your paypal link',
      timer: 5000,
      timerProgressBar: true,
      showCancelButton: false,
      showConfirmButton: false,
      onClose: () => {
        clearInterval(timerInterval)
      }
    }).then((result) => {
      /* Read more about handling dismissals below */
      if (result.dismiss === Swal.DismissReason.timer) {
        console.log('I was closed by the timer')
      }
    })
  });
</script>