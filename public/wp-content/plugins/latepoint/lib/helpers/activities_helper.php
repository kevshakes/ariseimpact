<?php 

class OsActivitiesHelper {
  public static function create_activity($atts = array()){
  	$activity = new OsActivityModel();
  	if(isset($atts['booking'])){
      $atts['booking_id'] = $atts['booking']->id;
  		$atts['agent_id'] = $atts['booking']->agent_id;
  		$atts['service_id'] = $atts['booking']->service_id;
  		$atts['customer_id'] = $atts['booking']->customer_id;
  	}
  	$atts['initiated_by'] = OsAuthHelper::get_highest_current_user_type();
  	$atts['initiated_by_id'] = OsAuthHelper::get_highest_current_user_id();

    if($atts['code'] == 'booking_change_status'){
      $atts['description'] = sprintf(__('Appointment status changed from %s to %s', 'latepoint'), $atts['old_value'], $atts['booking']->status);
    }

  	$activity = $activity->set_data($atts);
  	$activity->save();
  }
}