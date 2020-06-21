<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsSmser' ) ) :

class OsSmser {

  protected
  $vars = array(),
  $twilio_sid = '',
  $twilio_token = '',
  $from = '';

  function __construct(){
    $this->from = OsSettingsHelper::get_settings_value('notifications_sms_twilio_phone');
    $this->twilio_sid = OsSettingsHelper::get_settings_value('notifications_sms_twilio_account_sid');
    $this->twilio_token = OsSettingsHelper::get_settings_value('notifications_sms_twilio_auth_token');
  }


  function send_reminder($reminder, $booking_id){
    $booking = new OsBookingModel($booking_id);
    if(empty($booking->id)) return false;

    $this->vars['agent'] = $booking->agent;
    $this->vars['customer'] = $booking->customer;
    $this->vars['booking'] = $booking;

    if($reminder['receiver'] == 'agent'){
      $to = $booking->agent->phone;
    }else{
      $to = $booking->customer->phone;
    }

    $message = $reminder['content'];

    $message = OsReplacerHelper::replace_all_vars($message, array('customer' => $booking->customer, 'agent' => $booking->agent, 'booking' => $booking));
    $this->send_sms($to, $message);
  }

  function send_sms($to, $message){
    if(!OsSettingsHelper::is_sms_allowed()) return false;
    if(!OsSettingsHelper::is_sms_processor_setup()) return false;
    $to = OsUtilHelper::e164format($to);
    if(empty($to)) return false;
    try{
      $client = new Twilio\Rest\Client($this->twilio_sid, $this->twilio_token);
      $client->messages->create(
          $to,
          array(
              'from' => $this->from,
              'body' => $message
          )
      );
    }catch(Exception $e){
      error_log($e->getMessage());
    }
  }

}

endif;