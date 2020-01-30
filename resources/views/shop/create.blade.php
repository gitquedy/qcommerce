@extends('layouts/contentLayoutMaster')

@section('title', 'Add Shop')

@section('content')
<section id="basic-usage" class="row">
    <div class=" col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Register your Shop</h4>
        </div>
        <div class="card-content">
          <div class="card-body">
            <p>To connect to Lazada Philippines shop, please follow the instructions below: </p>
            <p><b>Step 1</b> : <a href="{{ App\Lazop::getAuthLink() }}">Connect Shop by clicking here</a></p>
            <p><b>Step 2</b> : Login Your Lazada and click "Authorized"</p>
            <p><b>Step 3</b> : Put your store name and set your preferred shortname (usually 2-3 character)</p>
            
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection