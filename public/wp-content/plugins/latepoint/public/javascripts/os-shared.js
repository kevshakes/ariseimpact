function latepoint_mask_timefield($elem){
	if(jQuery().inputmask){
	  $elem.inputmask({
	      'mask': '99:99',
	      'placeholder': 'HH:MM'
	  });
	}
}

function latepoint_mask_phone($elem){
	if(latepoint_is_phone_masking_enabled() && jQuery().inputmask) $elem.inputmask(latepoint_get_phone_format());
}


function latepoint_get_phone_format(){
  return latepoint_helper.phone_format;
}

function latepoint_is_phone_masking_enabled(){
	return (latepoint_helper.enable_phone_masking == 'yes');
}

function latepoint_show_booking_end_time(){
	return (latepoint_helper.show_booking_end_time == 'yes');
}

function latepoint_init_form_masks(){
	if(latepoint_is_phone_masking_enabled()) latepoint_mask_phone(jQuery('.os-mask-phone'));
}

function latepoint_get_paypal_payment_amount($booking_form_wrapper){
	var payment_portion = $booking_form_wrapper.find('input[name="booking[payment_portion]"]').val();
	var payment_amount = (payment_portion == 'deposit') ? $booking_form_wrapper.find('.lp-paypal-btn-trigger').data('deposit-amount') : $booking_form_wrapper.find('.lp-paypal-btn-trigger').data('full-amount');
	return payment_amount;
}