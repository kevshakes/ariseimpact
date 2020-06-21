<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsMailer' ) ) :

class OsMailer {

  protected $views_folder = LATEPOINT_VIEWS_MAILERS_ABSPATH,
  $vars = array(),
  $layout = 'mailer',
  $headers = [];

  public function send_email($to, $subject, $message, $headers){
    if(!OsSettingsHelper::is_email_allowed()) return true;
    return wp_mail($to, $subject, $message, $headers);
  }

  function get_headers(){
    return $this->headers;
  }

  function send_reminder($reminder, $booking_id){
    $booking = new OsBookingModel($booking_id);
    if(empty($booking->id)) return false;

    $this->vars['agent'] = $booking->agent;
    $this->vars['customer'] = $booking->customer;
    $this->vars['booking'] = $booking;

    if($reminder['receiver'] == 'agent'){
      $to = $booking->agent->email;
      // append extra email addresses if agent has them
      if(!empty($booking->agent->extra_emails)) $to.= ', '.$booking->agent->extra_emails;
    }else{
      $to = $booking->customer->email;
    }

    $subject = $reminder['subject'];
    $message = $reminder['content'];

    $subject = OsReplacerHelper::replace_all_vars($subject, array('customer' => $booking->customer, 'agent' => $booking->agent, 'booking' => $booking));
    $message = OsReplacerHelper::replace_all_vars($message, array('customer' => $booking->customer, 'agent' => $booking->agent, 'booking' => $booking));
    $this->send_email($to, $subject, $message, $this->headers);
  }

  function get_view_uri($view_name){
    return $this->views_folder.$view_name.'.php';
  }

  function __construct(){
    $this->headers[] = 'Content-Type: text/html; charset=UTF-8';
    $this->headers[] = 'From: '.get_bloginfo( 'name' ).' <'.get_bloginfo( 'admin_email' ).'>';
    // $this->headers[] = 'From: '.get_bloginfo( 'name' );
  }

  function set_layout($layout = 'mailer'){
    if(isset($this->params['layout'])){
      $this->layout = $this->params['layout'];
    }else{
      $this->layout = $layout;
    }
  }

  function get_layout(){
    return $this->layout;
  }

  function render($view, $extra_vars = array()){
    $view = $this->get_view_uri($view);
    extract($this->vars);
    extract($extra_vars);
    ob_start();
    if($this->get_layout() != 'none'){
      // rendering layout, view variable will be passed and used in layout file
      include LATEPOINT_VIEWS_LAYOUTS_ABSPATH . OsRouterHelper::add_extension($this->get_layout(), '.php');
    }else{
      include OsRouterHelper::add_extension($view, '.php');
    }
    $response_html = ob_get_clean();
    return $response_html;
  }

}

endif;