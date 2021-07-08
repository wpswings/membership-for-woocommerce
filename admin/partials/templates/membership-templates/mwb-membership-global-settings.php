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

	$mwb_membership_global_options['mwb_membership_email_subject'] = ! empty( $_POST['mwb_membership_email_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_email_subject'] ) ) : '';

	$mwb_membership_global_options['mwb_membership_email_content'] = ! empty( $_POST['mwb_membership_email_content'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_email_content'] ) ) : '';

	$mwb_membership_global_options['mwb_membership_attach_invoice'] = ! empty( $_POST['mwb_membership_attach_invoice'] ) ? 'on' : 'off';
	//phpcs:disable
	$mwb_membership_global_options['mwb_membership_invoice_address'] = ! empty( $_POST['mwb_membership_invoice_address'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_invoice_address'] ) ) : ''; // phpcs:ignore
	//phpcs:enable

	$mwb_membership_global_options['mwb_membership_invoice_phone'] = ! empty( $_POST['mwb_membership_invoice_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_invoice_phone'] ) ) : '';

	$mwb_membership_global_options['mwb_membership_invoice_email'] = ! empty( $_POST['mwb_membership_invoice_email'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_invoice_email'] ) ) : '';

	//phpcs:disable
	$mwb_membership_global_options['mwb_membership_invoice_logo'] = ! empty( $_POST['mwb_membership_invoice_logo'] ) ? esc_url_raw( wp_unslash( $_POST['mwb_membership_invoice_logo'] ) ) : ''; // phpcs:ignore
	//phpcs:enable

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

// Email subject.
$mwb_membership_email_subject = ! empty( $mwb_membership_global_settings['mwb_membership_email_subject'] ) ? $mwb_membership_global_settings['mwb_membership_email_subject'] : '';

// Email Content.
$mwb_membership_email_content = ! empty( $mwb_membership_global_settings['mwb_membership_email_content'] ) ? $mwb_membership_global_settings['mwb_membership_email_content'] : '';

// Attach invoice.
$mwb_membership_attach_invoice = ! empty( $mwb_membership_global_settings['mwb_membership_attach_invoice'] ) ? $mwb_membership_global_settings['mwb_membership_attach_invoice'] : '';

// Company address.
$mwb_membership_invoice_address = ! empty( $mwb_membership_global_settings['mwb_membership_invoice_address'] ) ? $mwb_membership_global_settings['mwb_membership_invoice_address'] : '';

// Company Phone.
$mwb_membership_invoice_phone = ! empty( $mwb_membership_global_settings['mwb_membership_invoice_phone'] ) ? $mwb_membership_global_settings['mwb_membership_invoice_phone'] : '';

// Company phone.
$mwb_membership_invoice_email = ! empty( $mwb_membership_global_settings['mwb_membership_invoice_email'] ) ? $mwb_membership_global_settings['mwb_membership_invoice_email'] : '';

// COmpany Logo.
$mwb_membership_invoice_logo = ! empty( $mwb_membership_global_settings['mwb_membership_invoice_logo'] ) ? $mwb_membership_global_settings['mwb_membership_invoice_logo'] : '';

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
								$attribute_description = esc_html__( 'Enable/Disable Membership for Woocommerce plugin functionality on front-end.', 'membership-for-woocommerce' );

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
								$attribute_description = esc_html__( 'If enabled, this will delete all data at plugin uninstall', 'membership-for-woocommerce' );

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
							<label for="mwb_membership_plan_user_history"><?php esc_html_e( 'Show History to User', 'membership-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">

							<?php

							$description = esc_html__( 'This will Enable Users to visit and see Plans History in Membership tab  on My Account page.', 'membership-for-woocommerce' );

							$instance->tool_tip( $description );

							?>

							<input type="checkbox" id="mwb_membership_plan_user_history" name="mwb_membership_plan_user_history" value="yes" <?php echo ( 'on' === $mwb_membership_user_history ) ? "checked='checked'" : ''; ?>>
						</td>
					</tr>
					<!-- Show history to user end -->

					<!-- Email Subject start-->
					<tr valign="top">

						<th scope="row" class="titledesc">
							<label for="mwb_membership_email_subject"><?php esc_html_e( 'Email Subject', 'membership-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">

							<?php

							$description = esc_html__( 'This will add email subject which will be sent to Customer on successful membership purchase.', 'membership-for-woocommerce' );

							$instance->tool_tip( $description );

							?>

							<input type="text" id="mwb_membership_email_subject" name="mwb_membership_email_subject" value="<?php echo esc_html( $mwb_membership_email_subject ); ?>">
						</td>
					</tr>
					<!-- Email Subject end-->

					<!-- Email Content start-->
					<tr valign="top">

						<th scope="row" class="titledesc">
							<label for="mwb_membership_email_content"><?php esc_html_e( 'Email Content', 'membership-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">

							<?php

							$description = esc_html__( 'This will add email content which will be sent to Customer on successful membership purchase.', 'membership-for-woocommerce' );

							$instance->tool_tip( $description );

							$content   = esc_html( $mwb_membership_email_content );
							$editor_id = 'mwb_membership_email_content';

							$args = array(
								'media_buttons' => false,
								'tinymce'       => array(
									'toolbar1' => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
								),
							);

							wp_editor( $content, $editor_id, $args );

							?>

						</td>
					</tr>
					<!-- Email Content end-->

					<!-- Attach invoice to email start -->
					<tr valign="top">

						<th scope="row" class="titledesc">
							<label for="mwb_membership_attach_invoice"><?php esc_html_e( 'Attach Invoice to Email', 'membership-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">

							<?php

							$description = esc_html__( 'This will Enable/Disable Invoice attachment to Email.', 'membership-for-woocommerce' );

							$instance->tool_tip( $description );

							?>

							<input type="checkbox" id="mwb_membership_attach_invoice" name="mwb_membership_attach_invoice" value="yes" <?php echo ( 'on' === $mwb_membership_attach_invoice ) ? "checked='checked'" : ''; ?> >
						</td>
					</tr>
					<!-- Attach invoice to email start -->

					<!-- Invoice Company address start -->
					<tr valign="top" class="mfw_membership_invoice_pdf">

						<th scope="row" class="titledesc">
							<label for="mwb_membership_invoice_address"><?php esc_html_e( 'Invoice Company Address', 'membership-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">

							<?php

							$description = esc_html__( 'This will add Company address to Invoice else default address will be added.', 'membership-for-woocommerce' );

							$instance->tool_tip( $description );

							?>

							<textarea id="mwb_membership_invoice_address" name="mwb_membership_invoice_address" ><?php echo esc_html( $mwb_membership_invoice_address ); ?></textarea>
						</td>
					</tr>
					<!-- Invoice Company address end-->

					<!-- Invoice Company phone start -->
					<tr valign="top" class="mfw_membership_invoice_pdf">

						<th scope="row" class="titledesc">
							<label for="mwb_membership_invoice_phone"><?php esc_html_e( 'Invoice Company Phone No.', 'membership-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">

							<?php

							$description = esc_html__( 'This will add Company phone no. to Invoice else default phone no. will be added.', 'membership-for-woocommerce' );

							$instance->tool_tip( $description );

							?>

							<input type="tel" id="mwb_membership_invoice_phone" name="mwb_membership_invoice_phone" value="<?php echo esc_html( $mwb_membership_invoice_phone ); ?>">
						</td>
					</tr>
					<!-- Invoice Company phone end-->

					<!-- Invoice Company email start -->
					<tr valign="top" class="mfw_membership_invoice_pdf">

						<th scope="row" class="titledesc">
							<label for="mwb_membership_invoice_email"><?php esc_html_e( 'Invoice Company Email', 'membership-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">

							<?php

							$description = esc_html__( 'This will add Company email to Invoice else default email will be added.', 'membership-for-woocommerce' );

							$instance->tool_tip( $description );

							?>

							<input type="email" id="mwb_membership_invoice_email" name="mwb_membership_invoice_email" value="<?php echo esc_html( $mwb_membership_invoice_email ); ?>">
						</td>
					</tr>
					<!-- Invoice Company email end-->

					<!-- Invoice Company logo start -->
					<tr valign="top" class="mfw_membership_invoice_pdf">

						<th scope="row" class="titledesc">
							<label for="mwb_membership_invoice_logo"><?php esc_html_e( 'Invoice Company logo', 'membership-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">

							<?php

							$description = esc_html__( 'This will add Company logo url to Invoice ( JPG, JPEG, PNG only ).', 'membership-for-woocommerce' );

							$instance->tool_tip( $description );

							$upload_btn_cls = empty( $mwb_membership_invoice_logo ) ? '' : 'button_hide';
							$remove_btn_cls = ! empty( $mwb_membership_invoice_logo ) ? '' : 'button_hide';

							?>

							<input type="hidden" id="mwb_membership_invoice_logo" name="mwb_membership_invoice_logo" value="<?php echo esc_html( $mwb_membership_invoice_logo ); ?>">
							<input type="button" id="upload_img" class="button <?php echo esc_html( $upload_btn_cls ); ?>" value="<?php esc_html_e( 'Upload Logo', 'membership-for-woocommerce' ); ?>">
							<input type="button" id="remove_img" class="button <?php echo esc_html( $remove_btn_cls ); ?>" value="<?php esc_html_e( 'Remove Logo', 'membership-for-woocommerce' ); ?>">
							<div id="img_thumbnail">
								<?php if ( '' !== $mwb_membership_invoice_logo ) { ?>
									<img src="<?php echo esc_html( $mwb_membership_invoice_logo ); ?>" width="60px" height="60px"/>
								<?php } ?>     
							</div>
						</td>
					</tr>
					<!-- Invoice Company email end-->

			</tbody>

		</table>

	</div>

	<!-- Save Settings -->
	<p class="submit">
		<input type="submit" value="<?php esc_html_e( 'Save Changes', 'membership-for-woocommerce' ); ?>" class="button-primary woocommerce-save-button" name="mwb_membership_global_settings_save" id="mwb_membership_global_setting_save" >
	</p>

</form>
<!-- Global settig end. -->
