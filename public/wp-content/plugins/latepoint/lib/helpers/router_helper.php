<?php 

class OsRouterHelper {

  public static function build_pre_route_link($route, $params = array()){
    return self::build_link($route, array_merge(array('pre_route'=> 1), $params));
  }

  public static function add_extension($string = '', $extension = '.php'){
    if(substr($string, -strlen($extension))===$extension) return $string;
    else return $string.$extension;
  }

  public static function build_link($route, $params = array()){
    $params_query = '';
    if($params){
      $params_query = '&'.http_build_query($params);
    }
    if(is_array($route) && (count($route) == 2)) $route = OsRouterHelper::build_route_name($route[0], $route[1]);
    return admin_url('admin.php?page=latepoint&route_name='.$route.$params_query);
  }

  public static function build_admin_post_link($route, $params = array()){
    $params_query = '';
    if($params){
      $params_query = '&'.http_build_query($params);
    }
    if(is_array($route) && (count($route) == 2)) $route = OsRouterHelper::build_route_name($route[0], $route[1]);
    return admin_url('admin-post.php?action=latepoint_route_call&route_name='.$route.$params_query);
  }

  public static function link_has_route($route_name, $link){
    $link_params = parse_url($link);
    parse_str($link_params['query'], $link_query_params);
    return ($link_query_params && isset($link_query_params['route_name']) && ($link_query_params['route_name'] == $route_name));
  }

  public static function build_front_link($route, $params = array()){
    $params_query = '';
    if($params){
      $params_query = '&'.http_build_query($params);
    }
    if(is_array($route) && (count($route) == 2)) $route = OsRouterHelper::build_route_name($route[0], $route[1]);
    return admin_url('index.php?latepoint_is_custom_route=true&route_name='.$route.$params_query);
  }

  public static function build_route_name($controller, $action){
    return $controller.'__'.$action;
  }

  public static function call_by_route_name($route_name, $return_format = 'html'){
    list($controller_name, $action) = explode('__', $route_name);
    $controller_name = str_replace('_', '', ucwords($controller_name, '_'));
    $controller_class_name = 'Os'.$controller_name.'Controller';
    if(class_exists($controller_class_name)){
      $controller_obj = new $controller_class_name();
      if($return_format) $controller_obj->set_return_format($return_format);
      if(method_exists($controller_obj, $action)){
        $controller_obj->route_name = $route_name;
        $controller_obj->$action();
      }else{
        _e('Page Not Found', 'latepoint');
      }
    }else{
      _e('Page Not Found', 'latepoint');
    }
  }

  public static function get_request_param($name, $default = false){
    if(isset($_GET[$name])){
      $param = stripslashes_deep($_GET[$name]);
    }elseif(isset($_POST[$name])){
      $param = stripslashes_deep($_POST[$name]);
    }else{
    	$param = $default;
    }
    return $param;
  }
}