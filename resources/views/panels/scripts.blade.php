{{-- Vendor Scripts --}}
        <script src="{{ asset(mix('vendors/js/vendors.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/ui/prism.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
        @yield('vendor-script')
        {{-- Theme Scripts --}}
        <script src="{{ asset(mix('js/core/app-menu.js')) }}"></script>
        <script src="{{ asset(mix('js/core/app.js')) }}"></script>
        <script src="{{ asset(mix('js/scripts/components.js')) }}"></script>
        <script src="{{ asset('vendors/js/printThis/printThis.js') }}"></script>
        <script src="{{ asset('vendors/js/forms/extended/typeahead/typeahead.bundle.min.js') }}"></script>
        <script>
            var last_notification_count = "first";

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

            function countOccurences(arr) {
              var a = [],
                b = [],
                prev;

              arr.sort();
              for (var i = 0; i < arr.length; i++) {
                if (arr[i] !== prev) {
                  a.push(arr[i]);
                  b.push(1);
                } else {
                  b[b.length - 1]++;
                }
                prev = arr[i];
              }

              return [a, b];
            }

            function timeDifference(current, previous) {
              var msPerMinute = 60 * 1000;
              var msPerHour = msPerMinute * 60;
              var msPerDay = msPerHour * 24;
              var msPerMonth = msPerDay * 30;

              var elapsed = current - previous;
              var difference = '';

              if (elapsed < msPerHour) {
                  difference += Math.round(elapsed/msPerMinute);
                  difference += Math.round(elapsed/msPerMinute) > 1 ? ' minutes ' : ' minute ';   
              }

              else if (elapsed < msPerDay ) {
                  difference += Math.round(elapsed/msPerHour );
                  difference += Math.round(elapsed/msPerHour) > 1 ? ' hours ' : ' hour ';
                  if (elapsed%msPerHour != 0) {
                    difference += Math.round((elapsed%msPerHour)/msPerMinute);
                    diffrence += Math.round((elapsed%msPerHour)/msPerMinute) > 1 ? ' minutes' : ' minute ';
                  }
              }

              else if (elapsed < msPerMonth) {
                  difference += Math.round(elapsed/msPerDay);
                  difference += Math.round(elapsed/msPerDay) > 1 ? ' days ' : ' day ';
                  if (elapsed%msPerDay != 0) {
                    difference += Math.round((elapsed%msPerDay)/msPerHour);
                    difference += Math.round((elapsed%msPerDay)/msPerHour) > 1 ? ' hours ' : ' hour ';
                  }
                  if (elapsed%msPerHour != 0) {
                    difference += Math.round((elapsed%msPerHour)/msPerMinute);
                    difference += Math.round((elapsed%msPerHour)/msPerMinute) > 1 ? ' minutes' : ' minute ';
                  }
              }
              return difference;
            }
            
            function notification_refresh(main_data){
                if(last_notification_count != "first" && last_notification_count < main_data.total) {
                    let src = '{{asset('file/notification.mp3')}}';
                    let audio = new Audio(src);
                    audio.play();
                }
                last_notification_count = main_data.total;
                if(main_data.total>0){
                    $('#notification_count').html(main_data.total);
                    $('#notification_count_sub').html(main_data.total+" New");
                }else{
                    $('#notification_count').html("");
                    $('#notification_count_sub').html("NO");
                }

                
                var not_string = '<div id="sound"></div>';
                
                if(main_data.orders>0){

                  var shop = [];
                  for (var i = 0; i < main_data.order_value.length; i++) {
                    shop.push(main_data.order_value[i].short_name);
                  }

                  var result = countOccurences(shop);
                  var shop_short_name = result[0];
                  var shop_order_count = result[1];
                  var shop_total_order_price = new Array(shop_short_name.length).fill(0);
                  var site = new Array(shop_short_name.length).fill('');
                  var time = new Array(shop_short_name.length).fill('');

                  for (var i = 0; i < shop_short_name.length; i++) {
                    for (var j = 0; j < main_data.order_value.length; j++) {
                      if (shop_short_name[i] == main_data.order_value[j].short_name) {
                        shop_total_order_price[i] += main_data.order_value[j].price;
                        if (site[i] == '') {
                          site[i] = main_data.order_value[j].site;
                          time[i] = main_data.order_value[j].created_at;
                        }
                      }
                    }
                  }

                  for (var i = 0; i < shop_short_name.length; i++) {

                    if (site[i] == 'lazada') {
                      var site_link = 'lazada&status=pending';
                    }
                    if (site[i] == 'shopee') {
                      var site_link = 'shopee&status=READY_TO_SHIP';
                    }
                    if (site[i] == 'shopify') {
                      var site_link = 'site=shopify&status=Open';
                    }
                    if (site[i] == 'woocommerce') {
                      var site_link = 'woocommerce&status=processing';
                    }

                    var new_orders = shop_order_count[i] > 1 ? ' new orders' : ' new order';

                    not_string += '<a class="d-flex justify-content-between" href="{{url("/order")}}?site=' + site_link + '">'+
                                    '<div class="media d-flex align-items-start">'+
                                        '<div class="media-left"><img width="40px" height="40px" src="/images/shop/icon/' +  site[i] + '.png" class="img-flag" /></div>'+
                                        '<div class="media-body">'+
                                            '<h6 class="primary media-heading">You have ' + shop_order_count[i] + new_orders + '</h6><small class="notification-text">' + shop_short_name[i] + ' - Php' + shop_total_order_price[i] + '</small>'+
                                        '</div><small>'+
                                            '<time class="media-meta" >' + timeDifference(new Date(), new Date(time[i])) + ' ago</time></small>'+
                                    '</div>'+
                                '</a>';
                  }
                }
                
                if(main_data.total_new_products>0){

                  var shop = [];
                  for (var i = 0; i < main_data.product_value.length; i++) {
                    shop.push(main_data.product_value[i].short_name);
                  }

                  var result = countOccurences(shop);
                  var shop_short_name = result[0];
                  var shop_product_count = result[1];
                  var site = new Array(shop_short_name.length).fill('');
                  var time = new Array(shop_short_name.length).fill('');

                  for (var i = 0; i < shop_short_name.length; i++) {
                    for (var j = 0; j < main_data.product_value.length; j++) {
                      if (shop_short_name[i] == main_data.product_value[j].short_name) {
                        site[i] = main_data.product_value[j].site;
                        time[i] = main_data.product_value[j].created_at;
                        break;
                      }
                    }
                  }

                  for (var i = 0; i < shop_short_name.length; i++) {

                    var new_products = shop_product_count[i] > 1 ? ' new products' : ' new product';

                    not_string += '<a class="d-flex justify-content-between" href="{{url("/product")}}?site=' + site[i] + '">'+
                                    '<div class="media d-flex align-items-start">'+
                                        '<div class="media-left"><img width="40px" height="40px" src="/images/shop/icon/' +  site[i] + '.png" class="img-flag" /></div>'+
                                        '<div class="media-body">'+
                                            '<h6 class="primary media-heading">You have ' + shop_product_count[i] + new_products + '</h6><small class="notification-text">' + shop_short_name[i] + '</small>'+
                                        '</div><small>'+
                                            '<time class="media-meta" >' + timeDifference(new Date(), new Date(time[i])) + ' ago</time></small>'+
                                    '</div>'+
                                '</a>';
                  }
                }          
                $('#notification_area').html(not_string);
            }
            $(document).ready(function(){
              notification();
              setInterval(function(){ notification() }, 100000); 
            });

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

<script type="text/javascript">
  $(document).ready(function() {
  @if(Session::has('status'))
 toastr.{{ Session::get('alert-class', 'success') }}('{{ Session::get('status') }}', '', {timeOut: 10000})
  @endif
  @if(Session::has('success'))
 toastr.{{ Session::get('alert-class', 'success') }}('{{ Session::get('success') }}', '', {timeOut: 10000})
  @endif
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })
});
</script>
@endif
<script>
// $(document).ready(function(){
//       $(".s2").select2({
//         dropdownAutoWidth: true,
//         width: '100%'
//       });
//   }); 
 
 // $(document).ready(function(){
 //      $(".s280").select2({
 //        dropdownAutoWidth: true,
 //        width: '70%'
 //      });
 //  }); 
 </script>
