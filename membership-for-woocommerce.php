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
 * Version:           2.6.1
 * Author:            WP Swings
 * Author URI:        https://wpswings.com/?utm_source=wpswings-official&utm_medium=membership-org-backend&utm_campaign=official
 * Text Domain:       membership-for-woocommerce
 * Domain Path:       /languages
 * Requires Plugins: woocommerce
 *
 * Requires at least: 5.0
 * Tested up to:      6.6.2
 * WC requires at least: 5.0
 * WC tested up to:   9.3.3
 *
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use Automattic\WooCommerce\Utilities\OrderUtil;
require_once ABSPATH . 'wp-admin/includes/plugin.php';

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
		membership_for_woocommerce_constants( 'MEMBERSHIP_FOR_WOOCOMMERCE_VERSION', '2.6.1' );
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

	// check wallet plugin active or not.
	/**
	 * This function is used to check whether wallet plugin is active or not.
	 *
	 * @return bool
	 */
	function wps_msfw_is_wallet_plugin_active() {
		$is_active = false;
		if ( is_plugin_active( 'wallet-system-for-woocommerce/wallet-system-for-woocommerce.php' ) ) {
			if ( 'on' === get_option( 'wps_wsfw_enable', '' ) ) {

				$is_active = true;
			}
		}
		return $is_active;
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
	 * Replace get_post_meta with wps_membership_get_meta_data.
	 *
	 * @param  string $id  id.
	 * @param  string $key key.
	 * @param  string $v   v.
	 * @return string
	 */
	function wps_membership_get_meta_data( $id, $key, $v ) {
		if ( 'shop_order' === OrderUtil::get_order_type( $id ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
			$order    = wc_get_order( $id );
			if ( '_customer_user' == $key ) {
				$meta_val = $order->get_customer_id();
				return $meta_val;
			}
			$meta_val = $order->get_meta( $key );
			return $meta_val;
		} elseif ( 'wps_subscriptions' === OrderUtil::get_order_type( $id ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
			$order    = new WPS_Subscription( $id );
			$meta_val = $order->get_meta( $key );

			return $meta_val;
		} else {
			// Traditional CPT-based orders are in use.
			$meta_val = get_post_meta( $id, $key, $v );
			return $meta_val;
		}
	}

	/**
	 * Replace update_post_meta with wps_membership_update_meta_data.
	 *
	 * @param  string $id    id.
	 * @param  string $key   key.
	 * @param  string $value value.
	 * @return void
	 */
	function wps_membership_update_meta_data( $id, $key, $value ) {
		if ( 'shop_order' === OrderUtil::get_order_type( $id ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
			// HPOS usage is enabled.
			$order = wc_get_order( $id );
			$order->update_meta_data( $key, $value );
			$order->save();
		} elseif ( 'wps_subscriptions' === OrderUtil::get_order_type( $id ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
			// HPOS usage is enabled.
			$order = new WPS_Subscription( $id );

			$order->update_meta_data( $key, $value );
			$order->save();
		} else {
			// Traditional CPT-based orders are in use.
			update_post_meta( $id, $key, $value );
		}
	}

	add_action( 'admin_init', 'wps_mfw_set_cron_for_plugin_notification' );
	add_action( 'wps_wgm_check_for_notification_update', 'wps_mfw_save_notice_message' );
	add_action( 'wp_ajax_wps_mfw_dismiss_notice_banner', 'wps_mfw_dismiss_notice_banner_callback' );

	/**
	 * Function to set cron.
	 *
	 * @return void
	 */
	function wps_mfw_set_cron_for_plugin_notification() {
		$wps_sfw_offset = get_option( 'gmt_offset' );
		$wps_sfw_time   = time() + $wps_sfw_offset * 60 * 60;
		if ( ! wp_next_scheduled( 'wps_wgm_check_for_notification_update' ) ) {
			wp_schedule_event( $wps_sfw_time, 'daily', 'wps_wgm_check_for_notification_update' );
		}
	}

	/**
	 * Function to save msg.
	 *
	 * @return void
	 */
	function wps_mfw_save_notice_message() {
		$wps_notification_data = wps_mfw_get_update_notification_data();

		if ( is_array( $wps_notification_data ) && ! empty( $wps_notification_data ) ) {
			$banner_id      = array_key_exists( 'notification_id', $wps_notification_data[0] ) ? $wps_notification_data[0]['wps_banner_id'] : '';
			$banner_image = array_key_exists( 'notification_message', $wps_notification_data[0] ) ? $wps_notification_data[0]['wps_banner_image'] : '';
			$banner_url = array_key_exists( 'notification_message', $wps_notification_data[0] ) ? $wps_notification_data[0]['wps_banner_url'] : '';
			$banner_type = array_key_exists( 'notification_message', $wps_notification_data[0] ) ? $wps_notification_data[0]['wps_banner_type'] : '';
			update_option( 'wps_wgm_notify_new_banner_id', $banner_id );
			update_option( 'wps_wgm_notify_new_banner_image', $banner_image );
			update_option( 'wps_wgm_notify_new_banner_url', $banner_url );
			if ( 'regular' == $banner_type ) {
				update_option( 'wps_wgm_notify_hide_baneer_notification', '' );
			}
		}
	}

	/**
	 * Function to update notice.
	 *
	 * @return array
	 */
	function wps_mfw_get_update_notification_data() {
		$wps_notification_data = array();
		$url                   = 'https://demo.wpswings.com/client-notification/woo-gift-cards-lite/wps-client-notify.php';
		$attr                  = array(
			'action'         => 'wps_notification_fetch',
			'plugin_version' => MEMBERSHIP_FOR_WOOCOMMERCE_VERSION,
		);
		$query                 = esc_url_raw( add_query_arg( $attr, $url ) );
		$response              = wp_remote_get(
			$query,
			array(
				'timeout'   => 20,
				'sslverify' => false,
			)
		);

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo '<p><strong>Something went wrong: ' . esc_html( stripslashes( $error_message ) ) . '</strong></p>';
		} else {
			$wps_notification_data = json_decode( wp_remote_retrieve_body( $response ), true );
		}
		return $wps_notification_data;
	}

	/**
	 * Function to dissmiss notice.
	 *
	 * @return void
	 */
	function wps_mfw_dismiss_notice_banner_callback() {
		if ( isset( $_REQUEST['wps_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['wps_nonce'] ) ), 'plan-import-nonce' ) ) {

			$banner_id = get_option( 'wps_wgm_notify_new_banner_id', false );

			if ( isset( $banner_id ) && '' != $banner_id ) {
				update_option( 'wps_wgm_notify_hide_baneer_notification', $banner_id );
			}

			wp_send_json_success();
		}
	}


	add_action( 'admin_notices', 'wps_banner_notification_plugin_html' );
	if ( ! function_exists( 'wps_banner_notification_plugin_html' ) ) {
		/**
		 * Common Function To show banner image.
		 *
		 * @return void
		 */
		function wps_banner_notification_plugin_html() {

			$screen = get_current_screen();
			if ( isset( $screen->id ) ) {
				$pagescreen = $screen->id;
			}
			if ( ( isset( $pagescreen ) && 'plugins' === $pagescreen ) || ( 'wp-swings_page_home' == $pagescreen ) || ( 'wp-swings_page_membership_for_woocommerce_menu' == $pagescreen ) ) {
				$banner_id = get_option( 'wps_wgm_notify_new_banner_id', false );
				if ( isset( $banner_id ) && '' !== $banner_id ) {
					$hidden_banner_id            = get_option( 'wps_wgm_notify_hide_baneer_notification', false );
					$banner_image = get_option( 'wps_wgm_notify_new_banner_image', '' );
					$banner_url = get_option( 'wps_wgm_notify_new_banner_url', '' );
					if ( isset( $hidden_banner_id ) && $hidden_banner_id < $banner_id ) {

						if ( '' !== $banner_image && '' !== $banner_url ) {

							?>
								<div class="wps-offer-notice notice notice-warning is-dismissible">
									<div class="notice-container">
										<a href="<?php echo esc_url( $banner_url ); ?>" target="_blank"><img src="<?php echo esc_url( $banner_image ); ?>" alt="Subscription cards"/></a>
									</div>
									<button type="button" class="notice-dismiss dismiss_banner" id="dismiss-banner"><span class="screen-reader-text">Dismiss this notice.</span></button>
								</div>
								
							<?php
						}
					}
				}
			}
		}
	}

	register_deactivation_hook( __FILE__, 'wps_mfw_remove_cron_for_notification_update' );

	/**
	 * This function is used to clear scheduled events
	 *
	 * @return void
	 */
	function wps_mfw_remove_cron_for_notification_update() {
		wp_clear_scheduled_hook( 'wps_wgm_check_for_notification_update' );
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

add_action(
	'before_woocommerce_init',
	function() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);
