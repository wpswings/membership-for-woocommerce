<?php
/**
 * Fired during plugin activation
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'mwb_membership_check_product_is_membership' ) ) {
	/**
	 * This function is used to check susbcripton product.
	 *
	 * @name mwb_sfw_check_product_is_subscription
	 * @param Object $product product.
	 * @since 1.0.0
	 */
	function mwb_membership_check_product_is_membership( $product ) {

		$mwb_is_membership = false;
		if ( is_object( $product ) ) {
			$product_id = $product->get_id();
			$mwb_membership_default_product = get_option( 'mwb_membership_default_product', '' );

			if ( $mwb_membership_default_product == $product_id ) {
				$mwb_is_membership = true;
			}
		}

		return apply_filters( 'mwb_membership_check_membership_product_type', $mwb_is_membership, $product );
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


