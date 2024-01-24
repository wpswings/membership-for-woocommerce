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
		 * @param   object $mfw_request  data of requesting headers and other information.
		 * @return  object $wps_mfw_rest_response    returns processed data and status of operations.
		 */
		public function wps_mfw_default_process( $mfw_request ) {

			$wps_mfw_rest_response           = array();
			$wps_mfw_rest_response['status'] = 'success';
			$wps_mfw_rest_response['code']   = 200;
			$wps_mfw_rest_response['data']   = $mfw_request->get_headers();
			return $wps_mfw_rest_response;
		}

		/**
		 * This function is used to get all membership details.
		 *
		 * @param object $mfw_request mfw_request.
		 * @return object
		 */
		public function wps_list_membership_details( $mfw_request ) {

			$args = array(
				'post_type'   => 'wps_cpt_membership',
				'post_status' => 'publish',
				'numberposts' => -1,
			);

			$wps_membership_posts  = get_posts( $args );
			$wps_offer_arr         = array();
			$count                 = 1;
			if ( ! empty( $wps_membership_posts ) && is_array( $wps_membership_posts ) ) {
				foreach ( array_reverse( $wps_membership_posts ) as $value ) {

					$wps_offer_arr[] = array(
						'membership_id'   => $value->ID,
						'membership_name' => $value->post_title,
						'plan_type'       => ! empty( wps_membership_get_meta_data( $value->ID, 'wps_membership_plan_name_access_type', true ) ) ? wps_membership_get_meta_data( $value->ID, 'wps_membership_plan_name_access_type', true ) : '',
						'plan_price'      => ! empty( wps_membership_get_meta_data( $value->ID, 'wps_membership_plan_price', true ) ) ? wps_membership_get_meta_data( $value->ID, 'wps_membership_plan_price', true ) : '0',
						'plan_duration'   => ! empty( wps_membership_get_meta_data( $value->ID, 'wps_membership_plan_duration', true ) . ' ' . wps_membership_get_meta_data( $value->ID, 'wps_membership_plan_duration_type', true ) ) ? wps_membership_get_meta_data( $value->ID, 'wps_membership_plan_duration', true ) . ' ' . wps_membership_get_meta_data( $value->ID, 'wps_membership_plan_duration_type', true ) : '---',
					);
					++$count;
				}

				$wps_mfw_rest_response['status'] = 'success';
				$wps_mfw_rest_response['code']   = 200;
				$wps_mfw_rest_response['data']   = $wps_offer_arr;
			} else {

				$wps_mfw_rest_response['status'] = 'error';
				$wps_mfw_rest_response['code']   = 404;
				$wps_mfw_rest_response['data']   = esc_html__( 'No membership found', 'membership-for-woocommerce' );
			}
			return $wps_mfw_rest_response;
		}

		/**
		 * This function is used to fetch individual user membership details.
		 *
		 * @param object $mfw_request mfw_request.
		 * @return object
		 */
		public function wps_mfw_get_individual_user_membership_details( $mfw_request ) {

			$wps_mfw_rest_response = array();
			$request_response      = $mfw_request->get_params();
			$user_id               = ! empty( $request_response['user_id'] ) ? absint( trim( $request_response['user_id'] ) ) : '';

			if ( 'success' === $this->wps_mfw_validate_user_id( $user_id )['data']['status'] && '200' === $this->wps_mfw_validate_user_id( $user_id )['data']['code'] ) {

				$membership_id_arr   = get_user_meta( $user_id, 'mfw_membership_id', true );
				$membership_send_arr = array();
				$count               = 1;
				if ( ! empty( $membership_id_arr ) && is_array( $membership_id_arr ) ) {
					foreach ( $membership_id_arr as $membership_id ) {

						$membership_plan   = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
						$membership_status = wps_membership_get_meta_data( $membership_id, 'member_status', true );
						if ( empty( $membership_plan ) ) {

							continue;
						}

						if ( 'complete' !== $membership_status ) {

							continue;
						}

						$plan_duration = '';
						if ( 'limited' === $membership_plan['wps_membership_plan_name_access_type'] ) {

							$plan_duration = $membership_plan['wps_membership_plan_duration'] . ' ' . $membership_plan['wps_membership_plan_duration_type'];
						} else {

							$plan_duration = '---';
						}

						$membership_send_arr[] = array(
							'membership_id'     => $membership_id,
							'membership_name'   => $membership_plan['post_title'],
							'plan_price'        => $membership_plan['wps_membership_plan_price'],
							'plan_validity'     => $membership_plan['wps_membership_plan_name_access_type'],
							'plan_duration'     => $plan_duration,
							'membership_status' => $membership_status,
						);
						++$count;
					}

					$wps_mfw_rest_response['status'] = 'success';
					$wps_mfw_rest_response['code']   = '200';
					$wps_mfw_rest_response['data']   = $membership_send_arr;
				} else {

					$wps_mfw_rest_response['status']  = 'error';
					$wps_mfw_rest_response['code']    = '404';
					$wps_mfw_rest_response['message'] = esc_html__( 'No membership assigned', 'membership-for-woocommerce' );
				}
			} else {

				$wps_mfw_rest_response = $this->wps_mfw_validate_user_id( $user_id );
			}
			return $wps_mfw_rest_response;
		}

		/**
		 * This function is used to check whether user is exist or not.
		 *
		 * @param string $user_id user_id.
		 * @return array
		 */
		public function wps_mfw_validate_user_id( $user_id ) {
			$data = array();
			if ( ! empty( $user_id ) ) {
				$customer = new WP_User( $user_id );

				if ( $customer->ID > 0 ) {

					$data = array(
						'status' => 'success',
						'code'   => '200',
					);
				} else {

					$data = array(
						'status'  => 'error',
						'code'    => '404',
						'message' => esc_html__( 'Invalid user ID', 'membership-for-woocommerce' ),
					);
				}
			} else {

				$data = array(
					'status'  => 'error',
					'code'    => '404',
					'message' => esc_html__( 'User not found', 'membership-for-woocommerce' ),

				);
			}
			$wps_mfw_rest_response['data'] = $data;
			return $wps_mfw_rest_response;
		}
	}
}
