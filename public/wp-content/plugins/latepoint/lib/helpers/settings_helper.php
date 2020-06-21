<?php 

class OsSettingsHelper {
  
  public static $loaded_values;


  private static $encrypted_settings = ['license', 
                                        'google_calendar_client_secret', 
                                        'facebook_app_secret', 
                                        'google_client_secret', 
                                        'notifications_sms_twilio_auth_token', 
                                        'stripe_secret_key', 
                                        'braintree_secret_key', 
                                        'braintree_merchant_id',
                                        'paypal_client_secret'];

  private static $settings_to_autoload = ['enable_google_login',
                                'time_system',
                                'date_format',
                                'currency_symbol_before',
                                'currency_symbol_after',
                                'disable_phone_formatting',
                                'enable_payments_stripe',
                                'enable_payments_braintree',
                                'phone_format',
                                'steps_show_timezone_selector',
                                'show_booking_end_time',
                                'stripe_publishable_key',
                                'enable_payments_paypal',
                                'enable_facebook_login',
                                'earliest_possible_booking',
                                'enable_payments',
                                'enable_payments_cc',
                                'enable_payments_local',
                                'color_scheme_for_booking_form',
                                'steps_support_text',
                                'latest_possible_booking',
                                'facebook_app_id',
                                'google_client_id',
                                'paypal_client_id',
                                'paypal_currency_iso_code',
                                'paypal_use_braintree_api',
                                'stripe_secret_key',
                                'steps_hide_agent_info'];

  private static $defaults = [
    'date_format' => LATEPOINT_DEFAULT_DATE_FORMAT,
    'time_system' => LATEPOINT_DEFAULT_TIME_SYSTEM,
    'currency_symbol_before' => '$',
    'disable_phone_formatting' => 'off',
  ];

  public static function run_autoload(){
    // set defaults
    foreach(self::$defaults as $name => $default){
      self::$loaded_values[$name] = $default;
    }

    $settings_model = new OsSettingsModel();
    $settings_arr = $settings_model->select('name, value')->where(array('name' => self::$settings_to_autoload))->get_results();


    if($settings_arr && is_array($settings_arr)){
      foreach($settings_arr as $setting){
        if(in_array($setting->name, self::$encrypted_settings)){
          self::$loaded_values[$setting->name] = OsEncryptHelper::decrypt_value($setting->value);
        }else{
          self::$loaded_values[$setting->name] = $setting->value;
        }
      }
    }
  }


  // ENVIRONMENT SETTINGS

  // BASE ENVIRONMENT
  public static function is_env_live(){
    return (LATEPOINT_ENV == LATEPOINT_ENV_LIVE);
  }

  public static function is_env_dev(){
    return (LATEPOINT_ENV == LATEPOINT_ENV_DEV);
  }

  public static function is_env_demo(){
    return (LATEPOINT_ENV == LATEPOINT_ENV_DEMO);
  }

  // SMS, EMAILS

  public static function is_sms_allowed(){
    return LATEPOINT_ALLOW_SMS;
  }

  public static function is_email_allowed(){
    return LATEPOINT_ALLOW_EMAILS;
  }

  // PAYMENTS ENVIRONMENT
  public static function is_env_payments_live(){
    return (self::get_payments_environment() == LATEPOINT_ENV_LIVE);
  }

  public static function is_env_payments_dev(){
    return (self::get_payments_environment() == LATEPOINT_ENV_DEV);
  }

  public static function is_env_payments_demo(){
    return (self::get_payments_environment() == LATEPOINT_ENV_DEMO);
  }

  public static function get_payments_environment(){
    return self::get_settings_value('payments_environment', LATEPOINT_ENV_LIVE);
  }

  public static function set_menu_layout_style($layout){
    $_SESSION['menu_layout_style'] = $layout;
  }

