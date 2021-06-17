<div class="main-menu menu-fixed {{($configData['theme'] === 'light') ? "menu-light" : "menu-dark"}} menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row" style="flex-wrap:nowrap;">
            <li class="nav-item mr-auto active"><a class="navbar-brand" href="{{ route('dashboard') }}">
        <div class="brand-logo"></div>
                    <div class="brand-text-logo"></div>
                    {{-- <h2 class="brand-text mb-0">{{ env('APP_NAME') }}</h2> --}}
                </a></li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block primary collapse-toggle-icon" data-ticon="icon-disc"></i></a></li>
        </ul>
    </div>
<div class="shadow-bottom"></div>
<div class="main-menu-content">  
    <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">

        <li class="nav-item {{ $request->segment(1) == '' ? 'active' : '' }}">
            <a href="{{route('admin.dashboard')}}">
                <i class="feather icon-home"></i>
                <span class="menu-title" data-i18n="nav.order">Dashboard</span>
            </a>
        </li>  

        <li class="{{ $request->segment(1) == 'admin' && $request->segment(2) == 'manageuser' ? 'active' : '' }}">
            <a href="{{url('/admin/manageuser')}}">
                <i class="feather icon-users"></i>
                <span class="menu-title" data-i18n="">User Management</span>
            </a>
        </li>

        <li class="{{ $request->segment(1) == 'admin' && $request->segment(2) == 'manageshop' ? 'active' : '' }}">
            <a href="{{url('/admin/manageshop')}}">
                <i class="feather icon-shopping-bag"></i>
                <span class="menu-title" data-i18n="">Shop Management</span>
            </a>
        </li>

        <li class="{{ $request->segment(1) == 'admin' && $request->segment(2) == 'promocode' ? 'active' : '' }}">
            <a href="{{url('/admin/promocode')}}">
                <i class="feather icon-plus"></i>
                <span class="menu-title" data-i18n="">Promocode</span>
            </a>
        </li>

        <li class="{{ $request->segment(1) == 'admin' && $request->segment(2) == 'subscription' ? 'active' : '' }}">
            <a href="{{url('/admin/subscription')}}">
                <i class="feather icon-command"></i>
                <span class="menu-title" data-i18n="">Subscription</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="#">
                <i class="fa fa-credit-card"></i>
                <span class="menu-title" data-i18n="">Billing</span>
            </a>
            <ul class="menu-content">
                <li class="{{ $request->segment(1) == 'admin' && $request->segment(2) == 'billing' && $request->segment(3) == '' ? 'active' : '' }}">
                    <a href="{{url('/admin/billing')}}">
                        <i class="feather icon-circle"></i>
                        <span class="menu-title" data-i18n="">List of Invoices</span>
                    </a>
                </li>
                <li class="{{ $request->segment(1) == 'admin' && $request->segment(2) == 'billing' && $request->segment(3) == 'overdue' ? 'active' : '' }}">
                    <a href="{{route('billing.overdue')}}">
                        <i class="feather icon-circle"></i>
                        <span class="menu-title" data-i18n="">Over Due Invoices</span>
                    </a>
                </li>
            </ul>
        </li>
        
    </ul>
</div>
</div>