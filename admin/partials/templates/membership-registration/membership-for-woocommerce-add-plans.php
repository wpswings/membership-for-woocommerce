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

global $mfw_wps_mfw_obj;

$mfw_add_plans_settings =
/**
 * Filter is for returning something.
 *
 * @since 1.0.0
 */
apply_filters( 'mfw_add_plans_settings_array', array() );
?>
<!--  template file for admin settings. -->
<!-- <form action="" method="POST" class="wps-mfw-gen-section-form"> -->
	<div class="mfw-secion-wrap">
		<?php
		$mfw_wps_mfw_obj->wps_mfw_plug_generate_html( $mfw_add_plans_settings );

		?>
	</div>
<!-- </form> -->
