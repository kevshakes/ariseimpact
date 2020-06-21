<?php

class OsActivityModel extends OsModel{
  public $id,
      $agent_id,
      $booking_id,
      $service_id,
      $customer_id,
      $code,
      $description,
      $initiated_by,
      $initiated_by_id,
      $updated_at,
      $created_at;
      
      
      
      
      
  protected $codes;

  function __construct($id = false){
    parent::__construct();
    $this->table_name = LATEPOINT_TABLE_ACTIVITIES;
    $this->nice_names = array();

    $this->codes = array( 'customer_create' => __('New Customer Registration', 'latepoint'), 
                      'customer_update' => __('Customer Profile Update', 'latepoint'),
                      'booking_create' => __('New Appointment', 'latepoint'),
                      'booking_change_status' => __('Appointment Status Changed', 'latepoint'),
                      'booking_update' => __('Appointment Edited', 'latepoint'),
                      'agent_create' => __('New Agent', 'latepoint'),
                      'agent_update' => __('Agent Profile Update', 'latepoint'),
                      'coupon_create' => __('New Coupon', 'latepoint'),
                      'coupon_update' => __('Coupon Update', 'latepoint'),
                    );

    if($id){
      $this->load_by_id($id);
    }
  }

  protected function get_link_to_object(){
    $link = '#';
    switch($this->code){
      case 'customer_create':
      case 'customer_update':
        $link = OsRouterHelper::build_link(OsRouterHelper::build_route_name('customers', 'edit_form'), array('id' => $this->customer_id) );
      break;
      case 'booking_create':
      case 'booking_change_status':
      case 'booking_update':
        $link = OsRouterHelper::build_link(OsRouterHelper::build_route_name('bookings', 'edit_form'), array('id' => $this->booking_id) );
      break;
      case 'agent_update':
      case 'agent_create':
        $link = OsRouterHelper::build_link(OsRouterHelper::build_route_name('agents', 'edit_form'), array('id' => $this->agent_id) );
      break;
    }
    return $link;
  }



  protected function get_user_link_with_avatar(){
    $link = '#';
    $name = 'n/a';
    $avatar_url = LATEPOINT_DEFAULT_AVATAR_URL;
    switch($this->initiated_by){
      case 'wp_user':
      case 'admin':
        $link = get_edit_user_link($this->initiated_by_id);
        $userdata = get_userdata($this->initiated_by_id);
        $name = $userdata->display_name;
        $avatar_url = get_avatar_url($this->initiated_by_id, array('size' => 200));
      break;
      case 'agent':
        $agent = new OsAgentModel($this->initiated_by_id);
        $link = OsRouterHelper::build_link(OsRouterHelper::build_route_name('agents', 'edit_form'), array('id' => $this->initiated_by_id) );
        $name = $agent->full_name;
        $avatar_url = $agent->get_avatar_url();
      break;
      case 'customer':
        $customer = new OsCustomerModel($this->initiated_by_id);
        $link = OsRouterHelper::build_link(OsRouterHelper::build_route_name('customers', 'edit_form'), array('id' => $this->initiated_by_id) );
        $name = $customer->full_name;
        $avatar_url = $customer->get_avatar_url();
      break;
    }
    return "<a class='user-link-with-avatar' href='{$avatar_url}'><span class='ula-avatar' style='background-image: url({$avatar_url})'></span><span class='ula-name'>{$name}</span></a>";
  }


  protected function get_nice_created_at(){
    $time = strtotime($this->created_at);
    return date("m/d/y g:i A", $time);
  }


  protected function get_name(){
    if($this->code && isset($this->codes[$this->code])){
      return $this->codes[$this->code];
    }else{
      return $this->code;
    }
  }

  protected function params_to_save($role = 'admin'){
    $params_to_save = array('id', 
                            'agent_id',
                            'booking_id',
                            'service_id',
                            'customer_id',
                            'code',
                            'description',
                            'initiated_by',
                            'initiated_by_id');
    return $params_to_save;
  }

  protected function allowed_params($role = 'admin'){
    $allowed_params = array('id', 
                            'agent_id',
                            'booking_id',
                            'service_id',
                            'customer_id',
                            'code',
                            'description',
                            'initiated_by',
                            'initiated_by_id');
    return $allowed_params;
  }


  protected function properties_to_validate(){
    $validations = array(
      'code' => array('presence'),
      'initiated_by' => array('presence'),
      'initiated_by_id' => array('presence'),
    );
    return $validations;
  }
}