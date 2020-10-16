<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin
 * @author     Make Web Better <plugins@makewebbetter.com>
 */
class Membership_For_Woocommerce_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name  The name of this plugin.
	 * @param      string $version      The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Membership_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Membership_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/membership-for-woocommerce-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Membership_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Membership_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Adding Membership menu page.
	 *
	 * @since 1.0.0
	 */
	public function mwb_memberships_for_woo_admin_menu() {

		add_menu_page(
			esc_html__( 'Membership', 'membership-for-woocommerce' ),
			esc_html__( 'Membership', 'membership-for-woocommerce' ),
			'manage_woocommerce',
			'membership-for-woocommerce-setting',
			array( $this, 'mwb_membership_for_woo_add_backend' ),
			'dashicons-businessperson',
			57,
		);

		// Add submenu for membership settings.
		add_submenu_page( 'membership-for-woocommerce-setting', esc_html__( 'Membership Settings', 'membership-for-woocommerce' ), esc_html__( 'Membership Settings', 'membership-for-woocommerce' ), 'manage_options', 'membership-for-woocommerce-setting' );

		// Add submenu for members list.
		add_submenu_page( 'membership-for-woocommerce-setting', esc_html__( 'Members', 'membership-for-woocommerce' ), esc_html__( 'Members', 'membership-for-woocommerce' ), 'manage_options', 'membership-for-woocommerce-members', array( $this, 'add_submenu_page_members_callback' ) );
	}

	/**
	 * Callback function for Membership menu page.
	 */
	public function mwb_membership_for_woo_add_backend() {

		require_once plugin_dir_path( __FILE__ ) . '/partials/membership-for-woocommerce-admin-display.php';

	}

	/**
	 * Callback funtion for submenu members page.
	 */
	public function add_submenu_page_members_callback() {

		require_once MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH . 'admin/members/membership-for-woocommerce-members.php';

	}
}
