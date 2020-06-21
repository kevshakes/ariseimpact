<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsDebugController' ) ) :


  class OsDebugController extends OsController {



    function __construct(){
      parent::__construct();

      $this->views_folder = LATEPOINT_VIEWS_ABSPATH . 'debug/';
      $this->vars['page_header'] = __('LatePoint Status', 'latepoint');
    }

    function status(){
      
      $this->format_render(__FUNCTION__);
    }

	}



endif;