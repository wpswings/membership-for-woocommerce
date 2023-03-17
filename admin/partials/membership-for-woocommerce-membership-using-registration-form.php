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
 * @subpackage Membership_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $mfw_wps_mfw_obj;
?>

<div class="wrap mwb_bfw_config_tab">
		<?php
		if ( class_exists( 'Membership_For_Woocommerce_Admin' ) ) {
			$wps_mfw_sub_tabs_array = $mfw_wps_mfw_obj->wps_mfw_plug_config_sub_tabs();



			$active_sub_tab         = isset( $_GET['mfw_reg_sub_nav'] ) ? sanitize_key( $_GET['mfw_reg_sub_nav'] ) : '';// phpcs:ignore
			if ( ! isset( $_GET['mfw_reg_sub_nav'] ) ) {// phpcs:ignore
				$active_sub_tab = 'membership-for-woocommerce-add-plans';
			}
			?>
			<h3 class="nav-tab-wrapper">
			<?php

			foreach ( $wps_mfw_sub_tabs_array as $key => $taxonomy_slug ) {

				$mwb_sub_tab_title = $taxonomy_slug['name'];
				$mwb_name          = $taxonomy_slug['title'];
				echo "<a href='admin.php?page=membership_for_woocommerce_menu&mfw_tab=membership-for-woocommerce-membership-using-registration-form&mfw_reg_sub_nav=" . esc_attr( $mwb_sub_tab_title ) . "' class='nav-tab " . ( $active_sub_tab === $mwb_sub_tab_title ? 'nav-tab-active' : '' ) . " wps-mfw-nav-tab'>" . esc_attr( $mwb_name ) . '</a>';
			}


			?>
			</h3>

			<?php
			if ( array_key_exists( $active_sub_tab, $wps_mfw_sub_tabs_array ) ) {
				echo '<section class="wps-section">';
				echo '<div>';
				$mfw_wps_mfw_obj->wps_mfw_plug_load_template( $wps_mfw_sub_tabs_array[ $active_sub_tab ]['file_path'] );
				echo '</div></section>';
			}

			?>
		
			<?php
		}

		?>
</div>
