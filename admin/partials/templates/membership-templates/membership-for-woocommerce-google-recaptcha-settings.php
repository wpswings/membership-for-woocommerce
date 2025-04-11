<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for general tab.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

global $mfw_wps_mfw_obj;
$mfw_google_captcha_settings = apply_filters( 'mfw_google_captcha_settings', array() );
$wps_mfw_site_captcha_key = get_option( 'wps_mfw_site_captcha_key' );
?>

<!--  template file for admin settings. -->
<form action="" method="POST" class="wps-mfw-gen-section-form">
	<div class="mfw-secion-wrap">
		<?php
			do_action( 'mfw_whatsapp_api_settings_before' );
		?>
		<!-- Whatsapp Integration Settings -->
		<div class="wps-sm__modal"></div>
		<h4 class="wps_wpr_offer_notify_settings_heading"><?php esc_html_e( 'Override login forms and Integrate Google reCAPTCHA', 'membership-for-woocommerce' ); ?></h4>
		<?php
		$mfw_general_html = $mfw_wps_mfw_obj->wps_mfw_plug_generate_html( $mfw_google_captcha_settings );
		echo esc_html( $mfw_general_html );
		wp_nonce_field( 'admin_save_data', 'wps_tabs_nonce' );
		if ( ! empty( $wps_mfw_site_captcha_key ) ) {
		?>
			<div class="wps_mfw_captcha_wrap">
				<div class="wps_mfw_captcha_in">
					<span class="wps_mfw_captcha_closed">&times;</span>
					<div class="g-recaptcha" data-sitekey="<?php echo esc_html( $wps_mfw_site_captcha_key ); ?>"></div>
					<input type="submit" name="wps_mfw_verify_captcha" id="wps_mfw_verify_captcha" value="<?php esc_html_e( 'Verify Captcha', 'membership-for-woocommerce' ); ?>">
				</div>
			</div>
		<?php
		} ?>
	</div>
</form>
