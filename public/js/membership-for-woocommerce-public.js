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

	var payment_methods;

	// Opens modal when clicked on membership "buy now" button.
	$( ".mwb_membership_buynow" ).on( "click", function( e ) {
		e.preventDefault();

		$( ".mwb_membership_buy_now_modal" ).dialog( "open" );

		// Ajax call for states as per country.
		$( "#membership_billing_country" ).on( "change", function() {

			var country_code = $( this ).val();

			$.ajax({
				url  : membership_public_obj.ajaxurl,
				type : "POST",
				data : {
					action  : "membership_get_states",
					country : country_code,
					nonce   : membership_public_obj.nonce,
				},

				success: function( response ) {

					if ( response.length > 1 ) {
						$( "#mwb_billing_state_field" ).show();
						$( "#membership_billing_state" ).html( response );

					} else {

						$( "#mwb_billing_state_field" ).hide();
					}
				}
			});
		});

		// Opens payment fields in modal when selected.
		$( ".mwb_membership_payment_modal" ).on( "change", ".payment_method_select", function() {
		
			$payment_methods = $(this).val();
			
			$( ".payment_box" ).hide();
			$( "div.payment_method_" + $payment_methods ).show();

		});

		// Process checkout here.
		$( document ).on( "click", "#membership_proceed_payment", function( e ) {

			e.preventDefault();
		    //alert('hey');
			
			var first_name = $( "#membership_billing_first_name" ).val();
			console.log( first_name );
			var last_name  = $( "#membership_billing_last_name" ).val();
			console.log( last_name );
			var company    = $( "#membership_billing_company" ).val();
			console.log( company );
			var country    = $( "#membership_billing_country" ).val(); 
			console.log( country );  
			var address_1  = $( "#membership_billing_address_1" ).val();
			console.log( address_1 );
			var address_2  = $( "#membership_billing_address_2" ).val();
			console.log( address_2 );
			var city       = $( "#membership_billing_city" ).val();
			console.log( city );
			var state      = $( "#membership_billing_state" ).val();
			console.log( state );
			var postcode   = $( "#membership_billing_postcode" ).val();
			console.log( postcode );
			var phone      = $( "#membership_billing_phone" ).val();
			console.log( phone );
			var email      = $( "#membership_billing_email" ).val();
			console.log( email );
			var method_id  = $payment_methods;
			console.log( method_id );
			var plan_id = $( "#membership_plan_id" ).val();

			$.ajax({
				url  : membership_public_obj.ajaxurl,
				type : "POST",
				//dataType : "json",
				data : {
					action  : "membership_process_payment",
					nonce   : membership_public_obj.nonce,
					f_name  : first_name,
					l_name  : last_name,
					company : company,
					country : country,
					add_1   : address_1,
					add_2   : address_2,
					city    : city,
					state   : state,
					zip     : postcode,
					phone   : phone,
					email   : email,
					gateway : method_id,
					plan    : plan_id
				},

				success : function( response ) {

					console.log( response );
				}

			});
		 	 
		});

		
	});

	$( ".mwb_membership_buy_now_modal" ).dialog({
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
						removal.append( "auth_nonce", membership_bank_transfer.nonce );
						removal.append( "action", "remove_current_receipt" );

						$.ajax({
							url         : membership_bank_transfer.ajaxurl,
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
