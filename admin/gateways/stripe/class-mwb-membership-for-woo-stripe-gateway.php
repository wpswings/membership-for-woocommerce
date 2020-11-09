<?php
/**
 * Provides a Stripe Gateway for Membership For Woocommerce.
 *
 * @package Membership_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {

	die();
}

/**
 * Adding Paypal Payment Gateway support for Membership.
 */
class Mwb_Membership_For_Woo_Stripe_Gateway extends WC_Payment_Gateway_CC {


	/**
	 * API secret key.
	 *
	 * @var string
	 */
	private $secret_key;

	/**
	 * API publishable key.
	 *
	 * @var string
	 */
	private $publishable_ke;

	/**
	 * API error messages.
	 *
	 * @var string
	 */
	private $error_message;

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id                 = 'membership-for-woo-stripe-gateway';
		$this->method_title       = __( 'Stripe ( Membership )', 'membership-for-woocommerce' );
		$this->method_title_short = $this->method_title;
		$this->method_description = __( 'Stripe works by adding payment fields on the checkout and then sending the details to Stripe for verification.', 'membership-for-woocommerce' );
		$this->supports           = array(
			'products',
			'refunds',
			'subscriptions',
			'subscription_cancellation',
			'subscription_suspension',
			'subscription_reactivation',
			'subscription_amount_changes',
			'subscription_date_changes',
			'multiple_subscriptions',
		);

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Get setting values.
		$this->title               = $this->get_option( 'title' );
		$this->enabled             = $this->get_option( 'enabled' );
		$this->description         = $this->get_option( 'description' );
		$this->testmode            = $this->get_option( 'testmode' );
		$this->gateway_description = $this->get_option( 'gateway_description' );
		$this->logging             = $this->get_option( 'logging' );
		$this->stripe_icons        = $this->get_option( 'stripe_icons' );

		if ( 'yes' == $this->enabled ) {

			if ( 'yes' == $this->testmode ) {

				$this->publishable_key = $this->get_option( 'test_publishable_key' );
				$this->secret_key      = $this->get_option( 'test_secret_key' );

				$this->description .= '<div class="mwb-membership-stripe-test-mode">' . __( 'TEST MODE ENABLED', 'membership-for-woocommerce' ) . '</div>';
				$this->description .= sprintf( '%s <a href="%s" target="_blank">%s</a> %s', __( 'In test mode, you can use these card numbers, <br> <b>Normal payments</b> : 4242424242424242<br> <b>SCA authentication</b> : 4000002500003155<br>with any CVC and a valid expiration date or check the documentation', 'membership-for-woocommerce' ), 'https://stripe.com/docs/testing', __( 'Testing Stripe', 'membership-for-woocommerce' ), __( 'for more card numbers.', 'membership-for-woocommerce' ) );

				$this->description .= '<p id="mwb-membership-stripe-test-mode-notice">' . __( 'Please use a Dummy email address in Billing email.', 'membership-for-woocommerce' ) . '</p>';

				$this->description = trim( $this->description );

			} else {

				$this->publishable_key = $this->get_option( 'live_publishable_key' );
				$this->secret_key      = $this->get_option( 'live_secret_key' );
			}

			if ( empty( $this->publishable_key ) || empty( $this->secret_key ) ) {

				$this->enabled = 'no';
			}

			// Add admin notice when stripe keys are empty and disable gateway.
			add_action( 'admin_notices', array( $this, 'mwb_membership_for_woo_stripe_admin_notices' ) );
		}

		// Set secret key for stripe library.
		\Stripe\Stripe::setApiKey( $this->secret_key );

		// Save hook for gateway settings.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		// card form js.
		add_action( 'wp_enqueue_scripts', array( $this, 'mwb_enqueue_wc_card_form_js' ) );

