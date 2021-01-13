<?php
/**
 * Settings field for paypal express checkout.
 *
 * @package Membership_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Settings for PayPal Gateway.
 */
$settings = array(
	'enabled'                 => array(
		'title'       => __( 'Enable/Disable', 'membership-for-woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable PayPal Checkout', 'membership-for-woocommerce' ),
		'description' => __( 'This enables PayPal Checkout which allows customers to checkout directly via PayPal.', 'membership-for-woocommerce' ),
		'desc_tip'    => true,
		'default'     => 'yes',
	),

	'title'                   => array(
		'title'       => __( 'Title', 'membership-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'This controls the title which the user sees during checkout.', 'membership-for-woocommerce' ),
		'default'     => __( 'PayPal', 'membership-for-woocommerce' ),
		'desc_tip'    => true,
	),
	'description'             => array(
		'title'       => __( 'Description', 'membership-for-woocommerce' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'This controls the description which the user sees during checkout.', 'membership-for-woocommerce' ),
		'default'     => __( 'Pay via PayPal; you can pay with your credit card if you don\'t have a PayPal account.', 'membership-for-woocommerce' ),
	),

	'account_settings'        => array(
		'title'       => __( 'Account Settings', 'membership-for-woocommerce' ),
		'type'        => 'title',
		'description' => '',
	),
	'test_mode'               => array(
		'title'       => __( 'Environment', 'membership-for-woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Sandbox mode', 'membership-for-woocommerce' ),
		'description' => __( 'This specifies whether you will process live transactions, or you will process simulated transactions using the PayPal Sandbox.', 'membership-for-woocommerce' ),
		'default'     => 'yes',
		'desc_tip'    => true,
	),

	'api_credentials'         => array(
		'title'       => __( 'API Credentials', 'membership-for-woocommerce' ),
		'type'        => 'title',
		'description' => __( 'Fill out your Paypal Live API credentials below.', 'membership-for-woocommerce' ),
	),
	'api_username'            => array(
		'title'       => __( 'Live API Username', 'membership-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Get your API credentials from PayPal.', 'membership-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	),
	'api_password'            => array(
		'title'       => __( 'Live API Password', 'membership-for-woocommerce' ),
		'type'        => 'password',
		'description' => __( 'Get your API credentials from PayPal.', 'membership-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	),
	'api_signature'           => array(
		'title'       => __( 'Live API Signature', 'membership-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Get your API credentials from PayPal.', 'membership-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	),

	'sandbox_api_credentials' => array(
		'title'       => __( 'Sandbox API Credentials', 'membership-for-woocommerce' ),
		'type'        => 'title',
		'description' => __( 'Fill out your Sandbox API credentials below.', 'membership-for-woocommerce' ),
	),
	'sandbox_api_username'    => array(
		'title'       => __( 'Sandbox API Username', 'membership-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Get your API credentials from PayPal.', 'membership-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	),
	'sandbox_api_password'    => array(
		'title'       => __( 'Sandbox API Password', 'membership-for-woocommerce' ),
		'type'        => 'password',
		'description' => __( 'Get your API credentials from PayPal.', 'membership-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	),
	'sandbox_api_signature'   => array(
		'title'       => __( 'Sandbox API Signature', 'membership-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Get your API credentials from PayPal.', 'membership-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	),

	'advanced'                => array(
		'title'       => __( 'Advanced Settings', 'membership-for-woocommerce' ),
		'type'        => 'title',
		'description' => '',
	),

	'debug'                   => array(
		'title'       => __( 'Debug Log', 'membership-for-woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable Logging', 'membership-for-woocommerce' ),
		'default'     => 'no',
		'desc_tip'    => true,
		'description' => __( 'Log PayPal events, such as IPN requests.', 'membership-for-woocommerce' ),
	),

	'invoice_prefix'          => array(
		'title'       => __( 'Invoice Prefix', 'membership-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Please enter a prefix for your invoice numbers. If you use your PayPal account for multiple stores ensure this prefix is unique as PayPal will not allow orders with the same invoice number.', 'membership-for-woocommerce' ),
		'default'     => 'WC-',
		'desc_tip'    => true,
	),

	'require_billing'         => array(
		'title'       => __( 'Billing Addresses', 'membership-for-woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Require Billing Address', 'membership-for-woocommerce' ),
		'default'     => 'no',
		'description' => sprintf(
			/* Translators: 1) is an <a> tag linking to PayPal's contact info, 2) is the closing </a> tag. */
			__( 'PayPal only returns a shipping address back to the website. To make sure billing address is returned as well, please enable this functionality on your PayPal account by calling %1$sPayPal Technical Support%2$s.', 'membership-for-woocommerce' ),
			'<a href="https://www.paypal.com/us/selfhelp/contact/call">',
			'</a>'
		),
	),

	'require_phone_number'    => array(
		'title'       => __( 'Require Phone Number', 'membership-for-woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Require Phone Number', 'membership-for-woocommerce' ),
		'default'     => 'no',
		'description' => __( 'Require buyer to enter their phone number during checkout if none is provided by PayPal. Disabling this option doesn\'t affect direct Debit or Credit Card payments offered by PayPal.', 'membership-for-woocommerce' ),
	),

	'button_settings'         => array(
		'title'       => __( 'Button Settings', 'membership-for-woocommerce' ),
		'type'        => 'title',
		'description' => __( 'Customize the appearance of PayPal Checkout on your site.', 'membership-for-woocommerce' ),
	),

	'button_color'            => array(
		'title'       => __( 'Button Color', 'membership-for-woocommerce' ),
		'type'        => 'select',
		'class'       => 'wc-enhanced-select woocommerce_ppec_paypal_spb',
		'default'     => 'gold',
		'desc_tip'    => true,
		'description' => __( 'Controls the background color of the primary button. Use "Gold" to leverage PayPal\'s recognition and preference, or change it to match your site design or aesthetic.', 'membership-for-woocommerce' ),
		'options'     => array(
			'gold'   => __( 'Gold (Recommended)', 'membership-for-woocommerce' ),
			'blue'   => __( 'Blue', 'membership-for-woocommerce' ),
			'silver' => __( 'Silver', 'membership-for-woocommerce' ),
			'black'  => __( 'Black', 'membership-for-woocommerce' ),
		),
	),
	'button_shape'            => array(
		'title'       => __( 'Button Shape', 'membership-for-woocommerce' ),
		'type'        => 'select',
		'class'       => 'wc-enhanced-select woocommerce_ppec_paypal_spb',
		'default'     => 'rect',
		'desc_tip'    => true,
		'description' => __( 'The pill-shaped button\'s unique and powerful shape signifies PayPal in people\'s minds. Use the rectangular button as an alternative when pill-shaped buttons might pose design challenges.', 'membership-for-woocommerce' ),
		'options'     => array(
			'pill' => __( 'Pill', 'membership-for-woocommerce' ),
			'rect' => __( 'Rectangle', 'membership-for-woocommerce' ),
		),
	),
	'button_label'            => array(
		'title'       => __( 'Button Label', 'membership-for-woocommerce' ),
		'type'        => 'select',
		'class'       => 'wc-enhanced-select woocommerce_ppec_paypal_spb',
		'default'     => 'paypal',
		'desc_tip'    => true,
		'description' => __( 'This controls the label on the primary button.', 'membership-for-woocommerce' ),
		'options'     => array(
			'paypal'   => __( 'PayPal', 'membership-for-woocommerce' ),
			'checkout' => __( 'PayPal Checkout', 'membership-for-woocommerce' ),
			'buynow'   => __( 'PayPal Buy Now', 'membership-for-woocommerce' ),
			'pay'      => __( 'Pay with PayPal', 'membership-for-woocommerce' ),
		),
	),
);

return apply_filters( 'membership_paypal_express_checkout_settings', $settings );


