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

/**
 * This template is for Membership plans global settings.
 */

?>

<form action="" method="POST">

	<!-- Settings start -->
	<div class="mwb_membership_table mwb_upsell_table--border">
		<table class="form-table mwb_membership_plans_creation_setting">
			<tbody>

					<!-- Nonce field  -->
					<?php wp_nonce_field( 'mwb_membership_plans_setting_nonce', 'mwb_membership_plans_nonce' ); ?>

					<input type='hidden' id='mwb_membership_status' value='inactive'>

					<!-- Enable membership start. -->
					<tr valign="top">

						<th scope="row" class="titledesc">
							<label for="mwb_membership_enable_plugin  "><?php esc_html_e( 'Enable Membership Plans', 'membership-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">
							<?php
								$attribute_description = esc_html__( 'Enable Membership for Woocommerce plugin.', 'membership-for-woocommerce' );
								echo $attribute_description;
							?>

							<label for="mwb_membership_for_woo_enable_switch" class="mwb_membership_for_woo_enable_plugin_label mwb_membership_for_woo_plugin_support">

								<input id="mwb_membership_for_woo_enable_switch" class="mwb_membership_for_woo_plugin_input" type="checkbox" name="mwb_membership_enable_plugin" >	
								<span class="mwb_memebrship_for_woo_enable_plugin_span"></span>

							</label>
						</td>

					</tr>
					<!-- Enable membership end -->

					<!-- Manage content for non-members start. -->
					<tr valign="top">

						<th scope="row" class="titledesc">
							<label for="mwb_membership_manage_contnet  "><?php esc_html_e( 'Manage Content', 'membership-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">
							<?php
								$attribute_description = esc_html__( 'Manage content for non-membership members.', 'membership-for-woocommerce' );
								echo $attribute_description;
							?>

							<select name="mwb_membership_manage_content" id="mwb_membership_manage_content" value="">
								<option value="hide_for_non_mebers"><?php esc_html_e( 'Hide Content for Non-memberhsip user.', 'membership-for-woocommerce' ); ?></option>
								<option value="redirect_to_404"><?php esc_html_e( 'Redirect to 404 page.', 'membership-for-woocommerce' ); ?></option>
								<option value="display_a message"><?php esc_html_e( 'Display a message', 'membership-for-woocommerce' ); ?></option>
							</select>
						</td>

					</tr>
					<!-- Manage content for non-members end. -->

					<!-- Custom message to display start. -->
					<tr valign="top">

						<th scope="row" class="titledesc">
							<label for="mwb_membership_manage_contnet_display_message  "><?php esc_html_e( 'Enter message to display.', 'membership-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">
							<?php
								$attribute_description = esc_html__( 'Display the custom message when non-membership members try to access the membership products.', 'membership-for-woocommerce' );
								echo $attribute_description;
							?>

							<input type="text" id="mwb_membership_manage_content_display_msg" class="mwb_membership_manage_content_msg_input" vlaue="" name="mwb_membership_manage_content_display_msg" placeholder="<?php esc_html_e( 'Enter your message', 'membership-for-woocommerce' ); ?>">
						</td>

					</tr>
					<!-- Custom message to display end. -->

					<!--  -->

			</tbody>

		</table>

	</div>

</form>
