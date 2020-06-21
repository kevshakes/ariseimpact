<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsCustomerSmser' ) ) :


  class OsCustomerSmser extends OsSmser {

    function __construct(){
      parent::__construct();
    }

    function booking_confirmation($booking){
      $message = OsNotificationsHelper::customer_booking_confirmation_sms_message();
      $message = OsReplacerHelper::replace_all_vars($message, array('customer' => $booking->customer, 'agent' => $booking->agent, 'booking' => $booking));
      try {
        $this->send_sms($booking->customer->phone, $message);
        return true;
      } catch (Exception $e){
        return false;
      }
    }
	}

endif;