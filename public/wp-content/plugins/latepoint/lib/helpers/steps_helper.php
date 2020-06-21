<?php 
class OsStepsHelper {
	public static $step_names_in_order = false;
  public static $booking_object;
  public static $vars_for_view = [];
  public static $restrictions = ['show_locations' => false, 
                                  'show_agents' => false, 
                                  'show_services' => false, 
                                  'show_service_categories' => false, 
                                  'selected_location' => false, 
                                  'selected_agent' => false, 
                                  'selected_service' => false, 
                                  'selected_service_category' => false,
                                  'calendar_start_date' => false];
  public static function init_step_actions(){
    add_action('latepoint_process_step', 'OsStepsHelper::process_step', 10, 2);
    add_action('latepoint_load_step','OsStepsHelper::load_step', 10, 3);
  }                                  

  public static function process_step($step_name, $booking_object){
		$step_function_name = 'process_step_'.$step_name;
  	if(method_exists('OsStepsHelper', $step_function_name)){
  		$result = self::$step_function_name();
      if(is_wp_error($result)){
        wp_send_json(array('status' => LATEPOINT_STATUS_ERROR, 'message' => $result->get_error_message()));
        return;
      }
  	}
  }

  public static function output_step_edit_form($step){
    if(in_array($step->name, ['payment', 'verify', 'confirmation'])){
      $can_reorder = false;
    }else{
      $can_reorder = true;
    }
    ?>
    <div class="step-w" data-step-name="<?php echo $step->name; ?>" data-step-order-number="<?php echo $step->order_number; ?>">
      <div class="step-head">
        <div class="step-drag <?php echo ($can_reorder) ? '' : 'disabled'; ?>"><?php if(!$can_reorder) echo '<span>'.__('Order of this step can not be changed.', 'latepoint').'</span>'; ?></div>
        <div class="step-name"><?php echo $step->title; ?></div>
        <?php if($step->name == 'locations' && (OsLocationHelper::count_locations() <= 1)){ ?>
          <a href="<?php echo OsRouterHelper::build_link(OsRouterHelper::build_route_name('locations', 'index') ); ?>" class="step-message"><?php _e('Since you only have one location, this step will be skipped', 'latepoint'); ?></a>
        <?php } ?>
        <?php if($step->name == 'payment' && !OsSettingsHelper::is_accepting_payments()){ ?>
          <a href="<?php echo OsRouterHelper::build_link(OsRouterHelper::build_route_name('settings', 'payments') ); ?>" class="step-message"><?php _e('Payment processing is disabled. Click to setup.', 'latepoint'); ?></a>
        <?php } ?>
        <button class="step-edit-btn"><i class="latepoint-icon latepoint-icon-edit-3"></i></button>
      </div>
      <div class="step-body">
        <div class="os-form-w">
          <form data-os-action="<?php echo OsRouterHelper::build_route_name('settings', 'update_step'); ?>" action="">
            <div class="os-row">
              <div class="os-col-6">
                <?php echo OsFormHelper::text_field('step[title]', __('Step Title', 'latepoint'), $step->title, ['add_string_to_id' => $step->name]); ?>
              </div>
              <div class="os-col-6">
                <?php echo OsFormHelper::text_field('step[sub_title]', __('Step Sub Title', 'latepoint'), $step->sub_title, ['add_string_to_id' => $step->name]); ?>
              </div>
            </div>
            <?php echo OsFormHelper::textarea_field('step[description]', __('Short Description', 'latepoint'), $step->description, ['add_string_to_id' => $step->name]); ?>
            <div class="os-row">
              <div class="os-col-12">
                <?php echo OsFormHelper::checkbox_field('step[use_custom_image]', __('Use Custom Step Image', 'latepoint'), 'on', $step->is_using_custom_image(), ['data-toggle-element' => '.custom-step-image-w-'.$step->name, 'add_string_to_id' => $step->name], ['class' => 'toggle-element-outside']); ?>
              </div>
            </div>
            <div class="os-row custom-step-image-w-<?php echo $step->name; ?>" style="<?php echo ($step->is_using_custom_image()) ? '' : 'display: none;'; ?>">
              <div class="os-col-12">
                <div class="os-form-group">
                  <?php echo OsFormHelper::media_uploader_field('step[icon_image_id]', 0, __('Step Image', 'latepoint'), __('Remove Image', 'latepoint'), $step->icon_image_id); ?>
                </div>
              </div>
            </div>
            <?php echo OsFormHelper::hidden_field('step[name]', $step->name, ['add_string_to_id' => $step->name]); ?>
            <?php echo OsFormHelper::hidden_field('step[order_number]', $step->order_number, ['add_string_to_id' => $step->name]); ?>
            <div class="os-form-buttons">
              <?php echo OsFormHelper::button('submit', __('Save Step', 'latepoint'), 'submit', ['class' => 'latepoint-btn', 'add_string_to_id' => $step->name]);  ?>
              <a href="#" class="latepoint-btn latepoint-btn-secondary step-edit-cancel-btn"><?php _e('Cancel', 'latepoint'); ?></a>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php
  }

