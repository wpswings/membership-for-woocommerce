<?php
/**
 * Handle the admin Talk to an Expert workflow.
 *
 * @package Membership_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Talk to an Expert form handler.
 */
class Membership_For_Woocommerce_Talk_To_Expert_Form {

	/**
	 * Ajax action name.
	 *
	 * @var string
	 */
	const AJAX_ACTION = 'wps_mfw_submit_talk_to_expert';

	/**
	 * Ajax nonce action.
	 *
	 * @var string
	 */
	const NONCE_ACTION = 'wps_mfw_talk_to_expert_nonce';

	/**
	 * HubSpot base url.
	 *
	 * @var string
	 */
	const HUBSPOT_BASE_URL = 'https://api.hsforms.com/';

	/**
	 * HubSpot portal id.
	 *
	 * @var string
	 */
	const HUBSPOT_PORTAL_ID = '25444144';

	/**
	 * HubSpot form id.
	 *
	 * @var string
	 */
	const HUBSPOT_FORM_ID = 'eab973a7-5c65-4264-a31d-3b1b10b82c82';

	/**
	 * Get services landing URL.
	 *
	 * @return string
	 */
	public static function wps_mfw_get_services_landing_url() {
		return 'https://wpswings.com/woocommerce-services/?utm_source=wpswings-membership-services&utm_medium=membership-org-backend&utm_campaign=woocommerce-services';
	}

	/**
	 * Get service option labels keyed by submitted slug values.
	 *
	 * @return array
	 */
	public static function wps_mfw_get_service_options() {
		return array(
			'seo_services'                      => esc_html__( 'SEO services', 'membership-for-woocommerce' ),
			'google_ads_setup_and_ga4_setup'   => esc_html__( 'Google Ads Setup and GA4 setup', 'membership-for-woocommerce' ),
			'speed_optimization'                => esc_html__( 'Speed Optimization', 'membership-for-woocommerce' ),
			'woocommerce_development_services' => esc_html__( 'WooCommerce Development Services', 'membership-for-woocommerce' ),
		);
	}

	/**
	 * Get budget option labels keyed by submitted values.
	 *
	 * @return array
	 */
	public static function wps_mfw_get_budget_options() {
		return array(
			''               => esc_html__( 'Please Select', 'membership-for-woocommerce' ),
			'$500 - $1000'   => '$500 - $1000',
			'$1001 - $5000'  => '$1001 - $5000',
			'$5001 - $10000' => '$5001 - $10000',
			'$10001 - $15000' => '$10001 - $15000',
		);
	}

	/**
	 * Get default field values from the current user.
	 *
	 * @return array
	 */
	public static function wps_mfw_get_default_form_values() {
		$user       = function_exists( 'wp_get_current_user' ) ? wp_get_current_user() : null;
		$first_name = ! empty( $user->user_firstname ) ? (string) $user->user_firstname : '';
		$last_name  = ! empty( $user->user_lastname ) ? (string) $user->user_lastname : '';
		$email      = ! empty( $user->user_email ) ? (string) $user->user_email : '';

		if ( ( '' === $first_name || '' === $last_name ) && ! empty( $user->display_name ) ) {
			$display_name_parts = preg_split( '/\s+/', trim( (string) $user->display_name ) );

			if ( '' === $first_name && ! empty( $display_name_parts[0] ) ) {
				$first_name = $display_name_parts[0];
			}

			if ( '' === $last_name && count( $display_name_parts ) > 1 ) {
				array_shift( $display_name_parts );
				$last_name = implode( ' ', $display_name_parts );
			}
		}

		return array(
			'firstname' => $first_name,
			'lastname'  => $last_name,
			'email'     => $email,
			'phone'     => '',
			'budget'    => '',
			'message'   => '',
		);
	}

