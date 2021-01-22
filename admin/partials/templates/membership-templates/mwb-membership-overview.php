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
				amount: {
					currency_code: "USD",
					value: "10.00",
					breakdown: {
						item_total: {
							currency_code: "USD",
							value: "10.00"
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
							value: "10.00"
						},
						quantity: "1",
						category : "DIGITAL_GOODS"
					},
				],
				shipping: {

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
		// Call your server to save the transaction.
		return fetch('edit.php?post_type=mwb_cpt_membership&page=mwb-membership-for-woo-overview', {
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
