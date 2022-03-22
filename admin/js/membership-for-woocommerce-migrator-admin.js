jQuery(document).ready( function($) {

	const ajaxUrl  		 = localised.ajaxurl;
	const nonce    		 = localised.nonce;
	const action          = localised.callback;
	const pending_count  = localised.pending_count;
	const pending_products = localised.pending_products;
	const shortcode_count  = localised.shortcode_count;
	const shortcode_products = localised.shortcode_products;
	

	/* Close Button Click */
	jQuery( document ).on( 'click','.treat-button',function(e){
		e.preventDefault();
		
		Swal.fire({
			icon: 'warning',
			title: 'We Have got ' + pending_count + ' Products!<br/> And ' + shortcode_count + ' post to check shortcodes !',
			text: 'Click to start import',
			footer: 'Please do not reload/close this page until prompted',
			showCloseButton: true,
			showCancelButton: true,
			focusConfirm: false,
			confirmButtonText:
			  '<i class="fa fa-thumbs-up"></i> Great!',
			confirmButtonAriaLabel: 'Thumbs up, great!',
			cancelButtonText:
			  '<i class="fa fa-thumbs-down"></i>',
			cancelButtonAriaLabel: 'Thumbs down'
		}).then((result) => {
			if (result.isConfirmed) {

				Swal.fire({
					title   : 'Products are being imported!',
					html    : 'Do not reload/close this tab.',
					footer  : '<span class="order-progress-report">' + pending_count + ' are left to import',
					didOpen: () => {
						Swal.showLoading()
					}
				});
			
				startImport( pending_products );
				start_shortcode_Import( shortcode_products );
			} else if (result.isDismissed) {
			  Swal.fire('Import Stopped', '', 'info');
			}
		})
	});


	
	const startImport = ( products ) => {
		var event   = 'wps_mfw_import_single_product';
		var request = { action, event, nonce, products };
		jQuery.post( ajaxUrl , request ).done(function( response ){
			products = JSON.parse( response );
		}).then(
		function( products ) {
			products = JSON.parse( products ).products;
			count = Object.keys(products).length;
			jQuery('.order-progress-report').text( count + ' are left to import' );
			if( ! jQuery.isEmptyObject(products) ) {
				startImport(products);
			} else {
				// All orders imported!
				location.reload();
			}
		}, function(error) {
			console.error(error);
		});
	}

	const start_shortcode_Import = ( products ) => {
		var event   = 'wps_mfw_import_shortcode';
		var request = { action, event, nonce, products };
		jQuery.post( ajaxUrl , request ).done(function( response ){
			products = JSON.parse( response );
		}).then(
		function( products ) {
			products = JSON.parse( products ).products;
			count = Object.keys(products).length;
			jQuery('.order-progress-report').text( count + ' are left to import' );
			if( ! jQuery.isEmptyObject(products) ) {
				start_shortcode_Import(products);
			} else {
				// All orders imported!
				location.reload();
			}
		}, function(error) {
			console.error(error);
		});
	}


	// End of scripts.
});
