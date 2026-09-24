<?php
/**
 * The file that defines the core plugin api class
 *
 * A class definition that includes api's endpoints and functions used across the plugin
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/package/rest-api/version1
 */

/**
 * The core plugin  api class.
 *
 * This is used to define internationalization, api-specific hooks, and
 * endpoints for plugin.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/package/rest-api/version1
 */
class Membership_For_Woocommerce_Rest_Api {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin api.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the merthods, and set the hooks for the api and
	 *
	 * @since    1.0.0
	 * @param   string $plugin_name    Name of the plugin.
	 * @param   string $version        Version of the plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Define endpoints for the plugin.
	 *
	 * Uses the Membership_For_Woocommerce_Rest_Api class in order to create the endpoint
	 * with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function wps_mfw_add_endpoint() {
		// check API setting enable.
		if ( 'on' === get_option( 'wps_membership_enable_api_settings', true ) ) {

			// default endpoints to check API.
			register_rest_route(
				'wps-mfw',
				'/mfw-dummy-data/',
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'wps_mfw_default_callback' ),
					'permission_callback' => array( $this, 'wps_mfw_default_permission_check' ),
				)
			);

			// endpoints to show membership offers.
			register_rest_route(
				'wps-mfw',
				'/get-membership-offers/',
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'wps_mfw_get_membership_offers' ),
					'permission_callback' => array( $this, 'wps_mfw_default_permission_check' ),
				),
			);

			// endpoints to get individual membership of users.
			register_rest_route(
				'wps-mfw',
				'/get-user-membership',
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'wps_mfw_get_user_membership' ),
					'permission_callback' => array( $this, 'wps_mfw_user_membership_permission_check' ),
				),
			);
		}
	}

	/**
	 * This function is used to create process class boject.
	 *
	 * @return object
	 */
	public function wps_mfw_creating_api_process_class_obj() {

		require_once MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'package/rest-api/version1/class-membership-for-woocommerce-api-process.php';
		$wps_mfw_api_obj = new Membership_For_Woocommerce_Api_Process();
		return $wps_mfw_api_obj;
	}

	/**
	 * Begins execution of api endpoint.
	 *
	 * @param   object $request          All information related with the api request containing in this array.
	 * @return  object $wps_mfw_response return rest response to server from where the endpoint hits.
	 * @since    1.0.0
	 */
	public function wps_mfw_default_callback( $request ) {

		$wps_mfw_resultsdata = $this->wps_mfw_creating_api_process_class_obj()->wps_mfw_default_process( $request );
		if ( is_array( $wps_mfw_resultsdata ) && isset( $wps_mfw_resultsdata['status'] ) && 'success' == $wps_mfw_resultsdata['status'] ) {

			$wps_mfw_response = new WP_REST_Response( $wps_mfw_resultsdata, 200 );
		} else {

			$wps_mfw_response = new WP_Error( $wps_mfw_resultsdata );
		}
		return $wps_mfw_response;
	}

	/**
	 * Begins validation process of api endpoint.
	 *
	 * @param   object $request    All information related with the api request containing in this array.
	 * @return  bool   $result   return rest response to server from where the endpoint hits.
	 * @since    1.0.0
	 */
	public function wps_mfw_default_permission_check( $request ) {

		$result           = false;
		$request_response = $request->get_params();

		// Get the stored consumer secret from options.
		$wps_membership_api_consumer_secret_keys = get_option( 'wps_membership_api_consumer_secret_keys', false );

		// Reject the request if no consumer secret has been configured.
		// This prevents authentication bypass when the secret hasn't been generated.
		if ( empty( $wps_membership_api_consumer_secret_keys ) ) {
			return false;
		}

		// Get the consumer secret from the request.
		$consumer_secret = isset( $request_response['consumer_secret'] ) ? trim( $request_response['consumer_secret'] ) : '';

		// Reject the request if no consumer secret was provided.
		if ( empty( $consumer_secret ) ) {
			return false;
		}

		// Use hash_equals() for constant-time comparison to prevent timing attacks.
		// Both strings must be trimmed and non-empty at this point.
		$wps_membership_api_consumer_secret_keys = trim( $wps_membership_api_consumer_secret_keys );

		if ( hash_equals( $wps_membership_api_consumer_secret_keys, $consumer_secret ) ) {
			$result = true;
		}

		return $result;
	}

	/**
	 * Permission check for user membership endpoint with authorization verification.
	 *
	 * This function verifies both the consumer secret AND checks if the requesting
	 * user has authorization to view the requested user_id's membership data.
	 * This prevents Insecure Direct Object Reference (IDOR) vulnerabilities.
	 *
	 * @param   object $request    All information related with the api request.
	 * @return  bool   Whether the request is authorized.
	 * @since    1.0.0
	 */
	public function wps_mfw_user_membership_permission_check( $request ) {

		// First, verify the consumer secret (same security checks as default).
		if ( ! $this->wps_mfw_default_permission_check( $request ) ) {
			return false;
		}

		// Get the requested user_id from the request.
		$request_response = $request->get_params();
		$requested_user_id = isset( $request_response['user_id'] ) ? absint( $request_response['user_id'] ) : 0;

		// Get the current user (if authenticated).
		$current_user_id = get_current_user_id();

		// Allow if the current user is an administrator.
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		// Allow if the authenticated user is requesting their own data.
		if ( $current_user_id > 0 && $current_user_id === $requested_user_id ) {
			return true;
		}

		// Otherwise, deny access to prevent IDOR attacks.
		return false;
	}

	/**
	 * This function is used to get all membership details.
	 *
	 * @param object $request request.
	 * @return object
	 */
	public function wps_mfw_get_membership_offers( $request ) {

		$wps_mfw_resultsdata = $this->wps_mfw_creating_api_process_class_obj()->wps_list_membership_details( $request );
		if ( is_array( $wps_mfw_resultsdata ) && isset( $wps_mfw_resultsdata['status'] ) && 'success' == $wps_mfw_resultsdata['status'] ) {

			$wps_mfw_response = new WP_REST_Response( $wps_mfw_resultsdata, 200 );
		} else {

			$wps_mfw_response = new WP_REST_Response( $wps_mfw_resultsdata );
		}
		return $wps_mfw_response;
	}

	/**
	 * This function is used to get individual user membership.
	 *
	 * @param object $request request.
	 * @return object
	 */
	public function wps_mfw_get_user_membership( $request ) {

		$wps_mfw_resultsdata = $this->wps_mfw_creating_api_process_class_obj()->wps_mfw_get_individual_user_membership_details( $request );
		if ( is_array( $wps_mfw_resultsdata ) && isset( $wps_mfw_resultsdata['status'] ) && 'success' == $wps_mfw_resultsdata['status'] ) {

			$wps_mfw_response = new WP_REST_Response( $wps_mfw_resultsdata, 200 );
		} else {

			$wps_mfw_response = new WP_REST_Response( $wps_mfw_resultsdata );
		}
		return $wps_mfw_response;
	}
}
