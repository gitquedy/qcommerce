{{-- For submenu --}}
<ul class="menu-content">
    @foreach($menu as $submenu)
    @if(in_array($request->user()->role, explode(',',isset($submenu->access) ? $submenu->access : '')) || isset($submenu->access) == false)
            <?php
                $submenuTranslation = "";
                if(isset($menu->i18n)){
                    $submenuTranslation = $menu->i18n;
                }
            ?>
            <li class="{{ request()->getPathInfo() == $submenu->url ? 'active' : '' }}">
                <a href="{{ $submenu->url }}">
                    <i class="{{ isset($submenu->icon) ? $submenu->icon : "" }}"></i>
                    <span class="menu-title" data-i18n="{{ $submenuTranslation }}">{{ $submenu->name }}</span>
                </a>
                @if (isset($submenu->submenu))
                    @include('panels/submenu', ['menu' => $submenu->submenu])
                @endif
            </li>
        @endif
    @endforeach
</ul>

