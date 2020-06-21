<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsActivitiesController' ) ) :


  class OsActivitiesController extends OsController {



    function __construct(){
      parent::__construct();
      
      $this->views_folder = LATEPOINT_VIEWS_ABSPATH . 'activities/';
      $this->vars['page_header'] = __('Activities', 'latepoint');
      $this->vars['breadcrumbs'][] = array('label' => __('Activities', 'latepoint'), 'link' => OsRouterHelper::build_link(OsRouterHelper::build_route_name('activities', 'index') ) );
    }

    public function login_form(){
      $this->format_render(__FUNCTION__);
    }

    /*
      Index of activities
    */

    public function index(){
      $this->vars['breadcrumbs'][] = array('label' => __('Index', 'latepoint'), 'link' => false );
      $activities = new OsActivityModel();
      $this->vars['activities'] = $activities->order_by('created_at desc')->set_limit(50)->get_results_as_models();
      
      $this->format_render(__FUNCTION__);
    }
  }


endif;