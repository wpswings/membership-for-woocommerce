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
							 'post_status'  => 'private',
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
							 update_post_meta( $wps_membership_product_id, '_regular_price', 0 );
							 update_post_meta( $wps_membership_product_id, '_price', 0 );
							 update_post_meta( $wps_membership_product_id, '_visibility', 'hidden' );
							 update_post_meta( $wps_membership_product_id, '_virtual', 'yes' );

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

			wp_clear_scheduled_hook( 'makewebbetter_tracker_send_event' );
			wp_schedule_event( time() + 10, apply_filters( 'makewebbetter_tracker_event_recurrence', 'daily' ), 'makewebbetter_tracker_send_event' );
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
					 'post_status'  => 'private',
					 'post_title'   => 'Membership Product',
					 'post_type'    => 'product',
					 'post_author'  => 1,
					 'post_content' => stripslashes( html_entity_decode( 'Auto generated product for membership please do not delete or update.', ENT_QUOTES, 'UTF-8' ) ),
				 );

				 $wps_membership_product_id = wp_insert_post( $wps_membership_product );

				 if ( ! is_wp_error( $wps_membership_product_id ) ) {

					 $product = wc_get_product( $wps_membership_product_id );
					 wp_set_object_terms( $wps_membership_product_id, 'simple', 'product_type' );
					 update_post_meta( $wps_membership_product_id, '_regular_price', 0 );
					 update_post_meta( $wps_membership_product_id, '_price', 0 );
					 update_post_meta( $wps_membership_product_id, '_visibility', 'hidden' );
					 update_post_meta( $wps_membership_product_id, '_virtual', 'yes' );

					 if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {

						 $product->set_reviews_allowed( false );
						 $product->set_catalog_visibility( 'hidden' );
						 $product->save();
					 }

					 update_option( 'wps_membership_default_product', $wps_membership_product_id );
				 }
			}
			wp_clear_scheduled_hook( 'makewebbetter_tracker_send_event' );
			wp_schedule_event( time() + 10, apply_filters( 'makewebbetter_tracker_event_recurrence', 'daily' ), 'makewebbetter_tracker_send_event' );
		}
	}



		/**
        * Upgrade_wp_postmeta. (use period)
        *
        * Upgrade_wp_postmeta.
        *
        * @since    1.0.0
        */
		public static function mfw_upgrade_wp_postmeta() {
 
			$post_meta_keys = array(
				'mwb_membership_plan_price',
				'mwb_membership_plan_info',
				'mwb_membership_plan_name_access_type',
				'mwb_membership_plan_duration',
				'mwb_membership_plan_duration_type',
				'mwb_membership_subscription',
				'mwb_membership_subscription_expiry',
				'mwb_membership_subscription_expiry_type',
				'mwb_membership_plan_recurring',
				'mwb_membership_plan_access_type',
				'mwb_membership_plan_time_duration',
				'mwb_membership_plan_time_duration_type',
				'mwb_membership_plan_offer_price_type',
				'mwb_memebership_plan_discount_price',
				'mwb_memebership_plan_free_shipping',
				'mwb_membership_show_notice',
				'mwb_membership_notice_message',
				'mwb_membership_plan_target_categories',
				'mwb_membership_plan_target_ids',
				'mwb_membership_plan_post_target_ids',
				'mwb_membership_plan_target_tags',
				'mwb_membership_plan_target_post_categories',
				'mwb_membership_club',
				'mwb_membership_plan_page_target_ids',
				'mwb_membership_plan_target_disc_categories',
				'mwb_membership_plan_target_disc_tags',
				'mwb_membership_plan_target_disc_ids',
				'mwb_membership_product_offer_price_type',
				'mwb_memebership_product_discount_price',
				'mwb_member_user',
				'_mwb_membership_discount_',
				'_mwb_membership_exclude',
				'_mwb_membership_discount_product_',
				'_mwb_membership_discount_product_price',
				'_mwb_membership_discount_product_',  
				);

				
			foreach ( $post_meta_keys as $key => $meta_keys ) {
					$products = get_posts(
						array(
							'numberposts' => -1,
							'post_status' => 'publish',
							'fields'      => 'ids', // return only ids.
							'meta_key'    => $meta_keys, //phpcs:ignore
							'post_type'   => 'product',
							'order'       => 'ASC',
						)
					);
  
				if ( ! empty( $products ) && is_array( $products ) ) {
					foreach ( $products as $k => $product_id ) {
						$values   = get_post_meta( $product_id, $meta_keys, true );
						$new_key = str_replace( 'mwb_', 'wps_', $meta_keys );
  
						if ( ! empty( get_post_meta( $product_id, $new_key, true ) ) ) {
							continue;
						}
				   
						$arr_val_post = array();
						if ( is_array( $values  )) {
							foreach ( $values  as $key => $value){
								$keys = str_replace( 'mwb_', 'wps_', $key );
					   
								$new_key1 = str_replace( 'mwb_', 'wps_', $value );
								$arr_val_post[ $key ] = $new_key1;
							}
							update_post_meta( $product_id, $new_key, $arr_val_post );
						} else {
							update_post_meta( $product_id, $new_key, $values );
						}
					}
				}
			}
		}

  
		/**
		 * Upgrade_wp_options. (use period)
		 *
		 * Upgrade_wp_options.
		 *
		 * @since    1.0.0
		 */
		public static function mfw_upgrade_wp_options() {
			$wp_options = array(
				'mwb_membership_default_plans_page' => '',
				'mwb_membership_default_product'  => '',
				'makewebbetter_tracker_last_send'  => '',
				'mwb_mfwp_license_key'  => '',
				'mwb_mfwp_license_check'  => '',
				'mwb_membership_email_subject'  => '',
				'mwb_membership_email_content'  => '',
				'mwb_mfw_onboarding_data_skipped'  => '',
				'mwb_mfw_onboarding_data_sent'  => '',
				
				'mwb_membership_number_of_expiry_days' =>'',

				'mwb_membership_global_options' =>'',
				'mwb_membership_enable_plugin' =>'',
				'mwb_membership_plan_user_history' =>'',
				'mwb_membership_create_user_after_payment' =>'',
				'mwb_membership_for_woo_delete_data' =>'',
				'mwb_membership_attach_invoice' =>'',
			);
  
			foreach ( $wp_options as $key => $value ) {
  
				$new_key = str_replace( 'mwb_', 'wps_', $key );
				if ( ! empty( get_option( $new_key ) ) ) {
					continue;
				}
				$new_value = get_option( $key, $value );
  
				$arr_val = array();
				if ( is_array( $new_value )) {
					foreach ( $new_value as $key => $value) {
						$new_key1 = str_replace( 'mwb_', 'wps_', $key );
						$arr_val[ $new_key1 ] = $value;
					}
					update_option( $new_key, $arr_val );
				}
				else {
					update_option( $new_key, $new_value );
				}
			}
		}

}
