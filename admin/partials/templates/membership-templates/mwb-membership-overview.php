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
$activity_class = new Membership_Activity_Helper( 'mfw-invoices', 'uploads' );
$pdf_file       = $activity_class->create_pdf_n_upload( 'filddfkhvgbdlivbdjvbdvv', 'harshit' );
echo '<pre>'; print_r( $pdf_file ); echo '</pre>';
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
