<?php
/**
 * Fired during plugin activation
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes
 * @author     Make Web Better <plugins@makewebbetter.com>
 */
class Membership_For_Woocommerce_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		// Creating Instance of the global functions class.
		$global_class = Membership_For_Woocommerce_Global_Functions::get();

		add_role(
			'member',
			__( 'Member', 'membership-for-woocommerce' ),
			array(
				'read' => true,
			)
		);

		/**
		 * Generating default membership plans page at the time of plugin activation.
		 */
		$mwb_membership_default_plans_page_id = get_option( 'mwb_membership_default_plans_page', '' );

		$page_content = '5' <= get_bloginfo( 'version' ) ? $global_class->gutenberg_content() : '[mwb_membership_default_plans_page]';

		if ( empty( $mwb_membership_default_plans_page_id ) || 'publish' !== get_post_status( $mwb_membership_default_plans_page_id ) ) {

			$mwb_membership_plans_page = array(
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'post_content'   => $page_content,
				'post_name'      => 'membership-plans',
				'post_status'    => 'publish',
				'post_title'     => 'Membership Plans',
				'post_type'      => 'page',
			);

			$mwb_membership_plans_post = wp_insert_post( $mwb_membership_plans_page );

			update_option( 'mwb_membership_default_plans_page', $mwb_membership_plans_post );
		}
	}

}
