<?php 

class OsReplacerHelper {

  public static function replace_custom_fields_for_customer($text, $customer){
    $custom_fields = OsCustomFieldsHelper::get_custom_fields_arr('customer');
    if(!empty($custom_fields)){
      $needles = [];
      $replacements = [];
      foreach($custom_fields as $custom_field){
        $needles[] = '{'.$custom_field['id'].'}';
        $replacements[] = $customer->get_meta_by_key($custom_field['id'], '');
      }
      $text = str_replace($needles, $replacements, $text);
    }
    return $text;
  }

  public static function replace_customer_vars($text, $customer){
  	$needles = array('{customer_full_name}','{customer_first_name}', '{customer_last_name}', '{customer_email}','{customer_phone}', '{customer_notes}');
  	$replacements = array($customer->full_name, $customer->first_name, $customer->last_name, $customer->email, $customer->formatted_phone, $customer->notes);
  	$text = str_replace($needles, $replacements, $text);
  	return $text;
  }

  public static function replace_agent_vars($text, $agent){
  	$needles = array('{agent_full_name}','{agent_email}','{agent_phone}');
  	$replacements = array($agent->full_name, $agent->email, $agent->formatted_phone);
  	$text = str_replace($needles, $replacements, $text);
  	return $text;
  }

  public static function replace_tracking_vars($text, $booking){
    $needles = ['{booking_id}',
                '{agent_id}',
                '{customer_id}',
                '{service_id}'];
    $replacements = [$booking->id,
                    $booking->agent_id,        
                    $booking->customer_id,        
                    $booking->service_id];    
    $text = str_replace($needles, $replacements, $text);
    $text = apply_filters('latepoint_replace_tracking_vars', $text, $booking);
    return $text;    

  }

  public static function replace_booking_vars($text, $booking){
  	$needles = ['{booking_id}',
                '{service_name}',
                '{start_date}',
                '{start_time}',
                '{end_time}',
                '{booking_status}', 
                '{location_name}', 
                '{location_full_address}', 
                '{booking_duration}', 
                '{booking_price}', 
                '{booking_payment_portion}', 
                '{booking_payment_method}', 
                '{booking_payment_amount}'];
    $total_duration = ($booking->get_total_duration() > 0) ? $booking->get_total_duration().' '.__('minutes', 'latepoint') : __('n/a', 'latepoint');
  	$replacements = [$booking->id,
                      $booking->service->name, 
                      $booking->nice_start_date, 
                      $booking->nice_start_time, 
                      $booking->nice_end_time, 
                      $booking->nice_status, 
                      $booking->location->name, 
                      $booking->location->full_address, 
                      $total_duration, 
                      OsMoneyHelper::format_price($booking->price), 
                      $booking->get_payment_portion_nice_name(), 
                      $booking->get_payment_method_nice_name(),
                      OsMoneyHelper::format_price($booking->get_total_amount_paid_from_transactions())];
    $text = str_replace($needles, $replacements, $text);
    $text = apply_filters('latepoint_replace_booking_vars', $text, $booking);
  	return $text;
  }

  public static function replace_other_vars($text, $other_vars){
    if(isset($other_vars['old_status'])){
      $text = str_replace('{booking_old_status}', $other_vars['old_status'], $text);
    }
    if(isset($other_vars['token'])){
      $text = str_replace('{token}', $other_vars['token'], $text);
    }
    return $text;
  }

  public static function replace_all_vars($text, $vars){
  	if(isset($vars['booking'])) $text = self::replace_booking_vars($text, $vars['booking']);
  	if(isset($vars['customer'])){
      $text = self::replace_customer_vars($text, $vars['customer']);
      $text = self::replace_custom_fields_for_customer($text, $vars['customer']);
    }
    if(isset($vars['agent'])) $text = self::replace_agent_vars($text, $vars['agent']);
  	if(isset($vars['other_vars'])) $text = self::replace_other_vars($text, $vars['other_vars']);
  	return $text;
  }
}