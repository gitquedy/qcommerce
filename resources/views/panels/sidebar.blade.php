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

            
            
            <li class="nav-item  ">
                <a href="{{url('/barcode')}}">
                    <i class="feather icon-hash"></i>
                    <span class="menu-title" data-i18n="nav.order">Barcode</span>
                </a>
            </li>


        
            <li class="nav-item  ">
                <a href="">
                    <i class="feather icon-shopping-bag"></i>
                    <span class="menu-title" data-i18n="">Shops</span>
                </a>
                <ul class="menu-content">
                    <li class="">
                        <a href="{{url("/shop")}}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title" data-i18n="">List Shop</span>
                        </a>
                    </li>
                    <li class="">
                        <a href="{{url("/shop/create")}}">
                            <i class="feather icon-plus"></i>
                            <span class="menu-title" data-i18n="">Add Shop</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            
            <li class="nav-item  ">
                <a href="">
                    <i class="feather icon-package "></i>
                    <span class="menu-title" data-i18n="">Products</span>
                </a>
                <ul class="menu-content">
                    <li class="">
                        <a href="{{url('/product')}}?site=lazada">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title" data-i18n="">List Products</span>
                        </a>
                    </li>
                    <!--<li class="">-->
                    <!--    <a href="{{url("/shop/create")}}">-->
                    <!--        <i class="feather icon-plus"></i>-->
                    <!--        <span class="menu-title" data-i18n="">Add Shop</span>-->
                    <!--    </a>-->
                    <!--</li>-->
                </ul>
            </li>
            
            <li class="nav-item  ">
                <a href="">
                    <i class="feather icon-shopping-cart"></i>
                    <span class="menu-title" data-i18n="">Orders</span>
                    
                </a>
                <ul class="menu-content">
                    <li class="">
                        <a href="{{url('/order')}}?site=lazada">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title" data-i18n="">All Orders</span>
                            @if($sidebar_data['order_all']>0)
                            <span class="badge badge-pill badge-info float-right">{!!$sidebar_data['order_all']!!}</span>
                            @endif
                        </a>
                    </li>
                    <li class="">
                        <a href="{{url('/order')}}?site=lazada&status=pending">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title" data-i18n="">Pending</span>
                            @if($sidebar_data['order_pending']>0)
                            <span class="badge badge-pill badge-warning float-right">{!!$sidebar_data['order_pending']!!}</span>
                            @endif
                        </a>
                    </li>
                    <li class="">
                        <a href="{{url('/order')}}?site=lazada&printed=false">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title" data-i18n="">For Printing</span>
                            @if($sidebar_data['order_printing']>0)
                            <span class="badge badge-pill badge-danger float-right">{!!$sidebar_data['order_printing']!!}</span>
                            @endif
                        </a>
                    </li>
                    <li class="">
                        <a href="{{url('/order')}}?site=lazada&status=ready_to_ship">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title" data-i18n="">Ready to ship</span>
                            @if($sidebar_data['order_ready']>0)
                            <span class="badge badge-pill  float-right" style="background-color:yellow;color:black;">{!!$sidebar_data['order_ready']!!}</span>
                            @endif
                        </a>
                    </li>
                    <li class="">
                        <a href="{{url('/order')}}?site=lazada&status=shipped">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title" data-i18n="">Shipped</span>
                            @if($sidebar_data['order_shipped']>0)
                            <span class="badge badge-pill badge-primary float-right">{!!$sidebar_data['order_shipped']!!}</span>
                            @endif
                        </a>
                    </li>
                    <li class="">
                        <a href="{{url('/order')}}?site=lazada&status=delivered">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title" data-i18n="">Delivered</span>
                            @if($sidebar_data['order_delivered']>0)
                            <span class="badge badge-pill badge-success float-right">{!!$sidebar_data['order_delivered']!!}</span>
                            @endif
                        </a>
                    </li>
                </ul>
            </li>
            
            <li class="nav-item  ">
                <a href="{{url('/shippingfee')}}">
                    <i class="feather icon-truck"></i>
                    <span class="menu-title" data-i18n="">Shipping Fee</span>
                </a>
            </li>
            
            
            
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
            
            
            
            <li class="nav-item  ">
                <a href="">
                    <i class="feather icon-package"></i>
                    <span class="menu-title" data-i18n="">SKU</span>
                </a>
                <ul class="menu-content">
                    <li class="">
                        <a href="{{url("/sku")}}">
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

            <li class="nav-item  ">
                <a href="">
                    <i class="feather icon-truck"></i>
                    <span class="menu-title" data-i18n="">Suppliers</span>
                    
                </a>
                <ul class="menu-content">
                    <li class="">
                        <a href="{{url("/supplier")}}">
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
            
            
            <li class="nav-item  ">
                <a href="">
                    <i class="feather icon-settings"></i>
                    <span class="menu-title" data-i18n="">Settings</span>
                    
                </a>
                <ul class="menu-content">
                    <li class="">
                        <a href="{{url("/category")}}" >
                            <i class="feather icon-circle"></i>
                            <span class="menu-title" data-i18n="">Categories</span>
                        </a>
                    </li>
                    <li class="">
                        <a href="{{url("/brand")}}" >
                            <i class="feather icon-circle"></i>
                            <span class="menu-title" data-i18n="">Brands</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            
        
            
        
        </ul>


        <!-----------ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
           {{-- Foreach menu item starts --}}
            @foreach($menuData[0]->menu as $menu)
                @if(in_array($request->user()->role, explode(',',isset($menu->access) ? $menu->access : '')) || isset($menu->access) == false)
                        @if(isset($menu->navheader))
                            <li class="navigation-header">
                                <span>{{ $menu->navheader }}</span>
                            </li>
                        @else
                          {{-- Add Custom Class with nav-item --}}
                          @php
                            $custom_classes = "";
                            if(isset($menu->classlist)) {
                              $custom_classes = $menu->classlist;
                            }
                            $translation = "";
                            if(isset($menu->i18n)){
                                $translation = $menu->i18n;
                            }
                          @endphp
                          <li class="nav-item {{ (request()->is($menu->url)) ? 'active' : '' }} {{ $custom_classes }}">
                                <a href="{{ $menu->url }}">
                                    <i class="{{ $menu->icon }}"></i>
                                    <span class="menu-title" data-i18n="{{ $translation }}">{{ $menu->name }}</span>
                                    @if (isset($menu->badge))
                                        <?php $badgeClasses = "badge badge-pill badge-primary float-right" ?>
                                        <span class="{{ isset($menu->badgeClass) ? $menu->badgeClass.' test' : $badgeClasses.' notTest' }} ">{{$menu->badge}}</span>
                                    @endif
                                </a>
                                @if(isset($menu->submenu))
                                    @include('panels/submenu', ['menu' => $menu->submenu])
                                @endif
                            </li>
                    @endif
                @endif
            @endforeach
        {{-- Foreach menu item ends --}}
        </ul------------------->
    </div>

</div>
<!-- END: Main Menu-->

