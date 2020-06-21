<?php 

class OsPaymentsPaypalHelper {
  public static $gateway;

  public static function get_client(){
    return new PayPalCheckoutSdk\Core\PayPalHttpClient(self::environment());
  }

  public static function environment(){
    $client_id = OsSettingsHelper::get_settings_value('paypal_client_id');
    $client_secret = OsSettingsHelper::get_settings_value('paypal_client_secret');
    if(OsSettingsHelper::is_env_payments_live()){
      return new PayPalCheckoutSdk\Core\ProductionEnvironment($client_id, $client_secret);
    }else{
      return new PayPalCheckoutSdk\Core\SandboxEnvironment($client_id, $client_secret);
    }
  }

  public static function build_request_body(){
    return "{}";
  }

  public static function charge_by_token($token, $booking, $customer){
    $result = ['message' => '', 'status' => ''];

    if(isset($token) && !empty($token)){
      $request = new PayPalCheckoutSdk\Payments\AuthorizationsCaptureRequest($token);
      $request->body = self::build_request_body();
      $client = self::get_client();
      $response = $client->execute($request);
      if (OsSettingsHelper::is_env_dev()){
        error_log("Status Code: {$response->statusCode}\n");
        error_log("Status: {$response->result->status}\n");
        error_log("Capture ID: {$response->result->id}\n");
        error_log("Links:\n");
        foreach($response->result->links as $link){
          error_log("\t{$link->rel}: {$link->href}\tCall Type: {$link->method}\n");
        }
        error_log(json_encode($response->result, JSON_PRETTY_PRINT));
      }
      $result['charge_id'] = $response->result->id;
      $result['message'] = __('Payment was processed successfully', 'latepoint');
      $result['status'] = LATEPOINT_STATUS_SUCCESS;
    }else{
      $result['status'] = LATEPOINT_STATUS_ERROR;
      $result['message'] = __('Card information is invalid', 'latepoint');
    }
    return $result;
  }

  public static function get_client_secret(){
    return OsSettingsHelper::get_settings_value('paypal_client_secret');
  }

