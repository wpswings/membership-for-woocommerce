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
	 * Inastance of global functions class file.
	 *
	 * @var object
	 */
	public $global_class;

	/**
	 * Constructor function.
	 */
	public function __construct() {

		$this->id                 = 'membership-paypal-smart-buttons';
		$this->method_title       = __( 'PayPal Checkout( Membership )', 'membership-for-woocommerce' );
		$this->method_description = __( 'Allow customers to conveniently checkout directly with PayPal.', 'membership-for-woocommerce' );
		$this->has_fields         = true;

		// Instance of global class.
		$this->global_class = Membership_For_Woocommerce_Global_Functions::get();

		$this->payment_action  = 'capture';
		$this->currency_code   = get_woocommerce_currency();
		$this->vault           = false;
		$this->component       = 'buttons';
		$this->disable_funding = 'card';

		$this->title       = $this->method_title;
		$this->description = $this->get_option( 'description', 'Pay via your PayPal account' );
		$this->enabled     = $this->get_option( 'enabled', 'yes' );
		$this->test_mode   = $this->get_option( 'test_mode', 'yes' );

		if ( 'yes' === $this->test_mode ) {

			$this->client_id = $this->get_option( 'sb_client_id' );

		} else {

			$this->client_id = $this->get_option( 'live_client_id' );
		}

		$this->debug          = 'yes' === $this->get_option( 'debug', 'no' );

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

	/**
	 * Paypal smart button settings.
	 *
	 * @return array an array of paypal settings.
	 * @since 1.0.0
	 */
	public function paypal_sb_settings() {

		$settings = array();

		$settings['enabled']         = $this->enabled;
		$settings['payment_action']  = $this->payment_action;
		$settings['currency_code']   = $this->currency_code;
		$settings['vault']           = $this->vault;
		$settings['component']       = $this->component;
		$settings['disable_funding'] = $this->disable_funding;
		$settings['test_mode']       = $this->test_mode;
		$settings['client_id']       = $this->client_id;
		$settings['debug']           = $this->debug;
		$settings['button_layout']   = $this->button_layout;
		$settings['button_color']    = $this->button_color;
		$settings['button_shape']    = $this->button_shape;
		$settings['button_label']    = $this->button_label;

		return $settings;
	}

	/**
	 * Payment fields function
	 *
	 * @return void
	 */
	public function payment_fields() {

		if ( ! empty( $this->description ) ) {

			echo wp_kses_post( wpautop( wptexturize( $this->description ) ) . PHP_EOL );
		}
	}

	/**
	 * Process payment.
	 *
	 * @param int $plan_id   Membership plan ID.
	 * @param int $member_id Members ID.
	 * @param int $user      Optional User ID.
	 *
	 * @return bool
	 */
	public function process_payment( $plan_id, $member_id = '', $user = '' ) {

		$tnx_detail = '';

		if ( empty( $plan_id ) ) {
			return; // there must be a plan id.
		}

		if ( ! empty( $user ) ) {

			// Get tnx details saved in user meta.
			$tnx_detail = get_user_meta( $user, 'members_tnx_details', true );
		}

		if ( ! empty( $tnx_detail ) ) {

			try {	

				update_post_meta( $member_id, '_membership_tnx_details', $tnx_detail );

				// Updating status to complete.
				update_post_meta( $member_id, 'member_status', 'complete' );

				// Getting current activation date.
				$current_date = gmdate( 'Y-m-d' );

				$plan_obj = get_post_meta( $member_id, 'plan_obj', true );

				// Save expiry date in post.
				if ( ! empty( $plan_obj ) ) {

					if ( 'lifetime' == $plan_obj['mwb_membership_plan_name_access_type'] ) {

						update_post_meta( $member_id, 'member_expiry', 'Lifetime' );

					} elseif ( 'limited' == $plan_obj['mwb_membership_plan_name_access_type'] ) {

						$duration = $plan_obj['mwb_membership_plan_duration'] . ' ' . $plan_obj['mwb_membership_plan_duration_type'];

						$expiry_date = strtotime( $current_date . $duration );

						update_post_meta( $member_id, 'member_expiry', $expiry_date );
					}
				}

				delete_user_meta( $user, 'members_tnx_details' );

				// Send email invoice to customer.
				if ( 'email_invoice' === get_post_meta( $member_id, 'member_actions', true ) ) {

					$mail_sent = $this->global_class->email_membership_invoice( $member_id );

					if ( false == $mail_sent ) {

						$error = array(
							'status'  => 'email_failed',
							'message' => 'Email failed.',
						);

						$activity_class = new Membership_Activity_Helper( 'Email-logs', 'logger' );
						$activity_class->create_log( 'Paypal smart buttons email failure', $error );
					}
				}
			} catch ( \Throwable $e ) {

				/**
				 * If there was an error completing the payment, log it to a file.
				 */

				/* translators: %d: member's ID. %s: error message */
				$message = __( 'Error completing payment for member #%1$d. Error caused due to: %2$s ', 'membership-for-woocommerce' );

				$error = array(
					'status'  => 'payment_failed',
					'message' => sprintf( $message, $member_id, $e->getMessage() ),
				);

				// Handling log creation via activity helper class.
				$activity_class = new Membership_Activity_Helper( 'PayPal-smart-button-logs', 'logger' );
				$log_data       = $activity_class->create_log( 'PayPal Smart buttons payment failure', $error );

				return false;
			}

			return true;
		}

	}

}
