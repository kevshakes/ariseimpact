<?php 

class OsNotificationsHelper {


  // EMAIL NOTIFICATION SENDING


  // - NEW BOOKING
  public static function send_agent_new_appointment_notification($booking){
    $agent_mailer = new OsAgentMailer();
    $agent_mailer->new_booking_notification($booking->agent, $booking);
  }

  public static function send_customer_new_appointment_notification($booking){
    $customer_mailer = new OsCustomerMailer();
    $customer_mailer->booking_confirmation($booking);
  }


  // - BOOKING STATUS CHANGED
  public static function send_agent_booking_status_changed_notification($booking, $old_status = ''){
    $agent_mailer = new OsAgentMailer();
    $agent_mailer->booking_status_changed_notification($booking->agent, $booking, $old_status);
  }

  public static function send_customer_booking_status_changed_notification($booking, $old_status = ''){
    $customer_mailer = new OsCustomerMailer();
    $customer_mailer->booking_status_changed_notification($booking->customer, $booking, $old_status);
  }

  // SMS NOTIFICATION SENDING

  public static function send_agent_new_appointment_notification_sms($booking){
    $agent_smser = new OsAgentSmser();
    $agent_smser->new_booking_notification($booking->agent, $booking);
  }

  public static function send_customer_new_appointment_notification_sms($booking){
    $customer_smser = new OsCustomerSmser();
    $customer_smser->booking_confirmation($booking);
  }


  // PROCESS DIFFERENT SCENARIOS OF NOTIFICATIONS

  public static function process_update_booking_notifications($booking){
  }


  public static function process_new_booking_notifications($booking){
    if(OsSettingsHelper::get_settings_value('notifications_email') == 'on'){
      // send to customer
      if(OsSettingsHelper::get_settings_value('notification_customer_confirmation') == 'on'){
        self::send_customer_new_appointment_notification($booking);
      }
      // send to agent
      if(OsSettingsHelper::get_settings_value('notification_agent_confirmation') == 'on'){
        self::send_agent_new_appointment_notification($booking);
      }
    }
    if(OsSettingsHelper::is_sms_notifications_enabled()){
      // send to agent
      if(OsSettingsHelper::get_settings_value('notification_sms_agent_confirmation') == 'on'){
        self::send_agent_new_appointment_notification_sms($booking);
      }
      // send to customer
      if(OsSettingsHelper::get_settings_value('notification_sms_customer_confirmation') == 'on'){
        self::send_customer_new_appointment_notification_sms($booking);
      }
    }
  }

  public static function process_booking_status_changed_notifications($booking, $old_status = ''){
    if(OsSettingsHelper::get_settings_value('notifications_email') == 'on'){
      // send to customer
      if(OsSettingsHelper::get_settings_value('notification_customer_booking_status_changed') == 'on'){
        self::send_customer_booking_status_changed_notification($booking, $old_status);
      }
      // send to agent
      if(OsSettingsHelper::get_settings_value('notification_agent_booking_status_changed') == 'on'){
        self::send_agent_booking_status_changed_notification($booking, $old_status);
      }
    }
    // TODO ADD SMS NOTIFICATIONS
  }


  public static function process_new_customer_notifications($customer){

  }



  // ------------------------
  // ------------------------
  // EMAIL TEMPLATES
  // ------------------------
  // ------------------------


  // AGENT
  // ----------
  // New Booking
  public static function agent_new_booking_notification_subject(){
    $agent_mailer = new OsAgentMailer();
    return $agent_mailer->new_booking_notification_subject();
  }

  public static function agent_new_booking_notification_content(){
    $agent_mailer = new OsAgentMailer();
    return $agent_mailer->new_booking_notification_content();
  }

  // Status Change
  public static function agent_booking_status_changed_notification_subject(){
    $agent_mailer = new OsAgentMailer();
    return $agent_mailer->booking_status_changed_notification_subject();
  }

  public static function agent_booking_status_changed_notification_content(){
    $agent_mailer = new OsAgentMailer();
    return $agent_mailer->booking_status_changed_notification_content();
  }



  // CUSTOMER
  // ----------

  // New Booking
  public static function customer_booking_confirmation_subject(){
    $customer_mailer = new OsCustomerMailer();
    return $customer_mailer->booking_confirmation_subject();
  }

  public static function customer_booking_confirmation_content(){
    $customer_mailer = new OsCustomerMailer();
    return $customer_mailer->booking_confirmation_content();
  }


  // Status Change
  public static function customer_booking_status_changed_notification_subject(){
    $customer_mailer = new OsCustomerMailer();
    return $customer_mailer->booking_status_changed_notification_subject();
  }

  public static function customer_booking_status_changed_notification_content(){
    $customer_mailer = new OsCustomerMailer();
    return $customer_mailer->booking_status_changed_notification_content();
  }


  // Password Reset Request
  public static function customer_password_reset_request_subject(){
    $customer_mailer = new OsCustomerMailer();
    return $customer_mailer->password_reset_request_subject();
  }

  public static function customer_password_reset_request_content(){
    $customer_mailer = new OsCustomerMailer();
    return $customer_mailer->password_reset_request_content();
  }


  // ------------------------
  // ------------------------
  // SMS MESSAGES
  // ------------------------
  // ------------------------

  public static function agent_new_booking_notification_sms_message(){
    $default = 'You have a new {service_name} appointment on {start_date} at {start_time}';
    return OsSettingsHelper::get_settings_value('notification_sms_agent_new_booking_notification_message', $default);
  }

  public static function customer_booking_confirmation_sms_message(){
    $default = 'Appointment booked for {service_name} on {start_date} at {start_time}';
    return OsSettingsHelper::get_settings_value('notification_sms_customer_booking_confirmation_message', $default);
  }


  
  
}