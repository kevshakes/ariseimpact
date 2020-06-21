<?php

class OsCustomerModel extends OsModel{
  var $id,
      $first_name,
      $last_name,
      $password,
      $email,
      $phone,
      $account_nonse,
      $status,
      $activation_key,
      $google_user_id,
      $facebook_user_id,
      $avatar_image_id,
      $is_guest,
      $notes,
      $admin_notes,
      $updated_at,
      $created_at;

  function __construct($id = false){
    $this->table_name = LATEPOINT_TABLE_CUSTOMERS;
    $this->nice_names = array(
                              'first_name' => __('Customer First Name', 'latepoint'),
                              'email' => __('Email Address', 'latepoint'),
                              'last_name' => __('Customer Last Name', 'latepoint'));

    parent::__construct($id);
  }

  public function get_meta_by_key($meta_key, $default = false){
    if($this->is_new_record()) return $default;

    $meta = new OsCustomerMetaModel();
    return $meta->get_by_key($meta_key, $this->id, $default);
  }

  public function save_meta_by_key($meta_key, $meta_value){
    if($this->is_new_record()) return false;

    $meta = new OsCustomerMetaModel();
    return $meta->save_by_key($meta_key, $meta_value, $this->id);
  }

  public function set_timezone_name($timezone_name = false){
    if(!$timezone_name){
      $timezone_name = OsTimeHelper::get_timezone_name_from_session();
    }
    $this->save_meta_by_key('timezone_name', $timezone_name);
  }
  
  public function get_selected_timezone_name(){
    return $this->get_meta_by_key('timezone_name', OsTimeHelper::get_timezone_name_from_session());
  }

  public function get_selected_timezone_obj(){
    $timezone_obj = new DateTimeZone( $this->get_selected_timezone_name() );
    return $timezone_obj;
  }

  public function get_timeshift_in_minutes(){
    return OsTimeHelper::get_timezone_shift_in_minutes($this->get_selected_timezone_name());
  }



  public function save_custom_fields($custom_fields_data){
    if($this->is_new_record()) return;
    $custom_fields_for_customer = OsCustomFieldsHelper::get_custom_fields_arr('customer');
    foreach($custom_fields_for_customer as $custom_field){
      if(isset($custom_fields_data[$custom_field['id']])){
        $this->save_meta_by_key($custom_field['id'], $custom_fields_data[$custom_field['id']]);
      }
    }
  }

  public function delete($id = false){
    if(!$id && isset($this->id)){
      $id = $this->id;
    }
    $bookings = new OsBookingModel();
    $bookings_to_delete = $bookings->where(['customer_id' => $id])->get_results_as_models();
    if($bookings_to_delete){
      foreach($bookings_to_delete as $booking){
        $booking->delete();
      }
    }
    $transactions = new OsTransactionModel();
    $transactions_to_delete = $transactions->where(['customer_id' => $id])->get_results_as_models();
    if($transactions_to_delete){
      foreach($transactions_to_delete as $transaction){
        $transaction->delete();
      }
    }
    $customer_metas = new OsCustomerMetaModel();
    $customer_metas_to_delete = $customer_metas->where(['object_id' => $id])->get_results_as_models();
    if($customer_metas_to_delete){
      foreach($customer_metas_to_delete as $customer_meta){
        $customer_meta->delete();
      }
    }
    return parent::delete($id);
  }

  public function validate_custom_fields($custom_fields_data){
    $custom_fields_for_customer = OsCustomFieldsHelper::get_custom_fields_arr('customer');
    $is_valid = true;
    foreach($custom_fields_for_customer as $custom_field){
      if($custom_field['required'] == 'on'){
        // checkbox has different "required" validation
        if($custom_field['type'] == 'checkbox'){
          if(!isset($custom_fields_data[$custom_field['id']]) || empty($custom_fields_data[$custom_field['id']]) || $custom_fields_data[$custom_field['id']] == 'off'){
            $is_valid = false;
            $error_message = sprintf( __( '%s field has to be checked', 'latepoint' ), $custom_field['label'] );
            $this->add_error('validation', $error_message);
          }
        }else{
          if(!isset($custom_fields_data[$custom_field['id']]) || empty($custom_fields_data[$custom_field['id']])){
            $is_valid = false;
            $error_message = sprintf( __( '%s can not be blank', 'latepoint' ), $custom_field['label'] );
            $this->add_error('validation', $error_message);
          }
        }
      }
    }
    return $is_valid;
  }

  public function build_query_customers_for_agent($agent_id){
    return $this->select(LATEPOINT_TABLE_CUSTOMERS.'.*')->join(LATEPOINT_TABLE_BOOKINGS, ['customer_id' => LATEPOINT_TABLE_CUSTOMERS.'.id'])->where(['agent_id' => $agent_id])->group_by(LATEPOINT_TABLE_CUSTOMERS.'.id');
  }

  public function count_customers_for_agent($agent_id){
    $count = $this->select('count('.LATEPOINT_TABLE_CUSTOMERS.'.id) as total')->join(LATEPOINT_TABLE_BOOKINGS, ['customer_id' => LATEPOINT_TABLE_CUSTOMERS.'.id'])->where(['agent_id' => $agent_id])->set_limit(1)->get_results();
    return isset($count->total) ? $count->total : 0;
  }

