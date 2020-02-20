@extends('layouts/contentLayoutMaster')

@section('title', 'Add Shop')
@section('vendor-style')
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection
@section('content')
  <section id="basic-usage" class="row">
      <div class=" col-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Connect a new shop to {{ env('APP_NAME') }}.</h4>
          </div>
          <div class="card-content">
            <div class="card-body">
              <p>Choose an e-commerce platform to connect.</p>
              <div class="row">
                <div class="col-md-4">
                  <label>Channel</label>
                  <select class="form-control sel2" id="channel">
                    <option value="lazada" data-url="{{ App\Lazop::getAuthLink() }}">Lazada</option>
                    <option value="shopee" data-url="{{ App\Shopee::getAuthLink() }}">Shopee</option>
                  </select>
                </div>
              </div>
              <br>

              <p><b>Step 1</b> : <button class="btn btn-primary continue"><i class="fa fa-check"></i> Connect Shop by clicking here</button></p>
              <p><b>Step 2</b> : Login Your e-commerce and click "Authorized"</p>
              <p><b>Step 3</b> : Put your store name and set your preferred shortname (usually 2-3 character)</p>
              
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
        window.location.href = $('#channel').find(":selected").data('url');
      });
    });
  </script>
@endsection