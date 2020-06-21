<?php 

class OsWorkPeriodsHelper {

  public static $existing_work_periods;
  // args: period_id, week_day, is_active, start_time, end_time, custom_date, agent_id, service_id
  public static function generate_work_period_form($args = array(), $allow_remove = true){
    $default_args = array(
      'period_id' => false,
      'week_day' => 1,
      'allow_remove' => true,
      'start_time' => 480,
      'end_time' => 1080,
      'agent_id' => 0,
      'location_id' => 0,
      'service_id' => 0
    );
    $args = array_merge($default_args, $args);

    $period_id = (!$args['period_id']) ? 'new_'.$args['week_day'].'_'.OsUtilHelper::random_text() : $args['period_id'];
    $period_html = '<div class="ws-period">';
      $period_html.= OsFormHelper::time_field('work_periods['.$period_id.'][start_time]', __('Start', 'latepoint'), $args['start_time'], true);
      $period_html.= OsFormHelper::time_field('work_periods['.$period_id.'][end_time]', __('Finish', 'latepoint'), $args['end_time'], true);
      $period_html.= OsFormHelper::hidden_field('work_periods['.$period_id.'][week_day]', $args['week_day']);
      $period_html.= OsFormHelper::hidden_field('work_periods['.$period_id.'][is_active]', self::is_period_active($args['start_time'], $args['end_time']), array('class' => 'is-active'));
      $period_html.= OsFormHelper::hidden_field('work_periods['.$period_id.'][agent_id]', $args['agent_id']);
      $period_html.= OsFormHelper::hidden_field('work_periods['.$period_id.'][location_id]', $args['location_id']);
      $period_html.= OsFormHelper::hidden_field('work_periods['.$period_id.'][service_id]', $args['service_id']);
      if(isset($args['custom_date'])) $period_html.= OsFormHelper::hidden_field('work_periods['.$period_id.'][custom_date]', $args['custom_date']);
      if($allow_remove) $period_html.= '<button class="ws-period-remove"><i class="latepoint-icon latepoint-icon-x"></i></button>';
    $period_html.= '</div>';
    return $period_html;
  }

  public static function is_period_active($start_time, $end_time){
    return (($start_time == 0) && ($end_time == 0)) ? false : true;
  }

  public static function save_work_periods($work_periods_to_save){
    $ids_to_save = array();
    $inactive_weekdays = array();
    // save passed periods
    if($work_periods_to_save){
      foreach($work_periods_to_save as $id => $work_period){
        if(in_array($work_period['week_day'], $inactive_weekdays)) continue;
        if($work_period['is_active'] == 0){
          $work_period['start_time'] = 0;  
          $work_period['end_time'] = 0;  
          $inactive_weekdays[] = $work_period['week_day'];
        }else{
          $start_ampm = isset($work_period['start_time']['ampm']) ? $work_period['start_time']['ampm'] : false;
          $end_ampm = isset($work_period['end_time']['ampm']) ? $work_period['end_time']['ampm'] : false;

          $work_period['start_time'] = OsTimeHelper::convert_time_to_minutes($work_period['start_time']['formatted_value'], $start_ampm);
          $work_period['end_time'] = OsTimeHelper::convert_time_to_minutes($work_period['end_time']['formatted_value'], $end_ampm);
        }
        if(substr( $id, 0, 4 ) === "new_"){
          // new record
          $work_period_obj = new OsWorkPeriodModel();
          $work_period_obj->set_data($work_period);
          $work_period_obj->save();
          $ids_to_save[] = $work_period_obj->id;
        }else{
          // existing work period
          $work_period_obj = new OsWorkPeriodModel($id);
          if(!$work_period_obj){
            $work_period_obj = new OsWorkPeriodModel();
            unset($work_period['id']);
          }
          $work_period_obj->set_data($work_period);
          if($work_period_obj->save()){
            $ids_to_save[] = $work_period_obj->id;
          }
        }
      }
    }
    // if any periods were saved, get their agent and service info to delete obsolete records
    $search_args = (isset($work_period_obj)) ? array('agent_id' => $work_period_obj->agent_id, 'service_id' => $work_period_obj->service_id, 'location_id' => $work_period_obj->location_id) : array();
    if(isset($work_period_obj) && $work_period_obj->custom_date){
      $search_args['custom_date'] = $work_period_obj->custom_date;
    }else{
      $search_args['custom_date'] = 'IS NULL';
    }
    $ids_in_db = OsWorkPeriodsHelper::get_periods_ids_by_args($search_args);

    $period_ids_to_remove = array_diff($ids_in_db, $ids_to_save);
    if(!empty($period_ids_to_remove)){
      $work_period_obj = new OsWorkPeriodModel();
      foreach($period_ids_to_remove as $period_id){
        $work_period_obj->delete($period_id);
      }
    }
  }



