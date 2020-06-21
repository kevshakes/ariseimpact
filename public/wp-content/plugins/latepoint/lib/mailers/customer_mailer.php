<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsCustomerMailer' ) ) :


  class OsCustomerMailer extends OsMailer {



    function __construct(){
      parent::__construct();
      $this->views_folder = LATEPOINT_VIEWS_MAILERS_ABSPATH . 'customer/';
    }


    // NEW BOOKING 

    function booking_confirmation($booking){
    	$this->vars['customer'] = $booking->customer;
      $this->vars['booking'] = $booking;
      $this->vars['agent'] = $booking->agent;
      $to = $booking->customer->email;
      $message = $this->booking_confirmation_content();
      $subject = $this->booking_confirmation_subject();
      $subject = OsReplacerHelper::replace_all_vars($subject, array('customer' => $booking->customer, 'agent' => $booking->agent, 'booking' => $booking));
      $message = OsReplacerHelper::replace_all_vars($message, array('customer' => $booking->customer, 'agent' => $booking->agent, 'booking' => $booking));
      wp_mail($to, $subject, $message, $this->headers);
    }

    function booking_confirmation_subject(){
      $default = __('Appointment Confirmation', 'latepoint');
      return OsSettingsHelper::get_settings_value('notification_customer_booking_confirmation_subject', $default);
    }

    function booking_confirmation_content(){
      $content =  OsSettingsHelper::get_settings_value('notification_customer_booking_confirmation_content');
      if(!$content){
        return $this->render('booking_confirmation');
      }else{
        return $content;
      }
    }



    // CHANGE STATUS

    function booking_status_changed_notification($customer, $booking, $old_status = ''){
      $this->vars['agent'] = $booking->agent;
      $this->vars['customer'] = $customer;
      $this->vars['booking'] = $booking;
      $to = $customer->email;
      $subject = $this->booking_status_changed_notification_subject();
      $message = $this->booking_status_changed_notification_content();
      $subject = OsReplacerHelper::replace_all_vars($subject, array('customer' => $customer, 'agent' => $booking->agent, 'booking' => $booking, 'other_vars' => array('old_status' => $old_status)));
      $message = OsReplacerHelper::replace_all_vars($message, array('customer' => $customer, 'agent' => $booking->agent, 'booking' => $booking, 'other_vars' => array('old_status' => $old_status)));
      wp_mail($to, $subject, $message, $this->headers);
    }

    function booking_status_changed_notification_subject(){
      $default = __('Appointment status changed', 'latepoint');
      return OsSettingsHelper::get_settings_value('notification_customer_booking_status_changed_notification_subject', $default);
    }

    function booking_status_changed_notification_content(){
      $content =  OsSettingsHelper::get_settings_value('notification_customer_booking_status_changed_notification_content');
      if(!$content){
        return $this->render('booking_status_changed_notification');
      }else{
        return $content;
      }
    }



    // PASSWORD RESET TOKEN

    function password_reset_request($customer, $token){
      $this->vars['customer'] = $customer;
      $to = $customer->email;
      $subject = $this->password_reset_request_subject();
      $message = $this->password_reset_request_content();
      $subject = OsReplacerHelper::replace_all_vars($subject, array('customer' => $customer, 'other_vars' => ['token' => $token]));
      $message = OsReplacerHelper::replace_all_vars($message, array('customer' => $customer, 'other_vars' => ['token' => $token]));
      return wp_mail($to, $subject, $message, $this->headers);
    }

    function password_reset_request_subject(){
      $default = __('Reset Your Password', 'latepoint');
      return OsSettingsHelper::get_settings_value('email_customer_password_reset_request_subject', $default);
    }

    function password_reset_request_content(){
      $content =  OsSettingsHelper::get_settings_value('email_customer_password_reset_request_content');
      if(!$content){
        return $this->render('password_reset_request');
      }else{
        return $content;
      }
    }
	}

endif;