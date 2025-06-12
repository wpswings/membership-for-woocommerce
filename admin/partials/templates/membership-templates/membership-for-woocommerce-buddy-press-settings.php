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
$mfw_buddy_press_settings_array = apply_filters( 'mfw_buddy_press_settings_array', array() );
?>

<!--  template file for admin settings. -->
<form action="" method="POST" class="wps-mfw-gen-section-form">
	<div class="mfw-secion-wrap">
	<?php
		do_action( 'mfw_api_settings_before' );
	?>
	<div class="wps-sm__modal"><h4 class="wps_wpr_offer_notify_settings_heading">
		<?php esc_html_e( 'BuddyPress Integration Settings enable effortless user and group management.', 'membership-for-woocommerce' ); ?></h4>
	</div>
		<?php
		$mfw_general_html = $mfw_wps_mfw_obj->wps_mfw_plug_generate_html( $mfw_buddy_press_settings_array );
		echo esc_html( $mfw_general_html );
		wp_nonce_field( 'admin_save_data', 'wps_tabs_nonce' );
		?>
	</div>
</form>

