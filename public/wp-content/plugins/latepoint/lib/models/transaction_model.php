<?php

class OsTransactionModel extends OsModel{
  public $id,
    $token,
    $booking_id,
    $customer_id,
    $processor,
    $payment_method,
    $amount,
    $status,
    $notes,
    $updated_at,
    $created_at;
    
  function __construct($id = false){
    parent::__construct();
    $this->table_name = LATEPOINT_TABLE_TRANSACTIONS;
    $this->nice_names = array();

    if($id){
      $this->load_by_id($id);
    }
  }


  public function build_query_transactions_for_agent($agent_id){
    return $this->select(LATEPOINT_TABLE_TRANSACTIONS.'.*')->join(LATEPOINT_TABLE_BOOKINGS, ['id' => LATEPOINT_TABLE_TRANSACTIONS.'.booking_id'])->where(['agent_id' => $agent_id])->group_by(LATEPOINT_TABLE_TRANSACTIONS.'.id');
  }

  public function count_transactions_for_agent($agent_id){
    $count = $this->select('count('.LATEPOINT_TABLE_TRANSACTIONS.'.id) as total')->join(LATEPOINT_TABLE_BOOKINGS, ['id' => LATEPOINT_TABLE_TRANSACTIONS.'.booking_id'])->where(['agent_id' => $agent_id])->set_limit(1)->get_results();
    return isset($count->total) ? $count->total : 0;
  }

  protected function get_customer(){
    if($this->customer_id){
      if(!isset($this->customer) || (isset($this->customer) && ($this->customer->id != $this->customer_id))){
        $this->customer = new OsCustomerModel($this->customer_id);
      }
    }else{
      $this->customer = new OsCustomerModel();
    }
    return $this->customer;
  }


  protected function get_booking(){
    if($this->booking_id){
      if(!isset($this->booking) || (isset($this->booking) && ($this->booking->id != $this->booking_id))){
        $this->booking = new OsBookingModel($this->booking_id);
      }
    }else{
      $this->booking = new OsBookingModel();
    }
    return $this->booking;
  }

  protected function params_to_save($role = 'admin'){
    $params_to_save = array('id',
                            'token',
                            'booking_id',
                            'customer_id',
                            'processor',
                            'payment_method',
                            'amount',
                            'status',
                            'notes');
    return $params_to_save;
  }

  protected function allowed_params($role = 'admin'){
    $allowed_params = array('id',
                            'token',
                            'booking_id',
                            'customer_id',
                            'processor',
                            'payment_method',
                            'amount',
                            'status',
                            'notes');
    return $allowed_params;
  }


  protected function properties_to_validate(){
    $validations = array(
      'token' => array('presence'),
      'booking_id' => array('presence'),
      'customer_id' => array('presence'),
    );
    return $validations;
  }
}