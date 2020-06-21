<?php 

class OsParamsHelper {

	private static $params = [];

	public static function load_params(){
    $params = array();
    $post_params = array();
    $get_params = array();
    if(isset($_POST['params'])){
      if(is_string($_POST['params'])){
        parse_str($_POST['params'], $post_params);
      }
      if(is_array($_POST['params'])){
        $post_params = array_merge($_POST['params'], $post_params);
      }
    }
    $get_params = $_GET;
    $params = array_merge($post_params, $get_params);
    $params = stripslashes_deep($params);
    self::$params = $params;
	}

  public static function get_params(){
  	if(empty(self::$params)) self::load_params();
    return self::$params;
  }

  public static function get_param($param_name){
  	if(empty(self::$params)) self::load_params();
  	return self::$params[$param_name];
  }
}