<?php
/**
 * The global helper class of the plugin.
 *
 * @link       https://makewebbetter.com
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
 * @author     Make Web Better <plugins@makewebbetter.com>
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

			$allowed_html = array(
				'span' => array(
					'class'    => array(),
					'data-tip' => array(),
				),
			);

			echo wp_kses( wc_help_tip( $description ), $allowed_html );
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

		if ( ! empty( $product_id ) ) {

			$result = esc_html__( 'Product not found', 'membership-for-woocommerce' );

			$product = wc_get_product( $product_id );

			if ( ! empty( $product ) ) {

				if ( 'publish' !== $product->get_status() ) {

					$result = esc_html__( 'Product unavailable', 'membership-for-woocommerce' );

				} else {

					$result = get_the_title( $product_id );

				}
			}

			return $result;
		}
	}

	/**
	 * Return category name and its existance
	 *
	 * @param string $cat_id Category ID of a particular category.
	 *
	 * @since 1.0.0
	 */
	public function get_category_title( $cat_id = '' ) {

		if ( ! empty( $cat_id ) ) {

			$result = esc_html__( 'Category not found', 'membership-for-woocommerce' );

			$cat_name = get_the_category_by_ID( $cat_id );

			if ( ! empty( $cat_name ) ) {

				$result = $cat_name;

			}

			return $result;
		}
	}

	/**
	 * Membership default global options.
	 *
	 * @since 1.0.0
	 */
	public function default_global_options() {

		$default_global_settings = array(

			'mwb_membership_enable_plugin'              => 'on',
			'mwb_membership_manage_content'             => 'hide_for_non_members',
			'mwb_membership_manage_content_display_msg' => '',
			'mwb_membership_delete_data'                => 'off',
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

		if ( ! empty( $products ) && is_array( $products ) ) {

			$product_ids = ! empty( $products ) ? array_map( 'absint', $products ) : null;

			$output = '';

			if ( $product_ids ) {

				foreach ( $product_ids as $single_id ) {

					$single_name = $this->get_product_title( $single_id );
					$output     .= esc_html( $single_name ) . '(#' . esc_html( $single_id ) . '),';

				}
			}

			$output = preg_replace( '/,[^,]*$/', '', $output );
			return $output;
		}

	}

	/**
	 * Membership category title for CSV.
	 *
	 * @param array $categories An array of cataegory ids.
	 *
	 * @since 1.0.0
	 */
	public function csv_get_cat_title( $categories ) {

		if ( ! empty( $categories ) && is_array( $categories ) ) {

			$category_ids = ! empty( $categories ) ? array_map( 'absint', $categories ) : null;

			$output = '';

			if ( $category_ids ) {

				foreach ( $category_ids as $cat_id ) {

					$single_cat = $this->get_category_title( $cat_id );
					$output    .= esc_html( $single_cat ) . '(#' . esc_html( $cat_id ) . '),';
				}
			}

			$output = preg_replace( '/,[^,]*$/', '', $output );
			return $output;
		}
	}

	/**
	 * Membership supported gateways.
	 *
	 * @since 1.0.0
	 */
	public function supported_gateways() {

		$supported_gateways = array(

			'membership-paypal-gateway', // Membership Paypal.
			'membership-stripe-gateway', // Membership stripe.
			'membership-adv-bank-transfer', // Mwb Advance abnk transfer.
			'membership-paypal-smart-buttons',
		);

		return apply_filters( 'mwb_membership_for_woo_supported_gateways', $supported_gateways );
	}

	/**
	 * Available payment gateways.
	 *
	 * @since 1.0.0
	 */
	public function available_gateways() {

		$wc_gateways      = new WC_Payment_Gateways();
		$payment_gateways = $wc_gateways->get_available_payment_gateways();

		$woo_gateways = array();

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

		$title = '';

		$wc_gateways      = new WC_Payment_Gateways();
		$payment_gateways = $wc_gateways->get_available_payment_gateways();

		if ( ! empty( $payment_gateways ) && is_array( $payment_gateways ) ) {

			// Loop through Woocommerce available payment gateways.
			foreach ( $payment_gateways as $gateway ) {

				if ( $method_id === $gateway->id ) {

					$title = $gateway->method_title;
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

		$cart_items = WC()->cart->get_cart();

		$prod_ids = array();

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

		$cat_ids = array();

		if ( ! empty( $cart_items ) && is_array( $cart_items ) ) {

			foreach ( $cart_items as $cart_item_key => $cart_item ) {

				$cat_ids = array_merge( $cat_ids, $cart_item['data']->get_category_ids() );
			}
		}

		return $cat_ids;
	}

	/**
	 * Get all plans offered products ids.
	 *
	 * @since 1.0.0
	 */
	public function plans_products_ids() {

		$args = array(
			'post_type'   => 'mwb_cpt_membership',
			'post_status' => array( 'publish' ),
			'numberposts' => -1,
		);

		$products = array();

		$ids = array();

		$all_posts = get_posts( $args );

		if ( ! empty( $all_posts ) && is_array( $all_posts ) ) {

			foreach ( $all_posts as $post ) {

				$products = get_post_meta( $post->ID, 'mwb_membership_plan_target_ids', true );

				if ( is_array( $products ) ) {

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
			'post_type'   => 'mwb_cpt_membership',
			'post_status' => array( 'publish' ),
			'numberposts' => -1,
		);

		$categories = array();

		$cat_ids = array();

		$all_posts = get_posts( $args );

		if ( ! empty( $all_posts ) && is_array( $all_posts ) ) {

			foreach ( $all_posts as $post ) {

				$categories = get_post_meta( $post->ID, 'mwb_membership_plan_target_categories', true );

				if ( is_array( $categories ) ) {

					foreach ( $categories as $id ) {

						$cat_ids[] = $id;
					}
				}
			}
		}

		return $cat_ids;
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
						<div class="mwb_mfw_membership_front_page">
						<i class="fas fa-award mwb_mfw_membership_icon"></i>
						<div class="mwb_membership_plan_content_title">[mwb_membership_title]</div>
						<div class="mwb_membership_plan_content_price">[mwb_membership_price]</div>
						<div class="mwb_membership_plan_content_desc">[mwb_membership_desc]</div>
						<div class="mwb_mfw_buy_button">[mwb_membership_yes] [mwb_membership_no]</div>
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
		<div class="import_csv_field_wrapper" style="display: none;">
			<input type="file" name="csv_to_import" id="csv_file_upload">
			<input type="submit" value="Upload File" name="upload_csv_file" id="upload_csv_file" >
		</div>
		<?php

	}


	/**
	 * Returns payment modal content
	 *
	 * @param object $gateway An object of payment gateway.
	 * @return void
	 */
	public function gateway_modal_content( $gateway ) {

		?>
		<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?>" data-id="<?php echo esc_attr( $gateway->id ); ?>">
			<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio payment_method_select" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?> " required/>

			<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>">
				<?php echo $gateway->get_title(); /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?> <?php echo $gateway->get_icon(); /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
			</label>
			<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
				<div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" 
				<?php
				if ( ! $gateway->chosen ) :
						/* phpcs:ignore Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace */
					?>
					style="display:none;"<?php endif; /* phpcs:ignore Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace */ ?>>
					<?php $gateway->payment_fields(); ?>
				</div>
			<?php endif; ?>
		</li>
		<?php
	}

	/**
	 * Returns modal payment div wrapper.
	 *
	 * @param int $plan_id Membership plan ID.
	 *
	 * @since 1.0.0
	 */
	public function payment_gateways_html( $plan_id ) {

		$wc_gateways      = new WC_Payment_Gateways();
		$payment_gateways = $wc_gateways->get_available_payment_gateways();

		$supported_gateways = $this->supported_gateways();

		?>
		<form id="mwb_membership_buy_now_modal_form" action="" method="post" enctype="multipart/form-data" style="display: none;">

			<div class="mwb_membership_buy_now_modal">

				<!-- Modal payment content start -->
				<div class="mwb_membership_payment_modal">
					<?php
					foreach ( $payment_gateways as $gateway ) {

						if ( in_array( $gateway->id, $supported_gateways, true ) ) {

							$this->gateway_modal_content( $gateway );
						}
					}
					?>
				</div>
				<!-- Modal payment content end. -->

				<?php
				// Modal billing fields.
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/templates/mwb-membership-billing-modal.php';
				?>

				<!-- Paypal smarts buttons container -->
				<div id="paypal-button-container" style="display: none;"></div>
			</div>
		</form>
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
			'post_type'   => 'mwb_cpt_membership',
			'post_status' => array( 'publish' ),
			'numberposts' => -1,
		);

		$check = '';

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
			'post_type'   => 'mwb_cpt_membership',
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
	 * Function to run query.
	 *
	 * @param string $query Is the query.
	 * @return mixed
	 *
	 * @since 1.0.0
	 */
	public function run_query( $query = '' ) {

		global $wpdb;

		return ! empty( $wpdb->get_results( $query, ARRAY_A ) ) ? $wpdb->get_results( $query, ARRAY_A ) : false;
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
					'mwb_membership_plan_price'            => ! empty( $value[3] ) ? $value[3] : '',
					'mwb_membership_plan_name_access_type' => ! empty( $value[4] ) ? $value[4] : '',
					'mwb_membership_plan_duration'         => ! empty( $value[5] ) ? $value[5] : '',
					'mwb_membership_plan_duration_type'    => ! empty( $value[6] ) ? $value[6] : '',
					'mwb_membership_plan_start'            => ! empty( $value[7] ) ? $value[7] : '',
					'mwb_membership_plan_end'              => ! empty( $value[8] ) ? $value[8] : '',
					'mwb_membership_plan_recurring'        => ! empty( $value[9] ) ? $value[9] : '',
					'mwb_membership_plan_user_access'      => ! empty( $value[10] ) ? $value[10] : '',
					'mwb_membership_plan_access_type'      => ! empty( $value[11] ) ? $value[11] : '',
					'mwb_membership_plan_time_duration'    => ! empty( $value[12] ) ? $value[12] : '',
					'mwb_membership_plan_time_duration_type' => ! empty( $value[13] ) ? $value[13] : '',
					'mwb_membership_plan_offer_price_type' => ! empty( $value[14] ) ? $value[14] : '',
					'mwb_memebership_plan_discount_price'  => ! empty( $value[15] ) ? $value[15] : '',
					'mwb_memebership_plan_free_shipping'   => ! empty( $value[16] ) ? $value[16] : '',
					'mwb_membership_plan_target_ids'       => ! empty( $value[17] ) ? $this->import_csv_ids( $value[17] ) : '',
					'mwb_membership_plan_target_categories' => ! empty( $value[18] ) ? $this->import_csv_ids( $value[18] ) : '',
					'post_content'                         => ! empty( $value[19] ) ? $value[19] : '',

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

				$check = preg_match( '/(#[0-9][0-9])+/', $id, $matches );

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

				if ( ! empty( $value[17] ) ) {

					$prod_array[] = explode( ',', $value[17] );
				}
			}
		}

		if ( ! empty( $prod_array ) && is_array( $prod_array ) ) {

			foreach ( $prod_array as $key ) {

				foreach ( $key as $index => $title ) {

					$matches = array();

					$check = preg_match( '/[A-Za-z\s\#\-0-9]+/', $title, $matches );

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

				if ( ! empty( $value[18] ) ) {

					$cat_array[] = explode( ',', $value[18] );
				}
			}
		}

		if ( ! empty( $cat_array ) && is_array( $cat_array ) ) {

			foreach ( $cat_array as $key ) {

				foreach ( $key as $index => $title ) {

					$matches = array();

					$check = preg_match( '/[A-Za-z\s\#\-0-9]+/', $title, $matches );

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

		$product_titles = array();

		// Getting all Product ids from woocommerce.
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

		$cate_titles = array();

		// Getting all Category ids from woocommerce.
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
	 * @param int   $user id User id to obtain transaction details.
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function create_membership_for_customer( $fields, $plan_id ) {

		if ( ! empty( $plan_id ) && ! empty( $fields ) ) {

			// Getting plan data.
			$plan_obj = get_post( $plan_id, ARRAY_A );

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

				// Create user.
				$_user = wp_create_user( $fields['membership_billing_first_name'], '', $$fields['membership_billing_email'] );

				if ( $_user ) {

					$user_id   = $_user;
					$user_ob   = get_user_by( 'id', $user_id );
					$user_name = $user_ob->display_name;
				}
			}

			// Creating post for members, keeping its status to pending.
			$member_id = wp_insert_post(
				array(
					'post_type'   => 'mwb_cpt_members',
					'post_title'  => $user_name,
					'post_status' => 'publish',
					'post_author' => $user_id,
					'meta_input'  => array(
						'member_actions'  => 'email_invoice',
						'member_status'   => 'pending',
						'plan_obj'        => $plan_meta,
						'billing_details' => $fields,
					),
				)
			);

			// If tnx details exist in user meta update it in members post meta and delete it from existing user data.
			// if ( ! empty( $tnx_detail ) ) {
			// 	update_post_meta( $member_id, 'members_tnx_details', $tnx_detail );
			// 	delete_user_meta( $user, 'members_tnx_details', $$tnx_detail );
			// }

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

		if ( ! function_exists( 'wp_mail' ) ) {

			return;
		}

		if ( ! empty( $member_id ) ) {

			$plan_info = get_post_meta( $member_id, 'plan_obj', true );
			$billing   = get_post_meta( $member_id, 'billing_details', true );

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

			<div class="membership_invoice_wrapper">
				<div class="invoice_info">
					<p>
						<strong><?php esc_html_e( 'Invoice no. :', 'membership-for-woccommerce' ); ?></strong><?php echo esc_html( '#INV' . $member_id ); ?></br>
						<strong><?php esc_html_e( 'Invoice date :', 'membership-for-woccommerce' ); ?></strong><?php echo esc_html( current_time( 'Y-m-d' ) ); ?>
					</p>
				</div>

				<div class="invoice_billing">
					<h3><?php esc_html_e( 'Bill to', 'membership-for-woocommerce' ); ?></h3>
					<p>
						<?php echo sprintf( ' %s %s ', esc_html( $first_name ), esc_html( $last_name ) ); ?></br>
						<?php echo esc_html( $company ); ?></br>
						<?php echo sprintf( ' %s %s ', esc_html( $address_1 ), esc_html( $address_2 ) ); ?></br>
						<?php echo sprintf( ' %s %s ', esc_html( $city ), esc_html( $postcode ) ); ?></br>
						<?php echo sprintf( ' %s, %s ', esc_html( $state ), esc_html( $country ) ); ?></br>
						<?php echo esc_html( $phone ); ?></br>
						<?php echo esc_html( $email ); ?></br>
					</p>
				</div>

				<div class="membership_inv_table_wrapper">
					<table class="membership_inv_table">
						<thead>
							<tr>
								<th class="inv_table_slno"><?php echo esc_html__( 'SNo.', 'membership-for-woocommerce' ); ?></th>
								<th class="inv_table_product"><?php echo esc_html__( 'Product', 'membership-for-woocommerce' ); ?></th>
								<th class="inv_table_total"><?php echo esc_html__( 'Amount', 'membership-for-woocommerce' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php esc_html( '1.' ); ?></td>
								<td><?php esc_html( $plan_info['post_title'] ); ?></td>
								<td><?php echo sprintf( ' %s %s ', esc_html( get_woocommerce_currency() ), esc_html( $plan_info['mwb_membership_plan_price'] ) ); ?></td>
							</tr>
						</tbody>
					</table>
					<p class="membership_inv_total">
						<strong><?php esc_html_e( 'Total : ', 'membership-for-woocommerce' ); ?></strong><?php echo sprintf( ' %s %s ', esc_html( get_woocommerce_currency() ), esc_html( $plan_info['mwb_membership_plan_price'] ) ); ?>
					</p>
				</div>
			</div>
			<footer>
				<div class="membership_inv_footer">
					<p>
						<strong><?php echo esc_html( get_bloginfo( 'name' ) ); ?></strong></br>
						<?php echo esc_html( get_bloginfo( 'description' ) ); ?>
					</p>
				</div>
			</footer>

			<?php

			$content = ob_get_clean();

			// Handling invoice creation and upload.
			$activity_class = new Membership_Activity_Helper( 'mfw-invoices', 'uploads' );
			$pdf_file       = $activity_class->create_pdf_n_upload( $content, $first_name );

			// Get the attachment file using file url.
			$attachment = $pdf_file;

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

			wp_mail(
				$to,
				$subject,
				$content,
				'',
				$attachment
			);
		}

	}
}
