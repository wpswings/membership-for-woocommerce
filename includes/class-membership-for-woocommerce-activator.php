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
				$mwb_membership_default_plans_page_id = get_option( 'mwb_membership_default_plans_page' );
				if ( empty( $mwb_membership_default_plans_page_id ) ) {

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
				} else {
						  $current_post = get_post( $mwb_membership_default_plans_page_id, 'ARRAY_A' );
						  $current_post['post_status'] = 'publish';
						  wp_update_post( $current_post );
				}

				/**
				 * Generating default membership plans page at the time of plugin activation.
				 */
				$mwb_membership_default_product = get_option( 'mwb_membership_default_product' );

				if ( empty( $mwb_membership_default_product ) || 'private' !== get_post_status( $mwb_membership_default_product ) ) {

						 $mwb_membership_product = array(
							 'post_name'    => 'membership-product',
							 'post_status'  => 'private',
							 'post_title'   => 'Membership Product',
							 'post_type'    => 'product',
							 'post_author'  => 1,
							 'price'  => 1,
							 'post_content' => stripslashes( html_entity_decode( 'Auto generated product for membership please do not delete or update.', ENT_QUOTES, 'UTF-8' ) ),
						 );

						 $mwb_membership_product_id = wp_insert_post( $mwb_membership_product );

						 if ( ! is_wp_error( $mwb_membership_product_id ) ) {

							 $product = wc_get_product( $mwb_membership_product_id );

							 wp_set_object_terms( $mwb_membership_product_id, 'simple', 'product_type' );
							 update_post_meta( $mwb_membership_product_id, '_stock_status', 'instock' );
							 update_post_meta( $mwb_membership_product_id, 'total_sales', '0' );
							 update_post_meta( $mwb_membership_product_id, '_downloadable', 'no' );
							 update_post_meta( $mwb_membership_product_id, '_virtual', 'yes' );
							 update_post_meta( $mwb_membership_product_id, '_regular_price', '1' );
							 update_post_meta( $mwb_membership_product_id, '_sale_price', '' );
							 update_post_meta( $mwb_membership_product_id, '_purchase_note', '' );
							 update_post_meta( $mwb_membership_product_id, '_featured', 'no' );
							 update_post_meta( $mwb_membership_product_id, '_weight', '' );
							 update_post_meta( $mwb_membership_product_id, '_length', '' );
							 update_post_meta( $mwb_membership_product_id, '_width', '' );
							 update_post_meta( $mwb_membership_product_id, '_height', '' );
							 update_post_meta( $mwb_membership_product_id, '_sku', '' );
							 update_post_meta( $mwb_membership_product_id, '_product_attributes', array() );
							 update_post_meta( $mwb_membership_product_id, '_sale_price_dates_from', '' );
							 update_post_meta( $mwb_membership_product_id, '_sale_price_dates_to', '' );
							 update_post_meta( $mwb_membership_product_id, '_price', '1' );
							 update_post_meta( $mwb_membership_product_id, '_sold_individually', 'yes' );
							 update_post_meta( $mwb_membership_product_id, '_manage_stock', 'no' );
							 update_post_meta( $mwb_membership_product_id, '_backorders', 'no' );
							 update_post_meta( $mwb_membership_product_id, '_stock', '' );

							 if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {

								 $product->set_reviews_allowed( false );
								 $product->set_catalog_visibility( 'hidden' );
								 $product->save();
							 }

							 update_option( 'mwb_membership_default_product', $mwb_membership_product_id );
									 
						
							}
				}
				restore_current_blog();
			}
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
			$mwb_membership_default_plans_page_id = get_option( 'mwb_membership_default_plans_page' );
			if ( empty( $mwb_membership_default_plans_page_id ) ) {

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
			} else {
				$current_post = get_post( $mwb_membership_default_plans_page_id, 'ARRAY_A' );
				$current_post['post_status'] = 'publish';
				wp_update_post( $current_post );
			}

			/**
			 * Generating default membership plans page at the time of plugin activation.
			 */
			$mwb_membership_default_product = get_option( 'mwb_membership_default_product' );

			if ( empty( $mwb_membership_default_product ) || 'private' !== get_post_status( $mwb_membership_default_product ) ) {

				 $mwb_membership_product = array(
					 'post_name'    => 'membership-product',
					 'post_status'  => 'private',
					 'post_title'   => 'Membership Product',
					 'post_type'    => 'product',
					 'post_author'  => 1,
					 'post_content' => stripslashes( html_entity_decode( 'Auto generated product for membership please do not delete or update.', ENT_QUOTES, 'UTF-8' ) ),
				 );

				 $mwb_membership_product_id = wp_insert_post( $mwb_membership_product );

				 if ( ! is_wp_error( $mwb_membership_product_id ) ) {

					 $product = wc_get_product( $mwb_membership_product_id );

					 wp_set_object_terms( $mwb_membership_product_id, 'simple', 'product_type' );
					 update_post_meta( $mwb_membership_product_id, '_stock_status', 'instock' );
					 update_post_meta( $mwb_membership_product_id, 'total_sales', '0' );
					 update_post_meta( $mwb_membership_product_id, '_downloadable', 'no' );
					 update_post_meta( $mwb_membership_product_id, '_virtual', 'yes' );
					 update_post_meta( $mwb_membership_product_id, '_regular_price', '1' );
					 update_post_meta( $mwb_membership_product_id, '_sale_price', '' );
					 update_post_meta( $mwb_membership_product_id, '_purchase_note', '' );
					 update_post_meta( $mwb_membership_product_id, '_featured', 'no' );
					 update_post_meta( $mwb_membership_product_id, '_weight', '' );
					 update_post_meta( $mwb_membership_product_id, '_length', '' );
					 update_post_meta( $mwb_membership_product_id, '_width', '' );
					 update_post_meta( $mwb_membership_product_id, '_height', '' );
					 update_post_meta( $mwb_membership_product_id, '_sku', '' );
					 update_post_meta( $mwb_membership_product_id, '_product_attributes', array() );
					 update_post_meta( $mwb_membership_product_id, '_sale_price_dates_from', '' );
					 update_post_meta( $mwb_membership_product_id, '_sale_price_dates_to', '' );
					 update_post_meta( $mwb_membership_product_id, '_price', '' );
					 update_post_meta( $mwb_membership_product_id, '_sold_individually', 'yes' );
					 update_post_meta( $mwb_membership_product_id, '_manage_stock', 'no' );
					 update_post_meta( $mwb_membership_product_id, '_backorders', 'no' );
					 update_post_meta( $mwb_membership_product_id, '_stock', '' );

					 if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {

						 $product->set_reviews_allowed( false );
						 $product->set_catalog_visibility( 'hidden' );
						 $product->save();
					 }

					 update_option( 'mwb_membership_default_product', $mwb_membership_product_id );
				 }
			}
		}
	}

}
