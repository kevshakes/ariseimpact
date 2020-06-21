<?php 

class OsFormHelper {

  public static function atts_string_from_array($atts = array(), $join_atts = array()){
    $atts_str = '';
    if(!empty($atts)){
      if(isset($atts['add_string_to_id'])) unset($atts['add_string_to_id']);
      foreach($atts as $key => $value){
        if(isset($join_atts[$key])){
          $value.= ' '.$join_atts[$key];
          unset($join_atts[$key]);
        }
        $atts_str.= $key.'="'.$value.'" ';
      }
    }
    if(!empty($join_atts)){
      foreach($join_atts as $key => $value){
        $atts_str.= $key.'="'.$value.'" ';
      }
    }
    return $atts_str;
  }



  public static function media_uploader_field($name, $post_id = 0, $label_set_str, $label_remove_str, $value_image_id = false, $atts = array(), $wrapper_atts = array(), $is_avatar = false){
    $upload_link = esc_url( get_upload_iframe_src( 'image', $post_id ) );
    $img_html = '';
    $has_image_class = '';
    $label_str = $label_set_str;

    // Image is set
    if($value_image_id){
      $image_url = OsImageHelper::get_image_url_by_id($value_image_id);
      $img_html = '<img src="'.$image_url.'"/>';
      $has_image_class = 'has-image';
      $label_str = $label_remove_str;
    }

    $is_avatar_class = $is_avatar ? ' is-avatar ' : '';
    $html = '';
    $html.= '<div class="os-image-selector-w '.$is_avatar_class.'">';
      $html.= '<a href="'.$upload_link.'" data-label-remove-str="'.$label_remove_str.'" data-label-set-str="'.$label_set_str.'"'.self::atts_string_from_array($wrapper_atts, ['class' => "os-image-selector-trigger"]).'>';
        $html.= '<div class="os-image-container '.$has_image_class.'">'.$img_html.'</div>';
        $html.= '<div class="os-image-selector-text"><span class="os-text-holder">'.$label_str.'</span></div>';
      $html.= '</a>';
      $html.= '<input type="hidden" name="'.$name.'" value="'.esc_attr($value_image_id).'" '.self::atts_string_from_array($atts, ['class' => 'os-image-id-holder']).'/>';
    $html.= '</div>';
    return $html;
  }

  public static function file_upload_field($name, $label, $atts = array()){
    // generate id if not set
    if(!isset($atts['id'])) $atts['id'] = self::name_to_id($name, $atts);
    $html = '<div class="os-form-group">';
      if($label) $html.= '<label for="'.$name.'">'.$label.'</label>';
      $html.= '<input type="file" name="'.$name.'"  multiple="false" '.self::atts_string_from_array($atts).'/>';
    $html.= '</div>';
    return $html;
  }

  public static function generate_select_options_from_custom_field($options){
    if(!empty($options)){
      return preg_split('/\r\n|\r|\n/', $options);
    }else{
      return [];
    }
  }

  public static function wp_editor_field($name, $id, $label, $content, $atts = array()){
    $editor_height = isset($atts['editor_height']) ? $atts['editor_height'] : 300;
    echo '<div class="os-form-group os-form-control-wp-editor-group">';
      echo '<label for="'.$name.'">'.$label.'</label>';
      wp_editor($content, $id, array('textarea_name' => $name, 'media_buttons' => false, 'editor_height' => $editor_height));
    echo '</div>';
  }


  public static function textarea_field($name, $label, $value = '', $atts = array(), $wrapper_atts = array()){
    $extra_class = '';
    // generate id if not set
    if(!isset($atts['id'])) $atts['id'] = self::name_to_id($name, $atts);
    if($value) $extra_class = ' has-value';
    $html = '<div '.self::atts_string_from_array($wrapper_atts).'>';
      $html.= '<div '.self::atts_string_from_array(array('class' => 'os-form-group os-form-textfield-group os-form-textarea-group os-form-group-transparent'.$extra_class)).'>';
        if($label) $html.= '<label for="'.$atts['id'].'">'.$label.'</label>';
        $html.= '<textarea type="text" placeholder="'.$label.'" name="'.$name.'" '.self::atts_string_from_array($atts, ['class' => 'os-form-control']).'>'.$value.'</textarea>';
      $html.= '</div>';
    $html.= '</div>';
    return $html;
  }

