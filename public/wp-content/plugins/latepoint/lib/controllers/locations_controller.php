<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsLocationsController' ) ) :


  class OsLocationsController extends OsController {



    function __construct(){
      parent::__construct();

      $this->views_folder = LATEPOINT_VIEWS_ABSPATH . 'locations/';
      $this->vars['page_header'] = __('Locations', 'latepoint');
      $this->vars['breadcrumbs'][] = array('label' => __('Locations', 'latepoint'), 'link' => OsRouterHelper::build_link(OsRouterHelper::build_route_name('locations', 'index') ) );
    }

    public function set_selected_location(){
      $location_id = $this->params['id'];
      OsLocationHelper::set_selected_location($location_id);
    }


    /*
      Edit location
    */

    public function edit_form(){
      $location_id = $this->params['id'];

      $this->vars['page_header'] = __('Edit Location', 'latepoint');
      $this->vars['breadcrumbs'][] = array('label' => __('Edit Location', 'latepoint'), 'link' => false );

      $location = new OsLocationModel($location_id);
      $agents = new OsAgentModel();
      $services = new OsServiceModel();


      $this->vars['location'] = $location;
      $this->vars['agents'] = $agents->get_results_as_models();
      $this->vars['services'] = $services->get_results_as_models();

      $custom_work_periods = OsWorkPeriodsHelper::load_work_periods(array('location_id' => $location_id, 'flexible_search' => false));
      $this->vars['custom_work_periods'] = $custom_work_periods;
      $this->vars['is_custom_schedule'] = ($custom_work_periods && (count($custom_work_periods) > 0));

      $this->format_render(__FUNCTION__);
    }


    /*
      New location form
    */

    public function new_form(){
      $this->vars['page_header'] = __('Create New Location', 'latepoint');
      $this->vars['breadcrumbs'][] = array('label' => __('Create New Location', 'latepoint'), 'link' => false );

      $location = new OsLocationModel();
      $agents = new OsAgentModel();
      $services = new OsServiceModel();

      $this->vars['location'] = $location;
      $this->vars['agents'] = $agents->get_results_as_models();
      $this->vars['services'] = $services->get_results_as_models();


      $this->vars['custom_work_periods'] = false;
      $this->vars['is_custom_schedule'] = false;
      
      $this->format_render(__FUNCTION__);
    }





    /*
      Index of locations
    */

    public function index(){

      $this->format_render(__FUNCTION__);
    }




    /*
      Create location
    */

    public function create(){
      $this->update();
    }


    /*
      Update location
    */

    public function update(){
      $new_record = (isset($this->params['location']['id']) && $this->params['location']['id']) ? false : true;
      $location = new OsLocationModel();
      $location->set_data($this->params['location']);
      $extra_response_vars = array();

      if($location->save() && $location->save_agents_and_services($this->params['location']['agents'])){
        if($new_record){
          $response_html = __('Location Created. ID:', 'latepoint') . $location->id;
          OsActivitiesHelper::create_activity(array('code' => 'location_create', 'location_id' => $location->id));
        }else{
          $response_html = __('Location Updated. ID:', 'latepoint') . $location->id;
          OsActivitiesHelper::create_activity(array('code' => 'location_update', 'location_id' => $location->id));
        }
        $status = LATEPOINT_STATUS_SUCCESS;
        // save schedules
        if($this->params['is_custom_schedule'] == 'on'){
          $location->save_custom_schedule($this->params['work_periods']);
        }elseif($this->params['is_custom_schedule'] == 'off'){
          $location->delete_custom_schedule();
        }
        $extra_response_vars['record_id'] = $location->id;
      }else{
        $response_html = $location->get_error_messages();
        $status = LATEPOINT_STATUS_ERROR;
      }
      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html) + $extra_response_vars);
      }
    }



    /*
      Delete location
    */

    public function destroy(){
      if(filter_var($this->params['id'], FILTER_VALIDATE_INT)){
        $location = new OsLocationModel($this->params['id']);
        if($location->delete()){
          $status = LATEPOINT_STATUS_SUCCESS;
          $response_html = __('Location Removed', 'latepoint');
        }else{
          $status = LATEPOINT_STATUS_ERROR;
          $response_html = __('Error Removing Location', 'latepoint');
        }
      }else{
        $status = LATEPOINT_STATUS_ERROR;
        $response_html = __('Error Removing Location', 'latepoint');
      }

      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }

  }


endif;