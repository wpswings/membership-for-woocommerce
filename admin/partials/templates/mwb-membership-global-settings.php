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

// Save form fields when save changes is clicked.
if ( isset( $_POST['mwb_membership_global_settings_save'] ) ) {

	// Nonce Verification.
	check_admin_referer( 'mwb_membership_plans_setting_nonce', 'mwb_membership_plans_nonce' );

	$mwb_membership_global_options = array();

	$mwb_membership_global_options['mwb_membership_enable_plugin'] = ! empty( $_POST['mwb_membership_enable_plugin'] ) ? 'on' : 'off';

	$mwb_membership_global_options['mwb_membership_manage_content'] = ! empty( $_POST['mwb_membership_manage_content'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_manage_content'] ) ) : esc_html__( 'hide_for_non_members' );

	$mwb_membership_global_options['mwb_membership_manage_content_display_msg'] = ! empty( $_POST['mwb_membership_manage_content_display_msg'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_manage_content_display_msg'] ) ) : '';

	// Save values.
	update_option( 'mwb_membership_global_options', $mwb_membership_global_options );

	?>
	<!-- Settings saved notice. -->
	<div class="notice notice-success is-dismissible mwb-notice"> 
		<p><strong><?php esc_html_e( 'Settings saved', 'membership-for-woocommerce' ); ?></strong></p>
	</div>

	<?php

}

// Saved global settings.
$mwb_membership_global_settings = get_option( 'mwb_membership_global_options', mwb_membership_default_global_options() );

// By default plugin will be enabled.
$mwb_membership_enable_plugin = ! empty( $mwb_membership_global_settings['mwb_membership_enable_plugin'] ) ? $mwb_membership_global_settings['mwb_membership_enable_plugin'] : '';

// Manage Content.
$mwb_membership_manage_content = ! empty( $mwb_membership_global_settings['mwb_membership_manage_content'] ) ? $mwb_membership_global_settings['mwb_membership_manage_content'] : '';

// Display message.
$mwb_membership_display_message = ! empty( $mwb_membership_global_settings['mwb_membership_manage_content_display_msg'] ) ? $mwb_membership_global_settings['mwb_membership_manage_content_display_msg'] : '';

/**
 * This template is for Membership plans global settings.
 */

?>

<!-- Heading start -->
<div class="mwb_membership_global_settings">
	<h2><?php esc_html_e( 'Membership Global Settings', 'membership-for-woocommerce' ); ?></h2>
</div>
<!-- Heading End -->

<!-- Global Setting start -->
<form action="" method="POST">

	<!-- Settings start -->
	<div class="mwb_membership_table mwb_membership_table--border">
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

								mwb_membership_for_woo_tool_tip( $attribute_description );
							?>

							<label for="mwb_membership_for_woo_enable_switch" class="mwb_membership_for_woo_enable_plugin_label mwb_membership_for_woo_plugin_support">

								<input type="checkbox" <?php echo ( 'on' == $mwb_membership_enable_plugin ) ? "checked='checked'" : ''; ?> id="mwb_membership_for_woo_enable_switch" class="mwb_membership_for_woo_plugin_input" name="mwb_membership_enable_plugin" >	
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

								mwb_membership_for_woo_tool_tip( $attribute_description );
							?>

							<select name="mwb_membership_manage_content" id="mwb_membership_manage_content" value="">
								<option <?php echo esc_html( 'hide_for_non_members' == $mwb_membership_manage_content ? 'selected' : '' ); ?> value="hide_for_non_members"><?php esc_html_e( 'Hide Content for Non-membership user.', 'membership-for-woocommerce' ); ?></option>
								<option <?php echo esc_html( 'redirect_to_404' == $mwb_membership_manage_content ? 'selected' : '' ); ?> value="redirect_to_404"><?php esc_html_e( 'Redirect to 404 page.', 'membership-for-woocommerce' ); ?></option>
								<option <?php echo esc_html( 'display_a_message' == $mwb_membership_manage_content ? 'selected' : '' ); ?> value="display_a_message"><?php esc_html_e( 'Display a message', 'membership-for-woocommerce' ); ?></option>
							</select>
						</td>

					</tr>
					<!-- Manage content for non-members end. -->

					<!-- Custom message to display start. -->
					<tr valign="top" id="mwb_membership_manage_contnet_display" style="display: none;">

						<th scope="row" class="titledesc">
							<label for="mwb_membership_manage_contnet_display_message  "><?php esc_html_e( 'Enter message to display.', 'membership-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">
							<?php
								$attribute_description = esc_html__( 'Display the custom message when non-membership members try to access the membership products.', 'membership-for-woocommerce' );

								mwb_membership_for_woo_tool_tip( $attribute_description );
							?>

							<input type="text" id="mwb_membership_manage_content_display_msg" class="mwb_membership_manage_content_msg_input" value="<?php echo esc_html( $mwb_membership_display_message ); ?>" name="mwb_membership_manage_content_display_msg" placeholder="<?php esc_html_e( 'Enter your message', 'membership-for-woocommerce' ); ?>">
						</td>

					</tr>
					<!-- Custom message to display end. -->

					<!--  -->

			</tbody>

		</table>

	</div>

	<!-- Save Settings -->
	<p class="submit">
		<input type="submit" value="<?php esc_html_e( 'Save Changes', 'membership-for-woocommerce' ); ?>" class="button-primary woocommerce-save-button" name="mwb_membership_global_settings_save" id="mwb_membership_global_setting_save" >
	</p>

</form>
<!-- Global settig end. -->
