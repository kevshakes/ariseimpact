<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsCalendarsController' ) ) :


  class OsCalendarsController extends OsController {

    private $booking;

    function __construct(){
      parent::__construct();

      $this->views_folder = LATEPOINT_VIEWS_ABSPATH . 'calendars/';
      $this->vars['page_header'] = [['label' => __( 'Daily View', 'latepoint' ), 'icon' => '', 'link' => OsRouterHelper::build_link(['calendars', 'daily_agents'])],
                                    ['label' => __( 'Weekly View', 'latepoint' ), 'icon' => '', 'link' => OsRouterHelper::build_link(['calendars', 'weekly_agent'])],
                                    ['label' => __( 'Monthly View', 'latepoint' ), 'icon' => '', 'link' => OsRouterHelper::build_link(['calendars', 'monthly_agents'])]];
      if(OsAuthHelper::is_agent_logged_in()) $this->vars['page_header'][0]['link'] = OsRouterHelper::build_link(['calendars', 'daily_agent']);
      $this->vars['breadcrumbs'][] = array('label' => __('Appointments', 'latepoint'), 'link' => OsRouterHelper::build_link(OsRouterHelper::build_route_name('calendars', 'pending_approval') ) );
    }


    public function load_monthly_calendar_days_only(){
      $target_date = new OsWpDateTime($this->params['target_date_string']);
      $this->vars['target_date'] = $target_date;

      $this->set_layout('none');
      $this->format_render(__FUNCTION__);
    }


    public function load_monthly_calendar_days(){
      $target_date = new OsWpDateTime($this->params['target_date_string']);
      $service_id = $this->params['service_id'];
      $agent_id = $this->params['agent_id'];
      $calendar_layout = isset($this->params['calendar_layout']) ? $this->params['calendar_layout'] : 'classic';
      $location_id = isset($this->params['location_id']) ? $this->params['location_id'] : OsLocationHelper::get_selected_location_id();
      $duration = isset($this->params['duration']) ? $this->params['duration'] : false;
      $total_attendies = isset($this->params['total_attendies']) ? $this->params['total_attendies'] : 1;

      $calendar_settings = ['service_id' => $service_id, 
                            'agent_id' => $agent_id, 
                            'location_id' => $location_id, 
                            'layout' => $calendar_layout, 
                            'duration' => $duration, 
                            'total_attendies' => $total_attendies];
      if(!isset($this->params['allow_full_access'])){
        $calendar_settings['earliest_possible_booking'] = OsSettingsHelper::get_settings_value('earliest_possible_booking', false);
        $calendar_settings['latest_possible_booking'] = OsSettingsHelper::get_settings_value('latest_possible_booking', false);
      }

      $calendar_settings['timeshift_minutes'] = OsTimeHelper::get_timezone_shift_in_minutes(OsTimeHelper::get_timezone_name_from_session());


      $this->format_render('_monthly_calendar_days', array('target_date' => $target_date, 'calendar_settings' => $calendar_settings));
    }


    function daily_agents(){
      $this->vars['breadcrumbs'][] = array('label' => __('Daily View', 'latepoint'), 'link' => false );

      $services = new OsServiceModel();
      $agents = new OsAgentModel();

      if($this->logged_in_agent_id){
        $agents->where(['id' => $this->logged_in_agent_id]);
      }

      $agents_models = $agents->get_results_as_models();
      $services_models = $services->get_results_as_models();
      $this->vars['services'] = $services_models;

      if($services_models){

        if(isset($this->params['selected_service_id'])){
          $selected_service = $services->load_by_id($this->params['selected_service_id']);
        }else{
          $selected_service = $services_models[0];
        }

      }else{
        $selected_service = false;
      }


      $timeblock_interval = ($selected_service) ? $selected_service->get_timeblock_interval() : OsSettingsHelper::get_default_timeblock_interval();
      $selected_service_id = ($selected_service) ? $selected_service->id : false;

      $this->vars['agents'] = $agents_models;
      $this->vars['selected_service'] = $selected_service;
      $this->vars['selected_service_id'] = $selected_service_id;

      $this->vars['timeblock_interval'] = $timeblock_interval;

      $today_date = new OsWpDateTime('today');

      if(isset($this->params['target_date'])){
        $target_date = new OsWpDateTime($this->params['target_date']);
      }else{
        $target_date = new OsWpDateTime('today');
      }

      $this->vars['nice_selected_date'] = OsTimeHelper::nice_date($target_date->format('Y-m-d'));

      $calendar_start = clone $target_date;
      $calendar_end = clone $target_date;

      $this->vars['today_date'] = $today_date;
      $this->vars['target_date'] = $target_date;


      $work_periods_arr = OsBookingHelper::get_work_periods(['custom_date' => $target_date->format('Y-m-d'),
                                                              'location_id' => OsLocationHelper::get_selected_location_id(),
                                                              'week_day' => $target_date->format('N'),
                                                              'agent_id' => LATEPOINT_ALL]);
      $this->vars['work_periods_arr'] = $work_periods_arr;


      $bookings = [];
      foreach($agents_models as $agent){
        $bookings[$agent->id] = OsBookingHelper::get_bookings_for_date($target_date->format('Y-m-d'), ['agent_id' => $agent->id, 'location_id' => OsLocationHelper::get_selected_location_id()]);
      }

      list($this->vars['work_start_minutes'], $this->vars['work_end_minutes']) = OsBookingHelper::get_work_start_end_time($work_periods_arr);
      list($this->vars['calendar_start_minutes'], $this->vars['calendar_end_minutes']) = OsBookingHelper::get_calendar_start_end_time($bookings, $this->vars['work_start_minutes'], $this->vars['work_end_minutes']);

      $this->vars['bookings'] = $bookings;

      $this->vars['work_total_minutes'] = $this->vars['work_end_minutes'] - $this->vars['work_start_minutes'];
      $this->vars['calendar_total_minutes'] = $this->vars['calendar_end_minutes'] - $this->vars['calendar_start_minutes'];
      // !! Add calendar work start and end to daily agent and weekly agent functions, 
      // !! also move the calculation to separate function which takes bookings work work periods as arguments

      $this->format_render(__FUNCTION__);
    }


    function daily_agent(){
      $this->vars['breadcrumbs'][] = array('label' => __('Daily View', 'latepoint'), 'link' => false );

      $services = new OsServiceModel();
      $agents = new OsAgentModel();

      if($this->logged_in_agent_id){
        $agents->where(['id' => $this->logged_in_agent_id]);
        $this->params['selected_agent_id'] = $this->logged_in_agent_id;
      }

      $agents_models = $agents->get_results_as_models();
      $selected_agent = $agents_models[0];
      if(isset($this->params['selected_agent_id'])){
        $selected_agent = $agents->load_by_id($this->params['selected_agent_id']);
      }
      $selected_agent_id = (isset($selected_agent)) ? $selected_agent->id : false;

      $services_models = $services->get_results_as_models();
      $this->vars['services'] = $services_models;

      if($services_models){

        if(isset($this->params['selected_service_id'])){
          $selected_service = $services->load_by_id($this->params['selected_service_id']);
        }else{
          $selected_service = $services_models[0];
        }

      }else{
        $selected_service = false;
      }


      $timeblock_interval = ($selected_service) ? $selected_service->get_timeblock_interval() : OsSettingsHelper::get_default_timeblock_interval();
      $selected_service_id = ($selected_service) ? $selected_service->id : false;

      $this->vars['agents'] = $agents_models;
      $this->vars['selected_service'] = $selected_service;
      $this->vars['selected_agent'] = $selected_agent;
      $this->vars['selected_agent_id'] = $selected_agent_id;
      $this->vars['selected_service_id'] = $selected_service_id;

      $this->vars['timeblock_interval'] = $timeblock_interval;

      $today_date = new OsWpDateTime('today');

      if(isset($this->params['target_date'])){
        $target_date = new OsWpDateTime($this->params['target_date']);
      }else{
        $target_date = new OsWpDateTime('today');
      }

      $this->vars['nice_selected_date'] = OsTimeHelper::nice_date($target_date->format('Y-m-d'));

      $calendar_prev = clone $target_date;
      $calendar_next = clone $target_date;
      $calendar_start = clone $target_date;
      $calendar_end = clone $target_date;

      $this->vars['today_date'] = $today_date;
      $this->vars['target_date'] = $target_date;

      $this->vars['calendar_prev'] = $calendar_prev->modify('- 7 days');
      $this->vars['calendar_next'] = $calendar_next->modify('+ 7 days');





      $work_periods_arr = OsBookingHelper::get_work_periods(['agent_id' => $selected_agent_id, 
                                                              'custom_date' => $target_date->format('Y-m-d'),
                                                              'location_id' => OsLocationHelper::get_selected_location_id(),
                                                              'week_day' => $target_date->format('N')]);
      $this->vars['work_periods_arr'] = $work_periods_arr;

      $bookings = OsBookingHelper::get_bookings_for_date($target_date->format('Y-m-d'), ['agent_id' => $selected_agent_id, 'location_id' => OsLocationHelper::get_selected_location_id()]);

      list($this->vars['work_start_minutes'], $this->vars['work_end_minutes']) = OsBookingHelper::get_work_start_end_time($work_periods_arr);
      list($this->vars['calendar_start_minutes'], $this->vars['calendar_end_minutes']) = OsBookingHelper::get_calendar_start_end_time([$bookings], $this->vars['work_start_minutes'], $this->vars['work_end_minutes']);

      $this->vars['work_total_minutes'] = $this->vars['work_end_minutes'] - $this->vars['work_start_minutes'];
      $this->vars['calendar_total_minutes'] = $this->vars['calendar_end_minutes'] - $this->vars['calendar_start_minutes'];





      $this->vars['bookings'] = $bookings;
      $this->vars['total_bookings'] = $bookings ? count($bookings) : 0;
      $this->vars['total_customers'] = OsBookingHelper::get_total_customers_for_date($target_date->format('Y-m-d'), ['agent_id' => $selected_agent_id, 'location_id' => OsLocationHelper::get_selected_location_id()]);
      $this->vars['total_revenue'] = OsBookingHelper::get_stat_for_period('price', $target_date->format('Y-m-d'), $target_date->format('Y-m-d'), false, false, $selected_agent_id, OsLocationHelper::get_selected_location_id());
      $this->vars['total_openings'] = OsAgentHelper::count_openings_for_date($selected_agent, $selected_service, OsLocationHelper::get_selected_location(), $target_date->format('Y-m-d'));

      $this->format_render(__FUNCTION__);
    }



    function monthly_agents(){
      $this->vars['breadcrumbs'] = [];

      if(isset($this->params['month']) && isset($this->params['year'])){
        $start_date_string = implode('-', [$this->params['year'], $this->params['month'], '01']);
        $this->vars['calendar_only'] = true;
      }else{
        $this->vars['calendar_only'] = false;
        $start_date_string = implode('-', [OsTimeHelper::today_date('Y'), OsTimeHelper::today_date('m'), '01']);
      }

      $agents = new OsAgentModel();

      if($this->logged_in_agent_id){
        $agents->where(['id' => $this->logged_in_agent_id]);
      }
      $agents_arr = $agents->get_results();



      $agents = array();
      foreach($agents_arr as $agent_row){
        $agent = new OsAgentModel();
        $agent->load_from_row_data($agent_row);
        $agents[] = $agent;
      }

      $this->vars['agents'] = $agents;
      $this->vars['start_date_string'] = $start_date_string;
      $this->vars['calendar_start_date'] = new OsWpDateTime($start_date_string);


      
      $this->format_render(__FUNCTION__);
    }




    function weekly_agent(){
      $this->vars['breadcrumbs'][] = array('label' => __('Weekly Calendar', 'latepoint'), 'link' => false );

      $agents = new OsAgentModel();


      if($this->logged_in_agent_id){
        $this->params['selected_agent_id'] = $this->logged_in_agent_id;
        $agents->where(['id' => $this->logged_in_agent_id]);
      }

      $agents_arr = $agents->get_results();

      $this->vars['agents'] = $agents_arr;
      if(isset($this->params['selected_agent_id'])){
        $selected_agent = $agents->load_by_id($this->params['selected_agent_id']);
      }else{
        if(isset($agents_arr) && !empty($agents_arr)){
          $selected_agent = $agents->load_by_id($agents_arr[0]->id);
        }else{
          $selected_agent = false;
        }
      }

      $selected_agent_id = (isset($selected_agent) && $selected_agent) ? $selected_agent->id : false;
      $this->vars['selected_agent_id'] = $selected_agent_id;
      $this->vars['selected_agent'] = $selected_agent;

      $this->vars['timeblock_interval'] = OsSettingsHelper::get_default_timeblock_interval();

      $today_date = new OsWpDateTime('today');

      if(isset($this->params['target_date'])){
        $target_date = new OsWpDateTime($this->params['target_date']);
      }else{
        $target_date = new OsWpDateTime('today');
      }

      $calendar_prev = clone $target_date;
      $calendar_next = clone $target_date;
      $calendar_start = clone $target_date;
      $calendar_end = clone $target_date;

      $this->vars['today_date'] = $today_date;
      $this->vars['target_date'] = $target_date;
      $this->vars['calendar_start'] = $calendar_start->modify('monday this week');
      $this->vars['calendar_end'] = $calendar_end->modify('sunday this week');

      $this->vars['calendar_prev'] = $calendar_prev->modify('- 7 days');
      $this->vars['calendar_next'] = $calendar_next->modify('+ 7 days');




      $work_periods_arr = OsBookingHelper::get_work_periods(['agent_id' => $selected_agent_id, 
                                                              'location_id' => OsLocationHelper::get_selected_location_id()]);

      list($this->vars['work_start_minutes'], $this->vars['work_end_minutes']) = OsBookingHelper::get_work_start_end_time($work_periods_arr);
      $this->vars['calendar_start_minutes'] = $this->vars['work_start_minutes'];
      $this->vars['calendar_end_minutes'] = $this->vars['work_end_minutes'];


      $this->vars['work_total_minutes'] = $this->vars['work_end_minutes'] - $this->vars['work_start_minutes'];
      $this->vars['calendar_total_minutes'] = $this->vars['calendar_end_minutes'] - $this->vars['calendar_start_minutes'];

      $this->format_render(__FUNCTION__);
    }

  }

endif;