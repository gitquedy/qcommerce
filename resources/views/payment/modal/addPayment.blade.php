
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
                    <input type="number" name="paid" class="form-control update_input check_max_quantity" value="0" max="0">
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
                      <option value="gc">Gift Card</option>
                      <option value="cc">Credit Card</option>
                      <option value="cheque">Cheque</option>
                      <option value="other">Other</option>
                    </select>
                    <div class="form-control-position"> 
                      <i class="feather icon-credit-card"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
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
        })

        $('.datepicker').daterangepicker({
          singleDatePicker: true,
          showDropdowns: true,
          minYear: 1901,
          maxYear: parseInt(moment().format('YYYY'),10)
        });

       $(document).on('submit',"#add_payment_ajax",function(e) {
          e.preventDefault();
           $('.btn_save').prop('disabled', true);
            $.ajax({
              url : $(this).attr('action'),
              type : 'POST',
              data: new FormData(this),
              processData: false,
              contentType: false,
              success: function(result){
                if(result.success == true){
                  toastr.success(result.msg, '' , {positionClass : "toast-top-center", escapeHTML: false});
                  $('.view_modal').modal('toggle');
                }else{
                  if(result.msg){
                    toastr.error(result.msg);
                  }
                   $('.error').remove();
                      $.each(result.error, function(index, val){
                      $('[name="'+ index +'"]').after('<label class="text-danger error">' + val + '</label>');
                      });
                }
                $('.btn_save').prop('disabled', false);
                 },
                error: function(jqXhr, json, errorThrown){
                  console.log(jqXhr);
                  console.log(json);
                  console.log(errorThrown);
                  $('.btn_save').prop('disabled', false);
                }
              });
       });

    }); 
</script>