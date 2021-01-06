(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
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

jQuery( document ).ready( function( $ ) {

	var $payment_methods;

	// Opens modal when clicked on membership "buy now" button.
	$( ".mwb_membership_buynow" ).on( "click", function( e ) {
		e.preventDefault();

		// Ajax cal for states as per country on page ready.
		get_states_for_country();

		// Ajax call function for states as per country.
		function get_states_for_country() {

			$.ajax({
				url  : membership_public_obj.ajaxurl,
				type : "POST",
				data : {
					action  : "membership_get_states_public",
					country : $( "#membership_billing_country" ).val(),
					nonce   : membership_public_obj.nonce,
				},

				success: function( response ) {

					if ( response.length > 1 ) {
						$( "#mwb_billing_state_field" ).show();
						$( "#membership_billing_state" ).html( response );
						$( "#membership_billing_state" ).prop( "required", true );

					} else {

						$( "#mwb_billing_state_field" ).hide();
						$( "#mwb_billing_state_field" ).empty();
						$( "#membership_billing_state" ).prop( "required", false );
					}
				}
			});
		}

		$( "#mwb_membership_buy_now_modal_form" ).dialog( "open" );

		// Ajax call for states as per country change.
		$( "#membership_billing_country" ).on( "change", function() {

			get_states_for_country();
		});

		// Opens payment fields in modal when selected.
		$( ".mwb_membership_payment_modal" ).on( "change", ".payment_method_select", function() {
		
			$payment_methods = $(this).val();
			
			$( ".payment_box" ).hide();
			$( "div.payment_method_" + $payment_methods ).show();

		});

		// Process checkout here.
		$( document ).on( "submit", "#mwb_membership_buy_now_modal_form", function( e ) {

			e.preventDefault();
			
			var form = $( "#mwb_membership_buy_now_modal_form" );

			$.ajax({
				url  : membership_public_obj.ajaxurl,
				type : form.attr( "method" ),
				dataType : "json",
				data : {
					action    : "membership_process_payment",
					nonce     : membership_public_obj.nonce,
					form_data : form.serialize()
				},

				success : function( response ) {

					console.log( response );
					if ( "payment_success" == response['result'] ) {

						$( "#mwb_membership_buy_now_modal_form" ).dialog( "close" );

						Swal.fire({
							icon : 'success',
							title: response['message'],
						});

					} else if ( "payment_failed" == response['result'] ) {

						$( "#mwb_membership_buy_now_modal_form" ).dialog( "close" );
						
						Swal.fire({
							icon : 'error',
							title: 'Oops..!!',
							text : response['message']
						});
					}
				}

			});
		 	 
		});

	});

	// Payment modal definition.
	$( "#mwb_membership_buy_now_modal_form" ).dialog({
        modal    : true,
        autoOpen : false,
		show     : {effect: "blind", duration: 800},
		width    : 700,
	}); 

	

	// Advancnce bank transfer receipt upload.
	$( document ).on( "change", ".bacs_receipt_file", function() {

		var file = $( ".bacs_receipt_file" ).prop( "files" );

		$( ".bacs_receipt_attached" ).val( "" );
		$( "#progress-wrapper" ).removeClass( "progress-complete" );
		$( "#progress-wrapper" ).removeClass( "progress-failed" );
		$( "#progress-wrapper .status" ).text( "Processing" );
		$( "#progress-wrapper" ).show();

		var upload = new FormData();

		upload.append( "receipt", file[0] );
		upload.append( "auth_nonce", membership_public_obj.nonce );
		upload.append( "action", "upload_receipt" );

		$.ajax({
			url         : membership_public_obj.ajaxurl,
			type        : "POST",
			dataType    : "json",
			data        : upload,
			processData : false,
			contentType : false,

			success: function( response ) {

				if ( "success" == response["result"] ) {
					$( ".bacs_receipt_remove_file" ).show();
					$( ".bacs_receipt_attached" ).val( response.url );
					$( "#progress-wrapper" ).addClass( "progress-complete" );
					$( ".bacs_receipt_field" ).removeClass( "is_hidden" );
					$( "#progress-wrapper .status" ).text( "Completed" );

					// Add file removal script.
					$( document ).on( "click", ".bacs_receipt_remove_file", function() {

						var removal = new FormData();

						removal.append( "path", response.path );
						removal.append( "auth_nonce", membership_public_obj.nonce );
						removal.append( "action", "remove_current_receipt" );

						$.ajax({
							url         : membership_public_obj.ajaxurl,
							type        : "POST",
							dataType    : "json",
							data        : removal,
							processData : false,
							contentType : false,

							success : function( response ) {

								if( "success" == response["result"] ) {
									$( ".bacs_receipt_remove_file" ).hide();
									$( "#progress-wrapper" ).removeClass( "progress-complete" );
									$( "#progress-wrapper" ).addClass( "progress-failed" );
									$( "#progress-wrapper .status" ).text( "Something Went Wrong. Please refresh!" );
								}
							}
						});

					});
				}

				else if ( "failure" == response["result"] ) {

					$( "#progress-wrapper" ).addClass( "progress-failed" );
					$( "#progress-wrapper .status" ).text( response["errors"][0] );
				}
			}
		});

	});

});
