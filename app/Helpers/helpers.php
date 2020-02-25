<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;
use Config;
use App\Order;
use App\Shop;
use App\Products;
use Illuminate\Support\Str;

class Helper
{
    public static function applClasses()
    {
        $data = config('custom.custom');
        
        $layoutClasses = [
            'theme' => $data['theme'],
            'sidebarCollapsed' => $data['sidebarCollapsed'],
            'navbarColor' => $data['navbarColor'],
            'menuType' => $data['menuType'],
            'navbarType' => $data['navbarType'],
            'footerType' => $data['footerType'],
            'sidebarClass' => 'menu-expanded',
            'bodyClass' => $data['bodyClass'],
            'pageHeader' => $data['pageHeader'],
            'blankPage' => $data['blankPage'],
            'blankPageClass' => '',
            'contentLayout' => $data['contentLayout'],
            'sidebarPositionClass' => '',
            'contentsidebarClass' => '',
            'mainLayoutType' => $data['mainLayoutType'],
         ];



        //Theme
        if($layoutClasses['theme'] == 'dark')
            $layoutClasses['theme'] = "dark-layout";
        elseif($layoutClasses['theme'] == 'semi-dark')
            $layoutClasses['theme'] = "semi-dark-layout";
        else
            $layoutClasses['theme'] = "light";

        //menu Type
        switch($layoutClasses['menuType']){
          case "static":
              $layoutClasses['menuType'] = "menu-static";
              break;
          default:
              $layoutClasses['menuType'] = "menu-fixed";
      }


        //navbar
        switch($layoutClasses['navbarType']){
          case "static":
              $layoutClasses['navbarType'] = "navbar-static";
              $layoutClasses['navbarClass'] = "navbar-static-top";
              break;
          case "sticky":
              $layoutClasses['navbarType'] = "navbar-sticky";
              $layoutClasses['navbarClass'] = "fixed-top";
              break;
          case "hidden":
              $layoutClasses['navbarType'] = "navbar-hidden";
              break;
          default:
              $layoutClasses['navbarType'] = "navbar-floating";
              $layoutClasses['navbarClass'] = "floating-nav";
      }

        // sidebar Collapsed
        $layoutClasses['sidebarClass'] = "menu-collapsed";
        if($layoutClasses['sidebarCollapsed'] == 'false')
            $layoutClasses['sidebarClass'] = "";

        // sidebar Collapsed
        if($layoutClasses['blankPage'] == 'true')
            $layoutClasses['blankPageClass'] = "blank-page";

        //footer
        switch($layoutClasses['footerType']){
            case "sticky":
                $layoutClasses['footerType'] = "fixed-footer";
                break;
            case "hidden":
                $layoutClasses['footerType'] = "footer-hidden";
                break;
            default:
                $layoutClasses['footerType'] = "footer-static";
        }

        //Cotntent Sidebar
        switch($layoutClasses['contentLayout']){
            case "content-left-sidebar":
                $layoutClasses['sidebarPositionClass'] = "sidebar-left";
                $layoutClasses['contentsidebarClass'] = "content-right";
                break;
            case "content-right-sidebar":
                $layoutClasses['sidebarPositionClass'] = "sidebar-right";
                $layoutClasses['contentsidebarClass'] = "content-left";
                break;
            case "content-detached-left-sidebar":
                $layoutClasses['sidebarPositionClass'] = "sidebar-detached sidebar-left";
                $layoutClasses['contentsidebarClass'] = "content-detached content-right";
                break;
            case "content-detached-right-sidebar":
                $layoutClasses['sidebarPositionClass'] = "sidebar-detached sidebar-right";
                $layoutClasses['contentsidebarClass'] = "content-detached content-left";
                break;
            default:
                $layoutClasses['sidebarPositionClass'] = "";
                $layoutClasses['contentsidebarClass'] = "";
        }

        return $layoutClasses;
    }

    public static function updatePageConfig($pageConfigs){
        $demo = 'custom';
        if(isset($pageConfigs)){
            if(count($pageConfigs) > 0){
                foreach ($pageConfigs as $config => $val){
                    Config::set('custom.'.$demo.'.'.$config, $val);
                }
            }
        }
    }
    
    
    public static function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
        $dates = array();
        $first = (string) $first;
        $last = (string) $last;
        $current = strtotime( $first );
        $last = strtotime( $last );
    
        while( $current <= $last ) {
    
            $dates[] = date( $format, $current );
            $current = strtotime( $step, $current );
        }
    
        return $dates;
    }
    
    
    public static function get_colours( ) {
        
        $colour = array();
        
        $colour[] = "7367F0";
        $colour[] = "28C76F";

        $colour[] = "FF9F43";
        $colour[] = "00cfe8";
        $colour[] = "EA5455";
        
        for($x = 0; $x <= 100; $x++) {
            
            $color = substr(md5(rand()), 0, 6);
            $colour[] = $color;
            
        }
        
    
        return $colour;
    }
    
    public static function minifier($buffer) {

    $search = array(
        '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
        '/[^\S ]+\</s',     // strip whitespaces before tags, except space
        '/(\s)+/s',         // shorten multiple whitespace sequences
        '/<!--(.|\s)*?-->/' // Remove HTML comments
    );

    $replace = array(
        '>',
        '<',
        '\\1',
        ''
    );

    $buffer = preg_replace($search, $replace, $buffer);

    return $buffer;
    }
    
    
    
    public static function get_sidebar_data() {

        
        $shops = Shop::get_auth_shops();
        
        $shop_array = array();
        
        foreach($shops as $shopsVAL){
            $shop_array[] = $shopsVAL->id;
        }
        
        $order_all = Order::whereIn('shop_id',$shop_array)->get()->count();
        $order_pending = Order::whereIn('shop_id',$shop_array)->where('status','=','pending')->get()->count();
        $order_printing = Order::whereIn('shop_id',$shop_array)->where('printed','=','0')->get()->count();
        $order_ready = Order::whereIn('shop_id',$shop_array)->where('status','=','ready_to_ship')->get()->count();
        $order_shipped = Order::whereIn('shop_id',$shop_array)->where('status','=','shipped')->get()->count();
        $order_delivered = Order::whereIn('shop_id',$shop_array)->whereIn('status',['delivered', 'COMPLETED'])->get()->count();
        
        $result['order_all'] = $order_all;
        $result['order_pending'] = $order_pending;
        $result['order_printing'] = $order_printing;
        $result['order_ready'] = $order_ready;
        $result['order_shipped'] = $order_shipped;
        $result['order_delivered'] = $order_delivered;
    
        return $result;
        
        
        
        
    }

    
    
    
    
    
    
}
