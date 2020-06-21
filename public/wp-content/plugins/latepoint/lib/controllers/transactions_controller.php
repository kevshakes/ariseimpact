<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsTransactionsController' ) ) :


  class OsTransactionsController extends OsController {

    function __construct(){
      parent::__construct();
      
      $this->views_folder = LATEPOINT_VIEWS_ABSPATH . 'transactions/';
      $this->vars['page_header'] = OsMenuHelper::get_menu_items_by_id('appointments');
      $this->vars['breadcrumbs'][] = array('label' => __('Transactions', 'latepoint'), 'link' => OsRouterHelper::build_link(OsRouterHelper::build_route_name('transactions', 'index') ) );
    }

    public function add_for_booking(){
      $booking_id = $this->params['booking_id'];
      if(empty($booking_id)) return false;
      $booking = new OsBookingModel($booking_id);
      if(!empty($booking->id)){
        $transaction = new OsTransactionModel();
        $transaction->amount = isset($this->params['amount']) ? $this->params['amount'] : '';
        $transaction->processor = isset($this->params['processor']) ? $this->params['processor'] : '';
        $transaction->payment_method = isset($this->params['payment_method']) ? $this->params['payment_method'] : '';
        $transaction->token = isset($this->params['token']) ? $this->params['token'] : '';
        $transaction->created_at = isset($this->params['date']) ? $this->params['date'] : '';
        $transaction->booking_id = $booking->id;
        $transaction->customer_id = $this->params['customer_id'];
        $transaction->status = LATEPOINT_TRANSACTION_STATUS_APPROVED;
        if($transaction->save()){
          $status = LATEPOINT_STATUS_SUCCESS;
          $this->set_layout('none');
          $this->vars['transaction'] = $transaction;
          $response_html = $this->render(LATEPOINT_VIEWS_ABSPATH.'bookings/_transaction_box', 'none');
        }else{
          $status = LATEPOINT_STATUS_ERROR;
          $response_html = $transaction->get_error_messages();
        }
      }else{
        $status = LATEPOINT_STATUS_ERROR;
        $response_html = __('Invalid ID', 'latepoint');
      }
      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }

    /*
      Index of transactions
    */

    public function index(){

      $per_page = 15;
      $page_number = isset($this->params['page_number']) ? $this->params['page_number'] : 1;

      $transactions = new OsTransactionModel();
      $count_transactions = new OsTransactionModel();

      if($this->logged_in_agent_id){
        $transactions = $transactions->build_query_transactions_for_agent($this->logged_in_agent_id);
        $total_transactions = $count_transactions->count_transactions_for_agent($this->logged_in_agent_id);
      }else{
        $total_transactions = $count_transactions->count();
      }

      $transactions = $transactions->order_by('created_at desc')->set_limit($per_page);
      if($page_number > 1){
        $transactions = $transactions->set_offset(($page_number - 1) * $per_page);
      }

      

      
      $this->vars['transactions'] = $transactions->get_results_as_models();
      $this->vars['total_transactions'] = $total_transactions;
      $this->vars['per_page'] = $per_page;
      $this->vars['current_page_number'] = $page_number;
      
      $this->vars['showing_from'] = (($page_number - 1) * $per_page) ? (($page_number - 1) * $per_page) : 1;
      $this->vars['showing_to'] = min($page_number * $per_page, $total_transactions);

      $this->format_render(__FUNCTION__);
    }





  }


endif;