  public static function get_menu_layout_style(){
    if(!isset($_SESSION['menu_layout_style'])){
      $_SESSION['menu_layout_style'] = 'full';
    }
    return isset($_SESSION['menu_layout_style']) ? $_SESSION['menu_layout_style'] : 'full';
  }

  public static function is_accepting_payments(){
    return self::is_on('enable_payments');
  }

  public static function is_accepting_payments_cards(){
    return self::is_on('enable_payments_cc');
  }

  public static function is_accepting_payments_paypal(){
    return self::is_on('enable_payments_paypal');
  }

  public static function is_accepting_payments_local(){
    return self::is_on('enable_payments_local');
  }

  public static function get_time_system(){
    return self::get_settings_value('time_system', LATEPOINT_DEFAULT_TIME_SYSTEM);
  }

  public static function get_date_format(){
    return self::get_settings_value('date_format', LATEPOINT_DEFAULT_DATE_FORMAT);
  }

  public static function get_readable_datetime_format($no_year = false){
    return self::get_readable_date_format($no_year).', '.self::get_readable_time_format();
  }

  public static function get_readable_time_format(){
    $format = (self::get_time_system() == '12') ? 'g:i a' : 'G:i';
    return $format;
  }

  public static function get_readable_date_format($no_year = false){
    if(OsSettingsHelper::is_on('disable_verbose_date_output')) return self::get_date_format();
    $format = ($no_year) ? 'F j' : 'M j, Y';
    switch (self::get_date_format()) {
      case 'm/d/Y':
      case 'm.d.Y':
        $format = ($no_year) ? 'F j' : 'M j, Y';
        break;
      case 'd.m.Y':
      case 'd/m/Y':
        $format = ($no_year) ? 'j F' : 'j M, Y';
        break;
      case 'Y-m-d':
        $format = ($no_year) ? 'F j' : 'Y, M j';
        break;
    }
    return $format;
  }

  public static function get_payment_methods(){
    $payment_methods = [];
    if(self::is_accepting_payments()){
      if(self::is_accepting_payments_cards()) $payment_methods[] = 'cards';
      if(self::is_accepting_payments_paypal()) $payment_methods[] = 'paypal';
      if(self::is_accepting_payments_local()) $payment_methods[] = 'local';
    }
    return $payment_methods;
  }

  public static function can_process_payments(){
    return (self::is_using_stripe_payments() || self::is_using_braintree_payments());
  }

  public static function is_using_stripe_payments(){
    return (self::get_settings_value('enable_payments_stripe') == 'on');
  }

  public static function is_using_braintree_payments(){
    return (self::get_settings_value('enable_payments_braintree') == 'on');
  }

  public static function is_using_paypal_braintree_payments(){
    return (self::is_on('enable_payments_braintree') && self::is_on('enable_payments_paypal') && self::is_on('paypal_use_braintree_api'));
  }

  public static function is_using_paypal_native_payments(){
    return (self::is_on('enable_payments_paypal') && !self::is_on('paypal_use_braintree_api'));
  }

  public static function is_sms_processor_setup(){
    $phone = self::get_settings_value('notifications_sms_twilio_phone');
    $account_id = self::get_settings_value('notifications_sms_twilio_account_sid');
    $auth_token = self::get_settings_value('notifications_sms_twilio_auth_token');
    return (!empty($phone) && !empty($account_id) && !empty($auth_token));
  }

  public static function is_sms_notifications_enabled(){
    return self::is_on('notifications_sms');
  }

  public static function is_using_google_login(){
    return self::is_on('enable_google_login');
  }

  public static function is_using_facebook_login(){
    return self::is_on('enable_facebook_login');
  }

  public static function get_steps_support_text(){
    $default = '<h5>Questions?</h5><p>Call (858) 939-3746 for help</p>';
    return self::get_settings_value('steps_support_text', $default);
  }

