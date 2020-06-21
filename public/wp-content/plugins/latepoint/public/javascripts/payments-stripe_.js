'use strict';

function latepoint_register_stripe_card_elements(elements, exampleName) {
  var formClass = '.' + exampleName;
  var example = document.querySelector(formClass);

  var form = example.querySelector('form');
  var error = form.querySelector('.error');
  var errorMessage = error.querySelector('.message');


  // Listen for errors from each Element, and show error messages in the UI.
  elements.forEach(function(element) {
    element.on('change', function(event) {
      if (event.error) {
        error.classList.add('visible');
        errorMessage.innerText = event.error.message;
      } else {
        error.classList.remove('visible');
      }
    });
  });

  // Listen on the form's 'submit' handler...
  form.addEventListener('submit', function(e) {
    e.preventDefault();

    // Show a loading screen...
    example.classList.add('submitting');


  });
}

function latepoint_stripe_create_token(){
  // Gather additional customer data we may have collected in our form.
  var stripe = Stripe('pk_test_V7nM14AfbSELnrPfb4En8EH8');
  var elements = stripe.elements();
  var name = jQuery('#payment_name_on_card');
  var zip = jQuery('#payment_zip');
  var additionalData = {
    name: name ? name.value : undefined,
    address_zip: zip ? zip.value : undefined,
  };

  // Use Stripe.js to create a token. We only need to pass in one Element
  // from the Element group in order to create a token. We can also pass
  // in the additional customer data we collected in our form.
  stripe.createToken(elements[0], additionalData).then(function(result) {
    // Stop loading!
    // example.classList.remove('submitting');

    if (result.token) {
      // If we received a token, show the token ID.
      jQuery('.latepoint-form .token').innerText = result.token.id;
      console.log(result.token.id);
    } else {
      // Otherwise, un-disable inputs.
    }
  });
}

function latepoint_init_stripe_form(){
  var stripe = Stripe('pk_test_V7nM14AfbSELnrPfb4En8EH8');

  var elements = stripe.elements();


  var elementStyles = {
    base: {
      fontFamily: '"Avenir Next W01", -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
      fontSize: '14px',
      fontWeight: 500,
      '::placeholder': {
        color: '#AFB8D6',
      },
    }
  };

  var elementClasses = {
    focus: 'focused',
    empty: 'empty',
    invalid: 'invalid',
  };

  var cardNumber = elements.create('cardNumber', {
    style: elementStyles,
    classes: elementClasses,
    placeholder: jQuery('#payment_card_number').data('placeholder')
  });
  cardNumber.mount('#payment_card_number');

  var cardExpiry = elements.create('cardExpiry', {
    style: elementStyles,
    classes: elementClasses,
    placeholder: jQuery('#payment_card_expiration').data('placeholder')
  });
  cardExpiry.mount('#payment_card_expiration');

  var cardCvc = elements.create('cardCvc', {
    style: elementStyles,
    classes: elementClasses,
    placeholder: jQuery('#payment_card_cvc').data('placeholder')
  });
  cardCvc.mount('#payment_card_cvc');

  latepoint_register_stripe_card_elements([cardNumber, cardExpiry, cardCvc], 'latepoint-lightbox-w');
}