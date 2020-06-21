<?php 

class OsAgentHelper {

  public static function get_full_name($agent){
  	return join(' ', array($agent->first_name, $agent->last_name));
  }

  public static function get_agents_for_service_and_location($service_id = false, $location_id = false, $active_only = true){
    $all_agent_ids = OsConnectorHelper::get_connected_object_ids('agent_id', ['service_id' => $service_id, 'location_id' => $location_id]);
    if($active_only){
      $agents = new OsAgentModel();
      $active_agent_ids = $agents->select('id')->should_be_active()->get_results(ARRAY_A);
      if($active_agent_ids){
        $active_agent_ids = array_column($active_agent_ids, 'id');
        $all_agent_ids = array_intersect($active_agent_ids, $all_agent_ids);
      }else{
        $all_agent_ids = [];
      }
    }
    return $all_agent_ids;
  }

  public static function is_agent_available_on($agent_id, $start_date, $start_minutes, $duration, $service_id, $location_id, $total_attendies = 1){
    $work_periods_arr = OsBookingHelper::get_work_periods(['custom_date' => $start_date, 'service_id' => $service_id, 'agent_id' => $agent_id, 'location_id' => $location_id]);
    $booked_periods_arr = OsBookingHelper::get_bookings_times_for_date($start_date, $agent_id);
    $is_available = true;
    if(empty($duration)){
      $duration = OsServiceHelper::get_default_duration_for_service($service_id);
    }
    $end_minutes = $start_minutes + $duration;
    $capacity = OsServiceHelper::get_max_capacity_by_service_id($service_id);

    if(OsBookingHelper::is_timeframe_booked($start_minutes, $end_minutes, $booked_periods_arr, $service_id, $capacity, $total_attendies)){
      $is_available = false;
    }
    if(!OsBookingHelper::is_timeframe_in_work_periods($start_minutes, $end_minutes, $work_periods_arr)){
      $is_available = false;
    }
    return $is_available;
  }

  public static function get_agents_list(){
    $agents = new OsAgentModel();
    $agents = $agents->get_results_as_models();
    $agents_list = [];
    if($agents){
      foreach($agents as $agent){
        $agents_list[] = ['value' => $agent->id, 'label' => $agent->full_name];
      }
    }
    return $agents_list;
  }

  public static function get_avatar_url($agent){
    $default_avatar = LATEPOINT_DEFAULT_AVATAR_URL;
    return OsImageHelper::get_image_url_by_id($agent->avatar_image_id, 'thumbnail', $default_avatar);
  }

  public static function get_bio_image_url($agent){
    $default_bio_image = LATEPOINT_DEFAULT_AVATAR_URL;
    return OsImageHelper::get_image_url_by_id($agent->bio_image_id, 'large', $default_bio_image);
  }

  public static function get_top_agents($date_from, $date_to, $limit = false, $location_id = false){
    $agents = new OsAgentModel();
    $bookings = new OsBookingModel();

    $bookings->select('count('.LATEPOINT_TABLE_BOOKINGS.'.id) as total_appointments, SUM(end_time - start_time) as total_minutes, SUM(price) as total_price, agent_id')
              ->join(LATEPOINT_TABLE_AGENTS, [LATEPOINT_TABLE_AGENTS.'.id' => 'agent_id'])
              ->where(['start_date >=' => $date_from, 'start_date <=' => $date_to])
              ->group_by('agent_id')
              ->order_by('total_appointments desc')
              ->should_be_approved();
    if($location_id) $bookings->where(['location_id' => $location_id]);
    if($limit) $bookings->set_limit($limit);

    $top_agents = $bookings->get_results();

    for($i=0; $i<count($top_agents); $i++){
      $bookings = new OsBookingModel();

      $bookings->select('count('.LATEPOINT_TABLE_BOOKINGS.'.id) as total_appointments, service_id, bg_color, name')
                                                            ->join(LATEPOINT_TABLE_SERVICES, [LATEPOINT_TABLE_SERVICES.'.id' => 'service_id'])
                                                            ->where(['agent_id' => $top_agents[$i]->agent_id, 'start_date >=' => $date_from, 'start_date <=' => $date_to])
                                                            ->group_by('service_id')
                                                            ->should_be_approved();
      if($location_id) $bookings->where(['location_id' => $location_id]);
      $top_agents[$i]->service_breakdown = $bookings->get_results(ARRAY_A);
    }
    return $top_agents;
  }

  public static function count_agents_on_duty($date, $location_id = false){
    $agents = new OsAgentModel();
    return $agents->count();
  }

