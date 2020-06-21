<?php 

class OsPaymentsHelper {

	public static function default_payment_method(){
		$payment_methods = self::get_enabled_payment_methods();
		if(count($payment_methods) == 1){
			return $payment_methods[0];
		}elseif(count($payment_methods) == 0){
			return LATEPOINT_PAYMENT_METHOD_LOCAL;
		}else{
			return '';
		}
	}

	public static function get_enabled_payment_methods(){
		$payment_methods = [];
		if(OsSettingsHelper::is_accepting_payments()){
			if(OsSettingsHelper::is_accepting_payments_cards()) $payment_methods[] = LATEPOINT_PAYMENT_METHOD_CARD;
			if(OsSettingsHelper::is_accepting_payments_paypal()) $payment_methods[] = LATEPOINT_PAYMENT_METHOD_PAYPAL;
			if(OsSettingsHelper::is_accepting_payments_local()) $payment_methods[] = LATEPOINT_PAYMENT_METHOD_LOCAL;
		}
		return $payment_methods;
	}

	public static function process_payment_by_token($token, $booking){
		$result = false;

    $customer = new OsCustomerModel($booking->customer_id);
    if(!$booking->customer_id || !$customer->exists()){
      $result['message'] = __('Error! ISJF723493', 'latepoint');
      $result['status'] = LATEPOINT_STATUS_ERROR;
    }else{
    	if($booking->payment_method == LATEPOINT_PAYMENT_METHOD_PAYPAL){
    		if(OsSettingsHelper::is_using_paypal_braintree_payments()){
	      	$result = OsPaymentsBraintreeHelper::charge_by_token($token, $booking, $customer);
    		}elseif(OsSettingsHelper::is_using_paypal_native_payments()){
	      	$result = OsPaymentsPaypalHelper::charge_by_token($token, $booking, $customer);
    		}
    	}elseif($booking->payment_method == LATEPOINT_PAYMENT_METHOD_CARD){
	      if(OsSettingsHelper::is_using_stripe_payments()){
	      	$result = OsPaymentsStripeHelper::charge_by_token($token, $booking, $customer);
	      }elseif(OsSettingsHelper::is_using_braintree_payments()){
	      	$result = OsPaymentsBraintreeHelper::charge_by_token($token, $booking, $customer);
	      }
    	}
    }
    return $result;
	}

	public static function get_processor_name($payment_method){
		if($payment_method == LATEPOINT_PAYMENT_METHOD_PAYPAL){
			if(OsSettingsHelper::is_using_paypal_native_payments()){
				return LATEPOINT_PAYMENT_PROCESSOR_PAYPAL;
			}else{
				return LATEPOINT_PAYMENT_PROCESSOR_BRAINTREE;
			}
		}else{
			if(OsSettingsHelper::is_using_stripe_payments()){
				return LATEPOINT_PAYMENT_PROCESSOR_STRIPE;
			}elseif(OsSettingsHelper::is_using_braintree_payments()){
				return LATEPOINT_PAYMENT_PROCESSOR_BRAINTREE;
			}
		}
	}

	public static function convert_charge_amount_to_requirements($charge_amount, $payment_method){
		switch (self::get_processor_name($payment_method)) {
			case LATEPOINT_PAYMENT_PROCESSOR_STRIPE:
				return OsPaymentsStripeHelper::convert_charge_amount_to_requirements($charge_amount);
				break;
			case LATEPOINT_PAYMENT_PROCESSOR_BRAINTREE:
				return OsPaymentsBraintreeHelper::convert_charge_amount_to_requirements($charge_amount);
				break;
			case LATEPOINT_PAYMENT_PROCESSOR_PAYPAL:
				return OsPaymentsPaypalHelper::convert_charge_amount_to_requirements($charge_amount);
				break;
		}
	}
}