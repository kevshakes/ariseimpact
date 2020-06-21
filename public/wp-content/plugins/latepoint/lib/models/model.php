<?php 

class OsModel {

  protected $error,
  $db;

  public $nice_names = array();
  protected $comparisons = array('>=', '<=', '<', '>', '!=', 'LIKE');
  protected $conditions = array();
  protected $limit = false;
  protected $offset = false;
  protected $select_args = '*';
  protected $order_args = false;
  protected $group_args = false;
  protected $join_table = false;
  protected $join_on_args = false;
  protected $join_type = '';
  public $last_query = '';
  public $meta = false;

  function __construct($id = false){
    $this->error = false;
    global $wpdb;
    $this->db = $wpdb;
    if($id){
      $this->load_by_id($id);
    }
  }

  public function __get($property){
    $method = "get_$property";
    if(method_exists($this, $method)) return $this->$method();
  }


  public function exists(){
    return (isset($this->id) && !empty($this->id));
  }

  public function formatted_created_date($format = false){
    if(!$format) $format = OsSettingsHelper::get_readable_date_format();
    if(property_exists($this, 'created_at') && isset($this->created_at) && !empty($this->created_at)){
      $date = new OsWpDateTime($this->created_at);
      return $date->format($format);
    }else{
      return 'n/a';
    }
  }

  public function prepare($query, $values){
    if(empty($values)){
      return $query;
    }else{
      return $this->db->prepare($query, $values);
    }
  }


  public function group_by($group_args){
    if($this->group_args){
      $this->group_args = implode(',', array($this->group_args, $group_args));
    }else{
      $this->group_args = $group_args;
    }
    return $this;
  }

  public function get_group_args(){
    if($this->group_args){
      return 'GROUP BY '.$this->group_args;
    }else{
      return '';
    }
  }

  public function order_by($order_args){
    if($this->order_args){
      $this->order_args = implode(',', array($this->order_args, $order_args));
    }else{
      $this->order_args = $order_args;
    }
    return $this;
  }

  public function get_order_args(){
    if($this->order_args){
      return 'ORDER BY '.$this->order_args;
    }else{
      return '';
    }
  }

  public static function where_in_array_to_string($array_of_values){
    $clean_string = '';
    if(is_array($array_of_values)){
      $array_of_values = array_map(function($v) {
          return "'" . esc_sql($v) . "'";
      }, $array_of_values);
      $clean_string = ' (' . implode(',', $array_of_values) . ') ';
    }
    return $clean_string;
  }

  public function where($conditions){
    if(empty($conditions)) return $this;
    $this->conditions = array_merge($this->conditions, $conditions);
    return $this;
  }

  public function where_in($column, $array_of_values){
    $condition = array("{$column} IN " => $array_of_values);
    $this->conditions = array_merge($this->conditions, $condition);
    return $this;
  }

  public function where_not_in($column, $array_of_values){
    $condition = array("{$column} NOT IN " => $array_of_values);
    $this->conditions = array_merge($this->conditions, $condition);
    return $this;
  }

  public function join($table, $on_args, $type = ''){
    $this->join_table = $table;
    $this->join_on_args = $on_args;
    $this->join_type = in_array($type, ['left', 'right']) ? $type : '';
    return $this;
  }

  public function get_join_string(){
    if($this->join_table && $this->join_on_args){
      return $this->join_type.' JOIN '.$this->join_table.' ON '.$this->build_join_args_query();
    }else{
      return '';
    }
  }

  private function build_join_args_query(){
    $join_args_query_arr = [];
    foreach($this->join_on_args as $column_one => $column_two){
      $join_args_query_arr[] = "{$column_one} = {$column_two}";
    }
    return implode(' AND ', $join_args_query_arr);
  }

  public function select($select_args){
    if($this->select_args == '*'){
      $this->select_args = $select_args;
    }else{
      $this->select_args = implode(',', array($this->select_args, $select_args));
    }
    return $this;
  }

  public function get_select_args(){
    return $this->select_args;
  }


  public function set_limit($limit){
    $this->limit = $limit;
    return $this;
  }

  public function count(){
    $count = $this->select('count('.$this->table_name.'.id) as total')->set_limit(1)->get_results();
    $total = ($count) ? $count->total : 0;
    return $total;
  }


  public function set_offset($offset){
    $this->offset = $offset;
    return $this;
  }


