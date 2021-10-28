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


if ( ! function_exists( 'mwb_sfw_check_plugin_enable' ) ) {
	/**
	 * This function is used to check plugin is enable.
	 *
	 * @name mwb_sfw_check_plugin_enable
	 * @since 1.0.0
	 */
	function mwb_membership_check_plugin_enable() {
		$is_enable = false;
		$mwb_membership_enable_plugin = get_option( 'mwb_membership_enable_plugin', '' );
		if ( 'on' == $mwb_membership_enable_plugin ) {
			$is_enable = true;
		}

		return $is_enable;
	}
}