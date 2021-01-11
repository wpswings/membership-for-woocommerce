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
		$this->id                 = 'membership-paypal-gateway';
		$this->method_title       = __( 'Paypal ( Membership )', 'membership-for-woocommerce' );
		$this->method_description = __( 'Safe and Secure method for making payments with Paypal.', 'membership-for-woocommerce' );
		$this->has_fields         = true;
		$this->supports           = array(
			'products',
			'refunds',
		);
		$this->charge_type        = 'SALE';

		// Load form fields.
		$this->init_form_fields();

		// Load settings.
		$this->init_settings();

		// Get settings values.
		$this->title               = $this->get_option( 'title' );
		$this->enabled             = $this->get_option( 'enabled' );
		$this->description         = $this->get_option( 'description' );
		$this->testmode            = $this->get_option( 'testmode' );
		$this->order_number_prefix = $this->get_option( 'invoice_prefix' );
		$this->billing_desc        = $this->get_option( 'billing_description' );
		$this->logging             = $this->get_option( 'logging' );

		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = 'no';
		}

		if ( 'yes' == $this->testmode ) {

			$this->api_username  = $this->get_option( 'sandbox_api_username' );
			$this->api_password  = $this->get_option( 'sandbox_api_password' );
			$this->api_signature = $this->get_option( 'sandbox_api_signature' );
			$this->description   = __( 'TESTMODE ENABLED. In test mode, you can use the credentials of your Paypal sandbox account to make a payment.', 'membership-for-woocommerce' );

		} else {

			$this->api_username  = $this->get_option( 'api_username' );
			$this->api_password  = $this->get_option( 'api_password' );
			$this->api_signature = $this->get_option( 'api_signature' );

		}

		// if ( empty( $this->api_username ) || empty( $this->api_password ) || empty( $this->api_signature ) ) {

		// 	$this->enabled = 'no';
		// }

		$this->base_url = 'https://api-3t.paypal.com/nvp';

		$this->redirect_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&useraction=commit&token=';

		if ( 'yes' == $this->testmode ) {

			$this->base_url = 'https://api-3t.sandbox.paypal.com/nvp';

			$this->redirect_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&useraction=commit&token=';
		}

		// This action hook save settings.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		// This action hook display admin notices for paypal gateway.
		add_action( 'admin_notices', array( $this, 'mwb_membership_for_woo_admin_notices' ) );

		// This action hook proccess final payment on Thankyou page.
		//add_action( 'woocommcerce_thankyou_' . $this->id, array( $this, 'mwb_membership_for_woo_process_final_payment' ) );
	}

	/**
	 * Displaying admin notices and checking availability of membership paypal gateway.
	 */
	public function mwb_membership_for_woo_admin_notices() {

		if ( 'no' == $this->get_option( 'enabled' ) ) {

			return false;
		}

		if ( empty( $this->api_username ) || empty( $this->api_password ) || empty( $this->api_signature ) ) {

			echo '<div class="error"><p>' . esc_html_e( 'PayPal needs API credentials to work, please find your PayPal API credentials at ', 'membership-for-woocommerce' ) .
			'<a href="https://developer.paypal.com/" target="_blank">' . esc_html_e( 'PayPal Developer', 'membership-for-woocommerce' ) . '</a></p></div>';

		}

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

	/**
	 * Check if gateway enabled display notices
	 */
	public function admin_options() {

		if ( $this->is_valid_for_use() ) {

			parent::admin_options();

		} else {

			?>
			<div class="inline error">
				<p>
					<strong><?php esc_html_e( 'Gateway disabled', 'membership-for-woocommerce' ); ?></strong>: 
					<?php esc_html_e( 'Membership for Woocommerce do not support your store currency', 'membership-for-woocommerce' ); ?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Get gateway icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		$icon_html = '';
		$icon      = (array) $this->get_icon_image( WC()->countries->get_base_country() );

		foreach ( $icon as $i ) {
			$icon_html .= '<img src="' . esc_attr( $i ) . '" alt="' . esc_attr__( 'PayPal acceptance mark', 'woocommerce' ) . '" />';
		}

		$icon_html .= sprintf( '<a href="%1$s" class="about_paypal" onclick="javascript:window.open(\'%1$s\',\'WIPaypal\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700\'); return false;">' . esc_attr__( 'What is PayPal?', 'woocommerce' ) . '</a>', esc_url( $this->get_icon_url( WC()->countries->get_base_country() ) ) );

		return apply_filters( 'woocommerce_gateway_icon', $icon_html, $this->id );
	}

	/**
	 * Get the link for an icon based on country.
	 *
	 * @param  string $country country code.
	 * @return string
	 */
	protected function get_icon_url( $country ) {
		$url           = 'https://www.paypal.com/' . strtolower( $country );
		$home_counties = array( 'BE', 'CZ', 'DK', 'HU', 'IT', 'JP', 'NL', 'NO', 'ES', 'SE', 'TR', 'IN' );
		$countries     = array( 'DZ', 'AU', 'BH', 'BQ', 'BW', 'CA', 'CN', 'CW', 'FI', 'FR', 'DE', 'GR', 'HK', 'ID', 'JO', 'KE', 'KW', 'LU', 'MY', 'MA', 'OM', 'PH', 'PL', 'PT', 'QA', 'IE', 'RU', 'BL', 'SX', 'MF', 'SA', 'SG', 'SK', 'KR', 'SS', 'TW', 'TH', 'AE', 'GB', 'US', 'VN' );

		if ( in_array( $country, $home_counties ) ) {

			return $url . '/webapps/mpp/home';

		} elseif ( in_array( $country, $countries ) ) {

			return $url . '/webapps/mpp/paypal-popup';

		} else {

			return $url . '/cgi-bin/webscr?cmd=xpt/Marketing/general/WIPaypal-outside';
		}
	}

	/**
	 * Get PayPal images for a country.
	 *
	 * @param string $country Country code.
	 * @return array of image URLs
	 */
	protected function get_icon_image( $country ) {
		switch ( $country ) {
			case 'US':
			case 'NZ':
			case 'CZ':
			case 'HU':
			case 'MY':
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo/AM_mc_vs_dc_ae.jpg';
				break;
			case 'TR':
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_paypal_odeme_secenekleri.jpg';
				break;
			case 'GB':
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/Logo/AM_mc_vs_ms_ae_UK.png';
				break;
			case 'MX':
				$icon = array(
					'https://www.paypal.com/es_XC/Marketing/i/banner/paypal_visa_mastercard_amex.png',
					'https://www.paypal.com/es_XC/Marketing/i/banner/paypal_debit_card_275x60.gif',
				);
				break;
			case 'FR':
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_paypal_moyens_paiement_fr.jpg';
				break;
			case 'AU':
				$icon = 'https://www.paypalobjects.com/webstatic/en_AU/mktg/logo/Solutions-graphics-1-184x80.jpg';
				break;
			case 'DK':
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_PayPal_betalingsmuligheder_dk.jpg';
				break;
			case 'RU':
				$icon = 'https://www.paypalobjects.com/webstatic/ru_RU/mktg/business/pages/logo-center/AM_mc_vs_dc_ae.jpg';
				break;
			case 'NO':
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/banner_pl_just_pp_319x110.jpg';
				break;
			case 'CA':
				$icon = 'https://www.paypalobjects.com/webstatic/en_CA/mktg/logo-image/AM_mc_vs_dc_ae.jpg';
				break;
			case 'HK':
				$icon = 'https://www.paypalobjects.com/webstatic/en_HK/mktg/logo/AM_mc_vs_dc_ae.jpg';
				break;
			case 'SG':
				$icon = 'https://www.paypalobjects.com/webstatic/en_SG/mktg/Logos/AM_mc_vs_dc_ae.jpg';
				break;
			case 'TW':
				$icon = 'https://www.paypalobjects.com/webstatic/en_TW/mktg/logos/AM_mc_vs_dc_ae.jpg';
				break;
			case 'TH':
				$icon = 'https://www.paypalobjects.com/webstatic/en_TH/mktg/Logos/AM_mc_vs_dc_ae.jpg';
				break;
			case 'JP':
				$icon = 'https://www.paypal.com/ja_JP/JP/i/bnr/horizontal_solution_4_jcb.gif';
				break;
			case 'IN':
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo/AM_mc_vs_dc_ae.jpg';
				break;
			default:
				$icon = WC_HTTPS::force_https_url( WC()->plugin_url() . '/includes/gateways/paypal/assets/images/paypal.png' );
				break;
		}
		return apply_filters( 'woocommerce_paypal_icon', $icon );
	}

	/**
	 * Check if this gateway is enabled and available in the user's country.
	 *
	 * @return bool
	 */
	public function is_valid_for_use() {

		return in_array( get_woocommerce_currency(), apply_filters( 'woocommerce_paypal_supported_currencies', array( 'AUD', 'BRL', 'CAD', 'MXN', 'NZD', 'HKD', 'SGD', 'USD', 'EUR', 'JPY', 'TRY', 'NOK', 'CZK', 'DKK', 'HUF', 'ILS', 'MYR', 'PHP', 'PLN', 'SEK', 'CHF', 'TWD', 'THB', 'GBP', 'RMB', 'RUB', 'INR' ) ) );
	}

	/**
	 * Redirects to paypal payment page on successful authorization.
	 *
	 * @param int $plan_id   Membership plan ID.
	 * @param int $member_id Members ID.
	 *
	 * @return array
	 */
	public function process_payment( $plan_id, $member_id = '' ) {

		if ( empty( $plan_id ) ) {
			return; // there must be a plan id.
		}

		if ( ! empty( $plan_id ) && ! empty( $member_id ) ) {

			$plan_price = get_post_meta( $plan_id, 'mwb_membership_plan_price', true );

		}

		//$order = wc_get_order( $order_id );
		$plan = get_post_meta( $member_id, 'plan_obj', true );

		if ( ! empty( $plan ) && is_array( $plan ) ) {
			$result = $this->mwb_membership_for_woo_paypal_get_response( $plan );
		}

		$mwb_membership_parsed_data = array();

		wp_parse_str( $result, $mwb_membership_parsed_data );

		$mwb_membership_paypal_execution_url = $this->redirect_url;

		if ( isset( $mwb_membership_parsed_data['TOKEN'] ) ) {

			$mwb_membership_paypal_execution_url .= $mwb_membership_parsed_data['TOKEN'];

			return array(
				'result'   => 'success',
				'redirect' => $mwb_membership_paypal_execution_url,
			);

		} elseif ( isset( $mwb_membership_parsed_data['L_LONGMESSAGE0'] ) ) {

			$error_text = $mwb_membership_parsed_data['L_LONGMESSAGE0'];

			wc_add_notice( $error_text, 'error' );

		} else {

			wc_add_notice( __( 'Sorry, an error occured. Please try again.', 'membership-for-woocommerce' ), 'error' );
		}
	}

	/**
	 * Authorizes the Paypal request.
	 *
	 * @param object $order Order object.
	 */
	public function mwb_membership_for_woo_paypal_get_response( $order ) {

		if ( ! empty( $order ) ) {

			$mwb_membership_paypal_request = array(
				'METHOD'                         => 'SetExpressCheckout',
				'VERSION'                        => 98,
				'PAYMENTREQUEST_0_INVNUM'        => $this->order_number_prefix . $order->get_id(),
				'PAYMENTREQUEST_0_AMT'           => $order->get_total(),
				'PAYMENTREQUEST_0_CURRENCYCODE'  => $order->get_currency(),
				'PAYMENTREQUEST_0_PAYMENTACTION' => mb_strtoupper( $this->charge_type ),
				'L_BILLINGTYPE0'                 => 'MerchantInitiatedBilling',
				'L_BILLINGAGREEMENTDESCRIPTION0' => $this->billing_desc,
				'CANCELURL'                      => urlencode( wc_get_cart_url() . '?mwb_membership_paypal=cancel' ),
				'RETURNURL'                      => urlencode( $this->get_return_url( $order ) ),
			);
		}

		$response = $this->mwb_membership_for_woo_make_paypal_request( 'SetExpressCheckout', $order->get_id(), $this->base_url, $mwb_membership_paypal_request );

		return $response;
	}

	/**
	 * Initiating main api call with account credentials.
	 */
	public function mwb_membership_for_woo_make_paypal_request( $step, $order_id, $url = '', $payload = array() ) {
		$result = false;

		$mwb_membership_auth_data = array(
			'USER'      => $this->api_username,
			'PWD'       => $this->api_password,
			'SIGNATURE' => $this->api_signature,
		);

		$payload = array_merge( $mwb_membership_auth_data, $payload );

		$payload = array( 'body' => $payload );

		$remote_args = array(
			'method'  => 'POST',
			'timeout' => 300,
		);

		$remote_args = array_merge( $remote_args, $payload );

		$remote_args['body'] = build_query( $remote_args['body'] );

		$response = wp_remote_request( $url, $remote_args );

		$this->mwb_membership_for_woo_create_paypal_log( $step, $order_id, $response );

		if ( is_array( $response ) ) {
			$response_data = wp_remote_retrieve_body( $response );

			$status = wp_remote_retrieve_response_code( $response );

			if ( 200 == $status ) {
				$result = $response_data;
			}
		}

		return $result;
	}

	/**
	 * Writes error and response to the logs
	 */
	public function mwb_membership_for_woo_create_paypal_log( $step, $order_id, $final_response ) {

		if ( 'yes' == $this->logging ) {

			if ( ! defined( 'WC_LOG_DIR' ) ) {

				return;
			}

			$log_dir      = WC_LOG_DIR;
			$log_dir_file = WC_LOG_DIR . 'membership-for-woocommerce-paypal.log';

			// As sometimes when dir is not present, and fopen cannot create directories.
			if ( ! is_dir( $log_dir ) ) {

				mkdir( $log_dir, 0755, true );
			}

			if ( ! file_exists( $log_dir_file ) || ! is_writable( $log_dir_file ) ) {

				@fopen( $log_dir_file, 'a' );
			}

			if ( file_exists( $log_dir_file ) && is_writable( $log_dir_file ) ) {

				$log = 'Website: ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL .
						'Time: ' . current_time( 'F j, Y  g:i a' ) . PHP_EOL .
						'Order ID ' . $order_id . PHP_EOL .
						'Step: ' . $step . PHP_EOL .
						'Response: ' . json_encode( $final_response ) . PHP_EOL .
						'----------------------------------------------------------------------------' . PHP_EOL;

				file_put_contents( $log_dir_file, $log, FILE_APPEND );
			}
		}
	}

}
