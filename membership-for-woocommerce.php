<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link    https://makewebbetter.com
 * @since   1.0.0
 * @package Membership_For_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Membership For Woocommerce
 * Plugin URI:        https://makewebbetter.com/product/membership-for-woocommerce
 * Description:       Membership for WooCommerce plugin provides restrictions on access for any facility with recurring revenue to engage more customers.
 * Version:           1.0.0
 * Author:            MakeWebBetter
 * Author URI:        https://makewebbetter.com
 * Requires at least:    4.0
 * Tested up to:         5.6.2
 * WC requires at least: 3.0.0
 * WC tested up to:      5.0.0
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       membership-for-woocommerce
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Function to check for plugin activation.
 *
 * @param string $plugin_slug is the slug of the plugin.
 */
function mwb_membership_for_woo_is_plugin_active( $plugin_slug = '' ) {
	if ( empty( $plugin_slug ) ) {

		return;
	}

	$active_plugins = (array) get_option( 'active_plugins', array() );

	if ( is_multisite() ) {

		$active_plugins = array_merge( $active_plugins, get_option( 'active_sitewide_plugins', array() ) );
	}

	return in_array( $plugin_slug, $active_plugins, true ) || array_key_exists( $plugin_slug, $active_plugins );
}

/**
 * Checking whether the dependent plugin is active or not.
 */
function mwb_membership_for_woo_plugin_activation() {
	$activation['status']  = true;
	$activation['message'] = '';

	// If dependent plugin is not active.
	if ( ! mwb_membership_for_woo_is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

		$activation['status']  = false;
		$activation['message'] = 'woo_inactive';
	}

	return $activation;

}

// The following code runs during the activation of the plugin.
$mwb_membership_for_woo_plugin_activation = mwb_membership_for_woo_plugin_activation();

if ( true === $mwb_membership_for_woo_plugin_activation['status'] ) {

	// Define all the necessary details of the plugin.

	define( 'MEMBERSHIP_FOR_WOOCOMMERCE_URL', plugin_dir_url( __FILE__ ) ); // Plugin URL directory path.

	define( 'MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH', plugin_dir_path( __FILE__ ) ); // Plugin filesystem directory path.

	define( 'MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH_ADMIN', plugin_dir_path( __FILE__ ) . 'admin/partials/' ); // Plugin filesystem directory path to admin templates.

	define( 'MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH_PUBLIC', plugin_dir_path( __FILE__ ) . 'public/partials/' ); // PLugin filesystem directory path to public templates.

	/**
	 * Currently plugin version.
	 * Start at version 1.0.0 and use SemVer - https://semver.org
	 * Rename this for your plugin and update it as you release new versions.
	 */
	define( 'MEMBERSHIP_FOR_WOOCOMMERCE_VERSION', '1.0.0' );

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-membership-for-woocommerce-activator.php
	 */
	function activate_membership_for_woocommerce() {
		include_once plugin_dir_path( __FILE__ ) . 'includes/class-membership-for-woocommerce-activator.php';
		Membership_For_Woocommerce_Activator::activate();
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-membership-for-woocommerce-deactivator.php
	 */
	function deactivate_membership_for_woocommerce() {
		include_once plugin_dir_path( __FILE__ ) . 'includes/class-membership-for-woocommerce-deactivator.php';
		Membership_For_Woocommerce_Deactivator::deactivate();
	}

	// Add settings link in plugin action links.
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mwb_membership_for_woo_plugin_settings_link' );

	/**
	 * Add settings link callback.
	 *
	 * @since 1.0.0
	 * @param string $links link to the admin area of the plugin.
	 */
	function mwb_membership_for_woo_plugin_settings_link( $links ) {

		$plugin_links = array(
			'<a href="' . admin_url( 'edit.php?post_type=mwb_cpt_membership' ) . '">' . esc_html__( 'Settings', 'membership-for-woocommerce' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	register_activation_hook( __FILE__, 'activate_membership_for_woocommerce' ); // plugin activation hook.

	register_deactivation_hook( __FILE__, 'deactivate_membership_for_woocommerce' ); // plugin activation hook.

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	include plugin_dir_path( __FILE__ ) . 'includes/class-membership-for-woocommerce.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since 1.0.0
	 */
	function run_membership_for_woocommerce() {

		$plugin = new Membership_For_Woocommerce();
		$plugin->run();

	}

	run_membership_for_woocommerce();

} else {

	// Deactivate the plugin if Woocommerce not active.
	add_action( 'admin_init', 'mwb_membership_for_woo_plugin_activation_failure' );

	/**
	 * Deactivate the plugin.
	 */
	function mwb_membership_for_woo_plugin_activation_failure() {

		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	// Add admin error notice.
	add_action( 'admin_notices', 'mwb_membership_for_woo_plugin_activation_notice' );

	/**
	 * This function displays plugin activation error notices.
	 */
	function mwb_membership_for_woo_plugin_activation_notice() {

		global $mwb_membership_for_woo_plugin_activation;

		// To hide Plugin activated notice.
		unset( $_GET['activate'] );

		?>

		<?php if ( 'woo_inactive' === $mwb_membership_for_woo_plugin_activation['message'] ) { ?>

			<div class="notice notice-error is-dismissible">
				<p><strong><?php esc_html_e( 'WooCommerce', 'membership-for-woocommerce' ); ?></strong><?php esc_html_e( ' is not activated, Please activate WooCommerce first to activate ', 'membership-for-woocommerce' ); ?><strong><?php esc_html_e( 'Membership for WooCommerce', 'membership-for-woocommerce' ); ?></strong><?php esc_html_e( '.', 'membership-for-woocommerce' ); ?></p>
			</div>

			<?php
		}
	}
}

?>
