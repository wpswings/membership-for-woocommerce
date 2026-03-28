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
$mfw_genaral_settings =

/**
 * Filter for general setting.
 *
 * @since 1.0.0
 */
apply_filters( 'mfw_general_settings_array', array() );

$mfw_genaral_settings = is_array( $mfw_genaral_settings ) ? array_values( $mfw_genaral_settings ) : array();
$mfw_primary_setting  = ! empty( $mfw_genaral_settings ) ? array_shift( $mfw_genaral_settings ) : array();

?>
<form action="" method="POST" class="wps-mfw-gen-section-form">
	<div class="mfw-secion-wrap mfw-settings-screen">
		<?php

		/**
		 * Action for general setting before.
		 *
		 * @since 1.0.0
		 */
		do_action( 'mfw_general_settings_before' );
		?>

		<div class="wps-sm__modal"></div>
		<?php if ( ! empty( $mfw_primary_setting ) ) : ?>
			<section class="mfw-settings-hero">
				<div class="mfw-settings-hero__copy">
					<h3><?php esc_html_e( 'General Settings', 'membership-for-woocommerce' ); ?></h3>
					<p><?php esc_html_e( 'Enable the membership engine and configure how the plugin behaves across the store and member lifecycle.', 'membership-for-woocommerce' ); ?></p>
				</div>
				<div class="mfw-settings-hero__field">
					<?php
					$mfw_general_html = $mfw_wps_mfw_obj->wps_mfw_plug_generate_html( array( $mfw_primary_setting ) );
					echo esc_html( $mfw_general_html );
					?>
				</div>
			</section>
		<?php endif; ?>

		<section class="mfw-settings-card">
			<div class="mfw-settings-card__header">
				<h3><?php esc_html_e( 'Configuration', 'membership-for-woocommerce' ); ?></h3>
				<p><?php esc_html_e( 'Review the rest of the global membership settings before saving your configuration.', 'membership-for-woocommerce' ); ?></p>
			</div>
			<?php
			if ( ! empty( $mfw_genaral_settings ) ) {
				$mfw_general_html = $mfw_wps_mfw_obj->wps_mfw_plug_generate_html( $mfw_genaral_settings );
				echo esc_html( $mfw_general_html );
			}
			wp_nonce_field( 'admin_save_data', 'wps_tabs_nonce' );
			?>
		</section>
	</div>
</form>
