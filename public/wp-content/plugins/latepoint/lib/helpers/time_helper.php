<?php 
class OsTimeHelper {

  private static $timezone = false;


  public static function get_db_weekday_by_number($number){
    $weekdays = ['mo','tu','we','th','fr','sa','su'];
    return $weekdays[$number - 1];
  }

  public static function is_valid_date($date_string){
    return (bool)strtotime($date_string);
  }

  public static function reformat_date_string($date_string, $from_format, $to_format){
    $start_date_obj = OsWpDateTime::os_createFromFormat($from_format, $date_string);
    return $start_date_obj->format($to_format);
  }

  public static function shift_time_by_minutes($time_in_minutes, $shift_in_minutes = 0){
    if($shift_in_minutes){
      $time_in_minutes = $time_in_minutes + $shift_in_minutes;
      if($time_in_minutes > (24 * 60)){
        $time_in_minutes = $time_in_minutes - (24 * 60);
      }elseif($time_in_minutes < 0){
        $time_in_minutes = $time_in_minutes + (24 * 60);
      }
    }
    return $time_in_minutes;
  }


  public static function shift_date_by_minutes($date, $time_in_minutes, $shift_in_minutes = 0){
    if($shift_in_minutes){
      $time_in_minutes = $time_in_minutes + $shift_in_minutes;
      if($time_in_minutes > (24 * 60)){
        $date = OsTimeHelper::modify_date($date, '+1 day');
      }elseif($time_in_minutes < 0){
        $date = OsTimeHelper::modify_date($date, '-1 day');
      }
    }
    return $date;
  }

  public static function modify_date($date, $modify_by = '+1 day', $format = 'Y-m-d'){
    $date_obj = new DateTime( $date );
    return $date_obj->modify($modify_by)->format($format);
  }

  public static function nice_date($date){
    if($date == OsTimeHelper::today_date('Y-m-d')){
      $nice_date = __('Today', 'latepoint');
    }else{
      $nice_date = self::get_nice_date_with_optional_year($date, true);
    }
    return $nice_date;
  }

  public static function date_from_db($date_string, $format = false){
    $date_obj = OsWpDateTime::os_createFromFormat("Y-m-d H:i:s", $date_string);
    if($format) return $date_obj->format($format);
    return $date_obj;
  }

  public static function today_date($date_format = 'Y-m-d'){
    return current_time($date_format);
  }

  public static function get_modified_now_object($modify_by){
    $now_datetime = self::now_datetime_object();
    return $now_datetime->modify($modify_by);
  }

  public static function today_date_object(){
    $today_date_object = OsWpDateTime::os_createFromFormat("Y-m-d", current_time('Y-m-d'));
    return $today_date_object;
  }

  public static function now_datetime_object(){
    $now_time_object = OsWpDateTime::os_createFromFormat("Y-m-d H:i:s", current_time('mysql'));
    return $now_time_object;
  }

  public static function get_time_system(){
    return OsSettingsHelper::get_time_system();
  }

  public static function get_time_format(){
    return self::is_army_clock() ? 'H:i' : 'g:i a';
  }

  public static function is_army_clock(){
    return (self::get_time_system() == 24);
  }

  public static function get_time_systems_list_for_select(){
    return array( array( 'value' => '12', 'label' => __('12-hour clock', 'latepoint')), 
                  array( 'value' => '24', 'label' => __('24-hour clock', 'latepoint')));
  }

  public static function get_date_formats_list_for_select(){
    return array( array( 'value' => 'm/d/Y', 'label' => __('MM/DD/YYYY', 'latepoint')), 
                  array( 'value' => 'm.d.Y', 'label' => __('MM.DD.YYYY', 'latepoint')), 
                  array( 'value' => 'd/m/Y', 'label' => __('DD/MM/YYYY', 'latepoint')), 
                  array( 'value' => 'd.m.Y', 'label' => __('DD.MM.YYYY', 'latepoint')), 
                  array( 'value' => 'Y-m-d', 'label' => __('YYYY-MM-DD', 'latepoint')));
  }
  
  public static function get_time_systems_list(){
    return array('12' => __('12-hour clock', 'latepoint'), '24' => __('24-hour clock', 'latepoint'));
  }

  
  public static function format_date_with_locale($format, $date_obj){
    return OsUtilHelper::translate_months($date_obj->format($format));
  }


  public static function get_nice_date_with_optional_year($date, $show_year_if_not_current = true){
    $d = OsWpDateTime::os_createFromFormat("Y-m-d", $date);
    if(!$d) return $date;
    if(!$show_year_if_not_current || ($d->format('Y') == OsTimeHelper::today_date('Y'))){
      return OsUtilHelper::translate_months($d->format(OsSettingsHelper::get_readable_date_format(true)));
    }else{
      return OsUtilHelper::translate_months($d->format(OsSettingsHelper::get_readable_date_format()));
    }
  }

