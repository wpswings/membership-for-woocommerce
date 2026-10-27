<?php
/**
 * Charging for protected content ("Buy Access") — the one thing the reference "Members"
 * plugin leaves to its paid MemberPress upsell (its free-plugin upgrade banner literally
 * says "to unlock more features, consider MemberPress"; there is no priced-content flow in
 * the free plugin at all). A level with a `price` > 0 gets an auto-managed, hidden
 * WooCommerce product; visitors buy it through a normal WooCommerce checkout, and the level
 * is assigned automatically once the order is paid — no separate payment gateway or cart
 * system invented here, just WooCommerce's own.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Paid_Access.
 */
class Mfw_Role_Based_Paid_Access {

	const BUY_QUERY_VAR = 'mfw_buy_level';

	/**
	 * Order meta key: level ids this order actually assigned, so a refund/cancellation
	 * revokes exactly those (never a level the customer already held some other way).
	 */
	const GRANTED_LEVELS_META_KEY = '_mfw_role_based_granted_level_ids';

	/**
	 * @since 3.4.0
	 */
	public static function init() {
		add_action( 'template_redirect', array( __CLASS__, 'maybe_handle_buy_click' ) );
		add_action( 'woocommerce_order_status_completed', array( __CLASS__, 'grant_access_from_order' ) );
		add_action( 'woocommerce_order_status_processing', array( __CLASS__, 'grant_access_from_order' ) );
		add_action( 'woocommerce_order_status_refunded', array( __CLASS__, 'revoke_access_from_order' ) );
		add_action( 'woocommerce_order_status_cancelled', array( __CLASS__, 'revoke_access_from_order' ) );
		add_action( 'woocommerce_order_status_failed', array( __CLASS__, 'revoke_access_from_order' ) );
	}

	/**
	 * Inactive levels, and levels granting admin-level capabilities, are never purchasable,
	 * so they can't be bought via a Buy Access link.
	 *
	 * @since 3.4.0
	 * @param array $level Level row.
	 * @return bool
	 */
	public static function is_purchasable( $level ) {
		return 'active' === ( $level['status'] ?? 'active' )
			&& ! Mfw_Role_Based_Repository::level_has_unsafe_public_capabilities( $level )
			&& (float) $level['price'] > 0 && ! empty( $level['wc_product_id'] ) && 'publish' === get_post_status( $level['wc_product_id'] );
	}

	/**
	 * A "Buy Access" link that clears the cart, adds this level's product, and goes to
	 * checkout — routed through our own `template_redirect` handler (below) rather than a
	 * raw `?add-to-cart=` URL, so a logged-out visitor is sent to log in/register first
	 * (WooCommerce checkout can't assign a role-membership level to "nobody").
	 *
	 * @since 3.4.0
	 * @param int $level_id Level id.
	 * @return string
	 */
	public static function get_buy_url( $level_id ) {
		return add_query_arg( self::BUY_QUERY_VAR, absint( $level_id ), home_url( '/' ) );
	}

