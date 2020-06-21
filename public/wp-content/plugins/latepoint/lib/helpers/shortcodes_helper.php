<?php 

class OsShortcodesHelper {

  // [latepoint_book_form]
  public static function shortcode_latepoint_book_form( $atts, $content = "" ) {
      $atts = shortcode_atts( array(
          'show_locations' => false,
          'show_agents' => false,
          'show_services' => false,
          'show_service_categories' => false,
          'selected_location' => false,
          'selected_agent' => false,
          'selected_service' => false,
          'selected_service_category' => false,
          'calendar_start_date' => false,
          'hide_side_panel' => false,
          'hide_summary' => false
      ), $atts );
      $nonce = wp_create_nonce("latepoint_nonce");

      $data_atts = '';


      // Data attributes setup
      $restrictions = [];
      if($atts['show_locations']) $restrictions['show_locations'] = $atts['show_locations'];
      if($atts['show_agents']) $restrictions['show_agents'] = $atts['show_agents'];
      if($atts['show_services']) $restrictions['show_services'] = $atts['show_services'];
      if($atts['show_service_categories']) $restrictions['show_service_categories'] = $atts['show_service_categories'];
      if($atts['selected_location']) $restrictions['selected_location'] = $atts['selected_location'];
      if($atts['selected_agent']) $restrictions['selected_agent'] = $atts['selected_agent'];
      if($atts['selected_service']) $restrictions['selected_service'] = $atts['selected_service'];
      if($atts['selected_service_category']) $restrictions['selected_service_category'] = $atts['selected_service_category'];
      if($atts['calendar_start_date']) $restrictions['calendar_start_date'] = $atts['calendar_start_date'];
      
      $steps_controller = new OsStepsController();
      $summary_class = ($atts['hide_summary'] == 'yes') ? '' : 'latepoint-with-summary';
      $side_panel_class = ($atts['hide_side_panel'] == 'yes') ? 'latepoint-hide-side-panel' : '';
      $output = '<div class="latepoint-w latepoint-shortcode-booking-form '.$summary_class.' '.$side_panel_class.'">';
      $output.= $steps_controller->start($restrictions, false);
      $output.= '</div>';
      return $output;
  }


  // [latepoint_book_button]
  public static function shortcode_latepoint_book_button( $atts, $content = "" ) {
      $atts = shortcode_atts( array(
          'caption' => __('Book Appointment', 'latepoint'),
          'bg_color' => false,
          'text_color' => false,
          'font_size' => false,
          'border' => false,
          'border_radius' => false,
          'margin' => false,
          'padding' => false,
          'css' => false,
          'show_locations' => false,
          'show_agents' => false,
          'show_services' => false,
          'show_service_categories' => false,
          'selected_location' => false,
          'selected_agent' => false,
          'selected_service' => false,
          'selected_service_category' => false,
          'calendar_start_date' => false,
          'hide_side_panel' => false,
          'hide_summary' => false
      ), $atts );

      $nonce = wp_create_nonce("latepoint_nonce");

      $style = '';
      $data_atts = '';

      // Style setup
      if($atts['bg_color']) $style.= 'background-color: '.$atts['bg_color'].';';
      if($atts['text_color']) $style.= 'color: '.$atts['text_color'].';';
      if($atts['font_size']) $style.= 'font-size: '.$atts['font_size'].';';
      if($atts['border']) $style.= 'border: '.$atts['border'].';';
      if($atts['border_radius']) $style.= 'border-radius: '.$atts['border_radius'].';';
      if($atts['margin']) $style.= 'margin: '.$atts['margin'].';';
      if($atts['padding']) $style.= 'padding: '.$atts['padding'].';';


      // Data attributes setup
      if($atts['show_locations']) $data_atts.= 'data-show-locations="'.$atts['show_locations'].'" ';
      if($atts['show_agents']) $data_atts.= 'data-show-agents="'.$atts['show_agents'].'" ';
      if($atts['show_services']) $data_atts.= 'data-show-services="'.$atts['show_services'].'" ';
      if($atts['show_service_categories']) $data_atts.= 'data-show-service-categories="'.$atts['show_service_categories'].'" ';
      if($atts['selected_location']) $data_atts.= 'data-selected-location="'.$atts['selected_location'].'" ';
      if($atts['selected_agent']) $data_atts.= 'data-selected-agent="'.$atts['selected_agent'].'" ';
      if($atts['selected_service']) $data_atts.= 'data-selected-service="'.$atts['selected_service'].'" ';
      if($atts['selected_service_category']) $data_atts.= 'data-selected-service-category="'.$atts['selected_service_category'].'" ';
      if($atts['calendar_start_date']) $data_atts.= 'data-calendar-start-date="'.$atts['calendar_start_date'].'" ';


      if($atts['hide_summary'] == 'yes') $data_atts.= 'data-hide-summary="yes" ';
      if($atts['hide_side_panel'] == 'yes') $data_atts.= 'data-hide-side-panel="yes" ';
      
      if(($style == '') && $atts['css']) $style = $atts['css'];

      if($style != '') $style = 'style="'.$style.'"';


      $output = '<div class="latepoint-book-button os_trigger_booking" '.$data_atts.' '.$style.' data-nonce="'.$nonce.'">'.esc_attr($atts['caption']).'</div>';
      
      return $output;
  }

  // [latepoint_customer_dashboard]
  public static function shortcode_latepoint_customer_dashboard($atts){
    $atts = shortcode_atts( array(
        'caption' => __('Book Appointment', 'latepoint')
    ), $atts );

    $customersController = new OsCustomersController();
    $output = $customersController->dashboard();
    return $output;
  }

  // [latepoint_customer_login]
  public static function shortcode_latepoint_customer_login($atts){
    $atts = shortcode_atts( array(
        'caption' => __('Book Appointment', 'latepoint')
    ), $atts );

    $customersController = new OsCustomersController();
    $output = $customersController->login();
    return $output;
  }

}