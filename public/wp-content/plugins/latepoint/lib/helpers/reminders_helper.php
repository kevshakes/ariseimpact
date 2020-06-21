<?php 

class OsRemindersHelper {
  
  function __construct(){
  }

  public static function process_reminders(){
    $reminders = OsRemindersHelper::get_reminders_arr();
    if($reminders){
      foreach($reminders as $reminder){
        if(empty($reminder['value'])) continue;
        echo '<h3>'.$reminder['name'].'</h3>';
        $booking_model = new OsBookingModel();
        $modify_by = ($reminder['when'] == 'before') ? '+' : '-';
        $start_or_end = ($reminder['when'] == 'before') ? 'start' : 'end';

        if($reminder['unit'] == 'day'){
          $modify_by.= $reminder['value'].' days';
          $search_booking_datetime = OsTimeHelper::get_modified_now_object($modify_by);
          $booking_model->where([$start_or_end.'_date' => $search_booking_datetime->format('Y-m-d')]);
        }elseif($reminder['unit'] == 'hour'){
          $modify_by.= $reminder['value'].' hours';
          $search_booking_datetime = OsTimeHelper::get_modified_now_object($modify_by);
          $booking_model->where([$start_or_end.'_date' => $search_booking_datetime->format('Y-m-d')])
                        ->where([$start_or_end.'_time >=' => (OsTimeHelper::convert_datetime_to_minutes($search_booking_datetime) - 30)])
                        ->where([$start_or_end.'_time <=' => (OsTimeHelper::convert_datetime_to_minutes($search_booking_datetime) + 30)]);
        }
        $bookings = $booking_model->select('agent_id, customer_id, service_id, '.LATEPOINT_TABLE_BOOKINGS.'.id, start_date, start_time, end_date, end_time')->should_be_approved()->get_results();
        if(!$bookings) continue;
        foreach($bookings as $booking){
          echo $booking->id.' | ';
          OsRemindersHelper::send_reminder($reminder, $booking->id);
          echo '<br/>';
        }
      }
    }
  }

  public static function allowed_fields(){
    $allowed_params = array(
      'name',
      'receiver',
      'value',
      'unit',
      'when',
      'medium',
      'subject',
      'content',
      'id');
    return $allowed_params;
  }

  public static function prepare_to_save($array_to_filter){
    return $array_to_filter;
  }

  public static function send_reminder($reminder, $booking_id){
    $sent_reminders_model = new OsSentReminderModel();
    $is_sent_already = $sent_reminders_model->select('id')->where(['booking_id' => $booking_id, 'reminder_id' => $reminder['id']])->set_limit(1)->get_results();
    if($is_sent_already){
      echo 'Already Sent';
      return true;
    }

    if($reminder['medium'] == 'sms'){
      echo ' | Sending SMS';
      $smser = new OsSmser();
      $smser->send_reminder($reminder, $booking_id);
    }elseif($reminder['medium'] == 'email'){
      echo ' | Sending Email';
      $mailer = new OsMailer();
      $mailer->send_reminder($reminder, $booking_id);
    }
    self::log_reminder($reminder, $booking_id);
  }

  public static function log_reminder($reminder, $booking_id){
    $sent_reminder = new OsSentReminderModel();
    $sent_reminder->reminder_id = $reminder['id'];
    $sent_reminder->booking_id = $booking_id;
    $sent_reminder->save();
  }

  public static function has_validation_errors($reminder){
  	$errors = [];
  	if(empty($reminder['name'])) $errors[] = __('Reminder name can not be blank', 'latepoint');
    if(empty($reminder['unit'])) $errors[] = __('Unit has to be selected', 'latepoint');
    if(empty($reminder['when'])) $errors[] = __('Before or after has to be selected', 'latepoint');
    if(empty($reminder['medium'])) $errors[] = __('Select if SMS or Email Reminder', 'latepoint');
    if(empty($reminder['content'])) $errors[] = __('Reminder message content can not be blank', 'latepoint');
  	if(empty($reminder['value'])){
  		$errors[] = __('Value has to be greater than zero', 'latepoint');
  	}
  	if(empty($errors)){
  		return false;
  	}else{
  		return $errors;
  	}
  }

  public static function save($reminder){
    $reminders = OsRemindersHelper::get_reminders_arr();
    if(!isset($reminder['id']) || empty($reminder['id'])){
    	$reminder['id'] = OsRemindersHelper::generate_reminder_id();
    }
    $reminders[$reminder['id']] = $reminder;
    return OsRemindersHelper::save_reminders_arr($reminders);
  }

  public static function delete($reminder_id){
    if(isset($reminder_id) && !empty($reminder_id)){
	    $reminders = OsRemindersHelper::get_reminders_arr();
	    unset($reminders[$reminder_id]);
	    return OsRemindersHelper::save_reminders_arr($reminders);
	  }else{
	  	return false;
	  }
  }

  public static function generate_reminder_id(){
  	return 'rem_'.OsUtilHelper::random_text('alnum', 8);
  }

  public static function get_reminders_arr(){
    $reminders = OsSettingsHelper::get_settings_value('reminders', false);
    if($reminders){
	  	return json_decode($reminders, true);
    }else{
    	return [];
    }
  }

  public static function save_reminders_arr($reminders_arr){
    $reminders_arr = self::prepare_to_save($reminders_arr);
    return OsSettingsHelper::save_setting_by_name('reminders', json_encode($reminders_arr));
  }

}