  protected function build_conditions_query($conditions, $logical_operator = 'AND'){
    $where_conditions = array();
    $where_values = array();
    $sql_query = '';
    $index = 0;
    if($conditions){
      foreach($conditions as $column => $value){
        $temp_query = false;
        if($column == 'OR' || $column == 'AND'){
          $sql_query.= '(';
            $conditions_and_values = $this->build_conditions_query($value, $column);
            $sql_query.= $conditions_and_values[0];
            $where_values = array_merge($where_values, $conditions_and_values[1]);
          $sql_query.= ')';
        }else{
          // Check if its a comparison condition e.g. <, >, <=, >= etc...
          foreach($this->comparisons as $comparison){
            if(strpos($column, $comparison)){
              $column = str_replace($comparison, '', $column);
              $temp_query = $column.$comparison.' %s';
            }
          }
          // WHERE IN query
          if(strpos($column, ' NOT IN') && is_array($value)){
            $temp_query = $column.OsModel::where_in_array_to_string($value);
            
          }elseif(strpos($column, ' IN') && is_array($value)){
            $temp_query = $column.OsModel::where_in_array_to_string($value);
          }elseif(is_array($value) && (isset($value['OR']) || isset($value['AND']))){
          // IS ARRAY AND OR
            foreach($value as $condition_and_or => $condition_values){

              $temp_query.= '(';
              $sub_queries = [];
              foreach($condition_values as $condition_key => $condition_value){
                if(is_string($condition_key)){
                  $sub_conditions = array($column.$condition_key => $condition_value);
                }else{
                  $sub_conditions = array($column => $condition_value);
                }
                $conditions_and_values = $this->build_conditions_query($sub_conditions, $condition_and_or);
                $sub_queries[] = $conditions_and_values[0];
                $where_values = array_merge($where_values, $conditions_and_values[1]);
              }
              $temp_query.= implode(' '.$condition_and_or.' ', $sub_queries);
              $temp_query.= ')';
            }
          }elseif($value === 'IS NULL'){
          // IS NULL
            $temp_query = $column.' IS NULL ';
          }elseif($value === 'IS NOT NULL'){
          // IS NOT NULL
            $temp_query = $column.' IS NOT NULL ';
          }elseif(is_array($value) && !empty($value)){
            $temp_query = $column.' IN '.OsModel::where_in_array_to_string($value);
          }else{
            // Add to list of query values
            $where_values[] = $value;
          }
          if($temp_query){
            $sql_query.= $temp_query;
          }else{
            $sql_query.= $column.'= %s';
          }
        }
        $index++;
        if($index < count($conditions)) $sql_query.= ' '.$logical_operator.' ';
      }
    }
    return array($sql_query, $where_values);
  }


  public function escape_by_ref(&$string){
    $this->db->escape_by_ref($string);
  }

  public function get_results($results_type = OBJECT){
    $conditions_and_values = $this->build_conditions_query($this->conditions);
    if($conditions_and_values[0]){
      $where_query = 'WHERE '.$conditions_and_values[0];
    }else{
      $where_query = '';
    }
    if($this->limit){
      $limit_query = ' LIMIT %d';
      $conditions_and_values[1][] = $this->limit;
    }else{
      $limit_query = '';
    }


    if($this->offset){
      $offset_query = ' OFFSET %d';
      $conditions_and_values[1][] = $this->offset;
    }else{
      $offset_query = '';
    }

    $query = 'SELECT '.$this->get_select_args().' FROM '.$this->table_name.' '.$this->get_join_string().' '.$where_query.' '.$this->get_group_args().' '.$this->get_order_args().' '.$limit_query.' '.$offset_query;

    $this->last_query = vsprintf($query, $conditions_and_values[1]);
    OsDebugHelper::log($this->last_query);
    
    $items = $this->db->get_results( 
      $this->prepare($query, $conditions_and_values[1])
    , $results_type);

    if(($this->limit == 1) && isset($items[0])){
      $items = $items[0];
    }

    return $items;
  }


  public function get_query_results($query, $values = array(), $results_type = OBJECT){
    $this->last_query = $query;
    $items = $this->db->get_results( 
      $this->prepare($query, $values)
    , $results_type);
    OsDebugHelper::log($query);

    return $items;
  }


  public function reset_conditions(){
    $this->conditions = array();
  }


