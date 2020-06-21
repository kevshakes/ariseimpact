<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsUpdatesController' ) ) :


  class OsUpdatesController extends OsController {



    function __construct(){
      parent::__construct();

      $this->views_folder = LATEPOINT_VIEWS_ABSPATH . 'updates/';
      $this->vars['page_header'] = OsMenuHelper::get_menu_items_by_id('settings');
    }

    function status(){

      $this->vars['license'] = OsLicenseHelper::get_license_info();

      $this->format_render(__FUNCTION__);
    }



    public function save_license_information(){
      $license_data = $this->params['license'];

      $verify_license_key_result = OsLicenseHelper::verify_license_key($license_data);
      
      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $verify_license_key_result['status'], 'message' => $verify_license_key_result['message']));
      }
    }






    function check_version_status(){

      // connect
      $vars = array(
        '_nonce'            => wp_create_nonce('activate_licence'),
        'version'           => LATEPOINT_VERSION, 
        'domain'            => $_SERVER['SERVER_NAME'],
        'user_ip'           => OsUtilHelper::get_user_ip(),
      );

      $url = LATEPOINT_SERVER."/wp/latest-version-info.json";

      $args = array(
        'timeout' => 15,
        'headers' => array(),
        'body' => $vars,
        'sslverify ' => true
      );
     
      $request = wp_remote_get( $url,array('body' => $vars, 'sslverify ' => false));
      
      if( !is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200){
        $this->vars['version_info'] = json_decode($request['body'], true);
        $response_html = $this->render($this->get_view_uri('check_version_status'), 'none');
        $status = LATEPOINT_STATUS_SUCCESS;
      }else{
        $response_html = 'Error! 8346HS73';
        $status = LATEPOINT_STATUS_ERROR;
      }

      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }

    function get_updates_log(){

      // connect
      $vars = array(
        '_nonce'            => wp_create_nonce('activate_licence'),
        'version'           => LATEPOINT_VERSION, 
        'domain'            => $_SERVER['SERVER_NAME'],
        'user_ip'           => OsUtilHelper::get_user_ip(),
      );

      $url = LATEPOINT_SERVER."/wp/get-changelog";

      $args = array(
        'timeout' => 15,
        'headers' => array(),
        'body' => $vars,
        'sslverify ' => true
      );
     
      $request = wp_remote_get( $url,array('body' => $vars, 'sslverify ' => false));
      
      if( !is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200){ 
        $response_html = $request['body'];
        $status = LATEPOINT_STATUS_SUCCESS;
      }else{
        $response_html = 'Error! 8346HS73';
        $status = LATEPOINT_STATUS_ERROR;
      }

      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }

    }





	}



endif;