  public static function get_periods_ids_by_args($args = array()){
    $default_args = array(
      'custom_date' => false, 
      'week_day' => false, 
      'service_id' => 0, 
      'location_id' => 0, 
      'agent_id' => 0);
    $args = array_merge($default_args, $args);
    if($args['custom_date']) $query_args['custom_date'] = $args['custom_date'];
    if($args['week_day']) $query_args['week_day'] = $args['week_day'];
    $query_args['agent_id'] = $args['agent_id'];
    $query_args['location_id'] = $args['location_id'];
    $query_args['service_id'] = $args['service_id'];

    $work_periods_model = new OsWorkPeriodModel();
    $work_periods_rows = $work_periods_model->select('id')->where($query_args)->get_results();
    if(is_array($work_periods_rows)){
      $ids = array_map(function($row){return $row->id; }, $work_periods_rows);
    }else{
      $ids = array();
    }
    return $ids;
  }

  // custom_date, week_day, service_id, agent_id, flexible_search
  public static function load_work_periods($args = array()){
    $default_args = array(
      'custom_date' => false, 
      'week_day' => false, 
      'service_id' => 0, 
      'agent_id' => 0, 
      'location_id' => 0, 
      'flexible_search' => true);
    if(isset($args['location_id']) && ($args['location_id'] === false)) $args['location_id'] = 0;
    if(isset($args['agent_id']) && ($args['agent_id'] === false)) $args['agent_id'] = 0;
    if(isset($args['service_id']) && ($args['service_id'] === false)) $args['service_id'] = 0;
    $args = array_merge($default_args, $args);

    OsWorkPeriodsHelper::set_default_working_hours();

    $work_periods_model = new OsWorkPeriodModel();
    $query_args = array();

    // Service query
    if($args['flexible_search'] && $args['service_id']){
      $query_args['service_id']['OR'] = array($args['service_id'], 0);
    }else{
      // search only for schedules that belong to passed service_id
      $query_args['service_id'] = $args['service_id'];
    }

    // Agent query
    if($args['flexible_search'] && $args['location_id']){
      $query_args['location_id']['OR'] = array($args['location_id'], 0);
    }else{
      // search only for schedules that belong to passed location_id
      $query_args['location_id'] = $args['location_id'];
    }

    // Agent query
    if($args['agent_id'] != LATEPOINT_ALL){
      if($args['flexible_search'] && $args['agent_id']){
        $query_args['agent_id']['OR'] = array($args['agent_id'], 0);
      }else{
        // search only for schedules that belong to passed agent_id
        $query_args['agent_id'] = $args['agent_id'];
      }
    }

     // Week Day
     if($args['week_day'] && in_array($args['week_day'], OsUtilHelper::get_weekday_numbers())){
        $query_args['week_day'] = $args['week_day'];
     }

      // Custom Date
     if($args['custom_date']){
        $date_obj = new OsWpDateTime($args['custom_date']);
        $week_day = $date_obj->format('N');
        if($args['flexible_search']){
          $query_args['custom_date']['OR'] = array($date_obj->format('Y-m-d'), 'IS NULL');
        }else{
          $query_args['custom_date'] = $date_obj->format('Y-m-d');
        }
        $query_args['week_day'] = $week_day;
     }else{
      $query_args['custom_date'] = 'IS NULL';
     }

    $work_periods = $work_periods_model->where($query_args)->order_by('custom_date DESC, agent_id DESC, service_id DESC, location_id DESC, start_time asc')->get_results_as_models();
    // OsDebugHelper::log($work_periods_model->last_query);
    if($args['agent_id'] != LATEPOINT_ALL){
      $work_periods = self::filter_periods($work_periods);
    }
    return $work_periods;
  }

  public static function filter_periods($work_periods){
    // remove overriden periods
    $filtered_periods = [];
    if(is_array($work_periods) && (count($work_periods) > 1)){
      $reference = $work_periods[0];
      $filtered_periods[] = $reference;
      for($i = 1; $i < count($work_periods); $i++){
        if($work_periods[$i]->week_day == $reference->week_day){
          if($work_periods[$i]->agent_id != $reference->agent_id || 
            $work_periods[$i]->location_id != $reference->location_id || 
            $work_periods[$i]->service_id != $reference->service_id || 
            $work_periods[$i]->custom_date != $reference->custom_date){
            // conflicting period, skip it
          }else{
            $filtered_periods[] = $work_periods[$i];
          }
        }else{
          $reference = $work_periods[$i];
          $filtered_periods[] = $reference;
        }
      }
      return $filtered_periods;
    }else{
      return $work_periods;
    }
  }