  public function get_results_as_models($query = false, $values = array()){
    if($query){
      $items = $this->get_query_results($query, $values);
    }else{
      $items = $this->get_results();
    }
    $models = array();
    if(empty($items)) return false;
    if($this->limit == 1) $items = array($items);
    foreach($items as $item){
      $current_class_name = get_class($this);
      $model = new $current_class_name();
      foreach($item as $prop_name => $prop_value){
        $model->$prop_name = $prop_value;
      }
      $models[] = $model;
    }
    $this->reset_conditions();
    if($this->limit == 1 && isset($models[0])) $models = $models[0];
    return $models;
  }


  public function get_image_url($size = 'thumbnail'){
    $url = OsImageHelper::get_image_url_by_id($this->image_id, $size);
    return $url;
  }

  public function set_data($data, $role = 'admin'){
    // OsDebugHelper::log($data);
    if(is_array($data)){
      // array passed
      // if ID is passed and model not loaded from db yet - load data from db
      if(isset($data['id']) && is_numeric($data['id']) && property_exists($this, 'id') && $this->is_new_record()){
        $this->load_by_id($data['id']);
      }
      foreach($this->get_allowed_params($role) as $param){
        if(isset($data[$param])) $this->$param = $data[$param];
      }
    }else{
      // object passed
      // if ID is passed and model not loaded from db yet - load data from db
      if(isset($data->id) && is_numeric($data->id) && property_exists($this, 'id') && $this->is_new_record()){
        $this->load_by_id($data->id);
      }
      foreach($this->get_allowed_params($role) as $param){
        if(isset($data->$param)) $this->$param = $data->$param;
      }
    }
    apply_filters('latepoint_model_set_data', $this);
    return $this;
  }




  public function delete_where($where = false, $where_format = null){
    if(is_array($where) && $this->db->delete( $this->table_name, $where, $where_format)){
      return true;
    }else{
      return false;
    }
  }

  public function delete($id = false){
    if(!$id && isset($this->id)){
      $id = $this->id;
    }
    if($id && $this->db->delete( $this->table_name, array('id' => $id), array( '%d' ))){
      return true;
    }else{
      return false;
    }
  }


  public function load_from_row_data($row_data){
    foreach($row_data as $key => $field){
      if(property_exists($this, $key)) $this->$key = $field;
    }
  }

