<?php
/**
 * The common functionality of the plugin.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/common
 */

/**
 * The common functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the common stylesheet and JavaScript.
 * namespace membership_for_woocommerce_common.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/common
 */
class Membership_For_Woocommerce_Common {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Creating Instance of the global functions class.
	 *
	 * @var object
	 */
	public $global_class;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->global_class = Membership_For_Woocommerce_Global_Functions::get();
	}

	/**
	 * Register the stylesheets for the common side of the site.
	 *
	 * @since    1.0.0
	 */
	public function mfw_common_enqueue_styles() {
		wp_enqueue_style( $this->plugin_name . 'common', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'common/css/membership-for-woocommerce-common.css', array(), MEMBERSHIP_FOR_WOOCOMMERCE_VERSION, 'all' );
	}

	/**
	 * Register the JavaScript for the common side of the site.
	 *
	 * @since    1.0.0
	 */
	public function mfw_common_enqueue_scripts() {
		wp_register_script( $this->plugin_name . 'common', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'common/js/membership-for-woocommerce-common.js', array( 'jquery' ), MEMBERSHIP_FOR_WOOCOMMERCE_VERSION, false );
		wp_localize_script(
			$this->plugin_name . 'common',
			'mfw_common_param',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'wps_common_ajax_nonce' ),
			)
		);
		wp_enqueue_script( $this->plugin_name . 'common' );
	}

	/**
	 * Ajax function for membership checkout.
	 *
	 * @return void
	 */
	public function wps_membership_checkout() {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		check_ajax_referer( 'auth_adv_nonce', 'nonce' );
		$plan_id    = isset( $_POST['plan_id'] ) ? sanitize_text_field( wp_unslash( $_POST['plan_id'] ) ) : '';
		$plan_price = isset( $_POST['plan_price'] ) ? sanitize_text_field( wp_unslash( $_POST['plan_price'] ) ) : '';
		$plan_title = isset( $_POST['plan_title'] ) ? sanitize_text_field( wp_unslash( $_POST['plan_title'] ) ) : '';

		$wps_membership_default_product = get_option( 'wps_membership_default_product', '' );

		global $wp_session;

		$wp_session['plan_price'] = $plan_price;
		$wp_session['plan_title'] = $plan_title;
		$wp_session['plan_id']    = $plan_id;
		WC()->session->set( 'plan_id', $plan_id );
		WC()->session->set( 'plan_title', $plan_title );
		WC()->session->set( 'plan_price', $plan_price );
		WC()->session->set( 'product_id', $wps_membership_default_product );

		$cart_item_data = add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_membership_product_price_to_cart_item_data' ), 10, 2 );

		$redirect_url = wc_get_cart_url();
		echo wp_json_encode( $redirect_url );

		wp_die();
	}

	/**
	 * WooCommerce add cart item data.
	 *
	 * @param array $cart_item_data cart item data.
	 * @param int   $product_id product id.
	 * @return array
	 */
	public function add_membership_product_price_to_cart_item_data( $cart_item_data, $product_id ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return $cart_item_data;
		}
		$product = wc_get_product( $product_id );

		global $wp_session;

		if ( $product ) {
			$cart_item_data['plan_price'] = $wp_session['plan_price'];
			$cart_item_data['plan_title'] = $wp_session['plan_title'];
		}

		/**
		 * Filter for get cart items.
		 *
		 * @since 1.0.0
		 */
		$cart_item_data = apply_filters( 'add_membership_product_price_to_cart_item_data', $cart_item_data );
		$wps_membership_default_product = get_option( 'wps_membership_default_product', '' );
		return $cart_item_data;
	}



	/**
	 * Callback function for file Upload and import.
	 */
	public function wps_membership_csv_file_upload() {
		check_ajax_referer( 'plan-import-nonce', 'nonce' );
		if ( is_admin() || ( is_multisite() && is_super_admin() ) ) {

			include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-membership-activity-helper.php';

			// Handling file upload using activity helper class..
			$activity_class = new Membership_Activity_Helper( 'csv-uploads', 'uploads' );
			// phpcs:disable
			$csv_file    = ! empty( $_FILES['file'] ) ? map_deep( wp_unslash( $_FILES['file'] ), 'sanitize_text_field' ) : ''; // phpcs:ignore

			// phpcs:enable
			$upload_file = $activity_class->do_upload( $csv_file, array( 'csv' ) );

			if ( ! current_user_can( 'edit_posts' ) ) {
				exit;
			}

			if ( $upload_file && ( true === $upload_file['result'] ) ) {

				$file_url = $upload_file['url'];
				$csv      = array_map( 'str_getcsv', file( $file_url ) );

				unset( $csv[0] ); // Removing first key after CSV data is converted to array.

				// Getting a formatted CSV data.
				$formatted_csv_data = $this->global_class->csv_data_map( $csv );

				// Getting all Product titles from woocommerce store.
				$all_prod_title = $this->global_class->all_prod_title();

				// Getting all Category titles from woocommerce store.
				$all_cat_title = $this->global_class->all_cat_title();

				$prd_check = '';
				$cat_check = '';

				$csv_prod_title = $this->global_class->csv_prod_title( $csv ); // Getting all product titles from csv.
				$csv_cate_title = $this->global_class->csv_cat_title( $csv ); // Getting all category titles from csv.

				if ( is_array( $csv_prod_title ) && is_array( $csv_cate_title ) ) {

					foreach ( $csv_prod_title as $csv_prod_title_key => $csv_prod_title_value ) {

						if ( in_array( $csv_prod_title_value, $all_prod_title, true ) ) {

							$prd_check = true;
						}
					}

					foreach ( $csv_cate_title as $csv_cate_title_key => $csv_cate_title_value ) {

						if ( in_array( $csv_cate_title_value, $all_cat_title, true ) ) {

							$cat_check = true;
						}
					}
				}

				$args = array(
					'post_type'   => 'wps_cpt_membership',
					'post_status' => array( 'publish' ),
					'numberposts' => -1,
				);

				$check = '';
				$all_plan_array = array();
				$all_plans = get_posts( $args );
				foreach ( $all_plans as $single_plan ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited */

					array_push( $all_plan_array, $single_plan->post_title );
				}

				// If product ids and category ids from csv match from those of woocommerce, then only import the file.
				if ( true === $prd_check || true === $cat_check ) {
					foreach ( $formatted_csv_data as $formatted_csv_data_key => $formatted_csv_data_value ) {
						if ( in_array( $formatted_csv_data_value['post_title'], (array) $all_plan_array ) ) {
							$formatted_csv_data_value['post_title'] = $formatted_csv_data_value['post_title'] . '-copied';
						}
						if ( ! empty( $formatted_csv_data_value['post_title'] ) ) {
							$plan_id = wp_insert_post(
								array(
									'post_type'    => 'wps_cpt_membership',
									'post_title'   => $formatted_csv_data_value['post_title'],
									'post_status'  => $formatted_csv_data_value['post_status'],
									'post_content' => $formatted_csv_data_value['post_content'],
								),
								true
							);

							wps_membership_update_meta_data( $plan_id, 'wps_membership_plan_price', $value['wps_membership_plan_price'] );
							wps_membership_update_meta_data( $plan_id, 'wps_membership_plan_name_access_type', $value['wps_membership_plan_name_access_type'] );
							wps_membership_update_meta_data( $plan_id, 'wps_membership_plan_duration', $value['wps_membership_plan_duration'] );
							wps_membership_update_meta_data( $plan_id, 'wps_membership_plan_duration_type', $value['wps_membership_plan_duration_type'] );
							wps_membership_update_meta_data( $plan_id, 'wps_membership_plan_recurring', $value['wps_membership_plan_recurring'] );
							wps_membership_update_meta_data( $plan_id, 'wps_membership_plan_access_type', $value['wps_membership_plan_access_type'] );
							wps_membership_update_meta_data( $plan_id, 'wps_membership_plan_time_duration', $value['wps_membership_plan_time_duration'] );
							wps_membership_update_meta_data( $plan_id, 'wps_membership_plan_time_duration_type', $value['wps_membership_plan_time_duration_type'] );
							wps_membership_update_meta_data( $plan_id, 'wps_membership_plan_offer_price_type', $value['wps_membership_plan_offer_price_type'] );
							wps_membership_update_meta_data( $plan_id, 'wps_memebership_plan_discount_price', $value['wps_memebership_plan_discount_price'] );
							wps_membership_update_meta_data( $plan_id, 'wps_memebership_plan_free_shipping', $value['wps_memebership_plan_free_shipping'] );
							wps_membership_update_meta_data( $plan_id, 'wps_membership_plan_target_ids', $value['wps_membership_plan_target_ids'] );
							wps_membership_update_meta_data( $plan_id, 'wps_membership_plan_target_categories', $value['wps_membership_plan_target_categories'] );
						}
					}

					echo wp_json_encode(
						array(
							'status'   => 'success',
							'message'  => 'File Imported Successfully',
							'redirect' => admin_url( 'edit.php?post_type=wps_cpt_membership' ),
						)
					);

				} else {

					echo wp_json_encode(
						array(
							'status'   => 'failed',
							'message'  => 'Something Went Wrong. Either Products or Categories are not available!',
							'redirect' => admin_url( 'edit.php?post_type=wps_cpt_membership' ),
						)
					);
				}
			} else {

				echo wp_json_encode(
					array(
						'status'   => 'failed',
						'message'  => 'Invalid File type',
						'redirect' => admin_url( 'edit.php?post_type=wps_cpt_membership' ),
					)
				);
			}

			wp_die();
		}

	}

	/**
	 * Function is used for the sending the track data
	 *
	 * @param bool $override is the bool value to override tracking value.
	 * @name wps_membership_mfw_wpswings_tracker_send_event
	 * @since 1.0.0
	 */
	public function wps_membership_mfw_wpswings_tracker_send_event( $override = false ) {
		require_once WC()->plugin_path() . '/includes/class-wc-tracker.php';

		$last_send = get_option( 'wpswings_tracker_last_send' );

		/**
		 * Filter send tracker.
		 *
		 * @since 1.0.0
		 */
		if ( ! apply_filters( 'wpswings_tracker_send_override', $override ) ) {
			// Send a maximum of once per week by default.
			$last_send = $this->wps_mfw_last_send_time();

			/**
			 * Filter to send last interval.
			 *
			 * @since 1.0.0
			 */
			if ( $last_send && $last_send > apply_filters( 'wpswings_tracker_last_send_interval', strtotime( '-1 week' ) ) ) {
				return;
			}
		} else {
			// Make sure there is at least a 1 hour delay between override sends, we don't want duplicate calls due to double clicking links.
			$last_send = $this->wps_mfw_last_send_time();
			if ( $last_send && $last_send > strtotime( '-1 hours' ) ) {
				return;
			}
		}
		// Update time first before sending to ensure it is set.
		update_option( 'wpswings_tracker_last_send', time() );
		$params = WC_Tracker::get_tracking_data();
		$params['extensions']['membership_for_woocommerce'] = array(
			'version' => MEMBERSHIP_FOR_WOOCOMMERCE_VERSION,
			'site_url' => home_url(),
			'membership_plans' => $this->wps_mfw_membership_plan_count(),
			'members_data' => $this->wps_mfw_membership_get_all_members(),
		);

		/**
		 * Filter tracker params.
		 *
		 * @since 1.0.0
		 */
		$params = apply_filters( 'wpswings_tracker_params', $params );

		$api_url = 'https://tracking.wpswings.com/wp-json/mps-route/v1/mps-testing-data/';

		$sucess = wp_safe_remote_post(
			$api_url,
			array(
				'method'      => 'POST',
				'body'        => wp_json_encode( $params ),
			)
		);
	}

	/**
	 * Get All number of Membership Plan
	 *
	 * @return int
	 * @since 2.0.1
	 */
	public function wps_mfw_membership_plan_count() {

		$args = array(
			'post_type'   => 'wps_cpt_membership',
			'post_status' => array( 'publish' ),
			'numberposts' => -1,
		);

		$tag_ids = array();

		$all_posts = get_posts( $args );

		$total_membership_plan = count( $all_posts );
		return $total_membership_plan;
	}

	 /**
	  * Function is used get all members details
	  *
	  * @name wps_mfw_membership_get_all_members
	  * @since 2.0.1
	  */
	public function wps_mfw_membership_get_all_members() {
		$membership_details = array();
		// Get all limited memberships.

		$args = array(
			'post_type'   => 'wps_cpt_members',
			'post_status' => array( 'private', 'draft', 'pending', 'publish', 'cancelled' ),
			'numberposts' => -1,
		);
		$delay_members = get_posts( $args );
		$all_posts = get_posts( $args );

		if ( ! empty( $delay_members ) && is_array( $delay_members ) && count( $delay_members ) ) {
			$user_id = '';
			$user_name = '';
			$active_count = 0;
			$pending_count = 0;
			$cancel_count = 0;
			$expired_count = 0;
			$paused_count = 0;
			foreach ( $delay_members as $member ) {

				$plan_obj = wps_membership_get_meta_data( $member->ID, 'plan_obj', true );
				$status = wps_membership_get_meta_data( $member->ID, 'member_status', true );

				if ( 'complete' == $status ) {
					$active_count++;
					$membership_details['complete_membership'] = $active_count;
				} elseif ( 'pending' == $status ) {
					$pending_count++;
					$membership_details['pending_membership'] = $pending_count;
				} elseif ( 'cancelled' == $status ) {
					$cancel_count++;
					$membership_details['cancelled_membership'] = $cancel_count;
				} elseif ( 'expired' == $status ) {
					$expired_count++;
					$membership_details['expired_membership'] = $expired_count;
				} elseif ( 'paused' == $status ) {
					$paused_count++;
					$membership_details['paused_membership'] = $paused_count;
				}
			}
		}

		return $membership_details;
	}


	/**
	 * Get the updated time.
	 *
	 * @name wps_mfw_last_send_time
	 *
	 * @since 1.0.0
	 */
	public function wps_mfw_last_send_time() {

		/**
		 * Filter to send tracker.
		 *
		 * @since 1.0.0
		 */
		return apply_filters( 'wpswings_tracker_last_send_time', get_option( 'wpswings_tracker_last_send', false ) );
	}

	/**
	 * Membership status update according to subscription renewal.
	 *
	 * @param mixed $wps_new_order new order id.
	 * @param mixed $subscription_id subscription id.
	 * @param mixed $payment_method is the method for payment of subscription.
	 * @return void
	 */
	public function wps_membership_subscription_renewal( $wps_new_order, $subscription_id, $payment_method ) {
		$expiry_date = '';
		$next_payment_date = wps_membership_get_meta_data( $subscription_id, 'wps_next_payment_date', true );
		$end_payment_date = wps_membership_get_meta_data( $subscription_id, 'wps_susbcription_end', true );
		if ( ! empty( $next_payment_date ) ) {
			$expiry_date = $next_payment_date;
		} elseif ( ! empty( $end_payment_date ) ) {
			$expiry_date = $end_payment_date;
		}
		$subscription = get_post( $subscription_id );
		$parent_order_id  = $subscription->wps_parent_order;

		$order_status  = $wps_new_order->get_status();
		$order = new WC_Order( $wps_new_order->get_id() );
		$order_status = $order->status;
		if ( 'processing' == $order_status || 'complete' == $order_status ) {

			$order = wc_get_order( $parent_order_id );
			$member_id = get_member_id_from_order( $order );

			if ( ! empty( $member_id ) ) {
				wps_membership_update_meta_data( $member_id, 'member_status', 'complete' );
				wps_membership_update_meta_data( $member_id, 'member_expiry', $expiry_date );
			}
		}
		if ( 'failed' == $order_status ) {
			$member_id = get_member_id_from_order( $order );

			if ( ! empty( $member_id ) ) {
				wps_membership_update_meta_data( $member_id, 'member_status', 'hold' );
				wps_membership_update_meta_data( $member_id, 'member_expiry', $expiry_date );
			}
		}
	}


	/**
	 * Remove add-on payment gateways from checkout page.
	 *
	 * @param mixed $order_id order id.
	 * @param mixed $old_status order old status.
	 * @param mixed $new_status order new status.
	 *
	 * @throws  Exception Error.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_woo_order_status_change_custom( $order_id, $old_status, $new_status ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$order = new WC_Order( $order_id );
		$order_data = $order->get_data(); // The Order data.
		$billing_email = $order_data['billing']['email'];
		$billing_first_name = $order_data['billing']['first_name'];
		$billing_last_name = $order_data['billing']['last_name'];

		$billing_phone = $order_data['billing']['phone'];

		$is_user_created = get_option( 'wps_membership_create_user_after_payment', true );

		$_user = get_user_by( 'email', $billing_email );
		if ( ( $_user ) && ( 'processing' === $new_status || 'completed' === $new_status ) ) {

			// assign one time discount coupon.
			$wps_wpr_one_time_coupon_assignment = get_post_meta( $order_id, 'wps_wpr_one_time_coupon_assignment', true );
			if ( empty( $wps_wpr_one_time_coupon_assignment ) ) {

				$this->global_class->wps_msfw_assign_one_time_discount_coupon( $_user );
				update_post_meta( $order_id, 'wps_wpr_one_time_coupon_assignment', 'done' );
			}
		}

		// send welcome mail.
		if ( $_user && 'completed' === $new_status ) {

			$wps_mfw_send_welcome_mail_once_check = get_post_meta( $order_id, 'wps_mfw_send_welcome_mail_once_check', true );
			if ( empty( $wps_mfw_send_welcome_mail_once_check ) ) {

				$this->global_class->wps_mfw_membership_welcome_mail( $_user->ID );
				update_post_meta( $order_id, 'wps_mfw_send_welcome_mail_once_check', 'done' );
			}
		}
		// If user exist, get the required details.

		$items = $order->get_items();

		$member_id = '';

		$items = $order->get_items();

		foreach ( $items as $item ) {
			$get_data = $item->get_formatted_meta_data();
			$item_meta_data = $item->get_formatted_meta_data( '', true );
			foreach ( $item_meta_data as $mfw_key => $mfw_value ) {
				if ( '_member_id' == $mfw_value->display_key ) {
					$member_id = $mfw_value->value;
				}
			}
		}

		$plan_obj = wps_membership_get_meta_data( $member_id, 'plan_obj', true );
		$today_date = gmdate( 'Y-m-d' );
		// Save expiry date in post.
		if ( ! empty( $plan_obj ) ) {

			$access_type = wps_membership_get_meta_data( $plan_obj['ID'], 'wps_membership_plan_access_type', true );
			$current_date = 0;
			if ( 'delay_type' == $access_type ) {
				$time_duration      = wps_membership_get_meta_data( $plan_obj['ID'], 'wps_membership_plan_time_duration', true );
				$time_duration_type = wps_membership_get_meta_data( $plan_obj['ID'], 'wps_membership_plan_time_duration_type', true );

				$current_date = gmdate( 'Y-m-d', strtotime( $today_date . ' + ' . $time_duration . ' ' . $time_duration_type ) );
				wps_membership_update_meta_data( $member_id, 'membership_delay_date', $current_date );
			}

			if ( 'lifetime' == $plan_obj['wps_membership_plan_name_access_type'] ) {

				wps_membership_update_meta_data( $member_id, 'member_expiry', 'Lifetime' );

			} elseif ( 'limited' == $plan_obj['wps_membership_plan_name_access_type'] ) {

				$duration = $plan_obj['wps_membership_plan_duration'] . ' ' . $plan_obj['wps_membership_plan_duration_type'];

				$expiry_date = gmdate( strtotime( $current_date . $duration ) );

				if ( 'delay_type' == $access_type ) {
					$delay_duration = $time_duration . ' ' . $time_duration_type;

					$expiry_date = gmdate( strtotime( $today_date . $duration ) );
					$date_exipary = gmdate( 'Y-m-d', $expiry_date );
					$expiry_date = strtotime( $date_exipary . $delay_duration );
				}
				wps_membership_update_meta_data( $member_id, 'member_expiry', $expiry_date );
			}
		}

		$is_processing = get_option( 'wps_membership_create_member_on_processing' );
		if ( 'on' === $is_processing ) {
			$tmp_order_st = 'processing';
			if ( 'processing' == $order->get_status() || 'completed' == $order->get_status() ) {

				$order_st = 'complete';
			} elseif ( 'on-hold' == $order->get_status() || 'refunded' == $order->get_status() || 'failed' == $order->get_status() ) {
				$order_st = 'hold';
			} elseif ( 'pending' == $order->get_status() || 'completed' == $order->get_status() ) {
				$order_st = 'pending';
			} elseif ( 'cancelled' == $order->get_status() ) {
				$order_st = 'cancelled';
			}
		} else {
			$tmp_order_st = 'completed';
			if ( 'completed' == $order->get_status() ) {

				$order_st = 'complete';
			} elseif ( 'on-hold' == $order->get_status() || 'refunded' == $order->get_status() || 'failed' == $order->get_status() ) {
				$order_st = 'hold';
			} elseif ( 'pending' == $order->get_status() || 'processing' == $order->get_status() ) {
				$order_st = 'pending';
			} elseif ( 'cancelled' == $order->get_status() ) {
				$order_st = 'cancelled';
			}
		}

		if ( 'delay_type' == $access_type ) {
			if ( $current_date >= $today_date && $tmp_order_st == $order->get_status() ) {

				wps_membership_update_meta_data( $member_id, 'member_status', 'pending' );

				if ( 'yes' == $plan_obj['wps_membership_subscription'] ) {
					$subscription_id = wps_membership_get_meta_data( $order_id, 'wps_subscription_id', true );
					if ( ! empty( $subscription_id ) ) {
						wps_membership_update_meta_data( $subscription_id, 'wps_subscription_status', 'pending' );
					}
				}
			}
		} else {

			wps_membership_update_meta_data( $member_id, 'member_status', $order_st );
			$subscription_id = wps_membership_get_meta_data( $order_id, 'wps_subscription_id', true );
			if ( 'complete' == $order_st ) {

				if ( ! $_user ) {

					if ( 'on' == $is_user_created ) {

						$website = get_site_url();

						$user_name = $billing_first_name . '-' . rand();
						$password = $billing_first_name . substr( $billing_phone, -4, 4 );
						update_option( 'user_password', $password );
						$userdata = array(
							'user_login' => $user_name,
							'user_url'   => $website,
							'user_pass'  => $password, // When creating an user, `user_pass` is expected.
							'user_email' => $billing_email,
							'first_name' => $billing_first_name,
							'last_name' => $billing_last_name,
							'display_name' => $billing_first_name,
							'nickname' => $billing_first_name,
						);

						$_user = wp_insert_user( $userdata );
						update_user_meta( $_user, 'user_created_by_membership', 'yes' );

						update_option( 'user_name', $user_name );

						if ( $_user ) {

							$user_id   = $_user;
							$user_ob   = get_user_by( 'id', $user_id );
							$user_name = $user_ob->display_name;

							// assign one time discount coupon.
							$wps_wpr_one_time_coupon_assignment = get_post_meta( $order_id, 'wps_wpr_one_time_coupon_assignment', true );
							if ( empty( $wps_wpr_one_time_coupon_assignment ) ) {

								$this->global_class->wps_msfw_assign_one_time_discount_coupon( $_user );
								update_post_meta( $order_id, 'wps_wpr_one_time_coupon_assignment', 'done' );
							}

							// send welcome mail.
							$wps_mfw_send_welcome_mail_once_check = get_post_meta( $order_id, 'wps_mfw_send_welcome_mail_once_check', true );
							if ( empty( $wps_mfw_send_welcome_mail_once_check ) ) {

								$this->global_class->wps_mfw_membership_welcome_mail( $_user );
								update_post_meta( $order_id, 'wps_mfw_send_welcome_mail_once_check', 'done' );
							}
						}
					}
					if ( ! empty( $_user ) ) {
						$wps_membership_posts = $_user;
					} else {
						$wps_membership_posts = get_post_field( 'post_author', $member_id );
					}
				} else {
					if ( ! empty( $_user ) ) {
						$wps_membership_posts = $_user->ID;
					} else {
						$wps_membership_posts = get_post_field( 'post_author', $member_id );
					}
				}

				update_user_meta( $wps_membership_posts, 'is_member', 'member' );
				wps_membership_update_meta_data( $member_id, 'wps_member_user', $wps_membership_posts );
				if ( 'yes' == $plan_obj['wps_membership_subscription'] ) {
					if ( ! empty( $subscription_id ) ) {
						wps_membership_update_meta_data( $subscription_id, 'wps_subscription_status', 'active' );
						wps_membership_update_meta_data( $subscription_id, 'wps_next_payment_date', $expiry_date );

						if ( ! empty( $plan_obj['wps_membership_subscription_expiry'] ) ) {
							if ( function_exists( 'wps_sfw_susbcription_expiry_date' ) ) {
								$access_type = wps_membership_get_meta_data( $plan_obj['plan_id'], 'wps_membership_plan_access_type', true );
								$current_date = gmdate( 'Y-m-d' );
								if ( 'delay_type' == $access_type ) {
									$time_duration      = wps_membership_get_meta_data( $plan_obj['plan_id'], 'wps_membership_plan_time_duration', true );
									$time_duration_type = wps_membership_get_meta_data( $plan_obj['plan_id'], 'wps_membership_plan_time_duration_type', true );

									$current_date = gmdate( 'Y-m-d', strtotime( $current_date . ' + ' . $time_duration . ' ' . $time_duration_type ) );

								}
								$current_time = current_time( 'timestamp' );
								$wps_susbcription_end = wps_sfw_susbcription_expiry_date( $subscription_id, $current_time );
								wps_membership_update_meta_data( $subscription_id, 'wps_susbcription_end', $wps_susbcription_end );

							}
						} else {
							wps_membership_update_meta_data( $subscription_id, 'wps_susbcription_end', '' );
						}
					}
				}
			} else {
				if ( ! empty( $subscription_id ) ) {
					wps_membership_update_meta_data( $subscription_id, 'wps_subscription_status', $order_st );
				}
			}
		}
		wps_membership_update_meta_data( $member_id, 'billing_details_payment', wps_membership_get_meta_data( $order_id, '_payment_method', true ) );
	}

	/**
	 * Membership status update according to subscription renewal.
	 *
	 * @param mixed $subscription_id subscription id.
	 * @return void
	 */
	public function wps_membership_subscription_active_renewal( $subscription_id ) {
		$subscription = get_post( $subscription_id );
		$parent_order_id  = $subscription->wps_parent_order;
		$order = wc_get_order( $parent_order_id );
		$member_id = get_member_id_from_order( $order );
		if ( ! empty( $member_id ) ) {
			wps_membership_update_meta_data( $member_id, 'member_status', 'complete' );
		}
	}

	/**
	 * Membership status update according to subscription renewal.
	 *
	 * @param mixed $subscription_id subscription id.
	 * @return void
	 */
	public function wps_membership_subscription_on_hold_renewal( $subscription_id ) {
		$subscription = get_post( $subscription_id );
		$parent_order_id  = $subscription->wps_parent_order;
		$order = wc_get_order( $parent_order_id );
		$member_id = get_member_id_from_order( $order );
		if ( ! empty( $member_id ) ) {
			wps_membership_update_meta_data( $member_id, 'member_status', 'hold' );
		}
	}

	/**
	 * Membership status update according to subscription renewal.
	 *
	 * @param mixed $subscription_id subscription id.
	 * @return void
	 */
	public function wps_membership_subscription_expire( $subscription_id ) {
		$subscription = get_post( $subscription_id );
		$parent_order_id  = $subscription->wps_parent_order;
		$order = wc_get_order( $parent_order_id );
		$member_id = get_member_id_from_order( $order );
		if ( ! empty( $member_id ) ) {
			wps_membership_update_meta_data( $member_id, 'member_status', 'expired' );
		}
	}

	/**
	 * Update the option for settings from the multistep form.
	 *
	 * @name wps_membership_save_settings_filter
	 * @since 1.0.0
	 */
	public function wps_membership_save_settings_filter() {
		check_ajax_referer( 'ajax-nonce', 'nonce' );

		$term_accpted = ! empty( $_POST['consetCheck'] ) ? sanitize_text_field( wp_unslash( $_POST['consetCheck'] ) ) : ' ';
		if ( ! empty( $term_accpted ) && 'yes' == $term_accpted ) {
			update_option( 'mfw_enable_tracking', 'on' );
		}
		// settings fields.
		$first_name = ! empty( $_POST['firstName'] ) ? sanitize_text_field( wp_unslash( $_POST['firstName'] ) ) : '';
		update_option( 'firstname', $first_name );

		$email = ! empty( $_POST['email'] ) ? sanitize_text_field( wp_unslash( $_POST['email'] ) ) : '';
		update_option( 'email', $email );

		$desc = ! empty( $_POST['desc'] ) ? sanitize_text_field( wp_unslash( $_POST['desc'] ) ) : '';
		update_option( 'desc', $desc );

		$age = ! empty( $_POST['age'] ) ? sanitize_text_field( wp_unslash( $_POST['age'] ) ) : '';
		update_option( 'age', $age );

		$first_checkbox = ! empty( $_POST['FirstCheckbox'] ) ? sanitize_text_field( wp_unslash( $_POST['FirstCheckbox'] ) ) : '';
		update_option( 'first_checkbox', $first_checkbox );

		$checked_first_switch = ! empty( $_POST['checkedA'] ) ? sanitize_text_field( wp_unslash( $_POST['checkedA'] ) ) : '';
		if ( ! empty( $checked_first_switch ) && 'true' == $checked_first_switch ) {
			update_option( 'wps_membership_enable_plugin', 'on' );
		}

		$mem_plan_amount = ! empty( $_POST['memPlanAmount'] ) ? sanitize_text_field( wp_unslash( $_POST['memPlanAmount'] ) ) : '';
		if ( 0 > $mem_plan_amount ) {
			$mem_plan_amount = 0;
		}
		update_option( 'Mem_Plan_Amount', $mem_plan_amount );

		$mem_plan_title = ! empty( $_POST['memPlanTitle'] ) ? sanitize_text_field( wp_unslash( $_POST['memPlanTitle'] ) ) : '';
		update_option( 'Mem_Plan_Title', $mem_plan_title );

		$mem_plan_product = ! empty( $_POST['memPlanProduct'] ) ? sanitize_text_field( wp_unslash( $_POST['memPlanProduct'] ) ) : '';
		update_option( 'Mem_Plan_Product', $mem_plan_product );

		if ( ! empty( $mem_plan_title ) || ! empty( $mem_plan_amount ) ) {

			$post_id = wp_insert_post(
				array(
					'post_type' => 'wps_cpt_membership',
					'post_title' => $mem_plan_title,
					'post_content' => '',
					'post_status' => 'publish',
					'meta_input' => array(
						'wps_membership_plan_price' => $mem_plan_amount,
					),
				)
			);
			$product_array = array();
			array_push( $product_array, $mem_plan_product );
			if ( is_array( $product_array ) ) {
				$post_data = ! empty( $product_array ) ? array_map( 'sanitize_text_field', wp_unslash( $product_array ) ) : '';
			}
			wps_membership_update_meta_data( $post_id, 'wps_membership_plan_target_ids', $post_data );

			wps_membership_update_meta_data( $post_id, 'wps_membership_plan_name_access_type', 'lifetime' );
		}

		update_option( 'mfw_mfw_plugin_standard_multistep_done', 'yes' );

		$license_code = ! empty( $_POST['licenseCode'] ) ? sanitize_text_field( wp_unslash( $_POST['licenseCode'] ) ) : '';

		if ( class_exists( 'Membership_For_Woocommerce_Pro_Common' ) ) {

			$mfwp_plugin_common = new Membership_For_Woocommerce_Pro_Common( '', '' );

			$wps_mfw_response = $mfwp_plugin_common->mfwp_membership_validate_license_key( $license_code );

			if ( is_wp_error( $wps_mfw_response ) ) {
				wp_send_json( 'license_could_not_be_verified' );
			} else {
				$wps_mfw_license_data = json_decode( wp_remote_retrieve_body( $wps_mfw_response ) );
				if ( isset( $wps_mfw_license_data->result ) && 'success' === $wps_mfw_license_data->result ) {

					global $wpdb;
					if ( is_multisite() ) {
						$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

						foreach ( $blogids as $blog_id ) {

							switch_to_blog( $blog_id );
							update_option( 'wps_mfwp_license_key', $license_code );
							update_option( 'wps_mfwp_license_check', true );
							restore_current_blog();
						}
					} else {
						update_option( 'wps_mfwp_license_key', $license_code );
						update_option( 'wps_mfwp_license_check', true );
					}
				}
			}
		}
		wp_send_json( 'yes' );
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $user_id is the id of new registered user.
	 * @return void
	 */
	public function wps_membership_sen_email_to_new_registered_user( $user_id ) {
		$check = get_user_meta( $user_id, 'user_created_by_membership', true );
		if ( 'yes' == $check ) {

			$user = get_userdata( $user_id );
			$user_email = $user->user_email;
			$user_login = $user->user_login;

			// for simplicity, lets assume that user has typed their first and last name when they sign up.
			$user_full_name = $user->user_firstname . ' ' . $user->user_lastname;
			$user_password = get_option( 'user_password', true );
			// Now we are ready to build our welcome email.
			$to = $user_email;
			$subject = 'Hi ' . $user_full_name . ', welcome to our site!';
			$body = '
					  <h1>Dear ' . $user_full_name . ',</h1></br>
					  <p>Thank you for joining our site. Your account is now active.</p>
					  <p>Please go ahead and navigate around your account.</p>
					  <p>Here is your Credentials </p>
					  <p> User ID - ' . $user_login . ' </p>
					  <p> Password - ' . $user_password . ' </p>
			';
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			if ( wp_mail( $to, $subject, $body, $headers ) ) {
				error_log( 'email has been successfully sent to user whose email is ' . $user_email );
				$user_password = get_option( 'user_password', '' );
			} else {
				error_log( 'email failed to sent to user whose email is ' . $user_email );
			}
		}

	}

	/**
	 * Function to update status to cancel.
	 *
	 * @return void
	 */
	public function wps_membership_cancel_membership_count() {
		check_ajax_referer( 'wps_common_ajax_nonce', 'security' );
		$membership_id = isset( $_POST['membership_id'] ) ? sanitize_text_field( wp_unslash( $_POST['membership_id'] ) ) : '';
		if ( ! empty( $membership_id ) ) {
			wps_membership_update_meta_data( $membership_id, 'member_status', 'cancelled' );
			$user_id = get_current_user_id();
			update_user_meta( $user_id, 'is_member', '' );
			if ( ! empty( wps_membership_get_meta_data( $membership_id - 1, 'wps_subscription_status', true ) ) ) {

				wps_membership_update_meta_data( $membership_id - 1, 'wps_subscription_status', 'cancelled' );
			}
		}
	}

}