  public static function get_client_id(){
    return OsSettingsHelper::get_settings_value('paypal_client_id');
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
    $country_codes = ['AL' => "Albania",
                      'DZ' => "Algeria",
                      'AD' => "Andorra",
                      'AO' => "Angola",
                      'AI' => "Anguilla",
                      'AG' => "Antigua & Barbuda",
                      'AR' => "Argentina",
                      'AM' => "Armenia",
                      'AW' => "Aruba",
                      'AU' => "Australia",
                      'AT' => "Austria",
                      'AZ' => "Azerbaijan",
                      'BS' => "Bahamas",
                      'BH' => "Bahrain",
                      'BB' => "Barbados",
                      'BY' => "Belarus",
                      'BE' => "Belgium",
                      'BZ' => "Belize",
                      'BJ' => "Benin",
                      'BM' => "Bermuda",
                      'BT' => "Bhutan",
                      'BO' => "Bolivia",
                      'BA' => "Bosnia & Herzegovina",
                      'BW' => "Botswana",
                      'BR' => "Brazil",
                      'VG' => "British Virgin Islands",
                      'BN' => "Brunei",
                      'BG' => "Bulgaria",
                      'BF' => "Burkina Faso",
                      'BI' => "Burundi",
                      'KH' => "Cambodia",
                      'CM' => "Cameroon",
                      'CA' => "Canada",
                      'CV' => "Cape Verde",
                      'KY' => "Cayman Islands",
                      'TD' => "Chad",
                      'CL' => "Chile",
                      'C2' => "China",
                      'CO' => "Colombia",
                      'KM' => "Comoros",
                      'CG' => "Congo - Brazzaville",
                      'CD' => "Congo - Kinshasa",
                      'CK' => "Cook Islands",
                      'CR' => "Costa Rica",
                      'CI' => "Côte D’ivoire",
                      'HR' => "Croatia",
                      'CY' => "Cyprus",
                      'CZ' => "Czech Republic",
                      'DK' => "Denmark",
                      'DJ' => "Djibouti",
                      'DM' => "Dominica",
                      'DO' => "Dominican Republic",
                      'EC' => "Ecuador",
                      'EG' => "Egypt",
                      'SV' => "El Salvador",
                      'ER' => "Eritrea",
                      'EE' => "Estonia",
                      'ET' => "Ethiopia",
                      'FK' => "Falkland Islands",
                      'FO' => "Faroe Islands",
                      'FJ' => "Fiji",
                      'FI' => "Finland",
                      'FR' => "France",
                      'GF' => "French Guiana",
                      'PF' => "French Polynesia",
                      'GA' => "Gabon",
                      'GM' => "Gambia",
                      'GE' => "Georgia",
                      'DE' => "Germany",
                      'GI' => "Gibraltar",
                      'GR' => "Greece",
                      'GL' => "Greenland",
                      'GD' => "Grenada",
                      'GP' => "Guadeloupe",
                      'GT' => "Guatemala",
                      'GN' => "Guinea",
                      'GW' => "Guinea-bissau",
                      'GY' => "Guyana",
                      'HN' => "Honduras",
                      'HK' => "Hong Kong Sar China",
                      'HU' => "Hungary",
                      'IS' => "Iceland",
                      'IN' => "India",
                      'ID' => "Indonesia",
                      'IE' => "Ireland",
                      'IL' => "Israel",
                      'IT' => "Italy",
                      'JM' => "Jamaica",
                      'JP' => "Japan",
                      'JO' => "Jordan",
                      'KZ' => "Kazakhstan",
                      'KE' => "Kenya",
                      'KI' => "Kiribati",
                      'KW' => "Kuwait",
                      'KG' => "Kyrgyzstan",
                      'LA' => "Laos",
                      'LV' => "Latvia",
                      'LS' => "Lesotho",
                      'LI' => "Liechtenstein",
                      'LT' => "Lithuania",
                      'LU' => "Luxembourg",
                      'MK' => "Macedonia",
                      'MG' => "Madagascar",
                      'MW' => "Malawi",
                      'MY' => "Malaysia",
                      'MV' => "Maldives",
                      'ML' => "Mali",
                      'MT' => "Malta",
                      'MH' => "Marshall Islands",
                      'MQ' => "Martinique",
                      'MR' => "Mauritania",
                      'MU' => "Mauritius",
                      'YT' => "Mayotte",
                      'MX' => "Mexico",
                      'FM' => "Micronesia",
                      'MD' => "Moldova",
                      'MC' => "Monaco",
                      'MN' => "Mongolia",
                      'ME' => "Montenegro",
                      'MS' => "Montserrat",
                      'MA' => "Morocco",
                      'MZ' => "Mozambique",
                      'NA' => "Namibia",
                      'NR' => "Nauru",
                      'NP' => "Nepal",
                      'NL' => "Netherlands",
                      'NC' => "New Caledonia",
                      'NZ' => "New Zealand",
                      'NI' => "Nicaragua",
                      'NE' => "Niger",
                      'NG' => "Nigeria",
                      'NU' => "Niue",
                      'NF' => "Norfolk Island",
                      'NO' => "Norway",
                      'OM' => "Oman",
                      'PW' => "Palau",
                      'PA' => "Panama",
                      'PG' => "Papua New Guinea",
                      'PY' => "Paraguay",
                      'PE' => "Peru",
                      'PH' => "Philippines",
                      'PN' => "Pitcairn Islands",
                      'PL' => "Poland",
                      'PT' => "Portugal",
                      'QA' => "Qatar",
                      'RE' => "Réunion",
                      'RO' => "Romania",
                      'RU' => "Russia",
                      'RW' => "Rwanda",
                      'WS' => "Samoa",
                      'SM' => "San Marino",
                      'ST' => "São Tomé & Príncipe",
                      'SA' => "Saudi Arabia",
                      'SN' => "Senegal",
                      'RS' => "Serbia",
                      'SC' => "Seychelles",
                      'SL' => "Sierra Leone",
                      'SG' => "Singapore",
                      'SK' => "Slovakia",
                      'SI' => "Slovenia",
                      'SB' => "Solomon Islands",
                      'SO' => "Somalia",
                      'ZA' => "South Africa",
                      'KR' => "South Korea",
                      'ES' => "Spain",
                      'LK' => "Sri Lanka",
                      'SH' => "St. Helena",
                      'KN' => "St. Kitts & Nevis",
                      'LC' => "St. Lucia",
                      'PM' => "St. Pierre & Miquelon",
                      'VC' => "St. Vincent & Grenadines",
                      'SR' => "Suriname",
                      'SJ' => "Svalbard & Jan Mayen",
                      'SZ' => "Swaziland",
                      'SE' => "Sweden",
                      'CH' => "Switzerland",
                      'TW' => "Taiwan",
                      'TJ' => "Tajikistan",
                      'TZ' => "Tanzania",
                      'TH' => "Thailand",
                      'TG' => "Togo",
                      'TO' => "Tonga",
                      'TT' => "Trinidad & Tobago",
                      'TN' => "Tunisia",
                      'TM' => "Turkmenistan",
                      'TC' => "Turks & Caicos Islands",
                      'TV' => "Tuvalu",
                      'UG' => "Uganda",
                      'UA' => "Ukraine",
                      'AE' => "United Arab Emirates",
                      'GB' => "United Kingdom",
                      'US' => "United States",
                      'UY' => "Uruguay",
                      'VU' => "Vanuatu",
                      'VA' => "Vatican City",
                      'VE' => "Venezuela",
                      'VN' => "Vietnam",
                      'WF' => "Wallis & Futuna",
                      'YE' => "Yemen",
                      'ZM' => "Zambia",
                      'ZW' => "Zimbabwe"];
    return $country_codes;
  }

  public static function zero_decimal_currencies_list(){
    return array('HUF', 'JPY', 'TWD');
  }

  public static function load_country_currencies_list($country_code){
    $currency_list = array(
      'default_currency' => LATEPOINT_DEFAULT_BRAINTREE_CURRENCY_ISO_CODE,
      'currencies' => [ "AUD" => "Australian dollar",
                        "BRL" => "Brazilian real",
                        "CAD" => "Canadian dollar",
                        "CZK" => "Czech koruna",
                        "DKK" => "Danish krone",
                        "EUR" => "Euro",
                        "HKD" => "Hong Kong dollar",
                        "HUF" => "Hungarian forint",
                        "INR" => "Indian rupee",
                        "ILS" => "Israeli new shekel",
                        "JPY" => "Japanese yen",
                        "MYR" => "Malaysian ringgit",
                        "MXN" => "Mexican peso",
                        "TWD" => "New Taiwan dollar",
                        "NZD" => "New Zealand dollar",
                        "NOK" => "Norwegian krone",
                        "PHP" => "Philippine peso",
                        "PLN" => "Polish złoty",
                        "GBP" => "Pound sterling",
                        "RUB" => "Russian ruble",
                        "SGD" => "Singapore dollar",
                        "SEK" => "Swedish krona",
                        "CHF" => "Swiss franc",
                        "THB" => "Thai baht",
                        "USD" => "United States dollar"]
    );
    return $currency_list;
  }

  public static function load_countries_full_data_list(){
  }
}