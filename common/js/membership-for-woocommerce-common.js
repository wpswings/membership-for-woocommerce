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

	// Unified event listener for all notification toggles.
	$(document).on('change', '.wps_msfw_stop_notifications', function() {

		wps_mfw_toggle_notification( $(this), $(this).attr('data-type') );
	});
	
	function wps_mfw_toggle_notification( $checkbox, type ) {

		let isChecked = $checkbox.is(':checked') ? $checkbox.val() : 'no';
		let data     = {
			action : 'stops_notification',
			nonce  : mfw_common_param.nonce,
			stop   : isChecked,
			type   : type, // 'whatsapp', 'sms', or 'email'
		};

		$.ajax({
			url     : mfw_common_param.ajaxurl,
			method  : 'POST',
			data    : data,
			success : function(response) {
				let $notice = $('.mfw_whatsapp_stop_notice');
				$notice
					.show()
					.css('color', response.result ? 'red' : 'green')
					.html(response.msg);

				setTimeout(() => {
					$notice.hide();
				}, 2000);
			}
		});
	}

	// popup to send sms to user.
	var popupOverlay = jQuery(".wps-mfw_ul-popup-overlay");
	jQuery('.wps-mfw_u-list-wrap ul.wps-mfw_u-list li .wps-mfw_ul-cta button').on('click', function(e) {

		jQuery('.wps-mfw_uld-msg').html('');
		var dataId = jQuery(this).attr('data-id');
    	jQuery('.wps-mfw_ul-send').attr('data-id', dataId);

		// Show the popup using jQuery
		popupOverlay.css("display", "flex"); // or "block"
	});

	// Handle Close button
	jQuery(".wps-mfw_ul-close").on("click", function () {
		popupOverlay.css("display", "none");
	});

	// send sms to community users.
	jQuery(document).on('click', '.wps-mfw_ul-send', function () {

		const $button     = jQuery(this);
		const userId      = $button.attr('data-id');
		const message     = jQuery('.wps-mfw_ul-description').val();
		const $loader     = jQuery('.wps_wpr_sms_community_loader');
		const $msgBox     = jQuery('.wps-mfw_uld-msg');
		const $popup      = jQuery('.wps-mfw_ul-popup-overlay'); // Make sure this selector matches your HTML.
		
		if ( ! message ) {

			$msgBox.css('color', 'red').html(mfw_common_param.msg_error).show().focus();
			return false;
		}
		$button.prop('disabled', true);
		$loader.show();
		$msgBox.hide();
	
		jQuery.ajax({
			type : 'POST',
			url  : mfw_common_param.ajaxurl,
			data : {
				action  : 'send_sms_community_user',
				nonce   : mfw_common_param.nonce,
				user_id : userId,
				message : message,
			},
			success : function (response) {
				$button.prop('disabled', false);
				$loader.hide();
				$msgBox
					.css('color', response.result ? 'green' : 'red')
					.html(response.msg)
					.show();
				jQuery('.wps-mfw_ul-description').val('');
	
				setTimeout(() => {
					$popup.hide();
					$msgBox.hide();
				}, 2000);
			},
			error: function () {
				$button.prop('disabled', false);
				$loader.hide();
				$msgBox.css('color', 'red').text(mfw_common_param.ajax_error).show();
			}
		});
	});

});