	/**
	 * @since 3.4.0
	 */
	public static function maybe_handle_buy_click() {

		if ( empty( $_GET[ self::BUY_QUERY_VAR ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$level_id = absint( $_GET[ self::BUY_QUERY_VAR ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$level    = Mfw_Role_Based_Repository::get_level( $level_id );

		if ( ! $level || ! self::is_purchasable( $level ) ) {
			wp_safe_redirect( home_url( '/' ) );
			exit;
		}

		if ( ! is_user_logged_in() ) {
			// Send to the membership login page (which also handles registration —
			// Mfw_Role_Based_Self_Service's public signup form) with a redirect back to
			// this same buy link, so checkout has an actual account to assign the level to.
			$login_url = Mfw_Role_Based_Login::get_login_page_url();
			wp_safe_redirect( add_query_arg( 'redirect_to', rawurlencode( self::get_buy_url( $level_id ) ), $login_url ) );
			exit;
		}

		// Already holding this level? Nothing to buy.
		if ( in_array( $level_id, Mfw_Role_Based_Repository::get_user_level_ids( get_current_user_id() ), true ) ) {
			$redirect = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'role-membership' ) : home_url( '/' );
			wp_safe_redirect( $redirect );
			exit;
		}

		WC()->cart->empty_cart();
		WC()->cart->add_to_cart( $level['wc_product_id'] );

		wp_safe_redirect( wc_get_checkout_url() );
		exit;
	}

	/**
	 * Assigns the matching level(s) once an order carrying a level's linked product is
	 * paid. Resolves a guest order to a real (existing or newly created) user by billing
	 * email, via WooCommerce's own wc_create_new_customer() helper, so "buy access" works
	 * end-to-end even without a separate account-creation step first.
	 *
	 * @since 3.4.0
	 * @param int $order_id Order id.
	 */
	public static function grant_access_from_order( $order_id ) {

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		$matched_levels = self::get_order_levels( $order );

		if ( empty( $matched_levels ) ) {
			return;
		}

		$user_id = $order->get_customer_id();

		if ( ! $user_id ) {
			$email = $order->get_billing_email();
			$user  = $email ? get_user_by( 'email', $email ) : false;

			if ( $user ) {
				$user_id = $user->ID;
			} elseif ( $email ) {
				$created = wc_create_new_customer( $email, '', '', array( 'display_name' => $order->get_formatted_billing_full_name() ) );
				if ( ! is_wp_error( $created ) ) {
					$user_id = $created;
					$order->set_customer_id( $user_id );
					$order->save();
				}
			}
		}

		if ( ! $user_id ) {
			return; // No email to work with — can't assign access to nobody.
		}

		$granted = array_map( 'absint', (array) $order->get_meta( self::GRANTED_LEVELS_META_KEY ) );

		foreach ( $matched_levels as $level ) {
			// The access product is reachable via ?add-to-cart= too, so re-check here.
			if ( Mfw_Role_Based_Repository::level_has_unsafe_public_capabilities( $level ) ) {
				continue;
			}
			if ( ! in_array( (int) $level['id'], Mfw_Role_Based_Repository::get_user_level_ids( $user_id ), true ) ) {
				if ( Mfw_Role_Based_Repository::assign_level_to_user( $user_id, $level['id'], 0 ) ) {
					$granted[] = (int) $level['id'];
				}
			}
		}

		$order->update_meta_data( self::GRANTED_LEVELS_META_KEY, array_values( array_unique( array_filter( $granted ) ) ) );
		$order->save();
	}

	/**
	 * Takes paid access back when its order is refunded, cancelled or fails. Only the
	 * levels this order granted are revoked; orders from before that was recorded fall back
	 * to every level whose product is in the order.
	 *
	 * @since 3.5.0
	 * @param int $order_id Order id.
	 */
	public static function revoke_access_from_order( $order_id ) {

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		$user_id = $order->get_customer_id();
		if ( ! $user_id ) {
			return;
		}

		$granted = $order->get_meta( self::GRANTED_LEVELS_META_KEY );

		if ( is_array( $granted ) ) {
			$level_ids = array_map( 'absint', $granted );
		} else {
			$level_ids = array_map( 'absint', wp_list_pluck( self::get_order_levels( $order ), 'id' ) );
		}

		$held = Mfw_Role_Based_Repository::get_user_level_ids( $user_id );

		foreach ( $level_ids as $level_id ) {
			if ( in_array( $level_id, $held, true ) ) {
				Mfw_Role_Based_Repository::revoke_level_from_user( $user_id, $level_id, 0 );
			}
		}

		$order->update_meta_data( self::GRANTED_LEVELS_META_KEY, array() );
		$order->save();
	}

	/**
	 * @since 3.5.0
	 * @param WC_Order $order Order.
	 * @return array[] Levels whose linked access product is in the order.
	 */
	private static function get_order_levels( $order ) {

		$product_ids = array();
		foreach ( $order->get_items() as $item ) {
			$product_ids[] = (int) $item->get_product_id();
		}

		if ( empty( $product_ids ) ) {
			return array();
		}

		return array_filter(
			Mfw_Role_Based_Repository::get_levels(),
			function ( $level ) use ( $product_ids ) {
				return ! empty( $level['wc_product_id'] ) && in_array( (int) $level['wc_product_id'], $product_ids, true );
			}
		);
	}
}
