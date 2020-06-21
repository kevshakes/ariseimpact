<?php 

class OsMoneyHelper {

  public static function calculate_full_amount_to_charge($booking,  $apply_coupons = true){
  	$service = new OsServiceModel($booking->service_id);
    $amount = $service->get_charge_amount_for_duration($booking->duration);
    $amount = apply_filters('latepoint_full_amount', $amount, $booking, $apply_coupons);
    if($booking->total_attendies > 1) $amount = $amount * $booking->total_attendies;
  	return $amount;
  }

  public static function calculate_deposit_amount_to_charge($booking, $apply_coupons = true){
  	$service = new OsServiceModel($booking->service_id);
    $amount = $service->get_deposit_amount_for_duration($booking->duration);
    $amount = apply_filters('latepoint_deposit_amount', $amount, $booking, $apply_coupons);
  	return $amount;
  }

  public static function format_price($price){
  	$price = number_format($price, 2);
    $price = str_replace('.00', '', $price);
  	return implode('', array(OsSettingsHelper::get_settings_value('currency_symbol_before'), $price, OsSettingsHelper::get_settings_value('currency_symbol_after')));
  }

  public static function clean_price($price){
    $price = preg_replace('/[^\\d.]+/', '', $price);
    return $price;
  }

}