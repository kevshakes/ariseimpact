<?php 

class OsPaymentsStripeHelper {

  public static function charge_by_token($token, $booking, $customer){
    $result = ['message' => '', 'status' => ''];
    if(!OsSettingsHelper::is_env_payments_live()) $token = 'tok_mastercard';
    if(isset($token) && !empty($token)){
      try {
        $stripe_customer_id = $customer->get_meta_by_key('stripe_customer_id');
        if($stripe_customer_id){
          // has stripe customer id
          $stripe_customer = OsPaymentsStripeHelper::update_customer($stripe_customer_id, $token, $customer, array('source' => $token));
        }else{
          // does not have stripe customer id
          $stripe_customer = OsPaymentsStripeHelper::create_customer($customer, $token);
        }
        $customer->save_meta_by_key('stripe_customer_id', $stripe_customer->id);

        $booking->status = LATEPOINT_BOOKING_STATUS_PAYMENT_PENDING;

        $stripe_charge = OsPaymentsStripeHelper::create_charge($stripe_customer->id, $booking->specs_calculate_price_to_charge(LATEPOINT_PAYMENT_METHOD_CARD));
        $result['charge_id'] = $stripe_charge->id;
        
        $booking->status = OsBookingHelper::get_default_booking_status();
        $result['message'] = __('Payment was processed successfully', 'latepoint');
        $result['status'] = LATEPOINT_STATUS_SUCCESS;

      } catch(\Stripe\Error\Card $e) {
        // Since it's a decline, \Stripe\Error\Card will be caught
        $body = $e->getJsonBody();
        $err  = $body['error'];
        $result['status'] = LATEPOINT_STATUS_ERROR;
        $result['message'] = 'Error! ' . $err['message'];
        OsDebugHelper::log_stripe_exception($e);
      } catch (\Stripe\Error\RateLimit $e) {
        // Too many requests made to the API too quickly
        $result['status'] = LATEPOINT_STATUS_ERROR;
        $result['message'] = __('Error! KS98324H', 'latepoint');
        OsDebugHelper::log_stripe_exception($e);
      } catch (\Stripe\Error\InvalidRequest $e) {
        // Invalid parameters were supplied to Stripe's API
        $result['status'] = LATEPOINT_STATUS_ERROR;
        $result['message'] = __('Error! KF732493', 'latepoint');
        OsDebugHelper::log_stripe_exception($e);
      } catch (\Stripe\Error\Authentication $e) {
        // Authentication with Stripe's API failed (maybe you changed API keys recently)
        $result['status'] = LATEPOINT_STATUS_ERROR;
        $result['message'] = __('Error! AU38F834', 'latepoint');
        OsDebugHelper::log_stripe_exception($e);
      } catch (\Stripe\Error\ApiConnection $e) {
        // Network communication with Stripe failed
        $result['status'] = LATEPOINT_STATUS_ERROR;
        $result['message'] = __('Error! JS8234HS', 'latepoint');
        OsDebugHelper::log_stripe_exception($e);
      } catch (\Stripe\Error\Base $e) {
        // Display a very generic error to the user, and maybe send yourself an email
        $result['status'] = LATEPOINT_STATUS_ERROR;
        $result['message'] = __('Error! SU8324HS', 'latepoint');
        OsDebugHelper::log_stripe_exception($e);
      } catch (Exception $e) {
        // Something else happened, completely unrelated to Stripe
        $result['message'] = __('Unknown Error!', 'latepoint');
        $result['status'] = LATEPOINT_STATUS_ERROR;
      }
    }else{
      $result['status'] = LATEPOINT_STATUS_ERROR;
      $result['message'] = _e('Card information is invalid', 'latepoint');
    }
    return $result;
  }

  private static function get_properties_allowed_to_update($roles = 'admin'){
    return array('source', 'email');
  }

	public static function get_publishable_key(){
		return OsSettingsHelper::get_settings_value('stripe_publishable_key', '');
	}

	public static function get_secret_key(){
		return OsSettingsHelper::get_settings_value('stripe_secret_key');
	}

	public static function set_api_key(){
		if(self::get_secret_key()){
	    \Stripe\Stripe::setApiKey(self::get_secret_key());
		}
	}

  public static function update_customer($stripe_customer_id, $token, $customer, $values_to_update = array()){
    $stripe_customer = \Stripe\Customer::retrieve($stripe_customer_id);
    if($stripe_customer){
      foreach($values_to_update as $key => $value){
        if(in_array($key, self::get_properties_allowed_to_update())){
          $stripe_customer->$key = $value;
        }
      }
      $stripe_customer->save();
    }
    return $stripe_customer;
  }

