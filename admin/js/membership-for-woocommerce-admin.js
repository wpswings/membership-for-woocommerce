(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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

	$(document).ready(function(){


	});

		

})( jQuery );

// Basic JS.
jQuery( document ).ready( function( $ ) {

	// Display already selected option field.
	function selected() {

		var selection_access = $( "#mwb_membership_plan_access_type  option:selected" ).val();

		var selection_radio = $( "input[name='mwb_membership_plan_access_type']:checked" ).val();

		var selection_msg = $( "#mwb_membership_manage_content option:selected" ).val();

		switch( selection_access ) {

			case 'limited':
				$( "#mwb_membership_duration" ).show();
				$( "#mwb_membership_date_range_start" ).hide();
				$( "#mwb_membership_date_range_end" ).hide();
				$( "#mwb_membership_recurring_plan" ).show();
				break;
			
			case 'date_ranged':
				$( "#mwb_membership_date_range_start" ).show();
				$( "#mwb_membership_date_range_end" ).show();
				$( "#mwb_membership_duration" ).hide();
				$( "#mwb_membership_recurring_plan" ).show();
				break;

			default:
				$( "#mwb_membership_duration" ).hide();
				$( "#mwb_membership_date_range_start" ).hide();
				$( "#mwb_membership_date_range_end" ).hide();
				$( "#mwb_membership_recurring_plan" ).hide();

		}

		switch( selection_radio ) {

			case 'immediate_type':
				$( "#mwb_membership_plan_time_duratin_display" ).hide();
				break;

			case 'delay_type':
				$( "#mwb_membership_plan_time_duratin_display" ).show();
		}

		switch( selection_msg ) {

			case 'display_a_message':
				$( "#mwb_membership_manage_contnet_display" ).show();
				break;

			default:
				$( "#mwb_membership_manage_contnet_display" ).hide();
		}

	}

	selected(); // calling the function when the page is ready.

	// Display access type form fields as per user seletcion.
	$( "#mwb_membership_plan_access_type" ).on( 'change', function() {
		var selection = $( this ).val();

		switch( selection ) {

			case 'limited':
				$( "#mwb_membership_duration" ).show();
				$( "#mwb_membership_date_range_start" ).hide();
				$( "#mwb_membership_plan_start" ).val( "" );
				$( "#mwb_membership_date_range_end" ).hide();
				$( "#mwb_membership_plan_end" ).val( "" );
				$( "#mwb_membership_recurring_plan" ).show();
				break;
			
			case 'date_ranged':
				$( "#mwb_membership_date_range_start" ).show();
				$( "#mwb_membership_date_range_end" ).show();
				$( "#mwb_membership_duration" ).hide();
				$( "#mwb_membership_plan_duration" ).val( "" );
				$( "#mwb_membership_plan_duration_type" ).prop( "selectedIndex",0 );
				$( "#mwb_membership_recurring_plan" ).show();
				break;

			default:
				$( "#mwb_membership_duration" ).hide();
				$( "#mwb_membership_date_range_start" ).hide();
				$( "#mwb_membership_date_range_end" ).hide();
				$( "#mwb_membership_plan_start" ).val( "" );
				$( "#mwb_membership_plan_end" ).val( "" );
				$( "#mwb_membership_plan_duration" ).val( "" );
				$( "#mwb_membership_plan_duration_type" ).prop( "selectedIndex",0 );
				$( "#mwb_membership_recurring_plan" ).hide();

		}
	});

	// Display specify time form fields as per user selection.
	$( "#new_created_offers table tr:last td :radio" ).on( "change", function() {
		
		if ( this.id == "mwb_membership_plan_time_type" ) {

			$( "#mwb_membership_plan_time_duratin_display" ).show();

		} else {
			
			$( "#mwb_membership_plan_time_duratin_display" ).hide();
			$( "#mwb_membership_plan_time_duration" ).val( "" );
			$( "#mwb_membership_plan_time_duration_type" ).prop( "selectedIndex",0 );

		}

	});

	// Display free shipping link as per user selection.
	$( "input[name='mwb_memebership_plan_free_shipping']" ).on( "change", function(){

		if ( $( this ).is( ":checked" ) ) {

			$( ".mwb_membership_free_shipping_link" ).show();

		} else {

			$( ".mwb_membership_free_shipping_link" ).hide();
		}
	});

	// Display custom message field as per user selection.
	$( "#mwb_membership_manage_content" ).on( "change", function() {

		var selection = $( this ).val();

		switch( selection ) {

			case 'display_a_message':
				$( "#mwb_membership_manage_contnet_display" ).show();
				break;

			default:
				$( "#mwb_membership_manage_contnet_display" ).hide();
				$( "#mwb_membership_manage_content_display_msg" ).val( "" );

		}

	});

	// Import CSV modal.
	$( "#import_all_membership" ).on( "click", function( e ) {
		e.preventDefault();

		$( ".import_csv_field_wrapper" ).dialog( "open" );

		// Ajax call for import CSV.
		$( "#upload_csv_file" ).on( "click", function( e ) {
			e.preventDefault();

			var empty_check = $( "#csv_file_upload" ).val();
			//alert( empty_check );

			// If no file selected close the dialog box and show 'failure' sweet alert.
			if ( empty_check.length == 0 ) {
			
				// CLose the import modal.
				$( ".import_csv_field_wrapper" ).dialog( "close" );

				// Show "failure" response via sweet-alert.
				Swal.fire({
					icon : 'error',
					title: 'Oops..!!',
					text : 'No file selected',
				});

				//location.reload();	
			} else {

				var form = new FormData();
				var file = $( document ).find( "#csv_file_upload" );
		
				var single_file = file[0].files[0];

				form.append( "file", single_file );
				form.append( "action", "csv_file_upload" );
				form.append( "nonce", admin_ajax_obj.nonce )

				$.ajax({
					url         : admin_ajax_obj.ajaxurl,
					type        : "POST",
					data        : form,
					dataType    : 'json',
					contentType : false,
					processData : false,

					success : function( response ) {
						
						// CLose the import modal.
						$( ".import_csv_field_wrapper" ).dialog( "close" );

						if ( 'success' == response['status'] ) {

							// Show "success" response via sweet-alert.
							Swal.fire({
								icon : 'success',
								title: response['message'],
							});
							
							// Reload page after click on ok in
							$( ".swal2-confirm" ).on( "click", function() {
								window.location.href = response['redirect'];
							});
						} else if ( 'failed' == response['status'] ) {

							// Show "failure" response via sweet-alert.
							Swal.fire({
								icon : 'error',
								title: 'Oops..!!',
								text : response['message']
							});
						}


					},

				});
			}

		});
	});

	$( ".import_csv_field_wrapper" ).dialog({
        modal: true,
        autoOpen: false,
		show: {effect: "blind", duration: 800},
		width : 600,
	});

	function disable_Event_Propagation(event) {
	
		if (event.stopPropagation){
			event.stopPropagation();
		}
		else if(window.event){
			window.event.cancelBubble=true;
		}
	}

});
