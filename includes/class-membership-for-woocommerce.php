<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link  https://wpswings.com/
 * @since 1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes
 */
class Membership_For_Woocommerce {


	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since 1.0.0
	 * @var   Membership_For_Woocommerce_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 1.0.0
	 * @var   string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since 1.0.0
	 * @var   string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The current version of the plugin.
	 *
	 * @since 1.0.0
	 * @var   string    $mfw_onboard    To initializsed the object of class onboard.
	 */
	protected $mfw_onboard;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area,
	 * the public-facing side of the site and common side of the site.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		if ( defined( 'MEMBERSHIP_FOR_WOOCOMMERCE_VERSION' ) ) {

			$this->version = MEMBERSHIP_FOR_WOOCOMMERCE_VERSION;
		} else {

			$this->version = '2.6.1';
		}

		$this->plugin_name = 'membership-for-woocommerce';

		$this->membership_for_woocommerce_dependencies();
		$this->membership_for_woocommerce_locale();
		if ( is_admin() ) {
			$this->membership_for_woocommerce_admin_hooks();
		} else {
			$this->membership_for_woocommerce_public_hooks();
		}
		$this->membership_for_woocommerce_common_hooks();

		$this->membership_for_woocommerce_api_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Membership_For_Woocommerce_Loader. Orchestrates the hooks of the plugin.
	 * - Membership_For_Woocommerce_i18n. Defines internationalization functionality.
	 * - Membership_For_Woocommerce_Admin. Defines all hooks for the admin area.
	 * - Membership_For_Woocommerce_Common. Defines all hooks for the common area.
	 * - Membership_For_Woocommerce_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since 1.0.0
	 */
	private function membership_for_woocommerce_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-membership-for-woocommerce-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-membership-for-woocommerce-i18n.php';

			// The class responsible for on-boarding steps for plugin.
		if ( is_dir( plugin_dir_path( dirname( __FILE__ ) ) . 'onboarding' ) && ! class_exists( 'Membership_For_Woocommerce_Onboarding_Steps' ) ) {
			include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-membership-for-woocommerce-onboarding-steps.php';
		}

		if ( class_exists( 'Membership_For_Woocommerce_Onboarding_Steps' ) ) {
			$mfw_onboard_steps = new Membership_For_Woocommerce_Onboarding_Steps();
		}
				  // The class responsible for defining all actions that occur in the admin area.
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-membership-for-woocommerce-admin.php';

			// The class responsible for defining all actions that occur in the public-facing side of the site.
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-membership-for-woocommerce-public.php';

		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'package/rest-api/class-membership-for-woocommerce-rest-api.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-membership-for-woocommerce-common.php';

