  $("#login-form").submit(function(e) {
    e.preventDefault();
     $('.btn_save').prop('disabled', true);
      $.ajax({
        url : $(this).attr('action'),
        type : 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function(result){  
          console.log(result);
          if(result.success == true){
            toastr.info('Successful login');
            $('.error').remove();
            $('.form')[0].reset();
              setTimeout(function(){
                  window.location.replace(result.url);
              }, 1000);
          }else{
            if(result.msg){
              toastr.error(result.msg);
            }
                $.each(result.error, function(index, val){
                $('#error-box').html(val);
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