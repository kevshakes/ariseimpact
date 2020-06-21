<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsAgentMailer' ) ) :


  class OsAgentMailer extends OsMailer {

    function __construct(){
      parent::__construct();
      $this->views_folder = LATEPOINT_VIEWS_MAILERS_ABSPATH . 'agent/';
    }


    // NEW BOOKING

    function new_booking_notification($agent, $booking){
      $this->vars['agent'] = $agent;
    	$this->vars['customer'] = $booking->customer;
    	$this->vars['booking'] = $booking;
      $to = $agent->email;
      if(!empty($agent->extra_emails)) $to.= ', '.$agent->extra_emails;
      $subject = $this->new_booking_notification_subject();
      $message = $this->new_booking_notification_content();
      $subject = OsReplacerHelper::replace_all_vars($subject, array('customer' => $booking->customer, 'agent' => $agent, 'booking' => $booking));
      $message = OsReplacerHelper::replace_all_vars($message, array('customer' => $booking->customer, 'agent' => $agent, 'booking' => $booking));
      wp_mail($to, $subject, $message, $this->headers);
    }

    function new_booking_notification_subject(){
      $default = __('New Appointment Notification', 'latepoint');
      return OsSettingsHelper::get_settings_value('notification_agent_new_booking_notification_subject', $default);
    }

    function new_booking_notification_content(){
      $content =  OsSettingsHelper::get_settings_value('notification_agent_new_booking_notification_content');
      if(!$content){
        return $this->render('new_booking_notification');
      }else{
        return $content;
      }
    }



    // CHANGE STATUS

    function booking_status_changed_notification($agent, $booking, $old_status = ''){
      $this->vars['agent'] = $agent;
      $this->vars['customer'] = $booking->customer;
      $this->vars['booking'] = $booking;
      $to = $agent->email;
      if(!empty($agent->extra_emails)) $to.= ', '.$agent->extra_emails;
      $subject = $this->booking_status_changed_notification_subject();
      $message = $this->booking_status_changed_notification_content();
      $subject = OsReplacerHelper::replace_all_vars($subject, array('customer' => $booking->customer, 'agent' => $agent, 'booking' => $booking, 'other_vars' => array('old_status' => $old_status)));
      $message = OsReplacerHelper::replace_all_vars($message, array('customer' => $booking->customer, 'agent' => $agent, 'booking' => $booking, 'other_vars' => array('old_status' => $old_status)));
      wp_mail($to, $subject, $message, $this->headers);
    }

    function booking_status_changed_notification_subject(){
      $default = __('Appointment status changed', 'latepoint');
      return OsSettingsHelper::get_settings_value('notification_agent_booking_status_changed_notification_subject', $default);
    }

    function booking_status_changed_notification_content(){
      $content =  OsSettingsHelper::get_settings_value('notification_agent_booking_status_changed_notification_content');
      if(!$content){
        return $this->render('booking_status_changed_notification');
      }else{
        return $content;
      }
    }
	}

endif;