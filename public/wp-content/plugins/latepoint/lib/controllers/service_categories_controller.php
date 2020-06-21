<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsServiceCategoriesController' ) ) :


  class OsServiceCategoriesController extends OsController {



    function __construct(){
      parent::__construct();

      $this->views_folder = LATEPOINT_VIEWS_ABSPATH . 'service_categories/';
      $this->vars['page_header'] = __('Service Categories', 'latepoint');
      $this->vars['breadcrumbs'][] = array('label' => __('Service Categories', 'latepoint'), 'link' => OsRouterHelper::build_link(OsRouterHelper::build_route_name('service_categories', 'index') ) );
    }



    /*
      Edit service
    */

    public function edit(){
    }



    /*
      New category form
    */

    public function new_form(){
      $this->vars['page_header'] = __('Create New Service', 'latepoint');
      $this->vars['breadcrumbs'][] = array('label' => __('Create New Service', 'latepoint'), 'link' => false );

      $this->vars['category'] = new OsServiceCategoryModel();

      if($this->get_return_format() == 'json'){
        $response_html = $this->render($this->views_folder.'new_form', 'none');
        echo wp_send_json(array('status' => 'success', 'message' => $response_html));
        exit();
      }else{
        echo $this->render($this->views_folder . 'new_form', $this->get_layout());
      }
    }



    /*
      List of categories for select box
    */

    public function list_for_select(){


      $categories = new OsServiceCategoryModel();
      $categories = $categories->get_results();
      $response_html = '<option value="0">'.__('Uncategorized', 'latepoint').'</option>';
      foreach($categories as $category){
        $response_html.= '<option>'.$category->name.'</option>';
      }
      echo wp_send_json(array('status' => 'success', 'message' => $response_html));
    }



    /*
      Index of categories
    */

    public function index(){
      $this->vars['page_header'] = OsMenuHelper::get_menu_items_by_id('services');
      $service_categories = new OsServiceCategoryModel();
      $service_categories = $service_categories->get_results_as_models();
      $this->vars['service_categories'] = $service_categories;

      $services = new OsServiceModel();
      $this->vars['uncategorized_services'] = $services->where(array('category_id' => ['OR' => [0, 'IS NULL']]))->order_by('order_number asc')->get_results_as_models();

      $this->format_render(__FUNCTION__);
    }



    public function destroy(){
      if(filter_var($this->params['id'], FILTER_VALIDATE_INT)){
        $service_category = new OsServiceCategoryModel($this->params['id']);
        if($service_category->delete()){
          $status = LATEPOINT_STATUS_SUCCESS;
          $response_html = __('Service Category Removed', 'latepoint');
        }else{
          $status = LATEPOINT_STATUS_ERROR;
          $response_html = __('Error Removing Service Category. Error: FJI8321', 'latepoint');
        }
      }else{
        $status = LATEPOINT_STATUS_ERROR;
        $response_html = __('Error Removing Service Category. Error: SIF2348', 'latepoint');
      }

      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }



    public function udpate_order_of_categories(){
      foreach($this->params['service_datas'] as $service_data){
        $service = new OsServiceModel($service_data['id']);
        $service->category_id = $service_data['category_id'];
        $service->order_number = $service_data['order_number'];
        if($service->save()){
          $response_html = __('Service Order Updated', 'latepoint');
          $status = LATEPOINT_STATUS_SUCCESS;
        }else{
          $response_html = $service->get_error_messages();
          $status = LATEPOINT_STATUS_ERROR;
          break;
        }
      }
      if($status == LATEPOINT_STATUS_SUCCESS && is_array($this->params['category_datas'])){
        foreach($this->params['category_datas'] as $category_data){
          $service_category = new OsServiceCategoryModel($category_data['id']);
          $service_category->order_number = $category_data['order_number'];
          $service_category->parent_id = ($category_data['parent_id']) ? $category_data['parent_id'] : NULL;
          if($service_category->save()){
            $response_html = __('Service Categories Order Updated', 'latepoint');
            $status = LATEPOINT_STATUS_SUCCESS;
          }else{
            $response_html = $service_category->get_error_messages();
            $status = LATEPOINT_STATUS_ERROR;
            break;
          }
        }
      }
      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }


    /*
      Create service
    */

    public function create(){
      $service_category = new OsServiceCategoryModel();
      $service_category->set_data($this->params['service_category']);
      if($service_category->save()){
        $response_html = __('Service Category Created. ID: ', 'latepoint') . $service_category->id;
        $status = LATEPOINT_STATUS_SUCCESS;
      }else{
        $response_html = $service_category->get_error_messages();
        $status = LATEPOINT_STATUS_ERROR;
      }
      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }


    /*
      Update service category
    */

    public function update(){
      $service_category = new OsServiceCategoryModel($this->params['service_category']['id']);
      $service_category->set_data($this->params['service_category']);
      if($service_category->save()){
        $response_html = __('Service Category Updated. ID: ', 'latepoint') . $service_category->id;
        $status = LATEPOINT_STATUS_SUCCESS;
      }else{
        $response_html = $service_category->get_error_messages();
        $status = LATEPOINT_STATUS_ERROR;
      }
      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }



  }


endif;