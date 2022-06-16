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

});

