<?php
/**
 * Fired during plugin activation
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wps_membership_check_product_is_membership' ) ) {
	/**
	 * This function is used to check susbcripton product.
	 *
	 * @name wps_sfw_check_product_is_subscription
	 * @param Object $product product.
	 * @since 1.0.0
	 */
	function wps_membership_check_product_is_membership( $product ) {

		$wps_is_membership = false;
		if ( is_object( $product ) ) {
			$product_id = $product->get_id();
			$wps_membership_default_product = get_option( 'wps_membership_default_product', '' );

			if ( $wps_membership_default_product == $product_id ) {
				$wps_is_membership = true;
			}
		}

		/**
		 * Filter to check product type.
		 *
		 * @since 1.0.0
		 */
		return apply_filters( 'wps_membership_check_membership_product_type', $wps_is_membership, $product );
	}
}





/**
 * Function to get member from order.
 *
 * @param [type] $order is the order.
 * @return mixed
 */
function get_member_id_from_order( $order ) {
	$member_id = '';
	foreach ( $order->get_items() as $item_id => $item ) {

		if ( ! empty( $item->get_meta( '_member_id' ) ) ) {
			$member_id = $item->get_meta( '_member_id' );
		}
	}
	return $member_id;
}

/**
 * Function to check for plugin activation.
 *
 * @param string $plugin_slug is the slug of the plugin.
 */
function mwb_membership_is_plugin_active( $plugin_slug = '' ) {
	if ( empty( $plugin_slug ) ) {

		return;
	}

	$active_plugins = (array) get_option( 'active_plugins', array() );

	if ( is_multisite() ) {

		$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}

	return in_array( $plugin_slug, $active_plugins ) || array_key_exists( $plugin_slug, $active_plugins );
}