  public static function show_step_progress($steps_models, $active_step_model){
    ?>
    <div class="latepoint-progress">
      <ul>
        <?php foreach($steps_models as $index => $step_model){ ?>
          <li data-step-name="<?php echo $step_model->name; ?>" class="<?php if($active_step_model->name == $step_model->name) echo ' active '; ?>">
            <div class="progress-item"><?php echo '<span> '. esc_html($step_model->title) . '</span>'; ?></div>
          </li>
        <?php } ?>
      </ul>
    </div>
    <?php
  }

  public static function load_step( $step_name, $booking_object, $format = 'json'){

    if(OsAuthHelper::is_customer_logged_in() && OsSettingsHelper::get_settings_value('max_future_bookings_per_customer')){
      $customer = OsAuthHelper::get_logged_in_customer();
      if($customer->future_bookings_count >= OsSettingsHelper::get_settings_value('max_future_bookings_per_customer')){
		  	$steps_controller = new OsStepsController();
		    $steps_controller->set_layout('none');
			  $steps_controller->set_return_format($format);
        $steps_controller->format_render('_limit_reached', [], [
          'show_next_btn' => false, 
          'show_prev_btn' => false, 
          'is_first_step' => true, 
          'is_last_step' => true, 
          'is_pre_last_step' => false]);
        return;
      }
    }

    // run prepare step function
		$step_function_name = 'prepare_step_'.$step_name;
  	if(method_exists('OsStepsHelper', $step_function_name)){

  		$result = self::$step_function_name();
      if(is_wp_error($result)){
        wp_send_json(array('status' => LATEPOINT_STATUS_ERROR, 'message' => $result->get_error_message()));
        return;
      }

    	$steps_controller = new OsStepsController();
      $steps_controller->vars = self::$vars_for_view;
      $steps_controller->vars['booking'] = self::$booking_object;
      $steps_controller->vars['current_step'] = $step_name;
      $steps_controller->set_layout('none');
      $steps_controller->set_return_format($format);
      $steps_controller->format_render('_'.$step_name, [], [
        'step_name' 				=> $step_name, 
        'show_next_btn' 		=> self::can_step_show_next_btn($step_name), 
        'show_prev_btn' 		=> self::can_step_show_prev_btn($step_name), 
        'is_first_step' 		=> self::is_first_step($step_name), 
        'is_last_step' 			=> self::is_last_step($step_name), 
        'is_pre_last_step' 	=> self::is_pre_last_step($step_name)]);
  	}

  }

  public static function is_valid_step($step_name = false){
  	if(empty($step_name)) return false;
    return in_array($step_name, self::get_step_names_in_order());
  }


  public static function remove_already_selected_steps(){
    // if current step is agents or services selection and we have it preselected - skip to next step
    if(!empty(self::$restrictions['selected_service'])){
      self::remove_step_by_name('services');
    }
    if(!empty(self::$restrictions['selected_location'])){
      self::remove_step_by_name('locations');
    }
    if(!empty(self::$restrictions['selected_agent'])){
      self::remove_step_by_name('agents');
    }
  }


