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

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes
 */
class Membership_For_Woocommerce_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @param mixed $network_wide is used for multisite.
	 *
	 * @since    1.0.0
	 */
	public static function membership_for_woocommerce_activate( $network_wide ) {

	}

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @param [type] $network_wide is for multisite.
	 *
	 * @since    1.0.0
	 */
	public static function activate( $network_wide ) {

		global $wpdb;

		if ( is_multisite() || ! empty( $network_wide ) ) {
			$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
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

				$wps_membership_default_plans_page_id = get_option( 'mwb_membership_default_plans_page' );
				if ( ! empty( $wps_membership_default_plans_page_id ) ) {
					wp_delete_post( $wps_membership_default_plans_page_id );
					delete_option( 'mwb_membership_default_plans_page' );
				}
				$wps_membership_default_plans_page_id = get_option( 'wps_membership_default_plans_page' );
				if ( empty( $wps_membership_default_plans_page_id ) ) {

					$page_content = '5' <= get_bloginfo( 'version' ) ? $global_class->gutenberg_content() : '[wps_membership_default_plans_page]';

					if ( empty( $wps_membership_default_plans_page_id ) || 'publish' !== get_post_status( $wps_membership_default_plans_page_id ) ) {

						$wps_membership_plans_page = array(
							'comment_status' => 'closed',
							'ping_status'    => 'closed',
							'post_content'   => $page_content,
							'post_name'      => 'membership-plans',
							'post_status'    => 'publish',
							'post_title'     => 'Membership Plans',
							'post_type'      => 'page',
						);

						$wps_membership_plans_post = wp_insert_post( $wps_membership_plans_page );

						update_option( 'wps_membership_default_plans_page', $wps_membership_plans_post );
					}
				} else {
					$current_post = get_post( $wps_membership_default_plans_page_id, 'ARRAY_A' );
					$current_post['post_status'] = 'publish';
					wp_update_post( $current_post );
				}

				/**
				 * Generating default membership plans page at the time of plugin activation.
				 */
				$wps_membership_default_product = get_option( 'wps_membership_default_product' );

				if ( empty( $wps_membership_default_product ) || 'private' !== get_post_status( $wps_membership_default_product ) ) {

						 $wps_membership_product = array(
							 'post_name'    => 'membership-product',
							 'post_status'  => 'publish',
							 'post_title'   => 'Membership Product',
							 'post_type'    => 'product',
							 'post_author'  => 1,
							 'price'  => 1,
							 'post_content' => stripslashes( html_entity_decode( 'Auto generated product for membership please do not delete or update.', ENT_QUOTES, 'UTF-8' ) ),
						 );

						 $wps_membership_product_id = wp_insert_post( $wps_membership_product );

						 if ( ! is_wp_error( $wps_membership_product_id ) ) {

							 $product = wc_get_product( $wps_membership_product_id );

							 wp_set_object_terms( $wps_membership_product_id, 'simple', 'product_type' );
							 wps_membership_update_meta_data( $wps_membership_product_id, '_regular_price', 0 );
							 wps_membership_update_meta_data( $wps_membership_product_id, '_price', 0 );
							 wps_membership_update_meta_data( $wps_membership_product_id, '_visibility', 'public' );
							 wps_membership_update_meta_data( $wps_membership_product_id, '_virtual', 'yes' );

							 if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {

								 $product->set_reviews_allowed( false );
								 $product->set_catalog_visibility( 'hidden' );
								 $product->save();
							 }

							 update_option( 'wps_membership_default_product', $wps_membership_product_id );

						 }
				}

				restore_current_blog();
			}

			wp_clear_scheduled_hook( 'wpswings_tracker_send_event' );

			/**
			 * Filter to track event recurrance.
			 *
			 * @since 1.0.0
			 */
			wp_schedule_event( time() + 10, apply_filters( 'wpswings_tracker_event_recurrence', 'daily' ), 'wpswings_tracker_send_event' );
		} else {

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

			$wps_membership_default_plans_page_id = get_option( 'mwb_membership_default_plans_page' );
			if ( ! empty( $wps_membership_default_plans_page_id ) ) {
				wp_delete_post( $wps_membership_default_plans_page_id );
				delete_option( 'mwb_membership_default_plans_page' );
			}

			$wps_membership_default_plans_page_id = get_option( 'wps_membership_default_plans_page' );

			if ( empty( $wps_membership_default_plans_page_id ) ) {

				$page_content = '5' <= get_bloginfo( 'version' ) ? $global_class->gutenberg_content() : '[wps_membership_default_plans_page]';

				if ( empty( $wps_membership_default_plans_page_id ) || 'publish' !== get_post_status( $wps_membership_default_plans_page_id ) ) {

					$wps_membership_plans_page = array(
						'comment_status' => 'closed',
						'ping_status'    => 'closed',
						'post_content'   => $page_content,
						'post_name'      => 'membership-plans',
						'post_status'    => 'publish',
						'post_title'     => 'Membership Plans',
						'post_type'      => 'page',
					);

					$wps_membership_plans_post = wp_insert_post( $wps_membership_plans_page );

					update_option( 'wps_membership_default_plans_page', $wps_membership_plans_post );
				}
			} else {
				$current_post                = get_post( $wps_membership_default_plans_page_id, 'ARRAY_A' );
				$current_post['post_status'] = 'publish';
				wp_update_post( $current_post );
			}

			/**
			 * Generating default membership plans page at the time of plugin activation.
			 */
			$wps_membership_default_product = get_option( 'wps_membership_default_product' );

			if ( empty( $wps_membership_default_product ) || 'private' !== get_post_status( $wps_membership_default_product ) ) {

				 $wps_membership_product = array(
					 'post_name'    => 'membership-product',
					 'post_status'  => 'publish',
					 'post_title'   => 'Membership Product',
					 'post_type'    => 'product',
					 'post_author'  => 1,
					 'post_content' => stripslashes( html_entity_decode( 'Auto generated product for membership please do not delete or update.', ENT_QUOTES, 'UTF-8' ) ),
				 );

				 $wps_membership_product_id = wp_insert_post( $wps_membership_product );

				 if ( ! is_wp_error( $wps_membership_product_id ) ) {

					 $product = wc_get_product( $wps_membership_product_id );
					 wp_set_object_terms( $wps_membership_product_id, 'simple', 'product_type' );
					 wps_membership_update_meta_data( $wps_membership_product_id, '_regular_price', 0 );
					 wps_membership_update_meta_data( $wps_membership_product_id, '_price', 0 );
					 wps_membership_update_meta_data( $wps_membership_product_id, '_visibility', 'public' );
					 wps_membership_update_meta_data( $wps_membership_product_id, '_virtual', 'yes' );

					 if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {

						 $product->set_reviews_allowed( false );
						 $product->set_catalog_visibility( 'hidden' );
						 $product->save();
					 }

					 update_option( 'wps_membership_default_product', $wps_membership_product_id );
				 }
			}
			wp_clear_scheduled_hook( 'wpswings_tracker_send_event' );

			/**
			 * Filter for tracking recurrance.
			 *
			 * @since 1.0.0
			 */
			wp_schedule_event( time() + 10, apply_filters( 'wpswings_tracker_event_recurrence', 'daily' ), 'wpswings_tracker_send_event' );
		}
	}
}
