<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wpswings.com/
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
 * namespace membership_for_woocommerce_public.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/public
 */
class Membership_For_Woocommerce_Public {

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
	 * Under review membership products.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $under_review_products;

	/**
	 * Another plan membership products.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $another_plan_products;

	/**
	 * Exclude other plan membership products.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $exclude_other_plan_products;

	/**
	 * Query data handler.
	 *
	 * @since 1.0.0
	 * @var  array
	 */
	public $custom_query_data;

	/**
	 * Undocumented variable.
	 *
	 * @var object
	 */
	protected $global_class;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name           = $plugin_name;
		$this->version               = $version;
		$this->under_review_products = $this->under_review_products ? $this->under_review_products : array();
		$this->another_plan_products = $this->another_plan_products ? $this->another_plan_products : array();
		$this->exclude_other_plan_products = $this->exclude_other_plan_products ? $this->exclude_other_plan_products : array();

		$this->global_class = Membership_For_Woocommerce_Global_Functions::get();

		$this->custom_query_handler();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function mfw_public_enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'public/css/membership-for-woocommerce-public.css', array(), MEMBERSHIP_FOR_WOOCOMMERCE_VERSION, 'all' );
		wp_enqueue_style( 'public-css', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'public/css/wps-public.css', array(), MEMBERSHIP_FOR_WOOCOMMERCE_VERSION, 'all' );

		wp_enqueue_style( 'wp-jquery-ui-dialog' );

		if ( is_page( 'membership-plans' ) ) {

			wp_enqueue_style( 'membership-plan', plugin_dir_url( __FILE__ ) . 'css/membership-plan.css', array(), MEMBERSHIP_FOR_WOOCOMMERCE_VERSION, false );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function mfw_public_enqueue_scripts() {

		wp_register_script( $this->plugin_name, MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'public/js/membership-for-woocommerce-public.js', array( 'jquery' ), MEMBERSHIP_FOR_WOOCOMMERCE_VERSION, false );
		wp_localize_script( $this->plugin_name, 'mfw_public_param', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( $this->plugin_name );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-public.js', array( 'jquery' ), MEMBERSHIP_FOR_WOOCOMMERCE_VERSION, false );
		$button_text = get_option( 'wps_membership_change_buy_now_text', '' );
		$wps_mfw_single_plan = '';
		if ( isset( $_GET['plan_id'] ) && isset( $_GET['prod_id'] ) ) {
			$wps_mfw_single_plan = 'yes';
		} else {
			$wps_mfw_single_plan = '';
		}
		wp_localize_script(
			$this->plugin_name,
			'membership_public_obj',
			array(
				'ajaxurl'            => admin_url( 'admin-ajax.php' ),
				'nonce'              => wp_create_nonce( 'auth_adv_nonce' ),
				'buy_now_text'       => $button_text,
				'single_plan'        => $wps_mfw_single_plan,
				'plan_page_template' => get_option( 'wps_membership_plan_page_temp' ),
				'dark_mode'          => get_option( 'wps_membership_plan_page_dark_mode' ),
				'enable_new_layout'  => get_option( 'wps_msfw_enable_new_layout_settings' ),
				'new_layout_color'   => empty( get_option( 'wps_msfw_new_layout_color' ) ) ? 'ff7700' : get_option( 'wps_msfw_new_layout_color' ),
			)
		);

		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'sweet_alert', plugin_dir_url( __FILE__ ) . 'js/sweet-alert2.js', array( 'jquery' ), MEMBERSHIP_FOR_WOOCOMMERCE_VERSION, false );

		if ( is_page( 'membership-plans' ) ) {

			wp_enqueue_script( 'paypal-smart-buttons', plugin_dir_url( __FILE__ ) . 'js/membership-paypal-smart-buttons.js', array( 'jquery' ), MEMBERSHIP_FOR_WOOCOMMERCE_VERSION, false );

			$settings = '';

			$client_id    = ! empty( $settings['client_id'] ) ? $settings['client_id'] : 'sb';
			$currency     = ! empty( $settings['currency_code'] ) ? $settings['currency_code'] : '';
			$intent       = ! empty( $settings['payment_action'] ) ? $settings['payment_action'] : '';
			$component    = ! empty( $settings['component'] ) ? $settings['component'] : 'buttons';
			$disable_fund = ! empty( $settings['disable_funding'] ) ? $settings['disable_funding'] : '';
			$vault        = ! empty( $settings['vault'] ) ? 'true' : 'false';
			$debug        = ! empty( $settings['debug'] ) ? 'true' : 'false';

			$plan_data = array();
			$plan_id   = ! empty( $_GET['plan_id'] ) ? sanitize_text_field( wp_unslash( $_GET['plan_id'] ) ) : '';

			$plan_name  = ! empty( get_the_title( $plan_id ) ) ? get_the_title( $plan_id ) : '';
			$plan_desc  = ! empty( get_post_field( 'post_content', $plan_id ) ) ? get_post_field( 'post_content', $plan_id ) : '';
			$plan_price = ! empty( wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_price', true ) ) ? wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_price', true ) : '';

			$plan_data['name']  = $plan_name;
			$plan_data['desc']  = $plan_desc;
			$plan_data['price'] = $plan_price;

			wp_enqueue_script( 'paypal-sdk', 'https://www.paypal.com/sdk/js?client-id=' . esc_html( $client_id ) . '&currency=' . esc_html( $currency ) . '&intent=' . esc_html( $intent ) . '&components=' . esc_html( $component ) . '&disable-funding=' . esc_html( $disable_fund ) . '&vault=' . esc_html( $vault ) . '&debug=' . esc_html( $debug ), array( 'jquery' ), MEMBERSHIP_FOR_WOOCOMMERCE_VERSION, false );

			wp_localize_script(
				'paypal-smart-buttons',
				'paypal_sb_obj',
				array(
					'ajax_url'  => admin_url( 'admin-ajax.php' ),
					'settings'  => $settings,
					'plan_data' => $plan_data,
					'nonce'     => wp_create_nonce( 'paypal-nonce' ),
				)
			);
		}
	}

	/**
	 * Custom query handler.
	 */
	protected function custom_query_handler() {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$results = get_posts(
			array(
				'post_type' => 'wps_cpt_membership',
				'post_status' => 'publish',
				'meta_key' => 'wps_membership_plan_target_ids',
				'numberposts' => -1,

			)
		);

		$final_results = array();

		foreach ( $results as $key => $value ) {
			foreach ( $value as $key1 => $value1 ) {
				$final_results[ $key ][ $key1 ] = $value1;
			}
		}
		$this->custom_query_data = $final_results;
	}

	/**
	 * Register Endpoint for Membership plans.
	 */
	public function wps_membership_register_endpoint() {

		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		add_rewrite_endpoint( 'wps-membership-tab', EP_PERMALINK | EP_PAGES );
		flush_rewrite_rules();
	}

	/**
	 * Adding a query variable for the Endpoint.
	 *
	 * @param array $vars An array of query variables.
	 */
	public function wps_membership_endpoint_query_var( $vars ) {

		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return $vars;
		}

		$vars[] = 'wps-membership-tab';

		/**
		 * Filter for endpoints.
		 *
		 * @since 1.0.0
		 */
		$vars = apply_filters( 'wps_membership_endpoint_query_var', $vars );
		return $vars;
	}

	/**
	 * Inserting custom membership endpoint.
	 *
	 * @param array $items An array of all menu items on My Account page.
	 */
	public function wps_membership_add_membership_tab( $items ) {

		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return $items;
		}
		// Getting global options.
		$wps_membership_global_settings = get_option( 'wps_membership_global_options', $this->global_class->default_global_options() );

		if ( ! empty( $wps_membership_global_settings ) ) {

			if ( ! empty( $wps_membership_global_settings['wps_membership_plan_user_history'] ) && 'on' == $wps_membership_global_settings['wps_membership_plan_user_history'] ) {

				$logout = $items['customer-logout'];
				unset( $items['customer-logout'] );

				// Placing the custom tab just above logout tab.
				$items['wps-membership-tab'] = esc_html__( 'Membership Details', 'membership-for-woocommerce' );

				$items['customer-logout'] = $logout;
			}
		}

		/**
		 * Filter for membership tab.
		 *
		 * @since 1.0.0
		 */
		$items = apply_filters( 'wps_membership_add_membership_tab', $items );
		return $items;
	}

	/**
	 * Add title to Membership details tab.
	 *
	 * @param string $title stores the title of the endpoint.
	 * @return string
	 */
	public function wps_membership_tab_title( $title ) {
		global $wp_query;
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return $title;
		}
		$endpoint = isset( $wp_query->query_vars['wps-membership-tab'] );

		if ( $endpoint && ! is_admin() && in_the_loop() && is_account_page() ) {

			$title = __( 'Membership Details', 'membership-for-woocommerce' );
		}

		/**
		 * Filter membership title.
		 *
		 * @since 1.0.0
		 */
		$title = apply_filters( 'wps_membership_tab_title', $title );
		return $title;
	}

	/**
	 * Add content to Membership details tab.
	 */
	public function wps_membership_populate_tab() {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$user       = get_current_user_id();
		$memerships = get_user_meta( $user, 'mfw_membership_id', true );
		$instance   = $this->global_class;

		wc_get_template(
			'public/partials/templates/wps-membership-details-tab.php',
			array(
				'user_id'        => $user,
				'membership_ids' => $memerships,
				'instance'       => $instance,
			),
			'',
			MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH
		);
	}

	/**
	 * Membership Shortcodes for plan Action and plan Attributes.
	 */
	public function wps_membership_shortcodes() {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		// Buy now button shortcode.
		add_shortcode( 'wps_membership_buy_now', array( $this, 'buy_now_shortcode_yes' ) );
		add_shortcode( 'wps_membership_buy', array( $this, 'buy_now_shortcode_content' ) );

		// No thanks button shortcode.
		add_shortcode( 'wps_membership_no', array( $this, 'reject_shortcode_content' ) );

		// No thanks button shortcode.
		add_shortcode( 'wps_membership_plan_details', array( $this, 'wps_membership_plan_details' ) );

		// Membership Plan title shortcode.
		add_shortcode( 'wps_membership_title', array( $this, 'membership_plan_title_all_plan' ) );

		// Membership Plan title shortcode.
		add_shortcode( 'wps_membership_title_name', array( $this, 'membership_plan_title_shortcode' ) );

		// Membership Plan price shortcode.
		add_shortcode( 'wps_membership_price', array( $this, 'membership_plan_price_shortcode' ) );

		// Membership Plan Description shortcode.
		add_shortcode( 'wps_membership_desc', array( $this, 'membership_plan_desc_shortcode_all_plan' ) );

		// Membership Plan Description shortcode.
		add_shortcode( 'wps_membership_desc_data', array( $this, 'membership_plan_desc_shortcode' ) );

		// Membership default plan name content shortcode.
		add_shortcode( 'wps_membership_default_plans_page', array( $this, 'membership_offers_default_shortcode' ) );

		// Default Gutenberg offer.
		add_shortcode( 'wps_membership_default_page_identification', array( $this, 'default_offer_identification_shortcode' ) );
		add_shortcode( 'wps_membership_registration_form', array( $this, 'wps_membership_registration_form_shortcode' ) );
	}

	/**
	 * Membership default global options.
	 *
	 * @since 1.0.0
	 */
	public function default_global_options() {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$default_global_settings = array(

			'wps_membership_enable_plugin'     => 'on',
			'wps_membership_delete_data'       => 'off',
			'wps_membership_plan_user_history' => 'on',
			'wps_membership_email_subject'     => 'Thank you for Shopping, Do not reply.',
			'wps_membership_email_content'     => '',
			'wps_membership_attach_invoice'    => 'off',
			'wps_membership_invoice_address'   => '',
			'wps_membership_invoice_phone'     => '',
			'wps_membership_invoice_email'     => '',
			'wps_membership_invoice_logo'      => '',

		);
		return $default_global_settings;
	}

	/**
	 * Restrict purchase of product to non-members.
	 *
	 * @param bool   $is_purchasable Whether the product is purchasable or not.
	 * @param object $product Product object.
	 * @return bool
	 */
	public function wps_membership_for_woo_membership_purchasable( $is_purchasable, $product ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return $is_purchasable;
		}
		if ( is_admin() ) {

			return $is_purchasable;
		}

		$wps_membership_default_product = get_option( 'wps_membership_default_product', '' );

		$membership_product = wc_get_product( $wps_membership_default_product );

		if ( $membership_product ) {

			if ( $wps_membership_default_product == $product->get_id() ) {
				$is_purchasable = true;
				return $is_purchasable;
			}
		}
		$exclude = wps_membership_get_meta_data( $product->get_id(), '_wps_membership_exclude', true );

		if ( 'yes' === $exclude ) {
			$is_purchasable = false;
			return $is_purchasable;
		}

		$user = wp_get_current_user();
		$is_member_meta = get_user_meta( $user->ID, 'is_member' );
		if ( $this->global_class->plans_exist_check() == true ) {

			$is_membership_product = $this->wps_membership_products_on_shop_page( true, $product );

			// Determine access if is a membership product.
			if ( true == $is_membership_product ) {

				// Not a member.
				if ( ! is_user_logged_in() && ! in_array( 'member', (array) $is_member_meta ) ) {

					// If non logged in or not a member.
					if ( in_array( $product->get_id(), $this->global_class->plans_products_ids() ) || has_term( $this->global_class->plans_cat_ids(), 'product_cat' ) || has_term( $this->global_class->plans_tag_ids(), 'product_tag' ) ) {

						$is_purchasable = false;
					}
				} else {

					// Check if current product is accessible by any activated membership id.
					if ( true == $this->is_accessible_to_member( $product ) ) {

						$is_purchasable = true;

						if ( ! empty( $this->under_review_products ) && in_array( $product->get_id(), $this->under_review_products ) ) {

							$is_purchasable = false;
						}
					} else {
						if ( $this->global_class->plans_exist_check() == true ) {
							$is_purchasable = true;
						} else {
							$is_purchasable = false;
						}
					}
				}
			} else {

				$is_purchasable = true;
			}
		}

		/**
		 * Filter for tab.
		 *
		 * @since 1.0.0
		 */
		$is_purchasable = apply_filters( 'wps_membership_tab_is_purchasable', $is_purchasable );
		return $is_purchasable;
	}

	/**
	 * Hide price of selected product on shop page.
	 *
	 * @param string $price_html Price html.
	 * @param object $product Product object.
	 */
	public function wps_membership_for_woo_hide_price_shop_page( $price_html, $product ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return $price_html;
		}
		$user = wp_get_current_user();
		$is_member_meta = get_user_meta( $user->ID, 'is_member' );
		if ( $this->global_class->plans_exist_check() == true ) {

			if ( ! is_user_logged_in() && ! in_array( 'member', (array) $is_member_meta ) ) {

				if ( in_array( $product->get_id(), $this->global_class->plans_products_ids() ) && has_term( $this->global_class->plans_cat_ids(), 'product_cat' ) || has_term( $this->global_class->plans_tag_ids(), 'product_tag' ) ) {

					return '';
				} else {
					return $price_html;
				}
			}
		}

		/**
		 * Return price html.
		 *
		 * @since 1.0.0
		 */
		$price_html = apply_filters( 'wps_membership_tab_price_html', $price_html );
		return $price_html;
	}

	/**
	 * Membership template for all membership products.
	 *
	 * @return void
	 */
	public function wps_membership_product_membership_purchase_html() {

		global $product;
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$user = wp_get_current_user();
		$is_member_meta = get_user_meta( $user->ID, 'is_member' );
		$already_included_plan = array();
		$already_pending_plan = array();
		$suggested_membership = false;
		$count = 0;
		$is_pending = 'not pending';
		$page_link = '';
		$disable_required = false;
		$is_membership_product = $this->wps_membership_products_on_shop_page( true, $product );

		if ( ! $product->is_purchasable() && $this->global_class->plans_exist_check() == true ) {

			if ( function_exists( 'is_product' ) && is_product() ) {

				$data = $this->custom_query_data;

				if ( ! empty( $data ) && is_array( $data ) ) {

					$wps_membership_default_plans_page_id = get_option( 'wps_membership_default_plans_page', '' );

					if ( ! empty( $wps_membership_default_plans_page_id ) && 'publish' == get_post_status( $wps_membership_default_plans_page_id ) ) {
						$page_link = get_page_link( $wps_membership_default_plans_page_id );
					}

					foreach ( $data as $plan ) {

						$page_link_found = false;
						$target_ids      = ! empty( wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_ids', true ) ) ? wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_ids', true ) : array();
						$target_cat_ids  = ! empty( wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_categories', true ) ) ? wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_categories', true ) : array();
						$target_tag_ids  = ! empty( wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_tags', true ) ) ? wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_tags', true ) : array();

						if ( $target_cat_ids ) {

							$target_ids = array_merge( $target_ids, $target_cat_ids );
						}

						$product_terms = $this->get_product_terms( $product->get_id() );

						if ( ! empty( $product_terms ) ) {
							foreach ( $product_terms as $product_terms_key => $product_terms_value ) {
								if ( in_array( $product_terms_value, (array) $target_tag_ids ) ) {
									array_push( $target_ids, $product->get_id() );
								}
							}
						}

						if ( ! empty( $target_ids ) && is_array( $target_ids ) ) {
							$target_ids = array_unique( $target_ids );
						}

						if ( ! empty( $target_ids ) && is_array( $target_ids ) ) {

							if ( in_array( $product->get_id(), $target_ids ) ) {

								foreach ( $target_ids as $ids ) {
									$exclude = wps_membership_get_meta_data( $ids, '_wps_membership_exclude', true );

									if ( 'yes' === $exclude ) {
										return;
									}
								}

								$page_link_found = true;

								$page_link = add_query_arg(
									array(
										'plan_id' => $plan['ID'],
										'prod_id' => $product->get_id(),
									),
									$page_link
								);
								// Show plans under review.

								if ( ! empty( $this->under_review_products ) && in_array( $product->get_id(), $this->under_review_products ) ) {
									$is_pending = 'not pending';
									$user_id = get_current_user_id();

									$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );

									if ( ! empty( $current_memberships ) && is_array( $current_memberships ) ) {

										foreach ( $current_memberships as $key => $membership_id ) {

											$member_status = wps_membership_get_meta_data( $membership_id, 'member_status', true );

											if ( ! empty( $member_status ) && 'complete' != $member_status && 'expired' != $member_status ) {

												$active_plan = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );

												if ( ! empty( $active_plan['ID'] ) && $active_plan['ID'] == $plan['ID'] ) {
													$is_pending = 'pending';
													if ( ! in_array( $active_plan['ID'], $already_pending_plan ) ) {
														array_push( $already_pending_plan, $active_plan['ID'] );

														?>
														<div class="product-meta product-meta-review">
															<span><b><?php esc_html_e( 'Membership Under Review', 'membership-for-woocommerce' ); ?></b></span>
														</div>
															<?php
													}
												}
											}
										}
									}
								}

								array_push( $already_included_plan, $plan['ID'] );

								if ( 'not pending' === $is_pending ) {

									if ( true === $suggested_membership ) {

										++$count;
										if ( true === $is_membership_product ) {
											$user = wp_get_current_user();

											if ( is_user_logged_in() && in_array( 'member', (array) $is_member_meta ) ) {

												echo '<div class="wps-mfwp__available--title">Other Available Membership</div>';
												$suggested_membership = true;
											}
										}

										echo '<div class="available_member wps_mfw_plan_suggestion" >
												<div>
													<a class="button alt ' . esc_html( $disable_required ) . ' mfw-membership" href="' . esc_url( $page_link ) . '" target="_blank" >' . esc_html__( 'Membership :- ', 'membership-for-woocommerce' ) . esc_html( get_the_title( $plan['ID'] ) ) . '</a>
												</div>
											</div>';
									} else {
										// Show options to buy plans.
										echo '<div class="plan_suggestion wps_mfw_plan_suggestion" >
											<div>
												<a class="button alt ' . esc_html( $disable_required ) . ' mfw-membership" href="' . esc_url( $page_link ) . '" target="_blank" >' . esc_html__( 'Become a  ', 'membership-for-woocommerce' ) . esc_html( get_the_title( $plan['ID'] ) ) . esc_html__( '  member and buy this product', 'membership-for-woocommerce' ) . '</a>
											</div>
										</div>';
									}
								}
							}
						}

						if ( false == $page_link_found && ( ! empty( $target_cat_ids ) && is_array( $target_cat_ids ) ) || ! empty( $target_tag_ids ) && is_array( $target_tag_ids ) ) {

							$is_has_target_id = array();
							$product_terms = $this->get_product_terms( get_the_ID() );

							if ( ! empty( $product_terms ) ) {
								foreach ( $product_terms as $product_terms_key => $product_terms_value ) {
									if ( in_array( $product_terms_value, (array) $target_tag_ids ) ) {
										array_push( $is_has_target_id, $product->get_id() );
									}
								}
							}

							if ( has_term( $target_cat_ids, 'product_cat' ) || ! empty( $is_has_target_id ) ) {

								if ( empty( $target_ids ) ) { // If target id is empty string make it an array.

									$target_ids = array();
								}

								if ( ! in_array( $product->get_id(), $target_ids ) ) { // checking if the product does not exist in target id of a plan.

									$page_link = add_query_arg(
										array(
											'plan_id' => $plan['ID'],
											'prod_id' => $product->get_id(),
										),
										$page_link
									);

										$disable_required = false;
										// Show plans under review.
									if ( ! empty( $this->under_review_products ) && in_array( $product->get_id(), $this->under_review_products ) ) {

										$user_id = get_current_user_id();

										$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );

										if ( ! empty( $current_memberships ) && is_array( $current_memberships ) ) {

											foreach ( $current_memberships as $key => $membership_id ) {

												$member_status = wps_membership_get_meta_data( $membership_id, 'member_status', true );

												if ( ! empty( $member_status ) && 'complete' != $member_status ) {

													$active_plan = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );

													if ( ! empty( $active_plan['ID'] ) && $active_plan['ID'] == $plan['ID'] ) {
														$is_pending = 'pending';
														$disable_required = 'disable_required';
														?>
														<div class="product-meta product-meta-review">
															<span><b><?php esc_html_e( 'Membership Under Review', 'membership-for-woocommerce' ); ?></b></span>
														</div>
														<?php
													}
												}
											}
										}
									}

									array_push( $already_included_plan, $plan['ID'] );
									// Show options to buy plans.
									if ( 'not pending' === $is_pending ) {

										if ( true === $suggested_membership ) {

											++$count;
											if ( true === $is_membership_product ) {
												$user = wp_get_current_user();

												if ( is_user_logged_in() && in_array( 'member', (array) $is_member_meta ) ) {

													echo '<div class="wps-mfwp__available--title">Other Available Membership</div>';
													$suggested_membership = true;
												}
											}

											echo '<div class="available_member wps_mfw_plan_suggestion">
													<div>
														<a class="button alt ' . esc_html( $disable_required ) . ' mfw-membership" href="' . esc_url( $page_link ) . '" target="_blank" >' . esc_html__( 'Membership :- ', 'membership-for-woocommerce' ) . esc_html( get_the_title( $plan['ID'] ) ) . '</a>
													</div>
												</div>';
										} else {
											// Show options to buy plans.
											echo '<div class="plan_suggestion wps_mfw_plan_suggestion" >
												<div>
													<a class="button alt ' . esc_html( $disable_required ) . ' mfw-membership" href="' . esc_url( $page_link ) . '" target="_blank" >' . esc_html__( 'Become a  ', 'membership-for-woocommerce' ) . esc_html( get_the_title( $plan['ID'] ) ) . esc_html__( '  member and buy this product', 'membership-for-woocommerce' ) . '</a>
												</div>
											</div>';
										}
									}
								}
							}
						}
					}
				}
			}
		}

		$is_membership_product = $this->wps_membership_products_on_shop_page( true, $product );
		if ( true === $is_membership_product ) {

			if ( is_user_logged_in() && in_array( 'member', (array) $is_member_meta ) ) {
				$data                = $this->custom_query_data;
				$user_id             = get_current_user_id();
				$existing_plan_id    = array();
				$plan_existing       = false;
				$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );

				if ( ! empty( $current_memberships ) && is_array( $current_memberships ) ) {

					foreach ( $current_memberships as $key => $membership_id ) {

						$member_status = wps_membership_get_meta_data( $membership_id, 'member_status', true );
						if ( 'pending' == $member_status || 'on-hold' == $member_status ) {

							$active_plan = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
							$club_membership = $this->get_all_included_membership( $active_plan['ID'] );
							if ( ! empty( $club_membership ) ) {
								$existing_plan_id = array_merge( $existing_plan_id, $club_membership );
							}
							if ( ! empty( $active_plan['ID'] ) ) {
								array_push( $existing_plan_id, $active_plan['ID'] );
							}

								break;
						}
						if ( ! empty( $member_status ) && 'complete' == $member_status ) {

							$active_plan = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
							if ( empty( $active_plan ) ) {
								continue;
							}

							$club_membership = $this->get_all_included_membership( $active_plan['ID'] );
							if ( ! empty( $club_membership ) ) {
								$existing_plan_id = array_merge( $existing_plan_id, $club_membership );
							}
							array_push( $existing_plan_id, $active_plan['ID'] );
						}
					}

					if ( false == $plan_existing ) {

						foreach ( $data as $plan ) {
							$wps_membership_default_plans_page_id = get_option( 'wps_membership_default_plans_page', '' );

							if ( ! empty( $wps_membership_default_plans_page_id ) && 'publish' == get_post_status( $wps_membership_default_plans_page_id ) ) {
								$page_link = get_page_link( $wps_membership_default_plans_page_id );
							}

							if ( ! in_array( $plan['ID'], $existing_plan_id ) ) {

								if ( ! in_array( $plan['ID'], $already_included_plan ) ) {
									$page_link_found = false;
									$target_ids      = ! empty( wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_ids', true ) ) ? wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_ids', true ) : array();
									$target_cat_ids  = ! empty( wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_categories', true ) ) ? wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_categories', true ) : array();
									$target_tag_ids  = ! empty( wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_tags', true ) ) ? wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_tags', true ) : array();

									if ( $target_cat_ids ) {
										$target_ids = array_merge( $target_ids, $target_cat_ids );
									}
									$product_terms = $this->get_product_terms( $product->get_id() );
									if ( ! empty( $product_terms ) ) {
										foreach ( $product_terms as $product_terms_key => $product_terms_value ) {
											if ( in_array( $product_terms_value, (array) $target_tag_ids ) ) {
												array_push( $target_ids, $product->get_id() );
											}
										}
									}

									if ( ! empty( $target_ids ) && is_array( $target_ids ) ) {
										$target_ids = array_unique( $target_ids );
									}

									if ( ! empty( $target_ids ) && is_array( $target_ids ) ) {

										if ( in_array( $product->get_id(), $target_ids ) ) {

											foreach ( $target_ids as $ids ) {
												$exclude = wps_membership_get_meta_data( $ids, '_wps_membership_exclude', true );

												if ( 'yes' === $exclude ) {
													return;
												}
											}
											$page_link_found = true;

											$page_link = add_query_arg(
												array(),
												$page_link
											);
										}
										// Show options to buy plans.
										if ( 0 == $count ) {
											if ( true === $is_membership_product ) {
												$user = wp_get_current_user();
												$is_member_meta = get_user_meta( $user->ID, 'is_member' );

												if ( is_user_logged_in() && in_array( 'member', (array) $is_member_meta ) ) {

													echo '<div class="wps-mfwp__available--title">Other Available Membership</div>';
													$suggested_membership = true;
												}
											}
										}
										++$count;

										$page_link = $page_link . '?plan_id=' . $plan['ID'] . '&prod_id=' . $product->get_id();
										echo '<div class="available_member wps_mfw_plan_suggestion" >
												<div>
													<a class="button alt ' . esc_html( $disable_required ) . ' mfw-membership" href="' . esc_url( $page_link ) . '" target="_blank" >' . esc_html__( 'Memberships	 :- ', 'membership-for-woocommerce' ) . esc_html( get_the_title( $plan['ID'] ) ) . '</a>
												</div>
											</div>';
									}
								}
							}
						}
					}
				}
			}
		}

	}

	/**
	 * Common function to get terms realted to product.
	 *
	 * @param [type] $product_id is the id of the current product.
	 * @return array
	 */
	public function get_product_terms( $product_id ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$term_related_to_product = array();
		$terms = wp_get_post_terms( $product_id, 'product_tag' );

		if ( count( $terms ) > 0 ) {
			foreach ( $terms as $term ) {
				$term_id = $term->term_id; // Product tag Id.
				array_push( $term_related_to_product, $term_id );
			}
			// Set the product tag names in an array.
		}

		$term_related_to_product = ! empty( $term_related_to_product ) ? $term_related_to_product : array();
		return $term_related_to_product;
	}

	/**
	 * Display membership tag on products which are offered in any membership on shop page.
	 *
	 * @param bool   $return_status Returns current products purchaseable status.
	 * @param object $_product Product object.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_products_on_shop_page( $return_status = false, $_product = false ) {

		global $product;
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		if ( empty( $product ) ) {

			$product = $_product;
		}
		if ( object == gettype( $product ) ) {
			$product_id = $product->get_id();
		} else {
			$product_id = get_the_ID();
		}
		$product_id         = $product_id;
		$is_product_exclude = false;

		if ( $this->global_class->plans_exist_check() == true ) {

			$data = $this->custom_query_data;

			if ( ! empty( $data ) && is_array( $data ) ) {

				$output = '';

				foreach ( $data as $plan ) {

					$exclude_product = array();

					/**
					 * Filter for exclude product.
					 *
					 * @since 1.0.0
					 */
					$exclude_product = apply_filters( 'wps_membership_exclude_product', $exclude_product, $product_id );

					/**
					 * Filter for exclude products.
					 *
					 * @since 1.0.0
					 */
					$is_product_exclude = apply_filters( 'wps_membership_is_exclude_product', $exclude_product, $data, $is_product_exclude );

					if ( $is_product_exclude ) {
						break;
					}

					if ( in_array( $plan['ID'], $exclude_product ) && ! empty( $exclude_product ) ) {
						break;
					}

					$target_ids     = wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_ids', true );
					$target_cat_ids = wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_categories', true );
					$target_tag_ids  = wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_tags', true );

					if ( ! empty( $target_ids ) && is_array( $target_ids ) ) {

						if ( in_array( get_the_ID(), $target_ids ) ) {

							$output .= esc_html( get_the_title( $plan['ID'] ) ) . ' | ';
						}
					}

					if ( ( ! empty( $target_cat_ids ) && is_array( $target_cat_ids ) ) || ( ! empty( $target_tag_ids ) && is_array( $target_tag_ids ) ) ) {

						if ( has_term( $target_cat_ids, 'product_cat', get_post( $product_id ) ) || has_term( $target_tag_ids, 'product_tag', get_post( $product_id ) ) ) {

							if ( empty( $target_ids ) ) { // If target id is empty string make it an array.

								$target_ids = array();
							}

							if ( ! in_array( $product_id, $target_ids ) ) { // checking if the product does not exist in target id of a plan.

								$output .= esc_html( get_the_title( $plan['ID'] ) ) . ' | ';

							}
						}
					}
				}

				$output = substr( $output, 0, -2 );

				if ( $output ) {

					if ( true == $return_status ) {

						return true;
					} else {

						if ( in_array( $product_id, $this->under_review_products ) ) {
							?>
							<div class="product-meta product-meta-review">
								<span><b><?php esc_html_e( 'Membership Under Review', 'membership-for-woocommerce' ); ?></b></span>
							</div>
							<?php
						}

						?>
						 <div class="mfw-product-meta-membership-wrap">
							<div class="product-meta mfw-product-meta-membership">
								<span><b><?php esc_html_e( 'Membership Product ', 'membership-for-woocommerce' ); ?></b></span>
							</div>
							<i class="fa-question-circle wps_mfw_membership_tool_tip_wrapper">
								<div class="wps_mfw_membership_tool_tip">
									<?php echo esc_html( $output ); ?>
								</div>
							</i>
						</div>
						<?php
					}
				}
			}
		}
	}

	/**
	 * Validate shortcode for rendering content according to user( live offer )
	 * and admin ( for viewing purpose ).
	 *
	 * @since    3.0.0
	 */
	public function wps_membership_validate_mode() {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		// If user is customer.
		if ( ! current_user_can( 'manage_options' ) ) {

			return 'live_plan';

		} elseif ( current_user_can( 'manage_options' ) ) { // Else if user is admin.

			return 'admin_view';
		}

		return false;
	}

	/**
	 * Default plan page shortcode.
	 * Returns : html :
	 *
	 * @since 1.0.0
	 */
	public function membership_offers_default_shortcode() {

		$output = '';
		$mode   = $this->wps_membership_validate_mode();

		// If on default page, plan_id and prod_id are set.
		if ( isset( $_GET['plan_id'] ) && isset( $_GET['prod_id'] ) ) {

			$plan_id = sanitize_text_field( wp_unslash( $_GET['plan_id'] ) );
			$prod_id = sanitize_text_field( wp_unslash( $_GET['prod_id'] ) );

			// Get plan details.
			$plan_title = get_the_title( $plan_id );
			$plan_price = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_price', true );

			if ( function_exists( 'wps_mmcsfw_admin_fetch_currency_rates_from_base_currency' ) ) {
				$plan_price = wps_mmcsfw_admin_fetch_currency_rates_from_base_currency( '', $plan_price );
			}

			$plan_currency = get_woocommerce_currency_symbol();
			if ( function_exists( 'wps_mmcsfw_get_custom_currency_symbol' ) ) {
				$plan_currency = wps_mmcsfw_get_custom_currency_symbol( '' );
			}
			$plan_desc     = get_post_field( 'post_content', $plan_id );
			$plan_info     = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_info', true );

			/**
			 * Filter for membership plan.
			 *
			 * @since 1.0.0
			 */
			$offer_banner_text  = apply_filters( 'wps_membership_plan_default_banner_txt', esc_html__( 'One Membership, Many Benefits', 'membership-for-woocommerce' ) );
			/**
			 * Filter for membership plan.
			 *
			 * @since 1.0.0
			 */
			$offer_buy_now_txt  = apply_filters( 'wps_membership_plan_default_buy_now_txt', esc_html__( 'Buy Now!', 'membership-for-woocommerce' ) );
			/**
			 * Filter for membership plan.
			 *
			 * @since 1.0.0
			 */
			$offer_no_thnks_txt = apply_filters( 'wps_membership_plan_default_no_thanks_txt', esc_html__( 'No thanks!', 'membership-for-woocommerce' ) );

			$output .= '<div class="wps_membership_plan_banner">
							<h2><b><i>' . trim( $offer_banner_text ) . '</i></b></h2>
						</div>';

			$output .= '<div class="wps_membership_plan_offer_wrapper">';

			$output .= '<div class="wps_membership_plan_content_title">' . ucwords( $plan_title ) . '</div>';

			$output .= '<div class="wps_membership_plan_content_price">' . sprintf( ' %s %s ', esc_html( $plan_currency ), esc_html( $plan_price ) ) . '</div>';

			$output .= '<input type="hidden" id="wps_membership_plan_price" value="' . esc_html( $plan_price ) . '">';

			$output .= '<input type="hidden" id="wps_membership_plan_id" value="' . esc_html( $plan_id ) . '">';

			$output .= '<div class="wps_membership_plan_content_desc">' . $plan_desc . '</div>';

			$output .= '<div class="wps_membership_plan_info">' . $plan_info . '</div>';

			$output .= '</div>';

			$output .= '<div class="wps_membership_offer_action">
							<form class="wps_membership_buy_now_btn thickbox" method="post">
								<input type="hidden" name="membership_title" id="wps_membership_title" value="' . $plan_title . '">
								<input type="hidden" name="membership_id" value="' . $plan_id . '">
								<input type="submit" data-mode="' . $mode . '" class="wps_membership_buynow" name="wps_membership_buynow" value="' . $offer_buy_now_txt . '">
							</form>
							<a class="wps_membership_no_thanks button alt" href="' . get_permalink( $prod_id ) . '">' . $offer_no_thnks_txt . '</a>';
			$output .= '</div>';

		} else { // If plan_id and prod_id on default page are not set.

			/**
			 * Filter for membership plan.
			 *
			 * @since 1.0.0
			 */
			$error_msg = apply_filters( 'wps_membership_error_message', esc_html__( 'You ran out of session.', 'membership-for-woocommerce' ) );

			/**
			 * Filter for membership plan.
			 *
			 * @since 1.0.0
			 */
			$link_text = apply_filters( 'wps_membership_go_back_link_text', esc_html__( 'Go back to Shop page.', 'membership-for-woocommerce' ) );

			$shop_page_url = wc_get_page_permalink( 'shop' );

			$output .= $error_msg . '<a href="' . $shop_page_url . '" class="button">' . $link_text . '</a>';

		}

		/**
		 * Filter for tab output.
		 *
		 * @since 1.0.0
		 */
		$output = apply_filters( 'wps_membership_tab_output', $output );
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

	/**
	 * Shortcode for plan - Price.
	 * Returns : String :
	 *
	 * @param array  $atts    An array of shortcode attributes.
	 * @param string $content Output of the shortcode.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function membership_plan_price_shortcode( $atts, $content ) {

		$price = '';

		/**
		 * If shortcode attribute is set then get the plan_id from attribute else
		 * if on default page get the plan_id from query.
		 */

		$plan_id = ! empty( $atts['plan_id'] ) ? $atts['plan_id'] : '';

		if ( empty( $plan_id ) ) {

			$plan_id = isset( $_GET['plan_id'] ) ? sanitize_text_field( wp_unslash( $_GET['plan_id'] ) ) : '';

		}

		if ( ! empty( $plan_id ) ) {

			$plan_price = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_price', true );
			if ( function_exists( 'wps_mmcsfw_admin_fetch_currency_rates_from_base_currency' ) ) {
				$plan_price = wps_mmcsfw_admin_fetch_currency_rates_from_base_currency( '', $plan_price );
			}
			$plan_currency = get_woocommerce_currency_symbol();

			if ( function_exists( 'wps_mmcsfw_get_custom_currency_symbol' ) ) {
				$plan_currency = wps_mmcsfw_get_custom_currency_symbol( '' );
			}

			if ( ! empty( $plan_price ) ) {

				$price .= '<div class="wps_membership_plan_content_price">' . sprintf( ' %s %s ', esc_html( $plan_currency ), esc_html( $plan_price ) ) . '</div>';
			} else {

				$price .= '<div class="wps_membership_plan_content_price">' . $content . '</div>';
			}
		}

		/**
		 * Filter for price shortcode.'
		 *
		 * @since 1.0.0
		 */
		$price = apply_filters( 'membership_plan_price_shortcode', $price );

		return $price;
	}

	/**
	 * Shortcode for plan - title.
	 * Returns : String :
	 *
	 * @param array  $atts    An array of shortcode attributes.
	 * @param string $content Output of the shortcode.
	 *
	 * @since 1.0.0
	 */
	public function membership_plan_title_all_plan( $atts, $content ) {

		$title = '';

		/**
		 * If shortcode attribute is set then get the plan_id from attribute else
		 * if on default page get the plan_id from query.
		 */

		$plan_id = ! empty( $_GET['plan_id'] ) ? sanitize_text_field( wp_unslash( $_GET['plan_id'] ) ) : '';

		if ( empty( $plan_id ) ) {

			$plan_id = isset( $_GET['plan_id'] ) ? sanitize_text_field( wp_unslash( $_GET['plan_id'] ) ) : '';

		}

		if ( ! empty( $plan_id ) ) {

			$plan_title = get_the_title( $plan_id );

			if ( ! empty( $plan_title ) ) {

				$title .= '<div class="wps_membership_plan_content_title_for_page">' . ucwords( $plan_title ) . '</div>';
			} else {

				$title .= '<div class="wps_membership_plan_content_title">' . $content . '</div>';
			}
		}

		/**
		 * Filter for title.
		 *
		 * @since 1.0.0
		 */
		$title = apply_filters( 'membership_plan_title_all_plan', $title );
		return $title;
	}

	/**
	 * Shortcode for plan - title.
	 * Returns : String :
	 *
	 * @param array  $atts    An array of shortcode attributes.
	 * @param string $content Output of the shortcode.
	 *
	 * @since 1.0.0
	 */
	public function membership_plan_title_shortcode( $atts, $content ) {

		$title = '';

		/**
		 * If shortcode attribute is set then get the plan_id from attribute else
		 * if on default page get the plan_id from query.
		 */

		$plan_id = ! empty( $atts['plan_id'] ) ? $atts['plan_id'] : '';

		if ( empty( $plan_id ) ) {

			$plan_id = isset( $_GET['plan_id'] ) ? sanitize_text_field( wp_unslash( $_GET['plan_id'] ) ) : '';

		}

		if ( ! empty( $plan_id ) ) {

			$plan_title = get_the_title( $plan_id );

			if ( ! empty( $plan_title ) ) {

				$title .= '<div class="wps_membership_plan_content_title">' . ucwords( $plan_title ) . '</div>';
			} else {

				$title .= '<div class="wps_membership_plan_content_title">' . $content . '</div>';
			}
		}

		/**
		 * Filter for title.
		 *
		 * @since 1.0.0
		 */
		$title = apply_filters( 'membership_plan_title_shortcode', $title );

		return $title;
	}

	/**
	 * Shortcode for plan - details.
	 * Returns : String :
	 *
	 * @param mixed $plan_id plan id of current membership.
	 *
	 * @since 1.1.0
	 */
	public function get_plan_details( $plan_id ) {

		$plugin_admin = new Membership_For_Woocommerce_Admin( '', '' );
		$plugin_admin->set_plan_creation_fields( $plan_id );

		$plan = $plugin_admin->settings_fields;
		$description           = '';
		$plan_type             = ! empty( $plan['wps_membership_plan_name_access_type'] ) ? $plan['wps_membership_plan_name_access_type'] : '';
		$plan_dura             = ! empty( $plan['wps_membership_plan_duration'] ) ? $plan['wps_membership_plan_duration'] : '';
		$dura_type             = ! empty( $plan['wps_membership_plan_duration_type'] ) ? $plan['wps_membership_plan_duration_type'] : '';
		$plan_start            = ! empty( $plan['wps_membership_plan_start'] ) ? $plan['wps_membership_plan_start'] : '';
		$plan_end              = ! empty( $plan['wps_membership_plan_end'] ) ? $plan['wps_membership_plan_end'] : '';
		$plan_access           = ! empty( $plan['wps_membership_plan_user_access'] ) ? $plan['wps_membership_plan_user_access'] : '';
		$access_type           = ! empty( $plan['wps_membership_plan_access_type'] ) ? $plan['wps_membership_plan_access_type'] : '';
		$delay_dura            = ! empty( $plan['wps_membership_plan_time_duration'] ) ? $plan['wps_membership_plan_time_duration'] : '';
		$delay_type            = ! empty( $plan['wps_membership_plan_time_duration_type'] ) ? $plan['wps_membership_plan_time_duration_type'] : '';
		$discount              = ! empty( $plan['wps_memebership_plan_discount_price'] ) ? $plan['wps_memebership_plan_discount_price'] : '';
		$price_type            = ! empty( $plan['wps_membership_plan_offer_price_type'] ) ? $plan['wps_membership_plan_offer_price_type'] : '';
		$shipping              = ! empty( $plan['wps_memebership_plan_free_shipping'] ) ? $plan['wps_memebership_plan_free_shipping'] : '';
		$products              = ! empty( $plan['wps_membership_plan_target_ids'] ) ? $plan['wps_membership_plan_target_ids'] : '';
		$categories            = ! empty( $plan['wps_membership_plan_target_categories'] ) ? $plan['wps_membership_plan_target_categories'] : '';
		$club_membership       = ! empty( $plan['wps_membership_club'] ) ? $plan['wps_membership_club'] : '';
		$discount_on_product   = ! empty( $plan['wps_memebership_product_discount_price'] ) ? $plan['wps_memebership_product_discount_price'] : '';
		$price_type_on_product = ! empty( $plan['wps_membership_product_offer_price_type'] ) ? $plan['wps_membership_product_offer_price_type'] : '';
		$plan_subscription = ! empty( $plan['wps_membership_subscription'] ) ? $plan['wps_membership_subscription'] : '';
		$plan_subscription_duration = ! empty( $plan['wps_membership_subscription_expiry'] ) ? $plan['wps_membership_subscription_expiry'] : '';
		$plan_subscription_duration_type = ! empty( $plan['wps_membership_subscription_expiry_type'] ) ? $plan['wps_membership_subscription_expiry_type'] : '';

		$plan_currency = '';

		if ( 'fixed' == $price_type ) {

			if ( function_exists( 'wps_mmcsfw_admin_fetch_currency_rates_from_base_currency' ) ) {
				$discount = wps_mmcsfw_admin_fetch_currency_rates_from_base_currency( '', $discount );
			}
			$plan_currency = get_woocommerce_currency_symbol();
			if ( function_exists( 'wps_mmcsfw_get_custom_currency_symbol' ) ) {
				$plan_currency = wps_mmcsfw_get_custom_currency_symbol( '' );
			}
		}
		if ( 'fixed' == $price_type_on_product ) {
			if ( function_exists( 'wps_mmcsfw_admin_fetch_currency_rates_from_base_currency' ) ) {
				$discount_on_product = wps_mmcsfw_admin_fetch_currency_rates_from_base_currency( '', $discount_on_product );
			}
			$plan_currency = get_woocommerce_currency_symbol();
			if ( function_exists( 'wps_mmcsfw_get_custom_currency_symbol' ) ) {
				$plan_currency = wps_mmcsfw_get_custom_currency_symbol( '' );
			}
		}

		$args = array(
			'post_type'   => 'wps_cpt_membership',
			'post_status' => array( 'publish' ),
			'numberposts' => -1,
		);

		$existing_plans = get_posts( $args );
		$instance        = $plugin_admin->global_class;

		if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
			$check_licence = check_membership_pro_plugin_is_active();
			if ( $check_licence ) {

				$description .= '<div class="members_plans_details">  ';
				if ( ! empty( $plan ) ) {
					$description .= '<div class="wps_members_plans"> 
						<label>' . __( 'Membership Details', 'membership-for-woocommerce' ) . ' <span>&#10148; </span></label>';
					$description .= '<div class="wps_table_wrapper"><ul class="form-table">';

					$description .= '<li><label>';
					$description .= __( 'Plan Type', 'membership-for-woocommerce' );
					$description .= '</label><span>' . $plan_type . '</span></li>';

					switch ( $plan_type ) {

						case 'lifetime':
							break;

						case 'limited':
							$description .= '<li>
								<label>' . esc_html__( 'Plan Duration', 'membership-for-woocommerce' ) . '</label>

								<span>' . sprintf( ' %u %s ', esc_html( $plan_dura ), esc_html( $dura_type ) ) . '</span>
							</li>';
							break;

						case 'date_ranged':
							$description .= '<li>
								<label>' . esc_html__( 'Duration', 'membership-for-woocommerce' ) . '</label>

								<span>' . sprintf( ' %s to %s ', esc_html( $plan_start ), esc_html( $plan_end ) ) . '</span>
							</li>';
							break;

						default:
							$description .= '<li>' . esc_html__( 'Plan duration not defined', 'membership-for-woocommerce' ) . '</li></br>';

					}

					$description .= '<li><label>';
					$description .= __( 'Subscription Membership', 'membership-for-woocommerce' );
					$description .= '</label><span>' . $plan_subscription . '</span></li>';

					$description .= '<li><label>';
					$description .= __( 'Subscription Membership Duration', 'membership-for-woocommerce' );
					$description .= '</label><span>' . sprintf( ' %u %s ', esc_html( $plan_subscription_duration ), esc_html( $plan_subscription_duration_type ) ) . '</span></li>';
					if ( ! empty( $plan_access ) ) {
						$description .= '<li><label>';
						$description .= __( 'Plan access', 'membership-for-woocommerce' );
						$description .= '</label><span>' . $plan_access . '</span></li>';
					}

					$description .= '<li><label>';
					$description .= __( 'Access Type', 'membership-for-woocommerce' );
					$description .= '</label><span>' . $access_type . '</span></li>';

					switch ( $access_type ) {

						case 'immediate_type':
							break;

						case 'delay_type':
							$description .= '<li>
								<label>' . esc_html__( 'Delay Duration', 'membership-for-woocommerce' ) . '</label>

								<span>' . sprintf( ' %u %s ', esc_html( $delay_dura ), esc_html( $delay_type ) ) . '</span>
							</li>';
							break;

						default:
							$description .= '<li>' . esc_html__( 'Access type duration not defined', 'membership-for-woocommerce' ) . '</li></br>';
					}

					$description .= '<li><label>';
					$description .= __( 'Discount on cart', 'membership-for-woocommerce' );
					$description .= '</label><span>' . esc_html( $plan_currency ) . sprintf( ' %u %s ', esc_html( $discount ), esc_html( $price_type ) ) . '</span></li>';

					$description .= '<li><label>';
					$description .= __( 'Discount on Product', 'membership-for-woocommerce' );
					$description .= '</label><span>' . esc_html( $plan_currency ) . sprintf( ' %u %s ', esc_html( $discount_on_product ), esc_html( $price_type_on_product ) ) . '</span></li>';

					if ( ! empty( $club_membership ) ) {
						$description .= '<li><label>';
						$description .= __( 'Include Membership', 'membership-for-woocommerce' );
						$description .= '</label><span>';
						$prod_ids = maybe_unserialize( $products );
						if ( ! empty( $club_membership ) && is_array( $club_membership ) ) {
							foreach ( $club_membership as $ids ) {
								$description .= get_the_title( $ids );
							}
						}
						$description .= '</span></li>';

					}

					$description .= '<li><label>';
					$description .= __( 'Free Shipping', 'membership-for-woocommerce' );
					$description .= '</label><span>' . $shipping . '</span></li>';

					$description .= '<li><label>';
					$description .= __( 'Offered Products', 'membership-for-woocommerce' );
					$description .= '</label><span>';
					$prod_ids = maybe_unserialize( $products );
					if ( ! empty( $prod_ids ) && is_array( $prod_ids ) ) {
						foreach ( $prod_ids as $ids ) {
							$description .= ( esc_html( $instance->get_product_title( $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
						}
					}
					$description .= '</span></li>';

					$cat_ids = maybe_unserialize( $categories );
					if ( ! empty( $cat_ids ) ) {

						$description .= '<li><label>';
						$description .= __( 'Offered Categories', 'membership-for-woocommerce' );
						$description .= '</label><span>';

						$cat_ids = maybe_unserialize( $categories );
						if ( ! empty( $cat_ids ) && is_array( $cat_ids ) ) {
							foreach ( $cat_ids as $ids ) {
								$description .= ( esc_html( $instance->get_category_title( $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
							}
						}

						$description .= '</span></li>';
					}

					$tag_ids = maybe_unserialize( $plan['wps_membership_plan_target_tags'] );
					if ( ! empty( $tag_ids ) ) {

						$description .= '<li><label>';
						$description .= __( 'Offered Product Tags', 'membership-for-woocommerce' );
						$description .= '</label><span>';

						if ( ! empty( $tag_ids ) && is_array( $tag_ids ) ) {
							foreach ( $tag_ids as $ids ) {
								$tagn     = get_term_by( 'id', $ids, 'product_tag' );
								$tag_name = $tagn->name;
								$description .= ( esc_html( $tag_name ) . '(#' . esc_html( $ids ) . ') ' );
							}
						}

						$description .= '</span></li>';
					}

					$post_ids = maybe_unserialize( $plan['wps_membership_plan_post_target_ids'] );
					if ( ! empty( $post_ids ) ) {
						$description .= '<li><label>';
						$description .= __( 'Offered Posts', 'membership-for-woocommerce' );
						$description .= '</label><span>';

						if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
							foreach ( $post_ids as $ids ) {

								$description .= ( esc_html( get_post_field( 'post_title', $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
							}
						}
						$description .= '</span></li>';
					}

					$cat_ids = maybe_unserialize( $plan['wps_membership_plan_target_post_categories'] );
					if ( ! empty( $cat_ids ) ) {
						$description .= '<li><label>';
						$description .= __( 'Offered Posts Categories', 'membership-for-woocommerce' );
						$description .= '</label><span>';

						if ( ! empty( $cat_ids ) && is_array( $cat_ids ) ) {
							foreach ( $cat_ids as $ids ) {
								$description .= ( esc_html( $instance->get_category_title( $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
							}
						}
						$description .= '</span></li>';
					}

					$tag_ids = maybe_unserialize( $plan['wps_membership_plan_target_post_tags'] );
					if ( ! empty( $tag_ids ) ) {
						$description .= '<li><label>';
						$description .= __( 'Offered Post Tags', 'membership-for-woocommerce' );
						$description .= '</label><span>';
						if ( ! empty( $tag_ids ) && is_array( $tag_ids ) ) {
							foreach ( $tag_ids as $ids ) {
								$tagn     = get_term_by( 'id', $ids, 'post_tag' );
								$tag_name = $tagn->name;
								$description .= ( esc_html( $tag_name ) . '(#' . esc_html( $ids ) . ') ' );
							}
						}
						$description .= '</span></li>';

					}

					$page_ids = maybe_unserialize( $plan['wps_membership_plan_page_target_ids'] );
					if ( ! empty( $page_ids ) ) {

						$description .= '<li><label>';
						$description .= __( 'Offered Pages', 'membership-for-woocommerce' );
						$description .= '</label><span>';
						$page_ids = maybe_unserialize( $plan['wps_membership_plan_page_target_ids'] );

						if ( ! empty( $page_ids ) && is_array( $page_ids ) ) {
							foreach ( $page_ids as $ids ) {

								$description .= ( esc_html( get_post_field( 'post_title', $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
							}
						}
						$description .= '</span></li>';
					}

					$product_ids_discount = maybe_unserialize( $plan['wps_membership_plan_target_disc_ids'] );
					if ( ! empty( $product_ids_discount ) ) {

						$description .= '<li><label>';
						$description .= __( 'Offered Product (under Product Discount)', 'membership-for-woocommerce' );
						$description .= '</label><span>';
						$product_ids_discount = maybe_unserialize( $plan['wps_membership_plan_target_disc_ids'] );

						if ( ! empty( $product_ids_discount ) && is_array( $product_ids_discount ) ) {
							foreach ( $product_ids_discount as $ids ) {

								$description .= ( esc_html( get_post_field( 'post_title', $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
							}
						}
						$description .= '</span></li>';
					}

					$product_cat_discount = maybe_unserialize( $plan['wps_membership_plan_target_disc_categories'] );
					if ( ! empty( $product_cat_discount ) ) {

						$description .= '<li><label>';
						$description .= __( 'Offered Product Categories (under Product Discount)', 'membership-for-woocommerce' );
						$description .= '</label><span>';

						$cat_ids = maybe_unserialize( $product_cat_discount );
						if ( ! empty( $cat_ids ) && is_array( $cat_ids ) ) {
							foreach ( $cat_ids as $ids ) {
								$description .= ( esc_html( $instance->get_category_title( $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
							}
						}
						$description .= '</span></li>';
					}

					$product_tags_discount = maybe_unserialize( $plan['wps_membership_plan_target_disc_tags'] );
					if ( ! empty( $product_tags_discount ) ) {

						$description .= '<li><label>';
						$description .= __( 'Offered Product Tags (under Product Discount)', 'membership-for-woocommerce' );
						$description .= '</label><span>';

						if ( ! empty( $product_tags_discount ) && is_array( $product_tags_discount ) ) {
							foreach ( $product_tags_discount as $ids ) {
								$tagn     = get_term_by( 'id', $ids, 'product_tag' );
								$tag_name = $tagn->name;
								$description .= ( esc_html( $tag_name ) . '(#' . esc_html( $ids ) . ') ' );
							}
						}
						$description .= '</span></li>';
					}

					$description .= '</ul></div>';

					$description .= '</div></div>';

				}
			}
		}
		return $description;
	}

	/**
	 * Shortcode for plan - description
	 * Return : string :
	 *
	 * @param array  $atts    An array of shortcode attributes.
	 * @param string $content Content of the shortcode.
	 */
	public function membership_plan_desc_shortcode_all_plan( $atts, $content ) {

		$description = '';

		/**
		 * If shortcode attribute is set then get the plan_id from attribute else
		 * if on default page get the plan_id from query.
		 */

		$plan_id = ! empty( $atts['plan_id'] ) ? $atts['plan_id'] : '';

		if ( empty( $plan_id ) ) {

			$plan_id = isset( $_GET['plan_id'] ) ? sanitize_text_field( wp_unslash( $_GET['plan_id'] ) ) : '';

		}

		if ( ! empty( $plan_id ) ) {

			$plan_desc = get_post_field( 'post_content', $plan_id );

			if ( ! empty( $plan_desc ) ) {

				$description .= '<div class="wps_membership_plan_content_description">' . $plan_desc . '</div>';

				$description .= $this->get_plan_details( $plan_id );

			} else {
				$description .= '<div class="wps_membership_plan_content_desc">' . $content . '</div>';
				$description .= $this->get_plan_details( $plan_id );
			}

			$plan_info = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_info', true );

			if ( ! empty( $plan_info ) ) {

				$description .= '<div class="wps_membership_plan_info">' . $plan_info . '</div>';
			}
		} else {

			$description .= '<div class="wps_membership_plan_content_desc">';
			$user = wp_get_current_user();
				$data                = $this->custom_query_data;
				$user_id             = get_current_user_id();
				$existing_plan_id    = array();
				$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );

			if ( ! empty( $data ) ) {
				foreach ( $data as $plan ) {

					$plan_info = wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_info', true );

					$wps_membership_default_plans_page_id = get_option( 'wps_membership_default_plans_page', '' );

					if ( ! empty( $wps_membership_default_plans_page_id ) && 'publish' == get_post_status( $wps_membership_default_plans_page_id ) ) {
						$page_link = get_page_link( $wps_membership_default_plans_page_id );
					}
					$plan_price    = floatval( wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_price', true ) );

					if ( function_exists( 'wps_mmcsfw_admin_fetch_currency_rates_from_base_currency' ) ) {
						$plan_price = wps_mmcsfw_admin_fetch_currency_rates_from_base_currency( '', $plan_price );
					}

					$plan_currency = get_woocommerce_currency_symbol();
					if ( function_exists( 'wps_mmcsfw_get_custom_currency_symbol' ) ) {
						$plan_currency = wps_mmcsfw_get_custom_currency_symbol( '' );
					}

					$mode = $this->wps_membership_validate_mode();
					$content = 'Buy Now';
						$description .= '<div class="wps_all_plans_detail_wrapper">';
						$description .= '<h2>' . $plan['post_title'] . '</h2>';
						$description .= '<div class="wps_membership_plan_content_price">' . sprintf( ' %s %s ', esc_html( $plan_currency ), esc_html( $plan_price ) ) . '</div>';
						$plan_desc = get_post_field( 'post_content', $plan['ID'] );

					if ( ! empty( $plan_desc ) ) {

						$description .= '<div class="wps_membership_plan_content_description">' . $plan_desc . '</div>';
					}
					if ( ! empty( $plan_info ) ) {

						$description .= '<div class="wps_membership_plan_info">' . $plan_info . '</div>';
					}
						$description .= $this->get_plan_details( $plan['ID'] );

						$description .= '</div>';

						$description .= '<form method="post" class="wps_membership_buy_now_btn">
						<input type="hidden" id="wps_membership_plan_id" name="plan_id" value="' . $plan['ID'] . '">
						<input type="hidden" id="wps_membership_plan_price" value="' . esc_html( $plan_price ) . '">
						<input type="hidden" name="membership_title" id="wps_membership_title" value="' . $plan['post_title'] . '">
						<input type="button" data-mode="' . $mode . '" class="wps_membership_buynow" name="wps_membership_buynow" value="' . $content . '">
						</form>';
				}
			} else {
				$description .= esc_html__( 'Plans Not Available', 'membership-for-woocommerce' );
			}

			$description .= '</div>';
		}

		/**
		 * Filter for desc.
		 *
		 * @since 1.0.0
		 */
		$description = apply_filters( 'membership_plan_description_shortcode', $description );

		return $description;
	}

	/**
	 * Shortcode for plan - description
	 * Return : string :
	 *
	 * @param array  $atts    An array of shortcode attributes.
	 * @param string $content Content of the shortcode.
	 */
	public function membership_plan_desc_shortcode( $atts, $content ) {

		$description = '';

		/**
		 * If shortcode attribute is set then get the plan_id from attribute else
		 * if on default page get the plan_id from query.
		 */

		$plan_id = ! empty( $atts['plan_id'] ) ? $atts['plan_id'] : '';

		if ( empty( $plan_id ) ) {

			$plan_id = isset( $_GET['plan_id'] ) ? sanitize_text_field( wp_unslash( $_GET['plan_id'] ) ) : '';

		}

		if ( ! empty( $plan_id ) ) {

			$plan_desc = get_post_field( 'post_content', $plan_id );

			if ( ! empty( $plan_desc ) ) {

				$description .= '<div class="wps_membership_plan_content_desc">' . $plan_desc . '</div>';
			} else {
				$description .= '<div class="wps_membership_plan_content_desc">' . $content . '</div>';
			}

			$plan_info = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_info', true );

			if ( ! empty( $plan_info ) ) {

				$description .= '<div class="wps_membership_plan_info">' . $plan_info . '</div>';
			}
		}

		/**
		 * Filter for desc.
		 *
		 * @since 1.0.0
		 */
		$description = apply_filters( 'membership_plan_description_shortcode', $description );

		return $description;
	}

	/**
	 * Shortcode for plan - Buy now button.
	 * Returns : Link :
	 *
	 * @param array  $atts    An array of shortcode attributes.
	 * @param string $content Content of the shortcode.
	 *
	 * @since 1.0.0
	 */
	public function buy_now_shortcode_yes( $atts, $content ) {

		$buy_button = '';

		/**
		 * If shortcode attribute is set then get the plan_id from attribute else
		 * if on default page get the plan_id from query.
		 */

		$plan_id = ! empty( $atts['plan_id'] ) ? $atts['plan_id'] : '';

		$mode = $this->wps_membership_validate_mode();

		if ( empty( $plan_id ) ) {

			$plan_id = isset( $_GET['plan_id'] ) ? sanitize_text_field( wp_unslash( $_GET['plan_id'] ) ) : '';

		}
		if ( ! empty( $plan_id ) ) {

			$plan_price = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_price', true );
			$plan_title = get_the_title( $plan_id );

			if ( empty( $content ) ) {

				/**
				 * Filter for content.
				 *
				 * @since 1.0.0
				 */
				$content = apply_filters( 'wps_mebership_buy_now_btn_txt', esc_html__( 'Buy Now!', 'membership-for-woocommerce' ) );

			}

			$buy_button .= '<form method="post" class="wps_membership_buy_now_btn">
								<input type="hidden" id="wps_membership_plan_id" name="plan_id" value="' . $plan_id . '">
								<input type="hidden" id="wps_membership_plan_price" value="' . esc_html( $plan_price ) . '">
								<input type="hidden" name="membership_title" id="wps_membership_title" value="' . $plan_title . '">
								<input type="button" data-mode="' . $mode . '" class="wps_membership_buynow" name="wps_membership_buynow" value="' . $content . '">
							</form>';
			/**
			 * Filter for button shortcode.
			 *
			 * @since 1.0.0
			 */
			$buy_button  = apply_filters( 'membership_plan_buy_button_shortcode', $buy_button );

		}
		return $buy_button;

	}

	/**
	 * Shortcode for plan - Buy now button.
	 * Returns : Link :
	 *
	 * @param array  $atts    An array of shortcode attributes.
	 * @param string $content Content of the shortcode.
	 *
	 * @since 1.0.0
	 */
	public function buy_now_shortcode_content( $atts, $content ) {

		$buy_button = '';

		/**
		 * If shortcode attribute is set then get the plan_id from attribute else
		 * if on default page get the plan_id from query.
		 */

		$plan_id = ! empty( $atts['plan_id'] ) ? $atts['plan_id'] : 0;
		$mode    = $this->wps_membership_validate_mode();

		if ( empty( $plan_id ) ) {

			$plan_id = isset( $_GET['plan_id'] ) ? sanitize_text_field( wp_unslash( $_GET['plan_id'] ) ) : '';

		}

		$plan_price = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_price', true );
		$plan_title = get_the_title( $plan_id );

		if ( empty( $content ) ) {

			/**
			 * Filter for content.
			 *
			 * @since 1.0.0
			 */
			$content = apply_filters( 'wps_mebership_buy_now_btn_txt', esc_html__( 'Buy Now!', 'membership-for-woocommerce' ) );

		}

		$buy_button .= '<form method="post" class="wps_membership_buy_now_btn">
							<input type="hidden" id="wps_membership_plan_id" name="plan_id" value="' . $plan_id . '">
							<input type="hidden" id="wps_membership_plan_price" value="' . esc_html( $plan_price ) . '">
							<input type="hidden" name="membership_title" id="wps_membership_title" value="' . $plan_title . '">
							<input type="button" data-mode="' . $mode . '" class="wps_membership_buynow" name="wps_membership_buynow" value="' . $content . '">
						</form>';

		/**
		 * Filter for plan buy.
		 *
		 * @since 1.0.0
		 */
		$buy_button  = apply_filters( 'membership_plan_buy_button_shortcode', $buy_button );
		return $buy_button;
	}

	/**
	 * Shortcode for plan - No thanks button
	 * Returns : link :
	 *
	 * @param array  $atts    An array of shortcode attributes.
	 * @param string $content Content of the shortcode.
	 *
	 * @since 1.0.0
	 */
	public function reject_shortcode_content( $atts, $content ) {

		$no_thanks_button = '';

		$mode = $this->wps_membership_validate_mode();

		/**
		 * If shortcode attribute is set then get the plan_id from attribute else
		 * if on default page get the plan_id from query.
		 */

		$plan_id = ! empty( $atts['plan_id'] ) ? $atts['plan_id'] : '';

		if ( empty( $plan_id ) ) {

			$plan_id = isset( $_GET['plan_id'] ) ? sanitize_text_field( wp_unslash( $_GET['plan_id'] ) ) : '';

			$prod_id = isset( $_GET['prod_id'] ) ? sanitize_text_field( wp_unslash( $_GET['prod_id'] ) ) : '';
		}

		if ( empty( $content ) ) {

			/**
			 * Filter for no thanks button.
			 *
			 * @since 1.0.0
			 */
			$content = apply_filters( 'wps_mebership_no_thanks_btn_txt', esc_html__( 'No Thanks!', 'membership-for-woocommerce' ) );

		}

		$no_thanks_button .= '<a class="wps_membership_no_thanks button alt thickbox" data-mode="' . $mode . '" href="' . ( ! empty( $prod_id ) ? get_permalink( $prod_id ) : wc_get_page_permalink( 'shop' ) ) . '">' . $content . '</a>';

		/**
		 * Filter for no thanks.
		 *
		 * @since 1.0.0
		 */
		$no_thanks_button  = apply_filters( 'membership_plan_no_thanks_button_shortcode', $no_thanks_button );

		return $no_thanks_button;
	}

	/**
	 * Shortcode for plan - No thanks button
	 * Returns : link :
	 *
	 * @param array  $atts    An array of shortcode attributes.
	 * @param string $content Content of the shortcode.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_plan_details( $atts, $content ) {

	}

	/**
	 * Hide all other shiiping methods, if free membership shipping available.
	 *
	 * @param array  $rates An array of shipping method rates.
	 * @param [type] $package Package of the shipping method.
	 * @return string
	 */
	public function wps_membership_unset_shipping_if_membership_available( $rates, $package ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return $rates;
		}
		$all_methods = array();
		$user = wp_get_current_user();
		$is_allowed_membership_shipping = false;

		$is_member_meta = get_user_meta( $user->ID, 'is_member' );
		if ( $this->global_class->plans_exist_check() == true ) {

			$user_id = get_current_user_id();

			$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );

			if ( ! empty( $current_memberships && is_array( $current_memberships ) ) ) {

				foreach ( $current_memberships as $key => $membership_id ) {

					$membership_status = wps_membership_get_meta_data( $membership_id, 'member_status', true );
					if ( ! empty( $membership_status ) && 'complete' == $membership_status ) {
						$active_plan = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
						if ( ! empty( $active_plan ) && is_array( $active_plan ) ) {
							if ( key_exists( 'wps_memebership_plan_free_shipping', $active_plan ) ) {

								if ( 'yes' == $active_plan['wps_memebership_plan_free_shipping'] ) {
									$is_allowed_membership_shipping = true;
								}
							}
						}
					}
				}
			}

			if ( $is_allowed_membership_shipping ) {
				return $rates;
			} else {
				foreach ( $rates as $rate_key => $rate ) {
							// Excluding membership shipping methods.
					if ( 'wps_membership_shipping' === $rate->get_method_id() ) {
						unset( $rates[ $rate_key ] );
					}
				}
				return $rates;
			}
		}

	}

	/**
	 * Register the AJAX Callback for file removal.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_remove_current_receipt() {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		// Verify nonce.
		check_ajax_referer( 'auth_adv_nonce', 'auth_nonce' );

		// phpcs:disable
		$file_path = ! empty( $_POST['path'] ) ? sanitize_text_field( wp_unslash( $_POST['path'] ) ) : ''; // phpcs:ignore
		// phpcs:enable

		if ( ! empty( $file_path ) ) {
			// Check file or not.
			if ( file_exists( $file_path ) ) {
				// Remove file.
				unlink( $file_path );

				echo wp_json_encode(
					array(
						'result' => 'success',
					)
				);
			} else {

				echo wp_json_encode(
					array(
						'result' => 'failure',
					)
				);
			}
			wp_die();
		}
	}

	/**
	 * Ajax callback for getting states.
	 */
	public function wps_membership_get_states_public() {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		// Nonce verify.
		check_ajax_referer( 'auth_adv_nonce', 'nonce' );

		$country_code = ! empty( $_POST['country'] ) ? sanitize_text_field( wp_unslash( $_POST['country'] ) ) : '';
		$country_class = new WC_Countries();
		$states = $country_class->__get( 'states' );
		$states = ! empty( $states[ $country_code ] ) ? $states[ $country_code ] : array();
		$result = '';

		if ( ! empty( $states ) && is_array( $states ) ) {
			foreach ( $states as $state_code => $name ) {
				?>
				<option value="<?php echo esc_html( $state_code ); ?>"><?php echo esc_html( $name ); ?></option>
				<?php
			}
		}
		wp_die();
	}

	/**
	 * Assign Club membership.
	 *
	 * @param [type] $plan_id is the id of current plan.
	 * @param [type] $plan_obj is the object of current plan.
	 * @param [type] $member_id is the id of member.
	 * @return void
	 */
	public function assign_club_membership_to_member( $plan_id, $plan_obj, $member_id ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$club_membership = wps_membership_get_meta_data( $plan_id, 'wps_membership_club', true );

		if ( ! empty( $club_membership ) && is_array( $club_membership ) ) {

			foreach ( $club_membership as $mem_ids ) {

				$product_ids = wps_membership_get_meta_data( $mem_ids, 'wps_membership_plan_target_ids', true );
				if ( ! empty( $product_ids ) ) {
					$plan_obj['wps_membership_plan_target_ids'] = ! empty( $plan_obj['wps_membership_plan_target_ids'] ) ? unserialize( $plan_obj['wps_membership_plan_target_ids'] ) : array();
					$plan_obj['wps_membership_plan_target_ids'] = array_merge( $plan_obj['wps_membership_plan_target_ids'], $product_ids );
					$plan_obj['wps_membership_plan_target_ids'] = serialize( $plan_obj['wps_membership_plan_target_ids'] );
				}
				$product_disc_ids = wps_membership_get_meta_data( $mem_ids, 'wps_membership_plan_target_disc_ids', true );
				if ( ! empty( $product_disc_ids ) ) {
					$plan_obj['wps_membership_plan_target_disc_ids'] = ! empty( $plan_obj['wps_membership_plan_target_disc_ids'] ) ? unserialize( $plan_obj['wps_membership_plan_target_disc_ids'] ) : array();
					$plan_obj['wps_membership_plan_target_disc_ids'] = array_merge( $plan_obj['wps_membership_plan_target_disc_ids'], $product_disc_ids );
					$plan_obj['wps_membership_plan_target_disc_ids'] = serialize( $plan_obj['wps_membership_plan_target_disc_ids'] );
				}

				$post_ids = wps_membership_get_meta_data( $mem_ids, 'wps_membership_plan_post_target_ids', true );
				if ( ! empty( $post_ids ) ) {
					$plan_obj['wps_membership_plan_post_target_ids'] = ! empty( $plan_obj['wps_membership_plan_post_target_ids'] ) ? unserialize( $plan_obj['wps_membership_plan_post_target_ids'] ) : array();
					$plan_obj['wps_membership_plan_post_target_ids'] = array_merge( $plan_obj['wps_membership_plan_post_target_ids'], $post_ids );
					$plan_obj['wps_membership_plan_post_target_ids'] = serialize( $plan_obj['wps_membership_plan_post_target_ids'] );
				}

				$post_ids = wps_membership_get_meta_data( $mem_ids, 'wps_membership_plan_page_target_ids', true );
				if ( ! empty( $post_ids ) ) {
					$plan_obj['wps_membership_plan_page_target_ids'] = ! empty( $plan_obj['wps_membership_plan_page_target_ids'] ) ? unserialize( $plan_obj['wps_membership_plan_page_target_ids'] ) : array();
					$plan_obj['wps_membership_plan_page_target_ids'] = array_merge( $plan_obj['wps_membership_plan_page_target_ids'], $post_ids );
					$plan_obj['wps_membership_plan_page_target_ids'] = serialize( $plan_obj['wps_membership_plan_page_target_ids'] );
				}

				$cat_ids = wps_membership_get_meta_data( $mem_ids, 'wps_membership_plan_target_categories', true );
				if ( ! empty( $cat_ids ) ) {
					$plan_obj['wps_membership_plan_target_categories'] = ! empty( $plan_obj['wps_membership_plan_target_categories'] ) ? unserialize( $plan_obj['wps_membership_plan_target_categories'] ) : array();
					$plan_obj['wps_membership_plan_target_categories'] = array_merge( $plan_obj['wps_membership_plan_target_categories'], $cat_ids );
					$plan_obj['wps_membership_plan_target_categories'] = serialize( $plan_obj['wps_membership_plan_target_categories'] );
				}
				$cat_ids = wps_membership_get_meta_data( $mem_ids, 'wps_membership_plan_target_disc_categories', true );
				if ( ! empty( $cat_ids ) ) {
					$plan_obj['wps_membership_plan_target_disc_categories'] = ! empty( $plan_obj['wps_membership_plan_target_disc_categories'] ) ? unserialize( $plan_obj['wps_membership_plan_target_disc_categories'] ) : array();
					$plan_obj['wps_membership_plan_target_disc_categories'] = array_merge( $plan_obj['wps_membership_plan_target_disc_categories'], $cat_ids );
					$plan_obj['wps_membership_plan_target_disc_categories'] = serialize( $plan_obj['wps_membership_plan_target_disc_categories'] );
				}
				$tag_ids = wps_membership_get_meta_data( $mem_ids, 'wps_membership_plan_target_tags', true );
				if ( ! empty( $tag_ids ) ) {
					$plan_obj['wps_membership_plan_target_tags'] = ! empty( $plan_obj['wps_membership_plan_target_tags'] ) ? unserialize( $plan_obj['wps_membership_plan_target_tags'] ) : array();
					$plan_obj['wps_membership_plan_target_tags'] = array_merge( $plan_obj['wps_membership_plan_target_tags'], $tag_ids );
					$plan_obj['wps_membership_plan_target_tags'] = serialize( $plan_obj['wps_membership_plan_target_tags'] );
				}
				$post_ids = wps_membership_get_meta_data( $mem_ids, 'wps_membership_plan_target_disc_tags', true );
				if ( ! empty( $post_ids ) ) {
					$plan_obj['wps_membership_plan_target_disc_tags'] = ! empty( $plan_obj['wps_membership_plan_target_disc_tags'] ) ? unserialize( $plan_obj['wps_membership_plan_target_disc_tags'] ) : array();
					$plan_obj['wps_membership_plan_target_disc_tags'] = array_merge( $plan_obj['wps_membership_plan_target_disc_tags'], $post_ids );
					$plan_obj['wps_membership_plan_target_disc_tags'] = serialize( $plan_obj['wps_membership_plan_target_disc_tags'] );
				}
				$ptags = wps_membership_get_meta_data( $mem_ids, 'wps_membership_plan_target_post_tags', true );
				if ( ! empty( $ptags ) ) {
					$plan_obj['wps_membership_plan_target_post_tags'] = ! empty( $plan_obj['wps_membership_plan_target_post_tags'] ) ? unserialize( $plan_obj['wps_membership_plan_target_post_tags'] ) : array();
					$plan_obj['wps_membership_plan_target_post_tags'] = array_merge( $plan_obj['wps_membership_plan_target_post_tags'], $ptags );
					$plan_obj['wps_membership_plan_target_post_tags'] = serialize( $plan_obj['wps_membership_plan_target_post_tags'] );
				}
				$pcats = wps_membership_get_meta_data( $mem_ids, 'wps_membership_plan_target_post_categories', true );
				if ( ! empty( $pcats ) ) {
					$plan_obj['wps_membership_plan_target_post_categories'] = ! empty( $plan_obj['wps_membership_plan_target_post_categories'] ) ? unserialize( $plan_obj['wps_membership_plan_target_post_categories'] ) : array();
					$plan_obj['wps_membership_plan_target_post_categories'] = array_merge( $plan_obj['wps_membership_plan_target_post_categories'], $pcats );
					$plan_obj['wps_membership_plan_target_post_categories'] = serialize( $plan_obj['wps_membership_plan_target_post_categories'] );
				}
				// $product_disc_ids

				$product_disc_ids = wps_membership_get_meta_data( $mem_ids, 'wps_membership_plan_target_disc_ids', true );
				if ( ! empty( $product_disc_ids ) && is_array( $product_disc_ids ) ) {
					foreach ( $product_disc_ids as $product_id ) {

						$prouct_discount = wps_membership_get_meta_data( $product_id, '_wps_membership_discount_' . $mem_ids, true );
						wps_membership_update_meta_data( $product_id, '_wps_membership_discount_' . $plan_id, $prouct_discount );
					}
				}
				wps_membership_update_meta_data( $member_id, 'plan_obj', $plan_obj );
			}
		}
	}

	/**
	 * Ajax call for membership process payment.
	 *
	 * @param mixed $order_id  id of order.
	 */
	public function wps_membership_process_payment( $order_id ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$fields = array();
		$order = wc_get_order( $order_id );
		$plan_id = '';
		foreach ( $order->get_items() as $item_id => $item ) {
			$plan_id = $item->get_meta( '_wps_plan_id' );
			$member_id = $item->get_meta( '_member_id' );
			$product_id = $item->get_data()['product_id'];
		}

		$wps_membership_default_product = get_option( 'wps_membership_default_product', '' );
		if ( $product_id == $wps_membership_default_product ) {
			if ( $plan_id ) {
				$is_processing = get_option( 'wps_membership_create_member_on_processing' );
				if ( 'on' === $is_processing ) {

					if ( 'processing' == $order->get_status() ) {
						$order_st = 'complete';
					} elseif ( 'on-hold' == $order->get_status() || 'refunded' == $order->get_status() ) {
						$order_st = 'hold';
					} elseif ( 'pending' == $order->get_status() || 'completed' == $order->get_status() || 'failed' == $order->get_status() ) {
						$order_st = 'pending';
					} elseif ( 'cancelled' == $order->get_status() ) {
						$order_st = 'cancelled';
					}
				} else {
					if ( 'completed' == $order->get_status() ) {
						$order_st = 'complete';
					} elseif ( 'on-hold' == $order->get_status() || 'refunded' == $order->get_status() ) {
						$order_st = 'hold';
					} elseif ( 'pending' == $order->get_status() || 'processing' == $order->get_status() || 'failed' == $order->get_status() ) {
						$order_st = 'pending';
					} elseif ( 'cancelled' == $order->get_status() ) {
						$order_st = 'cancelled';
					}
				}
				wps_membership_update_meta_data( $member_id, 'member_status', $order_st );

			} else {

				if ( ! empty( WC()->session ) && WC()->session->has_session() ) {
					$plan_id   = WC()->session->get( 'plan_id' );
				}

				$this->wps_msfw_membership_update_meta_data( $order, $plan_id, $member_id, $fields, $order_id );
			}

			$this->wps_process_payment_callback( $member_id );

		} else {
			$plan_id = wps_membership_get_meta_data( $product_id, 'wps_membership_plan_with_product', true );
			$is_plan_assigned = false;
			$user = get_user_by( 'email', $order->get_billing_email() );
			$is_member_meta = get_user_meta( $user->ID, 'is_member' );
			$user_id = $user->ID;
			$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );

			if ( $plan_id ) {
				$is_plan_assigned = false;
			}

			if ( ! empty( $current_memberships ) && is_array( $current_memberships ) && $plan_id ) {
				foreach ( $current_memberships as $key => $membership_id ) {
					$active_plan = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
					$status = wps_membership_get_meta_data( $membership_id, 'member_status', true );

					if ( ! empty( $active_plan['ID'] ) ) {
						if ( $plan_id == $active_plan['ID'] && 'cancelled' != $status && ! empty( $status ) ) {
							$is_plan_assigned = true;
							break;

						}
					}
				}
			}

			if ( ! $is_plan_assigned ) {
				$this->wps_msfw_membership_update_meta_data( $order, $plan_id, $member_id, $fields, $order_id );
				$this->wps_process_payment_callback( $member_id );
			}
		}
	}

	/**
	 * Function to update meta data.
	 *
	 * @param object $order is object of order.
	 * @param int    $plan_id is id of assigned plan.
	 * @param int    $member_id is the id of the member.
	 * @param array  $fields is a array of fields.
	 * @param int    $order_id is the id of order.
	 * @return void
	 */
	public function wps_msfw_membership_update_meta_data( $order, $plan_id, $member_id, $fields, $order_id ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$items    = $order->get_data()['line_items'];
		$keys     = array_keys( $items );

		wc_add_order_item_meta( $keys[0], '_wps_plan_id', $plan_id );
		$billing_data = $order->get_data()['billing'];
		$order_data = $order->get_data();
		$order_status = $order_data['status'];

		$fields['membership_billing_first_name'] = $billing_data['first_name'];
		$fields['membership_billing_last_name'] = $billing_data['last_name'];
		$fields['membership_billing_company'] = $billing_data['company'];
		$fields['membership_billing_country'] = $billing_data['country'];
		$fields['membership_billing_address_1'] = $billing_data['address_1'];
		$fields['membership_billing_address_2'] = $billing_data['address_2'];
		$fields['membership_billing_city'] = $billing_data['city'];
		$fields['membership_billing_state'] = $billing_data['state'];
		$fields['membership_billing_postcode'] = $billing_data['postcode'];
		$fields['membership_billing_phone'] = $billing_data['phone'];
		$fields['membership_billing_email'] = $billing_data['email'];

		// If all goes well, a membership for customer will be created.
		$member_data = $this->global_class->create_membership_for_customer( $fields, $plan_id, $order_status );

		if ( $member_data ) {

			$current_memberships = get_user_meta( $member_data['user_id'], 'mfw_membership_id', true );

			$current_memberships = ! empty( $current_memberships ) ? $current_memberships : array();

			array_push( $current_memberships, $member_data['member_id'] );

			// Assign membership plan to user and assign 'member' role to it.
			update_user_meta( $member_data['user_id'], 'mfw_membership_id', $current_memberships );

			$user = new WP_User( $member_data['user_id'] ); // create a new user object for this user.
			wc_add_order_item_meta( $keys[0], '_member_id', $member_data['member_id'] );

			if ( ! $member_id ) {
				$member_id = $member_data['member_id'];
			}

			wps_membership_update_meta_data( $member_id, 'member_order_id', $order_id );
			$plan_obj = wps_membership_get_meta_data( $member_id, 'plan_obj', true );
			if ( 'yes' == $plan_obj['wps_membership_subscription'] ) {
				$available_plan = get_option( 'all_subscription_plan' );
				$available_plan = $available_plan . '-' . $member_id;
				update_option( 'all_subscription_plan', $available_plan );
			}
			$this->assign_club_membership_to_member( $plan_id, $plan_obj, $member_id );
		}
	}

	/**
	 * Function to update rest of the things.
	 *
	 * @param int $member_id is the id of member.
	 * @return void
	 */
	public function wps_process_payment_callback( $member_id ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$member_status = wps_membership_get_meta_data( $member_id, 'member_status', true );

		// If manually completing membership then set its expiry date.
		if ( 'complete' == $member_status[0] ) {

			// Getting current activation date.
			$current_date = gmdate( 'Y-m-d' );
			$today_date = gmdate( 'Y-m-d' );

			$plan_obj = wps_membership_get_meta_data( $member_id, 'plan_obj', true );

			// Save expiry date in post.
			if ( ! empty( $plan_obj ) && is_array( $plan_obj ) ) {

				$access_type = wps_membership_get_meta_data( $plan_obj['ID'], 'wps_membership_plan_access_type', true );

				if ( 'delay_type' == $access_type ) {
					$time_duration      = wps_membership_get_meta_data( $plan_obj['ID'], 'wps_membership_plan_time_duration', true );
					$time_duration_type = wps_membership_get_meta_data( $plan_obj['ID'], 'wps_membership_plan_time_duration_type', true );
					$current_date = gmdate( 'Y-m-d', strtotime( $current_date . ' + ' . $time_duration . ' ' . $time_duration_type ) );
				}

				if ( 'lifetime' == $plan_obj['wps_membership_plan_name_access_type'] ) {

					wps_membership_update_meta_data( $member_id, 'member_expiry', 'Lifetime' );

				} elseif ( 'limited' == $plan_obj['wps_membership_plan_name_access_type'] ) {

					$duration = $plan_obj['wps_membership_plan_duration'] . ' ' . $plan_obj['wps_membership_plan_duration_type'];
					$expiry_date = strtotime( $today_date . $duration );
					wps_membership_update_meta_data( $member_id, 'member_expiry', $expiry_date );
				}
			}
			$expiry_date = wps_membership_get_meta_data( $member_id, 'member_expiry', true );
			if ( 'Lifetime' == $expiry_date ) {
				$expiry_date = 'Lifetime';
			} else {
				$expiry_date = esc_html( ! empty( $expiry_date ) ? gmdate( 'Y-m-d', $expiry_date ) : '' );

			}
			$user_id = get_current_user_id();
			$user = get_userdata( $user_id );
			$user_name = $user->data->display_name;
			$order_id = wps_membership_get_meta_data( $member_id, 'member_order_id', true );
			if ( key_exists( 'membership_creation_email', WC()->mailer()->emails ) ) {

				$customer_email = WC()->mailer()->emails['membership_creation_email'];
				if ( ! empty( $customer_email ) ) {
					$email_status = $customer_email->trigger( $user_id, $plan_obj, $user_name, $expiry_date, $order_id );
				}
			}
		}
	}

	/**
	 * Handle paypal transaction data.
	 */
	public function wps_membership_save_transaction() {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		// Nonce verification.
		check_ajax_referer( 'paypal-nonce', 'nonce' );

		$tr_details = ! empty( $_POST['details'] ) ? map_deep( wp_unslash( $_POST['details'] ), 'sanitize_text_field' ) : ''; // phpcs:ignore

		$user_id = get_current_user_id();

		$user_meta = '';

		if ( ! empty( $tr_details ) && 0 != $user_id ) {

			$user_meta = update_user_meta( $user_id, 'members_tnx_details', $tr_details );
		}

		if ( $user_meta ) {
			echo wp_json_encode(
				array(
					'status'  => true,
					'user_id' => $user_id,
				)
			);
			wp_die();
		} else {
			echo wp_json_encode(
				array(
					'status'  => false,
					'user_id' => 'User ID does not exist.',
				)
			);
			wp_die();
		}

	}

	/**
	 * Giving products/features access to members.
	 *
	 * @param object $product Product object.
	 * @since 1.0.0
	 */
	public function is_accessible_to_member( $product ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$access = false;
		$all_member_plans = array();
		$all_member_category = array();
		$all_member_tag = array();

		if ( ! empty( $product ) ) {

			$exclude = wps_membership_get_meta_data( $product->get_id(), '_wps_membership_exclude', true );

			if ( 'yes' === $exclude ) {
				$access = true;
				return $access;
			}
			$user_id = get_current_user_id();

			$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );

			if ( ! empty( $current_memberships && is_array( $current_memberships ) ) ) {

				foreach ( $current_memberships as $key => $membership_id ) {

					if ( 'publish' == get_post_status( $membership_id ) || 'draft' == get_post_status( $membership_id ) ) {
						$membership_status = wps_membership_get_meta_data( $membership_id, 'member_status', true );

						// Get Saved Plan Details.
						$membership_plan = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
						if ( ! empty( $membership_plan->ID ) ) {
							array_push( $all_member_plans, $membership_plan->ID );
						}
						$membership_status = wps_membership_get_meta_data( $membership_id, 'member_status', true );

						$accessible_prod = ! empty( $membership_plan['wps_membership_plan_target_ids'] ) ? maybe_unserialize( $membership_plan['wps_membership_plan_target_ids'] ) : array();
						$accessible_cat  = ! empty( $membership_plan['wps_membership_plan_target_categories'] ) ? maybe_unserialize( $membership_plan['wps_membership_plan_target_categories'] ) : array();
						$accessible_tag  = ! empty( $membership_plan['wps_membership_plan_target_tags'] ) ? maybe_unserialize( $membership_plan['wps_membership_plan_target_tags'] ) : array();

						if ( in_array( $product->get_id(), $accessible_prod ) || ( ! empty( $accessible_cat ) && has_term( $accessible_cat, 'product_cat' ) ) || ( ! empty( $accessible_tag ) && has_term( $accessible_tag, 'product_tag' ) ) ) {

							$access = true;

							$membership_status = wps_membership_get_meta_data( $membership_id, 'member_status', true );

							if ( ! empty( $membership_status ) && in_array( $membership_status, array( 'complete' ) ) ) {
								$access = true;

								array_push( $all_member_tag, $product->get_id() );

							}
							if ( ! empty( $membership_status ) && in_array( $membership_status, array( 'expired' ) ) ) {
								$access = false;

							} elseif ( 'pending' == $membership_status || 'hold' == $membership_status && 'publish' == $membership_plan['post_status'] ) {

								$this->under_review_products = $this->under_review_products ? $this->under_review_products : array();
								array_push( $this->under_review_products, $product->get_id() );
								array_unique( $this->under_review_products, $product->get_id() );
								$access = true;

							}
						} else {
							$access = false;
						}
					}
				}
			}

			foreach ( $all_member_tag as $memeber_tag_key => $memeber_tag_value ) {

				foreach ( array_keys( $this->under_review_products, $memeber_tag_value ) as $keys ) {
					unset( $this->under_review_products[ $keys ] );
				}
			}

			if ( ! empty( $all_member_plans ) ) {
				$args = array(
					'post_type'   => 'wps_cpt_membership',
					'post_status' => array( 'publish' ),
					'numberposts' => -1,
				);

				$check = '';
				$all_plan_array = array();
				$all_plans = get_posts( $args );
				foreach ( $all_plans as $single_plan ) { /* phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited */
					 array_push( $all_plan_array, $single_plan->ID );
				}

				if ( ! in_array( $product->get_id(), (array) $accessible_prod ) ) {

					if ( ! in_array( $product->get_id(), (array) $this->under_review_products ) ) {

						foreach ( $all_plans as $single_plan ) { /* phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited */
							if ( 'publish' == $single_plan->post_status ) {

								if ( ! in_array( $single_plan->ID, (array) $all_member_plans ) ) {
									$all_plan_accessible_prod = $single_plan->wps_membership_plan_target_ids ? maybe_unserialize( $single_plan->wps_membership_plan_target_ids ) : array();

									$all_plan_accessible_cat  = $single_plan->wps_membership_plan_target_categories ? maybe_unserialize( $single_plan->wps_membership_plan_target_categories ) : array();

									$all_plan_accessible_tag  = $single_plan->wps_membership_plan_target_tags ? maybe_unserialize( $single_plan->wps_membership_plan_target_tags ) : array();
								}
							}
						}
						if ( in_array( $product->get_id(), $all_plan_accessible_prod ) || ( ! empty( $all_plan_accessible_cat ) && has_term( $all_plan_accessible_cat, 'product_cat' ) ) || ( ! empty( $all_plan_accessible_tag ) && has_term( $all_plan_accessible_tag, 'product_tag' ) ) ) {

							$access = true;

							$membership_status = wps_membership_get_meta_data( $membership_id, 'member_status', true );

							$this->another_plan_products = $this->another_plan_products ? $this->another_plan_products : array();
							array_push( $this->another_plan_products, $product->get_id() );
							array_unique( $this->another_plan_products, $product->get_id() );

							$access = true;

						} else {
							$access = false;
						}
					}
				}
			}
		}

		/**
		 * Filter to access member.
		 *
		 * @since 1.0.0
		 */
		$access = apply_filters( 'is_accessible_to_member', $access );
		return $access;
	}

	/**
	 * Add discount on cart as per membership plan.
	 *
	 * @param object $cart Current Cart object.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_add_cart_discount( $cart ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		// get cart total and minus cart total.
		$cart_total = $cart->subtotal;
		$cart_tax   = ! empty( $cart->tax_total ) ? $cart->tax_total : 0;
		$cart_total = (float) $cart_total - $cart_tax;

		$user_id                        = get_current_user_id();
		$discount_fixed                 = '';
		$applied_offer_price_percentage = array();
		$applied_offer_price_fixed      = array();
		$plan_existing                  = false;
		$data                           = $this->custom_query_data;
		$existing_plan_id               = array();
		$current_memberships            = get_user_meta( $user_id, 'mfw_membership_id', true );

		if ( ! empty( $current_memberships ) && is_array( $current_memberships ) ) {

			foreach ( $current_memberships as $key => $membership_id ) {

				$member_status = wps_membership_get_meta_data( $membership_id, 'member_status', true );

				if ( ! empty( $member_status ) && 'complete' == $member_status ) {

					$active_plan = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
					if ( empty( $active_plan ) ) {
						continue;
					}
					$plan_existing = true;
					$club_membership = $this->get_all_included_membership( $active_plan['ID'] );
					if ( ! empty( $club_membership ) ) {
						$existing_plan_id = array_merge( $existing_plan_id, $club_membership );
					}
					if ( ! empty( $active_plan['ID'] ) ) {
						array_push( $existing_plan_id, $active_plan['ID'] );
					}
				}
			}
		}

		if ( true == $plan_existing ) {

			if ( ! empty( $data ) && is_array( $data ) ) {

				foreach ( $data as $plan ) {

					if ( in_array( $plan['ID'], $existing_plan_id ) ) {

						$offer_type = wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_offer_price_type', true );
						$offer_price = wps_membership_get_meta_data( $plan['ID'], 'wps_memebership_plan_discount_price', true );

						if ( '%' == $offer_type ) {
							array_push( $applied_offer_price_percentage, floatval( $offer_price ) );
						} else {
							array_push( $applied_offer_price_fixed, floatval( $offer_price ) );
						}
					}
				}
			}
		}

		$discount_percentage                    = 0;
		$discount_fixed                         = 0;
		$applied_offer_price_percentage_on_cart = 0;
		$applied_offer_price_fixed_on_cart      = 0;
		if ( ! empty( $applied_offer_price_percentage ) ) {

			// Discount % is given( no negatives, not more than 100, if 100% then price zero ).
			$applied_offer_price_percentage_on_cart = max( $applied_offer_price_percentage );

			// Range should be 0-100 only.
			$applied_offer_price_percentage_on_cart = ( 100 < $applied_offer_price_percentage_on_cart ) ? 100 : $applied_offer_price_percentage_on_cart;
			$applied_offer_price_percentage_on_cart = ( 0 > $applied_offer_price_percentage_on_cart ) ? 0 : $applied_offer_price_percentage_on_cart;

			$discount_percentage = floatval( $cart_total * ( $applied_offer_price_percentage_on_cart / 100 ) );
		}

		// If fixed discount is given.
		if ( ! empty( $applied_offer_price_fixed ) ) {
			// When fixed price is given.
			$applied_offer_price_fixed_on_cart = max( $applied_offer_price_fixed );

			$applied_offer_price_fixed_on_cart = ( 0 > $applied_offer_price_fixed_on_cart ) ? 0 : $applied_offer_price_fixed_on_cart;

			$discount_fixed = floatval( $applied_offer_price_fixed_on_cart );

		}

		if ( ! empty( $discount_percentage ) || ! empty( $discount_fixed ) ) {

			if ( $discount_percentage > $discount_fixed ) {
				$discount = $discount_percentage;
			} else {
				$discount = $applied_offer_price_fixed_on_cart;
			}

			if ( ! empty( $discount_fixed ) ) {
				if ( function_exists( 'wps_mmcsfw_admin_fetch_currency_rates_from_base_currency' ) ) {
					$discount = wps_mmcsfw_admin_fetch_currency_rates_from_base_currency( '', $discount );
				}
			}
			$cart->add_fee( 'Membership Discount', -$discount, false );
		}
	}

	/**
	 * Check membership expiration on daily basis.
	 */
	public function wps_membership_cron_expiry_check() {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		// Get all limited memberships.
		$delay_members = get_posts(
			array(
				'numberposts' => -1,
				'fields'      => 'ids', // return only ids.
				'post_type'   => 'wps_cpt_members',
				'order'       => 'ASC',
				'meta_query'  => array(
					array(
						'relation' => 'AND',
						array(
							'key'     => 'member_expiry',
							'compare' => 'EXISTS',
						),
					),
				),
			)
		);

		if ( ! empty( $delay_members ) && is_array( $delay_members ) && count( $delay_members ) ) {
			$user_id = '';
			$user_name = '';

			foreach ( $delay_members as $member_id ) {

				$plan_obj = wps_membership_get_meta_data( $member_id, 'plan_obj', true );
				$member_status = wps_membership_get_meta_data( $member_id, 'member_status', true );
				$order = new WC_Order( wps_membership_get_meta_data( $member_id, 'member_order_id', true ) );
				$order_status = $order->status;

				if ( 'pending' == $member_status ) {

					// Save expiry date in post.
					if ( ! empty( $plan_obj ) ) {
						// Getting current activation date.

						$access_type = wps_membership_get_meta_data( $plan_obj['ID'], 'wps_membership_plan_access_type', true );
						if ( 'delay_type' == $access_type ) {
								$time_duration      = wps_membership_get_meta_data( $plan_obj['ID'], 'wps_membership_plan_time_duration', true );
								$time_duration_type = wps_membership_get_meta_data( $plan_obj['ID'], 'wps_membership_plan_time_duration_type', true );

								$delay_date = wps_membership_get_meta_data( $member_id, 'membership_delay_date', true );
								$expiry_date = wps_membership_get_meta_data( $member_id, 'member_expiry', true );
							// Getting current activation date.
							$current_date = gmdate( 'Y-m-d' );
							if ( $current_date >= $delay_date ) {

								if ( 'completed' == $order_status ) {
									wps_membership_update_meta_data( $member_id, 'member_status', 'complete' );
									$order_id = wps_membership_get_meta_data( $member_id, 'member_order_id', true );
									$plan = wps_membership_get_meta_data( $member_id, 'plan_obj', true );
									if ( 'yes' == $plan['wps_membership_subscription'] ) {
										$subscription_i_d = wps_membership_get_meta_data( $order_id, 'wps_subscription_id', true );
										if ( ! empty( $subscription_i_d ) ) {
											wps_membership_update_meta_data( $subscription_i_d, 'wps_subscription_status', 'active' );
											wps_membership_update_meta_data( $subscription_i_d, 'wps_next_payment_date', $expiry_date );
											if ( ! empty( $plan['wps_membership_subscription_expiry'] ) ) {
												if ( function_exists( 'wps_sfw_susbcription_expiry_date' ) ) {
													$current_time = current_time( 'timestamp' );
													$wps_susbcription_end = wps_sfw_susbcription_expiry_date( $subscription_i_d, $current_time );
													wps_membership_update_meta_data( $subscription_i_d, 'wps_susbcription_end', $wps_susbcription_end );

												}
											} else {
													wps_membership_update_meta_data( $subscription_i_d, 'wps_susbcription_end', '' );
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		// Get all limited memberships.
		$limited_members = get_posts(
			array(
				'numberposts' => -1,
				'fields'      => 'ids', // return only ids.
				'post_type'   => 'wps_cpt_members',
				'order'       => 'ASC',
				'meta_query'  => array(
					array(
						'relation' => 'AND',
						array(
							'key'     => 'member_expiry',
							'compare' => 'EXISTS',
						),
						array(
							'key'     => 'member_expiry',
							'value'   => 'Lifetime',
							'compare' => '!=',
						),
					),
				),
			)
		);

		if ( ! empty( $limited_members ) && is_array( $limited_members ) && count( $limited_members ) ) {
			$user_id = '';
			$user_name = '';

			foreach ( $limited_members as $member_id ) {
				$member_status = wps_membership_get_meta_data( $member_id, 'member_status', true );
				$post   = get_post( $member_id );
				$user = get_userdata( $post->post_author );
				$expiry_date = wps_membership_get_meta_data( $member_id, 'member_expiry', true );
				$plan_obj = wps_membership_get_meta_data( $member_id, 'plan_obj', true );
				$today_date = gmdate( 'Y-m-d' );
				$current_date = time();
				$order = new WC_Order( wps_membership_get_meta_data( $member_id, 'member_order_id', true ) );
				$order_status = $order->status;
				$order_id = wps_membership_get_meta_data( $member_id, 'member_order_id', true );
				$expiry_mail = gmdate( 'Y-m-d', strtotime( $expiry_date ) );
				$expiry = wps_membership_get_meta_data( $member_id, 'member_expiry', true );

				if ( 'Lifetime' == $expiry ) {
					$expiry_mail = 'Lifetime';
				} else {
					$expiry_mail = esc_html( ! empty( $expiry ) ? gmdate( 'Y-m-d', $expiry ) : '' );
				}

				$number_of_day_to_send_expiry_mail = get_option( 'wps_membership_number_of_expiry_days' );
				$expiry_current = gmdate( 'Y-m-d', strtotime( $expiry_mail . '- ' . $number_of_day_to_send_expiry_mail . ' day' ) );

				if ( 'complete' == $member_status ) {

					if ( $today_date >= $expiry_current ) {

						$user_name      = $user->data->display_name;
						$customer_email = WC()->mailer()->emails['membership_to_expire_email'];

						if ( ! empty( $customer_email ) ) {
							$email_status = $customer_email->trigger( $post->post_author, $member_id, $user_name, $expiry_mail, $plan_obj, $order_id );

						}
					}
				}

					// Set member status to Expired.

				if ( $today_date >= $expiry_mail ) {

					if ( 'complete' == $member_status ) {

						$plan = wps_membership_get_meta_data( $member_id, 'plan_obj', true );
						$is_subscription = $plan['wps_membership_subscription'];

						update_option( 'xfgxfg' . $member_id, $is_subscription . '--' );

						if ( 'yes' != $is_subscription ) {
							wps_membership_update_meta_data( $member_id, 'member_status', 'expired' );
						}
						$customer_email = '';
						if ( ! empty( WC()->mailer()->emails['membership_expired_email'] ) ) {
							$customer_email = WC()->mailer()->emails['membership_expired_email'];
						}

						$expiry_mail = gmdate( 'Y-m-d', strtotime( $expiry_date ) );

						$expiry = wps_membership_get_meta_data( $member_id, 'member_expiry', true );

						if ( 'Lifetime' == $expiry ) {
							$expiry_mail = 'Lifetime';
						} else {
							$expiry_mail = esc_html( ! empty( $expiry ) ? gmdate( 'Y-m-d', $expiry ) : '' );
						}
						if ( ! empty( $customer_email ) ) {
							$email_status = $customer_email->trigger( $post->post_author, $member_id, $user_name, $expiry_mail, $plan_obj, $order_id );
						}
					}
				}
			}
		}

		// Expired memberships.
		$expired_members = get_posts(
			array(
				'numberposts' => -1,
				'fields'      => 'ids', // return only ids.
				'post_type'   => 'wps_cpt_members',
				'order'       => 'ASC',
				'meta_query'  => array(
					array(
						'relation' => 'AND',
						array(
							'key'     => 'member_status',
							'compare' => 'EXISTS',
						),
						array(
							'key'     => 'member_status',
							'value'   => 'expired',
							'compare' => '==',
						),
					),
				),
			)
		);

		$already_processed_users = array();
		if ( ! empty( $expired_members ) && is_array( $expired_members ) && count( $expired_members ) ) {

			foreach ( $expired_members as $key => $id ) {

				$author_id = get_post_field( 'post_author', $id );
				$user      = get_user_by( 'id', $author_id );

				if ( false !== $user ) {

					// If already processed then ignore.
					if ( in_array( $author_id, $already_processed_users ) ) {
						continue;
					}
					$other_member_exists = false;
					$memberships = get_user_meta( $author_id, 'mfw_membership_id', true );

					array_push( $already_processed_users, $author_id );

					foreach ( $memberships as $key => $m_id ) {

						$status = wps_membership_get_meta_data( $m_id, 'member_status', true );

						if ( 'complete' == $status ) {

							$other_member_exists = true;
						}
					}

					if ( 1 == count( $memberships ) ) {
						if ( false == $other_member_exists ) {
							update_user_meta( $author_id, 'is_member', '' );
						}
					} else {

						$remove_role = true;

						foreach ( $memberships as $key => $m_id ) {

							$status = wps_membership_get_meta_data( $m_id, 'member_status', true );

							if ( 'expired' != $status ) {

								$remove_role = false;
								break;
							}
						}

						// If removal required then remove role.
						if ( false == $other_member_exists ) {
							update_user_meta( $author_id, 'is_member', '' );
						}
					}
				}
			}
		}
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
		$product = wc_get_product( $wps_membership_default_product );

		global $wp_session;

		$wp_session['plan_price'] = $plan_price;
		$wp_session['plan_title'] = $plan_title;
		$wp_session['plan_id'] = $plan_id;
		WC()->session->set( 'plan_id', $plan_id );
		$cart_item_data = add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_membership_product_price_to_cart_item_data' ), 10, 2 );

		$redirect_url = ( $cart_item_data ) ? wc_get_checkout_url() : wc_get_cart_url();
		echo wp_json_encode( $redirect_url );
		wp_die();
	}

	/**
	 * Function of callback on form submission.
	 *
	 * @return void
	 */
	public function wps_mfw_registration_form_submission_callback() {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		if ( isset( $_POST['wps_regiser_form_submit'] ) ) {
			$value_check = isset( $_POST['wps_nonce_name'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_nonce_name'] ) ) : '';
			wp_verify_nonce( $value_check, 'wps-form-nonce' );

			$plan_id = isset( $_POST['wps_register_form_plan'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_plan'] ) ) : '';
			$plan_title = get_the_title( $plan_id );
			$plan_price = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_price', true );
			$wps_fname = isset( $_POST['wps_register_form_fname'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_fname'] ) ) : '';
			$wps_lname = isset( $_POST['wps_register_form_lname'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_lname'] ) ) : '';
			$wps_country = isset( $_POST['wps_register_form_country'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_country'] ) ) : '';
			$wps_address1 = isset( $_POST['wps_register_form_address1'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_address1'] ) ) : '';
			$wps_city = isset( $_POST['wps_register_form_city'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_city'] ) ) : '';
			$wps_pincode = isset( $_POST['wps_register_form_pincode'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_pincode'] ) ) : '';
			$wps_phone = isset( $_POST['wps_register_form_phone_no'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_phone_no'] ) ) : '';
			$wps_email = isset( $_POST['wps_register_form_email'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_email'] ) ) : '';
			$wps_state = isset( $_POST['wps_register_form_state'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_state'] ) ) : '';

			$wps_membership_default_product = get_option( 'wps_membership_default_product', '' );

			global $wp_session;

			$wp_session['plan_price'] = $plan_price;
			$wp_session['plan_title'] = $plan_title;
			$wp_session['plan_id']    = $plan_id;
			WC()->session->set( 'wps_fname', $wps_fname );
			WC()->session->set( 'wps_lname', $wps_lname );
			WC()->session->set( 'wps_country', $wps_country );
			WC()->session->set( 'wps_address1', $wps_address1 );
			WC()->session->set( 'wps_city', $wps_city );
			WC()->session->set( 'wps_pincode', $wps_pincode );
			WC()->session->set( 'wps_phone', $wps_phone );
			WC()->session->set( 'wps_email', $wps_email );
			WC()->session->set( 'wps_state', $wps_state );
			WC()->session->set( 'plan_id', $plan_id );
			WC()->session->set( 'plan_title', $plan_title );
			WC()->session->set( 'plan_price', $plan_price );
			WC()->session->set( 'product_id', $wps_membership_default_product );
			WC()->session->set( 'form_submit', 'yes' );
		}
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
		global $wp_session;

		if ( empty( $wp_session ) ) {
			$cart_item_data['plan_price'] = WC()->session->get( 'plan_price' );
			$cart_item_data['plan_title'] = WC()->session->get( 'plan_title' );
			$cart_item_data['plan_id'] = WC()->session->get( 'plan_id' ); // In case of subscription.
		} else {
			$cart_item_data['plan_price'] = $wp_session['plan_price'];
			$cart_item_data['plan_title'] = $wp_session['plan_title'];
			$cart_item_data['plan_id'] = $wp_session['plan_id']; // In case of subscription.
		}

		if ( WC()->session->__isset( 'form_submit' ) ) {
			$cart_item_data['form_submit'] = 'yes';
			$cart_item_data['wps_fname'] = WC()->session->get( 'wps_fname' );
			$cart_item_data['wps_lname'] = WC()->session->get( 'wps_lname' );
			$cart_item_data['wps_country'] = WC()->session->get( 'wps_country' );
			$cart_item_data['wps_address1'] = WC()->session->get( 'wps_address1' );
			$cart_item_data['wps_city'] = WC()->session->get( 'wps_city' );
			$cart_item_data['wps_pincode'] = WC()->session->get( 'wps_pincode' );
			$cart_item_data['wps_phone'] = WC()->session->get( 'wps_phone' );
			$cart_item_data['wps_email'] = WC()->session->get( 'wps_email' );
			$cart_item_data['wps_state'] = WC()->session->get( 'wps_state' );
			WC()->session->__unset( 'wps_fname' );
			WC()->session->__unset( 'wps_lname' );
			WC()->session->__unset( 'wps_country' );
			WC()->session->__unset( 'wps_address1' );
			WC()->session->__unset( 'wps_city' );
			WC()->session->__unset( 'wps_pincode' );
			WC()->session->__unset( 'wps_phone' );
			WC()->session->__unset( 'wps_email' );
			WC()->session->__unset( 'wps_state' );
		}

		/**
		 * Filter for cart item.
		 *
		 * @since 1.0.0
		 */
		$cart_item_data = apply_filters( 'add_membership_product_price_to_cart_item_data', $cart_item_data );
		return $cart_item_data;
	}

	/**
	 * Set topup product price at run time.
	 *
	 * @param OBJECT $cart cart.
	 */
	public function wps_membership_set_membership_product_price( $cart ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$wps_membership_default_product = get_option( 'wps_membership_default_product', '' );

		$product = wc_get_product( $wps_membership_default_product );

		if ( ! $product && empty( $cart->cart_contents ) ) {
			return;
		}

		if ( ! empty( $product ) ) {

			foreach ( $cart->cart_contents as $cart_contents_key => $cart_contents_value ) {
				$wps_attached_plan_id = wps_membership_get_meta_data( $cart_contents_value['product_id'], 'wps_membership_plan_with_product', true );
				if ( isset( $cart_contents_value['plan_price'] ) && $cart_contents_value['plan_price'] && $product->get_id() == $cart_contents_value['product_id'] ) {
					$cart_contents_value['data']->set_price( $cart_contents_value['plan_price'] );
				}

				if ( $product->get_id() == $cart_contents_value['product_id'] ) {
					if ( key_exists( 'plan_id', $cart_contents_value ) ) {

						$wps_sfw_product = wps_membership_get_meta_data( $cart_contents_value['plan_id'], 'wps_membership_subscription', true );
					}

					if ( ! empty( $wps_sfw_product ) && 'yes' == $wps_sfw_product ) {

						$wps_membership_plan_name_access_type = wps_membership_get_meta_data( $cart_contents_value['plan_id'], 'wps_membership_plan_name_access_type', true );

						if ( 'limited' == $wps_membership_plan_name_access_type ) {
							$wps_membership_plan_duration = wps_membership_get_meta_data( $cart_contents_value['plan_id'], 'wps_membership_plan_duration', true );
							$wps_membership_plan_duration_type = wps_membership_get_meta_data( $cart_contents_value['plan_id'], 'wps_membership_plan_duration_type', true );

							$wps_membership_subscription_expiry = wps_membership_get_meta_data( $cart_contents_value['plan_id'], 'wps_membership_subscription_expiry', true );
							$wps_membership_subscription_expiry_type = wps_membership_get_meta_data( $cart_contents_value['plan_id'], 'wps_membership_subscription_expiry_type', true );

							wps_membership_update_meta_data( $wps_membership_default_product, '_wps_sfw_product', $wps_sfw_product );

							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_number', intval( $wps_membership_plan_duration ) );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_interval', substr( $wps_membership_plan_duration_type, 0, -1 ) );

							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_number', intval( $wps_membership_subscription_expiry ) );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_interval', $wps_membership_subscription_expiry_type );

							wps_membership_update_meta_data( $wps_membership_default_product, '_regular_price', $cart_contents_value['plan_price'] );

						}
					} else {
						wps_membership_update_meta_data( $wps_membership_default_product, '_wps_sfw_product', 'no' );

						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_number', '' );
						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_interval', '' );

						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_number', '' );
						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_interval', '' );

					}
				} else if ( $wps_attached_plan_id ) {
					$wps_membership_default_product = $cart_contents_value['product_id'];

						$wps_sfw_product = wps_membership_get_meta_data( $wps_attached_plan_id, 'wps_membership_subscription', true );

					if ( ! empty( $wps_sfw_product ) && 'yes' == $wps_sfw_product ) {

						$wps_membership_plan_name_access_type = wps_membership_get_meta_data( $wps_attached_plan_id, 'wps_membership_plan_name_access_type', true );

						if ( 'limited' == $wps_membership_plan_name_access_type ) {
							$wps_membership_plan_duration = wps_membership_get_meta_data( $wps_attached_plan_id, 'wps_membership_plan_duration', true );
							$wps_membership_plan_duration_type = wps_membership_get_meta_data( $wps_attached_plan_id, 'wps_membership_plan_duration_type', true );

							$wps_membership_subscription_expiry = wps_membership_get_meta_data( $wps_attached_plan_id, 'wps_membership_subscription_expiry', true );
							$wps_membership_subscription_expiry_type = wps_membership_get_meta_data( $wps_attached_plan_id, 'wps_membership_subscription_expiry_type', true );

							wps_membership_update_meta_data( $wps_membership_default_product, '_wps_sfw_product', $wps_sfw_product );

							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_number', intval( $wps_membership_plan_duration ) );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_interval', substr( $wps_membership_plan_duration_type, 0, -1 ) );

							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_number', intval( $wps_membership_subscription_expiry ) );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_interval', $wps_membership_subscription_expiry_type );

						}
					} else {
						wps_membership_update_meta_data( $wps_membership_default_product, '_wps_sfw_product', 'no' );

						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_number', '' );
						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_interval', '' );

						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_number', '' );
						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_interval', '' );

					}
				}

				if ( isset( $cart_contents_value['plan_title'] ) && $cart_contents_value['plan_title'] && $product->get_id() == $cart_contents_value['product_id'] ) {

					// Set the new name (WooCommerce versions 2.5.x to 3+).
					if ( method_exists( $cart_contents_value['data'], 'set_name' ) ) {
						$cart_contents_value['data']->set_name( $cart_contents_value['plan_title'] );

					} else {
						$cart_contents_value['data']->post->post_title = $cart_contents_value['plan_title'];
					}
				}
			}
		}
	}

	/**
	 * Function to get all included membership.
	 *
	 * @param mixed $active_plan_id is the currenct plan id.
	 * @return array
	 */
	public function get_all_included_membership( $active_plan_id ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$included_membership = array();
		$club_membership     = wps_membership_get_meta_data( $active_plan_id, 'wps_membership_club', true );
		if ( ! empty( $club_membership ) && is_array( $club_membership ) ) {
			foreach ( $club_membership as $club_membership_key => $club_membership_value ) {

				array_push( $included_membership, $club_membership_value );
			}
		}
		return $included_membership;
	}

	/**
	 * Make rechargeable product purchasable
	 *
	 * @param boolean $is_purchasable allow product to be purchased.
	 * @param mixed   $product object of product.
	 * @return boolean
	 */
	public function wps_membership_make_membership_product_purchasable( $is_purchasable, $product ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return $is_purchasable;
		}
		$wps_membership_default_product = get_option( 'wps_membership_default_product', '' );
		$page_link = '';
		$membership_product = wc_get_product( $wps_membership_default_product );
		if ( $membership_product ) {
			if ( $wps_membership_default_product == $product->get_id() ) {

				$is_purchasable = true;
				return $is_purchasable;
			}
		}

		/**
		 * Filter for purchasable product.
		 *
		 * @since 1.0.0
		 */
		$is_purchasable = apply_filters( 'add_membership_product_price_to_is_purchasable', $is_purchasable );
		$is_product_exclude = false;
		$user_id = 0;
		$is_member_meta = array();
		if ( is_user_logged_in() ) {
			$user_id               = get_current_user_id();
			$is_member_meta = get_user_meta( $user_id, 'is_member', true );
		}
			$data                  = $this->custom_query_data;
			$existing_plan_id      = array();
			$existing_plan_product = array();
			$plan_existing         = false;
			$current_memberships   = get_user_meta( $user_id, 'mfw_membership_id', true );

		if ( ! empty( $current_memberships ) && is_array( $current_memberships ) ) {

			foreach ( $current_memberships as $key => $membership_id ) {

				$member_status = wps_membership_get_meta_data( $membership_id, 'member_status', true );

				if ( ! empty( $member_status ) && 'complete' == $member_status ) {

					$active_plan = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
					if ( empty( $active_plan ) && ! is_array( $active_plan ) ) {
						continue;
					}
					$club_membership = $this->get_all_included_membership( $active_plan['ID'] );
					if ( ! empty( $club_membership ) ) {
						$existing_plan_id = array_merge( $existing_plan_id, $club_membership );
					}

					if ( ! empty( $active_plan['ID'] ) ) {
						array_push( $existing_plan_id, $active_plan['ID'] );
					}

					$target_ids      = ! empty( wps_membership_get_meta_data( $active_plan['ID'], 'wps_membership_plan_target_ids', true ) ) ? wps_membership_get_meta_data( $active_plan['ID'], 'wps_membership_plan_target_ids', true ) : array();
					$target_cat_ids  = ! empty( wps_membership_get_meta_data( $active_plan['ID'], 'wps_membership_plan_target_categories', true ) ) ? wps_membership_get_meta_data( $active_plan['ID'], 'wps_membership_plan_target_categories', true ) : array();
					$target_tag_ids  = ! empty( wps_membership_get_meta_data( $active_plan['ID'], 'wps_membership_plan_target_tags', true ) ) ? wps_membership_get_meta_data( $active_plan['ID'], 'wps_membership_plan_target_tags', true ) : array();

					$product_terms = $this->get_product_terms( $product->get_id() );

					if ( ! empty( $product_terms ) ) {
						foreach ( $product_terms as $product_terms_key => $product_terms_value ) {
							if ( in_array( $product_terms_value, (array) $target_tag_ids ) ) {
								array_push( $existing_plan_product, $product->get_id() );
							}
						}
					}

					if ( in_array( $product->get_id(), $target_ids ) || ( ! empty( $target_cat_ids ) && has_term( $target_cat_ids, 'product_cat' ) ) ) {
						array_push( $existing_plan_product, $product->get_id() );
					}
				}
			}
		}

		if ( false == $plan_existing ) {

			if ( ! empty( $data ) && is_array( $data ) ) {

				foreach ( $data as $plan ) {
					$wps_membership_default_plans_page_id = get_option( 'wps_membership_default_plans_page', '' );

					if ( ! empty( $wps_membership_default_plans_page_id ) && 'publish' == get_post_status( $wps_membership_default_plans_page_id ) ) {
						$page_link = get_page_link( $wps_membership_default_plans_page_id );
					}

					$exclude_product = array();

					/**
					 * Filter for exclude product.
					 *
					 * @since 1.0.0
					 */
					$exclude_product = apply_filters( 'wps_membership_exclude_product', $exclude_product, $product->get_id() );

					/**
					 * Filter for exclude product.
					 *
					 * @since 1.0.0
					 */
					$is_product_exclude = apply_filters( 'wps_membership_is_exclude_product', $exclude_product, $data, $is_product_exclude );

					if ( $is_product_exclude ) {
						break;
					}

					if ( ! empty( $exclude_product ) ) {
						if ( in_array( $plan['ID'], $exclude_product ) ) {
							break;
						}
					}

					if ( ! in_array( $plan['ID'], $existing_plan_id ) ) {

						$page_link_found = false;
						$target_ids      = ! empty( wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_ids', true ) ) ? wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_ids', true ) : array();
						$target_cat_ids  = ! empty( wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_categories', true ) ) ? wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_categories', true ) : array();
						$target_tag_ids  = ! empty( wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_tags', true ) ) ? wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_tags', true ) : array();

						if ( $target_cat_ids ) {
							foreach ( $target_cat_ids as $cat_id ) {
								$term = get_the_category_by_ID( $cat_id );
								$products = $this->get_category_query( $cat_id, 'product_cat' );
								$target_ids = array_merge( $target_ids, $products );
							}
						}

						$product_terms = $this->get_product_terms( $product->get_id() );

						if ( ! empty( $product_terms ) ) {
							foreach ( $product_terms as $product_terms_key => $product_terms_value ) {
								if ( in_array( $product_terms_value, (array) $target_tag_ids ) ) {
									array_push( $target_ids, $product->get_id() );
								}
							}
						}

						if ( in_array( $product->get_id(), $target_ids ) || ( ! empty( $target_cat_ids ) && has_term( $target_cat_ids, 'product_cat' ) ) ) {

							if ( ! in_array( $product->get_id(), $existing_plan_product ) ) {
								$is_purchasable = false;

								if ( $product->is_type( 'variable' ) ) {
									$product = wc_get_product( $product->get_id() );
									$current_products = $product->get_children();
									foreach ( $current_products as $key => $current_products_value ) {
										array_push( $this->exclude_other_plan_products, $current_products_value );
									}
								}
							}
						}
					}
				}
			}
		}

		if ( in_array( $product->get_id(), $this->exclude_other_plan_products ) ) {
			$is_purchasable = false;
		}

		return $is_purchasable;
	}

	/**
	 * Get product data through query.
	 *
	 * @param mixed $post_type type of post.
	 * @param mixed $taxonomy taxonomy.
	 * @param mixed $term terms.
	 */
	public function get_product_query( $post_type, $taxonomy, $term ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$products = new WP_Query(
			array(
				'post_type'   => $post_type,
				'post_status' => 'publish',
				'fields'      => 'ids',
				'tax_query'   => array(
					'relation' => 'AND',
					array(
						'taxonomy' => $taxonomy,
						'field'    => 'term_id',
						'terms'    => $term,
					),
				),
			)
		);

		/**
		 * Filter for product gallery.
		 *
		 * @since 1.0.0
		 */
		$products = apply_filters( 'get_product_query', $products );

		return $products;
	}

	/**
	 * Get product data through query.
	 *
	 * @param mixed $cat_id category id.
	 * @param mixed $taxonomy taxonomy.
	 */
	public function get_category_query( $cat_id, $taxonomy ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$all_ids = get_posts(
			array(
				'post_type' => 'product',
				'numberposts' => -1,
				'post_status' => 'publish',
				'fields' => 'ids',
				'tax_query' => array(
					array(
						'taxonomy' => $taxonomy,
						'terms'    => $cat_id, /*category name*/
					),
				),
			)
		);

		/**
		 * Filter for cat query.
		 *
		 * @since 1.0.0
		 */
		$all_ids = apply_filters( 'get_category_query', $all_ids );

		return $all_ids;
	}

	/**
	 * Add to cart.
	 *
	 * @return void
	 */
	public function wps_membership_buy_now_add_to_cart() {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		if ( WC()->session->__isset( 'product_id' ) ) {
			$product_id = WC()->session->get( 'product_id' );

			// check if product already in cart.
			if ( count( WC()->cart->get_cart() ) > 0 ) {

				$found = false;
				foreach ( WC()->cart->get_cart() as $cart_item ) {
					$product_in_cart = $cart_item['product_id'];
					if ( $product_in_cart == $product_id ) {
						$found = true;
					}
				}
				// if product not found, add it.
				if ( ! $found ) {

					add_action( 'woocommerce_before_cart', array( $this, 'add_cart_custom_notice' ) );
					WC()->session->__unset( 'product_id' );
				} else {
					$cart_item_data = add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_membership_product_price_to_cart_item_data' ), 10, 2 );
					WC()->cart->empty_cart();
					// if no products in cart, add it.
					WC()->cart->add_to_cart( $product_id );
					if ( WC()->session->__isset( 'form_submit' ) ) {

						wp_safe_redirect( wc_get_checkout_url() );
					} else {
						wp_safe_redirect( wc_get_cart_url() );
					}
				}
			} else {
				$cart_item_data = add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_membership_product_price_to_cart_item_data' ), 10, 2 );
				WC()->cart->empty_cart();
				// if no products in cart, add it.
				WC()->cart->add_to_cart( $product_id );
				if ( WC()->session->__isset( 'form_submit' ) ) {

					wp_safe_redirect( wc_get_checkout_url() );
				} else {
					wp_safe_redirect( wc_get_cart_url() );
				}
			}
			WC()->session->__unset( 'product_id' );
			WC()->session->__unset( 'form_submit' );
			WC()->session->__unset( 'wps_fname' );
			WC()->session->__unset( 'wps_lname' );
			WC()->session->__unset( 'wps_country' );
			WC()->session->__unset( 'wps_address1' );
			WC()->session->__unset( 'wps_city' );
			WC()->session->__unset( 'wps_pincode' );
			WC()->session->__unset( 'wps_phone' );
			WC()->session->__unset( 'wps_email' );
			WC()->session->__unset( 'wps_state' );
		}

		if ( is_cart() ) {
			if ( 1 < WC()->cart->get_cart_contents_count() ) {
				$wps_store_cart_prouduct_id = array();
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

					array_push( $wps_store_cart_prouduct_id, $cart_item['product_id'] );
				}

				$wps_membership_default_product = get_option( 'wps_membership_default_product' );
				if ( in_array( $wps_membership_default_product, $wps_store_cart_prouduct_id ) ) {
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

						if ( $wps_membership_default_product != $cart_item['product_id'] ) {

							WC()->cart->remove_cart_item( $cart_item_key );
						}
					}
					add_action( 'woocommerce_before_cart', array( $this, 'add_cart_custom_notice_2' ) );
				}
			}
		}
	}

	/**
	 * Function for remove billing fields.
	 *
	 * @param array $fields is array of fields.
	 * @return array
	 */
	public function wps_mfw_remove_billing_from_checkout( $fields ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$default_product_id = get_option( 'wps_membership_default_product' );
		$only_virtual      = false;
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

			$_product = $cart_item['data'];
			if ( $_product->get_id() == $default_product_id ) {
				if ( array_key_exists( 'form_submit', $cart_item ) && 'yes' == $cart_item['form_submit'] ) {

					$only_virtual = true;
				}
			}
		}

		if ( $only_virtual ) {
			if ( array_key_exists( 'wps_fname', $cart_item ) ) {

				$fields['billing']['billing_first_name'] = $cart_item['wps_fname'];

			}
			if ( array_key_exists( 'wps_lname', $cart_item ) ) {

				$fields['billing']['billing_last_name'] = $cart_item['wps_lname'];

			}
			if ( array_key_exists( 'wps_address1', $cart_item ) ) {

				$fields['billing']['billing_address_1'] = $cart_item['wps_address1'];

			}
			if ( array_key_exists( 'wps_city', $cart_item ) ) {

				$fields['billing']['billing_city'] = $cart_item['wps_city'];

			}

			if ( array_key_exists( 'wps_pincode', $cart_item ) ) {

				$fields['billing']['billing_postcode'] = $cart_item['wps_pincode'];

			}
			if ( array_key_exists( 'wps_country', $cart_item ) ) {

				$fields['billing']['billing_country'] = $cart_item['wps_country'];

			}
			if ( array_key_exists( 'wps_state', $cart_item ) ) {

				$fields['billing']['billing_state'] = $cart_item['wps_state'];

			}

			if ( array_key_exists( 'wps_phone', $cart_item ) ) {

				$fields['billing']['billing_phone'] = $cart_item['wps_phone'];

			}
			if ( array_key_exists( 'wps_email', $cart_item ) ) {

				$fields['billing']['billing_email'] = $cart_item['wps_email'];

			}

			add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );
			echo '<style type="text/css">
			form.checkout .woocommerce-billing-fields {
				display:none;
			}
			</style>';
		}
		return $fields;

	}

	/**
	 * Add notice on cart page if cart is already added with products
	 *
	 * @return void
	 */
	public function add_cart_custom_notice() {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		wc_print_notice(
			sprintf(
				'<span class="subscription-reminder">' .
				__( 'Sorry we cannot add membership products with other products, either empty cart or add membership product later when cart is empty', 'membership-for-woocommerce' ) . '</span>',
				__( 'empty', 'membership-for-woocommerce' )
			),
			'error'
		);
	}

	/**
	 * Add notice on cart page if cart is already added with products
	 *
	 * @return void
	 */
	public function add_cart_custom_notice_2() {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		wc_print_notice(
			sprintf(
				'<span class="subscription-reminder">' .
				__( 'Sorry we cannot add  other products with membership products , either empty cart or add  product later when cart is empty', 'membership-for-woocommerce' ) . '</span>',
				__( 'empty', 'membership-for-woocommerce' )
			),
			'error'
		);
	}

	/**
	 *  Adding distraction free mode to the offers page.
	 *
	 * @param mixed $page_template Default template for the page.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_plan_page_template( $page_template ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$pages_available = get_posts(
			array(
				'post_type'      => 'any',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'pagename'       => 'membership-plans',
				'order'          => 'ASC',
				'orderby'        => 'ID',
			)
		);

		$pages_available = array_merge(
			get_posts(
				array(
					'post_type'      => 'any',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					's'              => '[wps_membership_default_page_identification]',
					'order'          => 'ASC',
					'orderby'        => 'ID',
				)
			),
			$pages_available
		);

		foreach ( $pages_available as $single_page ) {

			if ( is_page( $single_page->ID ) ) {

				$page_template = plugin_dir_path( __FILE__ ) . '/partials/templates/membership-templates/wps-membership-template.php';
			}
		}

		/**
		 * Filter for plan page.
		 *
		 * @since 1.0.0
		 */
		$page_template = apply_filters( 'wps_membership_plan_page_template', $page_template );
		return $page_template;
	}

	/**
	 * Creating shipping method for membership.
	 *
	 * @param array $methods an array of shipping methods.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_for_woo_create_shipping_method( $methods ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		if ( ! class_exists( 'WPS_Membership_Free_Shipping_Method' ) ) {
			/**
			 * Custom shipping class for membership.
			 */
			require_once plugin_dir_path( __FILE__ ) . '/classes/class-wps-membership-free-shipping-method.php'; // Including class file.
			new WPS_Membership_Free_Shipping_Method();
		}
	}

	/**
	 * Adding membership shipping method.
	 *
	 * @param array $methods an array of shipping methods.
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_for_woo_add_shipping_method( $methods ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return $methods;
		}
		$methods['wps_membership_shipping'] = 'wps_Membership_Free_Shipping_Method';

		/**
		 * Filter for add shipping.
		 *
		 * @since 1.0.0
		 */
		$methods = apply_filters( 'wps_membership_for_woo_add_shipping_method', $methods );

		return $methods;
	}

	/**
	 * Adding membership shipping method.
	 *
	 * @param array $methods an array of shipping methods.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_add_to_cart_url( $methods ) {

		global $product;
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$user = wp_get_current_user();
		$is_member_meta = get_user_meta( $user->ID, 'is_member' );
		if ( is_user_logged_in() || in_array( 'member', (array) $is_member_meta ) ) {
			$data                = $this->custom_query_data;
			$user_id             = get_current_user_id();
			$existing_plan_id    = array();
			$existing_plan_product   = array();
			$page_link = '';
			$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );

			if ( ! empty( $current_memberships ) && is_array( $current_memberships ) ) {

				foreach ( $current_memberships as $key => $membership_id ) {

					$member_status = wps_membership_get_meta_data( $membership_id, 'member_status', true );

					if ( ! empty( $member_status ) && 'complete' == $member_status ) {

						$active_plan = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );

						$club_membership = $this->get_all_included_membership( $active_plan['ID'] );
						if ( ! empty( $club_membership ) ) {
							$existing_plan_id = array_merge( $existing_plan_id, $club_membership );
						}
						if ( ! empty( $active_plan['ID'] ) ) {
							array_push( $existing_plan_id, $active_plan['ID'] );
						}

						$target_ids      = ! empty( wps_membership_get_meta_data( $active_plan['ID'], 'wps_membership_plan_target_ids', true ) ) ? wps_membership_get_meta_data( $active_plan['ID'], 'wps_membership_plan_target_ids', true ) : array();
						$target_cat_ids  = ! empty( wps_membership_get_meta_data( $active_plan['ID'], 'wps_membership_plan_target_categories', true ) ) ? wps_membership_get_meta_data( $active_plan['ID'], 'wps_membership_plan_target_categories', true ) : array();
						$target_tag_ids  = ! empty( wps_membership_get_meta_data( $active_plan['ID'], 'wps_membership_plan_target_tags', true ) ) ? wps_membership_get_meta_data( $active_plan['ID'], 'wps_membership_plan_target_tags', true ) : array();

						$product_terms = $this->get_product_terms( get_the_ID() );

						if ( ! empty( $product_terms ) ) {
							foreach ( $product_terms as $product_terms_key => $product_terms_value ) {
								if ( in_array( $product_terms_value, (array) $target_tag_ids ) ) {
									array_push( $existing_plan_product, get_the_ID() );
								}
							}
						}
						if ( in_array( get_the_ID(), $target_ids ) || ( ! empty( $target_cat_ids ) && has_term( $target_cat_ids, 'product_cat' ) ) ) {
							array_push( $existing_plan_product, get_the_ID() );
						}
					}
				}
			}

			foreach ( $data as $plan ) {
				$wps_membership_default_plans_page_id = get_option( 'wps_membership_default_plans_page', '' );

				if ( ! empty( $wps_membership_default_plans_page_id ) && 'publish' == get_post_status( $wps_membership_default_plans_page_id ) ) {
						$page_link = get_page_link( $wps_membership_default_plans_page_id );
				}

				if ( ! in_array( $plan['ID'], $existing_plan_id ) ) {

							$page_link_found = false;
							$target_ids      = ! empty( wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_ids', true ) ) ? wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_ids', true ) : array();
							$target_cat_ids  = ! empty( wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_categories', true ) ) ? wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_categories', true ) : array();
							$target_tag_ids  = ! empty( wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_tags', true ) ) ? wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_tags', true ) : array();

							$product_terms = $this->get_product_terms( get_the_ID() );

					if ( ! empty( $product_terms ) ) {
						foreach ( $product_terms as $product_terms_key => $product_terms_value ) {
							if ( in_array( $product_terms_value, (array) $target_tag_ids ) ) {
								array_push( $target_ids, get_the_ID() );
							}
						}
					}

					if ( in_array( get_the_ID(), $target_ids ) || ( ! empty( $target_cat_ids ) && has_term( $target_cat_ids, 'product_cat' ) ) ) {

						if ( ! in_array( get_the_ID(), $existing_plan_product ) ) {

							$methods = '<div class="not_accessible"></div>';
							echo wp_kses_post( $methods );
						}
					}
				}
			}
		}
	}

	/**
	 * Set session for membership purchase.
	 *
	 * @return void
	 */
	public function wps_mfw_set_woocoomerce_session() {

		if ( ! empty( WC()->session ) && ! WC()->session->has_session() ) {
			WC()->session->set_customer_session_cookie( true );
		}
	}

	/**
	 * Set session for membership purchase.
	 *
	 * @param [type] $member_product is the member price to be return.
	 * @param [type] $product_id is the current product id.
	 * @return mixed
	 */
	public function wps_membership_get_product_price_of_member( $member_product, $product_id ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$user = wp_get_current_user();
		$is_member_meta = get_user_meta( $user->ID, 'is_member' );
		if ( is_user_logged_in() && in_array( 'member', (array) $is_member_meta ) ) {
			if ( ! is_cart() ) {

				$discount = wps_membership_get_meta_data( $product_id, '_wps_membership_discount_product_', true );

				if ( 'true' == $discount ) {
					$member_product = wps_membership_get_meta_data( $product_id, '_wps_membership_discount_product_price', true );
				}

				if ( empty( $member_product ) ) {
					$member_product = '';
				}
			}
		}
		return $member_product;
	}

	/**
	 * Updating subscription status according membership status.
	 *
	 * @param mixed $subscription_status is the subscription status.
	 * @param mixed $subscription_i_d id of current subscription id.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_subscription_get_status( $subscription_status, $subscription_i_d ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$member_id = '';
		$subscription = get_post( $subscription_i_d );
		$parent_order_id  = $subscription->wps_parent_order;
		$order = wc_get_order( $parent_order_id );
		foreach ( $order->get_items() as $item_id => $order_item ) {

			if ( ! empty( $order_item->get_meta( '_member_id' ) ) ) {
				$member_id = $order_item->get_meta( '_member_id' );
			}
		}

		if ( ! empty( $member_id ) ) {
			$plan = wps_membership_get_meta_data( $member_id, 'plan_obj', true );
			if ( 'yes' == $plan['wps_membership_subscription'] ) {
				$subscription_status = $order->get_status();
			}
		}
		return $subscription_status;
	}

	/**
	 * Updating subscription status according membership status.
	 *
	 * @param mixed $wps_next_payment_date is the next payment date.
	 * @param mixed $subscription_i_d id of current subscription id.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_subscription_next_payment_date( $wps_next_payment_date, $subscription_i_d ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$order_id = wps_membership_get_meta_data( $subscription_i_d, 'wps_parent_order', true );
		$order = wc_get_order( $order_id );
		$member_id = get_member_id_from_order( $order );

		if ( ! empty( $member_id ) ) {
			$expiry_date = wps_membership_get_meta_data( $member_id, 'member_expiry', true );
			$plan = wps_membership_get_meta_data( $member_id, 'plan_obj', true );
			if ( 'yes' == $plan['wps_membership_subscription'] ) {
				wps_membership_update_meta_data( $subscription_i_d, 'wps_next_payment_date', $expiry_date );
			}
			return $expiry_date;
		} else {
			return $wps_next_payment_date;
		}
	}

	/**
	 * Updating subscription status according membership status.
	 *
	 * @param mixed $wps_susbcription_end is the subscription end date.
	 * @param mixed $subscription_i_d id of current subscription id.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_susbcription_end_date( $wps_susbcription_end, $subscription_i_d ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$order_id = wps_membership_get_meta_data( $subscription_i_d, 'wps_parent_order', true );
		$order = wc_get_order( $order_id );
		$member_id = get_member_id_from_order( $order );

		if ( ! empty( $member_id ) ) {
			$expiry_date = wps_membership_get_meta_data( $member_id, 'member_expiry', true );
			$plan = wps_membership_get_meta_data( $member_id, 'plan_obj', true );
			if ( 'yes' == $plan['wps_membership_subscription'] ) {
				if ( ! empty( $plan['wps_membership_subscription_expiry'] ) ) {
					if ( function_exists( 'wps_sfw_susbcription_expiry_date' ) ) {
						$access_type = wps_membership_get_meta_data( $plan['plan_id'], 'wps_membership_plan_access_type', true );
						// $current_date = gmdate( 'Y-m-d' );
						$current_time = current_time( 'timestamp' );
						if ( 'delay_type' == $access_type ) {
							$time_duration      = wps_membership_get_meta_data( $plan['plan_id'], 'wps_membership_plan_time_duration', true );
							$time_duration_type = wps_membership_get_meta_data( $plan['plan_id'], 'wps_membership_plan_time_duration_type', true );

							$current_time = strtotime( gmdate( 'Y-m-d', strtotime( $current_time . ' + ' . $time_duration . ' ' . $time_duration_type ) ) );
						}
						$wps_susbcription_end = wps_sfw_susbcription_expiry_date( $subscription_i_d, $current_time );
						wps_membership_update_meta_data( $subscription_i_d, 'wps_susbcription_end', $wps_susbcription_end );

					}
				} else {
						wps_membership_update_meta_data( $subscription_i_d, 'wps_susbcription_end', '' );
				}
			}
			return $expiry_date;
		} else {
			return $wps_susbcription_end;
		}
	}

	/**
	 * This function is used to set single quantity for susbcription product.
	 *
	 * @name wps_sfw_hide_quantity_fields_for_subscription
	 * @param bool   $return return.
	 * @param object $product product.
	 * @since 1.0.0
	 */
	public function wps_membership_hide_quantity_fields_for_membership( $return, $product ) {

		if ( wps_membership_check_plugin_enable() && wps_membership_check_product_is_membership( $product ) ) {

			$return = true;
		}

		/**
		 * Filter to show quantity field.
		 *
		 * @since 1.0.0
		 */
		return apply_filters( 'wps_membership_show_quantity_fields_for_membership', $return, $product );
	}

	/**
	 * Function to check admin mail id
	 *
	 * @param [type] $fields are the checkout fields.
	 * @param [type] $errors are the errors to be return.
	 * @return void
	 */
	public function wps_membership_validate_email( $fields, $errors ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		global $woocommerce;
		$membership_name                = '';
		$wps_user                       = get_user_by( 'email', $fields['billing_email'] );
		$is_not_membership_applicable   = false;
		$is_membership_product          = false;
		$wps_membership_default_product = get_option( 'wps_membership_default_product', '' );
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$product = $cart_item['data'];
			if ( ! empty( $product ) ) {

				if ( $product->get_id() == $wps_membership_default_product ) {
					$membership_name = $product->get_title();
					$is_membership_product = true;
				}
			}
		}

		$is_member_meta = get_user_meta( $wps_user->ID, 'is_member' );
		if ( is_user_logged_in() || in_array( 'member', (array) $is_member_meta ) ) {
			$data                = $this->custom_query_data;
			$user_id             = $wps_user->ID;

			$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );

			if ( ! empty( $current_memberships ) && is_array( $current_memberships ) ) {

				foreach ( $current_memberships as $key => $membership_id ) {

					$member_status = wps_membership_get_meta_data( $membership_id, 'member_status', true );

					if ( ! empty( $member_status ) && 'complete' == $member_status || 'pending' == $member_status || 'hold' == $member_status ) {
						$active_plan       = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
						$active_plan_title = ! empty( $active_plan['post_title'] ) ? $active_plan['post_title'] : '';
						if ( $active_plan_title == $membership_name ) {
							$is_not_membership_applicable = true;
						}
					}
				}
			}
		}

		if ( $is_not_membership_applicable ) {
			$errors->add( 'validation', 'Membership plan already exists Buy a new plan !!' );
		}
	}

	/**
	 * Login link on thank you page.
	 *
	 * @param int $order_id is order id.
	 * @return void
	 */
	public function wps_membership_login_thanku_page( $order_id ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$order = new WC_Order( $order_id );
		$items = $order->get_items();
		foreach ( $items as $item ) {
			$product_id = $item['product_id'];
			$product = wc_get_product( $product_id );
			$plan_id = wps_membership_get_meta_data( $product_id, 'wps_membership_plan_with_product', true );
			$is_plan_assigned = false;
			$user = get_user_by( 'email', $order->get_billing_email() );
			if ( $plan_id ) {
				$is_plan_assigned = false;
			}
			if ( ! empty( $user ) ) {

				$user_id = $user->ID;
				$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );
				if ( ! empty( $current_memberships ) && is_array( $current_memberships ) && $plan_id ) {
					foreach ( $current_memberships as $key => $membership_id ) {
						$active_plan = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
						$status = wps_membership_get_meta_data( $membership_id, 'member_status', true );

						if ( ! empty( $active_plan['ID'] ) ) {

							if ( $plan_id == $active_plan['ID'] && 'cancelled' != $status && ! empty( $status ) ) {
								$is_plan_assigned = true;
								break;

							}
						}
					}
				}
			}

			if ( 'Membership Product' == $product->get_title() || ! $is_plan_assigned ) {
				if ( ! is_user_logged_in() ) {
					$is_user_created = get_option( 'wps_membership_create_user_after_payment', true );

					if ( 'on' !== $is_user_created ) {

						$html = '<div><strong>' . esc_html__( ' Thank You For Purchasing Membership Plan! Check your mail for the login Credential', 'membership-for-woocommerce' ) .
						'</strong><br><span style="color:red;">' . esc_html__( ' To Access Membership Please Login/Signup First. ', 'membership-for-woocommerce' ) . '</span><a class="button alt mfw-membership" href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '" target="_blank" style="color:#ffffff;">' . esc_html__( 'Login/Sign-up first', 'membership-for-woocommerce' ) . '</a>
					</div>';
						echo wp_kses_post( $html );
					} else {
						$html = '<div style="color:red;"><strong>' . esc_html__( ' Thank You For Purchasing Membership Plan!', 'membership-for-woocommerce' ) . '<br><span style="color:blue;">' . esc_html__( 'You will get your Login Credential when Shop Owner will complete your Order and then after You can Login and access your membership.', 'membership-for-woocommerce' ) . '</span></div>';
						echo wp_kses_post( $html );
					}
				}
			}
		}
	}

	/**
	 * Function to add lebel.
	 *
	 * @return void
	 */
	public function mfw_membership_add_label() {
		global $product;
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$price = '';
		$is_plan_assigned = false;
		$post_id    = get_the_ID();
		$user = wp_get_current_user();
		$is_member_meta = get_user_meta( $user->ID, 'is_member' );
		$user_id = get_current_user_id();
		$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );

		$plan_id = wps_membership_get_meta_data( $post_id, 'wps_membership_plan_with_product', true );

		if ( $plan_id ) {
			$is_plan_assigned = false;
		}

		if ( is_user_logged_in() ) {
			if ( in_array( 'member', (array) $is_member_meta ) ) {

				if ( ! empty( $current_memberships ) && is_array( $current_memberships ) && $plan_id ) {
					foreach ( $current_memberships as $key => $membership_id ) {
						$active_plan = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
						$status = wps_membership_get_meta_data( $membership_id, 'member_status', true );

						if ( ! empty( $active_plan['ID'] ) ) {
							if ( $plan_id == $active_plan['ID'] && 'complete' == $status ) {
								$is_plan_assigned = true;

							}
						}
					}

					if ( ! $is_plan_assigned ) {
						if ( ! is_single() ) {

							$result   = get_post( $plan_id );

							?>
						<div class="mfw-product-meta-membership-wrap">
								<div class="product-meta mfw-product-meta-membership">
									<span><b><?php esc_html_e( 'Product with Membership Plan ', 'membership-for-woocommerce' ); ?></b></span>
								</div>
								<i class="fa-question-circle wps_mfw_membership_tool_tip_wrapper">
									<div class="wps_mfw_membership_tool_tip">
										<?php echo esc_html( $result->post_title ); ?>
									</div>
								</i>
							</div>
							<?php
						} else {
							$result   = get_post( $plan_id );

							?>
							<div class="wps-info-membership-alert">
								<p >
									<?php esc_html_e( 'Buy this product and become a member of ', 'membership-for-woocommerce' ); ?>
									<?php echo esc_html( $result->post_title ); ?>
									<?php esc_html_e( ' membership plan. ', 'membership-for-woocommerce' ); ?>
	
								</p>
							</div>
							<?php
						}
					}
				}
			} else {

				if ( $plan_id ) {
					if ( ! is_single() ) {

						$result   = get_post( $plan_id );
						?>
					<div class="mfw-product-meta-membership-wrap">
							<div class="product-meta mfw-product-meta-membership">
								<span><b><?php esc_html_e( 'Product with Membership Plan ', 'membership-for-woocommerce' ); ?></b></span>
							</div>
							<i class="fa-question-circle wps_mfw_membership_tool_tip_wrapper">
								<div class="wps_mfw_membership_tool_tip">
									<?php echo esc_html( $result->post_title ); ?>
								</div>
							</i>
						</div>
						<?php
					} else {
						$result   = get_post( $plan_id );
						?>
						<div class="wps-info-membership-alert">
							<p >
								<?php esc_html_e( 'Buy this product and become a member of ', 'membership-for-woocommerce' ); ?>
								<?php echo esc_html( $result->post_title ); ?>
								<?php esc_html_e( ' membership plan. ', 'membership-for-woocommerce' ); ?>

							</p>
						</div>
						<?php
					}
				}
			}
		} else {
			if ( $plan_id ) {
				if ( ! is_single() ) {

					$result   = get_post( $plan_id );
					?>
				<div class="mfw-product-meta-membership-wrap">
						<div class="product-meta mfw-product-meta-membership">
							<span><b><?php esc_html_e( 'Product with Membership Plan ', 'membership-for-woocommerce' ); ?></b></span>
						</div>
						<i class="fa-question-circle wps_mfw_membership_tool_tip_wrapper">
							<div class="wps_mfw_membership_tool_tip">
								<?php echo esc_html( $result->post_title ); ?>
							</div>
						</i>
					</div>
					<?php
				} else {
					$result   = get_post( $plan_id );
					?>
					<div class="wps-info-membership-alert">
						<p >
							<?php esc_html_e( 'Buy this product and become a member of ', 'membership-for-woocommerce' ); ?>
							<?php echo esc_html( $result->post_title ); ?>
							<?php esc_html_e( ' membership plan. ', 'membership-for-woocommerce' ); ?>

						</p>
					</div>
					<?php
				}
			}
		}

	}

	/**
	 * Shortcode for form registration.
	 * Returns : empty string.
	 *
	 * @since 1.0.0
	 */
	public function wps_membership_registration_form_shortcode() {
		$wps_plan = get_posts(
			array(
				'post_type' => 'wps_cpt_membership',
				'post_status' => 'publish',
				'numberposts' => -1,

			)
		);
		$wps_membership_plans_page_id = get_option( 'wps_membership_default_plans_page', true );
		$page_url = get_permalink( $wps_membership_plans_page_id );

		$output = '';
		$output .= '<form method="POST">';
		$output .= '<div class="div_wrapper wps-register-form-wrapper">';
		$output .= '<div><label for="wps_register_form_plan"> ' . __( 'Select Plan', 'membership-for-woocommerce' ) . ' </lable><select required id="wps_register_form_plan" name="wps_register_form_plan"><option value="">' . __( 'Choose Plan', 'membership-for-woocommerce' ) . '</option>';
		foreach ( $wps_plan as $key => $value ) {

			$output .= '<option value="' . esc_attr( $value->ID ) . '">' . esc_html( $value->post_title ) . '</option>';
		}
		$output .= '</select><a style="margin-left:10px;" href="' . $page_url . '" target="_blank">' . __( ' Click here for all plans details ', 'membership-for-woocommerce' ) . '</a></div>';
		$output .= '<div><label for="wps_register_form_fname">' . __( 'First Name', 'membership-for-woocommerce' ) . '</label><input type="text" id="wps_register_form_fname" name="wps_register_form_fname" required placeholder="First Name"></div>';
		$output .= '<div><label for="wps_register_form_lname">' . __( 'Last Name', 'membership-for-woocommerce' ) . '</label><input type="text" id="wps_register_form_lname" name="wps_register_form_lname" required placeholder="Last Name"></div>';
		$output .= '<div><label for="wps_register_form_country">' . __( 'Country', 'membership-for-woocommerce' ) . '</label><input type="text" id="wps_register_form_country" name="wps_register_form_country" required placeholder="Country"></div>';
		$output .= '<div><label for="wps_register_form_address1">' . __( 'Street ', 'membership-for-woocommerce' ) . '</label><input type="text" id="wps_register_form_address1" name="wps_register_form_address1" required placeholder="Street Address"></div>';
		$output .= '<div><label for="wps_register_form_city">' . __( 'City ', 'membership-for-woocommerce' ) . '</label><input type="text" id="wps_register_form_city" name="wps_register_form_city" required placeholder="City"></div>';
		$output .= '<div><label for="wps_register_form_state">' . __( 'State ', 'membership-for-woocommerce' ) . '</label><input type="text" id="wps_register_form_state" name="wps_register_form_state" required placeholder="State"></div>';
		$output .= '<div><label for="wps_register_form_pincode">' . __( 'Pin Code ', 'membership-for-woocommerce' ) . '</label><input type="number" id="wps_register_form_pincode" name="wps_register_form_pincode" required placeholder="Pin Code"></div>';
		$output .= '<div><label for="wps_register_form_phone_no">' . __( 'Phone No ', 'membership-for-woocommerce' ) . '</label><input type="number" id="wps_register_form_phone_no" name="wps_register_form_phone_no" required placeholder="Phone Number"></div>';
		$output .= '<div><label for="wps_register_form_email">' . __( 'Email Address ', 'membership-for-woocommerce' ) . '</label><input type="email" id="wps_register_form_email" name="wps_register_form_email" required placeholder="Email Address"></div>';
		$output .= '<div></form>';
		$nonce = wp_create_nonce( 'wps-form-nonce' );
		$output .= '<input type="hidden" name="wps_nonce_name" value="' . esc_attr( $nonce ) . '" />';
		$output .= '<div><input type="submit" id="wps_regiser_form_submit" class="button" name="wps_regiser_form_submit" value="Register">';
		return $output;
	}

	/**
	 * This function is used to redirect user on selected page by admin.
	 *
	 * @param  string $redirection_url redirection_url.
	 * @return string
	 */
	public function wps_msfw_user_redirection( $redirection_url ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		$wps_membership_enable_other_settings = get_option( 'wps_membership_enable_other_settings', 'off' );
		$wps_msfw_page_for_redirection_user   = ! empty( get_option( 'wps_msfw_page_for_redirection_user' ) ) ? get_option( 'wps_msfw_page_for_redirection_user' ) : 0;
		if ( 'on' === $wps_membership_enable_other_settings ) {
			if ( $wps_msfw_page_for_redirection_user > 0 ) {

				$redirection_url = get_permalink( $wps_msfw_page_for_redirection_user );
			}
		}
		return $redirection_url;
	}

	/**
	 * Undocumented function.
	 *
	 * @param  array $gateways gateways.
	 * @return array
	 */
	public function wps_msfw_restrict_wallet_payments( $gateways ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return $gateways;
		}
		$wps_msfw_restrict_payment_via_wallet = get_option( 'wps_msfw_restrict_payment_via_wallet', '' );
		$count                                = 0;
		if ( 'on' === $wps_msfw_restrict_payment_via_wallet ) {
			if ( isset( WC()->cart ) && null !== WC()->cart ) {
				if ( ! empty( WC()->cart->get_cart() ) && is_array( WC()->cart->get_cart() ) ) {

					foreach ( WC()->cart->get_cart() as $item_key => $item_values ) {
						if ( ! empty( $item_values['product_id'] ) ) {

							$product = wc_get_product( $item_values['product_id'] );
							if ( ! empty( $product ) && is_object( $product ) ) {
								if ( 'Membership Product' === $product->get_title() ) {

									++$count;
									break;
								}
							}
						}
					}
				}
			}

			// check if counter is greater than zero, than reset wallet payment.
			if ( $count > 0 ) {

				unset( $gateways['wps_wcb_wallet_payment_gateway'] );
			}
		}
		return $gateways;
	}

	/**
	 * This function is used to restrict blocked user to not purchase include section product.
	 *
	 * @param  bool   $is_purchasable is_purchasable.
	 * @param  object $product        product.
	 * @return bool
	 */
	public function wps_mfw_block_user_unable_to_pruchase_include_product( $is_purchasable, $product ) {

		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {

			if ( object == gettype( $product ) ) {
				$product_id = $product->get_id();
			} else {
				$product_id = get_the_ID();
			}

			$product_id         = $product_id;
			$is_product_exclude = false;

			if ( $this->global_class->plans_exist_check() == true ) {

				$data = $this->custom_query_data;
				if ( ! empty( $data ) && is_array( $data ) ) {
					foreach ( $data as $plan ) {

						$exclude_product = array();

						/**
						 * Filter for exclude product.
						 *
						 * @since 1.0.0
						 */
						$exclude_product = apply_filters( 'wps_membership_exclude_product', $exclude_product, $product_id );

						/**
						 * Filter for exclude products.
						 *
						 * @since 1.0.0
						 */
						$is_product_exclude = apply_filters( 'wps_membership_is_exclude_product', $exclude_product, $data, $is_product_exclude );

						if ( $is_product_exclude ) {
							break;
						}

						if ( in_array( $plan['ID'], $exclude_product ) && ! empty( $exclude_product ) ) {
							break;
						}

						$target_ids     = wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_ids', true );
						$target_cat_ids = wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_categories', true );
						$target_tag_ids  = wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_tags', true );
						if ( ! empty( $target_ids ) && is_array( $target_ids ) ) {

							if ( in_array( get_the_ID(), $target_ids ) ) {

								$is_purchasable = false;
							}
						}

						if ( ( ! empty( $target_cat_ids ) && is_array( $target_cat_ids ) ) || ( ! empty( $target_tag_ids ) && is_array( $target_tag_ids ) ) ) {
							if ( has_term( $target_cat_ids, 'product_cat', get_post( $product_id ) ) || has_term( $target_tag_ids, 'product_tag', get_post( $product_id ) ) ) {

								if ( empty( $target_ids ) ) { // If target id is empty string make it an array.

									$target_ids = array();
								}
								if ( ! in_array( $product_id, $target_ids ) ) { // checking if the product does not exist in target id of a plan.

									$is_purchasable = false;
								}
							}
						}
					}
				}
			}
		}
		return $is_purchasable;
	}

}