  public static function remove_step_by_name($step_name){
  	self::$step_names_in_order = array_values(array_diff(self::$step_names_in_order, [$step_name]));
  }



  public static function load_steps_as_models($step_names = []){
    $step_model = new OsStepModel();
    $steps_data = $step_model->select('label, value, step')->where(['step' => $step_names])->get_results(ARRAY_A);
    $steps_data = OsUtilHelper::group_array_by($steps_data, 'step');

    $steps_models = [];
    foreach($step_names as $step_name){
      $step_model = new OsStepModel();
      $step_model->name = $step_name;
      $step_model->set_step_defaults();
      if(isset($steps_data[$step_name])){
        foreach($steps_data[$step_name] as $step_setting){
          $step_model->set_value_by_label($step_setting['label'], $step_setting['value']);
        }
      }
      $steps_models[] = $step_model;
    }
    return $steps_models;
  }


  public static function get_step_names_in_order($show_all_steps = false){
  	if(self::$step_names_in_order) return self::$step_names_in_order;

  // Returns step names in order
    $default_steps = array( 'locations', 'services', 'agents', 'datepicker', 'contact', 'payment', 'verify', 'confirmation');
    $default_steps = apply_filters('latepoint_step_names_in_order', $default_steps, $show_all_steps);

    $steps_model = new OsStepModel();

    $items = $steps_model->select('step')->where(['label' => 'order_number'])->order_by('value ASC')->get_results(ARRAY_A);
    
    if($items && (count($items) >= count($default_steps))){
      $steps = array_map(function($item){ return $item['step']; }, $items);
      $steps = array_values(array_intersect($steps, $default_steps));
      if(empty($steps)) $steps = $default_steps;
    }else{
      $steps = $default_steps;
    }
    if(!$show_all_steps){
      // If we only want to show steps that have been setup correctly
      if(!OsSettingsHelper::is_accepting_payments()){
        // Check if payment processing is setup, if not - remove step payments
        $payment_step_index_key = array_search('payment', $steps);
        if (false !== $payment_step_index_key) {
          unset($steps[$payment_step_index_key]);
          $steps = array_values($steps);
        }
      }
      if(OsLocationHelper::count_locations() <= 1){
        // Check if only one location exist - remove step locations
        $locations_step_index_key = array_search('locations', $steps);
        if (false !== $locations_step_index_key) {
          unset($steps[$locations_step_index_key]);
          $steps = array_values($steps);
        }
      }
      if(OsSettingsHelper::is_on('steps_skip_verify_step')){
        $verify_step_index_key = array_search('verify', $steps);
        if (false !== $verify_step_index_key) {
          unset($steps[$verify_step_index_key]);
          $steps = array_values($steps);
        }
      }
    }
    self::$step_names_in_order = $steps;
    return self::$step_names_in_order;
  }

