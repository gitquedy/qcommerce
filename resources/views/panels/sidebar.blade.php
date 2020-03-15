@inject('request', 'Illuminate\Http\Request')
@php
    $configData = Helper::applClasses();
    $sidebar_data = Helper::get_sidebar_data();
@endphp
<div class="main-menu menu-fixed {{($configData['theme'] === 'light') ? "menu-light" : "menu-dark"}} menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row" style="flex-wrap:nowrap;">
            <li class="nav-item mr-auto"><a class="navbar-brand" href="{{ route('dashboard') }}">
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

            <li class="nav-item  ">
                <a>
                    <span class="menu-title" data-i18n="nav.order">Navigation</span>
                </a>
            </li> 

            <li class="nav-item  ">
                <a href="{{route('dashboard')}}">
                    <i class="feather icon-home"></i>
                    <span class="menu-title" data-i18n="nav.order">Dashboard</span>
                </a>
            </li>  
        
            

            @can('shop.manage')
                <li class="nav-item  ">
                    <a href="">
                        <i class="feather icon-shopping-bag"></i>
                        <span class="menu-title" data-i18n="">Shops</span>
                    </a>
                    <ul class="menu-content">
                        <li class="">
                            <a href="{{url('/shop')}}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title" data-i18n="">List Shop</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="{{url('/shop/create')}}">
                                <i class="feather icon-plus"></i>
                                <span class="menu-title" data-i18n="">Add Shop</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan
            
            @can('product.manage')
                <li class="nav-item  ">
                    <a href="{{url('/product')}}?site=lazada">
                        <i class="feather icon-package "></i>
                        <span class="menu-title" data-i18n="">Products</span>
                    </a>
                </li>
            @endcan
            
            @can('order.manage')
                <li class="nav-item  ">
                    <a href="{{url('/order')}}?site=lazada&status=pending">
                        <i class="feather icon-shopping-cart"></i>
                        <span class="menu-title" data-i18n="">Orders</span>
                        
                    </a>
                </li>
            @endcan

            @if( $request->user()->can('returnRecon.manage') || $request->user()->can('payoutRecon.manage') || $request->user()->can('shippingFeeRecon.manage'))
                <li class="nav-item  ">
                    <a href="">
                        <i class="feather icon-briefcase "></i>
                        <span class="menu-title" data-i18n="">Reconciliation</span>
                    </a>
                    <ul class="menu-content">
                        @can('shippingFeeRecon.manage')
                            <li class="">
                                <a href="{{url('/order/reconciliation/shippingFee')}}?tab=all">
                                    <i class="feather icon-circle"></i>
                                    <span class="menu-title" data-i18n="">Shipping Fee</span>
                                </a>
                            </li>
                        @endcan
                        @can('payoutRecon.manage')
                            <li class="">
                                <a href="{{url('/order/reconciliation/payout')}}?tab=all">
                                    <i class="feather icon-circle"></i>
                                    <span class="menu-title" data-i18n="">Payout</span>
                                </a>
                            </li>
                        @endcan
                        @can('returnRecon.manage')
                            <li class="">
                                <a href="{{url('/order/reconciliation/returned')}}?tab=all">
                                    <i class="feather icon-circle"></i>
                                    <span class="menu-title" data-i18n="">Failed Delivery Return</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            <li class="nav-item  ">
                <a >
                    <span class="menu-title" data-i18n="nav.order"></span>
                </a>
            </li>
            
            <li class="nav-item  ">
                <a >
                    <span class="menu-title" data-i18n="nav.order">Inventory</span>
                </a>
            </li>
            
            @can('sku.manage')
                <li class="nav-item  ">
                    <a href="">
                        <i class="feather icon-package"></i>
                        <span class="menu-title" data-i18n="">SKU</span>
                    </a>
                    <ul class="menu-content">
                        <li class="">
                            <a href="{{url('/sku')}}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title" data-i18n="">List of SKU</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="{{route('sku.create')}}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title" data-i18n="">Add new SKU</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('barcode.manage')
            <li class="nav-item  ">
                <a href="{{url('/barcode')}}">
                    <i class="feather icon-hash"></i>
                    <span class="menu-title" data-i18n="nav.order">Barcode</span>
                </a>
            </li>
            @endcan

            @can('supplier.manage')
                <li class="nav-item  ">
                    <a href="">
                        <i class="feather icon-truck"></i>
                        <span class="menu-title" data-i18n="">Suppliers</span>
                        
                    </a>
                    <ul class="menu-content">
                        <li class="">
                            <a href="{{url('/supplier')}}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title" data-i18n="">List of Suppliers</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="{{route('supplier.create')}}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title" data-i18n="">Add new Supplier</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan
            
            @can('report.manage')
                <li class="nav-item  ">
                    <a href="">
                        <i class="feather icon-file-text"></i>
                        <span class="menu-title" data-i18n="">Reports</span>
                        
                    </a>
                    <ul class="menu-content">
                        <li class="">
                            <a href="{{route('reports.outOfStock')}}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title" data-i18n="">Out of Stock</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="{{route('reports.productAlert')}}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title" data-i18n="">Product Alert</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan
            
            @can('user.manage')
                <li class="nav-item  ">
                    <a href="{{url('/user')}}">
                        <i class="feather icon-users"></i>
                        <span class="menu-title" data-i18n="">User Management</span>
                    </a>
                </li>
            @endif
            
            <!-- @if($request->user()->can('category.manage') || $request->user()->can('brand.manage'))
                <li class="nav-item  ">
                    <a href="">
                        <i class="feather icon-settings"></i>
                        <span class="menu-title" data-i18n="">Settings</span>
                    </a>
                    <ul class="menu-content">
                        @can('category.manage')
                            <li class="">
                                <a href="{{url('/category')}}" >
                                    <i class="feather icon-circle"></i>
                                    <span class="menu-title" data-i18n="">Categories</span>
                                </a>
                            </li>
                        @endcan
                        @can('brand.manage')
                            <li class="">
                                <a href="{{url('/brand')}}" >
                                    <i class="feather icon-circle"></i>
                                    <span class="menu-title" data-i18n="">Brands</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif -->
        </ul>
    </div>
</div>

