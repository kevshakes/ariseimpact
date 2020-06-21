<?php 

class OsLocationHelper {

  static $locations;
  static $selected_location = false;
  static $total_locations;

  public static function locations_selector_html($locations_for_select){
    $html = '';
    if($locations_for_select && (OsLocationHelper::count_locations() > 1) && (count($locations_for_select) > 1)){
      $html.= '<select class="os-main-location-selector" data-route="'.OsRouterHelper::build_route_name('locations', 'set_selected_location').'">';
      foreach($locations_for_select as $location_for_select){
        $selected = (OsLocationHelper::get_selected_location_id() == $location_for_select->id) ? 'selected="selected"' : '';
        $html.= '<option '.$selected.' value="'.$location_for_select->id.'">'.$location_for_select->name.'</option>';
      }
      $html.= '</select>';
    }
    return $html;
  }

  public static function get_locations($agent_id = false){
    $locations = new OsLocationModel();
    if($agent_id) $locations->filter_by_agent_id($agent_id);
    $locations = $locations->get_results_as_models();
    return $locations;
  }

  public static function get_locations_list($agent_id = false){
    $locations = new OsLocationModel();
    if($agent_id) $locations->filter_by_agent_id($agent_id);
    $locations = $locations->get_results_as_models();
    $locations_list = [];
    foreach($locations as $location){
      $locations_list[] = ['value' => $location->id, 'label' => $location->name];
    }
    return $locations_list;
  }

  public static function count_locations($agent_id = false){
    if(self::$total_locations) return self::$total_locations;
    $locations = new OsLocationModel();
    if($agent_id) $locations->filter_by_agent_id($agent_id);
    self::$total_locations = count($locations->get_results_as_models());
    return self::$total_locations;
  }

  public static function set_selected_location($selected_location_id){
    $location_model = new OsLocationModel();
    $location = $location_model->where(['id' => $selected_location_id])->set_limit(1)->get_results_as_models();
    if($location){
      $_SESSION['selected_location_id'] = $selected_location_id;
      self::$selected_location = $location;
      return $location;
    }else{
      return false;
    }
  }

  public static function get_selected_location_id(){
    $selected_location = self::get_selected_location();
    if($selected_location){
      return $selected_location->id;
    }else{
      return false;
    }
  }

  public static function get_selected_location(){
    if(self::$selected_location) return self::$selected_location;

    $selected_location = false;
    $location_model = new OsLocationModel();

    // try get from session
    if(isset($_SESSION['selected_location_id'])){
      $selected_location = $location_model->where(['id' => $_SESSION['selected_location_id']])->set_limit(1)->get_results_as_models();
    }
    // try get first location from db
    if(!$selected_location){
      // location with ID stored in sessions does not exist
      if(OsAuthHelper::is_agent_logged_in()){
        // pull location from database that is assigned to logged in agent
        $location_model = new OsLocationModel();
        $selected_location = $location_model->filter_by_agent_id(OsAuthHelper::get_logged_in_agent_id())->set_limit(1)->get_results_as_models();
        if(!$selected_location){
          // agent has no assigned locations - pull the first one from existing
          $location_model = new OsLocationModel();
          $selected_location = $location_model->set_limit(1)->get_results_as_models();
        }
      }else{
        // admin logged in - pull first one
        $location_model = new OsLocationModel();
        $selected_location = $location_model->set_limit(1)->get_results_as_models();
      }
    }

    if(!$selected_location){
      // still no location found? Create a default one
      $selected_location = self::create_default_location();
    }
    self::set_selected_location($selected_location->id);
    return $selected_location;
  }

  public static function create_default_location(){
    $location_model = new OsLocationModel();
    $location_model->name = __('Main Location', 'latepoint');
    if($location_model->save()){
      $connector = new OsConnectorModel();
      $incomplete_connections = $connector->where(['location_id' => 'IS NULL'])->get_results_as_models();
      if($incomplete_connections){
        foreach($incomplete_connections as $incomplete_connection){
          $incomplete_connection->update_attributes(['location_id' => $location_model->id]);
        }
      }
      $bookings = new OsBookingModel();
      $incomplete_bookings = $bookings->where(['location_id' => 'IS NULL'])->get_results_as_models();
      if($incomplete_bookings){
        foreach($incomplete_bookings as $incomplete_booking){
          $incomplete_booking->update_attributes(['location_id' => $location_model->id]);
        }
      }
    }
    return $location_model;
  }
}