  public static function set_restrictions($restrictions = array()){

    if(isset($restrictions) && !empty($restrictions)){
      // filter locations
      if(isset($restrictions['show_locations'])) 
        self::$restrictions['show_locations'] = $restrictions['show_locations'];

      // filter agents
      if(isset($restrictions['show_agents'])) 
        self::$restrictions['show_agents'] = $restrictions['show_agents'];

      // filter service category
      if(isset($restrictions['show_service_categories'])) 
        self::$restrictions['show_service_categories'] = $restrictions['show_service_categories'];

      // filter services
      if(isset($restrictions['show_services'])) 
        self::$restrictions['show_services'] = $restrictions['show_services'];

      // preselected service category
      if(isset($restrictions['selected_service_category']) && is_numeric($restrictions['selected_service_category']))
        self::$restrictions['selected_service_category'] = $restrictions['selected_service_category'];

      // preselected calendar start date
      if(isset($restrictions['calendar_start_date']) && OsTimeHelper::is_valid_date($restrictions['calendar_start_date']))
        self::$restrictions['calendar_start_date'] = $restrictions['calendar_start_date'];

      // restriction in settings can ovveride it
      if(OsTimeHelper::is_valid_date(OsSettingsHelper::get_settings_value('earliest_possible_booking')))
        self::$restrictions['calendar_start_date'] = OsSettingsHelper::get_settings_value('earliest_possible_booking');

      // preselected location
      if(isset($restrictions['selected_location']) && is_numeric($restrictions['selected_location'])){
        self::$restrictions['selected_location'] = $restrictions['selected_location'];
        self::$booking_object->location_id = $restrictions['selected_location'];
      }
      // preselected agent
      if(isset($restrictions['selected_agent']) && (is_numeric($restrictions['selected_agent']) || ($restrictions['selected_agent'] == LATEPOINT_ANY_AGENT))){
        self::$restrictions['selected_agent'] = $restrictions['selected_agent'];
        self::$booking_object->agent_id = $restrictions['selected_agent'];
      }

      // preselected service
      if(isset($restrictions['selected_service']) && is_numeric($restrictions['selected_service'])){
        self::$restrictions['selected_service'] = $restrictions['selected_service'];
        self::$booking_object->service_id = $restrictions['selected_service'];
      }
    }
  }

  public static function get_booking_object(){
    return self::$booking_object;
  }

  public static function set_booking_object($booking_object_params = []){
    self::$booking_object = new OsBookingModel();
    self::$booking_object->customer_id = OsAuthHelper::get_logged_in_customer_id();
    self::$booking_object->set_data($booking_object_params);
    if(OsLocationHelper::count_locations() == 1) self::$booking_object->location_id = OsLocationHelper::get_selected_location_id();
  }

  public static function should_step_be_skipped($step_name){
    $skip = false;
		if($step_name == 'payment'){
      if((self::$booking_object->deposit_amount_to_charge() > 0) && !(self::$booking_object->full_amount_to_charge(false) > 0)){
        // if deposit required but charge amount is not set, do not skip the step, charge deposit
        $skip = false;
      }elseif(!(self::$booking_object->full_amount_to_charge() > 0) && !OsSettingsHelper::is_env_demo()){
        // if charge amount after coupons and filters is 0 - skip the step
        $skip = true;
      }
		}
    $skip = apply_filters('latepoint_should_step_be_skipped', $skip, $step_name, self::$booking_object);
		return $skip;
  }

  public static function get_next_step_name($current_step){
    $step_index = array_search($current_step, self::get_step_names_in_order());
    // no more steps
    if(count(self::get_step_names_in_order()) == ($step_index + 1)) return false;
    $next_step = self::get_step_names_in_order()[$step_index + 1];
    if(self::should_step_be_skipped($next_step)){
      $next_step = self::get_next_step_name($next_step);
    }
    return $next_step;
  }

  public static function get_prev_step_name($current_step){
    $step_index = array_search($current_step, self::get_step_names_in_order());
    $prev_index = ($step_index > 0) ? $step_index - 1 : 0;
    $prev_step = self::get_step_names_in_order()[$prev_index];
    if(self::should_step_be_skipped($prev_step)){
      $prev_step = self::get_prev_step_name($prev_step);
    }
    return $prev_step;
  }


  public static function is_first_step($step_name){
    $step_index = array_search($step_name, self::get_step_names_in_order());
    return $step_index == 0;
  }

  public static function is_last_step($step_name){
    $step_index = array_search($step_name, self::get_step_names_in_order());
    return (($step_index + 1) == count(self::get_step_names_in_order()));
  }

  public static function is_pre_last_step($step_name){
    $next_step_name = self::get_next_step_name($step_name);
    $step_index = array_search($next_step_name, self::get_step_names_in_order());
    return (($step_index + 1) == count(self::get_step_names_in_order()));
  }

  public static function can_step_show_prev_btn($step_name){
    $step_index = array_search($step_name, self::get_step_names_in_order());
    // if first or last step
    if($step_index == 0 || (($step_index + 1) == count(self::get_step_names_in_order()))){
      return false;
    }else{
      return true;
    }
  }