  public static function save_setting_by_name($name, $value){
    $settings_model = new OsSettingsModel();
    $settings_model = $settings_model->where(array('name' => $name))->set_limit(1)->get_results_as_models();
    if($settings_model){
      $settings_model->value = self::prepare_value($name, $value);
    }else{
      $settings_model = new OsSettingsModel();
      $settings_model->name = $name;
      $settings_model->value = self::prepare_value($name, $value);
    }
    unset(self::$loaded_values[$name]);
    return $settings_model->save();
  }

  public static function prepare_value($name, $value){
    if(in_array($name, self::$encrypted_settings)){
      $value = OsEncryptHelper::encrypt_value($value);
    }
    return $value;
  }

  public static function get_settings_value($name, $default = false){
    if(isset(self::$loaded_values[$name])) return self::$loaded_values[$name];
    $settings_model = new OsSettingsModel();
    $settings_model = $settings_model->where(array('name' => $name))->set_limit(1)->get_results_as_models();
    if($settings_model){
      if(in_array($name, self::$encrypted_settings)){
        $value = OsEncryptHelper::decrypt_value($settings_model->value);
      }else{
        $value = $settings_model->value;
      }
    }else{
      $value = $default;
    }
    self::$loaded_values[$name] = $value;
    return self::$loaded_values[$name];
  }

  public static function get_stripe_currency_iso_code(){
    return self::get_settings_value('stripe_currency_iso_code', LATEPOINT_DEFAULT_STRIPE_CURRENCY_ISO_CODE);
  }

  public static function get_braintree_currency_iso_code(){
    return self::get_settings_value('braintree_currency_iso_code', LATEPOINT_DEFAULT_BRAINTREE_CURRENCY_ISO_CODE);
  }

  public static function get_paypal_native_currency_iso_code(){
    return self::get_settings_value('paypal_currency_iso_code', LATEPOINT_DEFAULT_PAYPAL_CURRENCY_ISO_CODE);
  }

  public static function get_any_agent_order(){
    return self::get_settings_value('any_agent_order', LATEPOINT_ANY_AGENT_ORDER_RANDOM);
  }

  public static function get_day_calendar_min_height(){
    $height = preg_replace('/\D/', '', self::get_settings_value('day_calendar_min_height', 700));
    if(!$height) $height = 700;
    return $height;
  }

  public static function get_phone_format(){
    $interval = LATEPOINT_DEFAULT_PHONE_FORMAT;
    $settings_value = self::get_settings_value('phone_format');
    if($settings_value) $interval = $settings_value;
    return $interval;
  }

  public static function get_default_timeblock_interval(){
    $timeblock_interval = self::get_settings_value('timeblock_interval', LATEPOINT_DEFAULT_TIMEBLOCK_INTERVAL);
    if(empty($timeblock_interval)) $timeblock_interval = LATEPOINT_DEFAULT_TIMEBLOCK_INTERVAL;
    return $timeblock_interval;
  }

  public static function get_country_phone_code(){
  	$phone_code = LATEPOINT_DEFAULT_PHONE_CODE;
  	$settings_value = self::get_settings_value('country_phone_code');
  	if($settings_value) $phone_code = $settings_value;
  	return $phone_code;
  }

  public static function get_customer_dashboard_url(){
    return self::get_settings_value('page_url_customer_dashboard', '/customer-dashboard');
  }

  public static function get_customer_login_url(){
    return self::get_settings_value('page_url_customer_login', '/customer-login');
  }


  // BOOKING STEPS

  public static function steps_show_service_categories(){
    return (self::get_settings_value('steps_show_service_categories') == 'on');
  }

  public static function steps_show_agent_bio(){
    return (self::get_settings_value('steps_show_agent_bio') == 'on');
  }
  
  public static function get_booking_form_color_scheme(){
    return self::get_settings_value('color_scheme_for_booking_form', 'blue');
  }

  public static function get_booking_form_border_radius(){
    return self::get_settings_value('border_radius', 'rounded');
  }


  public static function is_on($setting){
    return (self::get_settings_value($setting) == 'on');
  }


}

?>