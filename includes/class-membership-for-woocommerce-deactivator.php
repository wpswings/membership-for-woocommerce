<?php
/**
 * Fired during plugin deactivation
 *
 * @link  https://makewebbetter.com/
 * @since 1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes
 */
class Membership_For_Woocommerce_Deactivator {


	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since 1.0.0
	 */
	public static function membership_for_woocommerce_deactivate() {
		if ( get_option( 'mfw_radio_reset_license', false ) ) {
			$license_key = get_option( 'mwb_mfw_license_key', false );
			$api_params = array(
				'slm_action' => 'slm_deactivate',
				'secret_key' => MEMBERSHIP_FOR_WOOCOMMERCE_SPECIAL_SECRET_KEY,
				'license_key' => $license_key,
				'registered_domain' => $_SERVER['SERVER_NAME'],
				'item_reference' => urlencode( MEMBERSHIP_FOR_WOOCOMMERCE_ITEM_REFERENCE ),
				'product_reference' => 'MWBPK-2965',
			);
			$query = esc_url_raw( add_query_arg( $api_params, MEMBERSHIP_FOR_WOOCOMMERCE_LICENSE_SERVER_URL ) );
			$response = wp_remote_get(
				$query,
				array(
					'timeout' => 20,
					'sslverify' => false,
				)
			);

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			if ( isset( $license_data->result ) && 'success' === $license_data->result ) {
				   delete_option( 'mwb_mfw_license_check' );
				   delete_option( 'mwb_mfw_license_key' );
			}
		}

	}

}
