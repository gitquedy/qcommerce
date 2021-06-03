@extends('layouts/fullLayoutMaster')

@section('title', 'Error ' . $exception->getStatusCode())

@section('mystyle')
        {{-- Page Css files --}}
        <link rel="stylesheet" href="{{ asset(mix('css/pages/error.css')) }}">
@endsection
@section('content')
<!-- error 404 -->
<section class="row flexbox-container">
  <div class="col-xl-12 col-md-8 col-12 d-flex justify-content-center">
    <div class="card auth-card bg-transparent shadow-none rounded-0 mb-0 w-100">
      <div class="card-content">
        <div class="card-body text-center">
          <img src="{{ asset('images/pages/404.png') }}" class="img-fluid align-self-center" alt="branding logo">
          <h1 class="font-large-2 my-1">{{ $exception->getStatusCode()  }} - {{ $exception->getMessage() }}</h1>
          <p class="p-2">
            Sorry for the inconvenience caused. Please try again later
          </p>
          <a class="btn btn-primary btn-lg mt-2" href="{{ url('/') }}">Back to Home</a>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- error 404 end -->
@endsection
