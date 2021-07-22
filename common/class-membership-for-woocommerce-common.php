<?php
/**
 * The common functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
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
		wp_enqueue_style( $this->plugin_name . 'common', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'common/css/membership-for-woocommerce-common.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the common side of the site.
	 *
	 * @since    1.0.0
	 */
	public function mfw_common_enqueue_scripts() {
		wp_register_script( $this->plugin_name . 'common', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'common/js/membership-for-woocommerce-common.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name . 'common', 'mfw_common_param', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( $this->plugin_name . 'common' );
	}

	/**
	 * validating makewebbetter license
	 *
	 * @since    1.0.0
	 */
	public function mwb_membership_mfw_validate_license_key() {
	
		$mwb_mfw_purchase_code = sanitize_text_field( $_POST['purchase_code'] );
		$api_params = array(
			'slm_action'        => 'slm_activate',
			'secret_key'        => MEMBERSHIP_FOR_WOOCOMMERCE_SPECIAL_SECRET_KEY,
			'license_key'       => $mwb_mfw_purchase_code,
			'_registered_domain' => $_SERVER['SERVER_NAME'],
			'item_reference'    => urlencode( MEMBERSHIP_FOR_WOOCOMMERCE_ITEM_REFERENCE ),
			'product_reference' => 'MWBPK-2965',

		);

		$query = esc_url_raw( add_query_arg( $api_params, MEMBERSHIP_FOR_WOOCOMMERCE_LICENSE_SERVER_URL ) );
		// $ch = curl_init();
		// curl_setopt( $ch, CURLOPT_URL, $query );
		// curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		// curl_setopt( $ch, CURLOPT_TIMEOUT, 20 );
		// curl_setopt( $ch, CURLOPT_SSL_VERIFYSTATUS, false );
		// $mwb_mfw_response = curl_exec( $ch );
		// curl_close($ch);

		$mwb_mfw_response = wp_remote_get(
			$query,
			array(
				'timeout' => 20,
				'sslverify' => false,
			)
		);

		if ( is_wp_error( $mwb_mfw_response ) ) {
			echo json_encode(
				array(
					'status' => false,
					'msg' => __(
						'An unexpected error occurred. Please try again.',
						'membership-for-woocommerce'
					),
				)
			);
		} else {
			$mwb_mfw_license_data = json_decode( wp_remote_retrieve_body( $mwb_mfw_response ) );

			if ( isset( $mwb_mfw_license_data->result ) && $mwb_mfw_license_data->result == 'success' ) {
				update_option( 'mwb_mfw_license_key', $mwb_mfw_purchase_code );
				update_option( 'mwb_mfw_license_check', true );

				echo json_encode(
					array(
						'status' => true,
						'msg' => __(
							'Successfully Verified. Please Wait.',
							'membership-for-woocommerce'
						),
					)
				);
			} else {
				echo json_encode(
					array(
						'status' => false,
						'msg' => $mwb_mfw_license_data->message,
					)
				);
			}
		}
		wp_die();
	}


	/**
	 * Ajax function for membership checkout.
	 *
	 * @return void
	 */
	public function mwb_membership_checkout() {

		ini_set('display_errors',1);
		error_reporting(E_ALL);
		check_ajax_referer( 'auth_adv_nonce', 'nonce' );
		$plan_id    = isset( $_POST['plan_id'] ) ? sanitize_text_field( wp_unslash( $_POST['plan_id'] ) ) : '';
		$plan_price = isset( $_POST['plan_price'] ) ? sanitize_text_field( wp_unslash( $_POST['plan_price'] ) ) : '';
		$plan_title = isset( $_POST['plan_title'] ) ? sanitize_text_field( wp_unslash( $_POST['plan_title'] ) ) : '';

		$mwb_membership_default_product = get_option( 'mwb_membership_default_product', '' );

		global $wp_session;

		$wp_session['plan_price'] = $plan_price;
		$wp_session['plan_title'] = $plan_title;
		$wp_session['plan_id']    = $plan_id;
		WC()->session->set( 'plan_id', $plan_id );
		WC()->session->set( 'plan_title', $plan_title );
		WC()->session->set( 'plan_price', $plan_price );
		WC()->session->set( 'product_id', $mwb_membership_default_product );

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
		$product = wc_get_product( $product_id );

		global $wp_session;

		if ( $product ) {
			$cart_item_data['plan_price'] = $wp_session['plan_price'];
			$cart_item_data['plan_title'] = $wp_session['plan_title'];
		}
		$cart_item_data = apply_filters( 'add_membership_product_price_to_cart_item_data', $cart_item_data );

		return $cart_item_data;
	}



	/**
	 * Callback function for file Upload and import.
	 */
	public function mwb_membership_csv_file_upload() {

		// Nonce Verification.
		//check_ajax_referer( 'plan-import-nonce', 'nonce' );
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-membership-activity-helper.php';

		// Handling file upload using activity helper class..
		$activity_class = new Membership_Activity_Helper( 'csv-uploads', 'uploads' );
		// phpcs:disable
		// $csv_file    = ! empty( $_FILES['file'] ) ? $_FILES['file'] : ''; // phpcs:ignore

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

				foreach ( $csv_prod_title as $key => $value ) {

					if ( in_array( $value, $all_prod_title, true ) ) {

						$prd_check = true;
					}
				}

				foreach ( $csv_cate_title as $key => $value ) {

					if ( in_array( $value, $all_cat_title, true ) ) {

						$cat_check = true;
					}
				}
			}

			$args = array(
				'post_type'   => 'mwb_cpt_membership',
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
				foreach ( $formatted_csv_data as $key => $value ) {
					if ( in_array( $value['post_title'], (array) $all_plan_array ) ) {
						$value['post_title'] = $value['post_title'] . '-copied';
					}
					if ( ! empty( $value['post_title'] ) ) {
						$plan_id = wp_insert_post(
							array(
								'post_type'    => 'mwb_cpt_membership',
								'post_title'   => $value['post_title'],
								'post_status'  => $value['post_status'],
								'post_content' => $value['post_content'],
							),
							true
						);

						update_post_meta( $plan_id, 'mwb_membership_plan_price', $value['mwb_membership_plan_price'] );
						update_post_meta( $plan_id, 'mwb_membership_plan_name_access_type', $value['mwb_membership_plan_name_access_type'] );
						update_post_meta( $plan_id, 'mwb_membership_plan_duration', $value['mwb_membership_plan_duration'] );
						update_post_meta( $plan_id, 'mwb_membership_plan_duration_type', $value['mwb_membership_plan_duration_type'] );
						update_post_meta( $plan_id, 'mwb_membership_plan_recurring', $value['mwb_membership_plan_recurring'] );
						update_post_meta( $plan_id, 'mwb_membership_plan_access_type', $value['mwb_membership_plan_access_type'] );
						update_post_meta( $plan_id, 'mwb_membership_plan_time_duration', $value['mwb_membership_plan_time_duration'] );
						update_post_meta( $plan_id, 'mwb_membership_plan_time_duration_type', $value['mwb_membership_plan_time_duration_type'] );
						update_post_meta( $plan_id, 'mwb_membership_plan_offer_price_type', $value['mwb_membership_plan_offer_price_type'] );
						update_post_meta( $plan_id, 'mwb_memebership_plan_discount_price', $value['mwb_memebership_plan_discount_price'] );
						update_post_meta( $plan_id, 'mwb_memebership_plan_free_shipping', $value['mwb_memebership_plan_free_shipping'] );
						update_post_meta( $plan_id, 'mwb_membership_plan_target_ids', $value['mwb_membership_plan_target_ids'] );
						update_post_meta( $plan_id, 'mwb_membership_plan_target_categories', $value['mwb_membership_plan_target_categories'] );
					}
				}

				echo wp_json_encode(
					array(
						'status'   => 'success',
						'message'  => 'File Imported Successfully',
						'redirect' => admin_url( 'edit.php?post_type=mwb_cpt_membership' ),
					)
				);

			} else {

				echo wp_json_encode(
					array(
						'status'   => 'failed',
						'message'  => 'Something Went Wrong. Either Products or Categories are not available!',
						'redirect' => admin_url( 'edit.php?post_type=mwb_cpt_membership' ),
					)
				);
			}
		}

		wp_die();
	}



}
