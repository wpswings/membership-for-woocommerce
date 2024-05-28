<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/shortcode-widget/elementor/elementor-widget
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 * namespace membership_for_woocommerce_public.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/shortcode-widget/elementor/elementor-widget
 */

namespace ElementorUpsellWidgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Widgets loader for elementor.
 */
class Elementor_Widget {

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @var Plugin The single instance of the class.
	 */
	private static $instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_widgets() {

		require MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'shortcode-widget/elementor/elementor-widget/widgets/class-membership-buy-now-button.php';
		require MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'shortcode-widget/elementor/elementor-widget/widgets/class-membership-thanks-button.php';
		require MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'shortcode-widget/elementor/elementor-widget/widgets/class-membership-plan-title.php';
		require MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'shortcode-widget/elementor/elementor-widget/widgets/class-membership-plan-price.php';
		require MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'shortcode-widget/elementor/elementor-widget/widgets/class-membership-plan-description.php';
		require MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'shortcode-widget/elementor/elementor-widget/widgets/class-membership-registration-form.php';
		require MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'shortcode-widget/elementor/elementor-widget/widgets/class-membership-template.php';

		// Register the plugin widget classes.
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\MEMBERSHIP_BUY_NOW_BUTTON() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\MEMBERSHIP_THANKS_BUTTON() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\MEMBERSHIP_PLAN_TITLE() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\MEMBERSHIP_PLAN_PRICE() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\MEMBERSHIP_PLAN_DESCRIPTION() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\MEMBERSHIP_REGISTRATION_FORM() );
		if ( is_plugin_active( 'membership-for-woocommerce-pro/membership-for-woocommerce-pro.php' ) ) {

			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\MEMBERSHIP_TEMPLATE() );
		}
	}

	/**
	 * Plugin class constructor
	 *
	 * Register plugin action hooks and filters
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		// Register the widgets.
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets' ) );
	}
}

// Instantiate the Widgets class.
Elementor_Widget::instance();
