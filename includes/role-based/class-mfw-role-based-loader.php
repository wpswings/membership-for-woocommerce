<?php
/**
 * Bootstraps the role-based membership module's own hooks (WPS-7877/WPS-7881).
 *
 * Everything in this module self-gates on Mfw_Mode_Controller::is_role_mode(), so its
 * hooks simply do nothing while purchase mode is active — no shared restriction logic,
 * no interference with the purchase-based flow either way.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Loader.
 */
class Mfw_Role_Based_Loader {

	/**
	 * @since 3.1.3
	 */
	public static function init() {

		Mfw_Role_Based_Notifications::init();

		if ( is_admin() ) {
			Mfw_Role_Based_Admin::init();
			Mfw_Role_Based_User_Assignment::init();
			Mfw_Role_Based_Dashboard_Widget::init();
		}

		if ( ! Mfw_Mode_Controller::is_role_mode() ) {
			return;
		}

		// These must run on every request type — REST route registration, cron
		// scheduling/handling, the unsubscribe-link handler (on `init`, so it must fire
		// for any visitor, logged in or not), and webhook/privacy hook registration are
		// not admin- or frontend-specific concerns.
		Mfw_Role_Based_Expiry::init();
		Mfw_Role_Based_Rest_Api::init();
		Mfw_Role_Based_Webhooks::init();
		Mfw_Role_Based_Privacy::init();
		Mfw_Role_Based_Campaigns::init();
		// Order-status hooks can fire from an admin order edit, a payment gateway's
		// webhook/API callback, or a frontend checkout — never assume one context.
		Mfw_Role_Based_Paid_Access::init();
		Mfw_Role_Based_Tiers::init();

		// Deliberately NOT an `elseif` against the is_admin() branch above: both
		// admin-ajax.php AND admin-post.php live under wp-admin, so is_admin() is true for
		// both — but Public::init() registers handlers that legitimately need to run there:
		// WooCommerce's own AJAX endpoints (add-to-cart, update-cart) need our
		// product-restriction/discount hooks, and several of our own frontend-facing
		// actions (self-service registration/level-switching, campaign-preference saving,
		// login) are wired as admin_post_*/admin_post_nopriv_* handlers, which only ever
		// fire via an admin-post.php request. If Public::init() only ran on the "else"
		// branch, none of that would ever register. Running both Admin::init() and
		// Public::init() on the same request is safe either way: only the one hook
		// relevant to whichever single action actually fired ever runs.
		$is_post_or_ajax_gateway = wp_doing_ajax() || ( isset( $GLOBALS['pagenow'] ) && 'admin-post.php' === $GLOBALS['pagenow'] );

		if ( ! is_admin() || $is_post_or_ajax_gateway ) {
			Mfw_Role_Based_Public::init();
		}
	}
}