		/**
		 * The class responsible for defining all global functions for the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-membership-for-woocommerce-global-functions.php';

		/**
		 * This class responsible for defining common functionality
		 * of the plugin.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'common/class-membership-for-woocommerce-common.php';

		// Elementor Compatibility.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'shortcode-widget/class-wps-membership-shortcode-widget-loader.php';
		if ( class_exists( 'WPS_Membership_Shortcode_Widget_Loader' ) ) {

			WPS_Membership_Shortcode_Widget_Loader::get_instance();
		}

		$this->loader = new Membership_For_Woocommerce_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Membership_For_Woocommerce_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since 1.0.0
	 */
	private function membership_for_woocommerce_locale() {

		$plugin_i18n = new Membership_For_Woocommerce_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Define the name of the hook to save admin notices for this plugin.
	 *
	 * @since 1.0.0
	 */
	private function wps_saved_notice_hook_name() {
		$wps_plugin_name                            = ! empty( explode( '/', plugin_basename( __FILE__ ) ) ) ? explode( '/', plugin_basename( __FILE__ ) )[0] : '';
		$wps_plugin_settings_saved_notice_hook_name = $wps_plugin_name . '_settings_saved_notice';
		return $wps_plugin_settings_saved_notice_hook_name;
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since 1.0.0
	 */
	private function membership_for_woocommerce_admin_hooks() {
		$mfw_plugin_admin = new Membership_For_Woocommerce_Admin( $this->mfw_get_plugin_name(), $this->mfw_get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $mfw_plugin_admin, 'mfw_admin_enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $mfw_plugin_admin, 'mfw_admin_enqueue_scripts' );

		// Add settings menu for Membership For WooCommerce.
		$this->loader->add_action( 'admin_menu', $mfw_plugin_admin, 'mfw_options_page' );
		$this->loader->add_action( 'admin_menu', $mfw_plugin_admin, 'wps_mfw_remove_default_submenu', 50 );

		$this->loader->add_filter( 'wps_add_plugins_menus_array', $mfw_plugin_admin, 'mfw_admin_submenu_page', 15 );
		$this->loader->add_filter( 'mfw_general_settings_array', $mfw_plugin_admin, 'mfw_admin_general_settings_page', 10 );

		// Saving tab settings.
		$this->loader->add_action( 'wps_mfw_settings_saved_notice', $mfw_plugin_admin, 'mfw_admin_save_tab_settings' );
		// Saving tab settings for registration form.
		$this->loader->add_action( 'wps_mfw_settings_saved_notice', $mfw_plugin_admin, 'mfw_admin_save_tab_settings_reg_form' );
		$this->loader->add_action( 'wp_ajax_wps_membership_search_products_for_membership_registration', $mfw_plugin_admin, 'wps_membership_search_products_for_membership_registration' );

		// Developer's Hook Listing.
		$this->loader->add_action( 'mfw_developer_admin_hooks_array', $mfw_plugin_admin, 'wps_developer_admin_hooks_listing' );
		$this->loader->add_action( 'mfw_developer_public_hooks_array', $mfw_plugin_admin, 'wps_developer_public_hooks_listing' );

		// Settings array for membership sub tabs.
		$this->loader->add_action( 'mfw_add_plans_settings_array', $mfw_plugin_admin, 'wps_mfw_add_plans_settings_array' );

		   // Remove title & editor field.
		$this->loader->add_action( 'init', $mfw_plugin_admin, 'membership_for_woo_remove_fields' );

		// Remove quick edit.
		$this->loader->add_filter( 'post_row_actions', $mfw_plugin_admin, 'membership_for_woo_remove_quick_edit', 10, 1 );
		// Remove bulk action for members.
		$this->loader->add_filter( 'bulk_actions-edit-wps_cpt_members', $mfw_plugin_admin, 'membership_for_woo_members_remove_bulkaction' );
		// Add custom post type.
		$this->loader->add_action( 'init', $mfw_plugin_admin, 'wps_membership_for_woo_cpt_members' );
		if ( wps_mfw_standard_check_multistep() ) {
			$this->loader->add_action( 'init', $mfw_plugin_admin, 'wps_membership_for_woo_cpt_membership' );
		}
		// Remove submenu page.
		$this->loader->add_action( 'admin_menu', $mfw_plugin_admin, 'wps_membership_remove_submenu' );

		// Display page states of membership default page.
		$this->loader->add_filter( 'display_post_states', $mfw_plugin_admin, 'wps_membership_default_page_states', 10, 2 );

		// Creating membership method.

		// Adding custom columns.
		$this->loader->add_filter( 'manage_wps_cpt_members_posts_columns', $mfw_plugin_admin, 'wps_membership_for_woo_cpt_columns_members' );
		$this->loader->add_filter( 'manage_wps_cpt_membership_posts_columns', $mfw_plugin_admin, 'wps_membership_for_woo_cpt_columns_membership' );

		// Populating columns.
		$this->loader->add_action( 'manage_wps_cpt_members_posts_custom_column', $mfw_plugin_admin, 'wps_membership_for_woo_fill_columns_members', 10, 2 );
		$this->loader->add_action( 'manage_wps_cpt_membership_posts_custom_column', $mfw_plugin_admin, 'wps_membership_for_woo_fill_columns_membership', 10, 2 );

		// Admin side ajax.
		$this->loader->add_action( 'wp_ajax_wps_membership_search_products_for_membership', $mfw_plugin_admin, 'wps_membership_search_products_for_membership' );
		$this->loader->add_action( 'wp_ajax_wps_membership_search_product_categories_for_membership', $mfw_plugin_admin, 'wps_membership_search_product_categories_for_membership' );
		$this->loader->add_action( 'wp_ajax_wps_membership_get_membership_content', $mfw_plugin_admin, 'wps_membership_get_membership_content' );
		$this->loader->add_action( 'wp_ajax_wps_membership_get_states', $mfw_plugin_admin, 'wps_membership_get_states' );
		$this->loader->add_action( 'wp_ajax_wps_membership_get_member_content', $mfw_plugin_admin, 'wps_membership_get_member_content' );

		// Download CSV.
		$this->loader->add_action( 'init', $mfw_plugin_admin, 'wps_membership_for_woo_export_csv_members' );
		$this->loader->add_action( 'init', $mfw_plugin_admin, 'wps_membership_for_woo_export_csv_membership' );

		// Add CSV export button.
		$this->loader->add_action( 'restrict_manage_posts', $mfw_plugin_admin, 'wps_membership_for_woo_export_members', 10 );
		$this->loader->add_action( 'restrict_manage_posts', $mfw_plugin_admin, 'wps_membership_for_woo_export_membership', 10 );

		// Save meta box fields.
		$this->loader->add_action( 'save_post_wps_cpt_membership', $mfw_plugin_admin, 'wps_membership_for_woo_save_fields' );

		$this->loader->add_action( 'edit_post_wps_cpt_members', $mfw_plugin_admin, 'wps_membership_save_member_fields' );

		// Creating membership method.
		$this->loader->add_action( 'woocommerce_shipping_init', $mfw_plugin_admin, 'wps_membership_for_woo_create_shipping_method' );
		$this->loader->add_filter( 'woocommerce_shipping_methods', $mfw_plugin_admin, 'wps_membership_for_woo_add_shipping_method' );

		// update member in user update.
		$this->loader->add_action( 'profile_update', $mfw_plugin_admin, 'wps_membership_for_woo_update_profile_for_member', 10, 2 );

		$this->loader->add_action( 'wp_trash_post', $mfw_plugin_admin, 'wps_membership_for_woo_add_to_trash_member' );
		$this->loader->add_action( 'wp_initialize_site', $mfw_plugin_admin, 'wps_membership_for_woo_on_create_new_blog', 900 );
		$this->loader->add_action( 'wps_sfw_subscription_cancel', $mfw_plugin_admin, 'wps_membership_cancel_membership_acc_susbcription', 20, 2 );

		// Add new column in user table at admin side.
		$this->loader->add_filter( 'manage_users_custom_column', $mfw_plugin_admin, 'wps_membership_new_modify_user_table_add_user', 10, 3 );

		$this->loader->add_filter( 'manage_users_columns', $mfw_plugin_admin, 'wps_membership_new_modify_user_table_value' );
		$this->loader->add_filter( 'user_contactmethods', $mfw_plugin_admin, 'wps_membership_new_column_value_assign', 10, 1 );
		$this->loader->add_action( 'wp_ajax_wps_membership_save_member_status', $mfw_plugin_admin, 'wps_membership_save_member_status' );

		$this->loader->add_action( 'wp_ajax_wps_membership_ajax_callbacks', $mfw_plugin_admin, 'wps_membership_ajax_callbacks' );

		// add custom menu in product edit page.
		$this->loader->add_filter( 'woocommerce_product_data_tabs', $mfw_plugin_admin, 'mfw_attach_plan_product_data_tab', 99, 1 );
		$this->loader->add_action( 'woocommerce_product_data_panels', $mfw_plugin_admin, 'mfw_attach_plan_product_data_fields' );

		$this->loader->add_action( 'save_post', $mfw_plugin_admin, 'wps_mfw_save_product_data' );

		$this->loader->add_action( 'wp_ajax_wps_membership_create_plan_reg', $mfw_plugin_admin, 'wps_membership_create_plan_reg_callback' );
		$this->loader->add_action( 'init', $mfw_plugin_admin, 'wps_mfwp_send_msg_to_all_members' );

		// API settings creations.
		$this->loader->add_filter( 'mfw_api_settings_array', $mfw_plugin_admin, 'wps_membership_api_html_settings', 10, 1 );
		$this->loader->add_action( 'wps_mfw_settings_saved_notice', $mfw_plugin_admin, 'mfw_admin_save_api_settings' );
		$this->loader->add_action( 'wps_mfw_settings_saved_notice', $mfw_plugin_admin, 'mfw_generate_api_keys_settings' );
		// Other settings.
		$this->loader->add_filter( 'mfw_other_settings_array', $mfw_plugin_admin, 'wps_mfw_other_html_settings', 10, 1 );
		$this->loader->add_action( 'wps_mfw_settings_saved_notice', $mfw_plugin_admin, 'mfw_admin_save_other_settings' );
		// wallet plugin compatible.
		if ( wps_msfw_is_wallet_plugin_active() ) {

			$this->loader->add_filter( 'mfw_other_settings_array', $mfw_plugin_admin, 'wps_msfw_restrict_wallet_payment', 20, 1 );
		}
	}

	/**
	 * Register all of the hooks related to the common functionality
	 * of the plugin.
	 *
	 * @since 1.0.0
	 */
	private function membership_for_woocommerce_common_hooks() {

		$mfw_plugin_common = new Membership_For_Woocommerce_Common( $this->mfw_get_plugin_name(), $this->mfw_get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $mfw_plugin_common, 'mfw_common_enqueue_styles' );

		$this->loader->add_action( 'wp_enqueue_scripts', $mfw_plugin_common, 'mfw_common_enqueue_scripts' );

		// AJAX handlers to save tnx data.
		$this->loader->add_action( 'wp_ajax_wps_membership_checkout', $mfw_plugin_common, 'wps_membership_checkout' );
		$this->loader->add_action( 'wp_ajax_nopriv_wps_membership_checkout', $mfw_plugin_common, 'wps_membership_checkout' );
		$this->loader->add_action( 'wp_ajax_wps_membership_csv_file_upload', $mfw_plugin_common, 'wps_membership_csv_file_upload' ); // Import CSV.
		$this->loader->add_action( 'wps_sfw_other_payment_gateway_renewal', $mfw_plugin_common, 'wps_membership_subscription_renewal', 99, 3 );
		$this->loader->add_action( 'wps_sfw_expire_subscription_scheduler', $mfw_plugin_common, 'wps_membership_subscription_expire', 99, 1 );
		$this->loader->add_action( 'wps_sfw_subscription_active_renewal', $mfw_plugin_common, 'wps_membership_subscription_active_renewal', 99, 1 );
		$this->loader->add_action( 'wps_sfw_subscription_on_hold_renewal', $mfw_plugin_common, 'wps_membership_subscription_on_hold_renewal', 99, 1 );
		// Save ajax request for the plugin's multistep.
		$this->loader->add_action( 'wp_ajax_wps_membership_save_settings_filter', $mfw_plugin_common, 'wps_membership_save_settings_filter' );
		$this->loader->add_action( 'wp_ajax_nopriv_wps_membership_save_settings_filter', $mfw_plugin_common, 'wps_membership_save_settings_filter' );
		$this->loader->add_action( 'user_register', $mfw_plugin_common, 'wps_membership_sen_email_to_new_registered_user' );

		$this->loader->add_action( 'wp_ajax_wps_membership_cancel_membership_count', $mfw_plugin_common, 'wps_membership_cancel_membership_count' );
		$this->loader->add_action( 'wp_ajax_nopriv_wps_membership_cancel_membership_count', $mfw_plugin_common, 'wps_membership_cancel_membership_count' );
		$this->loader->add_action( 'woocommerce_order_status_changed', $mfw_plugin_common, 'wps_membership_woo_order_status_change_custom', 10, 3 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since 1.0.0
	 */
	private function membership_for_woocommerce_public_hooks() {

		$global_class = Membership_For_Woocommerce_Global_Functions::get();

		// Getting global options.
		$wps_membership_global_settings = get_option( 'wps_membership_global_options', $global_class->default_global_options() );

		// By default plugin will be enabled.
		$wps_membership_enable_plugin = ! empty( $wps_membership_global_settings['wps_membership_enable_plugin'] ) ? $wps_membership_global_settings['wps_membership_enable_plugin'] : 'off';

		if ( 'on' === $wps_membership_enable_plugin ) {

			$mfw_plugin_public = new Membership_For_Woocommerce_Public( $this->mfw_get_plugin_name(), $this->mfw_get_version() );

			$this->loader->add_action( 'wp_enqueue_scripts', $mfw_plugin_public, 'mfw_public_enqueue_styles' );
			$this->loader->add_action( 'wp_enqueue_scripts', $mfw_plugin_public, 'mfw_public_enqueue_scripts' );

			// Register Endpoint.
			$this->loader->add_action( 'init', $mfw_plugin_public, 'wps_membership_register_endpoint' );
			// Add query variable.
			$this->loader->add_action( 'query_vars', $mfw_plugin_public, 'wps_membership_endpoint_query_var', 0 );
			// Inserting custom Membership tab.
			$this->loader->add_action( 'woocommerce_account_menu_items', $mfw_plugin_public, 'wps_membership_add_membership_tab' );
			// Add title to Tab page.
			$this->loader->add_filter( 'the_title', $mfw_plugin_public, 'wps_membership_tab_title' );
			// Populate mmbership details tab.
			$this->loader->add_action( 'woocommerce_account_wps-membership-tab_endpoint', $mfw_plugin_public, 'wps_membership_populate_tab' );
			// Load all defined shortcodes.
			$this->loader->add_action( 'init', $mfw_plugin_public, 'wps_membership_shortcodes' );

			// Make all membership products non-purchasable for non-members.
			$this->loader->add_filter( 'woocommerce_is_purchasable', $mfw_plugin_public, 'wps_membership_for_woo_membership_purchasable', 10, 2 );
			// Display "Buy membership" message for products on detail page.
			$this->loader->add_action( 'woocommerce_single_product_summary', $mfw_plugin_public, 'wps_membership_product_membership_purchase_html', 50 );
			// Hide price of membership products on shop page.
			$this->loader->add_action( 'woocommerce_get_price_html', $mfw_plugin_public, 'wps_membership_for_woo_hide_price_shop_page', 10, 2 );
			// Display "Membership" tag for membership products on shop page.
			$this->loader->add_action( 'woocommerce_shop_loop_item_title', $mfw_plugin_public, 'wps_membership_products_on_shop_page', 10 );
			$theme = wp_get_theme();

			if ( 'Betheme' == $theme->name ) {

				$this->loader->add_action( 'woocommerce_after_shop_loop_item_title', $mfw_plugin_public, 'wps_membership_products_on_shop_page', 10 );
			}
			// Hide other shipping methods, if membership free shipping available.
			$this->loader->add_filter( 'woocommerce_package_rates', $mfw_plugin_public, 'wps_membership_unset_shipping_if_membership_available', 10, 2 );

			// AJAX handlers for receipt removal.
			$this->loader->add_action( 'wp_ajax_wps_membership_remove_current_receipt', $mfw_plugin_public, 'wps_membership_remove_current_receipt' );
			$this->loader->add_action( 'wp_ajax_nopriv_wps_membership_remove_current_receipt', $mfw_plugin_public, 'wps_membership_remove_current_receipt' );
			// AJAX handlers for get states.
			$this->loader->add_action( 'wp_ajax_wps_membership_get_states_public', $mfw_plugin_public, 'wps_membership_get_states_public' );
			$this->loader->add_action( 'wp_ajax_nopriv_wps_membership_get_states_public', $mfw_plugin_public, 'wps_membership_get_states_public' );
			// AJAX handlers for process payment.
			$this->loader->add_action( 'wp_ajax_wps_membership_process_payment', $mfw_plugin_public, 'wps_membership_process_payment' );
			$this->loader->add_action( 'wp_ajax_nopriv_wps_membership_process_payment', $mfw_plugin_public, 'wps_membership_process_payment' );

			// AJAX handlers to save tnx data.
			$this->loader->add_action( 'wp_ajax_wps_membership_save_transaction', $mfw_plugin_public, 'wps_membership_save_transaction' );
			$this->loader->add_action( 'wp_ajax_nopriv_wps_membership_save_transaction', $mfw_plugin_public, 'wps_membership_save_transaction' );

			// Cart discount.
			$this->loader->add_action( 'woocommerce_cart_calculate_fees', $mfw_plugin_public, 'wps_membership_add_cart_discount' );

			// Cron even to check membership expiry.
			$this->loader->add_action( 'wps_membership_expiry_check', $mfw_plugin_public, 'wps_membership_cron_expiry_check' );

			// Settin membership price in cart.
			$this->loader->add_action( 'woocommerce_before_calculate_totals', $mfw_plugin_public, 'wps_membership_set_membership_product_price', 5 );

			// Settin membership price in cart.
			$this->loader->add_action( 'woocommerce_is_purchasable', $mfw_plugin_public, 'wps_membership_make_membership_product_purchasable', 10, 2 );

			// Settin membership price in cart.
			$this->loader->add_action( 'woocommerce_order_status_changed', $mfw_plugin_public, 'wps_membership_process_payment' );
			$this->loader->add_action( 'template_redirect', $mfw_plugin_public, 'wps_membership_buy_now_add_to_cart' );

			// Distraction free page for membership plans page.
			$this->loader->add_filter( 'page_template', $mfw_plugin_public, 'wps_membership_plan_page_template' );

			// Creating membership method.
			$this->loader->add_action( 'woocommerce_shipping_init', $mfw_plugin_public, 'wps_membership_for_woo_create_shipping_method' );
			$this->loader->add_filter( 'woocommerce_shipping_methods', $mfw_plugin_public, 'wps_membership_for_woo_add_shipping_method' );
			$this->loader->add_filter( 'add_to_cart_url', $mfw_plugin_public, 'wps_membership_add_to_cart_url', 20, 1 );
			$this->loader->add_action( 'woocommerce_init', $mfw_plugin_public, 'wps_mfw_set_woocoomerce_session', 10 );
			$this->loader->add_filter( 'mmcsfw_get_product_price_of_member', $mfw_plugin_public, 'wps_membership_get_product_price_of_member', 20, 2 );
			$this->loader->add_filter( 'wps_sfw_set_subscription_status', $mfw_plugin_public, 'wps_membership_subscription_get_status', 20, 2 );
			$this->loader->add_filter( 'wps_sfw_next_payment_date', $mfw_plugin_public, 'wps_membership_subscription_next_payment_date', 20, 2 );
			$this->loader->add_filter( 'wps_sfw_susbcription_end_date', $mfw_plugin_public, 'wps_membership_susbcription_end_date', 20, 2 );
			$this->loader->add_filter( 'woocommerce_is_sold_individually', $mfw_plugin_public, 'wps_membership_hide_quantity_fields_for_membership', 10, 2 );
			$this->loader->add_action( 'woocommerce_after_checkout_validation', $mfw_plugin_public, 'wps_membership_validate_email', 10, 2 );

			// Login at thank you page.
			$this->loader->add_action( 'woocommerce_thankyou', $mfw_plugin_public, 'wps_membership_login_thanku_page', 11, 1 );

			$this->loader->add_action( 'woocommerce_shop_loop_item_title', $mfw_plugin_public, 'mfw_membership_add_label', 20 );
			$theme = wp_get_theme();

			if ( 'Betheme' == $theme->name ) {

				$this->loader->add_action( 'woocommerce_after_shop_loop_item_title', $mfw_plugin_public, 'mfw_membership_add_label', 20 );
			}
			$this->loader->add_action( 'woocommerce_before_add_to_cart_form', $mfw_plugin_public, 'mfw_membership_add_label', 20 );
			$this->loader->add_action( 'woocommerce_init', $mfw_plugin_public, 'wps_mfw_registration_form_submission_callback' );
			$this->loader->add_filter( 'woocommerce_checkout_fields', $mfw_plugin_public, 'wps_mfw_remove_billing_from_checkout', 10, 1 );

			// redirect user when register on site.
			$this->loader->add_filter( 'woocommerce_registration_redirect', $mfw_plugin_public, 'wps_msfw_user_redirection', 10, 1 );
			// wallet plugin compatible.
			if ( wps_msfw_is_wallet_plugin_active() ) {

				$this->loader->add_filter( 'woocommerce_available_payment_gateways', $mfw_plugin_public, 'wps_msfw_restrict_wallet_payments', 10, 1 );
			}

			// block user unable to purchase include product.
			$this->loader->add_action( 'woocommerce_is_purchasable', $mfw_plugin_public, 'wps_mfw_block_user_unable_to_pruchase_include_product', 10, 2 );
		}
	}

	/**
	 * Register all of the hooks related to the api functionality
	 * of the plugin.
	 *
	 * @since 1.0.0
	 */
	private function membership_for_woocommerce_api_hooks() {

		$global_class                   = Membership_For_Woocommerce_Global_Functions::get();
		$wps_membership_global_settings = get_option( 'wps_membership_global_options', $global_class->default_global_options() );
		$wps_membership_enable_plugin   = ! empty( $wps_membership_global_settings['wps_membership_enable_plugin'] ) ? $wps_membership_global_settings['wps_membership_enable_plugin'] : 'off';

		if ( 'on' === $wps_membership_enable_plugin ) {

			$mfw_plugin_api = new Membership_For_Woocommerce_Rest_Api( $this->mfw_get_plugin_name(), $this->mfw_get_version() );
			$this->loader->add_action( 'rest_api_init', $mfw_plugin_api, 'wps_mfw_add_endpoint' );
		}

	}

	/**
	 * Check is usage tracking is enable
	 *
	 * @version 1.0.0
	 * @name is_enbale_usage_tracking
	 */
	public static function is_enbale_usage_tracking() {
		$check_is_enable = get_option( 'mfw_enable_tracking', false );
		return ! empty( $check_is_enable ) ? true : false;
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function mfw_run() {
		$this->loader->mfw_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since  1.0.0
	 * @return string    The name of the plugin.
	 */
	public function mfw_get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since  1.0.0
	 * @return Membership_For_Woocommerce_Loader    Orchestrates the hooks of the plugin.
	 */
	public function mfw_get_loader() {
		return $this->loader;
	}


	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since  1.0.0
	 * @return Membership_For_Woocommerce_Onboard    Orchestrates the hooks of the plugin.
	 */
	public function mfw_get_onboard() {
		return $this->mfw_onboard;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since  1.0.0
	 * @return string    The version number of the plugin.
	 */
	public function mfw_get_version() {
		return $this->version;
	}

	/**
	 * Predefined default wps_mfw_plug tabs.
	 *
	 * @return Array       An key=>value pair of Membership For WooCommerce tabs.
	 */
	public function wps_mfw_plug_default_tabs() {
		$mfw_default_tabs = array();

			$mfw_default_tabs['membership-for-woocommerce-overview'] = array(
				'title'       => esc_html__( 'Overview', 'membership-for-woocommerce' ),
				'name'        => 'membership-for-woocommerce-overview',
				'file_path'   => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/templates/membership-templates/wps-membership-overview.php',
			);
			$mfw_default_tabs['membership-for-woocommerce-general'] = array(
				'title'       => esc_html__( 'General Settings', 'membership-for-woocommerce' ),
				'name'        => 'membership-for-woocommerce-general',
				'file_path'   => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/membership-for-woocommerce-general.php',
			);
			$mfw_default_tabs['membership-for-woocommerce-membership-using-registration-form'] = array(
				'title'       => esc_html__( 'Membership Settings', 'membership-for-woocommerce' ),
				'name'        => 'membership-for-woocommerce-general',
				'file_path'   => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/membership-for-woocommerce-membership-using-registration-form.php',
			);
			$mfw_default_tabs['membership-for-woocommerce-shortcodes'] = array(
				'title'       => esc_html__( 'Shortcodes', 'membership-for-woocommerce' ),
				'name'        => 'membership-for-woocommerce-shortcodes',
				'file_path'   => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/templates/membership-templates/wps-membership-shortcodes.php',
			);
			$mfw_default_tabs['membership-for-woocommerce-api-settings'] = array(
				'title'       => esc_html__( 'API Settings', 'membership-for-woocommerce' ),
				'name'        => 'membership-for-woocommerce-api-settings',
				'file_path'   => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/templates/membership-templates/membership-for-woocommerce-api-settings.php',
			);
			$mfw_default_tabs['membership-for-woocommerce-other-settings'] = array(
				'title'       => esc_html__( 'Other Settings', 'membership-for-woocommerce' ),
				'name'        => 'membership-for-woocommerce-other-settings',
				'file_path'   => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/templates/membership-templates/membership-for-woocommerce-other-settings.php',
			);
			$mfw_default_tabs['membership-for-woocommerce-reports-settings'] = array(
				'title'       => esc_html__( 'Report', 'membership-for-woocommerce' ),
				'name'        => 'membership-for-woocommerce-reports-settings',
				'file_path'   => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/templates/membership-templates/membership-for-woocommerce-reports-settings.php',
			);

			/**
			 * Filter for admin tab setting.
			 *
			 * @since 1.0.0
			 */
			$mfw_default_tabs = apply_filters( 'wps_mfw_plugin_standard_admin_settings_tabs_after_system_status', $mfw_default_tabs );
			$mfw_default_tabs['membership-for-woocommerce-system-status'] = array(
				'title'       => esc_html__( 'System Status', 'membership-for-woocommerce' ),
				'name'        => 'membership-for-woocommerce-system-status',
				'file_path'   => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/membership-for-woocommerce-system-status.php',
			);

			$mfw_default_tabs['membership-for-woocommerce-developer'] = array(
				'title'       => esc_html__( 'Developer', 'membership-for-woocommerce' ),
				'name'        => 'membership-for-woocommerce-developer',
				'file_path'   => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/membership-for-woocommerce-developer.php',
			);
			$mfw_default_tabs =

			/**
			 * Filter for admin setting tabs.
			 *
			 * @since 1.0.0
			 */
			apply_filters( 'wps_mfw_mfw_plugin_standard_admin_settings_tabs', $mfw_default_tabs );

			return $mfw_default_tabs;
	}

	/**
	 * Predefined default wps_mfw_plug sub tabs.
	 *
	 * @return Array       An key=>value pair of Membership For WooCommerce sub tabs.
	 */
	public function wps_mfw_plug_config_sub_tabs() {
		$mfw_default_sub_tabs = array();
		$mfw_default_sub_tabs['membership-for-woocommerce-add-plans'] = array(
			'title'       => esc_html__( 'Add Plan', 'membership-for-woocommerce' ),
			'name'        => 'membership-for-woocommerce-add-plans',
			'file_path'   => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/templates/membership-registration/membership-for-woocommerce-add-plans.php',
		);
		$mfw_default_sub_tabs['membership-for-woocommerce-membership-restriction'] = array(
			'title'       => esc_html__( 'Membership Restriction', 'membership-for-woocommerce' ),
			'name'        => 'membership-for-woocommerce-membership-restriction',
			'file_path'   => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/templates/membership-registration/membership-for-woocommerce-membership-restriction.php',
		);
		$mfw_default_sub_tabs['membership-for-woocommerce-purchasing-discount-offer'] = array(
			'title'       => esc_html__( 'Purchasing Discount Offer', 'membership-for-woocommerce' ),
			'name'        => 'membership-for-woocommerce-purchasing-discount-offer',
			'file_path'   => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/templates/membership-registration/membership-for-woocommerce-purchasing-discount-offer.php',
		);
		$mfw_default_sub_tabs['membership-for-woocommerce-content-restriction'] = array(
			'title'       => esc_html__( 'Content Restriction', 'membership-for-woocommerce' ),
			'name'        => 'membership-for-woocommerce-content-restriction',
			'file_path'   => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/templates/membership-registration/membership-for-woocommerce-content-restriction.php',
		);
		$mfw_default_sub_tabs['membership-for-woocommerce-add-member'] = array(
			'title'       => esc_html__( 'Add Member', 'membership-for-woocommerce' ),
			'name'        => 'membership-for-woocommerce-add-member',
			'file_path'   => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/templates/membership-registration/membership-for-woocommerce-add-member.php',
		);
		$mfw_default_sub_tabs['membership-for-woocommerce-send-msg-to-member'] = array(
			'title'       => esc_html__( 'Send Message to Members', 'membership-for-woocommerce' ),
			'name'        => 'membership-for-woocommerce-send-msg-to-member',
			'file_path'   => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/templates/membership-registration/membership-for-woocommerce-send-message-to-members.php',
		);

		/**
		 * Filter for admin setting tabs.
		 *
		 * @since 1.0.0
		 */
		apply_filters( 'wps_mfw_mfw_plugin_standard_admin_settings_sub_tabs', $mfw_default_sub_tabs );

		return $mfw_default_sub_tabs;
	}

	/**
	 * Locate and load appropriate tempate.
	 *
	 * @since 1.0.0
	 * @param string $path   path file for inclusion.
	 * @param array  $params parameters to pass to the file for access.
	 */
	public function wps_mfw_plug_load_template( $path, $params = array() ) {

		if ( file_exists( $path ) ) {

			include $path;
		} else {

			/* translators: %s: file path */
			$mfw_notice = sprintf( esc_html__( 'Unable to locate file at location "%s". Some features may not work properly in this plugin. Please contact us!', 'membership-for-woocommerce' ), $path );
			$this->wps_mfw_plug_admin_notice( $mfw_notice, 'error' );
		}
	}

	/**
	 * Show admin notices.
	 *
	 * @param string $mfw_message Message to display.
	 * @param string $type        notice type, accepted values - error/update/update-nag.
	 * @since 1.0.0
	 */
	public static function wps_mfw_plug_admin_notice( $mfw_message, $type = 'error' ) {

		$mfw_classes = 'notice ';

		switch ( $type ) {

			case 'update':
				$mfw_classes .= 'updated is-dismissible';
				break;

			case 'update-nag':
				$mfw_classes .= 'update-nag is-dismissible';
				break;

			case 'success':
				$mfw_classes .= 'notice-success is-dismissible';
				break;

			default:
				$mfw_classes .= 'notice-error is-dismissible';
		}

		$mfw_notice  = '<div class="' . esc_attr( $mfw_classes ) . '">';
		$mfw_notice .= '<p>' . esc_html( $mfw_message ) . '</p>';
		$mfw_notice .= '</div>';

		echo wp_kses_post( $mfw_notice );
	}

	/**
	 * Show WordPress and server info.
	 *
	 * @return Array $mfw_system_data returns array of all wordpress and server related information.
	 * @since  1.0.0
	 */
	public function wps_mfw_plug_system_status() {
		global $wpdb;
		$mfw_system_status = array();
		$mfw_wordpress_status = array();
		$mfw_system_data = array();

		// Get the web server.
		$mfw_system_status['web_server'] = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';

		// Get PHP version.
		$mfw_system_status['php_version'] = function_exists( 'phpversion' ) ? phpversion() : __( 'N/A (phpversion function does not exist)', 'membership-for-woocommerce' );

		// Get the server's IP address.
		$mfw_system_status['server_ip'] = isset( $_SERVER['SERVER_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) : '';

		// Get the server's port.
		$mfw_system_status['server_port'] = isset( $_SERVER['SERVER_PORT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_PORT'] ) ) : '';

		// Get the uptime.
		$mfw_system_status['uptime'] = function_exists( 'exec' ) ? @exec( 'uptime -p' ) : __( 'N/A (make sure exec function is enabled)', 'membership-for-woocommerce' );

		// Get the server path.
		$mfw_system_status['server_path'] = defined( 'ABSPATH' ) ? ABSPATH : __( 'N/A (ABSPATH constant not defined)', 'membership-for-woocommerce' );

		// Get the OS.
		$mfw_system_status['os'] = function_exists( 'php_uname' ) ? php_uname( 's' ) : __( 'N/A (php_uname function does not exist)', 'membership-for-woocommerce' );

		// Get WordPress version.
		$mfw_wordpress_status['wp_version'] = function_exists( 'get_bloginfo' ) ? get_bloginfo( 'version' ) : __( 'N/A (get_bloginfo function does not exist)', 'membership-for-woocommerce' );

		// Get and count active WordPress plugins.
		$mfw_wordpress_status['wp_active_plugins'] = function_exists( 'get_option' ) ? count( get_option( 'active_plugins' ) ) : __( 'N/A (get_option function does not exist)', 'membership-for-woocommerce' );

		// See if this site is multisite or not.
		$mfw_wordpress_status['wp_multisite'] = function_exists( 'is_multisite' ) && is_multisite() ? __( 'Yes', 'membership-for-woocommerce' ) : __( 'No', 'membership-for-woocommerce' );

		// See if WP Debug is enabled.
		$mfw_wordpress_status['wp_debug_enabled'] = defined( 'WP_DEBUG' ) ? __( 'Yes', 'membership-for-woocommerce' ) : __( 'No', 'membership-for-woocommerce' );

		// See if WP Cache is enabled.
		$mfw_wordpress_status['wp_cache_enabled'] = defined( 'WP_CACHE' ) ? __( 'Yes', 'membership-for-woocommerce' ) : __( 'No', 'membership-for-woocommerce' );

		// Get the total number of WordPress users on the site.
		$mfw_wordpress_status['wp_users'] = function_exists( 'count_users' ) ? count_users() : __( 'N/A (count_users function does not exist)', 'membership-for-woocommerce' );

		// Get the number of published WordPress posts.
		$mfw_wordpress_status['wp_posts'] = wp_count_posts()->publish >= 1 ? wp_count_posts()->publish : __( '0', 'membership-for-woocommerce' );

		// Get PHP memory limit.
		$mfw_system_status['php_memory_limit'] = function_exists( 'ini_get' ) ? (int) ini_get( 'memory_limit' ) : __( 'N/A (ini_get function does not exist)', 'membership-for-woocommerce' );

		// Get the PHP error log path.
		$mfw_system_status['php_error_log_path'] = ! ini_get( 'error_log' ) ? __( 'N/A', 'membership-for-woocommerce' ) : ini_get( 'error_log' );

		// Get PHP max upload size.
		$mfw_system_status['php_max_upload'] = function_exists( 'ini_get' ) ? (int) ini_get( 'upload_max_filesize' ) : __( 'N/A (ini_get function does not exist)', 'membership-for-woocommerce' );

		// Get PHP max post size.
		$mfw_system_status['php_max_post'] = function_exists( 'ini_get' ) ? (int) ini_get( 'post_max_size' ) : __( 'N/A (ini_get function does not exist)', 'membership-for-woocommerce' );

		// Get the PHP architecture.
		if ( PHP_INT_SIZE == 4 ) {
			$mfw_system_status['php_architecture'] = '32-bit';
		} elseif ( PHP_INT_SIZE == 8 ) {
			$mfw_system_status['php_architecture'] = '64-bit';
		} else {
			$mfw_system_status['php_architecture'] = 'N/A';
		}

		// Get server host name.
		$mfw_system_status['server_hostname'] = function_exists( 'gethostname' ) ? gethostname() : __( 'N/A (gethostname function does not exist)', 'membership-for-woocommerce' );

		// Show the number of processes currently running on the server.
		$mfw_system_status['processes'] = function_exists( 'exec' ) ? @exec( 'ps aux | wc -l' ) : __( 'N/A (make sure exec is enabled)', 'membership-for-woocommerce' );

		// Get the memory usage.
		$mfw_system_status['memory_usage'] = function_exists( 'memory_get_peak_usage' ) ? round( memory_get_peak_usage( true ) / 1024 / 1024, 2 ) : 0;

		// Get CPU usage.
		// Check to see if system is Windows, if so then use an alternative since sys_getloadavg() won't work.
		if ( stristr( PHP_OS, 'win' ) ) {
			$mfw_system_status['is_windows'] = true;
			$mfw_system_status['windows_cpu_usage'] = function_exists( 'exec' ) ? @exec( 'wmic cpu get loadpercentage /all' ) : __( 'N/A (make sure exec is enabled)', 'membership-for-woocommerce' );
		}

		// Get the memory limit.
		$mfw_system_status['memory_limit'] = function_exists( 'ini_get' ) ? (int) ini_get( 'memory_limit' ) : __( 'N/A (ini_get function does not exist)', 'membership-for-woocommerce' );

		// Get the PHP maximum execution time.
		$mfw_system_status['php_max_execution_time'] = function_exists( 'ini_get' ) ? ini_get( 'max_execution_time' ) : __( 'N/A (ini_get function does not exist)', 'membership-for-woocommerce' );

		$mfw_system_data['php'] = $mfw_system_status;
		$mfw_system_data['wp'] = $mfw_wordpress_status;

		return $mfw_system_data;
	}

	/**
	 * Generate html components.
	 *
	 * @param string $mfw_components html to display.
	 * @since 1.0.0
	 */
	public function wps_mfw_plug_generate_html( $mfw_components = array() ) {
		if ( is_array( $mfw_components ) && ! empty( $mfw_components ) ) {
			foreach ( $mfw_components as $mfw_component ) {
				if ( ! empty( $mfw_component['type'] ) && ! empty( $mfw_component['id'] ) ) {

					switch ( $mfw_component['type'] ) {

						case 'hidden':
						case 'number':
						case 'email':
						case 'text':
							?>
						<div class="wps-form-group wps-mfw-<?php echo esc_attr( $mfw_component['type'] ); ?>">
							<div class="wps-form-group__label">
								<label for="<?php echo esc_attr( $mfw_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $mfw_component['title'] ) ? esc_html( $mfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="wps-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined wps_admin_membership_price_wrapper">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
							<?php if ( 'number' != $mfw_component['type'] ) { ?>
												<span class="mdc-floating-label" id="my-label-id"><?php echo ( isset( $mfw_component['placeholder'] ) ? esc_attr( $mfw_component['placeholder'] ) : '' ); ?></span>
						<?php } ?>
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<input min="0"
									class="mdc-text-field__input <?php echo ( isset( $mfw_component['class'] ) ? esc_attr( $mfw_component['class'] ) : '' ); ?>" 
									name="<?php echo ( isset( $mfw_component['name'] ) ? esc_html( $mfw_component['name'] ) : esc_html( $mfw_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $mfw_component['id'] ); ?>"
									type="<?php echo esc_attr( $mfw_component['type'] ); ?>"
									value="<?php echo ( isset( $mfw_component['value'] ) ? esc_attr( $mfw_component['value'] ) : '' ); ?>"
									placeholder="<?php echo ( isset( $mfw_component['placeholder'] ) ? esc_attr( $mfw_component['placeholder'] ) : '' ); ?>"
									>
								</label>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo ( isset( $mfw_component['description'] ) ? esc_attr( $mfw_component['description'] ) : '' ); ?></div>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'password':
							?>
						<div class="wps-form-group">
							<div class="wps-form-group__label">
								<label for="<?php echo esc_attr( $mfw_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $mfw_component['title'] ) ? esc_html( $mfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
								<?php
								$instance = Membership_For_Woocommerce_Global_Functions::get();
								$instance->tool_tip( ( isset( $mfw_component['description'] ) ? esc_attr( $mfw_component['description'] ) : '' ) );
								?>
							</div>
							<div class="wps-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--with-trailing-icon">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<input 
									class="mdc-text-field__input <?php echo ( isset( $mfw_component['class'] ) ? esc_attr( $mfw_component['class'] ) : '' ); ?> wps-form__password" 
									name="<?php echo ( isset( $mfw_component['name'] ) ? esc_html( $mfw_component['name'] ) : esc_html( $mfw_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $mfw_component['id'] ); ?>"
									type="<?php echo esc_attr( $mfw_component['type'] ); ?>"
									value="<?php echo ( isset( $mfw_component['value'] ) ? esc_attr( $mfw_component['value'] ) : '' ); ?>"
									placeholder="<?php echo ( isset( $mfw_component['placeholder'] ) ? esc_attr( $mfw_component['placeholder'] ) : '' ); ?>"
									>
									<i class="material-icons mdc-text-field__icon mdc-text-field__icon--trailing wps-password-hidden" tabindex="0" role="button">visibility</i>
								</label>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo ( isset( $mfw_component['description'] ) ? esc_attr( $mfw_component['description'] ) : '' ); ?></div>
								</div>
							</div>
						</div>
							<?php
							break;
						case 'textarea':
							?>
						<div class="wps-form-group">
							<div class="wps-form-group__label">
								<label class="wps-form-label" for="<?php echo esc_attr( $mfw_component['id'] ); ?>"><?php echo ( isset( $mfw_component['title'] ) ? esc_html( $mfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
								<?php
								$instance = Membership_For_Woocommerce_Global_Functions::get();
								$instance->tool_tip( ( isset( $mfw_component['description'] ) ? esc_attr( $mfw_component['description'] ) : '' ) );
								?>
							</div>
							<div class="wps-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--textarea"      for="text-field-hero-input">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
											<span class="mdc-floating-label"><?php echo ( isset( $mfw_component['placeholder'] ) ? esc_attr( $mfw_component['placeholder'] ) : '' ); ?></span>
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<span class="mdc-text-field__resizer">
										<textarea class="mdc-text-field__input <?php echo ( isset( $mfw_component['class'] ) ? esc_attr( $mfw_component['class'] ) : '' ); ?>" rows="2" cols="25" aria-label="Label" name="<?php echo ( isset( $mfw_component['name'] ) ? esc_html( $mfw_component['name'] ) : esc_html( $mfw_component['id'] ) ); ?>" id="<?php echo esc_attr( $mfw_component['id'] ); ?>" placeholder="<?php echo ( isset( $mfw_component['placeholder'] ) ? esc_attr( $mfw_component['placeholder'] ) : '' ); ?>"><?php echo ( isset( $mfw_component['value'] ) ? esc_textarea( $mfw_component['value'] ) : '' ); // WPCS: XSS ok. ?></textarea>
									</span>
								</label>
							</div>
						</div>
							<?php
							break;
						case 'access_type':
							?>
							<div class="wps-form-group wps-mfw-<?php echo esc_attr( $mfw_component['type'] ); ?>">
							<div class="wps-form-group__label">
								<label for="<?php echo esc_attr( $mfw_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $mfw_component['title'] ) ? esc_html( $mfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							
							</div>
							<div class="wps-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<select
									class="mdc-text-field__input <?php echo ( isset( $mfw_component['class'] ) ? esc_attr( $mfw_component['class'] ) : '' ); ?>" 
									 name="wps_mfw_reg_access_type" id="wps_mfw_reg_access_type" style="width:200px"
									>
			
									<option value="lifetime"><?php echo esc_html_e( 'Lifetime', 'membership-for-woocommerce' ); ?></option>
									<option value="limited"><?php echo esc_html_e( 'Limited', 'membership-for-woocommerce' ); ?></option>
									</select>
								</label>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo ( isset( $mfw_component['description'] ) ? esc_attr( $mfw_component['description'] ) : '' ); ?></div>
								</div>
							</div>
						</div>
						<div class="wps-form-group wps_mfw_plan_expiry_class hidden">
							<div class="wps-form-group__label">
								<label for="<?php echo esc_attr( $mfw_component['id'] ); ?>" class="wps-form-label"><?php esc_html_e( 'Set expiry', 'membership-for-woocommerce' ); // WPCS: XSS ok. ?></label>
							
							</div>
							<div class="wps-form-group__control">
								<!-- <label class="mdc-text-field mdc-text-field--outlined"> -->
									
									<input
									class="mdc-text-field__input <?php echo ( isset( $mfw_component['class'] ) ? esc_attr( $mfw_component['class'] ) : '' ); ?>" 
									 type="number" min="0" pattern="[0-9]" oninput="this.value=(parseInt(this.value)||0)" name="wps_mfw_reg_expiry_num" id="wps_mfw_reg_expiry_num" style="width:100px;height:55px"
									>
									<select id="wps_mfw_reg_expiry_time" name="wps_mfw_reg_expiry_time"  style="width:150px;height:55px">
										<option value="days"><?php esc_html_e( 'Days', 'membership-for-woocommerce' ); ?></option>
										<option value="weeks"><?php esc_html_e( 'Weeks', 'membership-for-woocommerce' ); ?></option>
										<option value="months"><?php esc_html_e( 'Months', 'membership-for-woocommerce' ); ?></option>
										<option value="years"><?php esc_html_e( 'Years', 'membership-for-woocommerce' ); ?></option>
									</select>
									
								<!-- </label> -->
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php esc_html_e( 'Set the expiry time.', 'membership-for-woocommerce' ); ?></div>
								</div>
							</div>
						</div>
						
							<?php
							break;
						case 'select':
						case 'multiselect':
							?>
						<div class="wps-form-group">
							<div class="wps-form-group__label">
								<label class="wps-form-label" for="<?php echo esc_attr( $mfw_component['id'] ); ?>"><?php echo ( isset( $mfw_component['title'] ) ? esc_html( $mfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
								<?php
								$instance = Membership_For_Woocommerce_Global_Functions::get();
								$instance->tool_tip( ( isset( $mfw_component['description'] ) ? esc_attr( $mfw_component['description'] ) : '' ) );
								?>
							</div>
							<div class="wps-form-group__control">
								<div class="wps-form-select">
									<select id="<?php echo esc_attr( $mfw_component['id'] ); ?>" name="<?php echo ( isset( $mfw_component['name'] ) ? esc_html( $mfw_component['name'] ) : esc_html( $mfw_component['id'] ) ); ?><?php echo ( 'multiselect' === $mfw_component['type'] ) ? '[]' : ''; ?>" id="<?php echo esc_attr( $mfw_component['id'] ); ?>" class="mdl-textfield__input <?php echo ( isset( $mfw_component['class'] ) ? esc_attr( $mfw_component['class'] ) : '' ); ?>" <?php echo 'multiselect' === $mfw_component['type'] ? 'multiple="multiple"' : ''; ?> >
							<?php
							foreach ( $mfw_component['options'] as $mfw_key => $mfw_val ) {
								?>
											<option value="<?php echo esc_attr( $mfw_key ); ?>"
												<?php
												if ( is_array( $mfw_component['value'] ) ) {
													selected( in_array( (string) $mfw_key, $mfw_component['value'], true ), true );
												} else {
														   selected( $mfw_component['value'], (string) $mfw_key );
												}
												?>
												>
												<?php echo esc_html( $mfw_val ); ?>
											</option>
										<?php
							}
							?>
									</select>
								</div>
							</div>
						</div>

							<?php
							break;

						case 'checkbox':
							?>
						<div class="wps-form-group">
							<div class="wps-form-group__label">
								<label for="<?php echo esc_attr( $mfw_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $mfw_component['title'] ) ? esc_html( $mfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
								<?php
								$instance = Membership_For_Woocommerce_Global_Functions::get();
								$instance->tool_tip( ( isset( $mfw_component['description'] ) ? esc_attr( $mfw_component['description'] ) : '' ) );
								?>
							</div>
							<div class="wps-form-group__control wps-pl-4">
								<div class="mdc-form-field">
									<div class="mdc-checkbox">
										<input 
										name="<?php echo ( isset( $mfw_component['name'] ) ? esc_html( $mfw_component['name'] ) : esc_html( $mfw_component['id'] ) ); ?>"
										id="<?php echo esc_attr( $mfw_component['id'] ); ?>"
										type="checkbox"
										class="mdc-checkbox__native-control <?php echo ( isset( $mfw_component['class'] ) ? esc_attr( $mfw_component['class'] ) : '' ); ?>"
										value="<?php echo ( isset( $mfw_component['value'] ) ? esc_attr( $mfw_component['value'] ) : '' ); ?>"
							<?php checked( $mfw_component['value'], '1' ); ?>
										/>
										<div class="mdc-checkbox__background">
											<svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
												<path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59"/>
											</svg>
											<div class="mdc-checkbox__mixedmark"></div>
										</div>
										<div class="mdc-checkbox__ripple"></div>
									</div>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'radio':
							?>
						<div class="wps-form-group">
							<div class="wps-form-group__label">
								<label for="<?php echo esc_attr( $mfw_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $mfw_component['title'] ) ? esc_html( $mfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
								<?php
								$instance = Membership_For_Woocommerce_Global_Functions::get();
								$instance->tool_tip( ( isset( $mfw_component['description'] ) ? esc_attr( $mfw_component['description'] ) : '' ) );
								?>
							</div>
							<div class="wps-form-group__control wps-pl-4">
								<div class="wps-flex-col">
							<?php
							foreach ( $mfw_component['options'] as $mfw_radio_key => $mfw_radio_val ) {
								?>
										<div class="mdc-form-field">
											<div class="mdc-radio">
												<input
												name="<?php echo ( isset( $mfw_component['name'] ) ? esc_html( $mfw_component['name'] ) : esc_html( $mfw_component['id'] ) ); ?>"
												value="<?php echo esc_attr( $mfw_radio_key ); ?>"
												type="radio"
												class="mdc-radio__native-control <?php echo ( isset( $mfw_component['class'] ) ? esc_attr( $mfw_component['class'] ) : '' ); ?>"
								<?php checked( $mfw_radio_key, $mfw_component['value'] ); ?>
												>
												<div class="mdc-radio__background">
													<div class="mdc-radio__outer-circle"></div>
													<div class="mdc-radio__inner-circle"></div>
												</div>
												<div class="mdc-radio__ripple"></div>
											</div>
											<label for="radio-1"><?php echo esc_html( $mfw_radio_val ); ?></label>
										</div>    
								<?php
							}
							?>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'radio-switch':
							?>

						<div class="wps-form-group">
							<div class="wps-form-group__label">
								<label for="" class="wps-form-label"><?php echo ( isset( $mfw_component['title'] ) ? esc_html( $mfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							
							</div>
							<div class="wps-form-group__control">
								<div>
									<div class="mdc-switch">
										<div class="mdc-switch__track"></div>
										<div class="mdc-switch__thumb-underlay">
											<div class="mdc-switch__thumb"></div>
											<input name="<?php echo ( isset( $mfw_component['name'] ) ? esc_html( $mfw_component['name'] ) : esc_html( $mfw_component['id'] ) ); ?>" type="checkbox" id="<?php echo esc_html( $mfw_component['id'] ); ?>" value="on" class="mdc-switch__native-control <?php echo ( isset( $mfw_component['class'] ) ? esc_attr( $mfw_component['class'] ) : '' ); ?>" role="switch" aria-checked="
							<?php
							if ( 'on' == $mfw_component['value'] ) {
								echo 'true';
							} else {
								echo 'false';
							}
							?>
											"
											<?php checked( $mfw_component['value'], 'on' ); ?>
											>
										</div>
									</div>
								</div>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo ( isset( $mfw_component['description'] ) ? esc_attr( $mfw_component['description'] ) : '' ); ?></div>
								</div>
								
							</div>
						</div>
							<?php
							break;

						case 'button':
							?>
							<div class="wps-form-group">
								<div class="wps-form-group__label"></div>
								<div class="wps-form-group__control">
									<button class="mdc-button mdc-button--raised" name= "<?php echo ( isset( $mfw_component['name'] ) ? esc_html( $mfw_component['name'] ) : esc_html( $mfw_component['id'] ) ); ?>"
										id="<?php echo esc_attr( $mfw_component['id'] ); ?>"> <span class="mdc-button__ripple"></span>
										<span class="mdc-button__label <?php echo ( isset( $mfw_component['class'] ) ? esc_attr( $mfw_component['class'] ) : '' ); ?>"><?php echo ( isset( $mfw_component['button_text'] ) ? esc_html( $mfw_component['button_text'] ) : '' ); ?></span>
									</button>
								</div>
							</div>
							<?php
							break;
						case 'multi-button':
							?>
							<button class="mdc-button mdc-button--raised" name= "<?php echo ( isset( $mfw_component['name'] ) ? esc_html( $mfw_component['name'] ) : esc_html( $mfw_component['id'] ) ); ?>"
								id="<?php echo esc_attr( $mfw_component['id'] ); ?>"> <span class="mdc-button__ripple"></span>
								<span class="mdc-button__label <?php echo ( isset( $mfw_component['class'] ) ? esc_attr( $mfw_component['class'] ) : '' ); ?>"><?php echo ( isset( $mfw_component['button_text'] ) ? esc_html( $mfw_component['button_text'] ) : '' ); ?></span>
							</button>
							<?php
							break;

						case 'button_two':
							?>
							<div class="wps-form-group wps-mfw-<?php echo esc_attr( $mfw_component['type'] ); ?>">
								<div class="wps-form-group__label">
									<label for="<?php echo esc_attr( $mfw_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $mfw_component['title'] ) ? esc_html( $mfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
								</div>
								<button class="mdc-button mdc-button--raised" name= "<?php echo ( isset( $mfw_component['name'] ) ? esc_html( $mfw_component['name'] ) : esc_html( $mfw_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $mfw_component['id'] ); ?>"> <span class="mdc-button__ripple"></span>
									<span class="mdc-button__label <?php echo ( isset( $mfw_component['class'] ) ? esc_attr( $mfw_component['class'] ) : '' ); ?>"><?php echo ( isset( $mfw_component['button_text'] ) ? esc_html( $mfw_component['button_text'] ) : '' ); ?></span>
								</button>
								<input type="hidden" name="attachment_id" class="wp_attachment_id" value="" />
								<img src="<?php echo esc_url( get_option( 'wps_mfw_image_text_url' ) ); ?>" class="wps_mfw_show_welcome_image" width="200" height="130">
							</div>
							<?php
							break;

						case 'multi':
							?>
							<div class="wps-form-group wps-mfw-<?php echo esc_attr( $mfw_component['type'] ); ?>">
								<div class="wps-form-group__label">
									<label for="<?php echo esc_attr( $mfw_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $mfw_component['title'] ) ? esc_html( $mfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
									</div>
									<div class="wps-form-group__control">
							<?php
							foreach ( $mfw_component['value'] as $component ) {
								?>
											<label class="mdc-text-field mdc-text-field--outlined">
												<span class="mdc-notched-outline">
													<span class="mdc-notched-outline__leading"></span>
													<span class="mdc-notched-outline__notch">
								<?php if ( 'number' != $component['type'] ) { ?>
													<span class="mdc-floating-label" id="my-label-id"><?php echo ( isset( $mfw_component['placeholder'] ) ? esc_attr( $mfw_component['placeholder'] ) : '' ); ?></span>
							<?php } ?>
													</span>
													<span class="mdc-notched-outline__trailing"></span>
												</span>
												<input 
												class="mdc-text-field__input <?php echo ( isset( $mfw_component['class'] ) ? esc_attr( $mfw_component['class'] ) : '' ); ?>" 
												name="<?php echo ( isset( $mfw_component['name'] ) ? esc_html( $mfw_component['name'] ) : esc_html( $mfw_component['id'] ) ); ?>"
												id="<?php echo esc_attr( $component['id'] ); ?>"
												type="<?php echo esc_attr( $component['type'] ); ?>"
												value="<?php echo ( isset( $mfw_component['value'] ) ? esc_attr( $mfw_component['value'] ) : '' ); ?>"
												placeholder="<?php echo ( isset( $mfw_component['placeholder'] ) ? esc_attr( $mfw_component['placeholder'] ) : '' ); ?>"
								<?php echo esc_attr( ( 'number' === $component['type'] ) ? 'max=10 min=0' : '' ); ?>
												>
											</label>
							<?php } ?>
									<div class="mdc-text-field-helper-line">
									</div>
								</div>
							</div>
								<?php
							break;
						case 'color':
						case 'date':
						case 'file':
							?>
							<div class="wps-form-group wps-mfw-<?php echo esc_attr( $mfw_component['type'] ); ?>">
								<div class="wps-form-group__label">
									<label for="<?php echo esc_attr( $mfw_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $mfw_component['title'] ) ? esc_html( $mfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
								</div>
								<div class="wps-form-group__control">
									<label>
										<input 
										class="<?php echo ( isset( $mfw_component['class'] ) ? esc_attr( $mfw_component['class'] ) : '' ); ?>" 
										name="<?php echo ( isset( $mfw_component['name'] ) ? esc_html( $mfw_component['name'] ) : esc_html( $mfw_component['id'] ) ); ?>"
										id="<?php echo esc_attr( $mfw_component['id'] ); ?>"
										type="<?php echo esc_attr( $mfw_component['type'] ); ?>"
										value="<?php echo ( isset( $mfw_component['value'] ) ? esc_attr( $mfw_component['value'] ) : '' ); ?>"
									<?php echo esc_html( ( 'date' === $mfw_component['type'] ) ? 'max=' . gmdate( 'Y-m-d', strtotime( gmdate( 'Y-m-d', mktime() ) . ' + 365 day' ) ) . 'min=' . gmdate( 'Y-m-d' ) . '' : '' ); ?>
										>
									</label>
									<div class="mdc-text-field-helper-line">
										<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo ( isset( $mfw_component['description'] ) ? esc_attr( $mfw_component['description'] ) : '' ); ?></div>
									</div>
								</div>
							</div>
							<?php
							break;
						case 'wp_editor':
							?>
							<div class="wps-form-group">
							<div class="wps-form-group__label">
								<label for="" class="wps-form-label"><?php echo ( isset( $mfw_component['title'] ) ? esc_html( $mfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
								<?php
								$instance = Membership_For_Woocommerce_Global_Functions::get();
								$instance->tool_tip( ( isset( $mfw_component['description'] ) ? esc_attr( $mfw_component['description'] ) : '' ) );
								?>
							</div>
							<div class="wps-form-group__control">

							<?php

							$description = ! empty( $mfw_component['description'] ) ? $mfw_component['description'] : '';

							$content   = ! empty( $mfw_component['value'] ) ? $mfw_component['value'] : '';

							$editor_id = ! empty( $mfw_component['id'] ) ? $mfw_component['id'] : '';

							$args = ! empty( $mfw_component['args'] ) ? $mfw_component['args'] : array();

							wp_editor( $content, $editor_id, $args );

							?>

</div>
</div>

							<?php
							break;

						case 'file_upload':
							?>
	<div class="wps-form-group">
							<div class="wps-form-group__label">
								<label for="" class="wps-form-label"><?php echo ( isset( $mfw_component['title'] ) ? esc_html( $mfw_component['title'] ) : '' ); ?></label>
								<?php
								$instance = Membership_For_Woocommerce_Global_Functions::get();
								$instance->tool_tip( ( isset( $mfw_component['description'] ) ? esc_attr( $mfw_component['description'] ) : '' ) );
								?>
							</div>
							<div class="wps-form-group__control">

							<?php

							$description = ! empty( $mfw_component['description'] ) ? esc_attr( $mfw_component['description'] ) : '';
							$wps_membership_invoice_logo   = ! empty( $mfw_component['value'] ) ? esc_attr( $mfw_component['value'] ) : '';

							$upload_btn_cls = empty( $wps_membership_invoice_logo ) ? '' : 'button_hide';
							$remove_btn_cls = ! empty( $wps_membership_invoice_logo ) ? '' : 'button_hide';
							$file_upload_id = ! empty( $mfw_component['id'] ) ? $mfw_component['id'] : '';

							?>
							<input type="hidden" id="wps_membership_invoice_logo" name="wps_membership_invoice_logo" value="<?php echo esc_html( $wps_membership_invoice_logo ); ?>">
								<input type="button" id="upload_img" class="button <?php echo esc_html( $upload_btn_cls ); ?>" value="<?php esc_html_e( 'Upload Logo', 'membership-for-woocommerce' ); ?>">
								<input type="button" id="remove_img" class="button <?php echo esc_html( $remove_btn_cls ); ?>" value="<?php esc_html_e( 'Remove Logo', 'membership-for-woocommerce' ); ?>">
								<div id="img_thumbnail">
														
								<img src="<?php echo esc_html( ! empty( $wps_membership_invoice_logo ) ? $wps_membership_invoice_logo : ' ' ); ?>" alt='Logo Image' onerror=this.src="<?php echo esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/placeholder.png' ); ?>" width="60px" height="60px"/>
															
								</div>
							</div>
							</div>
							<?php
							break;
						case 'submit':
							?>
						<tr valign="top">
							<td scope="row">
								<input type="submit" class="button button-primary" 
								name="<?php echo ( isset( $mfw_component['name'] ) ? esc_html( $mfw_component['name'] ) : esc_html( $mfw_component['id'] ) ); ?>"
								id="<?php echo esc_attr( $mfw_component['id'] ); ?>"
								class="<?php echo ( isset( $mfw_component['class'] ) ? esc_attr( $mfw_component['class'] ) : '' ); ?>"
								value="<?php echo esc_attr( $mfw_component['button_text'] ); ?>"
								/>
							</td>
						</tr>
							<?php
							break;

						default:
							break;
					}
				}
			}
		}
	}

}
