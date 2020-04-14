
<div class="modal-dialog modal-lg" role="document">
	<form action="{{ route('payment.addPaymentAjax') }}" id="add_payment_ajax" method="POST" class="form" enctype='multipart/form-data'>
  @method('POST')
	@csrf
  <style>
    .form-control[readonly] {
         background-color: transparent;
      }
  </style>
  <div class="modal-content">
  	<div class="modal-header">
		<h4 class="modal-title" id="modal-title">Add Payment for {{$sales->reference_no}}</h4>
    <input type="hidden" name="sales_id" value="{{$sales->id}}">
    <input type="hidden" name="customer_id" value="{{$sales->customer_id}}">
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="modal-body">
      <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Date</label>
                <div class="position-relative has-icon-left">
                  <input type="text" class="form-control datepicker update_input" name="date" value="{{date('m/d/Y')}}" readonly>
                  <div class="form-control-position"> 
                    <i class="feather icon-calendar"></i>
                  </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Referencce No.</label>
                <div class="position-relative has-icon-left">
                  <input type="text" class="form-control update_input" name="reference_no" placeholder="Reference No.">
                  <div class="form-control-position"> 
                    <i class="feather icon-hash"></i>
                  </div>
                </div>
            </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="card border-light">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <label>Amount</label>
                  <div class="position-relative has-icon-left">
                    @php
                    $balance = $sales->grand_total - $sales->paid;
                    @endphp
                    <input type="number" name="amount" class="form-control" max="{{$balance}}">
                    <div class="form-control-position"> 
                      <i class="feather icon-dollar-sign"></i>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <label>Type</label>
                  <div class="position-relative has-icon-left">
                    <select name="payment_type" id="payment_type" class="form-control select2">
                      <option value="cash">Cash</option>
                      <option value="gift_certificate">Gift Card</option>
                      <option value="credit_card">Credit Card</option>
                      <option value="cheque">Cheque</option>
                      <option value="deposit">Deposit</option>
                      <option value="other">Other</option>
                    </select>
                    <div class="form-control-position"> 
                      <i class="feather icon-credit-card"></i>
                    </div>
                  </div>
                </div>
              </div>
              <div class="credit_card payment_type_ext" style="display: none;">
                <hr>
                <div class="row">
                  <div class="col-md-6">
                    <label for="cc_no">Credit Card No.</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" name="cc_no" class="form-control input-number" placeholder="Credit Card No.">
                      <div class="form-control-position"> 
                        <i class="feather icon-credit-card"></i>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label for="cc_no">Holder Name</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" name="cc_holder" class="form-control" placeholder="Holder Name">
                      <div class="form-control-position"> 
                        <i class="feather icon-user"></i>
                      </div>
                    </div>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-6">
                    <label for="cc_type">Card Type</label>
                    <div class="position-relative has-icon-left">
                      <select name="cc_type" class="form-control select2">
                        <option value="visa">Visa</option>
                        <option value="mastercard">Master Card</option>
                        <option value="amex">Amex</option>
                        <option value="discover">Discover</option>
                      </select>
                      <div class="form-control-position"> 
                        <i class="feather icon-credit-card"></i>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <label for="cc_no">Month</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" name="cc_month" class="form-control input-number" placeholder="Month">
                      <div class="form-control-position"> 
                        <i class="feather icon-credit-card"></i>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <label for="cc_no">Year</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" name="cc_year" class="form-control input-number" placeholder="Year">
                      <div class="form-control-position"> 
                        <i class="feather icon-credit-card"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="cheque payment_type_ext" style="display: none;">
                <hr>
                <div class="row">
                  <div class="col-md-6">
                    <label for="cheque_no">Cheque No.</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" name="cheque_no" class="form-control input-number" placeholder="Cheque No.">
                      <div class="form-control-position"> 
                        <i class="feather icon-credit-card"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="gift_certificate payment_type_ext" style="display: none;">
                <hr>
                <div class="row">
                  <div class="col-md-6">
                    <label for="gift_card_no">Gift Card No.</label>
                    <div class="position-relative has-icon-left">
                      <input type="text" name="gift_card_no" class="form-control input-number" placeholder="Gift Card No.">
                      <div class="form-control-position"> 
                        <i class="feather icon-gift"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <label for="note">Note</label>
          <textarea name="note" id="" cols="20" rows="5" class="form-control" placeholder="Note"></textarea>
        </div>
      </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary no-print"> Submit </button>
      </form>
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>
<script src="{{ asset('js/scripts/forms-validation/form-modal.js') }}"></script>
<script>
   $(document).ready(function(){
        $(".select2").select2({
          dropdownAutoWidth: true,
          width: '100%'
        });

        $('.datepicker').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true,
          minYear: 1901,
          maxYear: parseInt(moment().format('YYYY'),10)
        });

        $('#payment_type').on('change', function() {
          $('.payment_type_ext').hide('fast');
          $('.'+$(this).val()).show('fast');
        });

        $('.input-number').keyup(function () { 
            this.value = this.value.replace(/[^0-9\.]/g,'');
        });

    }); 
</script>