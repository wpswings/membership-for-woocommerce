<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/public
 * @author     Make Web Better <plugins@makewebbetter.com>
 */
class Membership_For_Woocommerce_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/membership-for-woocommerce-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register Endpoint for Membership plans.
	 */
	public function mwb_membership_register_endpoint() {

		add_rewrite_endpoint( 'mwb-membership-tab', EP_PERMALINK | EP_PAGES );
		flush_rewrite_rules();
	}

	/**
	 * Membership Shortcodes for plan Action and plan Attributes.
	 */
	public function mwb_membership_shortcodes() {

		// Buy now button shortcode.
		add_shortcode( 'mwb_membership_yes', array( $this, 'buy_now_shortcode_content' ) );

		// No thanks button shortcode.
		add_shortcode( 'mwb_membership_no', array( $this, 'reject_shortcode_content' ) );

		// Membership Plan title shortcode.
		add_shortcode( 'mwb_membership_title', array( $this, 'membership_plan_title_content' ) );

		// Membership Plan price shortcode.
		add_shortcode( 'mwb_membership_price', array( $this, 'membership_plan_price_content' ) );

		// Membership Plan Description shortcode.
		add_shortcode( 'mwb_membership_desc', array( $this, 'membership_plan_description_content') );

	}

	/**
	 * Shortcode for offer - Buy now button.
	 * Returns : Link :)
	 */
	public function buy_now_shortcode_content( $atts, $content ) {
		
	}
}