	/**
	 * Derive plugin label from plugin header.
	 *
	 * @return string
	 */
	public static function wps_mfw_get_plugin_label() {
		$plugin_file = defined( 'MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH' ) ? MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'membership-for-woocommerce.php' : '';

		if ( $plugin_file && function_exists( 'get_file_data' ) && file_exists( $plugin_file ) ) {
			$plugin_data = get_file_data(
				$plugin_file,
				array(
					'name' => 'Plugin Name',
				)
			);

			if ( ! empty( $plugin_data['name'] ) ) {
				return (string) $plugin_data['name'];
			}
		}

		return 'Membership For WooCommerce';
	}

	/**
	 * Render modal markup.
	 *
	 * @return void
	 */
	public static function wps_mfw_render_modal() {
		$defaults        = self::wps_mfw_get_default_form_values();
		$service_options = self::wps_mfw_get_service_options();
		$budget_options  = self::wps_mfw_get_budget_options();
		?>
		<div class="mfw-expert-modal" data-mfw-expert-modal hidden>
			<div class="mfw-expert-modal__backdrop" data-mfw-expert-modal-close></div>
			<div class="mfw-expert-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="mfw-expert-modal-title">
				<button type="button" class="mfw-expert-modal__close" data-mfw-expert-modal-close aria-label="<?php echo esc_attr__( 'Close Talk to an Expert form', 'membership-for-woocommerce' ); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="mfw-expert-modal__header">
					<span class="mfw-expert-modal__eyebrow"><?php esc_html_e( 'Marketing Services', 'membership-for-woocommerce' ); ?></span>
					<h2 id="mfw-expert-modal-title"><?php esc_html_e( 'Talk to an Expert', 'membership-for-woocommerce' ); ?></h2>
					<p><?php esc_html_e( 'Share your store goals and our team will reach out with the right next step.', 'membership-for-woocommerce' ); ?></p>
				</div>
				<div class="mfw-expert-modal__panel">
					<div class="mfw-expert-modal__status" data-mfw-expert-modal-status hidden aria-live="polite"></div>
					<form
						class="mfw-expert-modal__form"
						data-mfw-expert-modal-form
						data-mfw-expert-action="<?php echo esc_attr( self::AJAX_ACTION ); ?>"
						data-mfw-expert-nonce="<?php echo esc_attr( wp_create_nonce( self::NONCE_ACTION ) ); ?>"
						data-mfw-expert-ajaxurl="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
					>
						<div class="mfw-expert-modal__grid">
							<div class="mfw-expert-modal__field">
								<label for="mfw_expert_firstname"><?php esc_html_e( 'First Name', 'membership-for-woocommerce' ); ?></label>
								<input id="mfw_expert_firstname" type="text" name="firstname" value="<?php echo esc_attr( $defaults['firstname'] ); ?>" placeholder="<?php esc_attr_e( 'John', 'membership-for-woocommerce' ); ?>" autocomplete="given-name">
							</div>
							<div class="mfw-expert-modal__field">
								<label for="mfw_expert_lastname"><?php esc_html_e( 'Last Name', 'membership-for-woocommerce' ); ?></label>
								<input id="mfw_expert_lastname" type="text" name="lastname" value="<?php echo esc_attr( $defaults['lastname'] ); ?>" placeholder="<?php esc_attr_e( 'Doe', 'membership-for-woocommerce' ); ?>" autocomplete="family-name">
							</div>
							<div class="mfw-expert-modal__field mfw-expert-modal__field--span-2">
								<label for="mfw_expert_email"><?php esc_html_e( 'Work Email', 'membership-for-woocommerce' ); ?> <span class="mfw-expert-modal__required">*</span></label>
								<input id="mfw_expert_email" type="email" name="email" value="<?php echo esc_attr( $defaults['email'] ); ?>" required placeholder="<?php esc_attr_e( 'name@yourstore.com', 'membership-for-woocommerce' ); ?>" autocomplete="email">
							</div>
							<div class="mfw-expert-modal__field">
								<label for="mfw_expert_phone"><?php esc_html_e( 'Contact Number', 'membership-for-woocommerce' ); ?></label>
								<input id="mfw_expert_phone" type="text" name="phone" value="<?php echo esc_attr( $defaults['phone'] ); ?>" placeholder="<?php esc_attr_e( '+1 000 000 0000', 'membership-for-woocommerce' ); ?>">
							</div>
						</div>
						<div class="mfw-expert-modal__field">
							<span class="mfw-expert-modal__legend"><?php esc_html_e( 'What services do you need help with?', 'membership-for-woocommerce' ); ?></span>
							<div class="mfw-expert-modal__checkboxes">
								<?php foreach ( $service_options as $service_key => $service_label ) : ?>
									<label class="mfw-expert-modal__checkbox">
										<input type="checkbox" name="what_services_do_you_need_help_with[]" value="<?php echo esc_attr( $service_key ); ?>">
										<span><?php echo esc_html( $service_label ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>
						</div>
						<div class="mfw-expert-modal__field">
							<label for="mfw_expert_budget"><?php esc_html_e( 'Budget', 'membership-for-woocommerce' ); ?></label>
							<select id="mfw_expert_budget" name="budget">
								<?php foreach ( $budget_options as $budget_value => $budget_label ) : ?>
									<option value="<?php echo esc_attr( $budget_value ); ?>"<?php echo '' === $budget_value ? ' selected disabled' : ''; ?>><?php echo esc_html( $budget_label ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="mfw-expert-modal__field">
							<label for="mfw_expert_message"><?php esc_html_e( 'What do you need help with?', 'membership-for-woocommerce' ); ?></label>
							<textarea id="mfw_expert_message" name="message" rows="4" placeholder="<?php esc_attr_e( 'Share your goals, blockers, or the service you need.', 'membership-for-woocommerce' ); ?>"><?php echo esc_textarea( $defaults['message'] ); ?></textarea>
						</div>
						<div class="mfw-expert-modal__actions">
							<button
								type="submit"
								class="mfw-expert-modal__submit"
								data-submit-label="<?php echo esc_attr__( 'Submit Request', 'membership-for-woocommerce' ); ?>"
								data-loading-label="<?php echo esc_attr__( 'Sending...', 'membership-for-woocommerce' ); ?>"
							><?php esc_html_e( 'Submit Request', 'membership-for-woocommerce' ); ?></button>
						</div>
					</form>
					<div class="mfw-expert-modal__success" data-mfw-expert-modal-success hidden aria-live="polite">
						<div class="mfw-expert-modal__success-mark" aria-hidden="true">
							<span class="mfw-expert-modal__success-ring"></span>
							<span class="mfw-expert-modal__success-core">
								<svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
									<path d="M6.5 12.5l3.4 3.4L17.8 8" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</span>
						</div>
						<h3><?php esc_html_e( 'Thank you', 'membership-for-woocommerce' ); ?></h3>
						<p data-mfw-expert-modal-success-message><?php esc_html_e( 'Thank you for submitting your request.', 'membership-for-woocommerce' ); ?></p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Handle admin ajax submission.
	 *
	 * @return void
	 */
	public function wps_mfw_handle_ajax_submission() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'You are not allowed to submit this request.', 'membership-for-woocommerce' ),
				),
				403
			);
		}

		$form_data = isset( $_POST['form_data'] ) ? wp_unslash( $_POST['form_data'] ) : '';
		$form_data = is_string( $form_data ) ? json_decode( $form_data, true ) : array();

		if ( ! is_array( $form_data ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Invalid request payload.', 'membership-for-woocommerce' ),
				),
				400
			);
		}

		$sanitized_data = self::wps_mfw_sanitize_submission( $form_data );

		if ( empty( $sanitized_data['email'] ) || ! is_email( $sanitized_data['email'] ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Please enter a valid email address.', 'membership-for-woocommerce' ),
				),
				400
			);
		}

		$response = wp_remote_post( self::wps_mfw_get_hubspot_endpoint(), self::wps_mfw_get_hubspot_request_args( $sanitized_data ) );

		if ( is_wp_error( $response ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'We could not submit your request right now. Please try again.', 'membership-for-woocommerce' ),
				),
				502
			);
		}

		$response_code = (int) wp_remote_retrieve_response_code( $response );
		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );
		$message       = self::wps_mfw_get_hubspot_response_message( $response_body, $response_code );

		if ( $response_code >= 200 && $response_code < 300 ) {
			wp_send_json_success(
				array(
					'message' => $message,
				)
			);
		}

		wp_send_json_error(
			array(
				'message' => $message,
			),
			400
		);
	}

	/**
	 * Sanitize posted form payload.
	 *
	 * @param array $form_data Raw form data.
	 * @return array
	 */
	public static function wps_mfw_sanitize_submission( $form_data ) {
		$form_data          = is_array( $form_data ) ? $form_data : array();
		$services           = self::wps_mfw_get_service_options();
		$budgets            = self::wps_mfw_get_budget_options();
		$submitted_services = array();

		if ( isset( $form_data['what_services_do_you_need_help_with'] ) ) {
			$raw_services = wp_unslash( $form_data['what_services_do_you_need_help_with'] );

			if ( ! is_array( $raw_services ) ) {
				$raw_services = array( $raw_services );
			}

			$submitted_services = array_filter(
				array_map( 'sanitize_text_field', $raw_services ),
				static function( $service ) {
					return '' !== $service;
				}
			);
		}

		$valid_services = array_values( array_intersect( array_keys( $services ), $submitted_services ) );
		$budget         = isset( $form_data['budget'] ) ? sanitize_text_field( wp_unslash( $form_data['budget'] ) ) : '';

		if ( ! array_key_exists( $budget, $budgets ) || '' === $budget ) {
			$budget = '';
		}

		return array(
			'firstname'                         => isset( $form_data['firstname'] ) ? sanitize_text_field( wp_unslash( $form_data['firstname'] ) ) : '',
			'lastname'                          => isset( $form_data['lastname'] ) ? sanitize_text_field( wp_unslash( $form_data['lastname'] ) ) : '',
			'email'                             => isset( $form_data['email'] ) ? sanitize_email( wp_unslash( $form_data['email'] ) ) : '',
			'phone'                             => isset( $form_data['phone'] ) ? sanitize_text_field( wp_unslash( $form_data['phone'] ) ) : '',
			'what_services_do_you_need_help_with' => $valid_services,
			'budget'                            => $budget,
			'message'                           => isset( $form_data['message'] ) ? sanitize_textarea_field( wp_unslash( $form_data['message'] ) ) : '',
			'annualrevenue'                     => self::wps_mfw_get_annual_revenue_last_12_months(),
		);
	}

	/**
	 * Build wp_remote_post arguments for HubSpot.
	 *
	 * @param array $sanitized_data Clean form data.
	 * @return array
	 */
	public static function wps_mfw_get_hubspot_request_args( $sanitized_data ) {
		$payload = self::wps_mfw_build_hubspot_payload( $sanitized_data );

		return array(
			'method'      => 'POST',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(
				'Content-Type' => 'application/json',
			),
			'body'        => wp_json_encode(
				array(
					'fields'  => array_values( array_filter( $payload['fields'] ) ),
					'context' => array(
						'pageUri'   => admin_url( 'admin.php?page=membership_for_woocommerce_menu' ),
						'pageName'  => self::wps_mfw_get_plugin_label(),
						'ipAddress' => self::wps_mfw_get_client_ip(),
					),
				)
			),
			'cookies'     => array(),
		);
	}

	/**
	 * Build HubSpot payload.
	 *
	 * @param array $sanitized_data Clean form data.
	 * @return array
	 */
	public static function wps_mfw_build_hubspot_payload( $sanitized_data ) {
		$selected_services = ! empty( $sanitized_data['what_services_do_you_need_help_with'] ) && is_array( $sanitized_data['what_services_do_you_need_help_with'] )
			? array_values( $sanitized_data['what_services_do_you_need_help_with'] )
			: array();

		$fields = array(
			self::wps_mfw_maybe_build_hubspot_field( 'firstname', isset( $sanitized_data['firstname'] ) ? $sanitized_data['firstname'] : '' ),
			self::wps_mfw_maybe_build_hubspot_field( 'lastname', isset( $sanitized_data['lastname'] ) ? $sanitized_data['lastname'] : '' ),
			self::wps_mfw_maybe_build_hubspot_field( 'email', isset( $sanitized_data['email'] ) ? $sanitized_data['email'] : '' ),
			self::wps_mfw_maybe_build_hubspot_field( 'phone', isset( $sanitized_data['phone'] ) ? $sanitized_data['phone'] : '' ),
			self::wps_mfw_maybe_build_hubspot_field( 'what_services_do_you_need_help_with', $selected_services ),
			self::wps_mfw_maybe_build_hubspot_field( 'budget', isset( $sanitized_data['budget'] ) ? $sanitized_data['budget'] : '' ),
			self::wps_mfw_maybe_build_hubspot_field( 'message', isset( $sanitized_data['message'] ) ? $sanitized_data['message'] : '' ),
			self::wps_mfw_maybe_build_hubspot_field( 'currency', self::wps_mfw_get_store_currency() ),
			self::wps_mfw_maybe_build_hubspot_field( 'org_plugin_name', self::wps_mfw_get_plugin_label() ),
			self::wps_mfw_maybe_build_hubspot_field( 'company', function_exists( 'get_bloginfo' ) ? get_bloginfo( 'name' ) : '' ),
			self::wps_mfw_maybe_build_hubspot_field( 'website', function_exists( 'home_url' ) ? home_url( '/' ) : '' ),
			self::wps_mfw_maybe_build_hubspot_field( 'country', self::wps_mfw_get_store_country_label() ),
			self::wps_mfw_maybe_build_hubspot_field( 'annualrevenue', isset( $sanitized_data['annualrevenue'] ) ? $sanitized_data['annualrevenue'] : self::wps_mfw_get_annual_revenue_last_12_months() ),
		);

		return array(
			'fields' => array_values( array_filter( $fields ) ),
		);
	}

	/**
	 * Build a single HubSpot field when value is non-empty.
	 *
	 * @param string       $field_name Field name.
	 * @param string|array $field_value Field value.
	 * @return array|null
	 */
	public static function wps_mfw_maybe_build_hubspot_field( $field_name, $field_value ) {
		if ( is_array( $field_value ) ) {
			$field_value = implode(
				';',
				array_values(
					array_filter(
						array_map( 'strval', $field_value ),
						static function( $value ) {
							return '' !== $value;
						}
					)
				)
			);
		}

		if ( null === $field_value ) {
			return null;
		}

		$field_value = is_scalar( $field_value ) ? trim( (string) $field_value ) : '';

		if ( '' === $field_value ) {
			return null;
		}

		return array(
			'name'  => (string) $field_name,
			'value' => $field_value,
		);
	}

	/**
	 * Clean response message.
	 *
	 * @param string $message Raw message.
	 * @return string
	 */
	public static function wps_mfw_clean_response_message( $message ) {
		$message = is_string( $message ) ? $message : '';

		if ( '' === $message ) {
			return '';
		}

		$charset = function_exists( 'get_bloginfo' ) ? get_bloginfo( 'charset' ) : 'UTF-8';
		$message = wp_strip_all_tags( $message );
		$message = html_entity_decode( $message, ENT_QUOTES, $charset ? $charset : 'UTF-8' );
		$message = preg_replace( '/[\x{00A0}\s]+/u', ' ', $message );

		return trim( (string) $message );
	}

	/**
	 * Get HubSpot endpoint.
	 *
	 * @return string
	 */
	public static function wps_mfw_get_hubspot_endpoint() {
		return self::HUBSPOT_BASE_URL . 'submissions/v3/integration/submit/' . self::HUBSPOT_PORTAL_ID . '/' . self::HUBSPOT_FORM_ID;
	}

	/**
	 * Resolve client IP for HubSpot context.
	 *
	 * @return string
	 */
	public static function wps_mfw_get_client_ip() {
		$ip_headers = array(
			'HTTP_CF_CONNECTING_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_REAL_IP',
			'REMOTE_ADDR',
		);

		foreach ( $ip_headers as $header_key ) {
			if ( empty( $_SERVER[ $header_key ] ) ) {
				continue;
			}

			$raw_value = sanitize_text_field( wp_unslash( $_SERVER[ $header_key ] ) );
			$ip_list   = is_string( $raw_value ) ? explode( ',', $raw_value ) : array( $raw_value );

			foreach ( $ip_list as $ip_candidate ) {
				$ip_candidate = trim( sanitize_text_field( (string) $ip_candidate ) );

				if ( filter_var( $ip_candidate, FILTER_VALIDATE_IP ) ) {
					return $ip_candidate;
				}
			}
		}

		return '';
	}

	/**
	 * Resolve message from HubSpot response.
	 *
	 * @param array $response_body Response body.
	 * @param int   $response_code Response code.
	 * @return string
	 */
	public static function wps_mfw_get_hubspot_response_message( $response_body, $response_code ) {
		$response_body = is_array( $response_body ) ? $response_body : array();
		$message       = '';

		if ( ! empty( $response_body['inlineMessage'] ) ) {
			$message = self::wps_mfw_clean_response_message( $response_body['inlineMessage'] );
		} elseif ( ! empty( $response_body['errors'][0]['message'] ) ) {
			$message = self::wps_mfw_clean_response_message( $response_body['errors'][0]['message'] );
		} elseif ( ! empty( $response_body['message'] ) ) {
			$message = self::wps_mfw_clean_response_message( $response_body['message'] );
		}

		if ( '' !== $message ) {
			return $message;
		}

		if ( $response_code >= 200 && $response_code < 300 ) {
			return esc_html__( 'Thank you for submitting your request.', 'membership-for-woocommerce' );
		}

		return esc_html__( 'We could not submit your request right now. Please try again.', 'membership-for-woocommerce' );
	}

	/**
	 * Get store currency code.
	 *
	 * @return string
	 */
	public static function wps_mfw_get_store_currency() {
		return function_exists( 'get_woocommerce_currency' ) ? (string) get_woocommerce_currency() : '';
	}

	/**
	 * Get store country label.
	 *
	 * @return string
	 */
	public static function wps_mfw_get_store_country_label() {
		$default_country = function_exists( 'get_option' ) ? (string) get_option( 'woocommerce_default_country', '' ) : '';
		$country_code    = strtoupper( strtok( $default_country, ':' ) );

		if ( '' === $country_code ) {
			return '';
		}

		if ( function_exists( 'WC' ) ) {
			$wc_instance = WC();
			if ( ! empty( $wc_instance->countries ) && ! empty( $wc_instance->countries->countries[ $country_code ] ) ) {
				return (string) $wc_instance->countries->countries[ $country_code ];
			}
		}

		return $country_code;
	}

	/**
	 * Get annual revenue from last twelve months of paid orders.
	 *
	 * @return string
	 */
	public static function wps_mfw_get_annual_revenue_last_12_months() {
		global $wpdb;

		$amount          = null;
		$fallback_amount = null;

		if ( isset( $wpdb ) && is_object( $wpdb ) && ! empty( $wpdb->prefix ) ) {
			$table_name = $wpdb->prefix . 'wc_order_stats';

			if ( self::wps_mfw_order_stats_table_exists( $wpdb, $table_name ) ) {
				$amount = self::wps_mfw_get_revenue_from_order_stats_table( $wpdb, $table_name );
			}
		}

		if ( null === $amount || (float) $amount <= 0 ) {
			$fallback_amount = self::wps_mfw_get_revenue_from_wc_orders();

			if ( null === $amount || (float) $fallback_amount > 0 ) {
				$amount = $fallback_amount;
			}
		}

		if ( null === $amount ) {
			$amount = 0;
		}

		return number_format( (float) $amount, 2, '.', '' );
	}

	/**
	 * Check whether wc_order_stats table exists.
	 *
	 * @param object $wpdb WordPress DB object.
	 * @param string $table_name Table name.
	 * @return bool
	 */
	public static function wps_mfw_order_stats_table_exists( $wpdb, $table_name ) {
		if ( ! method_exists( $wpdb, 'prepare' ) || ! method_exists( $wpdb, 'get_var' ) ) {
			return false;
		}

		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name );

		return $table_name === (string) $wpdb->get_var( $query );
	}

	/**
	 * Get revenue from wc_order_stats.
	 *
	 * @param object $wpdb WordPress DB object.
	 * @param string $table_name Table name.
	 * @return float
	 */
	public static function wps_mfw_get_revenue_from_order_stats_table( $wpdb, $table_name ) {
		if ( ! method_exists( $wpdb, 'prepare' ) || ! method_exists( $wpdb, 'get_var' ) ) {
			return 0.0;
		}

		$paid_statuses = self::wps_mfw_get_paid_status_values();
		$placeholders  = implode( ', ', array_fill( 0, count( $paid_statuses ), '%s' ) );
		$start_date    = gmdate( 'Y-m-d H:i:s', strtotime( '-12 months' ) );
		$sql           = "SELECT COALESCE(SUM(total_sales), 0) FROM {$table_name} WHERE parent_id = 0 AND date_paid IS NOT NULL AND date_paid != '0000-00-00 00:00:00' AND date_paid >= %s AND status IN ({$placeholders})";
		$args          = array_merge( array( $sql, $start_date ), $paid_statuses );
		$query         = call_user_func_array( array( $wpdb, 'prepare' ), $args );

		return (float) $wpdb->get_var( $query );
	}

	/**
	 * Get revenue using wc_get_orders fallback.
	 *
	 * @return float
	 */
	public static function wps_mfw_get_revenue_from_wc_orders() {
		if ( ! function_exists( 'wc_get_orders' ) ) {
			return 0.0;
		}

		$cutoff_timestamp = strtotime( '-12 months', current_time( 'timestamp', true ) );
		$orders           = wc_get_orders(
			array(
				'limit'   => -1,
				'status'  => function_exists( 'wc_get_is_paid_statuses' ) ? wc_get_is_paid_statuses() : array( 'processing', 'completed' ),
				'type'    => 'shop_order',
				'parent'  => 0,
				'return'  => 'objects',
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		);

		$total = 0.0;

		foreach ( (array) $orders as $order ) {
			if ( ! is_object( $order ) ) {
				continue;
			}

			if ( ! method_exists( $order, 'get_date_paid' ) || ! $order->get_date_paid() ) {
				continue;
			}

			if ( method_exists( $order, 'get_parent_id' ) && (int) $order->get_parent_id() > 0 ) {
				continue;
			}

			$date_paid = $order->get_date_paid();
			if ( ! $date_paid || ! method_exists( $date_paid, 'getTimestamp' ) || $date_paid->getTimestamp() < $cutoff_timestamp ) {
				continue;
			}

			if ( method_exists( $order, 'get_total' ) ) {
				$total += (float) $order->get_total();
			}
		}

		return $total;
	}

	/**
	 * Get prefixed and unprefixed paid statuses.
	 *
	 * @return array
	 */
	public static function wps_mfw_get_paid_status_values() {
		$statuses = function_exists( 'wc_get_is_paid_statuses' ) ? (array) wc_get_is_paid_statuses() : array( 'processing', 'completed' );
		$values   = array();

		foreach ( $statuses as $status ) {
			$status = strtolower( preg_replace( '/[^a-z0-9_-]/i', '', (string) $status ) );

			if ( '' === $status ) {
				continue;
			}

			$values[] = $status;

			if ( 0 === strpos( $status, 'wc-' ) ) {
				$values[] = substr( $status, 3 );
			} else {
				$values[] = 'wc-' . $status;
			}
		}

		return array_values( array_unique( $values ) );
	}
}