  public static function can_step_show_next_btn($step_name){
    $show_payments_next = ((count(OsSettingsHelper::get_payment_methods()) > 1) || self::$booking_object->can_pay_deposit_and_pay_full()) ? false : true;
    $step_show_btn_rules = array('services' => false, 
                                  'locations' => false, 
                                  'agents' => false, 
                                  'datepicker' => false, 
                                  'contact' => true, 
                                  'payment' => $show_payments_next, 
                                  'verify' => true, 
                                  'confirmation' => false);
    $step_show_btn_rules = apply_filters('latepoint_step_show_next_btn_rules', $step_show_btn_rules, $step_name);
    return $step_show_btn_rules[$step_name];
  }


  // LOCATIONS

  public static function process_step_locations(){
  }

  public static function prepare_step_locations(){
    $locations_model = new OsLocationModel();
    $show_selected_locations_arr = (self::$restrictions['show_locations']) ? explode(',', self::$restrictions['show_locations']) : false;
    $connected_ids = OsConnectorHelper::get_connected_object_ids('location_id', ['service_id' => self::$booking_object->service_id, 'agent_id' => self::$booking_object->agent_id]);

    // if show only specific services are selected (restrictions) - remove ids that are not found in connection
    $show_locations_arr = (!empty($show_selected_locations_arr) && !empty($connected_ids)) ? array_intersect($connected_ids, $show_selected_locations_arr) : $connected_ids;
    if(!empty($show_locations_arr)) $locations_model->where_in('id', $show_locations_arr);

    $locations = $locations_model->should_be_active()->order_by('name asc')->get_results_as_models();
    self::$vars_for_view['locations'] = $locations;
  }



  // SERVICES

  public static function process_step_services(){
  }

  public static function prepare_step_services(){
    $services_model = new OsServiceModel();
    $show_selected_services_arr = self::$restrictions['show_services'] ? explode(',', self::$restrictions['show_services']) : false;
    $show_service_categories_arr = self::$restrictions['show_service_categories'] ? explode(',', self::$restrictions['show_service_categories']) : false;
    $preselected_category = self::$restrictions['selected_service_category'];

    $connected_ids = OsConnectorHelper::get_connected_object_ids('service_id', ['agent_id' => self::$booking_object->agent_id, 'location_id' => self::$booking_object->location_id]);
    // if show only specific services are selected (restrictions) - remove ids that are not found in connection
    $show_services_arr = (!empty($show_selected_services_arr) && !empty($connected_ids)) ? array_intersect($connected_ids, $show_selected_services_arr) : $connected_ids;

    if(!empty($show_services_arr)) $services_model->where_in('id', $show_services_arr);

    $services = $services_model->should_be_active()->should_not_be_hidden()->get_results_as_models();

    self::$vars_for_view['show_services_arr'] = $show_services_arr;
    self::$vars_for_view['show_service_categories_arr'] = $show_service_categories_arr;
    self::$vars_for_view['preselected_category'] = $preselected_category;
    self::$vars_for_view['services'] = $services;
  }



  // AGENTS

  public static function process_step_agents(){
  }

  public static function prepare_step_agents(){
    $agents_model = new OsAgentModel();

    $show_selected_agents_arr = (self::$restrictions['show_agents']) ? explode(',', self::$restrictions['show_agents']) : false;
    $connected_ids = OsConnectorHelper::get_connected_object_ids('agent_id', ['service_id' => self::$booking_object->service_id, 'location_id' => self::$booking_object->location_id]);

    // If date/time is selected - filter agents who are available at that time
    if(self::$booking_object->start_date && self::$booking_object->start_time){
      $available_agent_ids = [];
      foreach($connected_ids as $agent_id){
        if(OsAgentHelper::is_agent_available_on($agent_id, self::$booking_object->start_date, self::$booking_object->start_time, self::$booking_object->get_total_duration(), self::$booking_object->service_id, self::$booking_object->location_id, self::$booking_object->total_attendies)) $available_agent_ids[] = $agent_id;
      }
      $connected_ids = (!empty($available_agent_ids) && !empty($connected_ids)) ? array_intersect($available_agent_ids, $connected_ids) : $connected_ids;
    }
    

    // if show only specific agents are selected (restrictions) - remove ids that are not found in connection
    $show_agents_arr = (!empty($show_selected_agents_arr) && !empty($connected_ids)) ? array_intersect($connected_ids, $show_selected_agents_arr) : $connected_ids;
    if(!empty($show_agents_arr)) $agents_model->where_in('id', $show_agents_arr);

    $agents = $agents_model->should_be_active()->get_results_as_models();


    self::$vars_for_view['agents'] = $agents;
  }