  public static function get_wp_timezone() {
    if(self::$timezone) return self::$timezone;
    $timezone_string = get_option( 'timezone_string' );
    if ( ! empty( $timezone_string ) ) {
      return new DateTimeZone( $timezone_string );
    }
    $offset  = get_option( 'gmt_offset' );
    $hours   = (int) $offset;
    $minutes = abs( ( $offset - (int) $offset ) * 60 );
    $offset  = sprintf( '%+03d:%02d', $hours, $minutes );
    self::$timezone = new DateTimeZone( $offset );
    return self::$timezone;
  }

  public static function get_wp_timezone_name() {
    $timezone_obj = self::get_wp_timezone();
    if($timezone_obj){
      return $timezone_obj->getName();
    }else{
      return 'America/New_York';
    }
  }

  public static function get_timezone_from_session(){
    $timezone = new DateTimeZone(self::get_timezone_name_from_session());
    return $timezone;
  }

  public static function get_timezone_name_from_session(){
    if(self::is_timezone_saved_in_session()){
      $timezone_name = $_SESSION['timezone_name'];
    }else{
      if(OsAuthHelper::is_customer_logged_in()){
        $customer_timezone_name = OsMetaHelper::get_customer_meta_by_key('timezone_name', OsAuthHelper::get_logged_in_customer_id());
        if(!empty($customer_timezone_name)){
          $timezone_name = $customer_timezone_name;
        }else{
          OsMetaHelper::save_customer_meta_by_key('timezone_name', self::get_wp_timezone_name(), OsAuthHelper::get_logged_in_customer_id());
          $timezone_name = self::get_wp_timezone_name();
        }
      }else{
        $timezone_name = self::get_wp_timezone_name();
      }
      self::set_timezone_name_in_session($timezone_name);
    }
    return $timezone_name;
  }

  public static function is_timezone_saved_in_session(){
    return (isset($_SESSION['timezone_name']) && !empty($_SESSION['timezone_name']));
  }

  public static function set_timezone_name_in_session($timezone_name){
    $_SESSION['timezone_name'] = $timezone_name;
  }

  public static function get_timezone_shift_in_minutes($requested_timezone_name){
    if(self::get_wp_timezone_name() == $requested_timezone_name) return 0;
    $wp_timezone = self::get_wp_timezone();
    try{
      $requested_timezone = new DateTimeZone( $requested_timezone_name );
    }catch(Exception $e){
      $requested_timezone = self::get_wp_timezone();
    }

    $now_in_wp_tz = new DateTime('now', $wp_timezone);
    $now_in_requested_tz = new DateTime('now', $requested_timezone);

    $offset = $requested_timezone->getOffset($now_in_requested_tz) - $wp_timezone->getOffset($now_in_wp_tz);
    $shift_in_minutes = round($offset / 60);
    return $shift_in_minutes;
  }

  public static function get_timezone_shift_from_gmt_in_minutes($requested_timezone_name){
    $utc_timezone = new DateTimeZone( 'UTC' );
    try{
      $requested_timezone = new DateTimeZone( $requested_timezone_name );
    }catch(Exception $e){
      $requested_timezone = self::get_wp_timezone();
    }

    $now_in_utc_tz = new DateTime('now', $utc_timezone);
    $now_in_requested_tz = new DateTime('now', $requested_timezone);

    $offset = $requested_timezone->getOffset($now_in_requested_tz) - $utc_timezone->getOffset($now_in_utc_tz);
    $shift_in_minutes = round($offset / 60);
    return $shift_in_minutes;
  }


  public static function convert_datetime_to_minutes($datetime){
    return $datetime->format('i') + ($datetime->format('G') * 60);
  }

	public static function get_current_minutes($timeshift_minutes = false){
    $now = new OsWpDateTime('now');
    if($timeshift_minutes) $now->modify($timeshift_minutes.' minutes');
    return $now->format('i') + ($now->format('G') * 60);
	}

  public static function convert_time_to_minutes($time, $ampm = false){
    if(strpos($time, ':') === false) return 0;

    list($hours, $minutes) = explode(':', $time);
    if($hours == '12' && $ampm == 'am'){
      // midnight
      $hours = '0';
    }
    if($ampm == 'pm' && $hours < 12){
      // convert to 24 hour format
      $hours = $hours + 12;
    }
    $minutes = ($hours * 60) + $minutes;
    return $minutes;
  }

  public static function am_or_pm($minutes) {
    if(self::is_army_clock()) return '';
    return ($minutes < 720) ? 'am' : 'pm';
  }

  public static function minutes_to_hours($time) {
    if($time){
      $hours = floor($time / 60);
      if(!self::is_army_clock() && $hours > 12) $hours = $hours - 12;
      return $hours;
    }else{
      return 0;
    }
  }


  public static function minutes_to_army_hours_and_minutes($time) {
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf('%02d:%02d', $hours, $minutes);
  }