	public static function set_default_working_hours(){
    $work_start_minutes = 8 * 60;
    $work_end_minutes = 17 * 60;
    $week_days = OsUtilHelper::get_weekday_numbers();

    // Try to find existing work periods in the database
    $work_periods_model = new OsWorkPeriodModel();
    if(!self::$existing_work_periods){
      self::$existing_work_periods = $work_periods_model->select('week_day')->where(array('agent_id' => 0, 'service_id' => 0, 'location_id' => 0))->where(array('custom_date' => 'IS NULL'))->group_by('week_day')->get_results(ARRAY_A);
      if(self::$existing_work_periods){
        self::$existing_work_periods = array_map(function($work_period){ return $work_period['week_day']; }, self::$existing_work_periods);
        $week_days = array_diff($week_days, self::$existing_work_periods);
        // if already had some work periods - set others to 0/0 because before we used to NOT store non working days in the database, now we set hours to 0/0 instead for day offs
        $work_start_minutes = 0;
        $work_end_minutes = 0;
      }
      if(!empty($week_days)){
        foreach($week_days as $week_day){
    			$work_period = new OsWorkPeriodModel();
    			$work_period->service_id = 0;
          $work_period->agent_id = 0;
    			$work_period->location_id = 0;
    			$work_period->week_day = $week_day;
    			$work_period->start_time = $work_start_minutes;
    			$work_period->end_time = $work_end_minutes;
    			$work_period->save();
    		}
      }
    }

	}

  public static function is_custom_schedule($args = array()){
    $args['flexible_search'] = false;
    $work_periods = self::load_work_periods($args);
    return (count($work_periods) > 0);
  }

  public static function remove_periods_for_date($date, $args = array()){
    $default_args = [ 'agent_id' => 0, 'service_id' => 0, 'location_id' => 0];
    $args = array_merge($default_args, $args);
    $args['custom_date'] = $date;
    $work_periods_model = new OsWorkPeriodModel();
    $work_periods = $work_periods_model->where($args)->get_results_as_models();
    if($work_periods){
      foreach($work_periods as $work_period){
        $work_period->delete();
      }
    }
    return true;
  }


  public static function generate_days_with_custom_schedule($args = array()){
    $default_args = [ 'agent_id' => 0, 'service_id' => 0, 'location_id' => 0];
    $args = array_merge($default_args, $args);

    $work_periods = new OsWorkPeriodModel();
    $work_periods = $work_periods->where($args)->where([
                          'custom_date >' => 0, 
                          'OR' => ['start_time !=' => 0, 
                          'end_time !=' => 0]])->group_by('custom_date')->order_by('custom_date asc')->get_results_as_models();
    $html = '';
    if($work_periods && isset($work_periods[0])){
      $date = new OsWpDateTime($work_periods[0]->custom_date);
      $processing_year = $date->format('Y');
      $html.= '<div class="os-form-sub-header sub-level"><h3>'.$date->format('Y').'</h3></div>';
    }
    $html.= '<div class="custom-day-work-periods">';
      if($work_periods){
        foreach($work_periods as $work_period){
          $date = new OsWpDateTime($work_period->custom_date);
          if($processing_year != $date->format('Y')) $html.= '</div><div class="os-form-sub-header sub-level"><h3>'.$date->format('Y').'</h3></div><div class="custom-day-work-periods">';
          $html.= '<div class="custom-day-work-period">';
          $html.= '<a href="#" title="'.__('Edit Day Schedule', 'latepoint').'" class="edit-custom-day" '.self::generate_custom_day_period_action($work_period->custom_date, false, $args).'><i class="latepoint-icon latepoint-icon-edit-3"></i></a>';
          $html.= '<a href="#" data-os-pass-this="yes" data-os-after-call="latepoint_custom_day_removed" data-os-action="'.OsRouterHelper::build_route_name('settings', 'remove_custom_day_schedule').'" data-os-params="'.OsUtilHelper::build_os_params(array_merge($args, ['date' => $work_period->custom_date])).'" data-os-prompt="'.__('Are you sure you want to remove custom schedule for this day?', 'latepoint').'" title="'.__('Remove Day Schedule', 'latepoint').'" class="remove-custom-day"><i class="latepoint-icon latepoint-icon-trash-2"></i></a>';
          $html.= '<div class="custom-day-work-period-i">';
          $html.= '<div class="custom-day-number">'.$date->format('d').'</div>';
          $html.= '<div class="custom-day-month">'.OsUtilHelper::get_month_name_by_number($date->format('n')).'</div>';
          $html.= '</div>';
          $work_periods_for_date_model = new OsWorkPeriodModel();
          $work_periods_for_date = $work_periods_for_date_model->where($args)->where(['custom_date' => $work_period->custom_date])->order_by('start_time asc')->get_results_as_models();
          if($work_periods_for_date){
            $html.= '<div class="custom-day-periods">';
            foreach($work_periods_for_date as $work_period_for_date){
              $html.= '<div class="custom-day-period">'. $work_period_for_date->nice_start_time.' - '.$work_period_for_date->nice_end_time. '</div>';
            }
            $html.= '</div>';
          }
          $html.= '</div>';
          $processing_year = $date->format('Y');
        }
      }
      $html.= '<a class="add-custom-day-w" '.self::generate_custom_day_period_action(false, false, $args).'>
                <div class="add-custom-day-i">
                  <div class="add-day-graphic-w"><div class="add-day-plus"><i class="latepoint-icon latepoint-icon-plus4"></i></div></div><div class="add-day-label">'.__('Add Day', 'latepoint').'</div>
                </div>
              </a>';

    $html.= '</div>';
    echo $html;
  }