  // DATEPICKER

  public static function prepare_step_datepicker(){
    if(empty(self::$booking_object->agent_id)) self::$booking_object->agent_id = LATEPOINT_ANY_AGENT;
    self::$vars_for_view['calendar_start_date'] = self::$restrictions['calendar_start_date'] ? self::$restrictions['calendar_start_date'] : 'today';
  }

  public static function process_step_datepicker(){
  }


  // CONTACT


  public static function prepare_step_contact(){

    if(OsAuthHelper::is_customer_logged_in()){
      self::$booking_object->customer = OsAuthHelper::get_logged_in_customer();
      self::$booking_object->customer_id = self::$booking_object->customer->id;
    }else{
      self::$booking_object->customer = new OsCustomerModel();
    }
    self::$vars_for_view['customer'] = self::$booking_object->customer;
    self::$vars_for_view['custom_fields_for_customer'] = OsCustomFieldsHelper::get_custom_fields_arr('customer');
  }

  public static function process_step_contact(){
    $status = LATEPOINT_STATUS_SUCCESS;

    $customer_params = OsParamsHelper::get_param('customer');
    $booking_params = OsParamsHelper::get_param('booking');
    $logged_in_customer = OsAuthHelper::get_logged_in_customer();

    if($logged_in_customer){
      // LOGGED IN
      // Check if they are changing the email on file
      if($logged_in_customer->email != $customer_params['email']){
        // Check if other customer already has this email
        $customer = new OsCustomerModel();
        $customer_with_email_exist = $customer->where(array('email'=> $customer_params['email'], 'id !=' => $logged_in_customer->id))->set_limit(1)->get_results_as_models();
        if($customer_with_email_exist){
          $status = LATEPOINT_STATUS_ERROR;
          $response_html = __('Another customer is registered with this email.', 'latepoint');
        }
      }
    }else{
      // NEW REGISTRATION
      $customer = new OsCustomerModel();
      $customer_exist = $customer->where(array('email'=> $customer_params['email']))->set_limit(1)->get_results_as_models();
      if($customer_exist){
        // CUSTOMER WITH THIS EMAIL EXISTS - ASK TO LOGIN, CHECK IF CURRENT CUSTOMER WAS REGISTERED AS A GUEST
        if($customer_exist->can_login_without_password() && !OsSettingsHelper::is_on('steps_require_setting_password')){
          $status == LATEPOINT_STATUS_SUCCESS;
          OsAuthHelper::authorize_customer($customer_exist->id);
        }else{
          // Not a guest account, do not allow using it
          $status = LATEPOINT_STATUS_ERROR;
          $response_html = __('An account with that email address already exists. Please try signing in.', 'latepoint');
        }
      }else{
        // if we require password for new accounts - check if password is entered and matches confirmation
        if(OsSettingsHelper::is_on('steps_require_setting_password')){
          if(!empty($customer_params['password']) && $customer_params['password'] == $customer_params['password_confirmation']){
            $customer_params['password'] = OsAuthHelper::hash_password($customer_params['password']);
            $customer_params['is_guest'] = false;
          }else{
            // Password is blank or does not match the confirmation
            $status = LATEPOINT_STATUS_ERROR;
            $response_html = __('Setting password is required and should match password confirmation', 'latepoint');
          }
        }
          
      }
    }
    if($status == LATEPOINT_STATUS_SUCCESS){
      $customer = new OsCustomerModel();
      $is_new_customer = true;
      if(OsAuthHelper::is_customer_logged_in()){
        $customer = OsAuthHelper::get_logged_in_customer();
        if(!$customer->is_new_record()) $is_new_customer = false;
      }
      $customer->set_data($customer_params);
      $custom_fields_data = isset($customer_params['custom_fields']) ? $customer_params['custom_fields'] : [];
      if($customer->validate_custom_fields($custom_fields_data) && $customer->save()){
        $customer->save_custom_fields($custom_fields_data);
        if($is_new_customer){
          OsNotificationsHelper::process_new_customer_notifications($customer);
          OsActivitiesHelper::create_activity(array('code' => 'customer_create', 'customer_id' => $customer->id));
        }

        self::$booking_object->customer_id = $customer->id;
        if(!OsAuthHelper::is_customer_logged_in()){
          OsAuthHelper::authorize_customer($customer->id);
        }
        $customer->set_timezone_name();
      }else{
        $status = LATEPOINT_STATUS_ERROR;
        $response_html = $customer->get_error_messages();
        if(is_array($response_html)) $response_html = implode(', ', $response_html);
      }
    }
    if($status == LATEPOINT_STATUS_ERROR){
      return new WP_Error(LATEPOINT_STATUS_ERROR, $response_html);
    }

  }