		// Recurring payments for Subscriptions.
		add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( $this, 'process_subscriptions_renewal_payment' ), 10, 2 );

		add_filter( 'woocommerce_payment_successful_result', array( $this, 'mwb_membership_for_woo_intent_redirect' ), 99999, 2 );

		add_action( 'woocommerce_account_view-order_endpoint', array( $this, 'check_intent_status_on_order_page' ), 1 );
	}

	/**
	 * Enqueue Woocommerce card form js.
	 *
	 * @since    1.0.0
	 */
	public function mwb_enqueue_wc_card_form_js() {

		wp_enqueue_script( 'wc-credit-card-form' );

		// If Stripe is not enabled.
		if ( 'no' === $this->enabled ) {
			return;
		}

		// If no SSL bail.
		if ( ! $this->testmode && ! is_ssl() ) {
			$this->mwb_membership_for_woo_create_stripe_log( '', 'mwb_enqueue_wc_card_form_js', 'Stripe live mode requires SSL.' );
			return;
		}

		wp_register_script( 'stripe', 'https://js.stripe.com/v3/', '', '3.0', true );

		wp_register_script( 'mwb_membership_stripe', plugin_dir_url( __FILE__ ) . 'assets/js/stripe.min.js', array( 'jquery-payment', 'stripe' ), '4.2.3', true );

		// No such requirement just for satisfying the Stripe.js need.
		$sepa_elements_options = apply_filters(
			'wc_stripe_sepa_elements_options',
			array(
				'supportedCountries' => array( 'SEPA' ),
				'placeholderCountry' => WC()->countries->get_base_country(),
				'style'              => array( 'base' => array( 'fontSize' => '15px' ) ),
			)
		);

		$stripe_params = array(
			'key'                   => $this->publishable_key,
			'i18n_terms'            => __( 'Please accept the terms and conditions first.', 'membership-for-woocommerce' ),
			'i18n_required_fields'  => __( 'Please fill in required checkout fields first.', 'membership-for-woocommerce' ),
			'invalid_request_error' => __( 'Unable to process this payment, please try again or use alternative method.', 'membership-for-woocommerce' ),
			'ajaxurl'               => WC_AJAX::get_endpoint( '%%endpoint%%' ),
			'nonce'                 => wp_create_nonce( 'mwb_membership_stripe_nonce' ),
			'sepa_elements_options' => $sepa_elements_options,
			'elements_options'      => apply_filters( 'wc_stripe_elements_options', array() ),
		);

		wp_localize_script( 'mwb_membership_stripe', 'wc_stripe_params', apply_filters( 'wc_stripe_params', $stripe_params ) );
		wp_enqueue_script( 'mwb_membership_stripe' );
	}

	/**
	 * Stripe payment form on checkout.
	 *
	 * @since    3.2.0
	 */
	public function payment_fields() {

		if ( $description = $this->get_description() ) {
			echo wpautop( wptexturize( $description ) );
		}

		?>
		<div class="stripe-source-errors" role="alert"></div>
		<div class="mwb_membership_credit_card_form">
		<?php

		if ( $this->supports( 'tokenization' ) && is_checkout() ) {
			$this->tokenization_script();
			$this->saved_payment_methods();
			$this->form();
			$this->save_payment_method_checkbox();
		} else {
			$this->form();
		}

		?>
		</div>
		<?php
	}

	/**
	 * Checking enability of one click upsell stripe.
	 *
	 * @since    3.2.0
	 */
	public function mwb_membership_for_woo_stripe_admin_notices() {

		if ( 'no' == $this->get_option( 'enabled' ) ) {

			return;
		}

		if ( empty( $this->publishable_key ) || empty( $this->secret_key ) ) {

			echo '<div class="error"><p>' . esc_html__( 'Stripe needs API Keys to work, please find your secret and publishable keys in the ', 'woocommerce-one-click-upsell-funnel-pro' ) . '<a href="https://manage.stripe.com/account/apikeys" target="_blank">' . esc_html__( 'Stripe accounts section ', 'woocommerce-one-click-upsell-funnel-pro' ) . '</a></p></div>';

		}
	}

	/**
	 * One click upsell stripe form fields template.
	 *
	 * @since    3.2.0
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'              => array(
				'type'        => 'checkbox',
				'title'       => __( 'Enable/Disable', 'membership-for-woocommerce' ),
				'description' => __( 'Enable Stripe for Memberhsip For Woocommerce', 'membership-for-woocommerce' ),
				'label'       => __( 'Enable Stripe ( Membership )', 'membership-for-woocommerce' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'title'                => array(
				'type'        => 'text',
				'title'       => __( 'Title', 'membership-for-woocommerce' ),
				'description' => __( 'The title visible to your customer during checkout.', 'membership-for-woocommerce' ),
				'default'     => __( 'Credit/Debit Card Payment ( Stripe )', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
			),
			'description'          => array(
				'type'        => 'textarea',
				'title'       => __( 'Description', 'membership-for-woocommerce' ),
				'description' => __( 'Optional. The description, visible to customer will see during checkout.', 'membership-for-woocommerce' ),
				'default'     => __( 'Use your credit/debit card to make payments via Stripe', 'membership-for-woocommerce' ),
				'id'          => 'woocommerce_mwb-membership-stripe-gateway_description',
				'css'         => 'max-width:400px',
				'desc_tip'    => true,
			),
			'gateway_description'  => array(
				'type'        => 'text',
				'title'       => __( 'Gateway Description', 'memberhsip-for-woocommerce' ),
				'description' => __( 'This may be up to 22 characters. The statement description must contain at least one letter, may not include ><"\' characters, and will appear on your customer\'s statement in capital letters.', 'membership-for-woocommerce' ),
				'default'     => __( 'Upsell Stripe', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
			),
			'stripe_icons'         => array(
				'title'   => __( 'Card Icons', 'membership-for-woocommerce' ),
				'label'   => __( 'Show card icons', 'membership-for-woocommerce' ),
				'type'    => 'checkbox',
				'default' => 'no',
			),
			// 'capture'              => array(
			// 	'title'       => __( 'Capture', 'membership-for-woocommerce' ),
			// 	'label'       => __( 'Capture charge immediately', 'membership-for-woocommerce' ),
			// 	'type'        => 'checkbox',
			// 	'description' => __( 'Whether or not to immediately capture the charge. When unchecked, the charge issues an authorization and will need to be captured later. Uncaptured charges expire in 7 days.', 'membership-for-woocommerce' ),
			// 	'default'     => 'yes',
			// 	'desc_tip'    => true,
			// ),
			'testmode'             => array(
				'type'        => 'checkbox',
				'title'       => __( 'Test mode', 'membership-for-woocommerce' ),
				'description' => __( 'Use the test mode in Stripe\'s dashboard to verify that everything works before going live.', 'membership-for-woocommerce' ),
				'label'       => __( 'Turn on testing', 'membership-for-woocommerce' ),
				'default'     => 'yes',
				'desc_tip'    => true,
			),
			'test_publishable_key' => array(
				'type'    => 'password',
				'title'   => __( 'Stripe API Test Publishable key', 'membership-for-woocommerce' ),
				'default' => '',
			),
			'test_secret_key'      => array(
				'type'    => 'password',
				'title'   => __( 'Stripe API Test Secret key', 'membership-for-woocommerce' ),
				'default' => '',
			),
			'live_publishable_key' => array(
				'type'    => 'password',
				'title'   => __( 'Stripe API Live Publishable key', 'membership-for-woocommerce' ),
				'default' => '',
			),
			'live_secret_key'      => array(
				'type'    => 'password',
				'title'   => __( 'Stripe API Live Secret key', 'membership-for-woocommerce' ),
				'default' => '',
			),
			'logging'              => array(
				'title'       => __( 'Logging', 'membership-for-woocommerce' ),
				'label'       => __( 'Log debug messages', 'membership-for-woocommerce' ),
				'type'        => 'checkbox',
				'description' => __( 'Save debug messages to the WooCommerce System Status log.', 'membership-for-woocommerce' ),
				'default'     => 'yes',
				'desc_tip'    => true,
			),
		);
	}

	/**
	 * Get card icons from Woocommerce stripe.
	 *
	 * @since    1.0.0
	 */
	public function get_icon() {

		if ( 'yes' != $this->stripe_icons ) {

			return;
		}

		$icons = array(
			'visa'       => '<img src="' . MEMBERSHIP_FOR_WOOCOMMERCE_URL . 'gateways/stripe/assets/visa.svg" class="stripe-visa-icon stripe-icon" alt="Visa" />',
			'amex'       => '<img src="' . MEMBERSHIP_FOR_WOOCOMMERCE_URL . 'gateways/stripe/assets/amex.svg" class="stripe-amex-icon stripe-icon" alt="American Express" />',
			'mastercard' => '<img src="' . MEMBERSHIP_FOR_WOOCOMMERCE_URL . 'gateways/stripe/assets/mastercard.svg" class="stripe-mastercard-icon stripe-icon" alt="Mastercard" />',
			'discover'   => '<img src="' . MEMBERSHIP_FOR_WOOCOMMERCE_URL . 'gateways/stripe/assets/discover.svg" class="stripe-discover-icon stripe-icon" alt="Discover" />',
			'diners'     => '<img src="' . MEMBERSHIP_FOR_WOOCOMMERCE_URL . 'gateways/stripe/assets/diners.svg" class="stripe-diners-icon stripe-icon" alt="Diners" />',
			'jcb'        => '<img src="' . MEMBERSHIP_FOR_WOOCOMMERCE_URL . 'gateways/stripe/assets/jcb.svg" class="stripe-jcb-icon stripe-icon" alt="JCB" />',
		);

		$icons_str = '';

		$icons_str .= $icons['visa'];
		$icons_str .= $icons['amex'];
		$icons_str .= $icons['mastercard'];

		if ( 'USD' === get_woocommerce_currency() ) {
			$icons_str .= $icons['discover'];
			$icons_str .= $icons['jcb'];
			$icons_str .= $icons['diners'];
		}

		return $icons_str;

	}

	/**
	 * Validate form fields.
	 *
	 * @return boolean
	 */
	public function validate_fields() {

		if ( empty( $_POST[ $this->id . '-card-number' ] ) || empty( $_POST[ $this->id . '-card-expiry' ] ) || empty( $_POST[ $this->id . '-card-cvc' ] ) ) {

			wc_add_notice( __( 'Please fill all the necessary card details.', 'membership-for-woocommerce' ) );

			return false;
		}

	}


}