  public static function minutes_to_hours_and_minutes($minutes, $format = '%02d:%02d', $add_ampm = true) {
    if(!$format) $format = '%02d:%02d';

    if ($minutes === '') {
        return;
    }
    $ampm = ($add_ampm) ? self::am_or_pm($minutes) : '';
    $hours = self::minutes_to_hours($minutes);
    $minutes = ($minutes % 60);

    return sprintf($format, $hours, $minutes).$ampm;
  }


  public static function timezones_options_list( $selected_zone, $locale = null ) {
    static $mo_loaded = false, $locale_loaded = null;

    $continents = array( 'Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific' );

    // Load translations for continents and cities.
    if ( ! $mo_loaded || $locale !== $locale_loaded ) {
      $locale_loaded = $locale ? $locale : get_locale();
      $mofile        = WP_LANG_DIR . '/continents-cities-' . $locale_loaded . '.mo';
      unload_textdomain( 'continents-cities' );
      load_textdomain( 'continents-cities', $mofile );
      $mo_loaded = true;
    }

    $zonen = array();
    foreach ( timezone_identifiers_list() as $zone ) {
      $zone = explode( '/', $zone );
      if ( ! in_array( $zone[0], $continents ) ) {
        continue;
      }

      // This determines what gets set and translated - we don't translate Etc/* strings here, they are done later
      $exists    = array(
        0 => ( isset( $zone[0] ) && $zone[0] ),
        1 => ( isset( $zone[1] ) && $zone[1] ),
        2 => ( isset( $zone[2] ) && $zone[2] ),
      );
      $exists[3] = ( $exists[0] && 'Etc' !== $zone[0] );
      $exists[4] = ( $exists[1] && $exists[3] );
      $exists[5] = ( $exists[2] && $exists[3] );

      // phpcs:disable WordPress.WP.I18n.LowLevelTranslationFunction,WordPress.WP.I18n.NonSingularStringLiteralText
      $zonen[] = array(
        'continent'   => ( $exists[0] ? $zone[0] : '' ),
        'city'        => ( $exists[1] ? $zone[1] : '' ),
        'subcity'     => ( $exists[2] ? $zone[2] : '' ),
        't_continent' => ( $exists[3] ? translate( str_replace( '_', ' ', $zone[0] ), 'continents-cities' ) : '' ),
        't_city'      => ( $exists[4] ? translate( str_replace( '_', ' ', $zone[1] ), 'continents-cities' ) : '' ),
        't_subcity'   => ( $exists[5] ? translate( str_replace( '_', ' ', $zone[2] ), 'continents-cities' ) : '' ),
      );
      // phpcs:enable
    }
    usort( $zonen, '_wp_timezone_choice_usort_callback' );

    $structure = array();

    if ( empty( $selected_zone ) ) {
      $structure[] = '<option selected="selected" value="">' . __( 'Select a city' ) . '</option>';
    }

    foreach ( $zonen as $key => $zone ) {
      // Build value in an array to join later
      $value = array( $zone['continent'] );

      if ( empty( $zone['city'] ) ) {
        // It's at the continent level (generally won't happen)
        $display = $zone['t_continent'];
      } else {
        // It's inside a continent group

        // Continent optgroup
        if ( ! isset( $zonen[ $key - 1 ] ) || $zonen[ $key - 1 ]['continent'] !== $zone['continent'] ) {
          $label       = $zone['t_continent'];
          $structure[] = '<optgroup label="' . esc_attr( $label ) . '">';
        }

        // Add the city to the value
        $value[] = $zone['city'];

        $display = $zone['t_city'];
        if ( ! empty( $zone['subcity'] ) ) {
          // Add the subcity to the value
          $value[]  = $zone['subcity'];
          $display .= ' - ' . $zone['t_subcity'];
        }
      }

      // Build the value
      $value    = join( '/', $value );
      $selected = '';
      if ( $value === $selected_zone ) {
        $selected = 'selected="selected" ';
      }
      $structure[] = '<option ' . $selected . 'value="' . esc_attr( $value ) . '">' .esc_html( $label ).', '. esc_html( $display ) . '</option>';

      // Close continent optgroup
      if ( ! empty( $zone['city'] ) && ( ! isset( $zonen[ $key + 1 ] ) || ( isset( $zonen[ $key + 1 ] ) && $zonen[ $key + 1 ]['continent'] !== $zone['continent'] ) ) ) {
        $structure[] = '</optgroup>';
      }
    }

    // Do UTC
    $structure[] = '<optgroup label="' . esc_attr__( 'UTC' ) . '">';
    $selected    = '';
    if ( 'UTC' === $selected_zone ) {
      $selected = 'selected="selected" ';
    }
    $structure[] = '<option ' . $selected . 'value="' . esc_attr( 'UTC' ) . '">' . __( 'UTC' ) . '</option>';
    $structure[] = '</optgroup>';

    return join( "\n", $structure );
  }

}