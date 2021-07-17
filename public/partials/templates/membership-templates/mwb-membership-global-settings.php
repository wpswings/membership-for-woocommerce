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
/*
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
// */



// $instance = global_class;
// Saved global settings.
/*
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


