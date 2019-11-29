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
            <p>To connect to Lazada Philippines shop, please follow the instructions below: <a href="{{ App\Lazop::getAuthLink() }}">Connect Shop Instruction</a></p>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection