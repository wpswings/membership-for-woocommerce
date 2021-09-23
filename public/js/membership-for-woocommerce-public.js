jQuery(document).ready(function ($) {


	jQuery(jQuery('.not_accessible').parent().find('.add_to_cart_button')).hide();


	var $payment_methods;

	const resetform = async () => {
		$('.membership_customer_details,.mwb_mfw_btn-back-a, .mwb_mfw_btn-next-a, .mwb_mfw_billing-heading h3, .mwb_mfw_form-field-wrapper-part-a').show();
		$('#mwb_proceed_payment, .mwb_membership_payment_modal,.mwb_mfw_btn-back-b, .mwb_mfw_btn-back-a').hide();
	}

	const validate_email = ( val ) => {
	
		let tooltip = $('.tooltip');
		let result =  (/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(val));
		if ( result ) {
			tooltip.hide();
		}
		else {
			tooltip.show();
		}
	}


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
	$('.ui-dialog-titlebar-close').on( 'click', function () {
		resetform();
	});

	// On esc keyup, close modal and reset form.
	$(document).keyup(function (e) {

		if (e.key === "Escape") { // escape key maps to keycode `27`
			$("#mwb_membership_buy_now_modal_form").dialog("close");
			resetform();
		}
	});

	$('.mwb_membership_buynow').on( 'click', function () {
	
		$('#mwb_mfw_progress-bar-a').css({ 'background-color': '#1a3365' }); //progress bar
		$('#mwb_mfw_progress-bar-b, #mwb_mfw_progress-bar-c, #mwb_mfw_progress-bar-d').css({ 'background-color': '' });
		$('.mwb_mfw_form-field-wrapper-part-a, .mwb_mfw_btn-next-a').show('300');
	});

	$('.mwb_mfw_btn-next-a').on( 'click', function () {

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
		$('#mwb_mfw_progress-bar-a, #mwb_mfw_progress-bar-b').css({ 'background-color': '#1a3365' }); //progress bar
		$('#mwb_mfw_progress-bar-c, #mwb_mfw_progress-bar-d').css({ 'background-color': '' });
		$('.mwb_mfw_form-field-wrapper-part-a').hide();
		$('.mwb_mfw_form-field-wrapper-part-b,.mwb_mfw_btn-back-a, .mwb_mfw_btn-next-b').show('500');

	});

	$('.mwb_mfw_btn-back-a').on( 'click', function () {

		$(this).hide();
		$('#mwb_mfw_progress-bar-a').css({ 'background-color': '#1a3365' }); //progress bar
		$('#mwb_mfw_progress-bar-b, #mwb_mfw_progress-bar-c, #mwb_mfw_progress-bar-d').css({ 'background-color': '' });
		$('.mwb_mfw_btn-next-a, .mwb_mfw_form-field-wrapper-part-a').show()
		$('.mwb_mfw_form-field-wrapper-part-b, .mwb_mfw_btn-next-b').hide();
	});

	$('.mwb_mfw_btn-next-b').on( 'click', function () {

		//Address Details form validation 
		let street = $('#membership_billing_address_1').val().length;
		let city = $('#membership_billing_city').val().length;
		let country = $('#membership_billing_country').val().length;
		let pin = $('#membership_billing_postcode').val().length;

		if ( street == 0 || city == 0 || country == 0 || pin == 0 ) {
			alert('Something you have missed');
			return false;
		}

		// end
		$(this).hide();
		$('#mwb_mfw_progress-bar-b, #mwb_mfw_progress-bar-a, #mwb_mfw_progress-bar-c').css({ 'background-color': '#1a3365' }); //progress bar
		$('#mwb_mfw_progress-bar-d').css({ 'background-color': '' });
		$('.mwb_mfw_form-field-wrapper-part-b, .membership_customer_details,.mwb_mfw_btn-back-a, .mwb_mfw_btn-next-a, .mwb_mfw_billing-heading h3, .mwb_mfw_btn-next-b, .mwb_mfw_form-field-wrapper-part-a').hide();
		$('#mwb_proceed_payment, .mwb_membership_payment_modal,.mwb_mfw_btn-back-b').show();
	});

	$('.mwb_mfw_btn-back-b').on( 'click', function () {

		$(this).hide();
		$('#mwb_mfw_progress-bar-a, #mwb_mfw_progress-bar-b').css({ 'background-color': '#1a3365' }); //progress bar
		$('#mwb_mfw_progress-bar-c, #mwb_mfw_progress-bar-d').css({ 'background-color': '' });
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

	$(".mwb_membership_buynow").on("click", function (e) {
		e.preventDefault();
		let plan_price = $('#mwb_membership_plan_price').val();
		let plan_id = $('#mwb_membership_plan_id').val();
		let plan_title = $('#mwb_membership_title').val();

		$.ajax({
			url: membership_public_obj.ajaxurl,
			type: "POST",
			data: {
				action: "mwb_membership_checkout",
				plan_price: plan_price,
				plan_id: plan_id,
				plan_title: plan_title,
				nonce: membership_public_obj.nonce,
			},

			success: function (response) {

				window.location.replace('cart');
			}
		});
	});

 });

 jQuery(document).on('click','.mwb_members_plans label',function(obj = this ) {
	debugger;
	var classes = jQuery(this.nextElementSibling).attr('class');
	var allclasses =classes.split(' ');
	if (allclasses.length>1) {
		if( allclasses[1]=='show__membership_details' ){
			jQuery('.mwb_members_plans .mwb_table_wrapper').removeClass('show__membership_details');
		}
 	} else {	
		jQuery(this.nextElementSibling).addClass('show__membership_details');
	}
 }); 