  public static function generate_off_days($args = array()){
    $default_args = [ 'agent_id' => 0, 'service_id' => 0, 'location_id' => 0];
    $args = array_merge($default_args, $args);

    $work_periods = new OsWorkPeriodModel();
    $work_periods = $work_periods->where($args)->where(['custom_date >' => 0, 
                                                        'start_time' => 0, 
                                                        'end_time' => 0])->group_by('custom_date')->order_by('custom_date asc')->get_results_as_models();
    $html = '';
    if($work_periods && isset($work_periods[0])){
      $date = new OsWpDateTime($work_periods[0]->custom_date);
      $processing_year = $date->format('Y');
      $html.= '<div class="os-form-sub-header sub-level"><h3>'.$date->format('Y').'</h3></div>';
    }
    $html.= '<div class="custom-day-work-periods">';
    if($work_periods){
      foreach($work_periods as $work_period){
        $date = new OsWpDateTime($work_period->custom_date);
        if($processing_year != $date->format('Y')) $html.= '</div><div class="os-form-sub-header sub-level"><h3>'.$date->format('Y').'</h3></div><div class="custom-day-work-periods">';
        $html.= '<div class="custom-day-work-period custom-day-off">';
        $html.= '<a href="#" data-os-pass-this="yes" data-os-after-call="latepoint_custom_day_removed" data-os-action="'.OsRouterHelper::build_route_name('settings', 'remove_custom_day_schedule').'" data-os-params="'.OsUtilHelper::build_os_params(array_merge($args, ['date' => $work_period->custom_date])).'" data-os-prompt="'.__('Are you sure you want to remove this day?', 'latepoint').'" title="'.__('Remove Day Schedule', 'latepoint').'" class="remove-custom-day"><i class="latepoint-icon latepoint-icon-trash-2"></i></a>';
        $html.= '<div class="custom-day-work-period-i">';
        $html.= '<div class="custom-day-number">'.$date->format('d').'</div>';
        $html.= '<div class="custom-day-month">'.OsUtilHelper::get_month_name_by_number($date->format('n')).'</div>';
        $html.= '</div>';
        $html.= '</div>';
        $processing_year = $date->format('Y');
      }
    }

    $html.= '<a class="add-custom-day-w" '.self::generate_custom_day_period_action(false, true, $args).'>
              <div class="add-custom-day-i">
                <div class="add-day-graphic-w"><div class="add-day-plus"><i class="latepoint-icon latepoint-icon-plus4"></i></div></div><div class="add-day-label">'.__('Add Day', 'latepoint').'</div>
              </div>
            </a>';
    $html.= '</div>';
    echo $html;
  }


  public static function generate_custom_day_period_action($target_date = false, $day_off = false, $args = array()){
    $os_params = [];
    if($day_off) $os_params['day_off'] = true;
    if($target_date){
      $os_params['target_date'] = $target_date;
      $hide_schedule_class = '';
    }else{
      $hide_schedule_class = ' hide-schedule';
    }
    $os_params = array_merge($os_params, $args);
    $html = 'data-os-after-call="latepoint_init_custom_day_schedule" data-os-lightbox-classes="width-700 '.$hide_schedule_class.' latepoint-lightbox-nopad" data-os-output-target="lightbox" data-os-action="'.OsRouterHelper::build_route_name('settings', 'custom_day_schedule_form').'"';
    if(!empty($os_params)) $html.= ' data-os-params="'.OsUtilHelper::build_os_params($os_params).'"';
    return $html;
  }


