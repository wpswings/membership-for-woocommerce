<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link    https://wpswings.com/
 * @since   1.0.0
 * @package Membership_For_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Membership For WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/membership-for-woocommerce/
 * Description:       <code><strong>Membership For WooCommerce</strong></code> plugin helps you to create membership plans & offers members-only discounts, send membership emails. <a href="https://wpswings.com/woocommerce-plugins/?utm_source=wpswings-membership-shop&utm_medium=membership-org-backend&utm_campaign=shop-page">Elevate your e-commerce store by exploring more on <strong>WP Swings</strong></a>
 * Version:           2.2.2
 * Author:            WP Swings
 * Author URI:        https://wpswings.com/?utm_source=wpswings-official&utm_medium=membership-org-backend&utm_campaign=official
 * Text Domain:       membership-for-woocommerce
 * Domain Path:       /languages
 *
 * Requires at least: 5.0
 * Tested up to:      6.2
 * WC requires at least: 4.0
 * WC tested up to:   7.6.1
 *
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}


require_once ABSPATH . 'wp-admin/includes/plugin.php';

$old_mfw_pro_exists = false;
$plug           = get_plugins();
if ( isset( $plug['membership-for-woocommerce-pro/membership-for-woocommerce-pro.php'] ) ) {
	if ( version_compare( $plug['membership-for-woocommerce-pro/membership-for-woocommerce-pro.php']['Version'], '2.1.0', '<' ) ) {
		$old_mfw_pro_exists = true;
	}
}
add_action( 'after_plugin_row_membership-for-woocommerce-pro/membership-for-woocommerce-pro.php', 'wps_mfw_old_upgrade_notice', 0, 3 );

/**
 * Migration to ofl pro plugin.
 *
 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
 * @param array  $plugin_data An array of plugin data.
 * @param string $status Status filter currently applied to the plugin list.
 */
function wps_mfw_old_upgrade_notice( $plugin_file, $plugin_data, $status ) {

		global $old_mfw_pro_exists;
	if ( $old_mfw_pro_exists ) {
		?>
			<tr class="plugin-update-tr active notice-warning notice-alt">
			<td colspan="4" class="plugin-update colspanchange">
				<div class="notice notice-error inline update-message notice-alt">
					<p class='wps-notice-title wps-notice-section'>
						<strong><?php esc_html_e( 'This plugin will not work anymore correctly.', 'membership-for-woocommerce' ); ?></strong><br>
					<?php esc_html_e( 'We highly recommend to update to latest pro version and once installed please migrate the existing settings.', 'membership-for-woocommerce' ); ?><br>
					<?php esc_html_e( 'If you are not getting automatic update now button here, then don\'t worry you will get in within 24 hours. If you still not get it please visit to your account dashboard and install it manually or connect to our support.', 'membership-for-woocommerce' ); ?>
					</p>
				</div>
			</td>
		</tr>
		<style>
			.wps-notice-section > p:before {
				content: none;
			}
		</style>
			<?php
	}
}

if ( class_exists( 'Membership_For_Woocommerce_Admin' ) ) {

	$wps_mfw_get_count = new Membership_For_Woocommerce_Admin( 'membership-for-woocommerce', '2.1.4' );
	$wps_pending_par   = $wps_mfw_get_count->wps_membership_get_count( 'pending', 'count' );

	if ( 0 != $wps_pending_par ) {

		add_action( 'after_plugin_row_' . plugin_basename( __FILE__ ), 'wps_mfw_org_migrate_notice', 0, 3 );
	}
}
		/**
		 * Migration to new domain notice.
		 *
		 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
		 * @param array  $plugin_data An array of plugin data.
		 * @param string $status Status filter currently applied to the plugin list.
		 */
function wps_mfw_org_migrate_notice( $plugin_file, $plugin_data, $status ) {

	?>
			<tr class="plugin-update-tr active notice-warning notice-alt">
				<td colspan="4" class="plugin-update colspanchange">
					<div class="notice notice-error inline update-message notice-alt">
						<p class='wps-notice-title wps-notice-section'>
					<?php esc_html_e( 'Heads up. The latest update includes some substantial changes across different areas of the plugin. Please ', 'membership-for-woocommerce-pro' ); ?>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=membership_for_woocommerce_menu&mfw_tab=membership-for-woocommerce-general' ) ); ?>"><?php esc_html_e( 'Click Here', 'membership-for-woocommerce' ); ?></a>
					<?php esc_html_e( 'to goto migration panel.', 'membership-for-woocommerce-pro' ); ?>
						</p>
					</div>
				</td>
			</tr>
			<style>
				.wps-notice-section > p:before {
					content: none;
				}
			</style>
			<?php
}


