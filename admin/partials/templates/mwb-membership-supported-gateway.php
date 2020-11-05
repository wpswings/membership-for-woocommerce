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

// $wc_gateways      = new WC_Payment_Gateways();
// $payment_gateways = $wc_gateways->get_available_payment_gateways();
// //print_r(mwb_membership_for_woo_supported_gateways());

// if ( ! empty( mwb_membership_for_woo_supported_gateways() ) ) {

// 	//$payment_gateways = mwb_membership_for_woo_supported_gateways();

// 	// Loop through Woocommerce available payment gateways.
// 	foreach ( $payment_gateways as $gateway => $id ) {

// 		echo $id->get_title();
// 	}
// }

?>

<div class="mwb_membership_table mwb_membership_gateways">
	<table class="form-table mwb_membership_plan_gateways">
		<tbody>
			<?php

			if ( ! empty( mwb_membership_for_woo_supported_gateways() ) ) {

				$payment_gateways = mwb_membership_for_woo_supported_gateways();

				// Loop through Woocommerce available payment gateways.
				foreach ( $payment_gateways as $gateway ) {

					//echo $gateway;
			?>
					<!-- Membership supported  start. -->
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label><?php echo esc_html( ucfirst( $gateway ) ); ?></label>
						</th>

						<td class="forminp forminp-text">
							<div class="mwb_membership_gateway_div">
								<p class="mwb_membership_shortcode">

									<a class="button" href="<?php echo admin_url( 'admin.php' ) . '?page=wc-settings&tab=checkout&section=' . $gateway; ?>"><?php esc_html_e( 'Manage', 'membership-for-woocommerce' ); ?></a>
								</p>

							</div>
						</td>

					</tr>
					<?php
				}
			}
			?>
			<!-- Membership Action Shortcodes End. -->

		</tbody>

	</table>

</div>
