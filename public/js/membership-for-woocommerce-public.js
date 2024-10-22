jQuery(document).ready(function ($) {
	
	if ('temp2' == membership_public_obj.plan_page_template) {

		jQuery('.wp-block-cover').addClass('wps-mfw-temp2');
	} else if ( 'temp3' == membership_public_obj.plan_page_template ) {

		jQuery('.wp-block-cover').addClass('wps-mfw-temp3');
	} else {

		jQuery('.wp-block-cover').removeClass('wps-mfw-temp2');
		jQuery('.wp-block-cover').removeClass('wps-mfw-temp3');
	}

	if ('yes' == membership_public_obj.single_plan) {
		jQuery('.wps_mfw_membership_front_page').addClass('wps_mfw_membership_single_plan');
		jQuery('.wps_membership_plan_content_price').parent().removeClass('wps_membership_plan_content_price');
		jQuery('.wp-block-cover.wps-mfw-temp2').css('min-height','100vh');
		
	} else {
		jQuery('.wps_mfw_membership_front_page').addClass('wps_mfw_membership_multiple_plan');
	}
	
	
	if ('on' == membership_public_obj.dark_mode) {
		jQuery('.wp-block-cover').addClass('wps-mfw-dark-mode');
	} else {
		jQuery('.wp-block-cover').removeClass('wps-mfw-dark-mode');
	}
	
	jQuery(jQuery('.not_accessible').parent().find('.add_to_cart_button')).hide();


	var $payment_methods;

	const resetform = async () => {
		$('.membership_customer_details,.wps_mfw_btn-back-a, .wps_mfw_btn-next-a, .wps_mfw_billing-heading h3, .wps_mfw_form-field-wrapper-part-a').show();
		$('#wps_proceed_payment, .wps_membership_payment_modal,.wps_mfw_btn-back-b, .wps_mfw_btn-back-a').hide();
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

	var button_text = membership_public_obj.buy_now_text;
	if( '' != button_text ) {
		$('.wps_membership_buynow').val( button_text );
	}
 
	// Payment modal definition.
	$("#wps_membership_buy_now_modal_form").dialog({
		modal: true,
		autoOpen: false,
		show: { effect: "blind", duration: 800 },
		width: 700,
	});


	// multi-step form for woo-commerce
	$('.wps_mfw_form-field-wrapper-part-a, .wps_mfw_form-field-wrapper-part-b, #wps_proceed_payment, .wps_membership_payment_modal, .wps_mfw_btn-back-a,.wps_mfw_btn-back-b, .wps_mfw_btn-next-b, .wps_mfw_order-confirm, .wps_mfw_purchase-again').hide();
	
	// Reset form on modal close.
	$('.ui-dialog-titlebar-close').on( 'click', function () {
		resetform();
	});

	// On esc keyup, close modal and reset form.
	$(document).keyup(function (e) {

		if (e.key === "Escape") { // escape key maps to keycode `27`
			$("#wps_membership_buy_now_modal_form").dialog("close");
			resetform();
		}
	});

	$('.wps_membership_buynow').on( 'click', function () {
	
		$('#wps_mfw_progress-bar-a').css({ 'background-color': '#1a3365' }); //progress bar
		$('#wps_mfw_progress-bar-b, #wps_mfw_progress-bar-c, #wps_mfw_progress-bar-d').css({ 'background-color': '' });
		$('.wps_mfw_form-field-wrapper-part-a, .wps_mfw_btn-next-a').show('300');
	});

	$('.wps_mfw_btn-next-a').on( 'click', function () {

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
		$('#wps_mfw_progress-bar-a, #wps_mfw_progress-bar-b').css({ 'background-color': '#1a3365' }); //progress bar
		$('#wps_mfw_progress-bar-c, #wps_mfw_progress-bar-d').css({ 'background-color': '' });
		$('.wps_mfw_form-field-wrapper-part-a').hide();
		$('.wps_mfw_form-field-wrapper-part-b,.wps_mfw_btn-back-a, .wps_mfw_btn-next-b').show('500');

	});

	$('.wps_mfw_btn-back-a').on( 'click', function () {

		$(this).hide();
		$('#wps_mfw_progress-bar-a').css({ 'background-color': '#1a3365' }); //progress bar
		$('#wps_mfw_progress-bar-b, #wps_mfw_progress-bar-c, #wps_mfw_progress-bar-d').css({ 'background-color': '' });
		$('.wps_mfw_btn-next-a, .wps_mfw_form-field-wrapper-part-a').show()
		$('.wps_mfw_form-field-wrapper-part-b, .wps_mfw_btn-next-b').hide();
	});

	$('.wps_mfw_btn-next-b').on( 'click', function () {

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
		$('#wps_mfw_progress-bar-b, #wps_mfw_progress-bar-a, #wps_mfw_progress-bar-c').css({ 'background-color': '#1a3365' }); //progress bar
		$('#wps_mfw_progress-bar-d').css({ 'background-color': '' });
		$('.wps_mfw_form-field-wrapper-part-b, .membership_customer_details,.wps_mfw_btn-back-a, .wps_mfw_btn-next-a, .wps_mfw_billing-heading h3, .wps_mfw_btn-next-b, .wps_mfw_form-field-wrapper-part-a').hide();
		$('#wps_proceed_payment, .wps_membership_payment_modal,.wps_mfw_btn-back-b').show();
	});

	$('.wps_mfw_btn-back-b').on( 'click', function () {

		$(this).hide();
		$('#wps_mfw_progress-bar-a, #wps_mfw_progress-bar-b').css({ 'background-color': '#1a3365' }); //progress bar
		$('#wps_mfw_progress-bar-c, #wps_mfw_progress-bar-d').css({ 'background-color': '' });
		$('.wps_mfw_billing-heading h3, .membership_customer_details, .wps_mfw_form-field-wrapper-part-b,.wps_mfw_btn-back-a, .wps_mfw_btn-next-b').show();
		$('.wps_mfw_btn-next-a,.wps_mfw_form-field-wrapper-part-a,#wps_proceed_payment, .wps_membership_payment_modal,.wps_mfw_btn-back-b').hide();
	});

	// Hover abbreviation on the shop catelogue
	$('.wps_mfw_membership_tool_tip_wrapper').on('mouseenter', function () {

		let abbr_content = $(this).children('.wps_mfw_membership_tool_tip').html().length;

		if (abbr_content >= 6) {

			$(this).children('.wps_mfw_membership_tool_tip').css({ 'display': 'block', 'animation': 'visible 0.2s linear 1' });
		} else {

			$(this).css('cursor', 'alias');
		}

	}).on('mouseleave', function () {

		$(this).children('.wps_mfw_membership_tool_tip').css('display', 'none');
	})

	$('.wps_membership_buynow').on("click", function (e) {
		e.preventDefault();
		let plan_price = jQuery(jQuery(jQuery(this).parent()).find('#wps_membership_plan_price')).val();
		let plan_id = jQuery(jQuery(jQuery(this).parent()).find('#wps_membership_plan_id')).val();
		let plan_title = jQuery(jQuery(jQuery(this).parent()).find('#wps_membership_title')).val();

		$.ajax({
			url: membership_public_obj.ajaxurl,
			type: "POST",
			data: {
				action: "wps_membership_checkout",
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

	// Membership Tab New layout js.
	if ( 'on' == membership_public_obj.enable_new_layout ) {

		// change points tab layout color.
        var root = $(':root');
        root.css('--wps-msfw-primary-color', membership_public_obj.new_layout_color );

		jQuery(document).find('.wps_msfw__new_layout').addClass('wps_msf_new_layout_dynamic_one');
		jQuery(document).find('.wps_msfw__new_layout_billing').addClass('wps_msf_new_layout_dynamic_one');
	}
 });

 jQuery(document).ready(function($){

	 var single_page = membership_public_obj.single_plan;
	 if('yes' == single_page ){
		jQuery('.members_plans_details .wps_members_plans label').trigger('click') ;
	 }
 });


 jQuery(document).on('click','.wps_members_plans label',function(obj = this ) {
	
	var classes = jQuery(this.nextElementSibling).attr('class');
 var allclasses =classes.split(' ');
if ( allclasses.length>1 ) {
	if( allclasses[1]=='show__membership_details' ){
	
		jQuery('.wps_members_plans .wps_table_wrapper').removeClass('show__membership_details');
	}
 } else {
		
	jQuery(this.nextElementSibling).addClass('show__membership_details');
	}

 }); 

 