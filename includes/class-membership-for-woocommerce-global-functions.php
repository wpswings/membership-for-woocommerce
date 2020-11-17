<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin
 */

/**
 * Allowed html for description on admin side
 *
 * @param string $description tooltip message.
 */
function mwb_membership_for_woo_tool_tip( $description = '' ) {

	// Run only if description message is present.
	if ( ! empty( $description ) ) {

		$allowed_html = array(
			'span' => array(
				'class'    => array(),
				'data-tip' => array(),
			),
		);

		echo wp_kses( wc_help_tip( $description ), $allowed_html );
	}
}

/**
 * Returns product name and status.
 *
 * @param string $product_id Product id of a particular product.
 */
function mwb_membership_for_woo_get_product_title( $product_id = '' ) {

	if ( ! empty( $product_id ) ) {

		$result = esc_html__( 'Product not found', 'membership-for-woocommerce' );

		$product = wc_get_product( $product_id );

		if ( ! empty( $product ) ) {

			if ( 'publish' != $product->get_status() ) {

				$result = esc_html__( 'Product unavailable', 'membership-for-woocommerce' );

			} else {

				$result = get_the_title( $product_id );

			}
		}

		return $result;
	}
}

/**
 * Return category name and its existance
 *
 * @param string $cat_id Category ID of a particular category.
 */
function mwb_membership_for_woo_get_category_title( $cat_id = '' ) {

	if ( ! empty( $cat_id ) ) {

		$result = esc_html__( 'Category not found', 'membership-for-woocommerce' );

		$cat_name = get_the_category_by_ID( $cat_id );

		if ( ! empty( $cat_name ) ) {

			$result = $cat_name;

		}

		return $result;
	}
}

/**
 * Membership default global options
 */
function mwb_membership_default_global_options() {

	$default_global_settings = array(

		'mwb_membership_enable_plugin'              => 'on',
		'mwb_membership_manage_content'             => 'hide_for_non_members',
		'mwb_membership_manage_content_display_msg' => '',
	);

	return $default_global_settings;

}

/**
 * Membership product title for CSV.
 *
 * @param array $products An array of product ids.
 */
function mwb_membership_csv_get_title( $products ) {

	if ( ! empty( $products ) && is_array( $products ) ) {

		$product_ids = ! empty( $products ) ? array_map( 'absint', $products ) : null;

		$output = '';

		if ( $product_ids ) {

			foreach ( $product_ids as $single_id ) {

				$single_name = mwb_membership_for_woo_get_product_title( $single_id );
				$output     .= esc_html( $single_name ) . '(#' . esc_html( $single_id ) . '),';

			}
		}

		$output = preg_replace( '/,[^,]*$/', '', $output );
		return $output;
	}

}

/**
 * Membership category title for CSV.
 *
 * @param array $categories An array of cataegory ids.
 */
function mwb_membership_csv_get_cat_title( $categories ) {

	if ( ! empty( $categories ) && is_array( $categories ) ) {

		$category_ids = ! empty( $categories ) ? array_map( 'absint', $categories ) : null;

		$output = '';

		if ( $category_ids ) {

			foreach ( $category_ids as $cat_id ) {

				$single_cat = mwb_membership_for_woo_get_category_title( $cat_id );
				$output    .= esc_html( $single_cat ) . '(#' . esc_html( $cat_id ) . '),';
			}
		}

		$output = preg_replace( '/,[^,]*$/', '', $output );
		return $output;
	}
}

/**
 * Membership supported gateways.
 */
function mwb_membership_for_woo_supported_gateways() {

	$supported_gateways = array(
		'bacs', // Direct bank transfer.
		'cheque', // Cheque payment.
		'cod', // Cash on delivery.
		'paypal', // Wocommmerce paypal (standard).
		'stripe_ideal', // Official stripe.
		'membership-for-woo-paypal-gateway', // Membership Paypal.
		'membership-for-woo-stripe-gateway', // Membership stripe.
	);

	return apply_filters( 'mwb_membership_for_woo_supported_gateways', $supported_gateways );
}

/**
 * Available payment gateways.
 */
function mwb_membership_for_woo_available_gateways() {

	$wc_gateways      = new WC_Payment_Gateways();
	$payment_gateways = $wc_gateways->get_available_payment_gateways();

	$woo_gateways = array();

	if ( ! empty( $payment_gateways ) && is_array( $payment_gateways ) ) {

		// Loop through Woocommerce available payment gateways.
		foreach ( $payment_gateways as $gateway_id ) {

			$woo_gateways[] = $gateway_id->id;
		}
	}

	return $woo_gateways;

}

/**
 * Cart item Ids
 */
function mwb_memberhsip_for_woo_cart_item_ids() {

	$cart_items = WC()->cart->get_cart();

	$prod_ids = array();

	if ( ! empty( $cart_items ) && is_array( $cart_items ) ) {

		foreach ( $cart_items as $item ) {

			$prod_ids[] = $item['product_id'];
		}
	}

	return $prod_ids;
}

/**
 * Cart item category IDS.
 */
function mwb_membership_for_woo_cart_item_cat_ids() {

	$cart_items = WC()->cart->get_cart();

	$cat_ids = array();

	if ( ! empty( $cart_items ) && is_array( $cart_items ) ) {

		foreach ( $cart_items as $cart_item_key => $cart_item ) {

			$cat_ids = array_merge( $cat_ids, $cart_item['data']->get_category_ids() );
		}
	}

	return $cat_ids;
}

/**
 * Get all plans offered products ids.
 */
function mwb_membership_for_woo_plans_products_ids() {

	$args = array(
		'post_type'   => 'mwb_cpt_membership',
		'post_status' => array( 'publish' ),
		'numberposts' => -1,
	);

	$products = array();

	$all_posts = get_posts( $args );

	if ( ! empty( $all_posts ) && is_array( $all_posts ) ) {

		foreach ( $all_posts as $post ) {

			$products = get_post_meta( $post->ID, 'mwb_membership_plan_target_ids', true );

			if ( is_array( $products ) ) {

				foreach ( $products as $id ) {
					$ids[] = $id;
				}
			}
		}
	}

	return $ids;


}