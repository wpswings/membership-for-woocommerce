<?php
/**
 * Fired during plugin activation
 *
 * @link       https://makewebbetter.com/
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
	 * @author     MakeWebBetter <makewebbetter.com>
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
		 * @return  Array $mwb_mfw_rest_response    returns processed data and status of operations.
		 */
		public function mwb_mfw_default_process( $mfw_request ) {
			$mwb_mfw_rest_response = array();

			// Write your custom code here.

			$mwb_mfw_rest_response['status'] = 200;
			$mwb_mfw_rest_response['data'] = $mfw_request->get_headers();
			return $mwb_mfw_rest_response;
		}
	}
}
