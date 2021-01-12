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

		$this->id                 = 'membership-paypal-gateway';
		$this->method_title       = __( 'PayPal Checkout( Membership )', 'membership-for-woocommerce' );
		$this->method_description = __( 'Allow customers to conveniently checkout directly with PayPal.', 'membership-for-woocommerce' );

		$this->init_form_fields();
		$this->init_settings();

	}

	/**
	 * Membership paypal gateway form fields.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'                    => array(
				'title'       => __( 'Enable/Disable', 'membership-for-woocommerce' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable PayPal Checkout', 'membership-for-woocommerce' ),
				'description' => __( 'This enables PayPal Checkout which allows customers to checkout directly via PayPal from your cart page.', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
				'default'     => 'yes',
			),

			'title'                      => array(
				'title'       => __( 'Title', 'membership-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'membership-for-woocommerce' ),
				'default'     => __( 'PayPal', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
			),
			'description'                => array(
				'title'       => __( 'Description', 'membership-for-woocommerce' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => __( 'This controls the description which the user sees during checkout.', 'membership-for-woocommerce' ),
				'default'     => __( 'Pay via PayPal; you can pay with your credit card if you don\'t have a PayPal account.', 'membership-for-woocommerce' ),
			),

			'account_settings'           => array(
				'title'       => __( 'Account Settings', 'membership-for-woocommerce' ),
				'type'        => 'title',
				'description' => '',
			),
			'environment'                => array(
				'title'       => __( 'Environment', 'membership-for-woocommerce' ),
				'type'        => 'select',
				'class'       => 'wc-enhanced-select',
				'description' => __( 'This setting specifies whether you will process live transactions, or whether you will process simulated transactions using the PayPal Sandbox.', 'membership-for-woocommerce' ),
				'default'     => 'live',
				'desc_tip'    => true,
				'options'     => array(
					'live'    => __( 'Live', 'membership-for-woocommerce' ),
					'sandbox' => __( 'Sandbox', 'membership-for-woocommerce' ),
				),
			),

			'api_credentials'            => array(
				'title'       => __( 'API Credentials', 'membership-for-woocommerce' ),
				'type'        => 'title',
				'description' => $api_creds_text,
			),
			'api_username'               => array(
				'title'       => __( 'Live API Username', 'membership-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Get your API credentials from PayPal.', 'membership-for-woocommerce' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'api_password'               => array(
				'title'       => __( 'Live API Password', 'membership-for-woocommerce' ),
				'type'        => 'password',
				'description' => __( 'Get your API credentials from PayPal.', 'membership-for-woocommerce' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'api_signature'              => array(
				'title'       => __( 'Live API Signature', 'membership-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Get your API credentials from PayPal.', 'membership-for-woocommerce' ),
				'default'     => '',
				'desc_tip'    => true,
				'placeholder' => __( 'Optional if you provide a certificate below', 'membership-for-woocommerce' ),
			),
			'api_certificate'            => array(
				'title'       => __( 'Live API Certificate', 'membership-for-woocommerce' ),
				'type'        => 'file',
				'description' => $this->get_certificate_setting_description(),
				'default'     => '',
			),
			'api_subject'                => array(
				'title'       => __( 'Live API Subject', 'membership-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'If you\'re processing transactions on behalf of someone else\'s PayPal account, enter their email address or Secure Merchant Account ID (also known as a Payer ID) here. Generally, you must have API permissions in place with the other account in order to process anything other than "sale" transactions for them.', 'membership-for-woocommerce' ),
				'default'     => '',
				'desc_tip'    => true,
				'placeholder' => __( 'Optional', 'membership-for-woocommerce' ),
			),
			'sandbox_api_credentials'    => array(
				'title'       => __( 'Sandbox API Credentials', 'membership-for-woocommerce' ),
				'type'        => 'title',
				'description' => $sandbox_api_creds_text,
			),
			'sandbox_api_username'       => array(
				'title'       => __( 'Sandbox API Username', 'membership-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Get your API credentials from PayPal.', 'membership-for-woocommerce' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'sandbox_api_password'       => array(
				'title'       => __( 'Sandbox API Password', 'membership-for-woocommerce' ),
				'type'        => 'password',
				'description' => __( 'Get your API credentials from PayPal.', 'membership-for-woocommerce' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'sandbox_api_signature'      => array(
				'title'       => __( 'Sandbox API Signature', 'membership-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Get your API credentials from PayPal.', 'membership-for-woocommerce' ),
				'default'     => '',
				'desc_tip'    => true,
				'placeholder' => __( 'Optional if you provide a certificate below', 'membership-for-woocommerce' ),
			),
			'sandbox_api_certificate'    => array(
				'title'       => __( 'Sandbox API Certificate', 'membership-for-woocommerce' ),
				'type'        => 'file',
				'description' => $this->get_certificate_setting_description( 'sandbox' ),
				'default'     => '',
			),
			'sandbox_api_subject'        => array(
				'title'       => __( 'Sandbox API Subject', 'membership-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'If you\'re processing transactions on behalf of someone else\'s PayPal account, enter their email address or Secure Merchant Account ID (also known as a Payer ID) here. Generally, you must have API permissions in place with the other account in order to process anything other than "sale" transactions for them.', 'membership-for-woocommerce' ),
				'default'     => '',
				'desc_tip'    => true,
				'placeholder' => __( 'Optional', 'membership-for-woocommerce' ),
			),
	}

}
