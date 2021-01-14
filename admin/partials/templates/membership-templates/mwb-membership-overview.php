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

$class = new Membership_Activity_Helper( 'mfw-invoices', 'uploads' );
$data =  $class->create_pdf_n_upload( 'Testegetgdjknfdbgkjnkjn nklj hdfkls ndkljshju', 'filename' );
echo '<pre>'; print_r( $data ); echo '</pre>';
return;


?>

<!-- Heading start -->
<div class="mwb_membership_Overview">
	<h2><?php esc_html_e( 'Membership Overview', 'membership-for-woocommerce' ); ?></h2>
</div>
<!-- Heading end. -->

<div id="paypal-button-container"></div>

<!-- Include the PayPal JavaScript SDK -->
<script src="https://www.paypal.com/sdk/js?client-id=sb&currency=USD"></script>

<script>
	// Render the PayPal button into #paypal-button-container
	paypal.Buttons({

		// Set up the transaction
		createOrder: function(data, actions) {
			return actions.order.create({
				purchase_units: [{
					amount: {
						value: '88.44'
					}
				}]
			});
		},

		// Finalize the transaction
		onApprove: function(data, actions) {
			return actions.order.capture().then(function(details) {
				// Show a success message to the buyer
				alert('Transaction completed by ' + details.payer.name.given_name + '!');
			});
		}


	}).render('#paypal-button-container');
</script>

<!-- <script src="https://www.paypal.com/sdk/js?client-id=sb"></script>
<script>paypal.Buttons().render('body');</script> -->
