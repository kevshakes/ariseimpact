<?php

class OsSentReminderModel extends OsModel{
  public $id,
      $booking_id,
      $reminder_id,
      $updated_at,
      $created_at;
      
      
      
      
      
  protected $codes;

  function __construct($id = false){
    parent::__construct();
    $this->table_name = LATEPOINT_TABLE_SENT_REMINDERS;
    $this->nice_names = array();

    if($id){
      $this->load_by_id($id);
    }
  }

  protected function params_to_save($role = 'admin'){
    $params_to_save = array('id', 
                            'reminder_id',
                            'booking_id');
    return $params_to_save;
  }

  protected function allowed_params($role = 'admin'){
    $allowed_params = array('id', 
                            'reminder_id',
                            'booking_id',);
    return $allowed_params;
  }


  protected function properties_to_validate(){
    $validations = array(
      'booking_id' => array('presence'),
      'reminder_id' => array('presence')
    );
    return $validations;
  }
}