  // possible args:
  // agent_id, service_id, location_id
  public static function generate_work_periods($work_periods = false, $args = array(), $new_record = false){
    if(!$work_periods) $work_periods = OsWorkPeriodsHelper::load_work_periods($args);
    $working_periods_with_weekdays = array();
    if($work_periods){
      foreach($work_periods as $work_period){
        $working_periods_with_weekdays['day_'.$work_period->week_day][] = $work_period;
      }
    }
    for($i=1; $i<=7; $i++){
      $is_day_off = true;
      $period_forms_html = '';
      if(isset($working_periods_with_weekdays['day_'.$i])){
        $is_day_off = false;
        // EXISTING WORK PERIOD
        $allow_remove = false;
        foreach($working_periods_with_weekdays['day_'.$i] as $work_period){
          if($work_period->start_time == $work_period->end_time){
            $is_day_off = true;
          }
          if(isset($args['agent_id']) && $args['agent_id'] && ($work_period->agent_id != $args['agent_id'])){
            $work_period->agent_id = $args['agent_id'];
            $work_period->id = false;
          }
          if(isset($args['service_id']) && $args['service_id'] && ($work_period->service_id != $args['service_id'])){
            $work_period->service_id = $args['service_id'];
            $work_period->id = false;
          }
          if(isset($args['location_id']) && $args['location_id'] && ($work_period->location_id != $args['location_id'])){
            $work_period->location_id = $args['location_id'];
            $work_period->id = false;
          }
          if($new_record){
            $work_period->id = false;
          }
          $period_forms_html.= OsWorkPeriodsHelper::generate_work_period_form(array('period_id' => $work_period->id, 
                                                                                'week_day' => $i, 
                                                                                'is_active' => $work_period->is_active, 
                                                                                'agent_id' => $work_period->agent_id, 
                                                                                'service_id' => $work_period->service_id, 
                                                                                'location_id' => $work_period->location_id, 
                                                                                'start_time' => $work_period->start_time, 
                                                                                'end_time' => $work_period->end_time), $allow_remove);
          $allow_remove = true;
        }
      }else{ 
        // NEW WORK PERIOD
        $period_forms_html.= OsWorkPeriodsHelper::generate_work_period_form(array('period_id' => false, 'week_day' => $i), false);
      } ?>
      <div class="weekday-schedule-w <?php echo $is_day_off ? 'day-off' : ''; ?>">
        <div class="ws-head-w">
          <div class="os-toggler <?php echo $is_day_off ? 'off' : ''; ?>">
            <div class="toggler-rail"><div class="toggler-pill"></div></div>
          </div>
          <div class="ws-head">
            <div class="ws-day-name"><?php echo OsBookingHelper::get_weekday_name_by_number($i, true); ?></div>
            <div class="ws-day-hours">
              <?php
              if(isset($working_periods_with_weekdays['day_'.$i])){
                foreach($working_periods_with_weekdays['day_'.$i] as $index => $work_period){
                  if($work_period->start_time == $work_period->end_time) continue;
                  if($index >= 2) {
                    echo '<span>'.sprintf(__('+%d More', 'latepoint'), count($working_periods_with_weekdays['day_'.$i]) - 2).'</span>';
                    break;
                  }
                  echo '<span>'.$work_period->nice_start_time.'-'.$work_period->nice_end_time.'</span>';
                }
              }
              ?>
            </div>
            <div class="wp-edit-icon">
              <i class="latepoint-icon latepoint-icon-edit-3"></i>
            </div>
          </div>
        </div>
        <div class="weekday-schedule-form">
          <?php echo $period_forms_html; ?>
          <div class="ws-period-add" data- 
          data-os-params="<?php echo OsUtilHelper::build_os_params(array('week_day' => $i, 'agent_id' => $work_period->agent_id, 'service_id' => $work_period->service_id, 'location_id' => $work_period->location_id)); ?>" 
          data-os-before-after="before" 
          data-os-after-call="latepoint_init_work_period_form"
          data-os-action="<?php echo OsRouterHelper::build_route_name('settings', 'load_work_period_form'); ?>">
            <div class="add-period-graphic-w">
              <div class="add-period-plus"><i class="latepoint-icon latepoint-icon-plus-square"></i></div>
            </div>
            <div class="add-period-label"><?php echo sprintf(__('Add another work period for %s', 'latepoint'), OsBookingHelper::get_weekday_name_by_number($i, true)); ?></div>
          </div>
        </div>
      </div>
      <?php
    }
  }
}