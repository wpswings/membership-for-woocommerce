jQuery(document).ready(function ($) {

	var $payment_methods;

	const resetform = async () => {
		$('.membership_customer_details,.mwb_mfw_btn-back-a, .mwb_mfw_btn-next-a, .mwb_mfw_billing-heading h3, .mwb_mfw_form-field-wrapper-part-a').show();
		$('#mwb_proceed_payment, .mwb_membership_payment_modal,.mwb_mfw_btn-back-b, .mwb_mfw_btn-back-a').hide();
	}

	// Opens modal when clicked on membership "buy now" button.
	$(".mwb_membership_buynow").on("click", function (e) {
		e.preventDefault();

		// Ajax cal for states as per country on page ready.
		get_states_for_country();

		// Ajax call function for states as per country.
		function get_states_for_country() {

			$.ajax({
				url: membership_public_obj.ajaxurl,
				type: "POST",
				data: {
					action: "membership_get_states_public",
					country: $("#membership_billing_country").val(),
					nonce: membership_public_obj.nonce,
				},

				success: function (response) {

					if (response.length >= 1) {

						$("#mwb_billing_state_field").show(500);
						$("#membership_billing_state").html(response);
						$("#membership_billing_state").prop("required", true);

					} else {

						$("#mwb_billing_state_field").hide(500);
						$("#membership_billing_state").empty();
						$("#membership_billing_state").prop("required", false);
					}
				}
			});
		}

		$("#mwb_membership_buy_now_modal_form").dialog("open");

		// Validate all email fields.

		let membership_modal = $("#mwb_membership_buy_now_modal_form");
		if( membership_modal.length > 0 ) {

			$('input[type="email"]').on( 'keyup', function() {
				let val = $(this).val();
				let tooltip = $('.tooltip');
				let result =  (/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(val));
				if ( result ) {
					tooltip.hide();
				}
				else {
					tooltip.show();
				}
			});
		}

		// Ajax call for states as per country change.
		$("#membership_billing_country").on("change", function () {

			get_states_for_country();
		});

		// Advancnce bank transfer receipt upload.
		$(document).on("change", ".bacs_receipt_file", function () {

			var file = $(".bacs_receipt_file").prop("files");

			$(".bacs_receipt_attached").val("");
			$("#progress-wrapper").removeClass("progress-complete");
			$("#progress-wrapper").removeClass("progress-failed");
			$("#progress-wrapper .status").text("Processing");
			$("#progress-wrapper").show();


			var upload = new FormData();

			upload.append("receipt", file[0]);
			upload.append("auth_nonce", membership_public_obj.nonce);
			upload.append("action", "upload_receipt");

			$.ajax({
				url: membership_public_obj.ajaxurl,
				type: "POST",
				dataType: "json",
				data: upload,
				processData: false,
				contentType: false,

				success: function (response) {

					if (true == response["result"]) {

						$(".bacs_receipt_remove_file").show(500);
						$(".bacs_receipt_attached").val(response.url);
						$("#progress-wrapper").addClass("progress-complete");
						$(".bacs_receipt_field").removeClass("is_hidden");
						$("#progress-wrapper .status").text("Completed");
						$("#membership_proceed_payment").show();

						// Add file removal script.
						$(document).on("click", ".bacs_receipt_remove_file", function () {

							var removal = new FormData();

							removal.append("path", response.path);
							removal.append("auth_nonce", membership_public_obj.nonce);
							removal.append("action", "remove_current_receipt");

							$.ajax({
								url: membership_public_obj.ajaxurl,
								type: "POST",
								dataType: "json",
								data: removal,
								processData: false,
								contentType: false,

								success: function (response) {

									if ("success" == response["result"]) {
										$( 'input[name="bacs_receipt_file"]' ).val('');
										$(".bacs_receipt_remove_file").hide();
										$("#membership_proceed_payment").hide();
										$("#progress-wrapper").removeClass("progress-complete");
										$("#progress-wrapper").addClass("progress-failed");
										$("#progress-wrapper .status").text("File removed");
									}
								}
							});

						});
					} else if (false == response["result"]) {

						$("#progress-wrapper").addClass("progress-failed");
						$("#progress-wrapper .status").text(response["message"]);
					}
				}
			});
		});

		// Opens payment fields in modal when selected.
		$('.mwb_membership_payment_modal').on("change", ".payment_method_select", function (e) {

			$payment_methods = $(this).val();

			$(".payment_box").slideUp();
			$("div.payment_method_" + $payment_methods).slideDown();

			$("#membership_proceed_payment").hide();
			
			if ($payment_methods == 'membership-paypal-smart-buttons') {
	
					$("#paypal-button-container").show(500);
					
				} else {
	
					$("#paypal-button-container").hide(500);
				}
			

		});

		// Process checkout here.
		$(document).on("submit", "#mwb_membership_buy_now_modal_form", function (e) {

			e.preventDefault();
			var form = $("#mwb_membership_buy_now_modal_form");

			$( '#membership_proceed_payment' ).css( 'pointer-events', 'none' );

			$.ajax({
				url: membership_public_obj.ajaxurl,
				type: form.attr("method"),
				dataType: "json",
				data: {
					action: "membership_process_payment",
					nonce: membership_public_obj.nonce,
					form_data: form.serialize()
				},

				success: function (response) {
					// Close payment modal.
					$("#mwb_membership_buy_now_modal_form").dialog("close");

					if ("payment_success" == response['result']) {

						// Show "success" response via sweet-alert.
						Swal.fire({
							icon: 'success',
							title: response['message'],
						});

						// Redirect to "shop" page on successful payment.
						$(".swal2-confirm").on("click", function () {
							window.location.href = response['redirect'];
						});

					} else if ("payment_failed" == response['result']) {

						// Show "failure" response via sweet-alert.
						Swal.fire({
							icon: 'error',
							title: 'Oops..!!',
							text: response['message']
						});

						$(".swal2-confirm").on("click", function () {
							location.reload();
						});

					} else {

						// Show "validation failure" response via sweet-alert.
						Swal.fire({
							icon: 'error',
							title: 'Oops..!!',
							text: response['message']
						});

						$(".swal2-confirm").on("click", function () {
							location.reload();
						});
					}
				}

			});

		});

	});

	// Payment modal definition.
	$("#mwb_membership_buy_now_modal_form").dialog({
		modal: true,
		autoOpen: false,
		show: { effect: "blind", duration: 800 },
		width: 700,
	});


	// multi-step form for woo-commerce
	$('.mwb_mfw_form-field-wrapper-part-a, .mwb_mfw_form-field-wrapper-part-b, #mwb_proceed_payment, .mwb_membership_payment_modal, .mwb_mfw_btn-back-a,.mwb_mfw_btn-back-b, .mwb_mfw_btn-next-b, .mwb_mfw_order-confirm, .mwb_mfw_purchase-again').hide();
	
	// Reset form on modal close.
	$('.ui-dialog-titlebar-close').click(function () {
		resetform();
	});

	// On esc keyup, close modal and reset form.
	$(document).keyup(function (e) {

		if (e.key === "Escape") { // escape key maps to keycode `27`
			$("#mwb_membership_buy_now_modal_form").dialog("close");
			resetform();
		}
	});

	$('.mwb_membership_buynow').click(function () {
	
		$('#mwb_mfw_progress-bar-a').css({ 'color': '#007cba' }); //progress bar
		$('#mwb_mfw_progress-bar-b, #mwb_mfw_progress-bar-c, #mwb_mfw_progress-bar-d').css({ 'color': '' });
		$('.mwb_mfw_form-field-wrapper-part-a, .mwb_mfw_btn-next-a').show('300');
	});

	$('.mwb_mfw_btn-next-a').click(function () {

		//Personal Details form validation 
		let f_name = $('#membership_billing_first_name').val().length;
		let l_name = $('#membership_billing_last_name').val().length;
		let phone = $('#membership_billing_phone').val().length;
		let email = $('#membership_billing_email').val().length;
		var person_name = $('#membership_billing_first_name').val();

		if (f_name == 0 || l_name == 0 || phone == 0 || email == 0) {
			alert(person_name.toUpperCase() + ' Something you have missed');
			return false;
		}

		// end
		$(this).hide();
		$('#mwb_mfw_progress-bar-a, #mwb_mfw_progress-bar-b').css({ 'color': '#007cba' }); //progress bar
		$('#mwb_mfw_progress-bar-c, #mwb_mfw_progress-bar-d').css({ 'color': '' });
		$('.mwb_mfw_form-field-wrapper-part-a').hide();
		$('.mwb_mfw_form-field-wrapper-part-b,.mwb_mfw_btn-back-a, .mwb_mfw_btn-next-b').show('500');

	});

	$('.mwb_mfw_btn-back-a').click(function () {

		$(this).hide();
		$('#mwb_mfw_progress-bar-a').css({ 'color': '#007cba' }); //progress bar
		$('#mwb_mfw_progress-bar-b, #mwb_mfw_progress-bar-c, #mwb_mfw_progress-bar-d').css({ 'color': '' });
		$('.mwb_mfw_btn-next-a, .mwb_mfw_form-field-wrapper-part-a').show()
		$('.mwb_mfw_form-field-wrapper-part-b, .mwb_mfw_btn-next-b').hide();
	});

	$('.mwb_mfw_btn-next-b').click(function () {

		//Address Details form validation 
		let street = $('#membership_billing_address_1').val().length;
		let city = $('#membership_billing_city').val().length;
		let country = $('#membership_billing_country').val().length;
		let pin = $('#membership_billing_postcode').val().length;

		if (street == 0 || city == 0 || country == 0 || pin == 0) {
			alert('Something you have missed');
			return false;
		}

		// end
		$(this).hide();
		$('#mwb_mfw_progress-bar-b, #mwb_mfw_progress-bar-a, #mwb_mfw_progress-bar-c').css({ 'color': '#007cba' }); //progress bar
		$('#mwb_mfw_progress-bar-d').css({ 'color': '' });
		$('.mwb_mfw_form-field-wrapper-part-b, .membership_customer_details,.mwb_mfw_btn-back-a, .mwb_mfw_btn-next-a, .mwb_mfw_billing-heading h3, .mwb_mfw_btn-next-b, .mwb_mfw_form-field-wrapper-part-a').hide();
		$('#mwb_proceed_payment, .mwb_membership_payment_modal,.mwb_mfw_btn-back-b').show();
	});

	$('.mwb_mfw_btn-back-b').click(function () {

		$(this).hide();
		$('#mwb_mfw_progress-bar-a, #mwb_mfw_progress-bar-b').css({ 'color': '#007cba' }); //progress bar
		$('#mwb_mfw_progress-bar-c, #mwb_mfw_progress-bar-d').css({ 'color': '' });
		$('.mwb_mfw_billing-heading h3, .membership_customer_details, .mwb_mfw_form-field-wrapper-part-b,.mwb_mfw_btn-back-a, .mwb_mfw_btn-next-b').show();
		$('.mwb_mfw_btn-next-a,.mwb_mfw_form-field-wrapper-part-a,#mwb_proceed_payment, .mwb_membership_payment_modal,.mwb_mfw_btn-back-b').hide();
	});

	// Hover abbreviation on the shop catelogue

	$('.mwb_mfw_membership_tool_tip_wrapper').on('mouseenter', function () {

		let abbr_content = $(this).children('.mwb_mfw_membership_tool_tip').html().length;

		if (abbr_content >= 6) {

			$(this).children('.mwb_mfw_membership_tool_tip').css({ 'display': 'block', 'animation': 'visible 0.2s linear 1' });
		} else {

			$(this).css('cursor', 'alias');
		}

	}).on('mouseleave', function () {

		$(this).children('.mwb_mfw_membership_tool_tip').css('display', 'none');
	})

});


