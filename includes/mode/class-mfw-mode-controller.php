<?php
/**
 * Central mode controller for the dual-flow (purchase-based / role-based) membership
 * architecture introduced by WPS-7875.
 *
 * This is the single integration point into the plugin's existing bootstrap: everything
 * under includes/mode/ and includes/role-based/ is required from here, so no other
 * pre-existing file needs to know about the new flow.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/mode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reads/writes the active membership mode and boots the mode-aware modules.
 */
class Mfw_Mode_Controller {

	/**
	 * Option key storing the active mode. Absent option = existing/upgrading install = purchase mode.
	 */
	const OPTION_KEY = 'wps_membership_mode';

	const MODE_PURCHASE = 'purchase';

	const MODE_ROLE = 'role';

	/**
	 * Guards against double-boot if init() is ever called more than once.
	 *
	 * @var bool
	 */
	private static $booted = false;

	/**
	 * Require the new-module files and boot the mode-aware pieces.
	 *
	 * @since 3.2.0
	 */
	public static function init() {

		if ( self::$booted ) {
			return;
		}

		self::$booted = true;

		self::load_files();

		if ( is_admin() ) {
			Mfw_Admin_Menu_Visibility::init();
			Mfw_Onboarding_Mode_Screen::init();
			Mfw_Role_Based_Mode_Switch::init();
		}

		// Registers both an admin_enqueue_scripts and a wp_enqueue_scripts callback —
		// needed on both sides (admin pages for the settings screens, front-end for the
		// role-based module's own shortcodes/hooks), so it boots outside the is_admin()
		// block above; each callback still gates itself on the right context internally.
		Mfw_Admin_Assets::init();

		// The role-based module gates its own hook registration internally on is_role_mode().
		Mfw_Role_Based_Loader::init();
	}

	/**
	 * Require every file that makes up the new dual-flow architecture.
	 *
	 * @since 3.2.0
	 */
	private static function load_files() {

		$mode_dir = plugin_dir_path( __FILE__ );

		require_once $mode_dir . 'class-mfw-admin-menu-visibility.php';
		require_once $mode_dir . 'class-mfw-onboarding-mode-screen.php';
		require_once $mode_dir . 'class-mfw-admin-assets.php';

		$role_based_dir = plugin_dir_path( dirname( __FILE__ ) ) . 'role-based/';

		require_once $role_based_dir . 'data/class-mfw-role-based-db.php';
		require_once $role_based_dir . 'data/class-mfw-role-based-repository.php';
		require_once $role_based_dir . 'class-mfw-role-based-email-template.php';
		require_once $role_based_dir . 'notifications/class-mfw-role-based-notifications.php';
		require_once $role_based_dir . 'admin/class-mfw-role-based-csv.php';
		require_once $role_based_dir . 'admin/class-mfw-role-based-roles-manager.php';
		require_once $role_based_dir . 'admin/class-mfw-role-based-admin.php';
		require_once $role_based_dir . 'admin/class-mfw-role-based-user-assignment.php';
		require_once $role_based_dir . 'public/class-mfw-role-based-login.php';
		require_once $role_based_dir . 'public/class-mfw-role-based-self-service.php';
		require_once $role_based_dir . 'class-mfw-role-based-paid-access.php';
		require_once $role_based_dir . 'class-mfw-role-based-tiers.php';
		require_once $role_based_dir . 'public/class-mfw-role-based-public.php';
		require_once $role_based_dir . 'class-mfw-role-based-pricing.php';
		require_once $role_based_dir . 'class-mfw-role-based-campaign-tracking.php';
		require_once $role_based_dir . 'class-mfw-role-based-campaigns.php';
		require_once $role_based_dir . 'class-mfw-role-based-expiry.php';
		require_once $role_based_dir . 'class-mfw-role-based-dashboard-widget.php';
		require_once $role_based_dir . 'class-mfw-role-based-rest-api.php';
		require_once $role_based_dir . 'class-mfw-role-based-webhooks.php';
		require_once $role_based_dir . 'class-mfw-role-based-privacy.php';
		require_once $role_based_dir . 'class-mfw-role-based-mode-switch.php';
		require_once $role_based_dir . 'class-mfw-role-based-loader.php';
	}

	/**
	 * Get the active mode, defaulting new/upgrading installs to purchase mode.
	 *
	 * @since 3.2.0
	 * @return string self::MODE_PURCHASE|self::MODE_ROLE
	 */
	public static function get_mode() {

		$mode = get_option( self::OPTION_KEY, self::MODE_PURCHASE );

		if ( ! in_array( $mode, array( self::MODE_PURCHASE, self::MODE_ROLE ), true ) ) {
			return self::MODE_PURCHASE;
		}

		return $mode;
	}

	/**
	 * Whether the admin has ever explicitly chosen a mode (activation wizard or settings).
	 *
	 * @since 3.2.0
	 * @return bool
	 */
	public static function is_mode_selected() {
		return false !== get_option( self::OPTION_KEY, false );
	}

	/**
	 * @since 3.2.0
	 * @return bool
	 */
	public static function is_purchase_mode() {
		return self::MODE_PURCHASE === self::get_mode();
	}

	/**
	 * @since 3.2.0
	 * @return bool
	 */
	public static function is_role_mode() {
		return self::MODE_ROLE === self::get_mode();
	}

	/**
	 * Switch the active mode. Never touches user meta, post meta, or any DB rows —
	 * switching is intentionally non-destructive: it only changes which admin
	 * configuration surfaces are shown and which module's hooks run.
	 *
	 * @since 3.2.0
	 * @param string $mode self::MODE_PURCHASE|self::MODE_ROLE.
	 * @return bool
	 */
	public static function set_mode( $mode ) {

		if ( ! in_array( $mode, array( self::MODE_PURCHASE, self::MODE_ROLE ), true ) ) {
			return false;
		}

		$previous_mode = self::get_mode();

		$updated = update_option( self::OPTION_KEY, $mode );

		if ( $updated && $previous_mode !== $mode ) {
			/**
			 * Fires right after the active membership mode changes.
			 *
			 * @since 3.2.0
			 * @param string $mode          The newly active mode.
			 * @param string $previous_mode The mode that was active before the switch.
			 */
			do_action( 'mfw_membership_mode_switched', $mode, $previous_mode );

			// Called directly (rather than via the action above) because the class is
			// always require'd regardless of mode, but its own hook registration only runs
			// once role mode is already active — which, mid-request during this very
			// switch, it isn't yet.
			if ( self::MODE_ROLE === $mode && class_exists( 'Mfw_Role_Based_Login' ) ) {
				Mfw_Role_Based_Login::maybe_create_login_page();
			}
		}

		return $updated;
	}
}
