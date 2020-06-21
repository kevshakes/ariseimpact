<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsAgentsController' ) ) :


  class OsAgentsController extends OsController {



    function __construct(){
      parent::__construct();
      
      $this->views_folder = LATEPOINT_VIEWS_ABSPATH . 'agents/';
      $this->vars['page_header'] = __('Agents', 'latepoint');
      $this->vars['breadcrumbs'][] = array('label' => __('Agents', 'latepoint'), 'link' => OsRouterHelper::build_link(OsRouterHelper::build_route_name('agents', 'index') ) );
    }

    public function login_form(){
      $this->format_render(__FUNCTION__);
    }


    /*
      Index of agents
    */

    public function index(){
      $this->vars['breadcrumbs'][] = array('label' => __('Index', 'latepoint'), 'link' => false );
      $agents = new OsAgentModel();
      if($this->logged_in_agent_id) $agents->where(['id' => $this->logged_in_agent_id]);

      $this->vars['agents'] = $agents->get_results_as_models();
      
      $this->format_render(__FUNCTION__);
    }



    /*
      New agent form
    */

    public function new_form(){
      if($this->logged_in_agent_id){
        $this->access_not_allowed();
        return;
      }

      $this->vars['page_header'] = __('Create New Agent', 'latepoint');
      $this->vars['breadcrumbs'][] = array('label' => __('Create New Agent', 'latepoint'), 'link' => false );

      $this->vars['agent'] = new OsAgentModel();
      $this->vars['wp_users_for_select'] = OsWpUserHelper::get_wp_users_for_select(['role' => LATEPOINT_WP_AGENT_ROLE]);

      $this->vars['custom_work_periods'] = false;
      $this->vars['is_custom_schedule'] = false;

      $services = new OsServiceModel();
      $this->vars['services'] = $services->get_results_as_models();

      $locations = new OsLocationModel();
      $this->vars['locations'] = $locations->get_results_as_models();
      
      $this->vars['show_admin_fields'] = !$this->logged_in_agent_id;

      $this->format_render(__FUNCTION__);
    }

    /*
      Edit agent
    */

    public function edit_form(){
      $this->vars['page_header'] = __('Edit Agent', 'latepoint');
      $this->vars['breadcrumbs'][] = array('label' => __('Edit Agent', 'latepoint'), 'link' => false );

      if($this->logged_in_agent_id && ($this->params['id'] != $this->logged_in_agent_id)){
        $this->access_not_allowed();
        return;
      }
      $this->vars['show_admin_fields'] = !$this->logged_in_agent_id;

      $agent_id = $this->params['id'];

      $agent = new OsAgentModel($agent_id);

      if($agent->id){

        $this->vars['agent'] = $agent;
        $this->vars['wp_users_for_select'] = OsWpUserHelper::get_wp_users_for_select(['role' => LATEPOINT_WP_AGENT_ROLE]);

        $custom_work_periods = OsWorkPeriodsHelper::load_work_periods(array('agent_id' => $agent_id, 'flexible_search' => false));
        $this->vars['custom_work_periods'] = $custom_work_periods;
        $this->vars['is_custom_schedule'] = ($custom_work_periods && (count($custom_work_periods) > 0));
        $services = new OsServiceModel();
        $this->vars['services'] = $services->get_results_as_models();
        $locations = new OsLocationModel();
        $this->vars['locations'] = $locations->get_results_as_models();
      }

      $this->format_render(__FUNCTION__);
    }



    /*
      Create agent
    */

    public function create(){
      $this->update();
    }


    /*
      Update agent
    */

    public function update(){
      $new_record = (isset($this->params['agent']['id']) && $this->params['agent']['id']) ? false : true;
      $agent = new OsAgentModel();
      if(isset($this->params['agent']['password'])) $this->params['agent']['password'] = wp_hash_password($this->params['password']);
      $agent->set_data($this->params['agent']);
      $agent->set_features($this->params['agent']['features']);
      $extra_response_vars = array();

      if($agent->save() && $agent->save_locations_and_services($this->params['agent']['locations'])){
        if($new_record){
          $response_html = __('Agent Created. ID:', 'latepoint') . $agent->id;
          OsActivitiesHelper::create_activity(array('code' => 'agent_create', 'agent_id' => $agent->id));
        }else{
          $response_html = __('Agent Updated. ID:', 'latepoint') . $agent->id;
          OsActivitiesHelper::create_activity(array('code' => 'agent_update', 'agent_id' => $agent->id));
        }
        $status = LATEPOINT_STATUS_SUCCESS;
        // save schedules
        if($this->params['is_custom_schedule'] == 'on'){
          $agent->save_custom_schedule($this->params['work_periods']);
        }elseif($this->params['is_custom_schedule'] == 'off'){
          $agent->delete_custom_schedule();
        }
        $extra_response_vars['record_id'] = $agent->id;
      }else{
        $response_html = $agent->get_error_messages();
        $status = LATEPOINT_STATUS_ERROR;
      }
      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html) + $extra_response_vars);
      }
    }


    public function destroy(){
      if(filter_var($this->params['id'], FILTER_VALIDATE_INT)){
        $agent = new OsAgentModel($this->params['id']);
        if($agent->delete()){
          $status = LATEPOINT_STATUS_SUCCESS;
          $response_html = __('Agent Removed', 'latepoint');
        }else{
          $status = LATEPOINT_STATUS_ERROR;
          $response_html = __('Error Removing Agent', 'latepoint');
        }
      }else{
        $status = LATEPOINT_STATUS_ERROR;
        $response_html = __('Error Removing Agent', 'latepoint');
      }

      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }

  }


endif;