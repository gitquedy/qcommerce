@inject('request', 'Illuminate\Http\Request')
@extends('layouts/contentLayoutMaster')

@section('title', 'Add New Bank')

@section('vendor-style')
        {{-- vednor files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection
@section('mystyle')
        {{-- Page css files --}}
@endsection

@section('content')
<section class="card">
    <div class="card-content">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{route('billing.details.add')}}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Bank</label>
                        <input class="form-control" name="bank">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Account Name</label>
                        <input class="form-control" name="account_name">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Account Number</label>
                        <input type="number" class="form-control" name="account_number">
                    </div>
                </div>
                <div class="col-md-12 text-right">
                    <br><button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@section('vendor-script')
{{-- vendor js files --}}
    <script src="{{ asset('js/scripts/forms-validation/form-normal.js') }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection
@section('myscript')
<script type="text/javascript">
</script>
@endsection
