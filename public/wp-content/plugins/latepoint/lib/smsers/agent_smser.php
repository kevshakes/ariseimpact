<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsAgentSmser' ) ) :


  class OsAgentSmser extends OsSmser {

    function __construct(){
      parent::__construct();
    }

    function new_booking_notification($agent, $booking){
      $message = OsNotificationsHelper::agent_new_booking_notification_sms_message();
      $message = OsReplacerHelper::replace_all_vars($message, array('customer' => $booking->customer, 'agent' => $agent, 'booking' => $booking));
      try {
        $this->send_sms($agent->phone, $message);
        if(!empty($agent->extra_phones)){
          $extra_phones_arr = explode(',', $agent->extra_phones);
          if(!empty($extra_phones_arr)){
            foreach($extra_phones_arr as $extra_phone){
              $this->send_sms($extra_phone, $message);
            }
          }
        }
        return true;
      } catch (Exception $e){
        return false;
      }
    }
	}

endif;