/**
 * Function to check for plugin activation.
 *
 * @param string $plugin_slug is the slug of the plugin.
 */
function wps_membership_is_plugin_active( $plugin_slug = '' ) {
	if ( empty( $plugin_slug ) ) {

		return;
	}

	$active_plugins = (array) get_option( 'active_plugins', array() );

	if ( is_multisite() ) {

		$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}

	return in_array( $plugin_slug, $active_plugins ) || array_key_exists( $plugin_slug, $active_plugins );
}


$old_mfw_pro_present   = false;
$installed_plugins = get_plugins();

if ( array_key_exists( 'membership-for-woocommerce-pro/membership-for-woocommerce-pro.php', $installed_plugins ) ) {
	$pro_plugin = $installed_plugins['membership-for-woocommerce-pro/membership-for-woocommerce-pro.php'];
	if ( version_compare( $pro_plugin['Version'], '2.1.0', '<' ) ) {
		$old_mfw_pro_present = true;
	}
}

if ( true === $old_mfw_pro_present ) {

	add_action( 'wps_mfw_settings_saved_notice', 'wps_mfw_lite_add_updatenow_notice' );



		/**
		 * Displays notice to upgrade to membership.
		 *
		 * @return void
		 */
	function wps_mfw_lite_add_updatenow_notice() {
		?>

		<tr class="plugin-update-tr active notice-warning notice-alt">
			<td colspan="4" class="plugin-update colspanchange">
				<div class="notice notice-error inline update-message notice-alt">
					<div class='wps-notice-title wps-notice-section'>
						<p><strong>IMPORTANT NOTICE:</strong></p>
					</div>
					<div class='wps-notice-content wps-notice-section'>
						<p><strong>Your Memebership for Woocommerce Pro plugin update is here! Please Update it now via </strong> <a href="<?php echo wp_kses_post( admin_url( 'plugins.php' ) ); ?>">Plugins Page.</a> </p>
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

	}
	add_action( 'admin_notices', 'wps_mfw_check_and_inform_update' );

	/**
	 * Check update if pro is old.
	 */
	function wps_mfw_check_and_inform_update() {
		$update_file = plugin_dir_path( dirname( __FILE__ ) ) . 'membership-for-woocommerce-pro/class-membership-for-woocommerce-pro-update.php';

		// If present but not active.

		if ( file_exists( $update_file ) ) {
			$wps_mfw_pro_license_key = get_option( 'mwb_mfwp_license_check', '' );
			! defined( 'MEMBERSHIP_FOR_WOOCOMMERCE_PRO_LICENSE_KEY' ) && define( 'MEMBERSHIP_FOR_WOOCOMMERCE_PRO_LICENSE_KEY', $wps_mfw_pro_license_key );
			! defined( 'MEMBERSHIP_FOR_WOOCOMMERCE_PRO_BASE_FILE' ) && define( 'MEMBERSHIP_FOR_WOOCOMMERCE_PRO_BASE_FILE', 'membership-for-woocommerce-pro/membership-for-woocommerce-pro.php' );
		}
			require_once $update_file;

		if ( defined( 'MEMBERSHIP_FOR_WOOCOMMERCE_PRO_BASE_FILE' ) ) {

			$wps_mfw_pro = new Membership_For_Woocommerce_Pro_Update();
			$wps_mfw_pro->mwb_check_update();
			$plugin_transient  = get_site_transient( 'update_plugins' );
			$update_obj        = ! empty( $plugin_transient->response[ MEMBERSHIP_FOR_WOOCOMMERCE_PRO_BASE_FILE ] ) ? $plugin_transient->response[ MEMBERSHIP_FOR_WOOCOMMERCE_PRO_BASE_FILE ] : false;

			if ( ! empty( $update_obj ) ) :
				?>
				<div class="notice notice-error is-dismissible">
					<p><?php esc_html_e( 'Your Memebership For Woocommerce Pro plugin update is here! Please Update it now.', 'membership-for-woocommerce' ); ?></p>
				</div>
				<?php
			endif;

		}
	}
}

