<?php
/**
 * Fired during plugin deactivation
 *
 * @link  https://wpswings.com/
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

		$mwb_membership_default_plans_page_id = get_option( 'mwb_membership_default_plans_page' );
		$current_post = get_post( $mwb_membership_default_plans_page_id, 'ARRAY_A' );
		$current_post['post_status'] = 'draft';
		wp_update_post( $current_post );
	}

}