	public static function create_customer($customer, $token){
      $stripe_customer = \Stripe\Customer::create([
          'email' => $customer->email,
          'source'  => $token,
      ]);
      return $stripe_customer;
	}

	public static function create_charge($stripe_customer_id, $amount){
    $stripe_charge = \Stripe\Charge::create([
        'customer' => $stripe_customer_id,
        'amount'   => $amount,
        'currency' => OsSettingsHelper::get_stripe_currency_iso_code(),
    ]);
    return $stripe_charge;
  }

  public static function zero_decimal_currencies_list(){
    return array('bif','clp','djf','gnf','jpy','kmf','krw','mga','pyg','rwf','ugx','vnd','vuv','xaf','xof','xpf');
  }

  public static function convert_charge_amount_to_requirements($charge_amount){
    $iso_code = OsSettingsHelper::get_stripe_currency_iso_code();
    if(in_array($iso_code, self::zero_decimal_currencies_list())){
      return round($charge_amount);
    }else{
      return $charge_amount * 100;
    }
  }

  public static function load_countries_list(){
  	$country_codes = ['AU' => 'Australia',
                      'AT' => 'Austria',
                      'BE' => 'Belgium',
                      'BR' => 'Brazil',
                      'CA' => 'Canada',
                      'DK' => 'Denmark',
                      'EE' => 'Estonia',
                      'FI' => 'Finland',
                      'FR' => 'France',
                      'DE' => 'Germany',
                      'GR' => 'Greece',
                      'HK' => 'Hong Kong',
                      'IN' => 'India',
                      'IE' => 'Ireland',
                      'IT' => 'Italy',
                      'JP' => 'Japan',
                      'LV' => 'Latvia',
                      'LT' => 'Lithuania',
                      'LU' => 'Luxembourg',
                      'MY' => 'Malaysia',
                      'MX' => 'Mexico',
                      'NL' => 'Netherlands',
                      'NZ' => 'New Zealand',
                      'NO' => 'Norway',
                      'PL' => 'Poland',
                      'PT' => 'Portugal',
                      'SG' => 'Singapore',
                      'SK' => 'Slovakia',
                      'SI' => 'Slovenia',
                      'ES' => 'Spain',
                      'SE' => 'Sweden',
                      'CH' => 'Switzerland',
                      'GB' => 'United Kingdom',
                      'US' => 'United States'];
  	return $country_codes;
  }

  public static function load_country_currencies_list($country_code){
    $currency_list = array(
      'currencies' => array('usd','aed','afn','all','amd','ang','aoa','ars','aud','awg','azn','bam','bbd','bdt','bgn','bif','bmd','bnd','bob','brl','bsd','bwp','bzd','cad','cdf','chf','clp','cny','cop','crc','cve','czk','djf','dkk','dop','dzd','egp','etb','eur','fjd','fkp','gbp','gel','gip','gmd','gnf','gtq','gyd','hkd','hnl','hrk','htg','huf','idr','ils','inr','isk','jmd','jpy','kes','kgs','khr','kmf','krw','kyd','kzt','lak','lbp','lkr','lrd','lsl','mad','mdl','mga','mkd','mmk','mnt','mop','mro','mur','mvr','mwk','mxn','myr','mzn','nad','ngn','nio','nok','npr','nzd','pab','pen','pgk','php','pkr','pln','pyg','qar','ron','rsd','rub','rwf','sar','sbd','scr','sek','sgd','shp','sll','sos','srd','std','svc','szl','thb','tjs','top','try','ttd','twd','tzs','uah','ugx','uyu','uzs','vnd','vuv','wst','xaf','xcd','xof','xpf','yer','zar','zmw'), 
      'default_currency' => LATEPOINT_DEFAULT_STRIPE_CURRENCY_ISO_CODE
    );
    try {
    	$country_info = \Stripe\CountrySpec::retrieve($country_code);
      if(isset($country_info['default_currency'])) $currency_list['default_currency'] = $country_info['default_currency'];
      if(isset($country_info['supported_payment_currencies'])) $currency_list['currencies'] = $country_info['supported_payment_currencies'];
    }catch(Exception $e){
    }
  	return $currency_list;
  }

  public static function load_countries_full_data_list(){
  	$countries = \Stripe\CountrySpec::all(["limit" => 100]);
  	$countries_formatted = array();
	  foreach($countries['data'] as $country){
	    $countries_formatted[$country->id]['currencies'] = $country['supported_payment_currencies'];
	    $countries_formatted[$country->id]['default_currency'] = $country['default_currency'];
	  }
  	return $countries_formatted;
  }
}