/**
 * Checking whether the dependent plugin is active or not.
 */
function wps_membership_plugin_activation() {
	$activation['status']  = true;
	$activation['message'] = '';

	// If dependent plugin is not active.
	if ( ! wps_membership_is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

		$activation['status']  = false;
		$activation['message'] = 'woo_inactive';
	}

	return $activation;

}

// The following code runs during the activation of the plugin.
$wps_membership_plugin_activation = wps_membership_plugin_activation();

if ( true === $wps_membership_plugin_activation['status'] ) {

	/**
	 * Define plugin constants.
	 *
	 * @since 1.0.0
	 */
	function define_membership_for_woocommerce_constants() {
		membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_VERSION', '2.2.2' );
		membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH', plugin_dir_path( __FILE__ ) );
		membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL', plugin_dir_url( __FILE__ ) );
		membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_SERVER_URL', 'https://wpswings.com/' );
		membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_ITEM_REFERENCE', 'Membership For WooCommerce' );
	}

	/**
	 * Define wps-site update feature.
	 *
	 * @since 1.0.0
	 */
	function auto_update_membership_for_woocommerce() {
		 $wps_mfw_license_key = get_option( 'wps_mfw_license_key', '' );
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
		membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_LICENSE_KEY', $wps_mfw_license_key );

	}

	if ( ! function_exists( 'wps_mfw_standard_check_multistep' ) ) {
		/**
		 * Function to check multistep function.
		 *
		 * @return bool
		 */
		function wps_mfw_standard_check_multistep() {
			$bool = false;
			$wps_standard_check = get_option( 'mfw_mfw_plugin_standard_multistep_done', false );

			if ( ! empty( $wps_standard_check ) ) {
				$bool = true;
			}

			/**
			 * Filter for multistep done.
			 *
			 * @since 1.0.0
			 */
			$bool = apply_filters( 'wps_standard_multistep_done', $bool );
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

		$wps_mfw_active_plugin = get_option( 'wps_all_plugins_active', false );
		if ( is_array( $wps_mfw_active_plugin ) && ! empty( $wps_mfw_active_plugin ) ) {
			$wps_mfw_active_plugin['membership-for-woocommerce'] = array(
				'plugin_name' => __( 'Membership For WooCommerce', 'membership-for-woocommerce' ),
				'active' => '1',
			);
		} else {
			$wps_mfw_active_plugin = array();
			$wps_mfw_active_plugin['membership-for-woocommerce'] = array(
				'plugin_name' => __( 'Membership For WooCommerce', 'membership-for-woocommerce' ),
				'active' => '1',
			);
		}
		update_option( 'wps_all_plugins_active', $wps_mfw_active_plugin );
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-membership-for-woocommerce-deactivator.php
	 */
	function deactivate_membership_for_woocommerce() {
		include_once plugin_dir_path( __FILE__ ) . 'includes/class-membership-for-woocommerce-deactivator.php';
		Membership_For_Woocommerce_Deactivator::membership_for_woocommerce_deactivate();
		$wps_mfw_deactive_plugin = get_option( 'wps_all_plugins_active', false );
		if ( is_array( $wps_mfw_deactive_plugin ) && ! empty( $wps_mfw_deactive_plugin ) ) {
			foreach ( $wps_mfw_deactive_plugin as $wps_mfw_deactive_key => $wps_mfw_deactive ) {
				if ( 'membership-for-woocommerce' === $wps_mfw_deactive_key ) {
					$wps_mfw_deactive_plugin[ $wps_mfw_deactive_key ]['active'] = '0';
				}
			}
		}
		update_option( 'wps_all_plugins_active', $wps_mfw_deactive_plugin );
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
		$mfw_mfw_plugin_standard = new Membership_For_Woocommerce();
		$mfw_mfw_plugin_standard->mfw_run();
		$GLOBALS['mfw_wps_mfw_obj'] = $mfw_mfw_plugin_standard;

	}
	run_membership_for_woocommerce();

	add_action( 'admin_enqueue_scripts', 'mfw_admin_enqueue_styles' );

	/**
	 * Schedule hook for member expiry.
	 *
	 * @return void
	 */
	function wps_membership_schedule_expiry() {
		if ( false === as_next_scheduled_action( 'wps_membership_expiry_check_action' ) ) {
			as_schedule_recurring_action( strtotime( 'tomorrow' ), DAY_IN_SECONDS, 'wps_membership_expiry_check_action' );
		}
	}

	/**
	 * Schedule action for expiry.
	 *
	 * @return void
	 */
	function wps_membership_schedule_action_expiry_check() {
		if ( class_exists( 'Membership_For_Woocommerce_Public' ) ) {
			$mfw_plugin_public = new Membership_For_Woocommerce_Public( '', '' );
			$mfw_plugin_public->wps_membership_cron_expiry_check();
		}
	}

	add_action( 'init', 'wps_membership_schedule_expiry' );

	add_action( 'wps_membership_expiry_check_action', 'wps_membership_schedule_action_expiry_check' );

		/**
		 * Schedule hook for member expiry.
		 *
		 * @return void
		 */
	function wps_membership_schedule_hook() {
		// Schedule cron for checking of membership expiration on daily basis.
		if ( ! wp_next_scheduled( 'wps_membership_expiry_check' ) ) {
			wp_schedule_event( time(), 'daily', 'wps_membership_expiry_check' );
		}

	}
	add_action( 'init', 'wps_membership_schedule_hook' );

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
					wp_enqueue_style( 'admin-css', plugin_dir_url( __FILE__ ) . '/admin/css/membership-for-woocommerce-admin.css', array(), '2.1.0', false );

				}
			}
		}
	}



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
		$mfw_plugins = get_plugins();
		if ( ! isset( $mfw_plugins['membership-for-woocommerce-pro/membership-for-woocommerce-pro.php'] ) ) {

			$my_link['goPro'] = '<a class="wps-wpr-go-pro" target="_blank" href="https://wpswings.com/product/membership-for-woocommerce-pro/?utm_source=wpswings-membership-pro&utm_medium=membership-org-backend&utm_campaign=go-pro">' . esc_html__( 'GO PRO', 'membership-for-woocommerce' ) . '</a>';
		}
		return array_merge( $my_link, $links );
	}
	if ( ! function_exists( 'wps_membership_check_plugin_enable' ) ) {
		/**
		 * This function is used to check plugin is enable.
		 *
		 * @name wps_membership_check_plugin_enable
		 * @since 1.0.0
		 */
		function wps_membership_check_plugin_enable() {
			$is_enable = false;
			$wps_membership_enable_plugin = get_option( 'wps_membership_enable_plugin', '' );
			if ( 'on' == $wps_membership_enable_plugin ) {
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
			$links_array[] = '<a href="https://demo.wpswings.com/membership-for-woocommerce-pro/?utm_source=wpswings-membership-demo&utm_medium=membership-pro-backend&utm_campaign=demo" target="_blank"><img src="' . esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/Demo.svg" style="margin-right: 6px;margin-top: -3px;max-width: 15px;" alt="Demo image">' . __( 'Demo', 'membership-for-woocommerce' ) . '</a>';
			$links_array[] = '<a href="https://www.youtube.com/watch?v=Yf0pa_Fgn5s&t=2s" target="_blank"><img src="' . esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/YouTube.png" style="margin-right: 6px;margin-top: -3px;max-width: 15px;" alt="Video image">' . __( 'Video', 'membership-for-woocommerce' ) . '</a>';
			$links_array[] = '<a href="https://docs.wpswings.com/membership-for-woocommerce/?utm_source=wpswings-membership-doc&utm_medium=membership-org-backend&utm_campaign=documentation" target="_blank"><img src="' . esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/Documentation.svg" style="margin-right: 6px;margin-top: -3px;max-width: 15px;" alt="documentation image">' . __( 'Documentation', 'membership-for-woocommerce' ) . '</a>';
			$links_array[] = '<a href="https://wpswings.com/submit-query/?utm_source=wpswings-membership-support&utm_medium=membership-org-backend&utm_campaign=support" target="_blank"><img src="' . esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/Support.svg" style="margin-right: 6px;margin-top: -3px;max-width: 15px;" alt="support image">' . __( 'Support', 'membership-for-woocommerce' ) . '</a>';
			$links_array[] = '<a href="https://wpswings.com/woocommerce-services/?utm_source=wpswings-membership-services&utm_medium=membership-org-backend&utm_campaign=woocommerce-services" target="_blank"><img style="height: 18px;width: 18px;" src="' . esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/mfw_Services.svg" class="wps-info-img" alt="support image">' . __( 'Services', 'membership-for-woocommerce' ) . '</a>';

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


		add_action( 'admin_init', 'wps_membership_code_migrate' );




		/**
		 * Function for codebase migration.
		 *
		 * @return void
		 */
		function wps_membership_code_migrate() {
			$is_migration_done = get_option( 'is_wps_migration_done', 'not_done' );
			if ( 'done' != $is_migration_done ) {
				global $wpdb;

				// Creating Instance of the global functions class.
				$global_class = Membership_For_Woocommerce_Global_Functions::get();

				add_role(
					'member',
					__( 'Member', 'membership-for-woocommerce' ),
					array(
						'read' => true,
					)
				);

				/**
				 * Generating default membership plans page at the time of plugin activation.
				 */

				$mwb_membership_default_plans_page_id = get_option( 'mwb_membership_default_plans_page' );
				if ( ! empty( $mwb_membership_default_plans_page_id ) ) {
					wp_delete_post( $mwb_membership_default_plans_page_id );
					delete_option( 'mwb_membership_default_plans_page' );
				}

				$wps_membership_default_plans_page_id = get_option( 'wps_membership_default_plans_page' );

				if ( empty( $wps_membership_default_plans_page_id ) ) {

					$page_content = '5' <= get_bloginfo( 'version' ) ? $global_class->gutenberg_content() : '[wps_membership_default_plans_page]';

					if ( empty( $wps_membership_default_plans_page_id ) || 'publish' !== get_post_status( $wps_membership_default_plans_page_id ) ) {

						$wps_membership_plans_page = array(
							'comment_status' => 'closed',
							'ping_status'    => 'closed',
							'post_content'   => $page_content,
							'post_name'      => 'membership-plans',
							'post_status'    => 'publish',
							'post_title'     => 'Membership Plans',
							'post_type'      => 'page',
						);

						$wps_membership_plans_post = wp_insert_post( $wps_membership_plans_page );

						update_option( 'wps_membership_default_plans_page', $wps_membership_plans_post );
					}
				} else {
					$current_post                = get_post( $wps_membership_default_plans_page_id, 'ARRAY_A' );
					$current_post['post_status'] = 'publish';
					wp_update_post( $current_post );
				}

				/**
				 * Generating default membership plans page at the time of plugin activation.
				 */
				$wps_membership_default_plans_product_id = get_option( 'mwb_membership_default_product' );
				if ( ! empty( $wps_membership_default_plans_product_id ) ) {
					wp_delete_post( $wps_membership_default_plans_product_id );
					delete_option( 'mwb_membership_default_product' );
				}
				$wps_membership_default_product = get_option( 'wps_membership_default_product' );

				if ( empty( $wps_membership_default_product ) || 'private' !== get_post_status( $wps_membership_default_product ) ) {

					 $wps_membership_product = array(
						 'post_name'    => 'membership-product',
						 'post_status'  => 'publish',
						 'post_title'   => 'Membership Product',
						 'post_type'    => 'product',
						 'post_author'  => 1,
						 'post_content' => stripslashes( html_entity_decode( 'Auto generated product for membership please do not delete or update.', ENT_QUOTES, 'UTF-8' ) ),
					 );

					 $wps_membership_product_id = wp_insert_post( $wps_membership_product );

					 if ( ! is_wp_error( $wps_membership_product_id ) ) {

						 $product = wc_get_product( $wps_membership_product_id );
						 wp_set_object_terms( $wps_membership_product_id, 'simple', 'product_type' );
						 update_post_meta( $wps_membership_product_id, '_regular_price', 0 );
						 update_post_meta( $wps_membership_product_id, '_price', 0 );
						 update_post_meta( $wps_membership_product_id, '_visibility', 'public' );
						 update_post_meta( $wps_membership_product_id, '_virtual', 'yes' );

						 if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {

							 $product->set_reviews_allowed( false );
							 $product->set_catalog_visibility( 'hidden' );
							 $product->save();
						 }

						 update_option( 'wps_membership_default_product', $wps_membership_product_id );
					 }
				}
				wp_clear_scheduled_hook( 'wpswings_tracker_send_event' );

				/**
				 * Filter for reccurence.
				 *
				 * @since 1.0.0
				 */
				wp_schedule_event( time() + 10, apply_filters( 'wpswings_tracker_event_recurrence', 'daily' ), 'wpswings_tracker_send_event' );

				$all_feeds = get_posts(
					array(
						'post_type'      => 'mwb_cpt_members',
						'post_status'    => array( 'publish', 'draft' ),
						'fields'         => 'ids',
						'posts_per_page' => -1,
					)
				);

				if ( ! empty( $all_feeds ) && is_array( $all_feeds ) ) {
					foreach ( $all_feeds as $key => $feed_id ) {
						$args = array(
							'ID'        => $feed_id,
							'post_type' => 'wps_cpt_members',
						);
						wp_update_post( $args );
					}
				}

				include_once plugin_dir_path( __FILE__ ) . 'includes/class-membership-for-woocommerce-activator.php';

				Membership_For_Woocommerce_Activator::mfw_migrate_membership_post_type();
				Membership_For_Woocommerce_Activator::mfw_upgrade_wp_options();
				Membership_For_Woocommerce_Activator::wpg_mfw_replace_mwb_to_wps_in_shortcodes();
				update_option( 'is_wps_migration_done', 'done', true );
			}

		}

		add_action( 'admin_notices', 'wps_membership_plugin_updation_notice' );





		/**
		 * Function for admin notice of update.
		 *
		 * @return void
		 */
		function wps_membership_plugin_updation_notice() {
			$mwf_plugins = get_plugins();
			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
				if ( ! empty( $screen->id ) && 'plugins' === $screen->id ) {
					if ( isset( $mwf_plugins['membership-for-woocommerce-pro/membership-for-woocommerce-pro.php'] ) ) {

						if ( $mwf_plugins['membership-for-woocommerce-pro/membership-for-woocommerce-pro.php']['Version'] < '2.0.2' ) {
							?>
							
							<div class="notice notice-error is-dismissible">
								<p><strong><?php esc_html_e( 'Version 2.1.0 of Membership for Woocommerce Pro ', 'membership-for-woocommerce' ); ?></strong><?php esc_html_e( ' is not available on your system! Please Update ', 'membership-for-woocommerce' ); ?><strong><?php esc_html_e( 'Membership For WooCommerce Pro', 'membership-for-woocommerce' ); ?></strong><?php esc_html_e( '.', 'membership-for-woocommerce' ); ?></p>
							</div>
							<?php
						}
					}
				}
			}

		}
		add_action( 'init', 'wps_mfw_deactivate_plugin' );

		/**
		 * Deactivating plugin.
		 *
		 * @return void
		 */
		function wps_mfw_deactivate_plugin() {

			if ( is_plugin_active( 'membership-for-woocommerce-pro/membership-for-woocommerce-pro.php' ) ) {
				$sfw_plugins = get_plugins();
				if ( $sfw_plugins['membership-for-woocommerce-pro/membership-for-woocommerce-pro.php']['Version'] < '2.1.0' ) {

					deactivate_plugins( 'membership-for-woocommerce-pro/membership-for-woocommerce-pro.php' );

				}
			}
		}
	}
} else {

	// Deactivate the plugin if Woocommerce not active.
	add_action( 'admin_init', 'wps_membership_plugin_activation_failure' );

	/**
	 * Deactivate the plugin.
	 */
	function wps_membership_plugin_activation_failure() {

		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	// Add admin error notice.
	if ( is_multisite() ) {
		add_action( 'network_admin_notices', 'wps_membership_plugin_activation_notice' );
	} else {
		add_action( 'admin_notices', 'wps_membership_plugin_activation_notice' );
	}

	/**
	 * This function displays plugin activation error notices.
	 */
	function wps_membership_plugin_activation_notice() {

		global $wps_membership_plugin_activation;

		// To hide Plugin activated notice.
		unset( $_GET['activate'] );

		?>

		<?php if ( 'woo_inactive' === $wps_membership_plugin_activation['message'] ) { ?>

			<div class="notice notice-error is-dismissible">
				<p><strong><?php esc_html_e( 'WooCommerce', 'membership-for-woocommerce' ); ?></strong><?php esc_html_e( ' is not activated, Please activate WooCommerce first to activate ', 'membership-for-woocommerce' ); ?><strong><?php esc_html_e( 'Membership For WooCommerce', 'membership-for-woocommerce' ); ?></strong><?php esc_html_e( '.', 'membership-for-woocommerce' ); ?></p>
			</div>

			<?php
		}

	}
}


