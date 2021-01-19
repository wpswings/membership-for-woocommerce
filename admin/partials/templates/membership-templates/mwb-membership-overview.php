<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin/partials
 */

// Exit is accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

// array( 'jugh' =>  )

// $class = new Membership_Activity_Helper( 'Advance-bacs-logs', 'logger' );
// $response = array(
// 	'status'  => 'failed',
// 	'message' => sprintf( 'Error completing payment for order #%d', $member_id ),
// );
// $data =  $class->create_log( 'Test-log2', $response );
// $activity_class = new Membership_Activity_Helper( 'mfw-invoices', 'uploads' );
// $pdf_file       = $activity_class->create_pdf_n_upload( 'filddfkhvgbdlivbdjvbdvv', 'harshit' );
$paypal = new Membership_Paypal_Express_Checkout();
echo '<pre>'; print_r( $paypal ); echo '</pre>';
$global_class = new Membership_For_Woocommerce_Global_Functions();
echo '<pre>'; print_r( $global_class->paypal_sb_settings() ); echo '</pre>';
return;


?>

<!-- Heading start -->
<div class="mwb_membership_Overview">
	<h2><?php esc_html_e( 'Membership Overview', 'membership-for-woocommerce' ); ?></h2>
</div>
<!-- Heading end. -->

<div id="paypal-button-container"></div>

<!-- Include the PayPal JavaScript SDK -->
<script src="https://www.paypal.com/sdk/js?client-id=Ac1d656B6aeet2elq-lW4_bu6EKDSPtHZCN4P1xp9u6c0Zi2GTmX7T-YAnjNkY2dbnZFyTfq_d8yRewK&currency=USD&intent=capture&components=buttons&disable-funding=card&debug=false"></script>

<script>
	// // Render the PayPal button into #paypal-button-container
	// paypal.Buttons({

	// 	// Set up the transaction
	// 	createOrder: function(data, actions) {
	// 		return actions.order.create({
	// 			purchase_units: [{
	// 				amount: {
	// 					currency_code: "USD",
	// 					value: "100.00",
	// 				},
	// 				items: [{
	// 					name: "Item 1",
	// 					description: "The best item ever",
	// 					unit_amount: {
	// 						currency_code: "USD",
	// 						value: "100.00"
	// 					},
	// 					quantity: "1",
	// 				}],
	// 			}]
	// 		});
	// 	},

	// 	// Finalize the transaction
	// 	onApprove: function(data, actions) {
	// 		return actions.order.capture().then(function(details) {
	// 			// Show a success message to the buyer
	// 			alert('Transaction completed by ' + details.payer.name.given_name + '!');
	// 		});
	// 	}


	// }).render('#paypal-button-container');

	paypal.Buttons({

		style: {
				color:  'gold',
				shape:  'pill',
				label:  'pay',
				height: 40
			},

	createOrder: function(data, actions) {
		return actions.order.create({
			purchase_units: [
			{
				// reference_id: "PUHF",
				// description: "Some description",

				// custom_id: "Something7364",
				// soft_descriptor: "Great description 1",
				amount: {
					currency_code: "USD",
					value: "100.00",
					breakdown: {
						item_total: {
							currency_code: "USD",
							value: "100.00"
						}
					}
				},
				items: [
					{
						name: "Item 1",
						description: "The best item ever",
						// sku: "xyz-2654",
						unit_amount: {
							currency_code: "USD",
							value: "100.00"
						},
						quantity: "1",
						category : "DIGITAL_GOODS"
					},
					// {
					// 	name: "Item 2",
					// 	description: "Not bad too",
					// 	sku: "zdc-3942",
					// 	unit_amount: {
					// 		currency_code: "EUR",
					// 		value: "50.00"
					// 	},
					// 	quantity: "2"
					// }
				],
				shipping: {
					// method: "United States Postal Service",
					name: {
							full_name: "John Doe",
						},
					address: {
						address_line_1: "123 Townsend St",
						address_line_2: "Floor 6",
						admin_area_2: "San Francisco",
						admin_area_1: "CA",
						postal_code: "94107",
						country_code: "US"
					}
				}

			}
		]
	});
},
onApprove: function(data, actions) {
	return actions.order.capture().then(function(details) {
		console.log( details );
		alert('Transaction completed by ' + details.payer.name.given_name);
		// Call your server to save the transaction
		return fetch('/api/paypal-transaction-complete', {
			method: 'post',
			headers: {
				'content-type': 'application/json'
			},
			body: JSON.stringify({
				orderID: data.orderID
			})
		});
	});
}
}).render('#paypal-button-container');
</script>
