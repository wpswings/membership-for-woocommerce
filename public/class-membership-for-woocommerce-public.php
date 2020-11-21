<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/public
 * @author     Make Web Better <plugins@makewebbetter.com>
 */
class Membership_For_Woocommerce_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

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

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Membership_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Membership_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/membership-for-woocommerce-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Membership_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Membership_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register Endpoint for Membership plans.
	 */
	public function mwb_membership_register_endpoint() {

		add_rewrite_endpoint( 'mwb-membership-tab', EP_PERMALINK | EP_PAGES );
		flush_rewrite_rules();
	}

	/**
	 * Adding a query variable for the Endpoint.
	 *
	 * @param array $vars An array of query variables.
	 */
	public function mwb_membership_endpoint_query_var( $vars ) {

		$vars[] = 'mwb-membership-tab';

		return $vars;
	}

	/**
	 * Inserting custom membership endpoint.
	 *
	 * @param array $items An array of all menu items on My Account page.
	 */
	public function mwb_membership_add_membership_tab( $items ) {

		$logout = $items['customer-logout'];
		unset( $items['customer-logout'] );

		// Placing the custom tab just above logout tab.
		$items['mwb-membership-tab'] = __( 'Membership Details', 'membership-for-woocommerce' );

		$items['customer-logout'] = $logout;

		return $items;
	}

	/**
	 * Membership Shortcodes for plan Action and plan Attributes.
	 */
	public function mwb_membership_shortcodes() {

		// Buy now button shortcode.
		add_shortcode( 'mwb_membership_yes', array( $this, 'buy_now_shortcode_content' ) );

		// No thanks button shortcode.
		add_shortcode( 'mwb_membership_no', array( $this, 'reject_shortcode_content' ) );

		// Membership Plan title shortcode.
		add_shortcode( 'mwb_membership_title', array( $this, 'membership_plan_title_shortcode' ) );

		// Membership Plan price shortcode.
		add_shortcode( 'mwb_membership_price', array( $this, 'membership_plan_price_shortcode' ) );

		// Membership Plan Description shortcode.
		add_shortcode( 'mwb_membership_desc', array( $this, 'membership_plan_description_shortcode' ) );

		// Membership default plan name content shortcode.
		add_shortcode( 'mwb_membership_default_plans_page', array( $this, 'mmbership_offers_default_shortcode' ) );

		// Default Gutenberg offer.
		add_shortcode( 'mwb_membership_default_page_identification', array( $this, 'default_offer_identification_shortcode' ) );
	}


	/**
	 * Restrict purchase of product to non-members.
	 *
	 * @param bool   $is_purchasable Whether the product is purchasable or not.
	 * @param object $product Product object.
	 * @return bool
	 */
	public function mwb_membership_for_woo_membership_purchasable( $is_purchasable, $product ) {

		if ( in_array( $product->id, mwb_membership_for_woo_plans_products_ids() ) || has_term( mwb_membership_for_woo_plans_cat_ids(), 'product_cat' ) ) {

			$is_purchasable = false;

		} else {

			$is_purchasable = true;
		}

		return $is_purchasable;
	}

	/**
	 * Hide price of selected product on shop page.
	 *
	 * @param string $price_html Price html.
	 * @param object $product Product object.
	 */
	public function mwb_membership_for_woo_hide_price_shop_page( $price_html, $product ) {

		if ( in_array( $product->id, mwb_membership_for_woo_plans_products_ids() ) || has_term( mwb_membership_for_woo_plans_cat_ids(), 'product_cat' ) ) {

			return '';
		}

		return $price_html;
	}

	/**
	 * Membership template for all membership products.
	 *
	 * @return void
	 */
	public function mwb_membership_product_membership_purchase_html() {

		global $product;

		if ( function_exists( 'is_product' ) && is_product() ) {

			global $post;
			global $wpdb;

			$query = "SELECT   wp_posts.* FROM wp_posts  INNER JOIN wp_postmeta ON ( wp_posts.ID = wp_postmeta.post_id ) WHERE 1=1  
					AND ( wp_postmeta.meta_key = 'mwb_membership_plan_target_ids' ) AND wp_posts.post_type = 'mwb_cpt_membership' 
					AND (wp_posts.post_status = 'publish') GROUP BY wp_posts.ID ORDER BY wp_posts.post_date DESC";

			$data = $wpdb->get_results( $query, ARRAY_A );

			if ( ! empty( $data ) && is_array( $data ) ) {

				$mwb_membership_nonce                 = wp_create_nonce( 'membership_plan' );
				$mwb_membership_default_plans_page_id = get_option( 'mwb_membership_default_plans_page', '' );

				if ( ! empty( $mwb_membership_default_plans_page_id ) && 'publish' == get_post_status( $mwb_membership_default_plans_page_id ) ) {
					$page_link = get_page_link( $mwb_membership_default_plans_page_id );
				}

				foreach ( $data as $plan ) {

					$target_ids     = get_post_meta( $plan['ID'], 'mwb_membership_plan_target_ids', true );
					$target_cat_ids = get_post_meta( $plan['ID'], 'mwb_membership_plan_target_categories', true );

					if ( ! empty( $target_ids ) && is_array( $target_ids ) ) {

						if ( in_array( $product->id, $target_ids ) ) {

							$page_link = add_query_arg(
								array(
									'plan_nonce' => $mwb_membership_nonce,
									'plan_id'    => $plan['ID'],
									'prod_id'    => $product->id,
								),
								$page_link
							);

							echo '<div style="clear: both">
									<div style="margin-top: 10px;">
										<a class="button alt" href="' . esc_html( $page_link ) . '" target="_blank" style="color:#ffffff;">' . esc_html__( 'Become a  ', 'membership-for-woocommerce' ) . esc_html( get_the_title( $plan['ID'] ) ) . esc_html__( '  member and buy this product', 'membership-for-woocommerce' ) . '</a>
									</div>
								</div>';
						}
					}

					if ( ! empty( $target_cat_ids ) && is_array( $target_cat_ids ) ) {

						if ( has_term( $target_cat_ids, 'product_cat' ) ) {

							if ( empty( $target_ids ) ) { // If target id is empty string make it an array.

								$target_ids = array();
							}

							if ( ! in_array( $product->id, $target_ids ) ) { // checking if the product does not exist in target id of a plan.

								$page_link = add_query_arg(
									array(
										'plan_nonce' => $mwb_membership_nonce,
										'plan_id'    => $plan['ID'],
										'prod_id'    => $product->id,
									),
									$page_link
								);

								echo '<div style="clear: both">
										<div style="margin-top: 10px;">
											<a class="button alt" href="' . esc_html( $page_link ) . '" target="_blank" style="color:#ffffff;">' . esc_html__( 'Become a  ', 'membership-for-woocommerce' ) . esc_html( get_the_title( $plan['ID'] ) ) . esc_html__( '  member and buy this product', 'membership-for-woocommerce' ) . '</a>
										</div>
									</div>';
							}
						}
					}
				}
			}
		}

	}

	/**
	 * Display membership tag on products which are offered in any membership on shop page.
	 */
	public function mwb_membership_products_on_shop_page() {

		global $product;

		if ( in_array( $product->id, mwb_membership_for_woo_plans_products_ids() ) || has_term( mwb_membership_for_woo_plans_cat_ids(), 'product_cat' ) ) {

			echo '<div class="product-meta>"
					<span><b>' . esc_html__( 'Membership Product', 'membership-for-woocommerce' ) . '</b></span>
				</div>';

		}

		global $post;
		global $wpdb;

		$query = "SELECT   wp_posts.* FROM wp_posts  INNER JOIN wp_postmeta ON ( wp_posts.ID = wp_postmeta.post_id ) WHERE 1=1  
				AND ( wp_postmeta.meta_key = 'mwb_membership_plan_target_ids' ) AND wp_posts.post_type = 'mwb_cpt_membership' 
				AND (wp_posts.post_status = 'publish') GROUP BY wp_posts.ID ORDER BY wp_posts.post_date DESC";

		$data = $wpdb->get_results( $query, ARRAY_A );

		if ( ! empty( $data ) && is_array( $data ) ) {

			$output = '';

			foreach ( $data as $plan ) {

				$target_ids     = get_post_meta( $plan['ID'], 'mwb_membership_plan_target_ids', true );
				$target_cat_ids = get_post_meta( $plan['ID'], 'mwb_membership_plan_target_categories', true );

				if ( ! empty( $target_ids ) && is_array( $target_ids ) ) {

					if ( in_array( $product->id, $target_ids ) ) {

						$output .= esc_html( get_the_title( $plan['ID'] ) ) . ' | ';
					}
				}

				if ( ! empty( $target_cat_ids ) && is_array( $target_cat_ids ) ) {

					if ( has_term( $target_cat_ids, 'product_cat' ) ) {

						if ( empty( $target_ids ) ) { // If target id is empty string make it an array.

							$target_ids = array();
						}

						if ( ! in_array( $product->id, $target_ids ) ) { // checking if the product does not exist in target id of a plan.

							$output .= esc_html( get_the_title( $plan['ID'] ) ) . ' | ';

						}
					}
				}
			}
			$output = substr( $output, 0, -2 );
			echo $output;
		}

	}

	/**
	 * Shortcode for offer - Buy now button.
	 * Returns : Link :
	 */
	// public function buy_now_shortcode_content( $atts, $content ) {
		
	// }

	/**
	 * Default plan page shortcode.
	 * Returns : html :
	 *
	 * @since 1.0.0
	 */
	public function mmbership_offers_default_shortcode() {

		$output = '';

		if ( isset( $_GET['plan_id'] ) && isset( $_GET['prod_id'] ) ) {

			$plan_id = sanitize_text_field( wp_unslash( $_GET['plan_id'] ) );
			$prod_id = sanitize_text_field( wp_unslash( $_GET['prod_id'] ) );

			if ( isset( $_GET['plan_nonce'] ) ) {

				$plan_nonce = sanitize_text_field( wp_unslash( $_GET['plan_nonce'] ) );

				wp_verify_nonce( $plan_nonce, 'membership_plan' );

				// Get plan details.
				$plan_title    = get_the_title( $plan_id );
				$plan_price    = get_post_meta( $plan_id, 'mwb_membership_plan_price', true );
				$plan_currency = get_woocommerce_currency();
				$plan_desc     = get_post_field( 'post_content', $plan_id );

				// Plans default text.
				$offer_banner_text  = esc_html__( 'One Membership, Many Benefits', 'membership-for-woocommerce' );
				$offer_buy_now_txt  = esc_html__( 'Buy Now!', 'membership-for-woocommerce' );
				$offer_no_thnks_txt = esc_html__( 'No thanks!', 'membership-for-woocommerce' );

				// Button colours.
				$buynow_btn_color   = '#83e620';
				$nothanks_btn_color = '#ed2913';

				$output .= '<div class="mwb_membership_plan_banner">
								<h2><b><i>' . trim( $offer_banner_text ) . '</i></b></h2>
							</div>';

				$output .= '<div class="mwb_membership_plan_offer_wrapper">';

				$output .= '<div class="mwb_membership_plan_content_title">
								<h3>' . $plan_title . '</h3>			
							</div>';

				$output .= '<div class="mwb_membership_plan_content_price">
								<h2>' . $plan_currency . ' ' . $plan_price . '</h2>			
							</div>';

				$output .= '<div class="mwb_membership_plan_content_desc">
								<h2>' . $plan_desc . '</h2>			
							</div>';

				$output .= '</div>';

				$output .= '<div class="mwb_membership_offer_action">
								<form class="mwb_membership_offer_form" method="post">
									<input type="hidden" name="membership_nonce" value="' . $plan_nonce . '">
									<input type="hidden" name="membership_id" value="' . $plan_id . '">
									<button data-id="' . $plan_id . '" style="background-color:' . $buynow_btn_color . '" class="mwb_membership_buy_now" type="submit" name="mwb_membership_buy_now">' . $offer_buy_now_txt . '</button>
								</form>
								<a style="color:' . $nothanks_btn_color . '" class="mwb_membership_no_thanks" href="' . get_permalink( $prod_id ) . '">' . $offer_no_thnks_txt . '</a>';

				$output .= '</div>';

			} else {

				$error_msg = esc_html__( 'You ran out of session.', 'membership-for-woocommerce' );

				$link_text = esc_html__( 'Go back to Shop page.', 'membership-for-woocommerce' );

				$error_msg = apply_filters( 'mwb_membership_error_message', $error_msg );

				$link_text = apply_filters( 'mwb_membership_go_back_link_text', $link_text );

				$shop_page_url = wc_get_page_permalink( 'shop' );

				$output .= $error_msg . '<a href="' . $shop_page_url . '" class="button">' . $link_text . '</a>';
			}
		} else {

			$error_msg = esc_html__( 'You ran out of session.', 'membership-for-woocommerce' );

			$link_text = esc_html__( 'Go back to Shop page.', 'membership-for-woocommerce' );

			$error_msg = apply_filters( 'mwb_membership_error_message', $error_msg );

			$link_text = apply_filters( 'mwb_membership_go_back_link_text', $link_text );

			$shop_page_url = wc_get_page_permalink( 'shop' );

			$output .= $error_msg . '<a href="' . $shop_page_url . '" class="button">' . $link_text . '</a>';

		}

		if ( ! isset( $_GET['plan_nonce'] ) || ! isset( $_GET['plan_id'] ) ) {
			$mwb_membership_no_offer_text = esc_html__( 'Sorry, you have no offers', 'membership-for-woocommerce' );

			$output .= '<div class="mwb_membership_no_offer"><h2>' . trim( $mwb_membership_no_offer_text, '"' ) . '</h2>';

			$output .= '<a class="button wc-backward" href="' . esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ) . '">' . esc_html__( 'Return to Shop', 'membership-for-woocommerce' ) . '</a></div>';

		}

		return $output;
	}

	/**
	 * Shortcode for Default Gutenberg offer identification.
	 * Returns : empty string.
	 *
	 * @since 1.0.0
	 */
	public function default_offer_identification_shortcode() {

		return '';

	}

}
