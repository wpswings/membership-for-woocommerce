<?php
/**
 * Hides the inactive mode's admin configuration surfaces so the two flows never
 * appear side by side ("no hybrid mode"). Purely a navigation-visibility concern —
 * it never touches the classes/CPTs it hides, and never affects data access.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/mode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Admin_Menu_Visibility.
 */
class Mfw_Admin_Menu_Visibility {

	/**
	 * The purchase-flow's own settings/dashboard submenu slug
	 * (registered via Membership_For_Woocommerce_Admin::mfw_admin_submenu_page()).
	 */
	const PURCHASE_SETTINGS_SLUG = 'membership_for_woocommerce_menu';

	/**
	 * The purchase-flow's "Membership Plans" CPT top-level menu slug.
	 * The "Members" CPT nests under it automatically, so hiding this one slug
	 * hides both.
	 */
	const PURCHASE_PLANS_MENU_SLUG = 'edit.php?post_type=wps_cpt_membership';

	/**
	 * @since 3.1.3
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'hide_inactive_mode_menus' ), 999 );
		add_action( 'admin_head-post.php', array( __CLASS__, 'maybe_hide_purchase_flow_product_field' ) );
		add_action( 'admin_head-post-new.php', array( __CLASS__, 'maybe_hide_purchase_flow_product_field' ) );
	}

	/**
	 * Runs late on admin_menu so every plugin/CPT has already registered its menu items.
	 *
	 * @since 3.1.3
	 */
	public static function hide_inactive_mode_menus() {

		if ( Mfw_Mode_Controller::is_role_mode() ) {
			remove_submenu_page( 'wps-plugins', self::PURCHASE_SETTINGS_SLUG );
			remove_menu_page( self::PURCHASE_PLANS_MENU_SLUG );
		}

		// Purchase mode is the default; the role-based module only registers its own
		// menu items when it is active (see Mfw_Role_Based_Admin), so there is nothing
		// to hide on its side while purchase mode is active.
	}

	/**
	 * The purchase-flow adds two pieces of markup to every WooCommerce product edit screen
	 * that a menu `remove_*_page()` can't hide, since they're embedded in the product screen
	 * by the (untouched) legacy admin class:
	 * - the "Attach Membership" Product Data tab + its panel (`#wps_attach_membership`,
	 *   `.attach-membership_tab`), registered via `woocommerce_product_data_tabs`;
	 * - the "Select Plan" dropdown inside that panel.
	 * While role mode is active they have nothing useful to show (no purchase-based
	 * Membership Plans exist to pick from in a role-based-only setup) and just clutter the
	 * screen next to our own restriction metabox, so both are hidden with scoped CSS here —
	 * the legacy file/markup itself is never touched, only its visibility.
	 *
	 * @since 3.4.0
	 */
	public static function maybe_hide_purchase_flow_product_field() {

		$screen = get_current_screen();

		if ( ! $screen || 'product' !== $screen->post_type || ! Mfw_Mode_Controller::is_role_mode() ) {
			return;
		}

		echo '<style>#wps_attach_membership,.attach-membership_tab{display:none !important;}</style>';
	}
}
