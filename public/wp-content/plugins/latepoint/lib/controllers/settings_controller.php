<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsSettingsController' ) ) :


  class OsSettingsController extends OsController {



    function __construct(){
      parent::__construct();

      $this->views_folder = LATEPOINT_VIEWS_ABSPATH . 'settings/';
      $this->vars['page_header'] = OsMenuHelper::get_menu_items_by_id('settings');
      $this->vars['breadcrumbs'][] = array('label' => __('Settings', 'latepoint'), 'link' => OsRouterHelper::build_link(OsRouterHelper::build_route_name('settings', 'general') ) );
    }

    public function set_menu_layout_style(){
      $menu_layout_style = (isset($this->params['menu_layout_style']) && in_array($this->params['menu_layout_style'], ['full', 'compact'])) ? $this->params['menu_layout_style'] : 'full';
      OsSettingsHelper::set_menu_layout_style($menu_layout_style);

      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => LATEPOINT_STATUS_SUCCESS, 'message' => ''));
      }
    }

    public function calendar_settings(){

      $this->vars['breadcrumbs'][] = array('label' => __('Calendar Integration', 'latepoint'), 'link' => false );
      $this->format_render(__FUNCTION__);
    }

    public function database_setup(){
      OsDatabaseHelper::run_setup();
      $this->format_render(__FUNCTION__);
    }


    public function pages(){
      $this->vars['breadcrumbs'][] = array('label' => __('Pages Setup', 'latepoint'), 'link' => false );

      $pages = get_pages();

      $this->vars['pages'] = $pages;

      $this->format_render(__FUNCTION__);
    }

    public function payments(){
      $this->vars['breadcrumbs'][] = array('label' => __('Payment Processing', 'latepoint'), 'link' => false );

      $pages = get_pages();

      $this->vars['pages'] = $pages;

      $this->format_render(__FUNCTION__);
    }


    public function work_periods(){

      $this->vars['breadcrumbs'][] = array('label' => __('Work Schedule Settings', 'latepoint'), 'link' => false );

      $this->format_render(__FUNCTION__);
    }


    public function general(){
      
      $this->vars['breadcrumbs'][] = array('label' => __('General', 'latepoint'), 'link' => false );


      $this->format_render(__FUNCTION__);
    }

    public function remove_custom_day_schedule(){
      $target_date_string = $this->params['date'];
      $args = [];
      $args['agent_id'] = isset($this->params['agent_id']) ? $this->params['agent_id'] : 0;
      $args['service_id'] = isset($this->params['service_id']) ? $this->params['service_id'] : 0;
      $args['location_id'] = isset($this->params['location_id']) ? $this->params['location_id'] : 0;
      if(OsUtilHelper::is_date_valid($target_date_string) && OsWorkPeriodsHelper::remove_periods_for_date($target_date_string, $args)){
        $response_html = __('Custom Day Schedule Removed', 'latepoint');
        $status = LATEPOINT_STATUS_SUCCESS;
      }else{
        $response_html = __('Invalid Date', 'latepoint');
        $status = LATEPOINT_STATUS_ERROR;
      }

      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }


    public function steps(){
      $this->vars['breadcrumbs'][] = array('label' => __('Step Settings', 'latepoint'), 'link' => false );

      $step_names = OsStepsHelper::get_step_names_in_order(true);
      $steps = array();
      foreach($step_names as $step_name){
        $steps[] = new OsStepModel($step_name);
      }
      $this->vars['steps'] = $steps;

      $this->format_render(__FUNCTION__);
    }

    public function update_step(){
      $step = new OsStepModel($this->params['step']['name']);
      $step->set_data($this->params['step']);
      if($step->save()){
        $response_html = __('Step Updated: ', 'latepoint') . $step->name;
        $status = LATEPOINT_STATUS_SUCCESS;
      }else{
        $response_html = $step->get_error_messages();
        $status = LATEPOINT_STATUS_ERROR;
      }
      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }

    public function udpate_order_of_steps(){
      foreach($this->params['steps'] as $step_name => $step_order_number){
        $step = new OsStepModel($step_name);
        $step->order_number = $step_order_number;
        if($step->save()){
          $status = LATEPOINT_STATUS_SUCCESS;
        }else{
          $response_html = $step->get_error_messages();
          $status = LATEPOINT_STATUS_ERROR;
          break;
        }
      }
      if($status == LATEPOINT_STATUS_SUCCESS) $response_html = __('Step Ordering Updated ', 'latepoint');
      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }


    public function save_custom_day_schedule(){
      $response_html = __('Work Schedule Updated', 'latepoint');
      $status = LATEPOINT_STATUS_SUCCESS;
      $day_date = new OsWpDateTime($this->params['custom_day_date']);
      $work_periods = $this->params['work_periods'];
      foreach($work_periods as &$work_period){
        $work_period['custom_date'] = $day_date->format('Y-m-d');
        $work_period['week_day'] = $day_date->format('N');
      }
      unset($work_period);

      OsDebugHelper::log($work_periods);
      OsWorkPeriodsHelper::save_work_periods($work_periods);

      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }



    public function custom_day_schedule_form(){
      $target_date_string = isset($this->params['target_date']) ? $this->params['target_date'] : 'now + 1 month';
      $this->vars['date_is_preselected'] = isset($this->params['target_date']);
      $this->vars['target_date'] = new OsWpDateTime($target_date_string);
      $this->vars['day_off'] = isset($this->params['day_off']) ? true : false;
      $this->vars['agent_id'] = isset($this->params['agent_id']) ? $this->params['agent_id'] : 0;
      $this->vars['service_id'] = isset($this->params['service_id']) ? $this->params['service_id'] : 0;
      $this->vars['location_id'] = isset($this->params['location_id']) ? $this->params['location_id'] : 0;

      $this->format_render(__FUNCTION__);
    }



    public function update_work_periods(){
      OsWorkPeriodsHelper::save_work_periods($this->params['work_periods']);
      $response_html = __('Work Schedule Updated', 'latepoint');
      $status = LATEPOINT_STATUS_SUCCESS;

      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }


    public function update(){

      $errors = array();

      if($this->params['settings']){
        foreach($this->params['settings'] as $setting_name => $setting_value){
          $setting = new OsSettingsModel();
          $setting = $setting->load_by_name($setting_name);
          $setting->name = $setting_name;
          $setting->value = OsSettingsHelper::prepare_value($setting_name, $setting_value);
          if($setting->save()){
            $settings_saved = true;
          }else{
            $settings_saved = false;
            $errors[] = $setting->get_error_messages();
          }
        }
      }

      $response_html = __('Settings Updated', 'latepoint');
      $status = LATEPOINT_STATUS_SUCCESS;

      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }


    public function load_work_period_form(){
      $args = ['week_day' => 1, 'agent_id' => 0, 'service_id' => 0, 'location_id' => 0];

      if(isset($this->params['week_day'])) $args['week_day'] = $this->params['week_day'];
      if(isset($this->params['agent_id'])) $args['agent_id'] = $this->params['agent_id'];
      if(isset($this->params['service_id'])) $args['service_id'] = $this->params['service_id'];
      if(isset($this->params['location_id'])) $args['location_id'] = $this->params['location_id'];

      $response_html = OsWorkPeriodsHelper::generate_work_period_form($args);
      $status = LATEPOINT_STATUS_SUCCESS;

      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }



    function generate_demo_data(){
      $log = array('Demo Installation Started');
      $customer_datas = array(
        array('first_name' => 'Brad', 'last_name' => 'Cooper', 'email' => 'tamik+c1@soziev.com', 'phone' => '2342342352'),
        array('first_name' => 'Mandy', 'last_name' => 'Bishops', 'email' => 'tamik+c2@soziev.com', 'phone' => '23523424324'),
        array('first_name' => 'Ken', 'last_name' => 'Brossman', 'email' => 'tamik+c3@soziev.com', 'phone' => '3454353453'),
        array('first_name' => 'Gloria', 'last_name' => 'Piralta', 'email' => 'tamik+c4@soziev.com', 'phone' => '5346345452'),
        array('first_name' => 'Jack', 'last_name' => 'Birdman', 'email' => 'tamik+c5@soziev.com', 'phone' => '5346345452'),
        array('first_name' => 'Julia', 'last_name' => 'Perez', 'email' => 'tamik+c6@soziev.com', 'phone' => '5346345452'),
        array('first_name' => 'Jane', 'last_name' => 'Zimmerman', 'email' => 'tamik+c7@soziev.com', 'phone' => '5346345452'),
        array('first_name' => 'Ben', 'last_name' => 'Stiller', 'email' => 'tamik+c8@soziev.com', 'phone' => '5346345452'),
        array('first_name' => 'Hector', 'last_name' => 'Sanchez', 'email' => 'tamik+c9@soziev.com', 'phone' => '5346345452'),
        array('first_name' => 'Nick', 'last_name' => 'Ramsey', 'email' => 'tamik+c10@soziev.com', 'phone' => '5346345452'),
      );
      $agent_datas = array(
        array('first_name' => 'John', 'last_name' => 'Mayers', 'email' => 'tamik+1@soziev.com', 'phone' => '8923749238'),
        array('first_name' => 'Kim', 'last_name' => 'Collins', 'email' => 'tamik+2@soziev.com', 'phone' => '8972348393'),
        array('first_name' => 'Ben', 'last_name' => 'Stones', 'email' => 'tamik+3@soziev.com', 'phone' => '8263481272'),
        array('first_name' => 'Clark', 'last_name' => 'Simeone', 'email' => 'tamik+4@soziev.com', 'phone' => '8457263473'),
      );

      $location_datas = array(
        array('name' => 'New York', 'full_address' => '408 5th Ave, New York, NY 10018'),
        array('name' => 'Los Angeles', 'full_address' => '420 San Pedro St, Los Angeles, CA 90013'),
      );

      $service_categories_datas = array(
        array('name' => 'General Dentistry'),
        array('name' => 'Cosmetic Dentistry'),
        array('name' => 'Implants Dentistry'),
      );

      $services_datas = array(
        array('name' => 'Tooth Whitening', 'duration' => 30, 'category_id' => 1, 'buffer_before' => 10, 'buffer_after' => 10, 'bg_color' => '#1449ff'),
        array('name' => 'Invisilign Braces', 'duration' => 60, 'category_id' => 1, 'buffer_before' => 20, 'buffer_after' => 20, 'bg_color' => '#8833F9'),
        array('name' => 'Cavity Removal', 'duration' => 120, 'category_id' => 0, 'buffer_before' => 0, 'buffer_after' => 0, 'bg_color' => '#49C47F'),
        array('name' => 'Porcelain Crown', 'duration' => 60, 'category_id' => 2, 'buffer_before' => 10, 'buffer_after' => 10, 'bg_color' => '#E9A019'),
        array('name' => 'Root Canal Therapy', 'duration' => 90, 'category_id' => 0, 'buffer_before' => 0, 'buffer_after' => 0, 'bg_color' => '#F93375'),
        array('name' => 'Gum Decease', 'duration' => 60, 'category_id' => 0, 'buffer_before' => 10, 'buffer_after' => 10, 'bg_color' => '#19CED6'),
      );


      $log[] = 'Creating Locations...';
      foreach($location_datas as $key => $location_data){
        $location = new OsLocationModel();
        $location_exist = $location->where(array('name' => $location_data['name']))->get_results();
        if($location_exist){
          $location_datas[$key]['id'] = $location_exist[0]->id;
          continue;
        }
        $location->set_data($location_data);
        if($location->save()){
          $log[] = __('Location Created. ID:', 'latepoint') . $location->id;
          $location_datas[$key]['id'] = $location->id;
        }
      }
      $log[] = '/ Locations Created';

      $log[] = 'Creating Customers...';
      foreach($customer_datas as $key => $customer_data){
        $customer = new OsCustomerModel();
        $customer_exist = $customer->where(array('email' => $customer_data['email']))->get_results();
        if($customer_exist){
          $customer_datas[$key]['id'] = $customer_exist[0]->id;
          continue;
        }
        $customer->set_data($customer_data);
        if($customer->save()){
          $log[] = __('Customer Created. ID:', 'latepoint') . $customer->id;
          $customer_datas[$key]['id'] = $customer->id;
        }
      }
      $log[] = '/ Customers Created';

      $log[] = 'Creating Agents...';
      foreach($agent_datas as $key => $agent_data){
        $agent = new OsAgentModel();
        $agent_exist = $agent->where(array('email' => $agent_data['email']))->get_results();
        if($agent_exist){
          $agent_datas[$key]['id'] = $agent_exist[0]->id;
          continue;
        }
        $agent->set_data($agent_data);
        if($agent->save()){
          $log[] = __('Agent Created. ID:', 'latepoint') . $agent->id;
          $agent_datas[$key]['id'] = $agent->id;
        }
      }
      $log[] = '/ Agents Created';


      $log[] = 'Creating Service Categories...';
      foreach($service_categories_datas as $key => $service_category_data){
        $service_category = new OsServiceCategoryModel();
        $service_category_exist = $service_category->where(array('name' => $service_category_data['name']))->get_results();
        if($service_category_exist){
          $service_categories_datas[$key]['id'] = $service_category_exist[0]->id;
          continue;
        }
        $service_category->set_data($service_category_data);
        if($service_category->save()){
          $log[] = __('Service Category Created. ID:', 'latepoint') . $service_category->id;
          $service_categories_datas[$key]['id'] = $service_category->id;
        }
      }
      $log[] = '/ Service Categories Created';


      $log[] = 'Creating Services...';
      foreach($services_datas as $key => $service_data){
        $service = new OsServiceModel();
        $service_exist = $service->where(array('name' => $service_data['name']))->get_results();
        if($service_exist){
          $services_datas[$key]['id'] = $service_exist[0]->id;
          continue;
        }
        $service_data['category_id'] = $service_categories_datas[$service_data['category_id']]['id'];
        $service->set_data($service_data);
        if($service->save()){
          $log[] = __('Service Created. ID:', 'latepoint') . $service->id;
          $services_datas[$key]['id'] = $service->id;
        }
      }
      $log[] = 'Services Created';


      $log[] = 'Creating Service/Agent Connections...';
      foreach($location_datas as $location_data){
        foreach($services_datas as $service_data){
          foreach($agent_datas as $agent_data){
            $service = new OsServiceModel($service_data['id']);
            $service->connect_to_agent($agent_data['id'], $location_data['id']);
          }
        }
      }
      $log[] = '/ Service/Agent Connections Created';

      $log[] = 'Generating Appointments';


      $target_date = new OsWpDateTime('today');
      $calendar_start = clone $target_date;
      $calendar_start->modify('first day of previous month');
      $calendar_end = clone $target_date;
      $calendar_end->modify('last day of next month');

      $timeblock_interval = OsSettingsHelper::get_default_timeblock_interval();

      for($day_date=clone $calendar_start; $day_date<$calendar_end; $day_date->modify('+1 day')){
        foreach($agent_datas as $agent_data){
          for($number_of_bookings = 0; $number_of_bookings <= rand(0, 10); $number_of_bookings++){
            $service_data = $services_datas[rand(0, count($services_datas) - 1)];
            $location_data = $location_datas[rand(0, count($location_datas) - 1)];
            $booking_model = new OsBookingModel;
            $work_periods_arr = OsBookingHelper::get_work_periods(['custom_date' => $day_date->format('Y-m-d'), 'service_id' => $service_data['id'], 'agent_id' => $agent_data['id'], 'location_id' => $location_data['id']]);
            if(!$work_periods_arr) continue;

            $booked_periods_arr = OsBookingHelper::get_bookings_times_for_date($day_date->format('Y-m-d'), $agent_data['id'], $location_data['id']);
            echo '<div style="padding: 20px 40px; background: #fff; margin: 20px;">';
            // echo '<h3>Information</h3>';
            // echo 'Date: <strong>'. $day_date->format('Y-m-d').'</strong>';
            // echo ' | Agent ID:  <strong>' . $agent_data['id'].'</strong>';
            // echo ' | Service ID:  <strong>' . $service_data['id'].'</strong>';
            // echo ' | Service Duration:  <strong>' . $service_data['buffer_before']. ':' . $service_data['duration']. ':' . $service_data['buffer_after'].'</strong>';

            // echo '<h3>Bookings</h3>';
            // echo '<pre>';
            // print_r($booked_periods_arr);
            // echo '</pre>';

            $available_minutes = array();
            foreach($work_periods_arr as $work_period){
              list($work_start_minutes, $work_end_minutes) = explode(':', $work_period);
              for($minutes = $work_start_minutes; $minutes <= ($work_end_minutes - $service_data['duration']); $minutes+= $timeblock_interval){
                $is_available = true;
                $minutes_start = $minutes - $service_data['buffer_before'];
                $minutes_end = $minutes + $service_data['duration'] + $service_data['buffer_after'];
                if(!OsBookingHelper::is_timeframe_booked($minutes_start, $minutes_end, $booked_periods_arr, $service_data['id'])){
                  $available_minutes[] = $minutes;
                }
              }
            }
            // echo '<pre>';
            // echo '<h3>Available Slots</h3>';
            // print_r($available_minutes);
            // echo '</pre>';

            if(count($available_minutes)){
              $booking = new OsBookingModel();
              $booking->location_id = $location_data['id'];
              $booking->service_id = $service_data['id'];
              $booking->customer_id = $customer_datas[rand(0, count($customer_datas) - 1)]['id'];
              $booking->agent_id = $agent_data['id'];
              $booking->buffer_before = $service_data['buffer_before'];
              $booking->buffer_after = $service_data['buffer_after'];
              $booking->status = 'approved';
              $booking->start_date = $day_date->format('Y-m-d');
              $booking->start_time = $available_minutes[rand(0, count($available_minutes) - 1)];
              $booking->end_time = $booking->start_time + $service_data['duration'];
              $booking->save();
              echo 'Booked: Date: <strong>'.$booking->start_date.'</strong>, Start Time: <strong>'.$booking->start_time.'</strong>, End Time: <strong>'.$booking->end_time.'</strong>';
            }

            echo '</div>';


          }
        }
      }


      $log[] = '/ Finished Generating Appointments';



      echo '<pre>';
      print_r($log);
      echo '</pre>';
    }

  }


endif;