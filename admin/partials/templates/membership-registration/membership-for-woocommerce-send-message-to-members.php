<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for membership using registration form tab.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage admin/partials/templates/membership-registration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<form action method="POST" class="wps-mfw-gen-section-form">
	<div class="wps-form-group">
		<div class="wps-form-group__label">
			<label class="wps-form-label" ><?php esc_html_e( 'Message', 'membership-for-woocommerce' ); ?></label>
		</div>
		<div class="wps-form-group__control">
			<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--textarea"      for="text-field-hero-input">
				<span class="mdc-notched-outline">
					<span class="mdc-notched-outline__leading"></span>
					<span class="mdc-notched-outline__notch">
						<span class="mdc-floating-label"><?php esc_html_e( 'Enter Message', 'membership-for-woocommerce' ); ?></span>
					</span>
					<span class="mdc-notched-outline__trailing"></span>
				</span>
				<span class="mdc-text-field__resizer">
					<textarea class="mdc-text-field__input " rows="2" cols="25" aria-label="Label" name="wps-mfwp-msg-body" id="wps-mfwp-msg-body" placeholder="Enter message"></textarea>
				</span>
			</label>
		</div>
	</div>
	<tr valign="top">
		<td scope="row">
			<input type="hidden" name="wps_send_msg_hidden" value="<?php wp_create_nonce( 'wps_mfw_send_msg_nonce' ); ?>">
			<input type="submit" class="button button-primary" 
			name="wps-mfwp-send-to-all-members"
			id="wps-mfwp-send-to-all-members"
			class=""
			value="<?php esc_attr_e( 'Send to all Members', 'membership-for-woocommerce' ); ?>"
			/>
		</td>
	</tr>
</form>


