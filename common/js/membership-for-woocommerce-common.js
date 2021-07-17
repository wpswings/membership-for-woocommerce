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
$(".mwb_membership_buynow").on("click", function (e) {
	e.preventDefault();
	let plan_price = $('#mwb_membership_plan_price').val();
	let plan_id = $('#mwb_membership_plan_id').val();
	let plan_title = $('#mwb_membership_title').val();

	$.ajax({
		url: mfw_common_param.ajaxurl,
		type: "POST",
		data: {
			action: "mwb_membership_checkout",
			plan_price: plan_price,
			plan_id: plan_id,
			plan_title: plan_title,
		//	nonce: membership_public_obj.nonce,
		},

		success: function (response) {

			window.location.replace('checkout');
		}
	});
});


//   // Import CSV modal.
//   $("#import_all_membership").on("click", function(e) {
//       e.preventDefault();

//       $(".import_csv_field_wrapper").dialog("open");

//       // Ajax call for import CSV.
//       $("#upload_csv_file").on("click", function(e) {
//           e.preventDefault();

//           var empty_check = $("#csv_file_upload").val();

//           // If no file selected close the dialog box and show 'failure' sweet alert.
//           if (empty_check.length == 0) {

//               // CLose the import modal.
//               $(".import_csv_field_wrapper").dialog("close");

//               // Show "failure" response via sweet-alert.
//               Swal.fire({
//                   icon: 'error',
//                   title: 'Oops..!!',
//                   text: 'No file selected',
//               });

//           } else {

//               var form = new FormData();
//               var file = $(document).find("#csv_file_upload");

//               var single_file = file[0].files[0];

//               form.append("file", single_file);
//               form.append("action", "csv_file_upload");
//               form.append("nonce", admin_ajax_obj.nonce)
// console.log(form);
//               $.ajax({
//                   url: admin_ajax_obj.ajaxurl,
//                   type: "POST",
//                   data: form,
//                 //   dataType: 'json',
//                 //   contentType: false,
//                 //   processData: false,

//                   success: function(response) {

//                       // Close the import modal.
//                       $(".import_csv_field_wrapper").dialog("close");

//                       if ('success' == response['status']) {

//                           // Show "success" response via sweet-alert.
//                           Swal.fire({
//                               icon: 'success',
//                               title: response['message'],
//                           });

//                           // Reload page after click on ok in
//                           $(".swal2-confirm").on("click", function() {
//                               window.location.href = response['redirect'];
//                           });
//                       } else if ('failed' == response['status']) {

//                           // Show "failure" response via sweet-alert.
//                           Swal.fire({
//                               icon: 'error',
//                               title: 'Oops..!!',
//                               text: response['message']
//                           });
//                       }
//                   },

//               });
//           }

//       });
//   });


// // Import CSV modal.
// $("#import_all_membership").on("click", function(e) {
// 	e.preventDefault();

	
// 	$(".import_csv_field_wrapper").dialog("open");
// 			$.ajax({
// 				url: mfw_common_param.ajaxurl,
// 				type: "POST",
// 				data: {
// 					action: "csv_file_upload",
// 				//	nonce: membership_public_obj.nonce,
// 				},
		
// 				success: function (response) {
		
// 				console.log(response);
// 				}
// 			});
// });

});

