jQuery(document).ready( function($) {

	const ajaxUrl  		 = localised.ajaxurl;
	const nonce    		 = localised.nonce;
	const action          = localised.callback;
	const pending_count  = localised.pending_count;
	const pending_products = localised.pending_products;

	
	

	/* Close Button Click */
	jQuery( document ).on( 'click','.treat-button',function(e){
		e.preventDefault();
		
		Swal.fire({
			icon: 'warning',
			title: ' We Have got ' + pending_count + ' Products! ',
			text: 'Click to start import',
			footer: 'Please do not reload/close this page until prompted',
			showCloseButton: true,
			showCancelButton: true,
			focusConfirm: false,
			confirmButtonText:
			  '<i class="fa fa-thumbs-up"></i> Great!',
			confirmButtonAriaLabel: 'Thumbs up, great!',
			cancelButtonText:
			  '<i class="fa fa-thumbs-down">cancel</i>',
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
				// All users imported!
				Swal.fire('All of the Data are Migrated successfully!', '', 'success').then(() => {
					window.location.reload();
				});
			}
		}, function(error) {
			console.error(error);
		});
	}

	

	if ( localised.pending_count == 0 ) {
		
		jQuery( ".treat-button" ).hide();
	}else{
		jQuery( ".treat-button" ).show();
		
	}
	
	
});
var wps_membership_migration_success = function() {
	
	
	if ( localised.pending_count != 0 ) {
		jQuery( ".treat-button" ).click();
		jQuery( ".treat-button" ).show();
	}else{
		jQuery( ".treat-button" ).hide();
		
	}
}