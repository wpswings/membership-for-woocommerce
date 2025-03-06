(function( $ ) {
	'use strict';

	/**
	 * All of the code for your common JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );



jQuery(document).ready(function ($) {
	$(".wps_membership_buynow").on("click", function (e) {
		e.preventDefault();
		let plan_price = $('#wps_membership_plan_price').val();
		let plan_id = $('#wps_membership_plan_id').val();
		let plan_title = $('#wps_membership_title').val();

		$.ajax({
			url: mfw_common_param.ajaxurl,
			type: "POST",
			data: {
				action: "wps_membership_checkout",
				plan_price: plan_price,
				plan_id: plan_id,
				plan_title: plan_title,
			},

			success: function (response) {

				
			}
		});
	});

	// Cancel membership.
	$(document).on( 'click', '.memberhip-cancel-button', function(){
		var notice = "Are you sure to Cancel your membership account!";
		var membership_id = $(this).data('membership_id');
		if( confirm( notice ) == true ) {
			$.ajax({
				url: mfw_common_param.ajaxurl,
				type: "POST",
				data: {
					action: "wps_membership_cancel_membership_count",
					membership_id : membership_id,
					'security' : mfw_common_param.nonce,
				},
	
				success: function (response) {
	
					window.location.reload();
				}
			});
		} 
	} );

	// stop whatsapp notify ajax.
	jQuery(document).on('change', '.wps_mfw_stop_whatsapp_notify', function(){

		var checked = jQuery(this).is(':checked') ? jQuery(this).val() : 'no';
		var data    = {
			'action' : 'stop_whatsapp_notification',
			'nonce'  : mfw_common_param.nonce,
			'stop'   : checked,
		};
		wps_mfw_stop_whatsapp_notify( data, jQuery(this) );
	});

	// stop sms notify ajax.
	jQuery(document).on('change', '.wps_mfw_stop_sms_notify', function(){

		var checked = jQuery(this).is(':checked') ? jQuery(this).val() : 'no';
		var data    = {
			'action' : 'stop_sms_notification',
			'nonce'  : mfw_common_param.nonce,
			'stop'   : checked,
		};
		wps_mfw_stop_whatsapp_notify( data, jQuery(this) );
	});

	// stop email notify ajax.
	jQuery(document).on('change', '.wps_mfw_stop_email_notify', function(){

		var checked = jQuery(this).is(':checked') ? jQuery(this).val() : 'no';
		var data    = {
			'action' : 'stop_email_notification',
			'nonce'  : mfw_common_param.nonce,
			'stop'   : checked,
		};
		wps_mfw_stop_whatsapp_notify( data, jQuery(this) );
	});

	function wps_mfw_stop_whatsapp_notify( value, _this ) {
		
		jQuery.ajax({
			'url'    : mfw_common_param.ajaxurl,
			'method' : 'POST',
			'data'   : value,
			success  : function( response ) {

				if ( response.result == true ) {

					jQuery('.mfw_whatsapp_stop_notice').show();
					jQuery('.mfw_whatsapp_stop_notice').css('color', 'red');
					jQuery('.mfw_whatsapp_stop_notice').html(response.msg);
				} else {

					jQuery('.mfw_whatsapp_stop_notice').show();
					jQuery('.mfw_whatsapp_stop_notice').css('color', 'green');
					jQuery('.mfw_whatsapp_stop_notice').html(response.msg);
				}

				setTimeout(() => {
					jQuery('.wps_wpr_enable_offer_setting_wrapper').next('.mfw_whatsapp_stop_notice').hide();
				}, 15000);
			}
		});
	}

});