  public function load_by_id($id){
    $query = $this->prepare('SELECT '.$this->get_select_args().' FROM '.$this->table_name.' WHERE id = %d', $id);
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


  public function is_new_record(){
    if($this->id){
      return false;
    }else{
      return true;
    }
  }

  public function get_field($field_name){
    return $this->$field_name;
  }

  public function set_field($field_name, $field_value){
    $this->$field_name = $field_value;
  }

  protected function before_save(){

  }

  protected function before_create(){

  }

  public function update_attributes($data){
    if($this->is_new_record()) return false;
    $clean_data = array();
    foreach($data as $key => $value){
      if(property_exists($this, $key)){
        if(in_array($key, $this->encrypted_params())) $value = OsEncryptHelper::encrypt_value();
        $clean_data[$key] = $value;
      }
    }
    if(empty($clean_data)){
      return false;
    }else{
      return $this->db->update($this->table_name, $clean_data, array('id' => $this->id));
    }
  }

  protected function set_defaults(){
    
  }

  public function save(){
    $this->set_defaults();
    $this->before_save();
    if($this->validate()){
      if(property_exists($this, 'updated_at')) $this->updated_at = OsTimeHelper::today_date("Y-m-d H:i:s");
      if($this->is_new_record()){
        // New Record (insert)
        $this->before_create();
        if(property_exists($this, 'created_at')) $this->created_at = OsTimeHelper::today_date("Y-m-d H:i:s");
        if(false === $this->db->insert($this->table_name, $this->get_params_to_save_with_values()) && property_exists($this, 'id')){
          return false;
        }else{
          OsDebugHelper::log($this->last_query);
          $this->id = $this->db->insert_id;
        }
      }else{
        // Existing record (update)
        if(false === $this->db->update($this->table_name, $this->get_params_to_save_with_values(), array('id' => $this->id))){
          return false;
        }else{
          OsDebugHelper::log($this->last_query);
        }
      }
    }else{
      return false;  
    }
    return true;
  }


  protected function get_property_nice_name($property){
    if(isset($this->nice_names[$property])){
      return $this->nice_names[$property];
    }else{
      return ucwords(str_replace("_", " ", $property));
    }
  }

  protected function get_params_to_save_with_values($role = 'admin'){
    $params_to_save = $this->get_params_to_save($role);
    $params_to_save_with_values = array();

    foreach($params_to_save as $param_name){
      if(property_exists($this, $param_name)){
        $params_to_save_with_values[$param_name] = $this->prepare_param($param_name, $this->$param_name);
      }
    }
    if(property_exists($this, 'updated_at') && isset($this->updated_at)) $params_to_save_with_values['updated_at'] = $this->updated_at;
    if(property_exists($this, 'created_at') && isset($this->created_at)) $params_to_save_with_values['created_at'] = $this->created_at;
    // OsDebugHelper::log($params_to_save_with_values);
    return $params_to_save_with_values;
  }


  protected function is_encrypted_param($param_name){
    return in_array($param_name, $this->encrypted_params($param_name));
  }

  protected function prepare_param($param_name, $value){
    if(!empty($value)){
      if($this->is_encrypted_param($param_name)){
        $value = OsEncryptHelper::encrypt_value($value);
      }else{
        $value = $value;
      }
    }
    return $value;
  }
  
  protected function encrypted_params(){
    return array();
  }

  // params that are allowed to be mass assigned using set_data method
  protected function allowed_params($role = 'admin'){
    $allowed_params = array();
    return $allowed_params;
  }

  protected function params_to_save($role = 'admin'){
    $allowed_params = array();
    return $allowed_params;
  }

  public function get_params_to_save($role = 'admin'){
    return $this->params_to_save($role);
  }

  public function get_allowed_params($role = 'admin'){
    $allowed_params = $this->allowed_params($role);
    $allowed_params = apply_filters('latepoint_model_allowed_params', $allowed_params, $this, $role);
    return $allowed_params;
  }






  // -------------------------
  // Error handling
  // -------------------------


  // CLEAR
  protected function clear_error(){
    $this->error = false;
  }


  // ADD
  public function add_error($code, $error_message = 'Field is not valid.'){
    if(is_wp_error($this->get_error())){
      $this->get_error()->add($code, $error_message);
    }else{
      $this->error = new WP_Error($code, $error_message);
    }
  }


  // GET
  public function get_error(){
    return $this->error;
  }


  // CHECK
  public function has_validation_error(){
    if(is_wp_error( $this->get_error() ) && $this->get_error()->get_error_messages('validation'))
      return true;
    else
      return false;
  }


  // GET MESSAGES
  public function get_error_messages($code = false){
    if(is_wp_error($this->get_error()))
      return $this->get_error()->get_error_messages($code);
    else
      return false;
  }




  // -------------------------
  // Validations
  // -------------------------

  protected function validate(){
    $this->clear_error();
    foreach($this->properties_to_validate() as $property_name => $validations){
      foreach($validations as $validation){
        $validation_function = 'validates_'.$validation;
        $validation_result = $this->$validation_function($property_name);
        if(is_wp_error($validation_result)){
          $this->add_error('validation', $validation_result->get_error_message($property_name));
        }
      }
    }
    if($this->has_validation_error()){
      return false;
    }else{
      return true;
    }
  }


  protected function properties_to_validate(){
    return array();
  }

  protected function validates_email($property){
    if(isset($this->$property) && !empty($this->$property) && OsUtilHelper::is_valid_email($this->$property)){
      return true;
    }else{
      return new WP_Error($property, sprintf( __( '%s is not valid', 'latepoint' ), $this->get_property_nice_name($property) ));
    }
  }

  protected function validates_presence($property){
    $validation_result = (isset($this->$property) && !empty($this->$property));
    if($validation_result){
      return true;
    }else{
      return new WP_Error($property, sprintf( __( '%s can not be blank', 'latepoint' ), $this->get_property_nice_name($property) ));
    }
  }
  
  protected function validates_uniqueness($property){
    if(isset($this->$property) && !empty($this->$property)){
      if($this->is_new_record()){
        $query = $this->prepare('SELECT '.$property.' FROM '.$this->table_name.' WHERE '.$property.' = %s LIMIT 1', $this->$property);
      }else{
        $query = $this->prepare('SELECT '.$property.' FROM '.$this->table_name.' WHERE '.$property.' = %s AND id != %d LIMIT 1', [$this->$property, $this->id]);
      }
      $items = $this->db->get_results( $query, ARRAY_A);
      if($items){
        return new WP_Error($property, sprintf( __( '%s has to be unique', 'latepoint' ), $this->get_property_nice_name($property) ));
      } 
    }
    return true;
  }

}