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


/**
 * Allow to remove method for an hook when, it's a class method used and class don't have global for instanciation !
 */
function remove_filters_with_method_name( $hook_name = '', $method_name = '', $priority = 0 ) {
	global $wp_filter;
	
	// Take only filters on right hook name and priority
	if ( ! isset( $wp_filter[ $hook_name ][ $priority ] ) || ! is_array( $wp_filter[ $hook_name ][ $priority ] ) ) {
	//	return false;

	}

// Loop on filters registered
	foreach ( (array) $wp_filter[ $hook_name ][ $priority ] as $unique_id => $filter_array ) {
		// Test if filter is an array ! (always for class/method)
		if ( isset( $filter_array['function'] ) && is_array( $filter_array['function'] ) ) {
			// Test if object is a class and method is equal to param !
			if ( is_object( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) && $filter_array['function'][1] == $method_name ) {
				// Test for WordPress >= 4.7 WP_Hook class (https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/)
				if ( is_a( $wp_filter[ $hook_name ], 'WP_Hook' ) ) {
					unset( $wp_filter[ $hook_name ]->callbacks[ $priority ][ $unique_id ] );
				} else {
					unset( $wp_filter[ $hook_name ][ $priority ][ $unique_id ] );
				}
			}
		}

	}

	return false;
}