  public static function service_selector_adder_field($name, $label, $add_label, $options = array(), $value = '', $atts = array(), $wrapper_atts = array()){
    $html = '<div '.self::atts_string_from_array($wrapper_atts, ['class' => 'os-form-group os-form-select-group os-form-group-transparent service-selector-adder-field-w']).'>';
      if($label) $html.= '<label for="'.$name.'">'.$label.'</label>';
      $html.= '<div class="selector-adder-w">';
        $html.= '<select name="'.$name.'" '.self::atts_string_from_array($atts, ['class' => 'os-form-control']).' data-select-source="'.OsRouterHelper::build_route_name('service_categories', 'list_for_select').'">';
        foreach($options as $option){
          if(isset($option['value']) && isset($option['label'])){
            $selected = ($value == $option['value']) ? 'selected' : '';
            $html.= '<option value="'.$option['value'].'" '.$selected.'>'.$option['label'].'</option>';
          }
        }
        $html.='</select>';
        $html.='<button class="latepoint-btn latepoint-btn-primary" data-os-action="'.OsRouterHelper::build_route_name('service_categories', 'new_form').'" data-os-output-target="lightbox"><i class="latepoint-icon latepoint-icon-plus"></i> <span>'.$add_label.'</span></button>';
      $html.= '</div>';
    $html.= '</div>';
    return $html;
  }

  public static function select_field($name, $label, $options = array(), $selected_value = '', $atts = array(), $wrapper_atts = array()){
    $html = '';
    // generate id if not set
    if(!isset($atts['id'])) $atts['id'] = self::name_to_id($name, $atts);
    if(!empty($wrapper_atts)) $html = '<div '.self::atts_string_from_array($wrapper_atts).'>';
      $html.= '<div class="os-form-group os-form-select-group os-form-group-transparent">';
        if($label) $html.= '<label for="'.$atts['id'].'">'.$label.'</label>';
        $html.= '<select name="'.$name.'" '.self::atts_string_from_array($atts, ['class' => 'os-form-control']).'>';
        if(isset($atts['placeholder'])) $html.= '<option value="">'.$atts['placeholder'].'</option>';
        if(is_array($options)){
          foreach($options as $key => $option){
            if(isset($option['value']) && isset($option['label'])){
              $selected = ($selected_value == $option['value']) ? 'selected' : '';
              $html.= '<option value="'.$option['value'].'" '.$selected.'>'.$option['label'].'</option>';
            }else{
              $value = (is_string($key)) ? $key : $option;
              $selected = ($selected_value == $value) ? 'selected' : '';
              $html.= '<option value="'.$value.'" '.$selected.'>'.$option.'</option>';
            }
          }
        }else{
          $html.= $options;
        }
        $html.='</select>';
      $html.= '</div>';
    if(!empty($wrapper_atts)) $html.= '</div>';
    return $html;
  }

  public static function time_field($name, $label, $value = '', $as_period = false){
    if(strpos($value, ':') === false){
      $formatted_value = OsTimeHelper::minutes_to_hours_and_minutes($value, false, false);
    }

    $extra_class = '';
    if($as_period) $extra_class = 'as-period';

    $html = '<div class="os-time-group os-time-input-w '.$extra_class.'">';
      if($label) $html.= '<label for="'.$name.'">'.$label.'</label>';
      $html.= '<div class="os-time-input-fields">';
        $html.= '<input type="text" placeholder="HH:MM" name="'.$name.'[formatted_value]" value="'.$formatted_value.'" class="os-form-control os-mask-time"/>';

        // am-pm toggler switch
        if(!OsTimeHelper::is_army_clock()){
          $is_am = (OsTimeHelper::am_or_pm($value) == 'am');
          $am_active = ($is_am) ? 'active' : '';
          $pm_active = (!$is_am) ? 'active' : '';
          $html.= '<input type="hidden" name="'.$name.'[ampm]" value="'.OsTimeHelper::am_or_pm($value).'" class="ampm-value-hidden-holder"/>';
          $html.= '<div class="time-ampm-w"><div class="time-ampm-select time-am '.$am_active.'" data-ampm-value="am">'.__('am', 'latepoint').'</div><div class="time-ampm-select time-pm '.$pm_active.'" data-ampm-value="pm">'.__('pm', 'latepoint').'</div></div>';
        }

      $html.= '</div>';
    $html.= '</div>';
    return $html;
  }


  public static function color_picker($name, $label, $value = '', $atts = array(), $wrapper_atts = array()){
    $extra_class = '';
    if($value != '') $extra_class = ' has-value';
    $html = '';
    if(!empty($wrapper_atts)) $html = '<div '.self::atts_string_from_array($wrapper_atts).'>';
      $html.= '<div '.self::atts_string_from_array(array('class' => 'os-form-group os-form-group-transparent os-form-color-picker-group'.$extra_class)).'>';
        if($label) $html.= '<label for="'.$name.'">'.$label.'</label>';
        $html.= '<div class="latepoint-color-picker-w">';
          $html.= '<div class="latepoint-color-picker" data-color="'.$value.'"></div>';
          $html.= '<input type="text" name="'.$name.'" placeholder="'.__('Pick a color', 'latepoint').'" value="'.$value.'" '.self::atts_string_from_array($atts, ['class' => 'os-form-control']).'/>';
        $html.= '</div>';
      $html.= '</div>';
    if(!empty($wrapper_atts)) $html.= '</div>';
    return $html;
  }

