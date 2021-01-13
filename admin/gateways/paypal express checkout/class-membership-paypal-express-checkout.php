<?php
/**
 * Provides a PayPal Express Checkout Gateway support for Membership For Woocommerce.
 *
 * @package Membership_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {

	die();
}

/**
 * Adding Paypal Express Checkout Gateway support for Membership.
 */
class Membership_Paypal_Express_Checkout extends WC_Payment_Gateway {

	/**
	 * Constructor function.
	 */
	public function __construct() {

		$this->id                 = 'membership-paypal-express-checkout';
		$this->method_title       = __( 'PayPal Checkout( Membership )', 'membership-for-woocommerce' );
		$this->method_description = __( 'Allow customers to conveniently checkout directly with PayPal.', 'membership-for-woocommerce' );

		$this->payment_action    = 'sale';
		$this->use_smart_buttons = 'yes';
		$this->instant_payments  = 'yes';

		$this->title       = $this->method_title;
		$this->description = '';
		$this->enabled     = $this->get_option( 'enabled', 'yes' );
		$this->button_size = $this->get_option( 'button_size', 'large' );
		$this->test_mode   = $this->get_option( 'test_mode', 'yes' );

		if ( 'yes' === $this->test_mode ) {

			$this->api_username  = $this->get_option( 'sandbox_api_username' );
			$this->api_password  = $this->get_option( 'sandbox_api_password' );
			$this->api_signature = $this->get_option( 'sandbox_api_signature' );

		} else {

			$this->api_username  = $this->get_option( 'api_username' );
			$this->api_password  = $this->get_option( 'api_password' );
			$this->api_signature = $this->get_option( 'api_signature' );

		}

		$this->debug           = 'yes' === $this->get_option( 'debug', 'no' );
		$this->invoice_prefix  = $this->get_option( 'invoice_prefix', '' );
		$this->require_billing = 'yes' === $this->get_option( 'require_billing', 'no' );

		$this->init_form_fields();
		$this->init_settings();

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

	}

	/**
	 * Membership paypal gateway form fields.
	 */
	public function init_form_fields() {

		$this->form_fields = include dirname( dirname( __FILE__ ) ) . '/paypal express checkout/settings-paypal-express-checkout.php';
	}

}
