<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsSearchController' ) ) :


  class OsSearchController extends OsController {

    private $booking;

    function __construct(){
      parent::__construct();
      $this->views_folder = LATEPOINT_VIEWS_ABSPATH . 'search/';
    }

    function query_results(){
      $query = trim($this->params['query']);
      if(!$query) return;
    	$sql_query = '%'.$query.'%';

      $customers = new OsCustomerModel();
      if($this->logged_in_agent_id){
        $customers->build_query_customers_for_agent($this->logged_in_agent_id);
      }
      $customers->where(array('OR' => array('CONCAT (first_name, " ", last_name) LIKE ' => $sql_query, 'email LIKE' => $sql_query, 'phone LIKE' => $sql_query)))->set_limit(6);
      
      $customers = $customers->get_results_as_models();
      $this->vars['customers'] = $customers;
      $this->vars['query'] = $query;

      if($this->logged_in_admin_user_id){
        $services = new OsServiceModel();
        $agents = new OsAgentModel();
        $services = $services->where(array('name LIKE ' => $sql_query))->set_limit(6)->get_results_as_models();
        $agents = $agents->where(array('OR' => array('CONCAT (first_name, " ", last_name) LIKE ' => $sql_query, 'email LIKE' => $sql_query, 'phone LIKE' => $sql_query)))->set_limit(6)->get_results_as_models();

        $this->vars['services'] = $services;
        $this->vars['agents'] = $agents;
      }


      $this->format_render(__FUNCTION__);
    }

  }
endif;