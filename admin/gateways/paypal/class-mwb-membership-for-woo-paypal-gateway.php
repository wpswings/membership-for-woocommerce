<?php
/**
 * Provides a PayPal Express Gateway for Membership For Woocommerce.
 *
 * @package Membership_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {

	die();
}

/**
 * Adding Paypal Payment Gateway support for Membership.
 */
class Mwb_Membership_For_Woo_Paypal_Gateway extends WC_Payment_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id                 = 'membership_for_woo_paypal_gateway';
		$this->method_title       = __( 'Paypal ( Membership )', 'membership-for-woocommerce' );
		$this->method_description = __( 'Safe and Secure method for making payments with Paypal.', 'membership-for-woocommerce' );
		$this->has_fields         = true;
		$this->supports           = array(
			'products',
			'refunds',
		);

		// Load form fields.
		$this->init_form_fields();

		// Load settings.
		$this->init_settings();

		// This action hook save settings.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		// This action hook display admin notices for paypal gateway.
		add_action( 'admin_notices', array( $this, 'mwb_membership_for_woo_admin_notices' ) );

		// This action hook proccess final payment on Thankyou page.
		add_action( 'woocommcerce_thankyou_' . $this->id, array( $this, 'mwb_membership_for_woo_fprocess_final_payment' ) );
	}

	/**
	 * Displaying admin notices and checking availability of membership paypal gateway.
	 */
	public function mwb_membership_for_woo_admin_notices() {

	}

	/**
	 * Membership paypal gateway form fields.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'               => array(
				'type'        => 'checkbox',
				'title'       => __( 'Enable/Disable', 'membership-for-woocommerce' ),
				'label'       => __( 'Enable or Disable Paypal ( Membership )', 'membership-for-woocommerce' ),
				'description' => __( 'Enable or Diable Paypal gateway for Membership', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
				'default'     => 'no',
			),
			'title'                 => array(
				'type'        => 'text',
				'title'       => __( 'Title', 'membership-for-woocommerce' ),
				'description' => __( 'The title which the will be visible to users on checkout.', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
				'default'     => __( 'Paypal', 'membership-for-woocommerce' ),
			),
			'description'           => array(
				'type'        => 'textaraea',
				'title'       => __( 'Description', 'membership-for-woocommerce' ),
				'description' => __( 'Optional, The description which will be visible to users on checkout', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
				'default'     => __( 'Make your payments with Paypal', 'membership-for-woocommerce' ),
				'id'          => 'woocommerce_mwb-membership-paypal-gateway_description',
				'css'         => 'max-width:400px',
			),
			'charge_type'           => array(
				'type'        => 'select',
				'title'       => __( 'Charge type', 'membership-for-woocommerce' ),
				'description' => __( 'Choose to capture payment at checkout, or authorize only to capture later (when order status is switched to "completed").', 'membership-for-woocommerce' ),
				'options'     => array(
					'SALE'          => __( 'Authorize & Capture', 'membership-for-woocommerce' ),
					'AUTHORIZATION' => __( 'Authorize only', 'membership-for-woocommerce' ),
				),
				'default'     => 'SALE',
				'class'       => 'select',
				'css'         => 'height:40px',
				'desc_tip'    => true,
			),
			'billing_description'   => array(
				'type'        => 'text',
				'title'       => __( 'Billing Description', 'membership-for-woocommerce' ),
				'description' => __( 'The billing description to be displayed on PayPal payment page', 'membership-for-woocommerce' ),
				'default'     => __( 'Do your payment safely and securely', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
			),
			'invoice_prefix'        => array(
				'type'        => 'text',
				'title'       => __( 'Invoice prefix', 'membership-for-woocommerce' ),
				'description' => __( 'Please enter a prefix for your invoice numbers. If you use your PayPal account for multiple stores ensure this prefix is unique as PayPal will not allow orders with the same invoice number.', 'membership-for-woocommcerc' ),
				'default'     => __( 'wc-', 'membership-for-woocommcerce' ),
				'desc_tip'    => true,
			),
			'copy_address'          => array(
				'type'        => 'checkbox',
				'title'       => __( 'Copy Shipping Address from PayPal Account', 'membership-for-woocommerce' ),
				'label'       => __( 'Yes', 'mmebership-for-woocommerce' ),
				'description' => __( 'Allow to copy shipping address from customers paypal account and add to order details.', 'membership-for-woocommerce' ),
				'default'     => 'yes',
				'desc_tip'    => true,
			),
			'testmode'              => array(
				'type'        => 'checkbox',
				'title'       => __( 'Enable Sandbox mode', 'membership-for-woocommerce' ),
				'description' => __( 'Use the paypal sandbox mode in order to verify that everything works fine before going live.', 'membership-for-woocommerce' ),
				'label'       => __( 'Turn on testing', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
				'default'     => 'yes',
			),
			'api_username'          => array(
				'type'        => 'text',
				'title'       => __( 'API Username', 'membership-for-woocommerce' ),
				'description' => __( 'Can be found in your PayPal account > "Profile" > "Profile and settings" > "My selling tools" > "API Access" > "View API Signature".', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
			),
			'api_password'          => array(
				'type'  => 'password',
				'title' => __( 'API Password', 'membership-for-woocommerce' ),
			),
			'api_signature'         => array(
				'type'  => 'text',
				'title' => __( 'API Signature', 'membership-for-woocommerce' ),
			),
			'sandbox_api_username'  => array(
				'type'        => 'text',
				'title'       => __( 'Sandbox API Username', 'membership-for-woocommerce' ),
				'description' => __( 'Can be found in your <u>Sandbox</u> PayPal account > "Profile" > "Profile and settings" > "My selling tools" > "API Access" > "View API Signature".', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
			),
			'sandbox_api_password'  => array(
				'type'  => 'password',
				'title' => __( 'Sandbox API Password', 'membership-for-woocommerce' ),
			),
			'sandbox_api_signature' => array(
				'type'  => 'text',
				'title' => __( 'Sandbox API Signature', 'membership-for-woocommerce' ),
			),
			'logging'               => array(
				'title'       => __( 'Logging', 'membership-for-woocommerce' ),
				'label'       => __( 'Log debug messages', 'membership-for-woocommerce' ),
				'type'        => 'checkbox',
				'description' => __( 'Save debug messages to the WooCommerce System Status log.', 'membership-for-woocommerce' ),
				'default'     => 'yes',
				'desc_tip'    => true,
			),

		);

	}

	


}
