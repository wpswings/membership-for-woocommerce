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

		$this->id                 = 'membership-paypal-smart-buttons';
		$this->method_title       = __( 'PayPal Checkout( Membership )', 'membership-for-woocommerce' );
		$this->method_description = __( 'Allow customers to conveniently checkout directly with PayPal.', 'membership-for-woocommerce' );

		$this->payment_action  = 'capture';
		$this->currency_code   = get_woocommerce_currency();
		$this->vault           = false;
		$this->component       = 'buttons';
		$this->disable_funding = 'card';

		$this->title       = $this->method_title;
		$this->description = $this->get_option( 'description' );
		$this->enabled     = $this->get_option( 'enabled', 'yes' );
		$this->test_mode   = $this->get_option( 'test_mode', 'yes' );

		if ( 'yes' === $this->test_mode ) {

			$this->client_id = $this->get_option( 'sb_client_id' );

		} else {

			$this->client_id = $this->get_option( 'live_client_id' );
		}

		$this->debug          = 'yes' === $this->get_option( 'debug', 'no' );
		$this->invoice_prefix = $this->get_option( 'invoice_prefix', '' );

		$this->button_layout = $this->get_option( 'button_layout', 'vertical' );
		$this->button_color  = $this->get_option( 'button_color', 'gold' );
		$this->button_shape  = $this->get_option( 'button_shape', 'rect' );
		$this->button_label  = $this->get_option( 'button_label', 'paypal' );

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
