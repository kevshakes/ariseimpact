<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsNotificationsController' ) ) :


  class OsNotificationsController extends OsController {

    function __construct(){
      parent::__construct();
      
      $this->views_folder = LATEPOINT_VIEWS_ABSPATH . 'notifications/';
      $this->vars['page_header'] = OsMenuHelper::get_menu_items_by_id('notifications');
      $this->vars['breadcrumbs'][] = array('label' => __('Notifications', 'latepoint'), 'link' => OsRouterHelper::build_link(OsRouterHelper::build_route_name('notifications', 'settings') ) );
    }


    public function send_sms(){

      $this->vars['breadcrumbs'][] = array('label' => __('SMS Settings', 'latepoint'), 'link' => false );

      $sid = '';
      $token = '';

      // $client = new Twilio\Rest\Client($sid, $token);

      // Use the client to do fun stuff like send text messages!
      // $client->messages->create(
      //     // the number you'd like to send the message to
      //     '+1111111111',
      //     array(
      //         // A Twilio phone number you purchased at twilio.com/console
      //         'from' => '+1111111111',
      //         // the body of the text message you'd like to send
      //         'body' => 'Testing'
      //     )
      // );
      $this->format_render(__FUNCTION__);
    }


    public function settings(){
      
      $this->vars['breadcrumbs'][] = array('label' => __('Settings', 'latepoint'), 'link' => false );

      $this->format_render(__FUNCTION__);
    }


    public function sms_templates(){
      
      $this->vars['breadcrumbs'][] = array('label' => __('SMS Templates', 'latepoint'), 'link' => false );

      $this->format_render(__FUNCTION__);
    }


    public function email_templates(){
      
      $this->vars['breadcrumbs'][] = array('label' => __('Email Templates', 'latepoint'), 'link' => false );

      $this->format_render(__FUNCTION__);
    }

    public function resend_customer_appointment_confirmation(){
      if($this->params['booking_id']){
        $booking = new OsBookingModel($this->params['booking_id']);
        $status = LATEPOINT_STATUS_SUCCESS;
        OsNotificationsHelper::process_new_booking_notifications($booking);
        $response_html = __('Appointment Confirmation Sent', 'latepoint');
      }else{
        $response_html = $booking->get_error_messages();
        $status = LATEPOINT_STATUS_ERROR;
      }
      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html, 'form_values_to_update' => $form_values_to_update));
      }
    }

  }
endif;