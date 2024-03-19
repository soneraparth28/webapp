//<--------- Start Payment -------//>
(function($) {
	"use strict";

	$('input[name=payment_gateway_ppv]').on('click', function() {
    if($(this).val() == 2) {
      $('#stripeContainerPPV').slideDown();
    } else {
      $('#stripeContainerPPV').slideUp();
    }
  });

 //<---------------- Pay PPV ----------->>>>
 if (stripeKey != '') {

 // Create a Stripe client.
 var stripe = Stripe(stripeKey);

 // Create an instance of Elements.
 var elements = stripe.elements();

 // Custom styling can be passed to options when creating an Element.
 // (Note that this demo uses a wider set of styles than the guide below.)
 var style = {
	 base: {
		 color: colorStripe,
		 fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
		 fontSmoothing: 'antialiased',
		 fontSize: '16px',
		 '::placeholder': {
			 color: '#aab7c4'
		 }
	 },
	 invalid: {
		 color: '#fa755a',
		 iconColor: '#fa755a'
	 }
 };

 // Create an instance of the card Element.
 var cardElement = elements.create('card', {style: style, hidePostalCode: true});

 // Add an instance of the card Element into the `card-elementPPV` <div>.
 cardElement.mount('#card-elementPPV');

 // Handle real-time validation errors from the card Element.
 cardElement.addEventListener('change', function(event) {
	 var displayError = document.getElementById('card-errorsPPV');
	 var payment = $('input[name=payment_gateway_ppv]:checked').val();

	 if (payment == 2) {
		 if (event.error) {
			 displayError.classList.remove('display-none');
			 displayError.textContent = event.error.message;
			 $('#ppvBtn').removeAttr('disabled');
			 $('#ppvBtn').find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
		 } else {
			 displayError.classList.add('display-none');
			 displayError.textContent = '';
		 }
	 }

 });

 var cardholderName = document.getElementById('cardholder-name-PPV');
 var cardholderEmail = document.getElementById('cardholder-email-PPV');
 var cardButton = document.getElementById('ppvBtn');

 cardButton.addEventListener('click', function(ev) {

	 var payment = $('input[name=payment_gateway_ppv]:checked').val();

	 if (payment == 2) {

	 stripe.createPaymentMethod('card', cardElement, {
		 billing_details: {name: cardholderName.value, email: cardholderEmail.value}
	 }).then(function(result) {
		 if (result.error) {

			 if (result.error.type == 'invalid_request_error') {

					 if(result.error.code == 'parameter_invalid_empty') {
						 $('.popout').addClass('popout-error').html(error).fadeIn('500').delay('8000').fadeOut('500');
					 } else {
						 $('.popout').addClass('popout-error').html(result.error.message).fadeIn('500').delay('8000').fadeOut('500');
					 }
			 }
			 $('#ppvBtn').removeAttr('disabled');
			 $('#ppvBtn').find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');

		 } else {

			 $('#ppvBtn').attr({'disabled' : 'true'});
			 $('#ppvBtn').find('i').addClass('spinner-border spinner-border-sm align-middle mr-1');

			 // Otherwise send paymentMethod.id to your server
			 $('input[name=payment_method_id]').remove();

			 var $input = $('<input id=payment_method_id type=hidden name=payment_method_id />').val(result.paymentMethod.id);
			 $('#formSendPPV').append($input);

			 $.ajax({
			 headers: {
					 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				 },
				type: "POST",
				dataType: 'json',
				url: URL_BASE+"/send/ppv",
				data: $('#formSendPPV').serialize(),
				success: function(result) {
						//handleServerResponse(result);

						if(result.success == true && result.url){
							window.location.href = result.url;
						}

						if(result.success == false) {
							$('#ppvBtn').removeAttr('disabled');
							$('#ppvBtn').find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
						}
			}//<-- RESULT
			})

		 }//ELSE
	 });
 }//PAYMENT STRIPE
});

 function handleServerResponse(response) {
	 if (response.error) {
		 $('.popout').addClass('popout-error').html(response.error).fadeIn('500').delay('8000').fadeOut('500');
		 $('#ppvBtn').removeAttr('disabled');
		 $('#ppvBtn').find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');

	 } else if (response.requires_action) {
		 // Use Stripe.js to handle required card action
		 stripe.handleCardAction(
			 response.payment_intent_client_secret
		 ).then(function(result) {
			 if (result.error) {
				 $('.popout').addClass('popout-error').html(error_payment_stripe_3d).fadeIn('500').delay('10000').fadeOut('500');
				 $('#ppvBtn').removeAttr('disabled');
				 $('#ppvBtn').find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');

			 } else {
				 // The card action has been handled
				 // The PaymentIntent can be confirmed again on the server

				 var $input = $('<input type=hidden name=payment_intent_id />').val(result.paymentIntent.id);
				 $('#formSendPPV').append($input);

				 $('input[name=payment_method_id]').remove();

				 $.ajax({
				 headers: {
						 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					 },
					type: "POST",
					dataType: 'json',
					url: URL_BASE+"/send/ppv",
					data: $('#formSendPPV').serialize(),
					success: function(result){

						if(result.success) {

							if (result.data) {
								$('.chatlist[data=' + result.msgId + ']').html('');
								$('.chatlist[data=' + result.msgId + ']').html(result.data);

								jQuery(".timeAgo").timeago();

				          new SmartPhoto(".js-smartPhoto",{
										resizeStyle: 'fit',
										showAnimation: false,
										nav: false,
										useHistoryApi: false
									});

									const players = Plyr.setup('.js-player');

									$('#payPerViewForm').modal('hide');
				 	 				$('.InputElement').val('');
				 	 				$('#formSendPPV').trigger("reset");
				 	 				$('#ppvBtn').removeAttr('disabled');
				 	 				$('#ppvBtn').find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
									$('.balanceWallet').html(result.wallet);

							} else {
								window.location.href = result.url;
							}

						} else {
							$('.popout').addClass('popout-error').html(result.error).fadeIn('500').delay('8000').fadeOut('500');
							$('#ppvBtn').removeAttr('disabled');
							$('#ppvBtn').find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
						}
				}//<-- RESULT
				})
			 }// ELSE
		 });
	 } else {
		 // Show success message
		 if (response.success) {

			 if (response.data) {
				 $('.chatlist[data=' + response.msgId + ']').html('');
				 $('.chatlist[data=' + response.msgId + ']').html(response.data);

				 jQuery(".timeAgo").timeago();

					 new SmartPhoto(".js-smartPhoto",{
						 resizeStyle: 'fit',
						 showAnimation: false,
						 nav: false,
						 useHistoryApi: false
					 });

					 const players = Plyr.setup('.js-player');

					 $('#payPerViewForm').modal('hide');
					 $('.InputElement').val('');
					 $('#formSendPPV').trigger("reset");
					 $('#ppvBtn').removeAttr('disabled');
					 $('#ppvBtn').find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
					 $('.balanceWallet').html(response.wallet);

			 } else {
				 window.location.href = response.url;
			 }
		 }
	 }
 }
}
// Stripe Elements


//<---------------- Pay PPV ----------->>>>
 $(document).on('click','.ppvBtn',function(s) {

	 s.preventDefault();
	 var element = $(this);
	 var form = $(this).attr('data-form');
	 element.attr({'disabled' : 'true'});
	 var payment = $('input[name=payment_gateway_ppv]:checked').val();
	 element.find('i').addClass('spinner-border spinner-border-sm align-middle mr-1');

	 (function(){
			$('#formSendPPV').ajaxForm({
			dataType : 'json',
			success:  function(result) {

				// Wallet
				if(result.success == true && result.public_id)
				{
					RevolutCheckout(result.public_id, result.mode).then(function (RC) {
                
						var card = RC.createCardField({
							target: document.getElementById('card-field'),
							name: result.name,  // (mandatory!) name of the cardholder
							onSuccess() {  // Callback called when payment finished successfully
							///window.alert("Thank you!");
							location.reload();
							},
							onError(message) {  // Callback in case some error happened
							window.alert("Payment failed! Reason: "+message);
							},
							onCancel() {  // (optional) Callback in case user cancelled a transaction
							//window.alert("Payment cancelled!");
							},
						});
						$('#ppvBtn').removeAttr('disabled');
		 	 			$('#ppvBtn').find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
						$('#btnsubmit').hide();
						$('.list-group').hide();

						$('#formSendPPV').find('#spinner').show();

						setTimeout(function() {
							$('#formSendPPV').find('#spinner').hide();
							$('#formSendPPV').find('#card-fields').show();
							$('#formSendPPV').find('#cardholder_name').val(result.name);
							$('#formSendPPV').find("#card-field").addClass('form-group');
							$('#formSendPPV').find("#card-field").find('iframe input').css('height','25px');
						}, 1000);

						document.getElementById("go-to-payment").addEventListener("click", function () {
							card.submit({
								name: $('#formSendPPV').find('#cardholder_name').val()
							});
						});

						var paymentRequest = RC.paymentRequest({
							target: document.getElementById('payment-request'),
							name: result.name,  // (mandatory!) name of the cardholder
							onSuccess() {  // Callback called when payment finished successfully
							///window.alert("Thank you!");
							location.reload();
							},
							onError(message) {  // Callback in case some error happened
							window.alert("Payment failed! Reason: "+message);
							},
							onCancel() {  // (optional) Callback in case user cancelled a transaction
							//window.alert("Payment cancelled!");
							},
							buttonStyle: { size: 'small' }
						});

						paymentRequest.canMakePayment().then((method) => {
							if (method) {
							paymentRequest.render()
							} else {
							setResult('Not supported')
							paymentRequest.destroy()
							}
						})

					});
				}
				else if (result.success == true && payment == 'wallet' || result.success && result.instantPayment) {

					if (result.data) {
						$('.chatlist[data=' + result.msgId + ']').html('');
						$('.chatlist[data=' + result.msgId + ']').html(result.data);

						jQuery(".timeAgo").timeago();

							const players = Plyr.setup('.js-player');

							const lightbox = GLightbox({
							    touchNavigation: true,
							    loop: false,
							    closeEffect: 'fade'
							});

							$('#payPerViewForm').modal('hide');
		 	 				$('.InputElement').val('');
		 	 				$('#formSendPPV').trigger("reset");
		 	 				$('#ppvBtn').removeAttr('disabled');
		 	 				$('#ppvBtn').find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
							$('.balanceWallet').html(result.wallet);

					} else {
						window.location.href = result.url;
					}

				}

				// success
				else if (result.success == true && result.insertBody) {

					$('#bodyContainer').html('');

				 $(result.insertBody).appendTo("#bodyContainer");

				 if (payment != 1 && payment != 2) {
					 element.removeAttr('disabled');
					 element.find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
				 }

					$('#errorPPV').hide();

				} else if (result.success == true && result.url) {
					window.location.href = result.url;
				} else {

					if (result.errors) {

						var error = '';
						var $key = '';

						for($key in result.errors) {
							error += '<li class="color-light"><i class="far fa-times-circle color-light"></i> ' + result.errors[$key] + '</li>';
						}

						$('#showErrorsPPV').html(error);
						$('#errorPPV').show();
						element.removeAttr('disabled');
						element.find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
					}
				}

			 },
			 error: function(responseText, statusText, xhr, $form) {
					 // error
					 element.removeAttr('disabled');
					 element.find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
					 swal({
							 type: 'error',
							 title: error_oops,
							 text: error_occurred+' ('+xhr+')',
						 });
			 }
		 }).submit();
	 })(); //<--- FUNCTION %
 });//<<<-------- * END FUNCTION CLICK * ---->>>>
//============ End Payment =================//

function toFixed(number, decimals) {
			var x = Math.pow(10, Number(decimals) + 1);
			return (Number(number) + (1 / x)).toFixed(decimals);
		}

$('#payPerViewForm').on('show.bs.modal', function (event) {
    let button, mediaIdInput, pricePPV, priceGrossPPV, subtotalPrice;
    if($(event.relatedTarget).length) button = $(event.relatedTarget);
    else if($("#payPerViewForm").find("#update-data-values").length) button = $("#payPerViewForm").find("#update-data-values").first();
    else return false;


	mediaIdInput = button.data('mediaid'); // Extract info from data-* attributes
	pricePPV = button.data('price'); // Extract info from data-* attributes
	priceGrossPPV = button.data('pricegross');
	subtotalPrice = button.data('subtotalprice');

  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
  var modal = $(this)
	modal.find('.pricePPV').html(pricePPV);
	modal.find('.mediaIdInput').val(mediaIdInput);
	modal.find('.priceInput').val(priceGrossPPV);

	var taxes = modal.find('li.isTaxable').length;

	// Taxes
	modal.find('.subtotal').html(subtotalPrice);
	modal.find('.totalPPV').html(pricePPV);

	for (var i = 1; i <= taxes; i++) {
		var percentage = modal.find('.percentageAppliedTax'+i).attr('data');
		var value = (parseFloat(modal.find('.priceInput').val()) * percentage / 100);
		modal.find('.amount'+i).html(toFixed(value, decimalZero));
	}

});

$('#payPerViewForm').on('hidden.bs.modal', function (e) {
  $('#errorPPV').hide();
	$('#formSendPPV').trigger("reset");
	$('#card-errorsPPV').addClass('display-none');
	$('.InputElement').val('');
	$('#card-elementPPV').removeClass('StripeElement--invalid');
});

})(jQuery);
