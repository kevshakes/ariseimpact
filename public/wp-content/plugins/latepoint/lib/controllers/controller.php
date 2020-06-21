<?php
class OsController {

  protected $params,
  $layout = 'admin',
  $views_folder = LATEPOINT_VIEWS_ABSPATH_SHARED,
  $return_format = 'html',
  $extra_css_classes = array('latepoint');

  public $vars;

  public $route_name;

  function generate_css_class($view_name){
    $class_name_filtered = strtolower(preg_replace('/^Os(\w+)Controller/i', '$1', static::class));
    return "latepoint-view-{$class_name_filtered}-{$view_name}";
  }

  function __construct(){
    $this->params = $this->get_params();
    $this->set_layout($this->layout);
    $this->vars['page_header'] = __('Bookings', 'latepoint');
    $this->vars['breadcrumbs'][] = array('label' => __('Dashboard', 'latepoint'), 'link' => OsRouterHelper::build_link(OsRouterHelper::build_route_name('dashboard', 'index') ));

    $this->load_settings();
    $this->get_logged_in_user_info();
  }

  protected function load_settings(){
    $this->time_system = OsTimeHelper::get_time_system();
  }


  public function access_not_allowed(){
    $this->format_render(__FUNCTION__, [], [], true);
    exit();
  }

  protected function get_logged_in_user_info(){
    $this->logged_in_admin_user_type = false;
    $this->logged_in_admin_user = false;
    $this->logged_in_admin_user_id = false;
    $this->logged_in_agent = false;
    $this->logged_in_agent_id = false;
    $this->logged_in_admin_user_avatar_url = false;

    // WP Admin users & WP Users connected to agent
    if(OsWpUserHelper::is_user_logged_in()){
      if(OsAuthHelper::is_admin_logged_in()){
        $this->logged_in_admin_user_type = LATEPOINT_WP_ADMIN_ROLE;
        $this->logged_in_admin_user = OsAuthHelper::get_logged_in_admin_user();
        $this->logged_in_admin_user_id = OsAuthHelper::get_logged_in_admin_user_id();
        $this->logged_in_admin_user_avatar_url = get_avatar_url($this->logged_in_admin_user->user_email);
      }elseif(OsAuthHelper::is_agent_logged_in()){
        $agent = OsAuthHelper::get_logged_in_agent();
        if($agent){
          $this->logged_in_agent = $agent;
          $this->logged_in_agent_id = $agent->id;
          $this->logged_in_admin_user_type = LATEPOINT_WP_AGENT_ROLE;
          $this->logged_in_admin_user_avatar_url = $this->logged_in_agent->avatar_url;
        }
      }
    }
    $this->vars['logged_in_admin_user_avatar_url'] = $this->logged_in_admin_user_avatar_url;
    $this->vars['logged_in_admin_user_type'] = $this->logged_in_admin_user_type;
    $this->vars['logged_in_admin_user'] = $this->logged_in_admin_user;
    $this->vars['logged_in_admin_user_id'] = $this->logged_in_admin_user_id;
    $this->vars['logged_in_agent'] = $this->logged_in_agent;
    $this->vars['logged_in_agent_id'] = $this->logged_in_agent_id;
  }

  protected function get_time_system(){
    return $this->time_system;
  }

  function format_render($view_name, $extra_vars = array(), $json_return_vars = array(), $from_shared_folder = false){
    echo $this->format_render_return($view_name, $extra_vars, $json_return_vars, $from_shared_folder);
  }

  // You can pass array to $view_name, ['json_view_name' => ..., 'html_view_name' => ...]
  function format_render_return($view_name, $extra_vars = array(), $json_return_vars = array(), $from_shared_folder = false){
    $html = '';
    if($this->get_return_format() == 'json'){
      if(is_array($view_name)) $view_name = $view_name['json_view_name'];
      $response_html = $this->render($this->get_view_uri($view_name, $from_shared_folder), 'none', $extra_vars);
      $this->send_json(array_merge(array('status' => LATEPOINT_STATUS_SUCCESS, 'message' => $response_html), $json_return_vars));
    }else{
      if(is_array($view_name)) $view_name = $view_name['html_view_name'];
      $this->extra_css_classes[] = $this->generate_css_class($view_name);
      $this->vars['extra_css_classes'] = $this->extra_css_classes;
      $html = $this->render($this->get_view_uri($view_name, $from_shared_folder), $this->get_layout(), $extra_vars);
    }
    return $html;
  }

  function set_layout($layout = 'admin'){
    if(isset($this->params['layout'])){
      $this->layout = $this->params['layout'];
    }else{
      $this->layout = $layout;
    }
  }

  function get_layout(){
    return $this->layout;
  }

  function set_return_format($format = 'html'){
    $this->return_format = $format;
  }

  function get_return_format(){
    return $this->return_format;
  }

  protected function send_json($data, $status_code = null){
    wp_send_json($data, $status_code);
  }

  function get_view_uri($view_name, $from_shared_folder = false){
    if($from_shared_folder){
      $view_uri = LATEPOINT_VIEWS_ABSPATH_SHARED.$view_name.'.php';
    }else{
      $view_uri = $this->views_folder.$view_name.'.php';
    }
    return $view_uri;
  }

  // render view and if needed layout, when layout is rendered - view variable is passed to a layout file
  function render($view, $layout = 'none', $extra_vars = array()){
    $this->vars['route_name'] = $this->route_name;
    extract($extra_vars);
    extract($this->vars);
    ob_start();
    if($layout != 'none'){
      // rendering layout, view variable will be passed and used in layout file
      include LATEPOINT_VIEWS_LAYOUTS_ABSPATH . $this->add_extension($layout, '.php');
    }else{
      include $this->add_extension($view, '.php');
    }
    $response_html = ob_get_clean();
    return $response_html;
  }

  /*
    Adds extension to a file string if its missing
  */
  function add_extension($string = '', $extension = '.php'){
    if(substr($string, -strlen($extension))===$extension) return $string;
    else return $string.$extension;
  }

  function get_params(){
    return OsParamsHelper::get_params();
  }
}