@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'App Store')

@section('vendor-style')
        {{-- vednor files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection
@section('mystyle')
    <link rel="stylesheet" href="{{ asset('css/pages/app-list.css') }}">
@endsection

@section('content')

<section class="card">
  <div class="card-header">
    <h4 class="card-title">Application Packages</h4>
  </div>
  <div class="card-content">
      <div class="card-body">
        <div class="row">
        @foreach($packages as $package)
        <div class="col-md-4 pd-t-10">
           <a href="{{ action('ApplicationController@show', $package->id) }}">
             <div class="bd rounded-5 pd-10 mg-y-10 ht-100p bd-gray-500">
                <div class="media mg-y-5">
                <img class="avatar avatar-lg" src="{{ $package->image }}" alt="">
                 <div class="mg-l-auto d-flex align-self-start d-none d-sm-block">
                    <button class="btn btn-primary tx-medium font-medium-1" type="button" onclick="openBundleDetail(this)" data-route="https://app.powersell.com/bundle/bundle-detail/502">Upgrade</button>
                  </div>                            
                </div>
                <label class="mg-y-0 lh-21 tx-16 tx-medium tx-gray-900">{{ $package->name }}</label>
                <input type="hidden" class="rate" value="5">
                <div class="d-none rating_0 tx-gray-900"><i class="fas fa-star fa-xs tx-orange" aria-hidden="true"></i><i class="fas fa-star fa-xs tx-orange" aria-hidden="true"></i><i class="fas fa-star fa-xs tx-orange" aria-hidden="true"></i><i class="fas fa-star fa-xs tx-orange" aria-hidden="true"></i><i class="fas fa-star fa-xs tx-orange" aria-hidden="true"></i></div>
                <p class="tx-gray-900">{{ $package->description }}</p>
            </div>
          </a>
        </div>

        @endforeach
        </div>
      </div>
  </div>
@endsection
@section('vendor-script')
{{-- vednor js files --}}
<script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
@endsection
@section('myscript')

@endsection
