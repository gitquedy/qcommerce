<div class="modal-dialog modal-lg" role="document">
	<form action="{{ route('deposit.addDepositAjax') }}" id="add_deposit_ajax" method="POST" class="form" enctype='multipart/form-data'>
    @method('POST')
		@csrf
  <style>
    .form-control[readonly] {
         background-color: transparent;
      }
  </style>
  <div class="modal-content">
  	<div class="modal-header">
		<h4 class="modal-title" id="modal-title">Add Deposit</h4>
    <input type="hidden" name="customer_id" value="{{$customer->id}}">
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
                <label>Bank Referencce No.</label>
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
          <label>Amount</label>
          <div class="position-relative has-icon-left">
            <input type="number" name="amount" class="form-control">
            <div class="form-control-position"> 
              <i class="feather icon-dollar-sign"></i>
            </div>
          </div>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-12">
          <label for="note">Note</label>
          <textarea name="note" id="" cols="20" rows="5" class="form-control" placeholder="Note"></textarea>
        </div>
      </div>
	</div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary no-print"> Submit </button>
      </form>
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>
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
    }); 
</script>
<script src="{{ asset('js/scripts/forms-validation/form-modal.js') }}"></script>