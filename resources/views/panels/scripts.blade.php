    {{-- Vendor Scripts --}}
        <script src="{{ asset(mix('vendors/js/vendors.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/ui/prism.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
        @yield('vendor-script')
        {{-- Theme Scripts --}}
        <script src="{{ asset(mix('js/core/app-menu.js')) }}"></script>
        <script src="{{ asset(mix('js/core/app.js')) }}"></script>
        <script src="{{ asset(mix('js/scripts/components.js')) }}"></script>
        <script>
        
            
            function notification(){
             
                 $.post("{{route('ajax_get_notification')}}",
                  {
                    name: "ccc"
                  },
                  function(data, status){
                      
                       try {
                              var json_obj = JSON.parse(data);
                            }
                            catch(err) {
                              var json_obj = {status:false};
                            }
                        
                        if(json_obj.status=="success"){
                            notification_refresh(json_obj.data);
                        }
                    
                  });
                 
            }
            
            
            function notification_refresh(main_data){
                

                
                if(main_data.total>0){
                    $('#notification_count').html(main_data.total);
                    $('#notification_count_sub').html(main_data.total+" New");
                }else{
                    $('#notification_count').html("");
                    $('#notification_count_sub').html("NO");
                }
                
                var not_string = '';
                
                if(main_data.orders>0){
                
                   not_string += '<a class="d-flex justify-content-between" href="{{url("/orders_pending")}}">'+
                                    '<div class="media d-flex align-items-start">'+
                                        '<div class="media-left"><i class="feather icon-shopping-cart font-medium-5 primary"></i></div>'+
                                        '<div class="media-body">'+
                                            '<h6 class="primary media-heading">You have '+main_data.orders+' new order!</h6><small class="notification-text"> Click to see now</small>'+
                                        '</div><small>'+
                                            '<time class="media-meta" >'+main_data.order_string+'</time></small>'+
                                    '</div>'+
                                '</a>';
                }
                
                if(main_data.total_new_products>0){
                
                   not_string += '<a class="d-flex justify-content-between" href="{{url("/product")}}">'+
                                    '<div class="media d-flex align-items-start">'+
                                        '<div class="media-left"><i class="feather icon-package font-medium-5 primary"></i></div>'+
                                        '<div class="media-body">'+
                                            '<h6 class="primary media-heading">You have '+main_data.total_new_products+' new product</h6><small class="notification-text"> Click to see now</small>'+
                                        '</div><small>'+
                                            '<time class="media-meta" >'+main_data.last_product_time+'</time></small>'+
                                    '</div>'+
                                '</a>';
                }
                                
                $('#notification_area').html(not_string);
                
                
                
                
                
                
                
            }
            
            notification();
            
            
            
            
            setInterval(function(){ notification() }, 10000);
            
            
             
             
         
         
         
        </script>
@if($configData['blankPage'] == false)
        <script src="{{ asset(mix('js/scripts/customizer.js')) }}"></script>
        <script src="{{ asset(mix('js/scripts/footer.js')) }}"></script>
@endif


@if (session('flash_success'))
<script>
    Swal.fire(
  'Successfully',
  '{{ session('flash_success') }}',
  'success'
);
</script>
@endif

@if (session('flash_error'))
<script>
  Swal.fire(
  'Oopps !',
  '{{ session('flash_error') }}',
  'error'
);
</script>
@endif
<script>
$(document).ready(function(){
      $(".s2").select2({
        dropdownAutoWidth: true,
        width: '100%'
      });
  }); 
 
 $(document).ready(function(){
      $(".s280").select2({
        dropdownAutoWidth: true,
        width: '70%'
      });
  }); 
 </script>
