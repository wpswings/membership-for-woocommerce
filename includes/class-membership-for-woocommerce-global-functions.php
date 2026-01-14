<?php
/**
 * The global helper class of the plugin.
 *
 * @link       https://wpswings.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin
 */

/**
 * The global helper class of the plugin.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin
 */
class Membership_For_Woocommerce_Global_Functions {

	/**
	 * Instance of the class
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		self::$instance = $this;
	}

	/**
	 * Returns Instcance of the class
	 */
	public static function get() {

		if ( null == self::$instance ) {

			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Allowed html for description on admin side
	 *
	 * @param string $description tooltip message.
	 *
	 * @since 1.0.0
	 */
	public function tool_tip( $description = '' ) {

		// Run only if description message is present.
		if ( ! empty( $description ) ) {

			$allowed_html  = '<div class="wps-tool-tip">';
			$allowed_html .= '	<span class="icon">?</span>';
			$allowed_html .= '<span class="description_tool_tip">' . esc_attr( $description ) . '</span>';
			$allowed_html .= '	</div>';
			echo wp_kses_post( $allowed_html );
		}
	}

	/**
	 * Returns product name and status.
	 *
	 * @param string $product_id Product id of a particular product.
	 *
	 * @since 1.0.0
	 */
	public function get_product_title( $product_id = '' ) {

		$result = esc_html__( 'Product not found', 'membership-for-woocommerce' );
		if ( ! empty( $product_id ) ) {

			$product = wc_get_product( $product_id );
			if ( ! empty( $product ) ) {
				if ( 'publish' !== $product->get_status() ) {

					$result = esc_html__( 'Product unavailable', 'membership-for-woocommerce' );
				} else {

					$result = $product->get_name();
				}
			}
		}
		return $result;
	}

	/**
	 * Return category name and its existance
	 *
	 * @param string $cat_id Category ID of a particular category.
	 *
	 * @since 1.0.0
	 */
	public function get_category_title( $cat_id = '' ) {

		$result = esc_html__( 'Category not found', 'membership-for-woocommerce' );
		if ( ! empty( $cat_id ) ) {

			$cat_name = get_the_category_by_ID( $cat_id );
			if ( ! empty( $cat_name ) ) {

				$result = $cat_name;
			}
		}
		return $result;
	}

	/**
	 * Membership default global options.
	 *
	 * @since 1.0.0
	 */
	public function default_global_options() {

		$default_global_settings = array(

			'wps_membership_enable_plugin'     => 'on',
			'wps_membership_delete_data'       => 'off',
			'wps_membership_email_subject'     => 'Thank you for Shopping, Do not reply.',
			'wps_membership_email_content'     => '',
			'wps_membership_attach_invoice'    => 'off',
			'wps_membership_invoice_address'   => '',
			'wps_membership_invoice_phone'     => '',
			'wps_membership_invoice_email'     => '',
			'wps_membership_invoice_logo'      => '',
			'wps_membership_for_woo_delete_data' => '',
		);
		return $default_global_settings;
	}

	/**
	 * Membership product title for CSV.
	 *
	 * @param array $products An array of product ids.
	 *
	 * @since 1.0.0
	 */
	public function csv_get_prod_title( $products ) {

		$output = '';
		if ( ! empty( $products ) && is_array( $products ) ) {

			$product_ids = ! empty( $products ) ? array_map( 'absint', $products ) : null;
			if ( ! empty( $product_ids ) && is_array( $product_ids ) ) {
				foreach ( $product_ids as $single_id ) {

					$single_name = $this->get_product_title( $single_id );
					$output     .= esc_html( $single_name ) . '(#' . esc_html( $single_id ) . '),';
				}
			}
			$output = preg_replace( '/,[^,]*$/', '', $output );
		}
		return $output;
	}

	/**
	 * Membership category title for CSV.
	 *
	 * @param array $categories An array of cataegory ids.
	 *
	 * @since 1.0.0
	 */
	public function csv_get_cat_title( $categories ) {

		$output = '';
		if ( ! empty( $categories ) && is_array( $categories ) ) {

			$category_ids = ! empty( $categories ) ? array_map( 'absint', $categories ) : null;
			if ( ! empty( $category_ids ) && is_array( $category_ids ) ) {
				foreach ( $category_ids as $cat_id ) {

					$single_cat = $this->get_category_title( $cat_id );
					$output    .= esc_html( $single_cat ) . '(#' . esc_html( $cat_id ) . '),';
				}
			}
			$output = preg_replace( '/,[^,]*$/', '', $output );
		}
		return $output;
	}


	/**
	 * Available payment gateways.
	 *
	 * @since 1.0.0
	 */
	public function available_gateways() {

		$woo_gateways     = array();
		$wc_gateways      = new WC_Payment_Gateways();
		$payment_gateways = $wc_gateways->get_available_payment_gateways();
		if ( ! empty( $payment_gateways ) && is_array( $payment_gateways ) ) {
			// Loop through Woocommerce available payment gateways.
			foreach ( $payment_gateways as $gateway_id ) {

				$woo_gateways[] = $gateway_id->id;
			}
		}
		return $woo_gateways;
	}

	/**
	 * Returns the method title.
	 *
	 * @param string $method_id Id of the payment method.
	 *
	 * @since 1.0.0
	 */
	public function get_payment_method_title( $method_id ) {

		$title            = '';
		$wc_gateways      = new WC_Payment_Gateways();
		$payment_gateways = $wc_gateways->get_available_payment_gateways();
		if ( ! empty( $method_id ) ) {
			if ( ! empty( $payment_gateways ) && is_array( $payment_gateways ) ) {

				// Loop through Woocommerce available payment gateways.
				foreach ( $payment_gateways as $gateway ) {
					if ( $method_id === $gateway->id ) {

						$title = $gateway->method_title;
					}
				}
			}
		}
		return $title;
	}

	/**
	 * Cart item Ids.
	 *
	 * @since 1.0.0
	 */
	public function cart_item_ids() {

		$prod_ids   = array();
		$cart_items = WC()->cart->get_cart();
		if ( ! empty( $cart_items ) && is_array( $cart_items ) ) {
			foreach ( $cart_items as $item ) {

				$prod_ids[] = $item['product_id'];
			}
		}
		return $prod_ids;
	}

	/**
	 * Cart item category Ids.
	 *
	 * @since 1.0.0
	 */
	public function cart_item_cat_ids() {

		$cart_items = WC()->cart->get_cart();
		$cat_ids    = array();
		if ( ! empty( $cart_items ) && is_array( $cart_items ) ) {
			foreach ( $cart_items as $cart_item_key => $cart_item ) {

				$cat_ids = array_merge( $cat_ids, $cart_item['data']->get_category_ids() );
			}
		}
		return $cat_ids;
	}

	/**
	 * Cart item category Ids.
	 *
	 * @since 1.0.0
	 */
	public function cart_item_tag_ids() {

		$cart_items = WC()->cart->get_cart();
		$tag_ids    = array();
		if ( ! empty( $cart_items ) && is_array( $cart_items ) ) {
			foreach ( $cart_items as $cart_item_key => $cart_item ) {

				$tag_ids = array_merge( $tag_ids, $cart_item['data']->get_tag_ids() );
			}
		}
		return $tag_ids;
	}

	/**
	 * Get all plans offered products ids.
	 *
	 * @since 1.0.0
	 */
	public function plans_products_ids() {

		$args = array(
			'post_type'   => 'wps_cpt_membership',
			'post_status' => array( 'publish' ),
			'numberposts' => -1,
		);

		$ids       = array();
		$products  = array();
		$all_posts = get_posts( $args );
		if ( ! empty( $all_posts ) && is_array( $all_posts ) ) {
			foreach ( $all_posts as $post ) {

				$products = wps_membership_get_meta_data( $post->ID, 'wps_membership_plan_target_ids', true );
				if ( ! empty( $products ) && is_array( $products ) ) {
					foreach ( $products as $id ) {

						$ids[] = $id;
					}
				}
			}
		}
		return $ids;
	}

	/**
	 * Get all plans offered categories ids.
	 *
	 * @since 1.0.0
	 */
	public function plans_cat_ids() {

		$args = array(
			'post_type'   => 'wps_cpt_membership',
			'post_status' => array( 'publish' ),
			'numberposts' => -1,
		);

		$categories = array();
		$cat_ids    = array();
		$all_posts  = get_posts( $args );
		if ( ! empty( $all_posts ) && is_array( $all_posts ) ) {
			foreach ( $all_posts as $post ) {

				$categories = wps_membership_get_meta_data( $post->ID, 'wps_membership_plan_target_categories', true );
				if ( ! empty( $categories ) && is_array( $categories ) ) {
					foreach ( $categories as $id ) {

						$cat_ids[] = $id;
					}
				}
			}
		}
		return $cat_ids;
	}


	/**
	 * Get all plans offered tag ids.
	 *
	 * @since 1.0.0
	 */
	public function plans_tag_ids() {

		$args = array(
			'post_type'   => 'wps_cpt_membership',
			'post_status' => array( 'publish' ),
			'numberposts' => -1,
		);

		$tag       = array();
		$tag_ids   = array();
		$all_posts = get_posts( $args );
		if ( ! empty( $all_posts ) && is_array( $all_posts ) ) {
			foreach ( $all_posts as $post ) {

				$tag = wps_membership_get_meta_data( $post->ID, 'wps_membership_plan_target_tags', true );
				if ( ! empty( $tag ) && is_array( $tag ) ) {
					foreach ( $tag as $id ) {

						$tag_ids[] = $id;
					}
				}
			}
		}
		return $tag_ids;
	}

	/**
	 * Gutenberg offer plan content.
	 *
	 * @since 1.0.0
	 */
	public function gutenberg_content() {

		$page_content = '<!-- wp:cover {"minHeight":722,"minHeightUnit":"px","customGradient":"linear-gradient(153deg,rgb(6,89,229) 35%,rgb(155,81,224) 80%)","align":"wide"} -->
						<div class="wp-block-cover alignwide has-background-dim has-background-gradient" style="background:linear-gradient(153deg,rgb(6,89,229) 35%,rgb(155,81,224) 80%);min-height:722px"><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","textColor":"white"} -->
						<h2 class="has-text-align-center has-white-color has-text-color"><strong><em>One Membership, Many Benefits</em></strong></h2>
						<!-- /wp:heading -->
						
						<!-- wp:group -->
						<div class="wp-block-group"><div class="wp-block-group__inner-container"><!-- wp:html -->
						<div class="wps_mfw_membership_front_page">
						<span class="dashicons dashicons-awards wps_mfw_membership_icon"></span>
						<div class="wps_membership_plan_content_title">[wps_membership_title]</div>
						<div class="wps_membership_plan_content_price">[wps_membership_price]</div>
						<div class="wps_membership_plan_content_desc">[wps_membership_desc]</div>
						<div class="wps_mfw_buy_button">[wps_membership_buy_now] [wps_membership_no]</div>
						</div>
						<!-- /wp:html --></div></div>
						<!-- /wp:group --></div></div>
						<!-- /wp:cover -->';

		return $page_content;
	}

	/**
	 * Returns Import CSV modal.
	 *
	 * @since 1.0.0
	 */
	public function import_csv_modal_content() {
		?>
		<div class="import_csv_field_wrapper" >
			<input type="file" name="csv_to_import" id="wps_membership_csv_file_upload">
			<input type="submit" value="Upload File" name="upload_csv_file" id="upload_csv_file" >
		</div>
		<?php
	}

	/**
	 * Check if any plan exist or not.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function plans_exist_check() {

		$args = array(
			'post_type'   => 'wps_cpt_membership',
			'post_status' => array( 'publish' ),
			'numberposts' => -1,
		);

		$check     = '';
		$all_plans = get_posts( $args );
		if ( ! empty( $all_plans ) && is_array( $all_plans ) ) {

			$check = true;
		} else {

			$check = false;
		}
		return $check;
	}

	/**
	 * Return all memberships in membership free shipping.
	 *
	 * @since 1.0.0
	 */
	public function format_all_membership() {

		$formatted_all_membership = array();

		// Query run for all memberships for free shipping.
		$args = array(
			'post_type'   => 'wps_cpt_membership',
			'post_status' => array( 'publish' ),
			'numberposts' => -1,
			'fields'      => 'ids',
		);

		$all_membership = get_posts( $args );
		if ( ! empty( $all_membership ) && is_array( $all_membership ) ) {
			foreach ( $all_membership as $key => $id ) {

				$formatted_all_membership[ $id ] = get_the_title( $id );
			}
		}
		return $formatted_all_membership;
	}

	/**
	 * Maps the CSV data.
	 *
	 * @param array $csv_data An array of CSV data.
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function csv_data_map( $csv_data ) {

		$formatted_data = array();
		if ( ! empty( $csv_data ) && is_array( $csv_data ) ) {
			foreach ( $csv_data as $key => $value ) {

				$formatted_data[] = array(
					'post_id'                              => ! empty( $value[0] ) ? $value[0] : '',
					'post_title'                           => ! empty( $value[1] ) ? $value[1] : '',
					'post_status'                          => ! empty( $value[2] ) ? $value[2] : '',
					'wps_membership_plan_price'            => ! empty( $value[3] ) ? $value[3] : '',
					'wps_membership_plan_name_access_type' => ! empty( $value[4] ) ? $value[4] : '',
					'wps_membership_plan_duration'         => ! empty( $value[5] ) ? $value[5] : '',
					'wps_membership_plan_duration_type'    => ! empty( $value[6] ) ? $value[6] : '',
					'wps_membership_plan_recurring'        => ! empty( $value[7] ) ? $value[7] : '',
					'wps_membership_plan_access_type'      => ! empty( $value[8] ) ? $value[8] : '',
					'wps_membership_plan_time_duration'    => ! empty( $value[9] ) ? $value[9] : '',
					'wps_membership_plan_time_duration_type' => ! empty( $value[10] ) ? $value[10] : '',
					'wps_membership_plan_offer_price_type' => ! empty( $value[11] ) ? $value[11] : '',
					'wps_memebership_plan_discount_price'  => ! empty( $value[12] ) ? $value[12] : '',
					'wps_memebership_plan_free_shipping'   => ! empty( $value[13] ) ? $value[13] : '',
					'wps_membership_plan_target_ids'       => ! empty( $value[14] ) ? $this->import_csv_ids( $value[14] ) : '',
					'wps_membership_plan_target_categories' => ! empty( $value[15] ) ? $this->import_csv_ids( $value[15] ) : '',
					'post_content'                         => ! empty( $value[16] ) ? $value[16] : '',
				);
			}
		}
		return $formatted_data;
	}

	/**
	 * String to array conversion of pro ids.
	 *
	 * @param string $csv_string A string of CSV Products and Category ids.
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function import_csv_ids( $csv_string ) {

		$ids = array();
		if ( ! empty( $csv_string ) ) {

			$ids_array = explode( ',', $csv_string );
			foreach ( $ids_array as $key => $id ) {

				$matches = array();
				$check   = preg_match( '/(#[0-9][0-9])+/', $id, $matches );
				if ( $check ) {

					$ids[] = str_replace( '#', '', $matches[0] );
				}
			}
		}
		return $ids;
	}

	/**
	 * Returns all Products ids offered from CSV.
	 *
	 * @param array $csv_data An array of CSV data.
	 * @return array
	 * @since 1.0.0
	 */
	public function csv_prod_title( $csv_data ) {

		$csv_prod_title = array();
		$prod_array     = array();
		if ( ! empty( $csv_data ) && is_array( $csv_data ) ) {
			foreach ( $csv_data as $key => $value ) {

				if ( ! empty( $value[14] ) ) {

					$prod_array[] = explode( ',', $value[14] );
				}
			}
		}

		if ( ! empty( $prod_array ) && is_array( $prod_array ) ) {
			foreach ( $prod_array as $key ) {
				foreach ( $key as $index => $title ) {

					$matches = array();
					$check   = preg_match( '/[A-Za-z\s\#\-0-9]+/', $title, $matches );
					if ( $check ) {

						$csv_prod_title[] = $matches[0];
					}
				}
			}
		}
		return $csv_prod_title;
	}

	/**
	 * Return all Category ids offered from CSV.
	 *
	 * @param array $csv_data An array of CSV data.
	 * @return array
	 * @since 1.0.0
	 */
	public function csv_cat_title( $csv_data ) {

		$csv_cat_title = array();
		$cat_array     = array();
		if ( ! empty( $csv_data ) && is_array( $csv_data ) ) {
			foreach ( $csv_data as $key => $value ) {

				if ( ! empty( $value[15] ) ) {

					$cat_array[] = explode( ',', $value[15] );
				}
			}
		}

		if ( ! empty( $cat_array ) && is_array( $cat_array ) ) {
			foreach ( $cat_array as $key ) {
				foreach ( $key as $index => $title ) {

					$matches = array();
					$check   = preg_match( '/[A-Za-z\s\#\-0-9]+/', $title, $matches );
					if ( $check ) {

						$csv_cat_title[] = $matches[0];
					}
				}
			}
		}
		return $csv_cat_title;
	}

	/**
	 * Returns an aray of all products title available in woocommerce store.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function all_prod_title() {

		// Getting all Product ids from woocommerce.
		$product_titles = array();
		$all_prod_ids = get_posts(
			array(
				'posts_per_page' => -1,
				'post_type'      => array( 'product', 'product_variation' ),
				'fields'         => 'ids',
			)
		);

		if ( ! empty( $all_prod_ids ) && is_array( $all_prod_ids ) ) {
			foreach ( $all_prod_ids as $id ) {

				$product_titles[] = get_the_title( $id );
			}
		}
		return $product_titles;
	}

	/**
	 * Returns an array of all category title available in woocommerce store.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function all_cat_title() {

		// Getting all Category ids from woocommerce.
		$cate_titles = array();
		$all_cat_ids = get_terms(
			array(
				'taxonomy' => 'product_cat',
				'fields'   => 'ids',
			)
		);

		if ( ! empty( $all_cat_ids ) && is_array( $all_cat_ids ) ) {
			foreach ( $all_cat_ids as $id ) {

				$term = get_term_by( 'id', $id, 'product_cat' );
				if ( $term ) {

					$cate_titles[] = $term->name;
				}
			}
		}
		return $cate_titles;
	}

	/**
	 * Create pending membership for customer.
	 *
	 * @param array $fields an array of member billing details.
	 * @param int   $plan_id Is the membership plan ID.
	 * @param mixed $order_status is the order status.
	 * @return array
	 * @throws  Exception Error.
	 *
	 * @since 1.0.0
	 */
	public function create_membership_for_customer( $fields, $plan_id, $order_status ) {

		if ( ! empty( $plan_id ) && ! empty( $fields ) ) {

			// Getting plan data.
			$plan_obj  = get_post( $plan_id, ARRAY_A );
			$post_meta = get_post_meta( $plan_id );

			// Formatting array.
			foreach ( $post_meta as $key => $value ) {

				$post_meta[ $key ] = reset( $value );
			}

			$plan_meta = array_merge( $plan_obj, $post_meta );
			// Checking if user exist or not by email.
			$_user = get_user_by( 'email', $fields['membership_billing_email'] );

			// If user exist, get the required details.
			if ( $_user ) {

				$user_name = $_user->display_name;
				$user_id   = $_user->ID;
			} else {

				$is_user_created = get_option( 'wps_membership_create_user_after_payment', true );
				if ( 'on' == $is_user_created ) {

					$users = get_users();
					$max_user_id = '';
					foreach ( $users as $key => $value ) {
						if ( $value->ID > $max_user_id ) {

							$max_user_id = $value->ID;
						}
					}

					$user_id   = $max_user_id + 1;
					$user_name = '---';

				} else {

					$website   = get_site_url();
					$user_name = $fields['membership_billing_first_name'] . '-' . wp_rand();
					$password  = $fields['membership_billing_first_name'] . substr( $fields['membership_billing_phone'], -4, 4 );
					update_option( 'user_password', $password );
					$userdata = array(
						'user_login'   => $user_name,
						'user_url'     => $website,
						'user_pass'    => $password, // When creating an user, `user_pass` is expected.
						'user_email'   => $fields['membership_billing_email'],
						'first_name'   => $fields['membership_billing_first_name'],
						'last_name'    => $fields['membership_billing_last_name'],
						'display_name' => $fields['membership_billing_first_name'],
						'nickname'     => $fields['membership_billing_first_name'],
					);

					$_user = wp_insert_user( $userdata );
					update_user_meta( $_user, 'user_created_by_membership', 'yes' );
					update_option( 'user_name', $user_name );
					if ( $_user ) {

						$user_id   = $_user;

						// send user id and password to the user.
						$this->wps_msfw_send_user_login_details_to_users( $user_id );

						$user_ob   = get_user_by( 'id', $user_id );
						$user_name = $user_ob->display_name;
					}
				}
			}
			$is_processing = get_option( 'wps_membership_create_member_on_processing' );
			if ( 'on' === $is_processing ) {

				if ( 'processing' == $order_status ) {
					$order_st = 'complete';
				} elseif ( 'on-hold' == $order_status || 'refunded' == $order_status ) {
					$order_st = 'hold';
				} elseif ( 'pending' == $order_status || 'failed' == $order_status || 'completed' == $order_status ) {
					$order_st = 'pending';
				} elseif ( 'cancelled' == $order_status ) {
					$order_st = 'cancelled';
				}
			} elseif ( 'completed' == $order_status ) {
					$order_st = 'complete';
			} elseif ( 'on-hold' == $order_status || 'refunded' == $order_status ) {
				$order_st = 'hold';
			} elseif ( 'pending' == $order_status || 'failed' == $order_status || 'processing' == $order_status ) {
				$order_st = 'pending';
			} elseif ( 'cancelled' == $order_status ) {
				$order_st = 'cancelled';
			}

			// Creating post for members, keeping its status to pending.
			$member_id = wp_insert_post(
				array(
					'post_type'   => 'wps_cpt_members',
					'post_title'  => $user_name,
					'post_status' => 'publish',
					'post_author' => $user_id,
					'meta_input'  => array(
						'member_actions'  => 'email_invoice',
						'member_status'   => $order_st,
						'plan_obj'        => $plan_meta,
						'billing_details' => $fields,
					),
				)
			);

			return array(
				'status'    => true,
				'member_id' => $member_id,
				'user_id'   => $user_id,
			);
		}
	}

	/**
	 * Email membership invoice to customers after successfull purchase.
	 *
	 * @param int $member_id Members's ID.
	 */
	public function email_membership_invoice( $member_id ) {

		// Getting global options.
		$wps_membership_global_settings = get_option( 'wps_membership_global_options', $this->default_global_options() );

		// The main address pieces.
		$store_address   = get_option( 'woocommerce_store_address' );
		$store_address_2 = get_option( 'woocommerce_store_address_2' );
		$store_city      = get_option( 'woocommerce_store_city' );
		$store_postcode  = get_option( 'woocommerce_store_postcode' );

		// The country/state.
		$store_raw_country = get_option( 'woocommerce_default_country' );

		// Split the country/state.
		$split_country = explode( ':', $store_raw_country );

		// Country and state separated.
		$store_country = $split_country[0];
		$store_state   = $split_country[1];

		$store_details = $store_address . '<br/>' .
						! empty( $store_address_2 ) ? $store_address_2 : '<br/>' .
						$store_city . ', ' . $store_state . ' ' . $store_postcode . '<br/>' .
						$store_country;

		// From name.
		$from_name = get_bloginfo( 'name' );

		// From email.
		$from_email = get_bloginfo( 'admin_email' );

		if ( ! function_exists( 'wp_mail' ) ) {

			return;
		}

		if ( ! empty( $member_id ) ) {

			$plan_info = wps_membership_get_meta_data( $member_id, 'plan_obj', true );
			$billing   = wps_membership_get_meta_data( $member_id, 'billing_details', true );
			$status    = wps_membership_get_meta_data( $member_id, 'member_status', true );

			$first_name = ! empty( $billing['membership_billing_first_name'] ) ? $billing['membership_billing_first_name'] : '';
			$last_name  = ! empty( $billing['membership_billing_last_name'] ) ? $billing['membership_billing_last_name'] : '';
			$company    = ! empty( $billing['membership_billing_company'] ) ? $billing['membership_billing_company'] : '';
			$address_1  = ! empty( $billing['membership_billing_address_1'] ) ? $billing['membership_billing_address_1'] : '';
			$address_2  = ! empty( $billing['membership_billing_address_2'] ) ? $billing['membership_billing_address_2'] : '';
			$city       = ! empty( $billing['membership_billing_city'] ) ? $billing['membership_billing_city'] : '';
			$postcode   = ! empty( $billing['membership_billing_postcode'] ) ? $billing['membership_billing_postcode'] : '';
			$state      = ! empty( $billing['membership_billing_state'] ) ? $billing['membership_billing_state'] : '';
			$country    = ! empty( $billing['membership_billing_country'] ) ? $billing['membership_billing_country'] : '';
			$email      = ! empty( $billing['membership_billing_email'] ) ? $billing['membership_billing_email'] : '';
			$phone      = ! empty( $billing['membership_billing_phone'] ) ? $billing['membership_billing_phone'] : '';

			ob_start();
			?>

			<table id="wps-mfw__invoice-table">
				<tbody>
					<tr>
						<td><h1><?php esc_html_e( 'Membership Invoice #', 'membership-for-woocommerce' ); ?><strong><?php echo esc_html( $member_id ); ?></strong></h1></td>
						<td  id="wps-mfw__invoice-table-td" align="right"><img src="<?php echo esc_html( ! empty( $wps_membership_global_settings['wps_membership_invoice_logo'] ) ? $wps_membership_global_settings['wps_membership_invoice_logo'] : '' ); ?>" height="50px"/><br/>
							<?php echo esc_html( get_bloginfo( 'name' ) ); ?><br/>
							<?php echo esc_html( ! empty( $wps_membership_global_settings['wps_membership_invoice_address'] ) ? $wps_membership_global_settings['wps_membership_invoice_address'] : $store_details ); ?><br/>
							<br/>
							<strong><?php echo esc_html( ! empty( $wps_membership_global_settings['wps_membership_invoice_phone'] ) ? $wps_membership_global_settings['wps_membership_invoice_phone'] : '' ); ?></strong> | <strong><?php echo esc_html( ! empty( $wps_membership_global_settings['wps_membership_invoice_email'] ) ? $wps_membership_global_settings['wps_membership_invoice_email'] : get_option( 'woocommerce_email_from_address' ) ); ?></strong>
						</td>
					</tr>
				</tbody>
			</table>

			<table>
				<tbody>
					<tr>
						<td><b><?php esc_html_e( 'Invoice to : ', 'membership-for-woocommerce' ); ?></b><br/>
							<strong><?php echo esc_html( $first_name . $last_name ); ?></strong>
							<br/>
								<?php echo esc_html( $company ); ?><br/>
								<?php printf( ' %s %s ', esc_html( $address_1 ), esc_html( $address_2 ) ); ?><br/>
								<?php printf( ' %s %s ', esc_html( $city ), esc_html( $postcode ) ); ?><br/>
								<?php printf( ' %s, %s ', esc_html( $state ), esc_html( $country ) ); ?>
							<br/>
							<?php echo esc_html( $phone ); ?>
							<br/>
							<?php echo esc_html( $email ); ?>
						</td>
						<td align="right">
							<strong><?php printf( ' %s %s ', esc_html__( 'Status : ', 'membership-for-woocommerce' ), esc_html( $status ) ); ?></strong><br/>
							<?php printf( ' %s %s ', esc_html__( 'Invoice Date : ', 'membership-for-woocommerce' ), esc_html( gmdate( 'd-m-Y' ) ) ); ?>
						</td>
					</tr>
				</tbody>
			</table>

			<table id="wps-mfw__item-table">
				<thead>
					<tr class="tr_1">
						<th><?php esc_html_e( 'Item name', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Price', 'membership-for-woocommerce' ); ?> </th>
						<th><?php esc_html_e( 'Quantity', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Total', 'membership-for-woocommerce' ); ?></th>
					</tr>
				</thead>

				<tbody>
					<tr>
						<td><?php echo esc_html( ! empty( $plan_info['post_title'] ) ? $plan_info['post_title'] : '' ); ?></td>
						<td><?php echo esc_html( ! empty( $plan_info['wps_membership_plan_price'] ) ? get_woocommerce_currency() . ' ' . $plan_info['wps_membership_plan_price'] : '' ); ?></td>
						<td><?php esc_html_e( '1', 'membership-for-woocommerce' ); ?></td>
						<td><?php echo esc_html( ! empty( $plan_info['wps_membership_plan_price'] ) ? get_woocommerce_currency() . ' ' . $plan_info['wps_membership_plan_price'] : '' ); ?></td>
					</tr>

					<tr align="right">
						<td class="td_1" colspan="4"><strong><?php printf( ' %s %s ', esc_html__( 'Grand total : ', 'membership-for-woocommerce' ), esc_html( get_woocommerce_currency() . ' ' . $plan_info['wps_membership_plan_price'] ) ); ?></strong></td>
					</tr>
					<tr>
						<td colspan="4">
							<h2><?php esc_html_e( 'Thank you for shopping with us', 'membership-for-woocommerce' ); ?></h2><br/>
							<strong><?php echo esc_html( get_bloginfo( 'name' ) ); ?><br/></strong>
							<?php echo esc_html( get_bloginfo( 'description' ) ); ?>
						</td>
					</tr>
				</tbody>
			</table>


			<?php

			$content = ob_get_clean();

			ob_start();
			?>
			<!DOCTYPE html>
			<html lang="en">
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<title><?php esc_html_e( 'Membership Invoice', 'membership-for-woocommerce' ); ?></title>
			</head>
			<body>
				<div id="wps-mfw__member-invoice-container">
					<table border="0" cellpadding="0" cellspacing="0" id="wps-mfw__member-invoice-table-container">
						<tbody>
							<tr>
								<td class="hello td_1">
									<h1>
										<?php esc_html_e( 'Membership Invoice ', 'membership-for-woocommerce' ); ?><strong><?php echo esc_html__( '#', 'membership-for-woocommerce' ) . esc_html( $member_id ); ?></strong>
									</h1>
								</td>
								<td class="xyz td_2">
									<img src="<?php echo esc_html( ! empty( $wps_membership_global_settings['wps_membership_invoice_logo'] ) ? $wps_membership_global_settings['wps_membership_invoice_logo'] : '' ); ?>" height="50px" class="CToWUd">
									<br>
									<?php echo esc_html( get_bloginfo( 'name' ) ); ?><br>
									<?php echo esc_html( ! empty( $wps_membership_global_settings['wps_membership_invoice_address'] ) ? $wps_membership_global_settings['wps_membership_invoice_address'] : $store_details ); ?><br>
									<strong><?php echo esc_html( ! empty( $wps_membership_global_settings['wps_membership_invoice_phone'] ) ? $wps_membership_global_settings['wps_membership_invoice_phone'] : '' ); ?></strong>
									|
									<strong><?php echo esc_html( ! empty( $wps_membership_global_settings['wps_membership_invoice_email'] ) ? $wps_membership_global_settings['wps_membership_invoice_email'] : get_option( 'woocommerce_email_from_address' ) ); ?></strong>
								</td>
							</tr>
						</tbody>
					</table>

					<table border = "0" cellpadding = "0" cellspacing = "0" id="wps-mfw__member-invoice-to-table">
						<tbody>
							<tr>
								<td class="td_1">
									<b><?php esc_html_e( 'Invoice to : ', 'membership-for-woocommerce' ); ?></b><br>
									<strong><?php echo esc_html( $first_name . ' ' . $last_name ); ?></strong>
									<br>
									<?php echo esc_html( $company ); ?><br/>
									<?php printf( ' %s %s ', esc_html( $address_1 ), esc_html( $address_2 ) ); ?><br/>
									<?php printf( ' %s %s ', esc_html( $city ), esc_html( $postcode ) ); ?><br/>
									<?php printf( ' %s, %s ', esc_html( $state ), esc_html( $country ) ); ?>
									<br>
									<?php echo esc_html( $phone ); ?>
									<br/>
									<?php echo esc_html( $email ); ?>
								</td>
								<td class="td_2">
									<strong><?php printf( ' %s %s ', esc_html__( 'Status : ', 'membership-for-woocommerce' ), esc_html( $status ) ); ?></strong><br>
									<?php printf( ' %s %s ', esc_html__( 'Invoice Date : ', 'membership-for-woocommerce' ), esc_html( gmdate( 'd-m-Y' ) ) ); ?>
								</td>
							</tr>
						</tbody>
					</table>

					<div id="wps-mfw__member-item-container">
						<table border = "0" cellpadding = "0" cellspacing = "0" id="wps-mfw__member-item-table_a">
							<thead>
								<tr>
									<th class="th_1"><?php esc_html_e( 'Item name', 'membership-for-woocommerce' ); ?></th>
									<th class="th_2"><?php esc_html_e( 'Price', 'membership-for-woocommerce' ); ?> </th>
									<th class="th_3"><?php esc_html_e( 'Quantity', 'membership-for-woocommerce' ); ?></th>
									<th class="th_4"><?php esc_html_e( 'Total', 'membership-for-woocommerce' ); ?></th>
								</tr>
							</thead>

							<tbody>
								<tr>
									<td class="td_1"><?php echo esc_html( ! empty( $plan_info['post_title'] ) ? $plan_info['post_title'] : '' ); ?></td>
									<td class="td_2"><?php echo esc_html( ! empty( $plan_info['wps_membership_plan_price'] ) ? get_woocommerce_currency() . ' ' . $plan_info['wps_membership_plan_price'] : '' ); ?></td>
									<td class="td_3"><?php esc_html_e( '1', 'membership-for-woocommerce' ); ?></td>
									<td class="td_4"><?php echo esc_html( ! empty( $plan_info['wps_membership_plan_price'] ) ? get_woocommerce_currency() . ' ' . $plan_info['wps_membership_plan_price'] : '' ); ?></td>
								</tr>

								<tr align="right">
									<td colspan="4" class="td_5">
										<strong><?php printf( ' %s %s ', esc_html__( 'Grand total : ', 'membership-for-woocommerce' ), esc_html( get_woocommerce_currency() . ' ' . $plan_info['wps_membership_plan_price'] ) ); ?></strong>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<table border = "0" cellpadding = "0" cellspacing = "0" id="wps-mfw__member-item-table_b">
						<tbody>
							<tr>
								<td colspan="4" class="td_1">
									<h2><?php esc_html_e( 'Thank you for shopping with us', 'membership-for-woocommerce' ); ?></h2><br/>
									<strong><?php echo esc_html( get_bloginfo( 'name' ) ); ?><br/></strong>
									<?php echo esc_html( get_bloginfo( 'description' ) ); ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</body>
			</html>
			<?php
			$email_content = ob_get_clean();

			// Handling invoice creation and upload.
			$activity_class = new Membership_Activity_Helper( 'mfw-invoices', 'uploads' );
			$pdf_file       = $activity_class->create_pdf_n_upload( $content, $first_name );

			if ( true == $pdf_file['status'] ) {

				$pdf_location = $pdf_file['message'];
			} else {

				$activity_class = new Membership_Activity_Helper( 'Pdf-creation-logs', 'logger' );
				$activity_class->create_log( 'Pdf creation failure', $pdf_file['message'] );
			}

			$attachment = '';

			if ( ! empty( $wps_membership_global_settings['wps_membership_attach_invoice'] ) && 'on' == $wps_membership_global_settings['wps_membership_attach_invoice'] ) {
				// Get the attachment file using file url.
				$attachment = $pdf_location;
			}

			/**
			 * Now send mail to customer including virtual invoice and a hard copy of it as attachment.
			 */
			$_user      = get_user_by( 'email', $email );
			$user_email = $_user->user_email;

			// If user exists store its email id in array.
			if ( $user_email === $email ) {

				$to = array( $email );
			} else {
				$to = array( $email, $user_email );
			}

			$subject = 'Thanks for purchasing ' . $plan_info['post_title'];
			$mail    = wp_mail(
				$to,
				! empty( $wps_membership_global_settings['wps_membership_email_subject'] ) ? $wps_membership_global_settings['wps_membership_email_subject'] : $subject,
				! empty( $wps_membership_global_settings['wps_membership_email_content'] ) ? $wps_membership_global_settings['wps_membership_email_content'] . $email_content : $email_content,
				array(
					'Content-Type: text/html; charset=UTF-8',
					'From: ' . $from_name . ' <' . $from_email . '>',
				),
				$attachment
			);

			if ( $mail ) {
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * Returns Membership details tab headers.
	 */
	public function membership_tab_headers() {

		$table_headers = array(
			'members-id'      => esc_html__( 'Member ID', 'membership-for-woocommerce' ),
			'members-date'    => esc_html__( 'Date', 'membership-for-woocommerce' ),
			'members-status'  => esc_html__( 'Status', 'membership-for-woocommerce' ),
			'members-total'   => esc_html__( 'Total', 'membership-for-woocommerce' ),
			'members-actions' => esc_html__( 'Actions', 'membership-for-woocommerce' ),
		);

		/**
		 * Filter for tab headers.
		 *
		 * @since 1.0.0
		 */
		return apply_filters( 'wps_memberhsip_tab_headers', $table_headers );
	}

	/**
	 * Returns members details for CSV.
	 *
	 * @param object $post Post object.
	 * @param string $fields Member fields needed.
	 *
	 * @since 1.0.0
	 */
	public function get_member_details( $post = '', $fields = '' ) {

		$post_id = '';
		$result  = false;
		if ( ! empty( $post ) ) {
			$post_id = $post->ID;
		}

		$billing_info = wps_membership_get_meta_data( $post_id, 'billing_details', true );
		$plan_info    = wps_membership_get_meta_data( $post_id, 'plan_obj', true );
		$plan_status  = wps_membership_get_meta_data( $post_id, 'member_status', true );
		if ( ! empty( $fields ) && ! empty( $billing_info ) ) {

			switch ( $fields ) {

				case 'name':
					$result = $billing_info['membership_billing_first_name'] . $billing_info['membership_billing_last_name'];
					break;

				case 'email':
					$result = $billing_info['membership_billing_email'];
					break;

				case 'phone':
					$result = $billing_info['membership_billing_phone'];
					break;

				case 'payment_method':
					$result = ! empty( $billing_info['payment_method'] ) ? $this->get_payment_method_title( $billing_info['payment_method'] ) : $billing_info['payment_method'];
					break;
			}
		}

		if ( ! empty( $fields ) && ! empty( $plan_info ) ) {

			switch ( $fields ) {

				case 'plan_id':
					$result = $plan_info['ID'];
					break;

				case 'plan_name':
					$result = $plan_info['post_title'];
					break;
				case 'expiration':
					$result = wps_membership_get_meta_data( $post_id, 'member_expiry', true );
					break;
			}
		}

		if ( ! empty( $fields ) && 'plan_status' == $fields && ! empty( $plan_status ) ) {
			$result = $plan_status;
		}

		return $result;
	}

	/**
	 * This function is used to assign one time discount coupon to new members.
	 *
	 * @param  object $user user.
	 * @return void
	 */
	public function wps_msfw_assign_one_time_discount_coupon( $user ) {

		if ( ! empty( $user ) && is_object( $user ) ) {

			$wps_msfw_enable_to_rewards_one_time_coupon = get_option( 'wps_msfw_enable_to_rewards_one_time_coupon' );
			if ( 'on' === $wps_msfw_enable_to_rewards_one_time_coupon ) {

				$wps_msfw_one_time_coupon_amount = get_option( 'wps_msfw_one_time_coupon_amount' );
				$wps_msfw_one_time_coupon_amount = ! empty( $wps_msfw_one_time_coupon_amount ) && $wps_msfw_one_time_coupon_amount > 0 ? $wps_msfw_one_time_coupon_amount : 5;
				if ( ! empty( $user->user_email ) && $user->ID ) {

					// check if not assign.
					if ( empty( get_user_meta( $user->ID, 'wps_msfw_one_time_coupon_assign_done', true ) ) ) {

						// generate coupon code.
						$coupon_code = wp_generate_password( 8, false );
						// check coupon code exists.
						if ( $this->wps_msfw_coupon_code_exists( $coupon_code ) ) {

							$coupon_code = wp_generate_password( 8, false );
						}

						// insert coupon.
						$coupon = array(
							'post_title'   => $coupon_code,
							'post_content' => '',
							'post_status'  => 'publish',
							'post_author'  => 1,
							'post_type'    => 'shop_coupon',
						);

						$new_coupon_id = wp_insert_post( $coupon );
						// update coupon values.
						if ( ! empty( $new_coupon_id ) ) {

							$wps_msfw_set_coupon_max_usage = apply_filters( 'wps_msfw_set_coupon_usage_limit', 1 );
							update_post_meta( $new_coupon_id, 'discount_type', 'fixed_cart' );
							update_post_meta( $new_coupon_id, 'coupon_amount', $wps_msfw_one_time_coupon_amount );
							update_post_meta( $new_coupon_id, 'individual_use', 'yes' );
							update_post_meta( $new_coupon_id, 'product_ids', '' );
							update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
							update_post_meta( $new_coupon_id, 'usage_limit', $wps_msfw_set_coupon_max_usage );
							update_post_meta( $new_coupon_id, 'usage_limit_per_user', $wps_msfw_set_coupon_max_usage );
							update_post_meta( $new_coupon_id, 'expiry_date', strtotime( '+1 month' ) );
							update_user_meta( $user->ID, 'wps_msfw_one_time_coupon_assign_done', 'done' );

							$subject = 'Important message for you!';
							$body    = $coupon_code . ' ' . esc_html__( 'use this Coupon Code to get discount on Cart.', 'membership-for-woocommerce' );
							$headers = array( 'Content-Type: text/html; charset=UTF-8' );
							$headers = 'From: ' . get_option( 'admin_email' ) . "\r\n";
							wp_mail( $user->user_email, $subject, $body, $headers );
						}
					}
				}
			}
		}
	}

	/**
	 * Check coupon exists.
	 *
	 * @param  string $coupon_code coupon_code.
	 * @return bool
	 */
	public function wps_msfw_coupon_code_exists( $coupon_code ) {
		global $wpdb;
		$sql   = $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_title = %s AND post_type = 'shop_coupon'", $coupon_code );
		$count = $wpdb->get_var( $sql );
		return $count;
	}

	/**
	 * This function is used to block user.
	 *
	 * @return bool
	 */
	public function wps_mfw_is_user_block() {

		$flag              = true;
		$wps_is_user_block = get_user_meta( get_current_user_id(), 'wps_is_user_block', true );
		if ( 'yes' === $wps_is_user_block ) {

			$flag = false;
		}
		return $flag;
	}

	/**
	 * This function is used to send welcome mail while purchasing membership plan.
	 *
	 * @param  string $user_id user_id.
	 * @return void
	 */
	public function wps_mfw_membership_welcome_mail( $user_id ) {

		$wps_mfw_send_welcome_mail = get_option( 'wps_mfw_send_welcome_mail', true );
		if ( 'on' === $wps_mfw_send_welcome_mail ) {

			if ( ! empty( $user_id ) ) {

				$user                        = get_user_by( 'ID', $user_id );
				$wps_mfw_welcome_mail_sended = get_user_meta( $user_id, 'wps_mfw_welcome_mail_sended', true );
				if ( empty( $wps_mfw_welcome_mail_sended ) ) {

					$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );
					$current_memberships = ! empty( $current_memberships ) && is_array( $current_memberships ) ? $current_memberships : array();
					if ( ! empty( $current_memberships[0] ) && is_array( $current_memberships ) ) {

						$memberships_plan_obj = wps_membership_get_meta_data( $current_memberships[0], 'plan_obj', true );
						if ( ! empty( $memberships_plan_obj ) && is_array( $memberships_plan_obj ) ) {

							$welcome_email_body         = ! empty( get_option( 'wps_mfw_mail_welcome_body' ) ) ? get_option( 'wps_mfw_mail_welcome_body' ) : esc_html__( 'Welcome to the [Name] Membership! We are thrilled to have you as a part of our community. As a [Name] member, you now enjoy immediate access to a wide range of exclusive features and premium benefits designed just for you.', 'membership-for-woocommerce' );
							$welcome_email_body         = str_replace( '[Name]', ucfirst( $memberships_plan_obj['post_name'] ), $welcome_email_body );
							$welcome_image              = ! empty( get_option( 'wps_mfw_image_text_url' ) ) ? get_option( 'wps_mfw_image_text_url' ) : 'https://demo.wpswings.com/membership-for-woocommerce-pro/wp-content/uploads/2024/06/welcome.png';
							$wps_mfw_welcome_email_temp = '
								<body style="margin: 0; padding: 0;">
									<center>
										<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border-collapse: collapse !important;">
											<tr>
												<td>
													<table role="presentation" cellspacing="0" cellpadding="0" border="0" class="email-container" style="width: 100%;max-width:600px;margin:0 auto;">
														<!-- Header -->
														<tr>
															<td class="email-header" style="padding:40px 20px;text-align: center;">
																<h1 style="font-family:sans-serif;font-size:18px;font-weight:700;color:#3A9EFD;line-height:1.25;margin: 0 0 5px;">Hello! [Admin]</h1>
																<h2 style="font-family:sans-serif;font-size:20px;font-weight:600;color:#1a1a1a;line-height:1.25;margin: 0 0 15px;">Welcome to <strong>[Name] Membership!</strong></h2>
															</td>
														</tr>
														<!-- Body -->
														<tr>
															<td class="email-body" style="text-align: center;">
																<img src="' . $welcome_image . '" alt="welcome-image" style="width: 100%;height: auto;"/>
																<p style="font-family:sans-serif;font-size:14px;font-weight:400;color:#333333;line-height:1.5;padding:40px 20px 0;margin: 0 auto 15px;max-width:450px;">' . $welcome_email_body . '</p>
																<p style="margin: 0 0 40px;text-align:center;"><a href="#" style="display:inline-block;background:#3a9efd;border-radius: 100px;font-family:sans-serif;font-size:14px;font-weight:400;color:#ffffff;line-height:1.25;padding:10px 15px;text-decoration : none;">Visit Shop Page</a></p>
															</td>
														</tr>
														<!-- Footer -->
														<tr>
															<td class="email-footer" style="background: #F2F6F9;padding:40px 20px;text-align: center;">
																<h3 style="font-family:sans-serif;font-size:18px;font-weight:600;color:#1a1a1a;line-height:1.25;margin: 0 auto 15px;max-width:300px;">Thanks for becoming member</h3>
																<p style="font-family:sans-serif;font-size:14px;font-weight:400;color:#333333;line-height:1.5;margin: 0 auto;max-width:400px;"><a href="site" style="text-decoration : none;">[SITE_NAME]</a></p>
															</td>
														</tr>
													</table>    
												</td>
											</tr>
										</table>
									</center>
								</body>';

							$wps_mfw_welcome_email_temp = str_replace( '[Admin]', $user->display_name, $wps_mfw_welcome_email_temp );
							$wps_mfw_welcome_email_temp = str_replace( 'href="#"', 'href="' . get_permalink( wc_get_page_id( 'shop' ) ) . '"', $wps_mfw_welcome_email_temp );
							$wps_mfw_welcome_email_temp = str_replace( '[SITE_NAME]', get_bloginfo( 'name' ), $wps_mfw_welcome_email_temp );
							$wps_mfw_welcome_email_temp = str_replace( 'href="site"', 'href="' . site_url() . '"', $wps_mfw_welcome_email_temp );
							$wps_mfw_welcome_email_temp = str_replace( '[Name]', ucfirst( $memberships_plan_obj['post_name'] ), $wps_mfw_welcome_email_temp );
							$subject                    = ! empty( get_option( 'wps_mfw_mail_welcome_subject' ) ) ? get_option( 'wps_mfw_mail_welcome_subject' ) : esc_html__( 'Congratulations! You are Officially a Member', 'membership-for-woocommerce' );
							$headers                    = array( 'Content-Type: text/html; charset=UTF-8' );
							wp_mail( $user->user_email, $subject, $wps_mfw_welcome_email_temp, $headers );
							update_user_meta( $user_id, 'wps_mfw_welcome_mail_sended', 'done' );
						}
					}
				}
			}
		}

		// add members to buddypress group.
		do_action( 'wps_msfw_add_members_to_buudypress_group', $user_id );
	}

	/**
	 * Function for get all ids.
	 *
	 * @param string $plan_id is the id of plan.
	 * @return array.
	 */
	public function wps_get_target_ids( $plan_id = '' ) {

		$target_ids      = ! empty( wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_target_ids', true ) ) ? wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_target_ids', true ) : array();
		$target_cat_ids  = ! empty( wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_target_categories', true ) ) ? wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_target_categories', true ) : array();
		$target_tag_ids  = ! empty( wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_target_tags', true ) ) ? wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_target_tags', true ) : array();
		if ( empty( $target_ids ) ) {
			$target_ids = array();
		}
		if ( ! empty( $target_cat_ids ) && is_array( $target_cat_ids ) ) {
			foreach ( $target_cat_ids as $key => $value ) {

				$cat_name = get_the_category_by_ID( $value );
				$args     = array(
					'post_status' => 'publish',
					'post_type'   => 'product',
					'fields'      => 'ids',
					'product_cat' => $cat_name,

				);
				$result = get_posts( $args );
				if ( ! empty( $result ) ) {
					$target_ids = array_merge( $target_ids, $result );
				}
			}
		}

		if ( ! empty( $target_tag_ids ) && is_array( $target_tag_ids ) ) {
			foreach ( $target_tag_ids as $key => $value ) {

				$tag  = get_term( $value );
				$args = array(
					'post_status' => 'publish',
					'post_type'   => 'product',
					'fields'      => 'ids',
					'product_cat' => $tag->name,

				);
				$result1 = get_posts( $args );
				if ( ! empty( $result ) ) {
					$target_ids = array_merge( $target_ids, $result1 );
				}
			}
		}

		return array_unique( $target_ids );
	}

	/**
	 * This function is used check wither user is a member or not.
	 *
	 * @param  string $user_id user_id.
	 * @return bool
	 */
	public function wps_mfw_check_user_has_active_membership( $user_id ) {

		$is_member         = get_user_meta( $user_id, 'is_member', true );
		$mfw_membership_id = get_user_meta( $user_id, 'mfw_membership_id', true );
		$flag              = false;
		if ( ( ! empty( $mfw_membership_id ) && is_array( $mfw_membership_id ) ) && 'member' === $is_member ) {

			$flag = true;
		}
		return $flag;
	}

	/**
	 * This function is used to get specific user all assigned membership.
	 *
	 * @param  string $user_id user_id.
	 * @return array
	 */
	public function wps_mfw_get_all_specific_user_membership( $user_id ) {

		$membership_send_arr = array();
		$mfw_membership_id   = get_user_meta( $user_id, 'mfw_membership_id', true );
		if ( ! empty( $mfw_membership_id ) && is_array( $mfw_membership_id ) ) {
			foreach ( $mfw_membership_id as $membership_id ) {

				// check whether is exists or get from post meta.
				if ( function_exists( 'wps_membership_get_meta_data' ) ) {

					$membership_plan   = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
					$membership_status = wps_membership_get_meta_data( $membership_id, 'member_status', true );
				} else {

					$membership_plan   = get_post_meta( $membership_id, 'plan_obj', true );
					$membership_status = get_post_meta( $membership_id, 'member_status', true );
				}

				// return if no plan found.
				if ( empty( $membership_plan ) ) {

					continue;
				}

				// return if plan status is not completed.
				if ( 'complete' !== $membership_status ) {

					continue;
				}

				// push all active plan data.
				array_push( $membership_send_arr, $membership_plan );
			}
		}
		return $membership_send_arr;
	}

	/**
	 * Undocumented function.
	 *
	 * @param  string $country_code_name country_code_name.
	 * @return string
	 */
	public function get_country_code_by_name( $country_code_name ) {

		$countries = array(
			'AF' => array(
				'name' => 'Afghanistan',
				'dial_code' => '+93',
			),
			'AL' => array(
				'name' => 'Albania',
				'dial_code' => '+355',
			),
			'DZ' => array(
				'name' => 'Algeria',
				'dial_code' => '+213',
			),
			'US' => array(
				'name' => 'United States',
				'dial_code' => '+1',
			),
			'GB' => array(
				'name' => 'United Kingdom',
				'dial_code' => '+44',
			),
			'IN' => array(
				'name' => 'India',
				'dial_code' => '+91',
			),
			'AU' => array(
				'name' => 'Australia',
				'dial_code' => '+61',
			),
			'CA' => array(
				'name' => 'Canada',
				'dial_code' => '+1',
			),
			'CN' => array(
				'name' => 'China',
				'dial_code' => '+86',
			),
			'FR' => array(
				'name' => 'France',
				'dial_code' => '+33',
			),
			'DE' => array(
				'name' => 'Germany',
				'dial_code' => '+49',
			),
			'IT' => array(
				'name' => 'Italy',
				'dial_code' => '+39',
			),
			'JP' => array(
				'name' => 'Japan',
				'dial_code' => '+81',
			),
			'MX' => array(
				'name' => 'Mexico',
				'dial_code' => '+52',
			),
			'RU' => array(
				'name' => 'Russia',
				'dial_code' => '+7',
			),
			'ZA' => array(
				'name' => 'South Africa',
				'dial_code' => '+27',
			),
			'KR' => array(
				'name' => 'South Korea',
				'dial_code' => '+82',
			),
			'ES' => array(
				'name' => 'Spain',
				'dial_code' => '+34',
			),
			'SE' => array(
				'name' => 'Sweden',
				'dial_code' => '+46',
			),
			'CH' => array(
				'name' => 'Switzerland',
				'dial_code' => '+41',
			),
			'AE' => array(
				'name' => 'United Arab Emirates',
				'dial_code' => '+971',
			),
			'BR' => array(
				'name' => 'Brazil',
				'dial_code' => '+55',
			),
			'AR' => array(
				'name' => 'Argentina',
				'dial_code' => '+54',
			),
			'NG' => array(
				'name' => 'Nigeria',
				'dial_code' => '+234',
			),
			'PK' => array(
				'name' => 'Pakistan',
				'dial_code' => '+92',
			),
			'BD' => array(
				'name' => 'Bangladesh',
				'dial_code' => '+880',
			),
			'EG' => array(
				'name' => 'Egypt',
				'dial_code' => '+20',
			),
			'TR' => array(
				'name' => 'Turkey',
				'dial_code' => '+90',
			),
			'NL' => array(
				'name' => 'Netherlands',
				'dial_code' => '+31',
			),
			'BE' => array(
				'name' => 'Belgium',
				'dial_code' => '+32',
			),
			'AT' => array(
				'name' => 'Austria',
				'dial_code' => '+43',
			),
			'TH' => array(
				'name' => 'Thailand',
				'dial_code' => '+66',
			),
			'MY' => array(
				'name' => 'Malaysia',
				'dial_code' => '+60',
			),
			'SG' => array(
				'name' => 'Singapore',
				'dial_code' => '+65',
			),
			'NZ' => array(
				'name' => 'New Zealand',
				'dial_code' => '+64',
			),
			'PH' => array(
				'name' => 'Philippines',
				'dial_code' => '+63',
			),
			'VN' => array(
				'name' => 'Vietnam',
				'dial_code' => '+84',
			),
			'IL' => array(
				'name' => 'Israel',
				'dial_code' => '+972',
			),
			'SA' => array(
				'name' => 'Saudi Arabia',
				'dial_code' => '+966',
			),
		);

		$code = isset( $countries[ $country_code_name ] ) ? $countries[ $country_code_name ]['dial_code'] : '';
		return ! empty( $code ) ? str_replace( '+', '', $code ) : 0;
	}

	/**
	 * Check Buddy Press plugin active or not.
	 *
	 * @return bool
	 */
	public function wps_mfsw_is_buddy_press_active() {

		$flag = false;
		if ( is_plugin_active( 'buddypress/bp-loader.php' ) ) {

			$flag = true;
		}
		return $flag;
	}

	/**
	 * Send user id and password to new registered user.
	 *
	 * @param  int $user_id user_id.
	 * @return void
	 */
	public function wps_msfw_send_user_login_details_to_users( $user_id ) {
		$check = get_user_meta( $user_id, 'user_created_by_membership', true );
		if ( 'yes' == $check ) {

			$user       = get_userdata( $user_id );
			$user_email = $user->user_email;
			$user_login = $user->user_login;
			// for simplicity, lets assume that user has typed their first and last name when they sign up.
			$user_full_name = $user->user_firstname . ' ' . $user->user_lastname;
			$user_password = get_option( 'user_password', true );
			// Now we are ready to build our welcome email.
			$to      = $user_email;
			$subject = 'Hi ' . esc_attr( $user_full_name ) . ', welcome to our site!';
			$body    = '
					  <h1>Dear ' . esc_attr( $user_full_name ) . ',</h1></br>
					  <p>Thank you for joining our site. Your account is now active.</p>
					  <p>Please go ahead and navigate around your account.</p>
					  <p>Here is your Credentials </p>
					  <p> User ID - ' . esc_attr( $user_login ) . ' </p>
					  <p> Password - ' . esc_attr( $user_password ) . ' </p>
			';
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			wp_mail( $to, $subject, $body, $headers );
		}
	}

	/**
	 * This function is used to list all membership.
	 *
	 * @return array
	 */
	public function wps_msfw_list_all_membership() {

		$results = get_posts(
			array(
				'post_type'   => 'wps_cpt_membership',
				'post_status' => 'publish',
				'numberposts' => -1,
			)
		);

		$wps_msfw_all_membership = array();
		if ( ! empty( $results ) && is_array( $results ) ) {
			foreach ( $results as $key => $value ) {

				$wps_msfw_all_membership[ $value->ID ] = $value->post_title;
			}
		}
		return $wps_msfw_all_membership;
	}

	/**
	 * This function is used to assign membership to user by user id.
	 *
	 * @param  int $user_id       user_id.
	 * @param  int $membership_id membership_id.
	 * @return void
	 */
	public function wps_msfw_assigned_membership_by_user_id( $user_id, $membership_id ) {

		if ( ! empty( $user_id ) && ! empty( $membership_id ) ) {

			$post_id = wp_insert_post(
				array(
					'post_type'   => 'wps_cpt_members',
					'post_status' => 'publish',
					'post_author' => $user_id,
				),
				true
			);

			wps_membership_update_meta_data( $post_id, 'member_status', 'complete' );
			$actions = array(
				'member_status'  => 'complete',
				'member_actions' => '',
			);

			// When plans are assigned manually.
			$plan_obj    = get_post( $membership_id, ARRAY_A );
			$plan_status = ! empty( $plan_obj['post_status'] ) ? $plan_obj['post_status'] : '';
			if ( ! is_array( $plan_obj ) || 'publish' != $plan_status ) {

				return;
			}

			$post_meta = get_post_meta( $membership_id );
			foreach ( $post_meta as $post_meta_key => $post_meta_value ) {

				$post_meta[ $post_meta_key ] = reset( $post_meta_value );
			}

			$plan_meta = array_merge( $plan_obj, $post_meta );
			wps_membership_update_meta_data( $post_id, 'plan_obj', $plan_meta );
			wps_membership_update_meta_data( $post_id, 'wps_member_user', $user_id );

			$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );
			$current_memberships = ! empty( $current_memberships ) ? $current_memberships : array();
			if ( ! in_array( $post_id, (array) $current_memberships ) ) {

				array_push( $current_memberships, $post_id );
			}

			if ( 'yes' == wps_membership_get_meta_data( $membership_id, 'wps_membership_subscription', true ) ) {

				wps_membership_update_meta_data( $post_id, 'is_subscription_plan_member', 'yes' );
			} else {
				wps_membership_update_meta_data( $post_id, 'is_subscription_plan_member', '' );
			}

			// Assign membership plan to user and assign 'member' role to it.
			update_user_meta( $user_id, 'mfw_membership_id', $current_memberships );

			// Getting current activation date.
			$current_date = gmdate( 'Y-m-d' );
			$plan_obj     = wps_membership_get_meta_data( $post_id, 'plan_obj', true );
			// Save expiry date in post.
			if ( ! empty( $plan_obj ) && is_array( $plan_obj ) ) {

				$membership_plubic = new Membership_For_Woocommerce_Public( '', '' );
				$membership_plubic->assign_club_membership_to_member( $plan_obj['ID'], $plan_obj, $post_id );
				$access_type       = wps_membership_get_meta_data( $plan_obj['ID'], 'wps_membership_plan_access_type', true );
				if ( 'delay_type' == $access_type ) {

					$time_duration      = wps_membership_get_meta_data( $plan_obj['ID'], 'wps_membership_plan_time_duration', true );
					$time_duration_type = wps_membership_get_meta_data( $plan_obj['ID'], 'wps_membership_plan_time_duration_type', true );
					$current_date       = gmdate( 'Y-m-d', strtotime( $current_date . ' + ' . $time_duration . ' ' . $time_duration_type ) );
				}

				if ( 'lifetime' == $plan_obj['wps_membership_plan_name_access_type'] ) {

					wps_membership_update_meta_data( $post_id, 'member_expiry', 'Lifetime' );

				} elseif ( 'limited' == $plan_obj['wps_membership_plan_name_access_type'] ) {

					$duration    = $plan_obj['wps_membership_plan_duration'] . ' ' . $plan_obj['wps_membership_plan_duration_type'];
					$today_date  = gmdate( 'Y-m-d' );
					$expiry_date = strtotime( $today_date . $duration );
					wps_membership_update_meta_data( $post_id, 'member_expiry', $expiry_date );
					$order_id = wps_membership_get_meta_data( $post_id, 'member_order_id', true );
					if ( array_key_exists( 'wps_membership_subscription', $plan_obj ) ) {

						if ( 'yes' == $plan_obj['wps_membership_subscription'] ) {
							$subscription_id = wps_membership_get_meta_data( $order_id, 'wps_subscription_id', true );
							if ( ! empty( $subscription_id ) ) {

								wps_membership_update_meta_data( $subscription_id, 'wps_subscription_status', 'active' );
								wps_membership_update_meta_data( $subscription_id, 'wps_next_payment_date', $expiry_date );
								if ( ! empty( $plan_obj['wps_membership_subscription_expiry'] ) ) {
									if ( function_exists( 'wps_sfw_susbcription_expiry_date' ) ) {

										$current_time         = current_time( 'timestamp' );
										$wps_susbcription_end = wps_sfw_susbcription_expiry_date( $subscription_id, $current_time );
										wps_membership_update_meta_data( $subscription_id, 'wps_susbcription_end', $wps_susbcription_end );
									}
								} else {
									wps_membership_update_meta_data( $subscription_id, 'wps_susbcription_end', '' );
								}
							}
						}
					}
				}

				$user        = get_userdata( $user_id );// create a new user object for this user.
				$expiry_date = wps_membership_get_meta_data( $post_id, 'member_expiry', true );
				if ( 'Lifetime' == $expiry_date ) {

					$expiry_date = 'Lifetime';
				} else {
					$expiry_date = esc_html( ! empty( $expiry_date ) ? gmdate( 'Y-m-d', $expiry_date ) : '' );
				}

				$order_id  = wps_membership_get_meta_data( $post_id, 'member_order_id', true );
				$user_name = '';
				if ( isset( $user->data->display_name ) ) {

					$user_name = $user->data->display_name;
				}

				$customer_email = '';
				if ( key_exists( 'membership_creation_email', WC()->mailer()->emails ) ) {

					$customer_email = WC()->mailer()->emails['membership_creation_email'];
				}

				if ( ! empty( $customer_email ) ) {
					$email_status = $customer_email->trigger( $user_id, $plan_obj, $user_name, $expiry_date, $order_id );
				}
			}

			update_user_meta( $user_id, 'is_member', 'member' );
			foreach ( $actions as $action => $value ) {
				if ( array_key_exists( $action, $_POST ) ) {

					wps_membership_update_meta_data( $post_id, $action, $value );
				}
			}

			// Saving member billing details metabox fields.
			if ( isset( $_POST['payment_gateway_select'] ) ) {

				$payment = ! empty( $_POST['payment_gateway_select'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_gateway_select'] ) ) : '';

			} elseif ( isset( $_POST['billing_payment'] ) ) {

				$payment = ! empty( $_POST['billing_payment'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_payment'] ) ) : '';
			} else {
				$payment = ! empty( wps_membership_get_meta_data( $post_id, 'billing_details_payment', true ) ) ? wps_membership_get_meta_data( $post_id, 'billing_details_payment', true ) : '';
			}

			// phpcs:disable.
			$nonce = wp_create_nonce( 'membership_billing_nonce' );
			if ( wp_verify_nonce( $nonce, 'membership_billing_nonce' ) ) {

				$fields = array(
					'membership_billing_first_name' => ! empty( $_POST['billing_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_first_name'] ) ) : '',
					'membership_billing_last_name'  => ! empty( $_POST['billing_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_last_name'] ) ) : '',
					'membership_billing_company'    => ! empty( $_POST['billing_company'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_company'] ) ) : '',
					'membership_billing_address_1'  => ! empty( $_POST['billing_address_1'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address_1'] ) ) : '',
					'membership_billing_address_2'  => ! empty( $_POST['billing_address_2'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address_2'] ) ) : '',
					'membership_billing_city'       => ! empty( $_POST['billing_city'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_city'] ) ) : '',
					'membership_billing_postcode'   => ! empty( $_POST['billing_postcode'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_postcode'] ) ) : '',
					'membership_billing_country'    => ! empty( $_POST['billing_country'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_country'] ) ) : '',
					'membership_billing_state'      => ! empty( $_POST['billing_state'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_state'] ) ) : '',
					'membership_billing_email'      => ! empty( $_POST['billing_email'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_email'] ) ) : '',
					'membership_billing_phone'      => ! empty( $_POST['billing_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) : '',
					'payment_method'                => $payment,
				);
				// phpcs:enable.
				wps_membership_update_meta_data( $post_id, 'billing_details', $fields );
			}
		}
	}

	/**
	 * Return only valid and publish membership ids.
	 *
	 * @param mixed $user_id user_id.
	 * @return array
	 */
	public function wps_msfw_check_membership_id_is_valid( $user_id ) {

		// membership wise buddy groups settings.
		$membership_ids = get_user_meta( $user_id, 'mfw_membership_id', true );
		$membership_ids = is_array( $membership_ids ) ? $membership_ids : array();

		$cancelled_memberships = get_user_meta( $user_id, 'wps_msfw_cancel_membership_ids', true );
		$cancelled_memberships = is_array( $cancelled_memberships ) ? $cancelled_memberships : array();

		// Remove cancelled memberships if any.
		if ( ! empty( $cancelled_memberships ) ) {
			$membership_ids = array_diff( $membership_ids, $cancelled_memberships );
		}

		// Keep only existing posts; sanitize IDs and reindex.
		$membership_ids = array_values(
			array_filter(
				$membership_ids,
				static function ( $post_id ) {
					$post_id = absint( $post_id );
					return ( $post_id > 0 ) && get_post( $post_id );
				}
			)
		);

		// Preserve original behavior: return only if non-empty array.
		return ! empty( $membership_ids ) ? $membership_ids : array();
	}

	/**
	 * This function is used to build data for membership report export.
	 *
	 * @return array
	 */
	public function wps_mfw_build_membership_report_data() {

		// 1) Total membership plans.
		$total_membership_plans = 0;
		$args = array(
			'post_type'      => 'wps_cpt_membership',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);

		$loop = new WP_Query( $args );
		while ( $loop->have_posts() ) {
			$loop->the_post();
			$total_membership_plans++;
		}
		wp_reset_postdata();

		// 2) Members status counts (same as your logic).
		$total_members = 0;
		$complete      = 0;
		$pending       = 0;
		$expired       = 0;

		$member_ids = get_posts(
			array(
				'post_type'   => 'wps_cpt_members',
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields'      => 'ids',
			)
		);

		$array_of_ids           = array(); // store related order ids.
		$wps_store_member_ids   = array();

		if ( ! empty( $member_ids ) && is_array( $member_ids ) ) {

			foreach ( $member_ids as $member_post_id ) {

				$wps_store_member_ids[] = $member_post_id;

				$member_status = wps_membership_get_meta_data( $member_post_id, 'member_status', true );

				// IMPORTANT: keep your existing assumption so export matches screen.
				// (If you want, later we can replace this with a proper stored order-id meta.).
				$order_id = $member_post_id - 1;

				$order = wc_get_order( $order_id );
				if ( ! $order ) {
					continue;
				}

				$items = $order->get_items();
				if ( empty( $items ) ) {
					continue;
				}

				foreach ( $items as $item ) {

					// FIX: never use get_meta_data()[1] — it’s not guaranteed.
					$member_id_meta = $item->get_meta( '_member_id', true );

					if ( empty( $member_id_meta ) ) {
						continue;
					}

					// Same counters as your screen.
					if ( 'complete' === $member_status ) {
						$complete++;
						if ( ! in_array( $order_id, $array_of_ids, true ) ) {
							$array_of_ids[] = $order_id;
						}
					}

					if ( 'pending' === $member_status ) {
						$pending++;
					}

					if ( 'expired' === $member_status ) {
						$expired++;
						if ( ! in_array( $order_id, $array_of_ids, true ) ) {
							$array_of_ids[] = $order_id;
						}
					}

					$total_members++;
				}
			}
		}

		// 3) Last Activated Members (date buckets).
		$today       = 0;
		$yesterday   = 0;
		$last_7_days = 0;
		$this_month  = 0;
		$last_month  = 0;
		$this_year   = 0;
		$last_year   = 0;

		$today_timestamp = current_time( 'timestamp' );
		$today_start     = strtotime( 'today', $today_timestamp );

		$this_year_start = strtotime( gmdate( 'Y-01-01 00:00:00', $today_timestamp ) );
		$last_year_start = strtotime( gmdate( 'Y-01-01 00:00:00', strtotime( '-1 year', $today_timestamp ) ) );
		$last_year_end   = strtotime( gmdate( 'Y-12-31 23:59:59', strtotime( '-1 year', $today_timestamp ) ) );

		$last_7_days_start = strtotime( '-7 days', $today_timestamp );
		$yesterday_start   = strtotime( 'yesterday', $today_timestamp );

		$this_month_start = strtotime( gmdate( 'Y-m-01 00:00:00', $today_timestamp ) );
		$last_month_start = strtotime( gmdate( 'Y-m-01 00:00:00', strtotime( '-1 month', $today_timestamp ) ) );
		$last_month_end   = strtotime( gmdate( 'Y-m-t 23:59:59', strtotime( '-1 month', $today_timestamp ) ) );

		// Pull completed orders only (as you did).
		$order_ids = get_posts(
			array(
				'post_type'   => 'shop_order',
				'post_status' => 'wc-completed', // FIX: correct Woo status.
				'numberposts' => -1,
				'fields'      => 'ids',
			)
		);

		if ( ! empty( $order_ids ) && is_array( $order_ids ) && ! empty( $array_of_ids ) ) {

			foreach ( $order_ids as $oid ) {

				// only orders we stored from membership loop.
				if ( ! in_array( $oid, $array_of_ids, true ) ) {
					continue;
				}

				$order = wc_get_order( $oid );
				if ( ! $order ) {
					continue;
				}

				// FIX: use order created date (stable) instead of first order note date.
				$date_created = $order->get_date_created();
				if ( ! $date_created ) {
					continue;
				}
				$order_timestamp = $date_created->getTimestamp();

				$items = $order->get_items();
				if ( empty( $items ) ) {
					continue;
				}

				foreach ( $items as $item ) {

					if ( empty( $item->get_meta( '_member_id', true ) ) ) {
						continue;
					}

					if ( $yesterday_start <= $order_timestamp && $order_timestamp < $today_start ) {
						$yesterday++;
					}

					if ( $today_start <= $order_timestamp && $order_timestamp <= $today_timestamp ) {
						$today++;
					}

					if ( $last_7_days_start <= $order_timestamp && $order_timestamp <= $today_timestamp ) {
						$last_7_days++;
					}

					if ( $this_month_start <= $order_timestamp && $order_timestamp <= $today_timestamp ) {
						$this_month++;
					}

					if ( $last_month_start <= $order_timestamp && $order_timestamp <= $last_month_end ) {
						$last_month++;
					}

					if ( $last_year_start <= $order_timestamp && $order_timestamp <= $last_year_end ) {
						$last_year++;
					}

					if ( $this_year_start <= $order_timestamp && $order_timestamp <= $today_timestamp ) {
						$this_year++;
					}
				}
			}
		}

		return array(
			'total_membership_plans' => $total_membership_plans,
			'total_members'          => $total_members,
			'complete'               => $complete,
			'pending'                => $pending,
			'expired'                => $expired,
			'wps_store_member_ids'   => $wps_store_member_ids,
			'activity'               => array(
				'today'       => $today,
				'yesterday'   => $yesterday,
				'last_7_days' => $last_7_days,
				'this_month'  => $this_month,
				'last_month'  => $last_month,
				'this_year'   => $this_year,
				'last_year'   => $last_year,
			),
		);
	}
}
