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

		// Appearance section JS start.
		$('.mwb-membership-appearance-card').on( 'click', function(e) {

			e.preventDefault();

			$(this).addClass( 'nav-tab-active' );
			$('.mwb-membership-design').removeClass( 'nav-tab-active' );
			$('.mwb-membership-text').removeClass( 'nav-tab-active' );

			$('.mwb-membership-card-template-section').removeClass( 'mwb-membership-appearance-section-hidden' );
			$('.mwb_membership_card_table_column_wrapper').addClass( 'mwb-membership-appearance-section-hidden' );
			$('.mwb-membership-text-section').addClass( 'mwb-membership-appearance-section-hidden' );
		});

		$('.mwb-membership-design').on( 'click', function(e) {

			e.preventDefault();

			$(this).addClass( 'nav-tab-active' );
			$('.mwb-membership-text').removeClass( 'nav-tab-active' );
			$('.mwb-membership-appearance-card').removeClass( 'nav-tab-active' );

			$('.mwb-membership-card-template-section').addClass( 'mwb-membership-appearance-section-hidden' );
			$('.mwb_membership_card_table_column_wrapper').removeClass( 'mwb-membership-appearance-section-hidden' );
			$('.mwb-membership-text-section').addClass( 'mwb-membership-appearance-section-hidden' );
		
		});

		$('.mwb-membership-text').on( 'click', function(e) {

			e.preventDefault();

			$(this).addClass( 'nav-tab-active' );
			$('.mwb-membership-appearance-card').removeClass( 'nav-tab-active' );
			$('.mwb-membership-design').removeClass( 'nav-tab-active' );

			$('.mwb-membership-card-template-section').addClass( 'mwb-membership-appearance-section-hidden' );
			$('.mwb_membership_card_table_column_wrapper').addClass( 'mwb-membership-appearance-section-hidden' );
			$('.mwb-membership-text-section').removeClass( 'mwb-membership-appearance-section-hidden' );
			
		});
		// Appearance section JS end.

	});

		

})( jQuery );

// Basic JS.
jQuery(document).ready( function($) {

	// Display access type form fields as per user seletcion.
	$('#mwb_membership_plan_access_type').on( 'change', function() {
		var selection = $(this).val();
		
		//alert(selection);

		switch( selection ) {

			case 'limited':
				$('#mwb_membership_duration').show();
				$('#mwb_membership_date_range_start').hide();
				$('#mwb_membership_date_range_end').hide();
				break;
			
			case 'date_ranged':
				$('#mwb_membership_date_range_start').show();
				$('#mwb_membership_date_range_end').show();
				$('#mwb_membership_duration').hide();
				break;

			default:
				$('#mwb_membership_duration').hide();
				$('#mwb_membership_date_range_start').hide();
				$('#mwb_membership_date_range_end').hide();

		}
	}).find(':selected');

	// Display specify time form fields as per user selection.
	$('#new_created_offers table tr:last td :radio').on( 'change', function() {
		
		//alert('select');
		if ( this.id == 'mwb_membership_plan_time_type' ) {
			$('#mwb_membership_plan_time_duratin_display').show();

		} else {
			//alert('chnge');
			$('#mwb_membership_plan_time_duratin_display').hide();
		}

	});

	// Display custom message field as per user selection.
	$('#mwb_membership_manage_content').on( 'change', function() {

		var selection = $(this).val();
		//alert(selection);

		switch( selection ) {
			case 'display_a message':
				$('#mwb_membership_manage_contnet_display').show();
				break;

			default:
				$('#mwb_membership_manage_contnet_display').hide();
		}

	});

});
