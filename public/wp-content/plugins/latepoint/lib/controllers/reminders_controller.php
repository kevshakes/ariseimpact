<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsRemindersController' ) ) :


  class OsRemindersController extends OsController {

    function __construct(){
      parent::__construct();
      
      $this->views_folder = LATEPOINT_VIEWS_ABSPATH . 'reminders/';
      $this->vars['page_header'] = __('Reminders', 'latepoint');
      $this->vars['breadcrumbs'][] = array('label' => __('Reminders', 'latepoint'), 'link' => OsRouterHelper::build_link(OsRouterHelper::build_route_name('reminders', 'index') ) );
    }

    public function process_reminders(){
      OsRemindersHelper::process_reminders();
    }

    public function index(){
      
      $this->vars['breadcrumbs'][] = array('label' => __('Index', 'latepoint'), 'link' => false );
      $this->vars['page_header'] = OsMenuHelper::get_menu_items_by_id('notifications');
      $this->format_render(__FUNCTION__);
    }



    public function delete(){
      if(isset($this->params['id']) && !empty($this->params['id'])){
        if(OsRemindersHelper::delete($this->params['id'])){
          $status = LATEPOINT_STATUS_SUCCESS;
          $response_html = __('Custom Field Removed', 'latepoint');
        }else{
          $status = LATEPOINT_STATUS_ERROR;
          $response_html = __('Error Removing Custom Field', 'latepoint');
        }
      }else{
        $status = LATEPOINT_STATUS_ERROR;
        $response_html = __('Invalid Field ID', 'latepoint');
      }
      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }

    public function new_form(){
      $this->vars['reminder'] = ['id' => OsRemindersHelper::generate_reminder_id(), 
      'name' => '', 
      'unit' => 'day', 
      'value' => '7', 
      'medium' => 'email', 
      'subject' => '', 
      'receiver' => 'customer', 
      'content' => 'Testing', 
      'medium' => 'email', 
      'when' => 'before'];
      $this->set_layout('none');
      $this->format_render(__FUNCTION__);
    }

    public function save(){
      if($this->params['reminders']){
        foreach($this->params['reminders'] as $reminder){
          $validation_errors = OsRemindersHelper::has_validation_errors($reminder);
          if(is_array($validation_errors)){
            $status = LATEPOINT_STATUS_ERROR;
            $response_html = implode(', ', $validation_errors);
          }else{
            if(OsRemindersHelper::save($reminder)){
              $status = LATEPOINT_STATUS_SUCCESS;
              $response_html = __('Custom Field Saved', 'latepoint');
            }else{
              $status = LATEPOINT_STATUS_ERROR;
              $response_html = __('Error Saving Custom Field', 'latepoint');
            }
          }
        }
      }else{
        $status = LATEPOINT_STATUS_ERROR;
        $response_html = __('Invalid params', 'latepoint');
      }
      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }


  }
endif;