@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Add Shop')
@section('vendor-style')
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection
@section('content')
  <section id="basic-usage" class="container justify-content-md-center ">
      <div class="col-12">
        <div class="card">
          <div class="row">
          <div class="col-8">
            <div class="card-header">
              <h4 class="card-title">Connect a new shop to {{ env('APP_NAME') }}.</h4>
            </div>
            <div class="card-content">
              <div class="card-body">
                <p>Choose an e-commerce platform to connect.</p>
                <div class="row">
                  <div class="col-md-8">
                    <label>Channel</label>
                    <select class="form-control sel2" id="channel" name="channel">
                      <option value="lazada" data-url="{{ App\Lazop::getAuthLink() }}">Lazada</option>
                      <option value="shopee" data-url="{{ App\Shopee::getAuthLink() }}">Shopee</option>
                      <option value="shopify" data-url="{{ action('ShopifyController@install') }}">Shopify</option>
                    </select>
                  </div>
                </div><br>
                <div class="row" id="shopify">
                  <div class="col-md-8">
                    <div class="form-group">
                      <label>Domain</label>
                      <input class="form-control" id="domain" placeholder="Domain e.g. qcommerce.myshopify.com">
                    </div>
                  </div>
                </div>
                <br>
                <p><b>Step 1</b> : <button class="btn btn-primary continue"><i class="fa fa-check"></i> Connect Shop by clicking here</button></p>
                <p><b>Step 2</b> : Login Your e-commerce and click "Authorized"</p>
                <p><b>Step 3</b> : Put your store name and set your preferred shortname (usually 2-3 character)</p>
              </div>
            </div>
          </div>
          <div class="col-4">
            <div class="media-list">
                <div class="media">
                  <div class="media-body text-right">
                    <h4 class="media-heading">Lazada </h4>
                    Launched in 2012, Lazada is the selling destination in Southeast Asia – present in Indonesia, Malaysia, the Philippines, Singapore, Thailand and Vietnam.
                  </div>
                  <a class="media-right" href="#">
                    <img class="media-object rounded-circle"
                      src="{{ asset('images/shop/200x200/lazada.png') }}"  alt="Generic placeholder image"
                      height="64" width="64" />
                  </a>
                </div>
                <div class="media">
                  <div class="media-body text-right">
                    <h4 class="media-heading">Shopee</h4>
                    Shopee is a Singaporean e-commerce platform headquartered under Sea Group, which is a global consumer internet company founded in 2015 by Forrest Li.
                  </div>
                  <a class="media-right" href="#">
                    <img class="media-object rounded-circle"
                      src="{{ asset('images/shop/1024x1024/shopee.png') }}" alt="Generic placeholder image"
                      height="64" width="64" />
                  </a>
                </div>
                 <div class="media">
                  <div class="media-body text-right">
                    <h4 class="media-heading">Shopify </h4>
                    Shopify Inc. is a Canadian multinational e-commerce company headquartered in Ottawa, Ontario. It is also the name of its proprietary e-commerce platform for online stores and retail point-of-sale systems.
                  </div>
                  <a class="media-right" href="#">
                    <img class="media-object rounded-circle"
                      src="{{ asset('images/shop/800x800/shopify.png') }}"  alt="Generic placeholder image"
                      height="64" width="64" />
                  </a>
                </div>
              </div>
          </div>
          </div>
        </div>
      </div>
    </section>


@endsection

@section('vendor-script')
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection
@section('myscript')
  <script type="text/javascript">
    $(document).ready(function(){
      $("#shopify").slideUp();
      function formatState (state) {
        if (!state.id) { return state.text; }
        var $state = $(
          '<span><img width="40px" height="40px" src="/images/shop/260x260/' +  state.element.value.toLowerCase() + 
          '.png" class="img-flag" /> ' + 
          state.text +     '</span>'
           );
        return $state;
      };
      $('.sel2').select2({
        templateResult: formatState
      });
      $('.continue').click(function(){
        var channel = $('#channel').find(":selected").val();
        if(channel == 'shopify'){
          var domain = $("#domain").val();
          if(domain == ''){
            alert('Domain is required');
            return;
          }
          window.location.href = $('#channel').find(":selected").data('url') + '?channel=' + domain;
        }else{
          window.location.href = $('#channel').find(":selected").data('url');
        }
      });
      $('select[name=channel]').on('change', function() {
        if(this.value == 'shopify'){
          $("#shopify").slideDown();
        }else{
          $("#shopify").slideUp();
        }
      });

      // $(".select2").select2({
      //       dropdownAutoWidth: true,
      //       width: '100%'
      //   });
        $('select[name=warehouse_id]').on('change', function() {
            var selected = $(this).find('option:selected').val();
            if(selected == 'add_new') {
              $.ajax({
                url :  "{{ route('warehouse.addWarehouseModal') }}",
                type: "POST",
                success: function (response) {
                  if(response) {
                    $(".view_modal").html(response).modal('show');
                  }
                }
              });
              $(this).val('').trigger('change');
            } 
        });
    });


  </script>
@endsection