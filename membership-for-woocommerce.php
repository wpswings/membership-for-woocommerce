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
 * Plugin URI:        https://wpswings.com/product/membership-for-woocommerce/
 * Description:       Membership for WooCommerce plugin provides restrictions on access for any facility with recurring revenue to engage more customers.
 * Version:           2.0.2
 * Author:            WP Swings
 * Author URI:        https://wpswings.com/?utm_source=wpswings-official&utm_medium=membership-org-backend&utm_campaign=official
 * Text Domain:       membership-for-woocommerce
 * Domain Path:       /languages
 *
 * Requires at least: 5.0
 * Tested up to:      5.8.2
 * WC requires at least: 4.0
 * WC tested up to:   6.1.0
 *
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Function to check for plugin activation.
 *
 * @param string $plugin_slug is the slug of the plugin.
 */
function mwb_membership_is_plugin_active( $plugin_slug = '' ) {
	if ( empty( $plugin_slug ) ) {

		return;
	}

	$active_plugins = (array) get_option( 'active_plugins', array() );

	if ( is_multisite() ) {

		$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}

	return in_array( $plugin_slug, $active_plugins ) || array_key_exists( $plugin_slug, $active_plugins );
}

/**
 * Checking whether the dependent plugin is active or not.
 */
function mwb_membership_plugin_activation() {
	$activation['status']  = true;
	$activation['message'] = '';

	// If dependent plugin is not active.
	if ( ! mwb_membership_is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

		$activation['status']  = false;
		$activation['message'] = 'woo_inactive';
	}

	return $activation;

}

// The following code runs during the activation of the plugin.
$mwb_membership_plugin_activation = mwb_membership_plugin_activation();

