<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
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
 * @author     MakeWebBetter <plugins@makewebbetter.com>
 */
class Membership_For_Woocommerce {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Membership_For_Woocommerce_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'MEMBERSHIP_FOR_WOOCOMMERCE_VERSION' ) ) {
			$this->version = MEMBERSHIP_FOR_WOOCOMMERCE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'membership-for-woocommerce';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Membership_For_Woocommerce_Loader. Orchestrates the hooks of the plugin.
	 * - Membership_For_Woocommerce_i18n. Defines internationalization functionality.
	 * - Membership_For_Woocommerce_Admin. Defines all hooks for the admin area.
	 * - Membership_For_Woocommerce_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-membership-for-woocommerce-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-membership-for-woocommerce-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-membership-for-woocommerce-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-membership-for-woocommerce-public.php';

		/**
		 * The class responsible for defining all global functions for the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-membership-for-woocommerce-global-functions.php';

		/**
		 * The class responsible for defining all function for membership checkout validations.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-membership-checkout-validation.php';

		/**
		 * The class responsible for defining all function for membership checkout validations.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-membership-activity-helper.php';

		/**
		 * The class responsible for defining all actions that occur in the onboarding the site data
		 * in the admin side of the site.
		 */
		! class_exists( 'Makewebbetter_Onboarding_Helper' ) && require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-makewebbetter-onboarding-helper.php';
		$this->onboard = new Makewebbetter_Onboarding_Helper();

		$this->loader = new Membership_For_Woocommerce_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Membership_For_Woocommerce_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Membership_For_Woocommerce_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Membership_For_Woocommerce_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_filter( 'woocommerce_screen_ids', $plugin_admin, 'mwb_membership_set_wc_screen_ids' );

		// Add admin menu.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'mwb_memberships_for_woo_admin_menu' );

		// Admin side ajax.
		$this->loader->add_action( 'wp_ajax_search_products_for_membership', $plugin_admin, 'search_products_for_membership' );
		$this->loader->add_action( 'wp_ajax_search_product_categories_for_membership', $plugin_admin, 'search_product_categories_for_membership' );
		$this->loader->add_action( 'wp_ajax_mwb_get_membership_content', $plugin_admin, 'mwb_get_membership_content' );
		$this->loader->add_action( 'wp_ajax_csv_file_upload', $plugin_admin, 'csv_file_upload' ); // Import CSV.
		$this->loader->add_action( 'wp_ajax_membership_get_states', $plugin_admin, 'membership_get_states' );
		$this->loader->add_action( 'wp_ajax_mwb_get_member_content', $plugin_admin, 'mwb_get_member_content' );

		// Add custom post type.
		$this->loader->add_action( 'init', $plugin_admin, 'mwb_membership_for_woo_cpt_members' );
		$this->loader->add_action( 'init', $plugin_admin, 'mwb_membership_for_woo_cpt_membership' );

		// Adding custom columns.
		$this->loader->add_filter( 'manage_mwb_cpt_members_posts_columns', $plugin_admin, 'mwb_membership_for_woo_cpt_columns_members' );
		$this->loader->add_filter( 'manage_mwb_cpt_membership_posts_columns', $plugin_admin, 'mwb_membership_for_woo_cpt_columns_membership' );

		// Save meta box fields.
		$this->loader->add_action( 'save_post_mwb_cpt_membership', $plugin_admin, 'mwb_membership_for_woo_save_fields' );
		$this->loader->add_action( 'edit_post_mwb_cpt_members', $plugin_admin, 'mwb_membership_save_member_fields' );

		// Populating columns.
		$this->loader->add_action( 'manage_mwb_cpt_members_posts_custom_column', $plugin_admin, 'mwb_membership_for_woo_fill_columns_members', 10, 2 );
		$this->loader->add_action( 'manage_mwb_cpt_membership_posts_custom_column', $plugin_admin, 'mwb_membership_for_woo_fill_columns_membership', 10, 2 );

		// Add CSV export button.
		$this->loader->add_action( 'restrict_manage_posts', $plugin_admin, 'mwb_membership_for_woo_export_members', 10 );
		$this->loader->add_action( 'restrict_manage_posts', $plugin_admin, 'mwb_membership_for_woo_export_membership', 10 );

		// Download CSV.
		$this->loader->add_action( 'init', $plugin_admin, 'mwb_membership_for_woo_export_csv_members' );
		$this->loader->add_action( 'init', $plugin_admin, 'mwb_membership_for_woo_export_csv_membership' );

		// Creating membership method.
		$this->loader->add_action( 'woocommerce_shipping_init', $plugin_admin, 'mwb_membership_for_woo_create_shipping_method' );
		$this->loader->add_filter( 'woocommerce_shipping_methods', $plugin_admin, 'mwb_membership_for_woo_add_shipping_method' );

		// Supported Gateways column.
		$this->loader->add_filter( 'woocommerce_payment_gateways_setting_columns', $plugin_admin, 'mwb_membership_for_woo_gateway_support_column' );

		// Supported gateways content.
		$this->loader->add_action( 'woocommerce_payment_gateways_setting_column_mwb_membership_gateways', $plugin_admin, 'mwb_membership_for_woo_gateway_column_content' );

		// Add Membership gateways.
		$this->loader->add_filter( 'woocommerce_payment_gateways', $plugin_admin, 'mwb_membership_for_supported_gateways' );

