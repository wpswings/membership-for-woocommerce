<?php
/**
 * Fired during plugin activation
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Membership_For_Woocommerce_Api_Process' ) ) {

	/**
	 * The plugin API class.
	 *
	 * This is used to define the functions and data manipulation for custom endpoints.
	 *
	 * @since      1.0.0
	 * @package    Membership_For_Woocommerce
	 * @subpackage Membership_For_Woocommerce/includes
	 */
	class Membership_For_Woocommerce_Api_Process {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {

		}

		/**
		 * Define the function to process data for custom endpoint.
		 *
		 * @since    1.0.0
		 * @param   Array $mfw_request  data of requesting headers and other information.
		 * @return  Array $wps_mfw_rest_response    returns processed data and status of operations.
		 */
		public function wps_mfw_default_process( $mfw_request ) {
			$wps_mfw_rest_response = array();

			// Write your custom code here.

			$wps_mfw_rest_response['status'] = 200;
			$wps_mfw_rest_response['data'] = $mfw_request->get_headers();
			return $wps_mfw_rest_response;
		}
	}
}
