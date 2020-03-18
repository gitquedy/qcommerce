
$(document).on('click', '.chip-closeable',function() {
    var target = $(this).data('target');
    if($(this).data('type') == "multiple") {
      var value = $("#"+target).val().split(',');
      const index = value.indexOf($(this).data('value').toString());
      if (index > -1) {
        value.splice(index, 1);
      }
      $("#"+target).val(value.join(',')).trigger('change');
    }
    else {
      $("#"+target).val('').trigger('change');
    }
  });


$(document).on('click', '.filter_btn', function(event) {
    event.preventDefault();
    var target = $(this).data('target');
    if($(this).data('type') == "multiple") {
      if($("#"+target).val()==""){
        $("#"+target).val($(this).data('value')).trigger('change');
        $('#chip_area_'+target).append('<div class="chip chip-primary"><div class="chip-body"><span class="chip-text">'+$(this).html()+'</span><div class="chip-closeable" data-target="'+target+'" data-type="'+$(this).data('type')+'" data-value="'+$(this).data('value')+'"><i class="feather icon-x"></i></div></div></div>');
      }
      else {
        var value = $("#"+target).val().split(',');
        if($.inArray($(this).data('value').toString(), value) === -1){
          value.push($(this).data('value'));
          $("#"+target).val(value.join(',')).trigger('change');
          $('#chip_area_'+target).append('<div class="chip chip-primary"><div class="chip-body"><span class="chip-text">'+$(this).html()+'</span><div class="chip-closeable" data-target="'+target+'" data-type="'+$(this).data('type')+'" data-value="'+$(this).data('value')+'"><i class="feather icon-x"></i></div></div></div>');
        }
      }
    }
    else {
      $('#chip_area_'+target).html('<div class="chip chip-primary"><div class="chip-body"><span class="chip-text">'+$(this).html()+'</span><div class="chip-closeable" data-target="'+target+'" data-type="'+$(this).data('type')+'" data-value="'+$(this).data('value')+'"><i class="feather icon-x"></i></div></div></div>');
      $("#"+target).val($(this).data('value')).trigger('change');
    }
});