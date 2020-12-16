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

// Creating Instance of the global functions class.
$global_class = Membership_For_Woocommerce_Global_Functions::get();

?>
<!-- Heading start-->
<div class="mwb_membership_supported_gateways">
	<h2><?php esc_html_e( 'Membership Supported Gateways', 'membership-for-woocommerce' ); ?></h2>
</div>
<!-- Heading end. -->

<!-- Gateway start. -->
<div class="mwb_membership_manage_gateways">
	<a href="<?php echo esc_html( admin_url( 'admin.php?page=wc-settings&tab=checkout' ) ); ?>"><?php esc_html_e( 'Setup Membership Supported Gateways->', 'membership-for-woocommerce' ); ?></a>
</div>
<div class="mwb_membership_table mwb_membership_gateways">
	<table class="form-table mwb_membership_plan_gateways">
		<tbody>
			<?php

			if ( ! empty( $global_class->supported_gateways() ) ) {

				$payment_gateways   = $global_class->supported_gateways();
				$available_gateways = $global_class->available_gateways();

				// Loop through Woocommerce available payment gateways.
				foreach ( $payment_gateways as $gateway ) {

					?>

					<!-- Membership supported gateway start. -->
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label><?php echo esc_html( $global_class->get_payment_method_title( $gateway ) ? $global_class->get_payment_method_title( $gateway ) : ucfirst( str_replace( '-', ' ', $gateway ) ) ); ?></label>
						</th>

						<td class="forminp forminp-text">

						<?php

						if ( in_array( $gateway, $available_gateways ) ) {

							?>
							<div class="mwb_membership_gateway_div">
								<p class="mwb_membership_gateway">

									<a class="button" href="<?php echo esc_html( admin_url( 'admin.php' ) . '?page=wc-settings&tab=checkout&section=' . $gateway ); ?>"><?php esc_html_e( 'Manage', 'membership-for-woocommerce' ); ?></a>
								</p>

							</div>

							<?php
						} else {
							?>
							<div class="mwb_membership_gateway_div">
								<p class="mwb_membership_gateway">

									<a class="button disabled" href="javascript:void(0)"><?php esc_html_e( 'Manage', 'membership-for-woocommerce' ); ?></a>
								</p>

							</div>
							<?php
						}
						?>
						</td>

					</tr>
					<?php
				}
			}
			?>
			<!-- Membership Gateway End. -->

		</tbody>

	</table>

</div>
<!-- Gateway End. -->

