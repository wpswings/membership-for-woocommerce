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

	$mwb_membership_global_options['mwb_membership_delete_data'] = ! empty( $_POST['mwb_membership_delete_data'] ) ? 'on' : 'off';

	$mwb_membership_global_options['mwb_membership_plan_user_history'] = ! empty( $_POST['mwb_membership_plan_user_history'] ) ? 'on' : 'off';
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
$mwb_membership_global_settings = get_option( 'mwb_membership_global_options', $instance->default_global_options() );

// By default plugin will be enabled.
$mwb_membership_enable_plugin = ! empty( $mwb_membership_global_settings['mwb_membership_enable_plugin'] ) ? $mwb_membership_global_settings['mwb_membership_enable_plugin'] : '';

// Data delete setting.
$mwb_membership_delete_data = ! empty( $mwb_membership_global_settings['mwb_membership_delete_data'] ) ? $mwb_membership_global_settings['mwb_membership_delete_data'] : '';

// Show history to user.
$mwb_membership_user_history = ! empty( $mwb_membership_global_settings['mwb_membership_plan_user_history'] ) ? $mwb_membership_global_settings['mwb_membership_plan_user_history'] : '';

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

								$instance->tool_tip( $attribute_description );
							?>

							<label for="mwb_membership_for_woo_enable_switch" class="mwb_membership_for_woo_enable_plugin_label mwb_membership_for_woo_plugin_support">

								<input type="checkbox" <?php echo ( 'on' === $mwb_membership_enable_plugin ) ? "checked='checked'" : ''; ?> id="mwb_membership_for_woo_enable_switch" class="mwb_membership_for_woo_plugin_input" name="mwb_membership_enable_plugin" >	
								<span class="mwb_memebrship_for_woo_enable_plugin_span"></span>

							</label>
						</td>

					</tr>
					<!-- Enable membership end -->

					<!-- Delete data at uninstall start -->
					<tr valign="top" id="mwb_membership_delete_data">

						<th scope="row" class="titledesc">
							<label for="mwb_membership_delete_data"><?php esc_html_e( 'Delete data at Uninstall', 'membership-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">
							<?php
								$attribute_description = esc_html__( 'If enabled, this will delete all data at plugin unistall', 'membership-for-woocommerce' );

								$instance->tool_tip( $attribute_description );
							?>

							<label for="mwb_membership_for_woo_delete_data" class="mwb_membership_for_woo_delete_data">

								<input type="checkbox" <?php echo ( 'on' === $mwb_membership_delete_data ) ? "checked='checked'" : ''; ?> id="mwb_membership_for_woo_delete_data" class="mwb_membership_for_woo_plugin_input" name="mwb_membership_delete_data" >	
								<span class="mwb_memebrship_for_woo_delete_data_span"></span>

							</label>
						</td>

					</tr>
					<!-- Delete data at uninstall end -->

					<!-- Show history to user start -->
					<tr valign="top">

						<th scope="row" class="titledesc">
							<label for="mwb_membership_plan_user_access"><?php esc_html_e( 'Show History to User', 'membership-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">

							<?php

							$description = esc_html__( 'This will Enable Users to visit and see Plans Histroy in Membership tab  on My Account page.', 'membership-for-woocommerce' );

							$instance->tool_tip( $description );

							?>

							<input type="checkbox" id="mwb_membership_plan_user_history" name="mwb_membership_plan_user_history" value="yes" <?php echo ( 'on' === $mwb_membership_user_history ) ? "checked='checked'" : ''; ?>>
						</td>
					</tr>
					<!-- Show history to user end -->

			</tbody>

		</table>

	</div>

	<!-- Save Settings -->
	<p class="submit">
		<input type="submit" value="<?php esc_html_e( 'Save Changes', 'membership-for-woocommerce' ); ?>" class="button-primary woocommerce-save-button" name="mwb_membership_global_settings_save" id="mwb_membership_global_setting_save" >
	</p>

</form>
<!-- Global settig end. -->
