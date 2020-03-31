@inject('request', 'Illuminate\Http\Request')
@php
    $configData = Helper::applClasses();
    $sidebar_data = Helper::get_sidebar_data();
@endphp


@if($request->user()->isAdmin()) 
    <!-- Admin -->
    @include('panels.adminSidebar')
@else
    <!-- Client -->
    @include('panels.clientSidebar')
@endif


