<div class="modal-dialog modal-md" role="document">
    <form id="pay_invoice" method="POST" class="form" enctype='multipart/form-data'>
        @method('POST')
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title">Invoice #{{ $billing->invoice_no }}</h4>
                <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="billing_id" name="billing_id" value="{{$billing->id}}">
                <!-- <div class="text-bold-600 font-medium-2">Plan Name: {{ $billing->plan->name }}</div>
                <div class="text-bold-600 font-medium-2">Amount: {{ $billing->amount }}</div>
                <div class="text-bold-600 font-medium-2">Date Coverage: {{ $billing->payment_date }} - {{ $billing->next_payment_date}}</div> -->
                <div class="d-flex justify-content-between">
                    <div class="">Invoice Date: Month dd, yyyy</div>
                    <div class="">Invoice Due Date: Month dd, yyyy</div>
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
                            <td>{{$billing->plan->name}} Subscription Plan ({{ $billing->payment_date }}mm/dd/yy - mm/dd/yy{{ $billing->next_payment_date}})</td>
                            <td>Php{{$billing->amount}}</td>
                        </tr>
                    </tbody>
                </table>
                
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary no-print btn_save"><i class="fa fa-dollar"></i> Pay Now</button>
                <button type="button" class="btn btn-danger no-print" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </form>
</div>