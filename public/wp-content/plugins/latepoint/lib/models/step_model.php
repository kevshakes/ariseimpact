<?php

class OsStepModel extends OsModel{
  var $id,
      $name,
      $order_number,
      $icon_image_id,
      $title,
      $sub_title,
      $use_custom_image = 'off',
      $updated_at,
      $created_at,
      $description;



  function __construct($step = false){
    parent::__construct();
    $this->table_name = LATEPOINT_TABLE_STEP_SETTINGS;
    $this->nice_names = array();

    if($step){
      $this->name = $step;
      // load defaults
      $this->set_step_defaults();
      // load custom if set in db
      $step_settings = $this->where(array('step' => $step))->get_results();
      foreach($step_settings as $step_setting){
        $this->set_value_by_label($step_setting->label, $step_setting->value);
      }
    }
  }

  public function set_value_by_label($step_label, $step_value){
    if(array_search($step_label, $this->get_allowed_params()) && property_exists($this, $step_label)){
      $label = $step_label;
      $this->$label = $step_value;
    }
  }

  public function set_step_defaults(){
    if(!$this->name) return;
    foreach($this->get_allowed_params() as $param){
      $default = $this->get_default_value($param, $this->name);
      if($default) $this->$param = $this->get_default_value($param, $this->name);
    }
  }

  public function is_using_custom_image(){
    return ($this->use_custom_image == 'on');
  }


  protected function get_icon_image_url(){
    if($this->is_using_custom_image()){
      if(!$this->icon_image_id){
        return '';
      }else{
        return OsImageHelper::get_image_url_by_id($this->icon_image_id);
      }
    }else{
      $color_scheme = OsSettingsHelper::get_booking_form_color_scheme();
      return LATEPOINT_IMAGES_URL.'steps/colors/'.$color_scheme.'/'.$this->name.'.png';
    }
  }


  protected function before_save(){

  }


  public function save(){
    $this->before_save();
    if($this->validate()){
      $step_settings = $this->where(array('step' => $this->name))->get_results();
      foreach($this->get_allowed_params() as $param){
        $param_exists_in_db = false;
        foreach($step_settings as $step_setting){
          if($step_setting->label == $param){
            // Update
            $this->db->update(
              $this->table_name, 
              array('value' => $this->prepare_param($param, $this->$param), 'updated_at' => OsTimeHelper::today_date("Y-m-d H:i:s")), 
              array('step' => $this->name, 'label' => $param));
            OsDebugHelper::log($this->last_query);
            $param_exists_in_db = true;
          }
        }
        if(!$param_exists_in_db){
          // New
          $this->db->insert(
            $this->table_name, 
            array('label' => $param, 'value' => $this->prepare_param($param, $this->$param), 'step' => $this->name, 'updated_at' => OsTimeHelper::today_date("Y-m-d H:i:s"), 'created_at' => OsTimeHelper::today_date("Y-m-d H:i:s"),));
          OsDebugHelper::log($this->last_query);
        }
      }
    }else{
      return false;  
    }
    return true;
  }



  protected function params_to_save($role = 'admin'){
    $params_to_save = array('order_number',
                            'icon_image_id',
                            'title',
                            'sub_title',
                            'use_custom_image',
                            'description');
    return $params_to_save;
  }


  protected function allowed_params($role = 'admin'){
    $allowed_params = array('order_number',
                            'icon_image_id',
                            'title',
                            'sub_title',
                            'use_custom_image',
                            'description');
    return $allowed_params;
  }


  function get_default_value($property, $step){
    $defaults = array( 
      'locations' => array(
          'title' => __('Select Location', 'latepoint'),
          'order_number' => 1,
          'sub_title' => __('Location Selection', 'latepoint'),
          'description' => __('Please select a location you want the service to be performed at', 'latepoint')
      ),
      'services' => array(
          'title' => __('Select Service', 'latepoint'),
          'order_number' => 2,
          'sub_title' => __('Service Selection', 'latepoint'),
          'description' => __('Please select a service for which you want to schedule an appointment', 'latepoint')
      ),
      'agents' => array(
          'title' => __('Select Agent', 'latepoint'),
          'order_number' => 3,
          'sub_title' => __('Agent Selection', 'latepoint'),
          'description' => __('You can pick a specific agent to perform your service or select any to automatically assign you one', 'latepoint')
      ),
      'datepicker' => array(
          'title' => __('Select Date & Time', 'latepoint'),
          'order_number' => 4,
          'sub_title' => __('Date & Time Selection', 'latepoint'),
          'description' => __('Click on a date to see a timeline of available slots, click on a green time slot to reserve it', 'latepoint')
      ),
      'contact' => array(
          'title' => __('Enter Information', 'latepoint'),
          'order_number' => 5,
          'sub_title' => __('Customer Information', 'latepoint'),
          'description' => __('Please provide you contact details so we can send you a confirmation and other contact info', 'latepoint')
      ),
      'payment' => array(
          'title' => __('Payment Method', 'latepoint'),
          'order_number' => 6,
          'sub_title' => __('Your Payment Information', 'latepoint'),
          'description' => __('You can either pay online using your credit card or PayPal, or you can pay on arrival with cash', 'latepoint')
      ),
      'verify' => array(
          'title' => __('Verify Order Details', 'latepoint'),
          'order_number' => 7,
          'sub_title' => __('Verify Booking Details', 'latepoint'),
          'description' => __('Double check your reservation details and click submit button if everything is correct', 'latepoint')
      ),
      'confirmation' => array(
          'title' => __('Confirmation', 'latepoint'),
          'order_number' => 8,
          'sub_title' => __('Appointment Confirmation', 'latepoint'),
          'description' => __('Your appointment has been successfully scheduled. Please retain this confirmation for your record.', 'latepoint'),
      )
    );

    $defaults = apply_filters('latepoint_steps_defaults', $defaults);
    if(isset($defaults[$step]) && isset($defaults[$step][$property])){
      return $defaults[$step][$property];
    }else{
      return false;
    }
  }

  protected function properties_to_validate(){
    $validations = array(
      'name' => array('presence'),
      'title' => array('presence'),
      'sub_title' => array('presence'),
      'order_number' => array('presence'),
    );
    return $validations;
  }
}