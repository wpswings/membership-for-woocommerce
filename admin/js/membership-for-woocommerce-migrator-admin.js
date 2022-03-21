jQuery(document).ready( function($) {

	const ajaxUrl  		 = localised.ajaxurl;
	const nonce    		 = localised.nonce;
	const action          = localised.callback;
	const pending_count  = localised.pending_count;
	const pending_products = localised.pending_products;
	const completed_products = localised.completed_products;
	const searchHTML = '<style>input[type=number], select, numberarea{width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; margin-top: 6px; margin-bottom: 16px; resize: vertical;}input[type=submit]{background-color: #04AA6D; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer;}.container{border-radius: 5px; background-color: #f2f2f2; padding: 20px;}</style></head><div class="container"> <label for="ordername">Order Id</label> <input type="number" id="ordername" name="firstname" placeholder="Order ID to search.."></div>';

	/* Close Button Click */
	jQuery( document ).on( 'click','.treat-button',function(e){
		e.preventDefault();
		
		Swal.fire({
			icon: 'warning',
			title: 'We Have got ' + pending_count + ' Products!',
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


	// End of scripts.
});