  // VERIFICATION STEP

  public static function process_step_verify(){

  }

  public static function prepare_step_verify(){
    self::$vars_for_view['customer'] = new OsCustomerModel(self::$booking_object->customer_id);
    self::$vars_for_view['custom_fields_for_customer'] = OsCustomFieldsHelper::get_custom_fields_arr('customer');
  }

  // PAYMENT

  public static function process_step_payment(){
  }

  public static function prepare_step_payment(){

    $pay_methods = [];
    $pay_times = [];


    if(OsSettingsHelper::is_on('enable_payments')){

      if(OsSettingsHelper::is_accepting_payments_paypal()){
        self::$vars_for_view['paypal_amount_to_charge'] = [LATEPOINT_PAYMENT_PORTION_DEPOSIT => self::$booking_object->specs_calculate_deposit_price_to_charge(LATEPOINT_PAYMENT_METHOD_PAYPAL),
                                                  LATEPOINT_PAYMENT_PORTION_FULL => self::$booking_object->specs_calculate_full_price_to_charge(LATEPOINT_PAYMENT_METHOD_PAYPAL)];
        $pay_times['now'] = '
          <div class="lp-option lp-payment-trigger-paypal" data-method="'.LATEPOINT_PAYMENT_METHOD_PAYPAL.'">
            <div class="lp-option-image-w"><div class="lp-option-image" style="background-image: url('.LATEPOINT_IMAGES_URL.'payment_now_w_paypal.png)"></div></div>
            <div class="lp-option-label">'.__('Pay Now', 'latepoint').'</div>
          </div>';
        $pay_methods['paypal'] = '
          <div class="lp-option lp-option-with-paypal lp-payment-trigger-paypal" data-method="'.LATEPOINT_PAYMENT_METHOD_PAYPAL.'">
            <div class="lp-option-image-w"><div class="lp-option-image" style="background-image: url('.LATEPOINT_IMAGES_URL.'payment_paypal.png)"></div></div>
            <div class="lp-option-label">'.__('PayPal', 'latepoint').'</div>
          </div>';
      }

      if(OsSettingsHelper::is_on('enable_payments_cc')){
        $pay_times['now'] = '
          <div class="lp-option lp-payment-trigger-cc" data-method="'.LATEPOINT_PAYMENT_METHOD_CARD.'">
            <div class="lp-option-image-w"><div class="lp-option-image" style="background-image: url('.LATEPOINT_IMAGES_URL.'payment_cards.png)"></div></div>
            <div class="lp-option-label">'.__('Pay Now', 'latepoint').'</div>
          </div>';
        $pay_methods['cc'] = '
          <div class="lp-option lp-payment-trigger-cc" data-method="'.LATEPOINT_PAYMENT_METHOD_CARD.'">
            <div class="lp-option-image-w"><div class="lp-option-image" style="background-image: url('.LATEPOINT_IMAGES_URL.'payment_cards.png)"></div></div>
            <div class="lp-option-label">'.__('Credit Card', 'latepoint').'</div>
          </div>';
      }

      if(OsSettingsHelper::is_on('enable_payments_cc') && OsSettingsHelper::is_on('enable_payments_paypal')){
        $pay_times['now'] = '
          <div class="lp-option lp-payment-trigger-method-selector">
            <div class="lp-option-image-w"><div class="lp-option-image" style="background-image: url('.LATEPOINT_IMAGES_URL.'payment_now_w_paypal.png)"></div></div>
            <div class="lp-option-label">'.__('Pay Now', 'latepoint').'</div>
          </div>';
      }

      if(OsSettingsHelper::is_on('enable_payments_local')){
        $pay_times['later'] = '
          <div class="lp-option lp-payment-trigger-locally" data-method="'.LATEPOINT_PAYMENT_METHOD_LOCAL.'">
            <div class="lp-option-image-w"><div class="lp-option-image" style="background-image: url('.LATEPOINT_IMAGES_URL.'payment_later.png)"></div></div>
            <div class="lp-option-label">'.__('Pay Locally', 'latepoint').'</div>
          </div>';
      }
    }

    if(count($pay_times) == 2){
      $payment_css_class = 'lp-show-pay-times';
    }elseif(count($pay_methods) == 2){
      $payment_css_class = 'lp-show-pay-methods';
    }else{
      if(self::$booking_object->can_pay_deposit_and_pay_full()){
        // deposit & full payment available
        $payment_css_class = 'lp-show-pay-portion-selection';
        if(OsSettingsHelper::is_on('enable_payments_cc')){
          // cards
          self::$booking_object->payment_method = LATEPOINT_PAYMENT_METHOD_CARD;
        }elseif(OsSettingsHelper::is_on('enable_payments_paypal')){
          // paypal
          self::$booking_object->payment_method = LATEPOINT_PAYMENT_METHOD_PAYPAL;
        }
      }else{
        if(self::$booking_object->can_pay_deposit()){
          // deposit
          self::$booking_object->payment_portion = LATEPOINT_PAYMENT_PORTION_DEPOSIT;
        }elseif(self::$booking_object->can_pay_full()){
          // full payment
          self::$booking_object->payment_portion = LATEPOINT_PAYMENT_PORTION_FULL;
        }
        if(OsSettingsHelper::is_on('enable_payments_cc')){
          $payment_css_class = 'lp-show-card';
          self::$booking_object->payment_method = LATEPOINT_PAYMENT_METHOD_CARD;
        }
        if(OsSettingsHelper::is_on('enable_payments_paypal')){
          $payment_css_class = 'lp-show-paypal';
          self::$booking_object->payment_method = LATEPOINT_PAYMENT_METHOD_PAYPAL;
        }
      }
    }

    self::$vars_for_view['pay_times'] = $pay_times;
    self::$vars_for_view['pay_methods'] = $pay_methods;
    self::$vars_for_view['payment_css_class'] = $payment_css_class;
  }


  // CONFIRMATION

  public static function process_step_confirmation(){
  }

  public static function prepare_step_confirmation(){
    self::$vars_for_view['customer'] = new OsCustomerModel(self::$booking_object->customer_id);
    self::$vars_for_view['custom_fields_for_customer'] = OsCustomFieldsHelper::get_custom_fields_arr('customer');
    if(self::$booking_object->is_new_record()){
      if(!self::$booking_object->save_from_booking_form()){
        OsDebugHelper::log(self::$booking_object->get_error_messages());
        $status = LATEPOINT_STATUS_ERROR;
        $response_html = self::$booking_object->get_error_messages();
        return new WP_Error(LATEPOINT_STATUS_ERROR, $response_html);
      }
    }
  }
}