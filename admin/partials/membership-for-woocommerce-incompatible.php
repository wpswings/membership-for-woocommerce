<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Rewardeem_woocommerce_Points_Rewards
 * @subpackage Rewardeem_woocommerce_Points_Rewards/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;

}
if ( class_exists( 'Membership_For_Woocommerce_Admin' ) ) {

	$wps_mfw_get_count = new Membership_For_Woocommerce_Admin( 'membership-for-woocommerce', '2.1.0' );
	$wps_pending_par   = $wps_mfw_get_count->wps_membership_get_count( 'pending', 'count' );




	if ( 0 != $wps_pending_par ) {


				$wps_par_global_custom_css = 'const triggerError = () => {
					swal({
				
						title: "Attention Required!",
						text: "Please Migrate Your Database Keys First By Clicking On Below Button , Then You can Have Access To Your Dashboard Button",
						icon: "error",
						button: "Click To Import",
						closeOnClickOutside: false,
					}).then(function() {
						wps_membership_migration_success();
					});
				}
				triggerError();';
			wp_register_script( 'wps_par_incompatible_css', false, array(), '2.1.1', 'all' );
			wp_enqueue_script( 'wps_par_incompatible_css' );
			wp_add_inline_script( 'wps_par_incompatible_css', $wps_par_global_custom_css );

	}
}
