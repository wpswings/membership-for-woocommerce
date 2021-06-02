<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for general tab.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $mfw_mwb_mfw_obj;
$mfw_template_settings = apply_filters( 'mfw_template_settings_array', array() );
?>
<!--  template file for admin settings. -->
<div class="mfw-section-wrap">
	<?php
		$mfw_template_html = $mfw_mwb_mfw_obj->mwb_mfw_plug_generate_html( $mfw_template_settings );
		echo esc_html( $mfw_template_html );
	?>
</div>
