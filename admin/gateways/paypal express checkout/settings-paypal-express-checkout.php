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
	'enabled'             => array(
		'title'       => __( 'Enable/Disable', 'membership-for-woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable PayPal Smart buttons', 'membership-for-woocommerce' ),
		'description' => __( 'This enables PayPal Smart buttons which allows customers to checkout directly via PayPal.', 'membership-for-woocommerce' ),
		'desc_tip'    => true,
		'default'     => 'yes',
	),

	'title'               => array(
		'title'       => __( 'Title', 'membership-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'This controls the title which the user sees during checkout.', 'membership-for-woocommerce' ),
		'default'     => __( 'PayPal', 'membership-for-woocommerce' ),
		'desc_tip'    => true,
	),

	'description'         => array(
		'title'       => __( 'Description', 'membership-for-woocommerce' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'This controls the description which the user sees during checkout.', 'membership-for-woocommerce' ),
		'default'     => __( 'Pay via your PayPal account', 'membership-for-woocommerce' ),
	),

	'account_settings'    => array(
		'title'       => __( 'Account Settings', 'membership-for-woocommerce' ),
		'type'        => 'title',
		'description' => 'Select your environment and fill up the details',
	),

	'test_mode'           => array(
		'title'       => __( 'Environment', 'membership-for-woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Sandbox mode', 'membership-for-woocommerce' ),
		'description' => __( 'This specifies whether you will process live transactions, or you will process simulated transactions using the PayPal Sandbox.', 'membership-for-woocommerce' ),
		'default'     => 'yes',
		'desc_tip'    => true,
	),

	'live_credentials'    => array(
		'title'       => __( 'Live Credentials', 'membership-for-woocommerce' ),
		'type'        => 'title',
		'description' => __( 'Fill out your Paypal Live credentials below.', 'membership-for-woocommerce' ),
	),

	'live_client_id'      => array(
		'title'       => __( 'Live Client ID', 'membership-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Get your Client\'s ID credentials from PayPal.', 'membership-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	),

	'sandbox_credentials' => array(
		'title'       => __( 'Sandbox Credentials', 'membership-for-woocommerce' ),
		'type'        => 'title',
		'description' => __( 'Fill out your Sandbox credentials below.', 'membership-for-woocommerce' ),
	),

	'sb_client_id'        => array(
		'title'       => __( 'Sandbox Client ID', 'membership-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Get your Sandbox Client\'s ID credentials from PayPal.', 'membership-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	),

	'advanced'            => array(
		'title'       => __( 'Advanced Settings', 'membership-for-woocommerce' ),
		'type'        => 'title',
		'description' => '',
	),

	'debug'               => array(
		'title'       => __( 'Debug', 'membership-for-woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable Logging', 'membership-for-woocommerce' ),
		'default'     => 'no',
		'desc_tip'    => true,
		'description' => __( 'Log PayPal events, such as IPN requests.', 'membership-for-woocommerce' ),
	),

	'invoice_prefix'      => array(
		'title'       => __( 'Invoice Prefix', 'membership-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Please enter a prefix for your invoice numbers. If you use your PayPal account for multiple stores ensure this prefix is unique as PayPal will not allow orders with the same invoice number.', 'membership-for-woocommerce' ),
		'default'     => 'WC-',
		'desc_tip'    => true,
	),

	'button_settings'     => array(
		'title'       => __( 'Button Settings', 'membership-for-woocommerce' ),
		'type'        => 'title',
		'description' => __( 'Customize the appearance of PayPal Smart buttons on your site.', 'membership-for-woocommerce' ),
	),

	'button_layout'       => array(
		'title'       => __( 'Button layout', 'membership-for-woocommerce' ),
		'type'        => 'select',
		'class'       => 'wc-enhanced-select membership-smart-button-layout',
		'default'     => 'vertical',
		'desc_tip'    => true,
		'description' => __( 'Controls the layout of Paypal button. Use "Vertical" to leverage PayPal\'s recognition and preference, or change it to match your site design or aesthetic.', 'membership-for-woocommerce' ),
		'options'     => array(
			'vertical'   => __( 'Vertical (Recommended)', 'membership-for-woocommerce' ),
			'horizontal' => __( 'Horizontal', 'membership-for-woocommerce' ),
		),
	),

	'button_color'        => array(
		'title'       => __( 'Button Color', 'membership-for-woocommerce' ),
		'type'        => 'select',
		'class'       => 'wc-enhanced-select membership-smart-button-color',
		'default'     => 'gold',
		'desc_tip'    => true,
		'description' => __( 'Controls the background color of the primary button. Use "Gold" to leverage PayPal\'s recognition and preference, or change it to match your site design or aesthetic.', 'membership-for-woocommerce' ),
		'options'     => array(
			'gold'   => __( 'Gold (Recommended)', 'membership-for-woocommerce' ),
			'blue'   => __( 'Blue', 'membership-for-woocommerce' ),
			'silver' => __( 'Silver', 'membership-for-woocommerce' ),
			'white'  => __( 'White', 'membership-for-woocommerce' ),
			'black'  => __( 'Black', 'membership-for-woocommerce' ),
		),
	),

	'button_shape'        => array(
		'title'       => __( 'Button Shape', 'membership-for-woocommerce' ),
		'type'        => 'select',
		'class'       => 'wc-enhanced-select membership-smart-button-shape',
		'default'     => 'rect',
		'desc_tip'    => true,
		'description' => __( 'The pill-shaped button\'s unique and powerful shape signifies PayPal in people\'s minds. Use the rectangular button as an alternative when pill-shaped buttons might pose design challenges.', 'membership-for-woocommerce' ),
		'options'     => array(
			'rect' => __( 'Rectangle (Recommended)', 'membership-for-woocommerce' ),
			'pill' => __( 'Pill', 'membership-for-woocommerce' ),
		),
	),

	'button_label'        => array(
		'title'       => __( 'Button Label', 'membership-for-woocommerce' ),
		'type'        => 'select',
		'class'       => 'wc-enhanced-select membership-smart-button-label',
		'default'     => 'paypal',
		'desc_tip'    => true,
		'description' => __( 'This controls the label on the primary button.', 'membership-for-woocommerce' ),
		'options'     => array(
			'paypal'   => __( 'PayPal (Recommended)', 'membership-for-woocommerce' ),
			'checkout' => __( 'PayPal Checkout', 'membership-for-woocommerce' ),
			'buynow'   => __( 'PayPal Buy Now', 'membership-for-woocommerce' ),
			'pay'      => __( 'Pay with PayPal', 'membership-for-woocommerce' ),
		),
	),
);

return apply_filters( 'membership_paypal_express_checkout_settings', $settings );


