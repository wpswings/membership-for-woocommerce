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
		$this->version = $version;

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
		register_rest_route(
			'mfw-route/v1',
			'/mfw-dummy-data/',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'wps_mfw_default_callback' ),
				'permission_callback' => array( $this, 'wps_mfw_default_permission_check' ),
			)
		);
	}


	/**
	 * Begins validation process of api endpoint.
	 *
	 * @param   Array $request    All information related with the api request containing in this array.
	 * @return  Array   $result   return rest response to server from where the endpoint hits.
	 * @since    1.0.0
	 */
	public function wps_mfw_default_permission_check( $request ) {

		// Add rest api validation for each request.
		$result = true;
		return $result;
	}


	/**
	 * Begins execution of api endpoint.
	 *
	 * @param   Array $request    All information related with the api request containing in this array.
	 * @return  Array   $wps_mfw_response   return rest response to server from where the endpoint hits.
	 * @since    1.0.0
	 */
	public function wps_mfw_default_callback( $request ) {

		require_once MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'package/rest-api/version1/class-membership-for-woocommerce-api-process.php';
		$wps_mfw_api_obj = new Membership_For_Woocommerce_Api_Process();
		$wps_mfw_resultsdata = $wps_mfw_api_obj->wps_mfw_default_process( $request );
		if ( is_array( $wps_mfw_resultsdata ) && isset( $wps_mfw_resultsdata['status'] ) && 200 == $wps_mfw_resultsdata['status'] ) {
			unset( $wps_mfw_resultsdata['status'] );
			$wps_mfw_response = new WP_REST_Response( $wps_mfw_resultsdata, 200 );
		} else {
			$wps_mfw_response = new WP_Error( $wps_mfw_resultsdata );
		}
		return $wps_mfw_response;
	}
}
