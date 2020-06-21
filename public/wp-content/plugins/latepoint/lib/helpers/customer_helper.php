<?php 

class OsCustomerHelper {

  public static function get_full_name($customer){
  	return join(' ', array($customer->first_name, $customer->last_name));
  }

  public static function get_avatar_url($customer){
    $default_avatar = LATEPOINT_IMAGES_URL . 'default-avatar.jpg';
    return OsImageHelper::get_image_url_by_id($customer->avatar_image_id, 'thumbnail', $default_avatar);
  }


  public static function get_avatar_image($customer){
  	return '<img src="'.self::get_avatar_url($customer).'"/>';
  }


  public static function total_new_customers_for_date($date){
    $customers = new OsCustomerModel();
    $customers = $customers->where(array('DATE(created_at)' => $date));
    return $customers->count();
  }

}