<?php
/**
 * Loads the WP Swings admin design system on our new pages, so Membership Mode and
 * Role-Based Membership match the rest of the brand (light-blue background, blue accent,
 * NunitoSans, pill buttons) instead of default WordPress grey.
 *
 * Reuses the plugin's own existing admin/css/wps-admin.css as-is (loaded, never edited) —
 * the existing admin_enqueue_scripts callback in admin/class-membership-for-woocommerce-admin.php
 * only loads it on that class's own screen ids, which our new pages don't match, so it has
 * to be enqueued again here for our pages specifically.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/mode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Admin_Assets.
 */
class Mfw_Admin_Assets {

	/**
	 * @since 3.2.0
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'maybe_enqueue' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'maybe_enqueue_public' ) );
	}

	/**
	 * @since 3.2.0
	 */
	public static function maybe_enqueue() {

		if ( ! isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$page = sanitize_key( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$our_pages = array( Mfw_Onboarding_Mode_Screen::PAGE_SLUG, Mfw_Role_Based_Admin::PAGE_SLUG );

		if ( ! in_array( $page, $our_pages, true ) ) {
			return;
		}

		if ( Mfw_Role_Based_Admin::PAGE_SLUG === $page ) {
			// Custom admin pages don't get the block/classic editor assets by default —
			// needed here for the wp_editor() notice field ("design" control).
			wp_enqueue_editor();
		}

		wp_enqueue_style(
			'wps-admin-shared-css',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/css/wps-admin.min.css',
			array(),
			MEMBERSHIP_FOR_WOOCOMMERCE_VERSION,
			'all'
		);

		wp_enqueue_style(
			'mfw-role-based-admin-css',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'includes/role-based/assets/css/mfw-role-based-admin.css',
			array( 'wps-admin-shared-css' ),
			MEMBERSHIP_FOR_WOOCOMMERCE_VERSION,
			'all'
		);
	}

	/**
	 * The role-based module's own shortcodes/hooks (login page, self-signup form, My Account
	 * "Membership" tab, restricted-content notices) can appear on any front-end page an admin
	 * chooses, not a fixed set of screen ids like the admin pages above — so unlike
	 * maybe_enqueue(), this loads on every front-end request while role mode is active rather
	 * than matching against specific pages. The stylesheet itself is fully scoped to
	 * .mfw-role-* classes, so loading it unconditionally never affects unrelated theme markup.
	 *
	 * @since 3.5.0
	 */
	public static function maybe_enqueue_public() {

		if ( is_admin() || ! Mfw_Mode_Controller::is_role_mode() ) {
			return;
		}

		wp_enqueue_style(
			'mfw-role-based-public-css',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'includes/role-based/assets/css/mfw-role-based-public.css',
			array(),
			MEMBERSHIP_FOR_WOOCOMMERCE_VERSION,
			'all'
		);
	}
}
