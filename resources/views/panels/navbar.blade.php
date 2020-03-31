@if($configData["mainLayoutType"] == 'horizontal')
  <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu {{ $configData['navbarColor'] }} navbar-fixed">
      <div class="navbar-header d-xl-block d-none">
          <ul class="nav navbar-nav flex-row">
              <li class="nav-item"><a class="navbar-brand" href="dashboard-analytics">
                  <div class="brand-logo"></div></a></li>
          </ul>
      </div>
  @else
    <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu {{ $configData['navbarClass'] }} navbar-light navbar-shadow {{ $configData['navbarColor'] }}">
  @endif
    <div class="navbar-wrapper">
        <div class="navbar-container content">
            <div class="navbar-collapse" id="navbar-mobile">
                <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                    <ul class="nav navbar-nav">
                        <li class="nav-item mobile-menu d-xl-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ficon feather icon-menu"></i></a></li>
                    </ul>
                    <!--<ul class="nav navbar-nav bookmark-icons">-->
                    <!--    <li class="nav-item d-none d-lg-block"><a class="nav-link" href="/app-todo" data-toggle="tooltip" data-placement="top" title="Todo"><i class="ficon feather icon-check-square"></i></a></li>-->
                    <!--    <li class="nav-item d-none d-lg-block"><a class="nav-link" href="/app-chat" data-toggle="tooltip" data-placement="top" title="Chat"><i class="ficon feather icon-message-square"></i></a></li>-->
                    <!--    <li class="nav-item d-none d-lg-block"><a class="nav-link" href="/app-email" data-toggle="tooltip" data-placement="top" title="Email"><i class="ficon feather icon-mail"></i></a></li>-->
                    <!--    <li class="nav-item d-none d-lg-block"><a class="nav-link" href="/app-calender" data-toggle="tooltip" data-placement="top" title="Calendar"><i class="ficon feather icon-calendar"></i></a></li>-->
                    <!--    <li class="nav-item d-none d-lg-block"><a class="nav-link" href="{{ route('shop.create') }}" data-toggle="tooltip" data-placement="top" title="Shop"><i class="ficon feather icon-plus"></i></a></li>-->
                    <!--</ul>-->
                    <ul class="nav navbar-nav">
                        <!--<li class="nav-item d-none d-lg-block"><a class="nav-link bookmark-star"><i class="ficon feather icon-star warning"></i></a>-->
                        <!--    <div class="bookmark-input search-input">-->
                        <!--        <div class="bookmark-input-icon"><i class="feather icon-search primary"></i></div>-->
                        <!--        <input class="form-control input" type="text" placeholder="Explore Vuexy..." tabindex="0" data-search="laravel-search-list" />-->
                        <!--        <ul class="search-list"></ul>-->
                        <!--    </div>-->
                            <!-- select.bookmark-select-->
                            <!--   option 1-Column-->
                            <!--   option 2-Column-->
                            <!--   option Static Layout-->
                        <!--</li>-->
                    </ul>
                </div>
                <ul class="nav navbar-nav float-right">
                    <!--<li class="dropdown dropdown-language nav-item"><a class="dropdown-toggle nav-link" id="dropdown-flag" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="flag-icon flag-icon-us"></i><span class="selected-language">English</span></a>-->
                    <!--    <div class="dropdown-menu" aria-labelledby="dropdown-flag"><a class="dropdown-item" href="#" data-language="en"><i class="flag-icon flag-icon-us"></i> English</a><a class="dropdown-item" href="#" data-language="fr"><i class="flag-icon flag-icon-fr"></i> French</a><a class="dropdown-item" href="#" data-language="de"><i class="flag-icon flag-icon-de"></i> German</a><a class="dropdown-item" href="#" data-language="pt"><i class="flag-icon flag-icon-pt"></i> Portuguese</a></div>-->
                    <!--</li>-->
                    <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand"><i class="ficon feather icon-maximize"></i></a></li>
                    <!--<li class="nav-item nav-search"><a class="nav-link nav-link-search"><i class="ficon feather icon-search"></i></a>-->
                    <!--    <div class="search-input">-->
                    <!--        <div class="search-input-icon"><i class="feather icon-search primary"></i></div>-->
                    <!--        <input class="input" type="text" placeholder="Explore Vuexy..." tabindex="-1" data-search="laravel-search-list" />-->
                    <!--        <div class="search-input-close"><i class="feather icon-x"></i></div>-->
                    <!--        <ul class="search-list"></ul>-->
                    <!--    </div>-->
                    <!--</li>-->
                    <li class="dropdown dropdown-notification nav-item"><a class="nav-link nav-link-label" href="#" data-toggle="dropdown"><i class="ficon feather icon-bell"></i><span id="notification_count" class="badge badge-pill badge-primary badge-up"></span></a>
                        <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                            <li class="dropdown-menu-header">
                                <div class="dropdown-header m-0 p-2">
                                    <h3 class="white" id="notification_count_sub"></h3>
                                    <span class="grey darken-2">App Notifications</span>
                                </div>
                            </li>
                            <li class="scrollable-container media-list" id="notification_area">
                                <!-- a(href='javascript:void(0)').d-flex.justify-content-between-->
                                <!--   .d-flex.align-items-start-->
                                <!--       i.feather.icon-plus-square-->
                                <!--       .mx-1-->
                                <!--         .font-medium.block.notification-title New Message-->
                                <!--         small Are your going to meet me tonight?-->
                                <!--   small 62 Days ago-->
                                
                                
                                <!--<a class="d-flex justify-content-between" href="javascript:void(0)">-->
                                <!--    <div class="media d-flex align-items-start">-->
                                <!--        <div class="media-left"><i class="feather icon-download-cloud font-medium-5 success"></i></div>-->
                                <!--        <div class="media-body">-->
                                <!--            <h6 class="success media-heading red darken-1">99% Server load</h6><small class="notification-text">You got new order of goods.</small>-->
                                <!--        </div><small>-->
                                <!--            <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">5 hour ago</time></small>-->
                                <!--    </div>-->
                                <!--</a><a class="d-flex justify-content-between" href="javascript:void(0)">-->
                                <!--    <div class="media d-flex align-items-start">-->
                                <!--        <div class="media-left"><i class="feather icon-alert-triangle font-medium-5 danger"></i></div>-->
                                <!--        <div class="media-body">-->
                                <!--            <h6 class="danger media-heading yellow darken-3">Warning notifixation</h6><small class="notification-text">Server have 99% CPU usage.</small>-->
                                <!--        </div><small>-->
                                <!--            <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">Today</time></small>-->
                                <!--    </div>-->
                                <!--</a><a class="d-flex justify-content-between" href="javascript:void(0)">-->
                                <!--    <div class="media d-flex align-items-start">-->
                                <!--        <div class="media-left"><i class="feather icon-check-circle font-medium-5 info"></i></div>-->
                                <!--        <div class="media-body">-->
                                <!--            <h6 class="info media-heading">Complete the task</h6><small class="notification-text">Cake sesame snaps cupcake</small>-->
                                <!--        </div><small>-->
                                <!--            <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">Last week</time></small>-->
                                <!--    </div>-->
                                <!--</a><a class="d-flex justify-content-between" href="javascript:void(0)">-->
                                <!--    <div class="media d-flex align-items-start">-->
                                <!--        <div class="media-left"><i class="feather icon-file font-medium-5 warning"></i></div>-->
                                <!--        <div class="media-body">-->
                                <!--            <h6 class="warning media-heading">Generate monthly report</h6><small class="notification-text">Chocolate cake oat cake tiramisu marzipan</small>-->
                                <!--        </div><small>-->
                                <!--            <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">Last month</time></small>-->
                                <!--    </div>-->
                                <!--</a>-->
                            </li>
                            <!--<li class="dropdown-menu-footer"><a class="dropdown-item p-1 text-center" href="javascript:void(0)">Read all notifications</a></li>-->
                        </ul>
                    </li>
                    <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                            <div class="user-nav d-sm-flex d-none"><span class="user-name text-bold-600">{{ Auth::user()->formatName() }}</span><span class="user-status">Available</span></div><span><img class="round" src="{{ asset('images/profile/profile-picture/'.Auth::user()->picture) }}" alt="avatar" height="40" width="40" /></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ action('UserController@settings') }}"><i class="feather icon-settings"></i>Account Settings</a>
                            <div class="dropdown-divider"></div><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); 
                      document.getElementById('logout-form').submit();"><i class="feather icon-power"></i> Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<!-- END: Header-->