  public static function name_to_id($name, $atts){
    $name = strtolower(preg_replace('/[^0-9a-zA-Z_]/', '_', $name));
    $name = preg_replace('/__+/', '_', $name);
    $name = rtrim($name, '_');
    if(isset($atts['add_unique_id']) && $atts['add_unique_id']) $name.= '_'.OsUtilHelper::random_text('hexdec', 8);
    if(isset($atts['add_string_to_id']) && $atts['add_string_to_id']) $name.= $atts['add_string_to_id'];
    return $name;
  }


  // Value: on
  public static function checkbox_field($name, $label, $value = '', $is_checked = false, $atts = array(), $wrapper_atts = array(), $off_value = 'off'){
    $html = '';
    // generate id if not set
    if(!isset($atts['id'])) $atts['id'] = self::name_to_id($name, $atts);
    if(!empty($wrapper_atts)) $html.= '<div '.self::atts_string_from_array($wrapper_atts).'>';
      $checked_class = $is_checked ? 'is-checked' : '';
      if(isset($atts['data-toggle-element'])) $checked_class.= ' has-toggle-element';
      if(isset($atts['data-inverse-toggle'])) $checked_class.= ' inverse-toggle';
      $checked_attr = $is_checked ? 'checked' : '';
      $html.= '<div '.self::atts_string_from_array(array('class' => 'os-form-group os-form-checkbox-group '.$checked_class)).'>';
        if($label) $html.= '<label for="'.$atts['id'].'">';
          $html.= '<input type="hidden" name="'.$name.'" value="'.$off_value.'"/>';
          $html.= '<input type="checkbox" name="'.$name.'" value="'.$value.'" '.$checked_attr.' '.self::atts_string_from_array($atts, ['class' => 'os-form-checkbox']).'/>';
        if($label) $html.= $label.'</label>';
      $html.= '</div>';
    if(!empty($wrapper_atts)) $html.= '</div>';
    return $html;
  }

  public static function text_field($name, $label, $value = '', $atts = array(), $wrapper_atts = array()){
    $extra_class = '';
    // generate id if not set
    if(!isset($atts['id'])) $atts['id'] = self::name_to_id($name, $atts);
    if($value != '') $extra_class = ' has-value';
    if(empty($label)) $extra_class.= ' no-label';
    $html = '';
    if(!empty($wrapper_atts)) $html = '<div '.self::atts_string_from_array($wrapper_atts).'>';
      $html.= '<div class ="os-form-group os-form-group-transparent os-form-textfield-group'.$extra_class.'">';
        $placeholder = isset($atts['placeholder']) ? $atts['placeholder'] : $label;
        if($label) $html.= '<label for="'.$atts['id'].'">'.$label.'</label>';
        $html.= '<input type="text" placeholder="'.esc_attr($placeholder).'" name="'.esc_attr($name).'" value="'.esc_attr($value).'" '.self::atts_string_from_array($atts, ['class' => 'os-form-control']).'/>';
      $html.= '</div>';
    if(!empty($wrapper_atts)) $html.= '</div>';
    return $html;
  }

  public static function password_field($name, $label, $value = '', $atts = array(), $wrapper_atts = array()){
    $extra_class = '';
    $html = '';
    // generate id if not set
    if(!isset($atts['id'])) $atts['id'] = self::name_to_id($name, $atts);
    if($value) $extra_class = ' has-value';
    if(empty($label)) $extra_class.= ' no-label';
    if(!empty($wrapper_atts)) $html = '<div '.self::atts_string_from_array($wrapper_atts).'>';
      $html.= '<div '.self::atts_string_from_array(array('class' => 'os-form-group os-form-group-transparent os-form-textfield-group'.$extra_class)).'>';
        $placeholder = isset($atts['placeholder']) ? $atts['placeholder'] : $label;
        if($label) $html.= '<label for="'.$atts['id'].'">'.$label.'</label>';
        $html.= '<input type="password" placeholder="'.$placeholder.'" name="'.$name.'" value="'.$value.'" '.self::atts_string_from_array($atts, ['class' => 'os-form-control']).'/>';
      $html.= '</div>';
    if(!empty($wrapper_atts)) $html.= '</div>';
    return $html;
  }

  public static function hidden_field($name, $value, $atts = array()){
    // generate id if not set
    if(!isset($atts['id'])) $atts['id'] = self::name_to_id($name, $atts);
    $html = '<input type="hidden" name="'.$name.'" value="'.esc_attr($value).'" '.self::atts_string_from_array($atts).'/>';
    return $html;
  }

  public static function button($name, $label, $type = 'button', $atts = array()){
    // generate id if not set
    if(!isset($atts['id'])) $atts['id'] = self::name_to_id($name, $atts);
    $html = '<div class="os-form-group">';
      $html.= '<button type="'.$type.'" name="'.$name.'" '.self::atts_string_from_array($atts).'>'.$label.'</button>';
    $html.= '</div>';
    return $html;
  }
}