  protected function get_formatted_phone(){
    return OsUtilHelper::format_phone($this->phone);
  }


  protected function get_bookings(){
    $bookings = new OsBookingModel();
    if(OsAuthHelper::get_logged_in_agent_id()) $bookings->where(['agent_id' => OsAuthHelper::get_logged_in_agent_id()]);
    return $bookings->where(array('customer_id' => $this->id))->get_results_as_models();
  }

  protected function get_past_bookings(){
    $bookings = new OsBookingModel();

    return $bookings->where(array('customer_id' => $this->id, 
                                  'OR' => array('start_date <' => OsTimeHelper::today_date('Y-m-d'), 
                                                'AND' => array('start_date' => OsTimeHelper::today_date('Y-m-d'),
                                                               'start_time <' => OsTimeHelper::get_current_minutes()))))->get_results_as_models();
  }


  protected function get_future_bookings($limit = false){
    $bookings = new OsBookingModel();
    if($limit){
      $bookings = $bookings->set_limit($limit);
    }
    if(OsAuthHelper::get_logged_in_agent_id()) $bookings->where(['agent_id' => OsAuthHelper::get_logged_in_agent_id()]);
    return $bookings->order_by('start_date, start_time asc')->where(['customer_id' => $this->id])->should_be_in_future()->get_results_as_models();
  }


  protected function get_future_bookings_count(){
    $bookings = new OsBookingModel();
    if(OsAuthHelper::get_logged_in_agent_id()) $bookings->where(['agent_id' => OsAuthHelper::get_logged_in_agent_id()]);
    return $bookings->should_not_be_cancelled()->where(array('customer_id' => $this->id, 
                                  'OR' => array('start_date >' => OsTimeHelper::today_date('Y-m-d'), 
                                                'AND' => array('start_date' => OsTimeHelper::today_date('Y-m-d'),
                                                               'start_time >' => OsTimeHelper::get_current_minutes()))))->count();
  }

  protected function get_upcoming_booking(){
    return $this->get_future_bookings(1);
  }



  protected function get_total_bookings(){
    $bookings = new OsBookingModel();
    if(OsAuthHelper::get_logged_in_agent_id()) $bookings->where(['agent_id' => OsAuthHelper::get_logged_in_agent_id()]);
    return $bookings->select('count(id) as total_bookings')->where(array('customer_id' => $this->id))->count();
  }


  protected function get_full_name(){
    return trim(join(' ', array($this->first_name, $this->last_name)));
  }

  public function get_by_account_nonse($account_nonse){
    $query = $this->db->prepare('SELECT * FROM '.$this->table_name.' WHERE account_nonse = %s', $account_nonse);
    $result_row = $this->db->get_row( $query, ARRAY_A);

    if($result_row){
      foreach($result_row as $row_key => $row_value){
        if(property_exists($this, $row_key)) $this->$row_key = $row_value;
      }
      return $this;
    }else{
      return false;
    }
  }

  protected function before_save(){
    if($this->phone){
      $this->phone = OsUtilHelper::clean_phone($this->phone);
    }
  }

  protected function get_default_status(){
    return 'pending_verification';
  }

  protected function before_create(){
    if(!isset($this->is_guest)) $this->is_guest = true;
    if(empty($this->status)) $this->status = $this->get_default_status();
    if(empty($this->password)) $this->password = wp_hash_password(bin2hex(openssl_random_pseudo_bytes(8)));
    if(empty($this->activation_key)) $this->activation_key = sha1(mt_rand(10000,99999).time().$this->email);
    if(empty($this->account_nonse)) $this->account_nonse = sha1(mt_rand(10000,99999).time().$this->activation_key);
  }

  
  public function get_avatar_url(){
    return OsCustomerHelper::get_avatar_url($this);
  }

  public function get_avatar_image(){
    return OsCustomerHelper::get_avatar_image($this);
  }

  // if this was a guest account without a set password and social login was not used, you can login just by email
  public function can_login_without_password(){
    return ($this->is_guest && empty($this->google_user_id) && empty($this->facebook_user_id));
  }


  protected function allowed_params($role = 'admin'){
    $allowed_params = array('id',
                            'first_name',
                            'last_name',
                            'email',
                            'phone',
                            'avatar_image_id',
                            'is_guest',
                            'notes',
                            'admin_notes',
                            'password');
    return $allowed_params;
  }

  protected function params_to_save($role = 'admin'){
    $params_to_save = array('id',
                            'first_name',
                            'last_name',
                            'email',
                            'phone',
                            'password',
                            'activation_key',
                            'account_nonse',
                            'avatar_image_id',
                            'status',
                            'is_guest',
                            'notes',
                            'admin_notes',
                            'google_user_id',
                            'facebook_user_id');
    return $params_to_save;
  }

  protected function properties_to_validate(){
    $validations = array(
      'first_name' => array('presence'),
      'last_name' => array('presence'),
      'email' => array('presence', 'email'),
    );
    return $validations;
  }
}