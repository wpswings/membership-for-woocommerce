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


