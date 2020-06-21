<?php 

class OsPaymentsBraintreeHelper {
  public static $gateway;

  public static function charge_by_token($token, $booking, $customer){
    $result = ['message' => '', 'status' => ''];
    if(!OsSettingsHelper::is_env_payments_live() && $booking->payment_method == LATEPOINT_PAYMENT_METHOD_CARD) $token = 'fake-valid-nonce';
    if(isset($token) && !empty($token)){
      try {
        $braintree_customer_id = $customer->get_meta_by_key('braintree_customer_id');
        if($braintree_customer_id){
          // has braintree customer id
          $braintree_customer_id = self::update_customer($braintree_customer_id, $token, $customer);
        }else{
          // does not have braintree customer id
          $braintree_customer_id = self::create_customer($customer, $token);
        }
        $customer->save_meta_by_key('braintree_customer_id', $braintree_customer_id);
        $booking->status = LATEPOINT_BOOKING_STATUS_PAYMENT_PENDING;

        $braintree_charge = self::create_charge($braintree_customer_id, $booking->specs_calculate_price_to_charge(LATEPOINT_PAYMENT_METHOD_CARD), $token);

        $result['charge_id'] = $braintree_charge->id;
        $booking->status = OsBookingHelper::get_default_booking_status();

        $result['message'] = __('Payment was processed successfully', 'latepoint');
        $result['status'] = LATEPOINT_STATUS_SUCCESS;
      } catch (\Braintree\Exception\Authentication $e) {
        $result['status'] = LATEPOINT_STATUS_ERROR;
        $result['message'] = __('Error! LKFDJF834', 'latepoint');
      } catch (Exception $e) {
        OsDebugHelper::log($e->getMessage());
        $result['message'] = __('Unknown Error! IFD38FHS', 'latepoint');
        $result['status'] = LATEPOINT_STATUS_ERROR;
      }
    }else{
      $result['status'] = LATEPOINT_STATUS_ERROR;
      $result['message'] = __('Card information is invalid', 'latepoint');
    }
    return $result;
  }

  private static function get_properties_allowed_to_update($roles = 'admin'){
    return array('source', 'email', 'firstName', 'lastName');
  }

  public static function get_tokenization_key(){
    return OsSettingsHelper::get_settings_value('braintree_tokenization_key', '');
  }

  public static function get_publishable_key(){
    return OsSettingsHelper::get_settings_value('braintree_publishable_key', '');
  }

  public static function get_secret_key(){
    return OsSettingsHelper::get_settings_value('braintree_secret_key');
  }

  public static function get_merchant_id(){
    return OsSettingsHelper::get_settings_value('braintree_merchant_id');
  }

  public static function get_environment_name(){
    $environment_name = (OsSettingsHelper::is_env_payments_live()) ? 'production' : 'sandbox';
    return $environment_name;
  }

  public static function set_api_key(){
    self::$gateway = new Braintree_Gateway([
      'environment' => self::get_environment_name(),
      'merchantId' => self::get_merchant_id(),
      'publicKey' => self::get_publishable_key(),
      'privateKey' => self::get_secret_key()
    ]);
  }

  public static function generate_client_token(){
    return self::$gateway->ClientToken()->generate();
  }

  public static function update_customer($braintree_customer_id, $token, $customer){
    $customer_result = self::$gateway->customer()->update($braintree_customer_id, [
    'firstName' => $customer->first_name,
    'lastName' => $customer->last_name,
    'email' => $customer->email,
    // 'paymentMethodNonce' => $token,
    ]);


    if($customer_result->success){
      return $customer_result->customer->id;
    }else{
      throw new Exception(__('Error KLF83JSKS!', 'latepoint'));
    }
  }

  public static function create_customer($customer, $token){
    $customer_result = self::$gateway->customer()->create([
    'firstName' => $customer->first_name,
    'lastName' => $customer->last_name,
    'email' => $customer->email,
    // 'paymentMethodNonce' => $token,
    ]);
    if($customer_result->success){
      return $customer_result->customer->id;
    }else{
      throw new Exception(__('Error JDFHGOS93!', 'latepoint'));
    }
  }

  public static function create_charge($braintree_customer_id, $amount, $token){
    $result = self::$gateway->transaction()->sale([
      'amount' => $amount,
      'paymentMethodNonce' => $token,
      'customerId' => $braintree_customer_id,
      'options' => [
        'submitForSettlement' => true
      ]
    ]);
    if($result->success){
      return $result->transaction;
    }else{
      foreach($result->errors->deepAll() AS $error) {
        OsDebugHelper::log($error->attribute . ': ' . $error->code . ' ' . $error->message);
      }
      throw new Exception($result->message);
    }
  }

  public static function zero_decimal_currencies_list(){
    return array('BIF','CLP','DJF','GNF','JPY','KMF','KRW','LAK','PYG','RWF','UGX','VND','VUV','XAF','XOF','XPF');
  }

  public static function convert_charge_amount_to_requirements($charge_amount){
    $iso_code = OsSettingsHelper::get_braintree_currency_iso_code();
    if(in_array($iso_code, self::zero_decimal_currencies_list())){
      return $charge_amount;
    }else{
      return $charge_amount;
    }
  }

  public static function load_countries_list(){
    $country_codes = array('AT' => 'AT', 'AU' => 'AU', 'BE' => 'BE', 'CA' => 'CA', 'CH' => 'CH', 'DE' => 'DE', 'DK' => 'DK', 'ES' => 'ES', 'FI' => 'FI', 'FR' => 'FR', 'GB' => 'GB', 'HK' => 'HK', 'IE' => 'IE', 'IT' => 'IT', 'JP' => 'JP', 'LU' => 'LU', 'NL' => 'NL', 'NO' => 'NO', 'NZ' => 'NZ', 'PT' => 'PT', 'RO' => 'RO', 'SE' => 'SE', 'SG' => 'SG', 'US' => 'US');
    return $country_codes;
  }

  public static function load_country_currencies_list($country_code){
    $currency_list = array(
      'currencies' => array('AED','AMD','AOA','ARS','AUD','AWG','AZN','BAM','BBD','BDT','BGN','BIF','BMD','BND','BOB','BRL','BSD','BWP','BYN','BZD','CAD','CHF','CLP','CNY','COP','CRC','CVE','CZK','DJF','DKK','DOP','DZD','EGP','ETB','EUR','FJD','FKP','GBP','GEL','GHS','GIP','GMD','GNF','GTQ','GYD','HKD','HNL','HRK','HTG','HUF','IDR','ILS','INR','ISK','JMD','JPY','KES','KGS','KHR','KMF','KRW','KYD','KZT','LAK','LBP','LKR','LRD','LSL','LTL','MAD','MDL','MKD','MNT','MOP','MUR','MVR','MWK','MXN','MYR','MZN','NAD','NGN','NIO','NOK','NPR','NZD','PAB','PEN','PGK','PHP','PKR','PLN','PYG','QAR','RON','RSD','RUB','RWF','SAR','SBD','SCR','SEK','SGD','SHP','SLL','SOS','SRD','STD','SVC','SYP','SZL','THB','TJS','TOP','TRY','TTD','TWD','TZS','UAH','UGX','USD','UYU','UZS','VES','VND','VUV','WST','XAF','XCD','XOF','XPF','YER','ZAR','ZMK','ZWD'), 
      'default_currency' => LATEPOINT_DEFAULT_BRAINTREE_CURRENCY_ISO_CODE
    );
    return $currency_list;
  }

  public static function load_countries_full_data_list(){
  }
}