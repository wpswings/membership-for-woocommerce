<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link    https://makewebbetter.com/
 * @since   1.0.0
 * @package Membership_For_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Membership For WooCommerce
 * Plugin URI:        https://makewebbetter.com/product/membership-for-woocommerce/
 * Description:       Membership for WooCommerce plugin provides restrictions on access for any facility with recurring revenue to engage more customers.
 * Version:           1.0.0
 * Author:            makewebbetter
 * Author URI:        https://makewebbetter.com/
 * Text Domain:       membership-for-woocommerce
 * Domain Path:       /languages
 *
 * Requires at least: 4.6
 * Tested up to:      4.9.5
 *
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Define plugin constants.
 *
 * @since 1.0.0
 */
function define_membership_for_woocommerce_constants() {
	membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_VERSION', '1.0.0' );
	membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH', plugin_dir_path( __FILE__ ) );
	membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL', plugin_dir_url( __FILE__ ) );
	membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_SERVER_URL', 'https://makewebbetter.com' );
	membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_ITEM_REFERENCE', 'Membership For WooCommerce' );
}

/**
 * Define mwb-site update feature.
 *
 * @since 1.0.0
 */
function auto_update_membership_for_woocommerce() {
	 $mwb_mfw_license_key = get_option( 'mwb_mfw_license_key', '' );
	if ( ! defined( 'MEMBERSHIP_FOR_WOOCOMMERCE_SPECIAL_SECRET_KEY' ) ) {
		define( 'MEMBERSHIP_FOR_WOOCOMMERCE_SPECIAL_SECRET_KEY', '59f32ad2f20102.74284991' );
	}

	if ( ! defined( 'MEMBERSHIP_FOR_WOOCOMMERCE_LICENSE_SERVER_URL' ) ) {
		define( 'MEMBERSHIP_FOR_WOOCOMMERCE_LICENSE_SERVER_URL', 'https://makewebbetter.com' );
	}

	if ( ! defined( 'MEMBERSHIP_FOR_WOOCOMMERCE_ITEM_REFERENCE' ) ) {
		define( 'MEMBERSHIP_FOR_WOOCOMMERCE_ITEM_REFERENCE', 'Membership For WooCommerce' );
	}
	membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_BASE_FILE', __FILE__ );
	membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_LICENSE_KEY', $mwb_mfw_license_key );
	//include_once 'mwb-update.php';
}

/**
 * Callable function for defining plugin constants.
 *
 * @param String $key   Key for contant.
 * @param String $value value for contant.
 * @since 1.0.0
 */
function membership_for_woocommerce_constants( $key, $value ) {
	if ( ! defined( $key ) ) {

		define( $key, $value );
	}
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-membership-for-woocommerce-activator.php
 */
function activate_membership_for_woocommerce() {
	if ( ! wp_next_scheduled( 'mwb_mfw_check_license_daily' ) ) {
		wp_schedule_event( time(), 'daily', 'mwb_mfw_check_license_daily' );
	}

	include_once plugin_dir_path( __FILE__ ) . 'includes/class-membership-for-woocommerce-activator.php';
	Membership_For_Woocommerce_Activator::activate();
	Membership_For_Woocommerce_Activator::membership_for_woocommerce_activate();
	$mwb_mfw_active_plugin = get_option( 'mwb_all_plugins_active', false );
	if ( is_array( $mwb_mfw_active_plugin ) && ! empty( $mwb_mfw_active_plugin ) ) {
		$mwb_mfw_active_plugin['membership-for-woocommerce'] = array(
			'plugin_name' => __( 'Membership For WooCommerce', 'membership-for-woocommerce' ),
			'active' => '1',
		);
	} else {
		$mwb_mfw_active_plugin = array();
		$mwb_mfw_active_plugin['membership-for-woocommerce'] = array(
			'plugin_name' => __( 'Membership For WooCommerce', 'membership-for-woocommerce' ),
			'active' => '1',
		);
	}
	update_option( 'mwb_all_plugins_active', $mwb_mfw_active_plugin );
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-membership-for-woocommerce-deactivator.php
 */
function deactivate_membership_for_woocommerce() {
	include_once plugin_dir_path( __FILE__ ) . 'includes/class-membership-for-woocommerce-deactivator.php';
	Membership_For_Woocommerce_Deactivator::membership_for_woocommerce_deactivate();
	$mwb_mfw_deactive_plugin = get_option( 'mwb_all_plugins_active', false );
	if ( is_array( $mwb_mfw_deactive_plugin ) && ! empty( $mwb_mfw_deactive_plugin ) ) {
		foreach ( $mwb_mfw_deactive_plugin as $mwb_mfw_deactive_key => $mwb_mfw_deactive ) {
			if ( 'membership-for-woocommerce' === $mwb_mfw_deactive_key ) {
				$mwb_mfw_deactive_plugin[ $mwb_mfw_deactive_key ]['active'] = '0';
			}
		}
	}
	update_option( 'mwb_all_plugins_active', $mwb_mfw_deactive_plugin );
}

register_activation_hook( __FILE__, 'activate_membership_for_woocommerce' );
register_deactivation_hook( __FILE__, 'deactivate_membership_for_woocommerce' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-membership-for-woocommerce.php';


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
	 define_membership_for_woocommerce_constants();
	auto_update_membership_for_woocommerce();
	$mfw_plugin_standard = new Membership_For_Woocommerce();
	$mfw_plugin_standard->mfw_run();
	$GLOBALS['mfw_mwb_mfw_obj'] = $mfw_plugin_standard;

}
run_membership_for_woocommerce();


// Add settings link on plugin page.
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'membership_for_woocommerce_settings_link' );

/**
 * Settings link.
 *
 * @since 1.0.0
 * @param Array $links Settings link array.
 */
function membership_for_woocommerce_settings_link( $links ) {
	$my_link = array(
		'<a href="' . admin_url( 'admin.php?page=membership_for_woocommerce_menu' ) . '">' . __( 'Settings', 'membership-for-woocommerce' ) . '</a>',
	);
	return array_merge( $my_link, $links );
}

/**
 * Adding custom setting links at the plugin activation list.
 *
 * @param  array  $links_array      array containing the links to plugin.
 * @param  string $plugin_file_name plugin file name.
 * @return array
 */
function membership_for_woocommerce_custom_settings_at_plugin_tab( $links_array, $plugin_file_name ) {
	if ( strpos( $plugin_file_name, basename( __FILE__ ) ) ) {
		$links_array[] = '<a href="#" target="_blank"><img src="' . esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/Demo.svg" class="mwb-info-img" alt="Demo image">' . __( 'Demo', 'membership-for-woocommerce' ) . '</a>';
		$links_array[] = '<a href="#" target="_blank"><img src="' . esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/Documentation.svg" class="mwb-info-img" alt="documentation image">' . __( 'Documentation', 'membership-for-woocommerce' ) . '</a>';
		$links_array[] = '<a href="#" target="_blank"><img src="' . esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/Support.svg" class="mwb-info-img" alt="support image">' . __( 'Support', 'membership-for-woocommerce' ) . '</a>';
	}
	return $links_array;
}
add_filter( 'plugin_row_meta', 'membership_for_woocommerce_custom_settings_at_plugin_tab', 10, 2 );
