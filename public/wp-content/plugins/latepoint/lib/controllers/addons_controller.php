<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsAddonsController' ) ) :


  class OsAddonsController extends OsController {



    function __construct(){
      parent::__construct();

      $this->views_folder = LATEPOINT_VIEWS_ABSPATH . 'addons/';
      $this->vars['page_header'] = OsMenuHelper::get_menu_items_by_id('settings');
    }

    function install_addon(){
      if(!isset($this->params['addon_name']) || empty($this->params['addon_name'])) return;

      $addon_name = $this->params['addon_name'];

      $license = OsLicenseHelper::get_license_info();

      if(OsLicenseHelper::is_license_active()){
        $addon_info = OsAddonsHelper::get_addon_download_info($addon_name);
        $result = OsAddonsHelper::install_addon($addon_info);
        if(is_wp_error( $result )){
          $status = LATEPOINT_STATUS_ERROR;
          $response_html = $result->get_error_message();
          $code = '500';
        }else{
          $status = LATEPOINT_STATUS_SUCCESS;
          $code = '200';
          $response_html = __('Addon installed successfully.', 'latepoint');
        }
      }else{
        $this->vars['license'] = $license;
        $status = LATEPOINT_STATUS_ERROR;
        $response_html = $this->render(LATEPOINT_VIEWS_ABSPATH.'updates/_license_form', 'none');
        $code = '404';
      }

      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'code' => $code, 'message' => $response_html));
      }

    }


    function index(){

      $this->format_render(__FUNCTION__);
    }

    function load_addons_list(){

      // connect
      $vars = array(
        '_nonce'            => wp_create_nonce('activate_licence'),
        'version'           => LATEPOINT_VERSION, 
        'domain'            => $_SERVER['SERVER_NAME'],
        'marketplace'       => LATEPOINT_MARKETPLACE,
        'license_key'       => OsLicenseHelper::get_license_key(),
        'user_ip'           => OsUtilHelper::get_user_ip(),
      );

      $url = LATEPOINT_SERVER."/wp/addons/load_addons_list";

      $args = array(
        'timeout' => 15,
        'headers' => array(),
        'body' => $vars,
        'sslverify ' => true
      );
     
      $request = wp_remote_get( $url,array('body' => $vars, 'sslverify ' => false));
      
      $addons = false;
      if( !is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200){ 
        $this->vars['addons'] = json_decode($request['body']);
      }
      $this->format_render(__FUNCTION__);
    }
	}



endif;