  public static function count_agents(){
    $agents = new OsAgentModel();
    return $agents->count();
  }

  public static function count_openings_for_date($agent, $service, $location, $target_date){
    if(!isset($target_date) || !isset($agent) || !isset($service) || !isset($location) || empty($target_date) || empty($agent) || empty($service) || empty($location)) return 0;
    $work_start_end_time = OsBookingHelper::get_work_start_end_time_for_date(['custom_date' => $target_date, 'service_id' => $service->id, 'agent_id' => $agent->id, 'location_id' => $location->id]);
    $work_start_minutes = $work_start_end_time[0];
    $work_end_minutes = $work_start_end_time[1];
    $total_work_minutes = $work_end_minutes - $work_start_minutes;
    $timeblock_interval = $service->get_timeblock_interval();

    $work_periods_arr = OsBookingHelper::get_work_periods(['custom_date' => $target_date, 'service_id' => $service->id, 'agent_id' => $agent->id, 'location_id' => $location->id]);
    $booked_periods_arr = OsBookingHelper::get_bookings_times_for_date($target_date, $agent->id);

    $openings = 0;
    for($current_minutes = $work_start_minutes; $current_minutes <= $work_end_minutes; $current_minutes+=$timeblock_interval){
      $is_available = true;
      if(OsBookingHelper::is_timeframe_booked($current_minutes, $current_minutes + $service->duration, $booked_periods_arr, $service->id)){
        $is_available = false;
      }
      if(!OsBookingHelper::is_timeframe_in_work_periods($current_minutes, $current_minutes + $service->duration, $work_periods_arr)){
        $is_available = false;
      }
      if($is_available) $openings++;
    }
    return $openings;
  }

  public static function availability_timeline_off($off_label = false, $show_avatar = false, $agent = false){
    $off_label = $off_label ? $off_label : __('Not Available', 'latepoint');
    ?>
      <div class="agent-day-availability-w">
        <?php if($show_avatar && $agent){ ?><a href="<?php echo OsRouterHelper::build_link(OsRouterHelper::build_route_name('agents', 'edit_form'), array('id' => $agent->id) ) ?>" class="agent-avatar-w with-hover-name" style="background-image: url(<?php echo $agent->get_avatar_url(); ?>);"><span><?php echo $agent->full_name; ?></span></a><?php } ?>
        <div class="agent-timeslots">
          <div class="agent-timeslot full-day-off"><span class="agent-timeslot-label"><?php echo $off_label; ?></span></div>
        </div>
      </div>
    <?php
  }