		// Include supported gateway classes.
		$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'mwb_membership_for_woo_plugins_loaded' );

		// Display page states of membership default page.
		$this->loader->add_filter( 'display_post_states', $plugin_admin, 'mwb_membership_default_page_states', 10, 2 );

		// Remove submenu page.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'mwb_membership_remove_submenu' );

		// Distraction free page for membership plans page.
		$this->loader->add_filter( 'page_template', $plugin_admin, 'mwb_membership_plan_page_template' );

		// Hide payment gateways.
		$this->loader->add_filter( 'woocommerce_available_payment_gateways', $plugin_admin, 'mwb_membership_hide_payment_gateway', 100, 1 );

		// Remove title & editor field.
		$this->loader->add_action( 'init', $plugin_admin, 'membership_for_woo_remove_fields' );

		// Admin notice about free shipping.
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'mwb_membership_shipping_notice' );

		// Remove bulk action for members.
		$this->loader->add_filter( 'bulk_actions-edit-mwb_cpt_members', $plugin_admin, 'membership_for_woo_members_remove_bulkaction' );

		// Remove quick edit.
		$this->loader->add_filter( 'post_row_actions', $plugin_admin, 'membership_for_woo_remove_quick_edit', 10, 1 );

		// Add your screen.
		$this->loader->add_filter( 'mwb_helper_valid_frontend_screens', $plugin_admin, 'add_mwb_frontend_screens' );

		// Add Deactivation screen.
		$this->loader->add_filter( 'mwb_deactivation_supported_slug', $plugin_admin, 'add_mwb_deactivation_screens' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		// Creating Instance of the global functions class.
		$global_class = Membership_For_Woocommerce_Global_Functions::get();

		// Getting global options.
		$mwb_membership_global_settings = get_option( 'mwb_membership_global_options', $global_class->default_global_options() );

		// By default plugin will be enabled.
		$mwb_membership_enable_plugin = ! empty( $mwb_membership_global_settings['mwb_membership_enable_plugin'] ) ? $mwb_membership_global_settings['mwb_membership_enable_plugin'] : 'on';

		if ( 'on' === $mwb_membership_enable_plugin ) {

			$plugin_public = new Membership_For_Woocommerce_Public( $this->get_plugin_name(), $this->get_version() );

			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

			// Register Endpoint.
			$this->loader->add_action( 'init', $plugin_public, 'mwb_membership_register_endpoint' );
			// Add query variable.
			$this->loader->add_action( 'query_vars', $plugin_public, 'mwb_membership_endpoint_query_var', 0 );
			// Inserting custom Membership tab.
			$this->loader->add_action( 'woocommerce_account_menu_items', $plugin_public, 'mwb_membership_add_membership_tab' );
			// Add title to Tab page.
			$this->loader->add_filter( 'the_title', $plugin_public, 'mwb_membership_tab_title' );
			// Populate mmbership details tab.
			$this->loader->add_action( 'woocommerce_account_mwb-membership-tab_endpoint', $plugin_public, 'mwb_membership_populate_tab' );

			// Load all defined shortcodes.
			$this->loader->add_action( 'init', $plugin_public, 'mwb_membership_shortcodes' );

			// Make all membership products non-purchasable for non-members.
			$this->loader->add_filter( 'woocommerce_is_purchasable', $plugin_public, 'mwb_membership_for_woo_membership_purchasable', 10, 2 );
			// Display "Buy membership" message for products on detail page.
			$this->loader->add_action( 'woocommerce_single_product_summary', $plugin_public, 'mwb_membership_product_membership_purchase_html', 50 );
			// Hide price of membership products on shop page.
			$this->loader->add_action( 'woocommerce_get_price_html', $plugin_public, 'mwb_membership_for_woo_hide_price_shop_page', 10, 2 );
			// Display "Membership" tag for membership products on shop page.
			$this->loader->add_action( 'woocommerce_shop_loop_item_title', $plugin_public, 'mwb_membership_products_on_shop_page', 10 );

			// Hide other shipping methods, if membership free shipping available.
			$this->loader->add_filter( 'woocommerce_package_rates', $plugin_public, 'mwb_membership_unset_shipping_if_membership_available', 10, 2 );

			// AJAX handlers for receipt upload.
			$this->loader->add_action( 'wp_ajax_upload_receipt', $plugin_public, 'upload_receipt' );
			$this->loader->add_action( 'wp_ajax_nopriv_upload_receipt', $plugin_public, 'upload_receipt' );
			// AJAX handlers for receipt removal.
			$this->loader->add_action( 'wp_ajax_remove_current_receipt', $plugin_public, 'remove_current_receipt' );
			$this->loader->add_action( 'wp_ajax_nopriv_remove_current_receipt', $plugin_public, 'remove_current_receipt' );
			// AJAX handlers for get states.
			$this->loader->add_action( 'wp_ajax_membership_get_states_public', $plugin_public, 'membership_get_states_public' );
			$this->loader->add_action( 'wp_ajax_nopriv_membership_get_states_public', $plugin_public, 'membership_get_states_public' );
			// AJAX handlers for process payment.
			$this->loader->add_action( 'wp_ajax_membership_process_payment', $plugin_public, 'membership_process_payment' );
			$this->loader->add_action( 'wp_ajax_nopriv_membership_process_payment', $plugin_public, 'membership_process_payment' );

			// AJAX handlers to save tnx data.
			$this->loader->add_action( 'wp_ajax_membership_save_transaction', $plugin_public, 'membership_save_transaction' );
			$this->loader->add_action( 'wp_ajax_nopriv_membership_save_transaction', $plugin_public, 'membership_save_transaction' );

			// Cart discount.
			$this->loader->add_action( 'woocommerce_cart_calculate_fees', $plugin_public, 'mwb_membership_add_cart_discount' );

			// Cron even to check membership expiry.
			$this->loader->add_action( 'mwb_membership_expiry_check', $plugin_public, 'mwb_membership_cron_expiry_check' );
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Membership_For_Woocommerce_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
