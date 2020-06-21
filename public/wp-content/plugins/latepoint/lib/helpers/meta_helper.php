<?php 

class OsMetaHelper {
  
  function __construct(){
  }

  public static function get_agent_meta_by_key($meta_key, $agent_id, $default = false){
    $meta = new OsAgentMetaModel();
    return $meta->get_by_key($meta_key, $agent_id, $default);
  }

  public static function save_agent_meta_by_key($meta_key, $meta_value, $agent_id){
    $meta = new OsAgentMetaModel();
    return $meta->save_by_key($meta_key, $meta_value, $agent_id);
  }



  public static function get_booking_meta_by_key($meta_key, $booking_id, $default = false){
    $meta = new OsBookingMetaModel();
    return $meta->get_by_key($meta_key, $booking_id, $default);
  }

  public static function save_booking_meta_by_key($meta_key, $meta_value, $booking_id){
    $meta = new OsBookingMetaModel();
    return $meta->save_by_key($meta_key, $meta_value, $booking_id);
  }

  public static function delete_booking_meta($meta_key, $booking_id){
    if(empty($meta_key) || empty($booking_id)) return;
    $booking_meta_model = new OsBookingMetaModel();
    $booking_meta_model->delete_by_key($meta_key, $booking_id);
  }



  public static function get_customer_meta_by_key($meta_key, $customer_id, $default = false){
    $meta = new OsCustomerMetaModel();
    return $meta->get_by_key($meta_key, $customer_id, $default);
  }

  public static function save_customer_meta_by_key($meta_key, $meta_value, $customer_id){
    $meta = new OsCustomerMetaModel();
    return $meta->save_by_key($meta_key, $meta_value, $customer_id);
  }



  public static function get_booking_id_by_meta_value($meta_key, $meta_value){
    $meta = new OsBookingMetaModel();
    return $meta->get_object_id_by_value($meta_key, $meta_value);
  }
}
?>