if ( true === $mwb_membership_plugin_activation['status'] ) {

	/**
	 * Define plugin constants.
	 *
	 * @since 1.0.0
	 */
	function define_membership_for_woocommerce_constants() {
		membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_VERSION', '2.0.2' );
		membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH', plugin_dir_path( __FILE__ ) );
		membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL', plugin_dir_url( __FILE__ ) );
		membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_SERVER_URL', 'https://wpswings.com/' );
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
			define( 'MEMBERSHIP_FOR_WOOCOMMERCE_LICENSE_SERVER_URL', 'https://wpswings.com/' );
		}

		if ( ! defined( 'MEMBERSHIP_FOR_WOOCOMMERCE_ITEM_REFERENCE' ) ) {
			define( 'MEMBERSHIP_FOR_WOOCOMMERCE_ITEM_REFERENCE', 'Membership For WooCommerce' );
		}
		membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_BASE_FILE', __FILE__ );
		membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_LICENSE_KEY', $mwb_mfw_license_key );

	}

	if ( ! function_exists( 'mwb_mfw_standard_check_multistep' ) ) {
		/**
		 * Function to check multistep function.
		 *
		 * @return bool
		 */
		function mwb_mfw_standard_check_multistep() {
			$bool = false;
			$mwb_standard_check = get_option( 'mfw_mfw_plugin_standard_multistep_done', false );

			if ( ! empty( $mwb_standard_check ) ) {
				$bool = true;
			}
			$bool = apply_filters( 'mwb_standard_multistep_done', $bool );
			return $bool;
		}
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
	 *
	 * @param [type] $network_wide is for multiple sites.
	 * @return void
	 */
	function activate_membership_for_woocommerce( $network_wide ) {

		include_once plugin_dir_path( __FILE__ ) . 'includes/class-membership-for-woocommerce-activator.php';
		Membership_For_Woocommerce_Activator::activate( $network_wide );
		Membership_For_Woocommerce_Activator::membership_for_woocommerce_activate( $network_wide );

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

	add_action( 'admin_enqueue_scripts', 'mfw_admin_enqueue_styles' );

	/**
	 * Schedule hook for member expiry.
	 *
	 * @return void
	 */
	function mwb_membership_schedule_expiry() {
		if ( false === as_next_scheduled_action( 'mwb_membership_expiry_check_action' ) ) {
			as_schedule_recurring_action( strtotime( 'tomorrow' ), DAY_IN_SECONDS, 'mwb_membership_expiry_check_action' );
		}
	}

	/**
	 * Schedule action for expiry.
	 *
	 * @return void
	 */
	function mwb_membership_schedule_action_expiry_check() {
		if ( class_exists( 'Membership_For_Woocommerce_Public' ) ) {
			$mfw_plugin_public = new Membership_For_Woocommerce_Public( '', '' );
			$mfw_plugin_public->mwb_membership_cron_expiry_check();
		}
	}

	add_action( 'init', 'mwb_membership_schedule_expiry' );

	add_action( 'mwb_membership_expiry_check_action', 'mwb_membership_schedule_action_expiry_check' );

		/**
		 * Schedule hook for member expiry.
		 *
		 * @return void
		 */
	function mwb_membership_schedule_hook() {
		// Schedule cron for checking of membership expiration on daily basis.
		if ( ! wp_next_scheduled( 'mwb_membership_expiry_check' ) ) {
			wp_schedule_event( time(), 'daily', 'mwb_membership_expiry_check' );
		}

	}
	add_action( 'init', 'mwb_membership_schedule_hook' );

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @name mfw_admin_enqueue_styles.
	 */
	function mfw_admin_enqueue_styles() {
		$screen = get_current_screen();

		if ( isset( $screen->id ) || isset( $screen->post_type ) ) {

			$screen = get_current_screen();

			if ( 'product' != $screen->id ) {
				if ( isset( $screen->id ) && 'wp-swings_page_membership_for_woocommerce_menu' === $screen->id || 'plugins' == $screen->id ) {
					wp_enqueue_style( 'admin-css', plugin_dir_url( __FILE__ ) . '/admin/css/membership-for-woocommerce-admin.css', array(), '1.0.0', false );

				}
			}
		}
	}


// Upgrade notice.
add_action( 'after_plugin_row_' . plugin_basename( __FILE__ ), 'mfw_upgrade_notice', 0, 3 );

/**
* Displays notice to upgrade to membership.
*
* @param string $plugin_file Path to the plugin file relative to the plugins directory.
* @param array $plugin_data An array of plugin data.
* @param string $status Status filter currently applied to the plugin list.
*/
function mfw_upgrade_notice( $plugin_file, $plugin_data, $status ) {

?>

<tr class="plugin-update-tr active notice-warning notice-alt">
	<td colspan="4" class="plugin-update colspanchange">
		<div class="notice notice-success inline update-message notice-alt">
			<div class='wps-notice-title wps-notice-section'>
				<p><strong>IMPORTANT NOTICE:</strong></p>
			</div>
			<div class='wps-notice-content wps-notice-section'>
				<p>From this update <strong>Version 2.0.2</strong> onwards, the plugin and its support will be handled by <strong>WP Swings</strong>.</p><p><strong>WP Swings</strong> is just our improvised and rebranded version with all quality solutions and help being the same, so no worries at your end.
				Please connect with us for all setup, support, and update related queries without hesitation.</p>
			</div>
		</div>
	</td>
</tr>
<style>
	.wps-notice-section > p:before {
		content: none;
	}
</style>

<?php

}// Upgrade notice.

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
		if ( ! is_plugin_active( 'membership-for-woocommerce-pro/membership-for-woocommerce-pro.php' ) ) {

			$my_link['goPro'] = '<a class="mwb-wpr-go-pro" target="_blank" href="https://wpswings.com/product/membership-for-woocommerce-pro/?utm_source=wpswings-membership-pro&utm_medium=membership-org-backend&utm_campaign=go-pro">' . esc_html__( 'GO PRO', 'membership-for-woocommerce' ) . '</a>';
		}
		return array_merge( $my_link, $links );
	}
	if ( ! function_exists( 'mwb_membership_check_plugin_enable' ) ) {
		/**
		 * This function is used to check plugin is enable.
		 *
		 * @name mwb_membership_check_plugin_enable
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
	 * Adding custom setting links at the plugin activation list.
	 *
	 * @param  array  $links_array      array containing the links to plugin.
	 * @param  string $plugin_file_name plugin file name.
	 * @return array
	 */
	function membership_for_woocommerce_custom_settings_at_plugin_tab( $links_array, $plugin_file_name ) {
		if ( strpos( $plugin_file_name, basename( __FILE__ ) ) ) {
			$links_array[] = '<a href=" https://demo.wpswings.com/membership-for-woocommerce/?utm_source=wpswings-membership-demo&utm_medium=membership-org-backend&utm_campaign=demo" target="_blank"><img src="' . esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/Demo.svg" class="mwb-info-img" alt="Demo image">' . __( 'Demo', 'membership-for-woocommerce' ) . '</a>';
			$links_array[] = '<a href=" https://docs.wpswings.com/membership-for-woocommerce/?utm_source=wpswings-membership-doc&utm_medium=membership-org-backend&utm_campaign=documentation" target="_blank"><img src="' . esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/Documentation.svg" class="mwb-info-img" alt="documentation image">' . __( 'Documentation', 'membership-for-woocommerce' ) . '</a>';
			$links_array[] = '<a href="https://wpswings.com/submit-query/?utm_source=wpswings-membership-support&utm_medium=membership-org-backend&utm_campaign=support" target="_blank"><img src="' . esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/Support.svg" class="mwb-info-img" alt="support image">' . __( 'Support', 'membership-for-woocommerce' ) . '</a>';
		}
		return $links_array;
	}
	add_filter( 'plugin_row_meta', 'membership_for_woocommerce_custom_settings_at_plugin_tab', 10, 2 );

	add_action( 'activated_plugin', 'membership_for_woocommerce_redirect_on_settings' );
	if ( ! function_exists( 'membership_for_woocommerce_redirect_on_settings' ) ) {
		/**
		 * Redirect plugin as plugin get activated function.
		 *
		 * @param [type] $plugin is the currenct plugin.
		 * @return void
		 */
		function membership_for_woocommerce_redirect_on_settings( $plugin ) {
			if ( plugin_basename( __FILE__ ) === $plugin ) {
				$general_settings_url = admin_url( 'admin.php?page=membership_for_woocommerce_menu' );
				wp_redirect( esc_url( $general_settings_url ) );
				exit();
			}
		}
	}
} else {

	// Deactivate the plugin if Woocommerce not active.
	add_action( 'admin_init', 'mwb_membership_plugin_activation_failure' );

	/**
	 * Deactivate the plugin.
	 */
	function mwb_membership_plugin_activation_failure() {

		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	// Add admin error notice.
	if ( is_multisite() ) {
		add_action( 'network_admin_notices', 'mwb_membership_plugin_activation_notice' );
	} else {
		add_action( 'admin_notices', 'mwb_membership_plugin_activation_notice' );
	}

	/**
	 * This function displays plugin activation error notices.
	 */
	function mwb_membership_plugin_activation_notice() {

		global $mwb_membership_plugin_activation;

		// To hide Plugin activated notice.
		unset( $_GET['activate'] );

		?>

		<?php if ( 'woo_inactive' === $mwb_membership_plugin_activation['message'] ) { ?>

			<div class="notice notice-error is-dismissible">
				<p><strong><?php esc_html_e( 'WooCommerce', 'membership-for-woocommerce-pro' ); ?></strong><?php esc_html_e( ' is not activated, Please activate WooCommerce first to activate ', 'membership-for-woocommerce-pro' ); ?><strong><?php esc_html_e( 'Membership For WooCommerce', 'membership-for-woocommerce-pro' ); ?></strong><?php esc_html_e( '.', 'membership-for-woocommerce-pro' ); ?></p>
			</div>

			<?php
		}
	}
}