  public static function availability_timeline($agent, $service, $location, $target_date, $settings = array(), $total_attendies = 1){
    if(isset($agent) && isset($service)){
      $default_settings = array(
        'show_avatar' => true, 
        'book_on_click' => true, 
        'show_ticks' => true, 
        'preset_work_start_end_time' => false);
      $settings = array_merge($default_settings, $settings);

      // check if connection exxists between location, agent and service
      $is_connected = OsConnectorHelper::has_connection(['agent_id' => $agent->id, 'service_id' => $service->id, 'location_id' => $location->id]);
      if(!$is_connected){
        self::availability_timeline_off(__('Not Available', 'latepoint'), $settings['show_avatar'], $agent);
        return;
      }

      $duration_minutes = (isset($settings['custom_duration'])) ? $settings['custom_duration'] : $service->duration;
      $capacity = $service->capacity_max ? $service->capacity_max : 1;

      if($settings['preset_work_start_end_time']){
        $work_start_minutes = $settings['preset_work_start_end_time'][0];
        $work_end_minutes = $settings['preset_work_start_end_time'][1];
      }else{
        $work_start_end_time = OsBookingHelper::get_work_start_end_time_for_date(['custom_date' => $target_date, 'service_id' => $service->id, 'agent_id' => $agent->id, 'location_id' => $location->id, 'flexible_search' => true]);
        $work_start_minutes = $work_start_end_time[0];
        $work_end_minutes = $work_start_end_time[1];
      }
      $total_work_minutes = $work_end_minutes - $work_start_minutes;
      $timeblock_interval = $service->get_timeblock_interval();

      $work_periods_arr = OsBookingHelper::get_work_periods(['custom_date' => $target_date, 'service_id' => $service->id, 'agent_id' => $agent->id, 'location_id' => $location->id, 'flexible_search' => true]);
      $query_location_id = $location->id;
      // if agents can only have appointment in one location at a time
      if(OsSettingsHelper::is_on('one_location_at_time')) $query_location_id = false;

      $booked_periods_arr = OsBookingHelper::get_bookings_times_for_date($target_date, $agent->id, $query_location_id);
      ?>
      <div class="agent-day-availability-w">
        <?php if($settings['show_avatar']){ ?>
          <a href="<?php echo OsRouterHelper::build_link(OsRouterHelper::build_route_name('agents', 'edit_form'), array('id' => $agent->id) ) ?>" class="agent-avatar-w with-hover-name" style="background-image: url(<?php echo $agent->get_avatar_url(); ?>);"><span><?php echo $agent->full_name; ?></span></a>
        <?php } ?>
        <div class="agent-timeslots">
          <?php 
          if($work_start_minutes == $work_end_minutes){
            echo '<div class="agent-timeslot full-day-off"><span class="agent-timeslot-label">'.__('Day Off', 'latepoint').'</span></div>';
          }else{
            for($current_minutes = $work_start_minutes; $current_minutes <= $work_end_minutes; $current_minutes+=$timeblock_interval){
              $ampm = OsTimeHelper::am_or_pm($current_minutes);

              $booking_start_minute = ($current_minutes == $work_start_minutes) ? $current_minutes : $current_minutes - $service->buffer_before;
              $booking_end_minute = (($current_minutes + $duration_minutes) == $work_end_minutes) ? $current_minutes + $duration_minutes : $current_minutes + $duration_minutes + $service->buffer_after;

              $timeslot_class = 'agent-timeslot';
              $is_available = true;
              if(OsBookingHelper::is_timeframe_booked($booking_start_minute, $booking_end_minute, $booked_periods_arr, $service->id, $capacity, $total_attendies)){
                $timeslot_class.= ' is-booked';
                $is_available = false;
              }
              if(!OsBookingHelper::is_timeframe_in_work_periods($booking_start_minute, $booking_end_minute, $work_periods_arr)){
                $timeslot_class.= ' is-off';
                $is_available = false;
              }
              $tick_html = '';
              if(($current_minutes % 60) == 0){
                $timeslot_class.= ' with-tick';
                $tick_html = '<span class="agent-timeslot-tick"><strong>'. OsTimeHelper::minutes_to_hours($current_minutes) .'</strong>'.' '.$ampm.'</span>';
              }
              $datas_attr = '';
              if($is_available){
                $timeslot_class.= ' is-available';
                if($settings['book_on_click']){
                  $datas_attr = OsBookingHelper::quick_booking_btn_html(false, array('start_time'=> $current_minutes, 'agent_id' => $agent->id, 'service_id' => $service->id, 'location_id' => $location->id, 'start_date' => $target_date));
                }else{
                  $datas_attr = 'data-date="'.$target_date.'" data-formatted-date="'.OsTimeHelper::reformat_date_string($target_date, 'Y-m-d', OsSettingsHelper::get_date_format()).'" data-minutes="'.$current_minutes.'"';
                  $timeslot_class.= ' fill-booking-time';
                }
              }
              echo '<div '.$datas_attr.' class="'.$timeslot_class.'" data-minutes="' . $current_minutes . '"><span class="agent-timeslot-label">'.OsTimeHelper::minutes_to_hours_and_minutes($current_minutes).'</span>'.$tick_html.'</div>';
            }
          }
        ?>
        </div>
      </div><?php
    }else{ ?>
      <div class="no-results-w">
        <div class="icon-w"><i class="latepoint-icon latepoint-icon-users"></i></div>
        <?php if(!isset($agents)){ ?>
          <h2><?php _e('No Existing Agents Found', 'latepoint'); ?></h2>
          <a href="<?php echo OsRouterHelper::build_link(OsRouterHelper::build_route_name('agents', 'new_form') ) ?>" class="latepoint-btn"><i class="latepoint-icon latepoint-icon-plus"></i><span><?php _e('Add First Agent', 'latepoint'); ?></span></a>
        <?php }else{ ?>
          <div class="no-results-w">
            <div class="icon-w"><i class="latepoint-icon latepoint-icon-book"></i></div>
            <h2><?php _e('No Services Found', 'latepoint'); ?></h2>
            <a href="<?php echo OsRouterHelper::build_link(OsRouterHelper::build_route_name('services', 'new_form') ) ?>" class="latepoint-btn">
              <i class="latepoint-icon latepoint-icon-plus"></i>
              <span><?php _e('Add Service', 'latepoint'); ?></span>
            </a>
          </div>
        <?php } ?>
      </div>
    <?php 
    }
  }
}