<?php 

class OsCustomFieldsHelper {
  
  function __construct(){
  }

  public static function allowed_fields(){
    $allowed_params = array('label',
                            'placeholder',
                            'type',
                            'width',
                            'options',
                            'required',
                            'id');
    return $allowed_params;
  }

  public static function prepare_to_save($array_to_filter){
    // !!TODO
    return $array_to_filter;
  }

  public static function has_validation_errors($custom_field){
  	$errors = [];
  	if(empty($custom_field['label'])) $errors[] = __('Field Label can not be empty', 'latepoint');
  	if(empty($custom_field['type'])){
  		$errors[] = __('Type can not be empty', 'latepoint');
  	}else{
  		if($custom_field['type'] == 'select'){
		  	if(empty($custom_field['options'])) $errors[] = __('Options for select box can not be blank', 'latepoint');
  		}
  	}
  	if(empty($errors)){
  		return false;
  	}else{
  		return $errors;
  	}
  }

  public static function save($custom_field, $fields_for = 'customer'){
    $custom_fields = OsCustomFieldsHelper::get_custom_fields_arr($fields_for);
    if(!isset($custom_field['id']) || empty($custom_field['id'])){
    	$custom_field['id'] = OsCustomFieldsHelper::generate_custom_field_id($fields_for);
    }
    $custom_fields[$custom_field['id']] = $custom_field;
    return OsCustomFieldsHelper::save_custom_fields_arr($custom_fields, $fields_for);
  }

  public static function delete($custom_field_id, $fields_for = 'customer'){
    if(isset($custom_field_id) && !empty($custom_field_id)){
	    $custom_fields = OsCustomFieldsHelper::get_custom_fields_arr($fields_for);
	    unset($custom_fields[$custom_field_id]);
	    return OsCustomFieldsHelper::save_custom_fields_arr($custom_fields, $fields_for);
	  }else{
	  	return false;
	  }
  }

  public static function generate_custom_field_id(){
  	return 'cf_'.OsUtilHelper::random_text('alnum', 8);
  }

  public static function get_custom_fields_arr($fields_for = 'customer'){
    $custom_fields = OsSettingsHelper::get_settings_value('custom_fields_for_'.$fields_for, false);
    if($custom_fields){
	  	return json_decode($custom_fields, true);
    }else{
    	return [];
    }
  }

  public static function save_custom_fields_arr($custom_fields_arr, $fields_for = 'customer'){
    $custom_fields_arr = self::prepare_to_save($custom_fields_arr);
    return OsSettingsHelper::save_setting_by_name('custom_fields_for_'.$fields_for, json_encode($custom_fields_arr));
  }

}
