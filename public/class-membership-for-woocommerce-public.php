<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
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
	 * @access private
	 */
	private $custom_query_data;


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

		wp_enqueue_style( $this->plugin_name, MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'public/css/membership-for-woocommerce-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'public-css', MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'public/css/mwb-public.css', array(), $this->version, 'all' );

		wp_enqueue_style( 'wp-jquery-ui-dialog' );

		if ( is_page( 'membership-plans' ) ) {

			wp_enqueue_style( 'membership-plan', plugin_dir_url( __FILE__ ) . 'css/membership-plan.css', array( 'jquery' ), $this->version, false );
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function mfw_public_enqueue_scripts() {

		wp_register_script( $this->plugin_name, MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'public/js/membership-for-woocommerce-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'mfw_public_param', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( $this->plugin_name );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/membership-for-woocommerce-public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script(
			$this->plugin_name,
			'membership_public_obj',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'auth_adv_nonce' ),
			)
		);

		wp_enqueue_script( 'jquery-ui-dialog' );

		wp_enqueue_script( 'sweet_alert', plugin_dir_url( __FILE__ ) . 'js/sweet-alert2.js', array( 'jquery' ), $this->version, false );

		if ( is_page( 'membership-plans' ) ) {

			wp_enqueue_script( 'paypal-smart-buttons', plugin_dir_url( __FILE__ ) . 'js/membership-paypal-smart-buttons.js', array( 'jquery' ), $this->version, false );

			include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/gateways/paypal express checkout/class-membership-paypal-express-checkout.php';
			// Getting paypal settings to localize.
			$payapl_sb = new Membership_Paypal_Express_Checkout();
			$settings  = $payapl_sb->paypal_sb_settings();

			$client_id    = ! empty( $settings['client_id'] ) ? $settings['client_id'] : 'sb';
			$currency     = ! empty( $settings['currency_code'] ) ? $settings['currency_code'] : '';
			$intent       = ! empty( $settings['payment_action'] ) ? $settings['payment_action'] : '';
			$component    = ! empty( $settings['component'] ) ? $settings['component'] : 'buttons';
			$disable_fund = ! empty( $settings['disable_funding'] ) ? $settings['disable_funding'] : '';
			$vault        = ! empty( $settings['vault'] ) ? 'true' : 'false';
			$debug        = ! empty( $settings['debug'] ) ? 'true' : 'false';

			$plan_data = array();

			$plan_id = ! empty( $_GET['plan_id'] ) ? sanitize_text_field( wp_unslash( $_GET['plan_id'] ) ) : '';

			$plan_name  = ! empty( get_the_title( $plan_id ) ) ? get_the_title( $plan_id ) : '';
			$plan_desc  = ! empty( get_post_field( 'post_content', $plan_id ) ) ? get_post_field( 'post_content', $plan_id ) : '';
			$plan_price = ! empty( get_post_meta( $plan_id, 'mwb_membership_plan_price', true ) ) ? get_post_meta( $plan_id, 'mwb_membership_plan_price', true ) : '';

			$plan_data['name']  = $plan_name;
			$plan_data['desc']  = $plan_desc;
			$plan_data['price'] = $plan_price;

			wp_enqueue_script( 'paypal-sdk', 'https://www.paypal.com/sdk/js?client-id=' . $client_id . '&currency=' . $currency . '&intent=' . $intent . '&components=' . $component . '&disable-funding=' . $disable_fund . '&vault=' . $vault . '&debug=' . $debug, array( 'jquery' ), $this->version, false );

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

		$query = "SELECT   wp_posts.* FROM wp_posts  INNER JOIN wp_postmeta ON ( wp_posts.ID = wp_postmeta.post_id ) WHERE 1=1  
					AND ( wp_postmeta.meta_key = 'mwb_membership_plan_target_ids' ) AND wp_posts.post_type = 'mwb_cpt_membership' 
					AND (wp_posts.post_status = 'publish') GROUP BY wp_posts.ID ORDER BY wp_posts.post_date DESC";

		$this->custom_query_data = $this->global_class->run_query( $query );
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
		$vars = apply_filters( 'mwb_membership_endpoint_query_var', $vars );

		return $vars;
	}

	/**
	 * Inserting custom membership endpoint.
	 *
	 * @param array $items An array of all menu items on My Account page.
	 */
	public function mwb_membership_add_membership_tab( $items ) {

		// Getting global options.
		$mwb_membership_global_settings = get_option( 'mwb_membership_global_options', $this->global_class->default_global_options() );

		if ( ! empty( $mwb_membership_global_settings ) ) {

			if ( ! empty( $mwb_membership_global_settings['mwb_membership_plan_user_history'] ) && 'on' == $mwb_membership_global_settings['mwb_membership_plan_user_history'] ) {

				$logout = $items['customer-logout'];
				unset( $items['customer-logout'] );

				// Placing the custom tab just above logout tab.
				$items['mwb-membership-tab'] = esc_html__( 'Membership Details', 'membership-for-woocommerce' );

				$items['customer-logout'] = $logout;
			}
		}
		$items = apply_filters( 'mwb_membership_add_membership_tab', $items );

		return $items;
	}

	/**
	 * Add title to Membership details tab.
	 *
	 * @param string $title stores the title of the endpoint.
	 * @return string
	 */
	public function mwb_membership_tab_title( $title ) {
		global $wp_query;

		$endpoint = isset( $wp_query->query_vars['mwb-membership-tab'] );

		if ( $endpoint && ! is_admin() && in_the_loop() && is_account_page() ) {

			$title = __( 'Membership Details', 'membership-for-woocommerce' );

		}
		$title = apply_filters( 'mwb_membership_tab_title', $title );

		return $title;
	}


	/**
	 * Add content to Membership details tab.
	 */
	public function mwb_membership_populate_tab() {

		$user       = get_current_user_id();
		$memerships = get_user_meta( $user, 'mfw_membership_id', true );
		$instance   = $this->global_class;

		wc_get_template(
			'public/partials/templates/mwb-membership-details-tab.php',
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
	public function mwb_membership_shortcodes() {

		// Buy now button shortcode.
		add_shortcode( 'mwb_membership_yes', array( $this, 'buy_now_shortcode_yes' ) );
		add_shortcode( 'mwb_membership_buy', array( $this, 'buy_now_shortcode_content' ) );

		// No thanks button shortcode.
		add_shortcode( 'mwb_membership_no', array( $this, 'reject_shortcode_content' ) );

		// Membership Plan title shortcode.
		add_shortcode( 'mwb_membership_title', array( $this, 'membership_plan_title_all_plan' ) );

		// Membership Plan title shortcode.
		add_shortcode( 'mwb_membership_title_name', array( $this, 'membership_plan_title_shortcode' ) );

		// Membership Plan price shortcode.
		add_shortcode( 'mwb_membership_price', array( $this, 'membership_plan_price_shortcode' ) );

		// Membership Plan Description shortcode.
		add_shortcode( 'mwb_membership_desc', array( $this, 'membership_plan_desc_shortcode_all_plan' ) );

		// Membership Plan Description shortcode.
		add_shortcode( 'mwb_membership_desc_data', array( $this, 'membership_plan_desc_shortcode' ) );

		// Membership default plan name content shortcode.
		add_shortcode( 'mwb_membership_default_plans_page', array( $this, 'membership_offers_default_shortcode' ) );

		// Default Gutenberg offer.
		add_shortcode( 'mwb_membership_default_page_identification', array( $this, 'default_offer_identification_shortcode' ) );
	}
	/**
	 * Membership default global options.
	 *
	 * @since 1.0.0
	 */
	public function default_global_options() {

		$default_global_settings = array(

			'mwb_membership_enable_plugin'     => 'on',
			'mwb_membership_delete_data'       => 'off',
			'mwb_membership_plan_user_history' => 'on',
			'mwb_membership_email_subject'     => 'Thank you for Shopping, Do not reply.',
			'mwb_membership_email_content'     => '',
			'mwb_membership_attach_invoice'    => 'off',
			'mwb_membership_invoice_address'   => '',
			'mwb_membership_invoice_phone'     => '',
			'mwb_membership_invoice_email'     => '',
			'mwb_membership_invoice_logo'      => '',

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
	public function mwb_membership_for_woo_membership_purchasable( $is_purchasable, $product ) {

		if ( is_admin() ) {

			return $is_purchasable;
		}

		$mwb_membership_default_product = get_option( 'mwb_membership_default_product', '' );

		$membership_product = wc_get_product( $mwb_membership_default_product );

		if ( $membership_product ) {

			if ( $mwb_membership_default_product == $product->get_id() ) {

				$is_purchasable = true;
			}
		}
		$exclude = get_post_meta( $product->get_id(), '_mwb_membership_exclude', true );

		if ( 'yes' === $exclude ) {
			$is_purchasable = true;
			return $is_purchasable;
		}

		$user = wp_get_current_user();

		if ( $this->global_class->plans_exist_check() == true ) {

			$is_membership_product = $this->mwb_membership_products_on_shop_page( true, $product );

			// Determine access if is a membership product.
			if ( true == $is_membership_product ) {

				// Not a member.
				if ( ! is_user_logged_in() || ! in_array( 'member', (array) $user->roles ) ) {

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
		$is_purchasable = apply_filters( 'mwb_membership_tab_is_purchasable', $is_purchasable );

		return $is_purchasable;
	}

	/**
	 * Hide price of selected product on shop page.
	 *
	 * @param string $price_html Price html.
	 * @param object $product Product object.
	 */
	public function mwb_membership_for_woo_hide_price_shop_page( $price_html, $product ) {

		$user = wp_get_current_user();

		if ( $this->global_class->plans_exist_check() == true ) {

			if ( ! is_user_logged_in() && ! in_array( 'member', (array) $user->roles ) ) {

				if ( in_array( $product->get_id(), $this->global_class->plans_products_ids() ) || has_term( $this->global_class->plans_cat_ids(), 'product_cat' ) || has_term( $this->global_class->plans_tag_ids(), 'product_tag' ) ) {

					return '';
				}
			}
		}

		$price_html = apply_filters( 'mwb_membership_tab_price_html', $price_html );

		return $price_html;
	}

	/**
	 * Membership template for all membership products.
	 *
	 * @return void
	 */
	public function mwb_membership_product_membership_purchase_html() {

		global $product;
		$user = wp_get_current_user();
		$already_included_plan = array();
		$suggested_membership = false;
		$count = 0;
		$is_membership_product = $this->mwb_membership_products_on_shop_page( true, $product );

		if ( ! $product->is_purchasable() && $this->global_class->plans_exist_check() == true ) {

			if ( function_exists( 'is_product' ) && is_product() ) {

				$data = $this->custom_query_data;

				if ( ! empty( $data ) && is_array( $data ) ) {

					$mwb_membership_default_plans_page_id = get_option( 'mwb_membership_default_plans_page', '' );

					if ( ! empty( $mwb_membership_default_plans_page_id ) && 'publish' == get_post_status( $mwb_membership_default_plans_page_id ) ) {
						$page_link = get_page_link( $mwb_membership_default_plans_page_id );
					}

					foreach ( $data as $plan ) {

						$page_link_found = false;
						$target_ids      = get_post_meta( $plan['ID'], 'mwb_membership_plan_target_ids', true );
						$target_cat_ids  = get_post_meta( $plan['ID'], 'mwb_membership_plan_target_categories', true );
						$target_tag_ids  = get_post_meta( $plan['ID'], 'mwb_membership_plan_target_tags', true );

						if ( ! empty( $target_ids ) && is_array( $target_ids ) ) {

							if ( in_array( $product->get_id(), $target_ids ) ) {

								foreach ( $target_ids as $ids ) {
									$exclude = get_post_meta( $ids, '_mwb_membership_exclude', true );

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

								if ( is_user_logged_in() ) {
									$disable_required = false;
									// Show plans under review.
									$is_pending = 'not pending';

									if ( ! empty( $this->under_review_products ) && in_array( $product->get_id(), $this->under_review_products ) ) {
										$is_pending = 'not pending';
										$user_id = get_current_user_id();

										$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );

										if ( ! empty( $current_memberships ) && is_array( $current_memberships ) ) {

											foreach ( $current_memberships as $key => $membership_id ) {

												$member_status = get_post_meta( $membership_id, 'member_status', true );

												if ( ! empty( $member_status ) && 'complete' != $member_status ) {

													$active_plan = get_post_meta( $membership_id, 'plan_obj', true );

													if ( ! empty( $active_plan['ID'] ) && $active_plan['ID'] == $plan['ID'] ) {
														$is_pending = 'pending';
														// $disable_required = 'disable_required';
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

									if ( 'not pending' === $is_pending ) {

										if ( true === $suggested_membership ) {

											++$count;
											if ( true === $is_membership_product ) {
												$user = wp_get_current_user();

												if ( is_user_logged_in() && in_array( 'member', (array) $user->roles ) ) {

													echo '<div class="mwb-mfwp__available--title">Other Available Membership</div>';
													$suggested_membership = true;
												}
											}

											echo '<div class="available_member" style="clear: both">
	<div style="margin-top: 10px;">
		<a class="button alt ' . esc_html( $disable_required ) . ' mfw-membership" href="' . esc_url( $page_link ) . '" target="_blank" >' . esc_html__( 'Membership :- ', 'membership-for-woocommerce' ) . esc_html( get_the_title( $plan['ID'] ) ) . '</a>
	</div>
</div>';
										} else {
												// Show options to buy plans.
												echo '<div style="clear: both">
		<div style="margin-top: 10px;">
			<a class="button alt ' . esc_html( $disable_required ) . ' mfw-membership" href="' . esc_url( $page_link ) . '" target="_blank" >' . esc_html__( 'Become a  ', 'membership-for-woocommerce' ) . esc_html( get_the_title( $plan['ID'] ) ) . esc_html__( '  member and buy this product', 'membership-for-woocommerce' ) . '</a>
		</div>
	</div>';
										}
									}
								} else {
									echo '<div style="clear: both">
											<div style="margin-top: 10px;">
												<a class="button alt mfw-membership" href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '" target="_blank" >' . esc_html__( 'Login/Sign-up first', 'membership-for-woocommerce' ) . '</a>
											</div>
										</div>';
									break;
								}
							}
						}

						if ( false == $page_link_found && ( ! empty( $target_cat_ids ) && is_array( $target_cat_ids ) ) || ! empty( $target_tag_ids ) && is_array( $target_tag_ids ) ) {

							if ( has_term( $target_cat_ids, 'product_cat' ) || has_term( $target_tag_ids, 'product_tag' ) ) {

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

									if ( is_user_logged_in() ) {
										$disable_required = false;
										// Show plans under review.
										if ( ! empty( $this->under_review_products ) && in_array( $product->get_id(), $this->under_review_products ) ) {

											$user_id = get_current_user_id();

											$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );

											if ( ! empty( $current_memberships ) && is_array( $current_memberships ) ) {

												foreach ( $current_memberships as $key => $membership_id ) {

													$member_status = get_post_meta( $membership_id, 'member_status', true );

													if ( ! empty( $member_status ) && 'complete' != $member_status ) {

														$active_plan = get_post_meta( $membership_id, 'plan_obj', true );

														if ( ! empty( $active_plan['ID'] ) && $active_plan['ID'] == $plan['ID'] ) {
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

										echo '<div class="available_member" style="clear: both">
												<div style="margin-top: 10px;">
													<a class="button alt ' . esc_html( $disable_required ) . ' mfw-membership" href="' . esc_url( $page_link ) . '" target="_blank" >' . esc_html__( 'Become a  ', 'membership-for-woocommerce' ) . esc_html( get_the_title( $plan['ID'] ) ) . esc_html__( '  member and buy this product', 'membership-for-woocommerce' ) . '</a>
												</div>
											</div>';
									} else {
										echo '<div style="clear: both">
												<div style="margin-top: 10px;">
													<a class="button alt mfw-membership" href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '" target="_blank" >' . esc_html__( 'Login/Sign-up first', 'membership-for-woocommerce' ) . '</a>
												</div>
											</div>';
										break;
									}
								}
							}
						}
					}
				}
			}
		}

		$is_membership_product = $this->mwb_membership_products_on_shop_page( true, $product );
		if ( true === $is_membership_product ) {

			if ( is_user_logged_in() && in_array( 'member', (array) $user->roles ) ) {
				$data                = $this->custom_query_data;
				$user_id             = get_current_user_id();
				$existing_plan_id    = array();
				$plan_existing       = false;
				$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );

				if ( ! empty( $current_memberships ) && is_array( $current_memberships ) ) {

					foreach ( $current_memberships as $key => $membership_id ) {

						$member_status = get_post_meta( $membership_id, 'member_status', true );
						if ( 'pending' == $member_status || 'on-hold' == $member_status ) {

									$active_plan = get_post_meta( $membership_id, 'plan_obj', true );
									array_push( $existing_plan_id, $active_plan['ID'] );
								break;
						}
						if ( ! empty( $member_status ) && 'complete' == $member_status ) {

							$active_plan = get_post_meta( $membership_id, 'plan_obj', true );

							array_push( $existing_plan_id, $active_plan['ID'] );

						}
					}

					if ( false == $plan_existing ) {

						foreach ( $data as $plan ) {
							$mwb_membership_default_plans_page_id = get_option( 'mwb_membership_default_plans_page', '' );

							if ( ! empty( $mwb_membership_default_plans_page_id ) && 'publish' == get_post_status( $mwb_membership_default_plans_page_id ) ) {
								$page_link = get_page_link( $mwb_membership_default_plans_page_id );
							}

							if ( ! in_array( $plan['ID'], $existing_plan_id ) ) {

								if ( ! in_array( $plan['ID'], $already_included_plan ) ) {
									$page_link_found = false;
									$target_ids      = get_post_meta( $plan['ID'], 'mwb_membership_plan_target_ids', true );
									$target_cat_ids  = get_post_meta( $plan['ID'], 'mwb_membership_plan_target_categories', true );
									$target_tag_ids  = get_post_meta( $plan['ID'], 'mwb_membership_plan_target_tags', true );

									if ( ! empty( $target_ids ) && is_array( $target_ids ) ) {

										if ( in_array( $product->get_id(), $target_ids ) ) {

											foreach ( $target_ids as $ids ) {
												$exclude = get_post_meta( $ids, '_mwb_membership_exclude', true );

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

												if ( is_user_logged_in() && in_array( 'member', (array) $user->roles ) ) {

													echo '<div class="mwb-mfwp__available--title">Other Available Membership</div>';
													$suggested_membership = true;
												}
											}
										}
										++$count;

										$page_link = $page_link . '?plan_id=' . $plan['ID'] . '&prod_id=' . $product->get_id();

										echo '<div class="available_member" style="clear: both">
									<div style="margin-top: 10px;">
										<a class="button alt ' . esc_html( $disable_required ) . ' mfw-membership" href="' . esc_url( $page_link ) . '" target="_blank" >' . esc_html__( 'Membershipss	 :- ', 'membership-for-woocommerce' ) . esc_html( get_the_title( $plan['ID'] ) ) . '</a>
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
	 * Display membership tag on products which are offered in any membership on shop page.
	 *
	 * @param bool   $return_status Returns current products purchaseable status.
	 * @param object $_product Product object.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_products_on_shop_page( $return_status = false, $_product = false ) {

		global $product;

		if ( empty( $product ) ) {

			$product = $_product;
		}

		if ( $this->global_class->plans_exist_check() == true ) {

			$data = $this->custom_query_data;

			if ( ! empty( $data ) && is_array( $data ) ) {

				$output = '';

				foreach ( $data as $plan ) {

					$target_ids     = get_post_meta( $plan['ID'], 'mwb_membership_plan_target_ids', true );
					$target_cat_ids = get_post_meta( $plan['ID'], 'mwb_membership_plan_target_categories', true );
					$target_tag_ids  = get_post_meta( $plan['ID'], 'mwb_membership_plan_target_tags', true );

					if ( ! empty( $target_ids ) && is_array( $target_ids ) ) {

						if ( in_array( get_the_ID(), $target_ids ) ) {

							$output .= esc_html( get_the_title( $plan['ID'] ) ) . ' | ';
						}
					}

					if ( ( ! empty( $target_cat_ids ) && is_array( $target_cat_ids ) ) || ( ! empty( $target_tag_ids ) && is_array( $target_tag_ids ) ) ) {

						if ( has_term( $target_cat_ids, 'product_cat', get_post( $product->get_id() ) ) || has_term( $target_tag_ids, 'product_tag', get_post( $product->get_id() ) ) ) {

							if ( empty( $target_ids ) ) { // If target id is empty string make it an array.

								$target_ids = array();
							}

							if ( ! in_array( $product->get_id(), $target_ids ) ) { // checking if the product does not exist in target id of a plan.

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

						if ( in_array( $product->get_id(), $this->under_review_products ) ) {
							?>
							<div class="product-meta product-meta-review">
								<span><b><?php esc_html_e( 'Membership Under Review', 'membership-for-woocommerce' ); ?></b></span>
							</div>
							<?php
						}

						?>
							<div class="product-meta mfw-product-meta-membership">
								<span><b><?php esc_html_e( 'Membership Product', 'membership-for-woocommerce' ); ?></b></span>
							</div>
							<i class="fa-question-circle mwb_mfw_membership_tool_tip_wrapper">
								<div class="mwb_mfw_membership_tool_tip">
									<?php echo esc_html( $output ); ?>
								</div>
							</i>
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
	public function mwb_membership_validate_mode() {

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

		$mode = $this->mwb_membership_validate_mode();

		// If on default page, plan_id and prod_id are set.
		if ( isset( $_GET['plan_id'] ) && isset( $_GET['prod_id'] ) ) {

			$plan_id = sanitize_text_field( wp_unslash( $_GET['plan_id'] ) );
			$prod_id = sanitize_text_field( wp_unslash( $_GET['prod_id'] ) );

			// Get plan details.
			$plan_title    = get_the_title( $plan_id );
			$plan_price    = get_post_meta( $plan_id, 'mwb_membership_plan_price', true );
			$plan_currency = get_woocommerce_currency_symbol();
			$plan_desc     = get_post_field( 'post_content', $plan_id );
			$plan_info     = get_post_meta( $plan_id, 'mwb_membership_plan_info', true );

			// Plans default text.
			$offer_banner_text  = apply_filters( 'mwb_membership_plan_default_banner_txt', esc_html__( 'One Membership, Many Benefits', 'membership-for-woocommerce' ) );
			$offer_buy_now_txt  = apply_filters( 'mwb_membership_plan_default_buy_now_txt', esc_html__( 'Buy Now!', 'membership-for-woocommerce' ) );
			$offer_no_thnks_txt = apply_filters( 'mwb_membership_plan_default_no_thanks_txt', esc_html__( 'No thanks!', 'membership-for-woocommerce' ) );

			$output .= '<div class="mwb_membership_plan_banner">
							<h2><b><i>' . trim( $offer_banner_text ) . '</i></b></h2>
						</div>';

			$output .= '<div class="mwb_membership_plan_offer_wrapper">';

			$output .= '<div class="mwb_membership_plan_content_title">' . ucwords( $plan_title ) . '</div>';

			$output .= '<div class="mwb_membership_plan_content_price">' . sprintf( ' %s %s ', esc_html( $plan_currency ), esc_html( $plan_price ) ) . '</div>';

			$output .= '<input type="hidden" id="mwb_membership_plan_price" value="' . esc_html( $plan_price ) . '">';

			$output .= '<input type="hidden" id="mwb_membership_plan_id" value="' . esc_html( $plan_id ) . '">';

			$output .= '<div class="mwb_membership_plan_content_desc">' . $plan_desc . '</div>';

			$output .= '<div class="mwb_membership_plan_info">' . $plan_info . '</div>';

			$output .= '</div>';

			$output .= '<div class="mwb_membership_offer_action">
							<form class="mwb_membership_buy_now_btn thickbox" method="post">
								<input type="hidden" name="membership_title" id="mwb_membership_title" value="' . $plan_title . '">
								<input type="hidden" name="membership_id" value="' . $plan_id . '">
								<input type="submit" data-mode="' . $mode . '" class="mwb_membership_buynow" name="mwb_membership_buynow" value="' . $offer_buy_now_txt . '">
							</form>
							<a class="mwb_membership_no_thanks button alt" href="' . get_permalink( $prod_id ) . '">' . $offer_no_thnks_txt . '</a>';
			$output .= '</div>';

			$this->global_class->payment_gateways_html( $plan_id ); // Modal div wrapper.

		} else { // If plan_id and prod_id on default page are not set.

			$error_msg = apply_filters( 'mwb_membership_error_message', esc_html__( 'You ran out of session.', 'membership-for-woocommerce' ) );

			$link_text = apply_filters( 'mwb_membership_go_back_link_text', esc_html__( 'Go back to Shop page.', 'membership-for-woocommerce' ) );

			$shop_page_url = wc_get_page_permalink( 'shop' );

			$output .= $error_msg . '<a href="' . $shop_page_url . '" class="button">' . $link_text . '</a>';

		}
		$output = apply_filters( 'mwb_membership_tab_output', $output );

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

			$plan_price = get_post_meta( $plan_id, 'mwb_membership_plan_price', true );

			if ( ! empty( $plan_price ) ) {

				$price .= '<div class="mwb_membership_plan_content_price">' . sprintf( ' %s %s ', esc_html( get_woocommerce_currency_symbol() ), esc_html( $plan_price ) ) . '</div>';
			} else {

				$price .= '<div class="mwb_membership_plan_content_price">' . $content . '</div>';
			}
		}
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

				$title .= '<div class"mwb_membership_plan_content_title">' . ucwords( $plan_title ) . '</div>';
			} else {

				$title .= '<div class"mwb_membership_plan_content_title">' . $content . '</div>';
			}
		} else {
			$title .= '<div class"mwb_membership_plan_content_title"> All Membership Plans </div>';
		}
		$title = apply_filters( 'membership_plan_title_shortcode', $title );

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

				$title .= '<div class"mwb_membership_plan_content_title">' . ucwords( $plan_title ) . '</div>';
			} else {

				$title .= '<div class"mwb_membership_plan_content_title">' . $content . '</div>';
			}
		}
		$title = apply_filters( 'membership_plan_title_shortcode', $title );

		return $title;
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

				$description .= '<div class="mwb_membership_plan_content_desc">' . $plan_desc . '</div>';
			} else {
				$description .= '<div class="mwb_membership_plan_content_desc">' . $content . '</div>';
			}

			$plan_info = get_post_meta( $plan_id, 'mwb_membership_plan_info', true );

			if ( ! empty( $plan_info ) ) {

				$description .= '<div class="mwb_membership_plan_info">' . $plan_info . '</div>';
			}
		} else {

			$description .= '<div class="mwb_membership_plan_content_desc">';

			if ( is_user_logged_in() || in_array( 'member', (array) $user->roles ) ) {
				$data                = $this->custom_query_data;
				$user_id             = get_current_user_id();
				$existing_plan_id    = array();
				$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );

				if ( ! empty( $current_memberships ) && is_array( $current_memberships ) ) {

					foreach ( $current_memberships as $key => $membership_id ) {

						$member_status = get_post_meta( $membership_id, 'member_status', true );
						if ( 'pending' == $member_status || 'on-hold' == $member_status ) {
									$plan_existing = true;
									$active_plan   = get_post_meta( $membership_id, 'plan_obj', true );
									array_push( $existing_plan_id, $active_plan['ID'] );
								break;
						}
						if ( ! empty( $member_status ) && 'complete' == $member_status ) {

							$active_plan = get_post_meta( $membership_id, 'plan_obj', true );

							array_push( $existing_plan_id, $active_plan['ID'] );

						}
					}
				}
				$description .= '<ul>';
				foreach ( $data as $plan ) {
					$mwb_membership_default_plans_page_id = get_option( 'mwb_membership_default_plans_page', '' );

					if ( ! empty( $mwb_membership_default_plans_page_id ) && 'publish' == get_post_status( $mwb_membership_default_plans_page_id ) ) {
						$page_link = get_page_link( $mwb_membership_default_plans_page_id );
					}

					if ( ! in_array( $plan['ID'], $existing_plan_id ) ) {

						$description .= '<li>' . $plan['post_name'] . '</li>';

					}
				}
			}

			$description .= '</ul></div>';
		}
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

				$description .= '<div class="mwb_membership_plan_content_desc">' . $plan_desc . '</div>';
			} else {
				$description .= '<div class="mwb_membership_plan_content_desc">' . $content . '</div>';
			}

			$plan_info = get_post_meta( $plan_id, 'mwb_membership_plan_info', true );

			if ( ! empty( $plan_info ) ) {

				$description .= '<div class="mwb_membership_plan_info">' . $plan_info . '</div>';
			}
		}
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
		$mode = $this->mwb_membership_validate_mode();

		$mode = $this->mwb_membership_validate_mode();

		if ( empty( $plan_id ) ) {

			$plan_id = isset( $_GET['plan_id'] ) ? sanitize_text_field( wp_unslash( $_GET['plan_id'] ) ) : '';

		}
		if ( ! empty( $plan_id ) ) {

			$plan_price = get_post_meta( $plan_id, 'mwb_membership_plan_price', true );
			$plan_title = get_the_title( $plan_id );

			if ( empty( $content ) ) {

				$content = apply_filters( 'mwb_mebership_buy_now_btn_txt', esc_html__( 'Buy Now!', 'membership-for-woocommerce' ) );

			}

			$this->global_class->payment_gateways_html( $plan_id );

			$buy_button .= '<form method="post" class="mwb_membership_buy_now_btn">
								<input type="hidden" id="mwb_membership_plan_id" name="plan_id" value="' . $plan_id . '">
								<input type="hidden" id="mwb_membership_plan_price" value="' . esc_html( $plan_price ) . '">
								<input type="hidden" name="membership_title" id="mwb_membership_title" value="' . $plan_title . '">
								<input type="button" data-mode="' . $mode . '" class="mwb_membership_buynow" name="mwb_membership_buynow" value="' . $content . '">
							</form>';
			$buy_button = apply_filters( 'membership_plan_buy_button_shortcode', $buy_button );

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
		$mode    = $this->mwb_membership_validate_mode();

		if ( empty( $plan_id ) ) {

			$plan_id = isset( $_GET['plan_id'] ) ? sanitize_text_field( wp_unslash( $_GET['plan_id'] ) ) : '';

		}

		$plan_price = get_post_meta( $plan_id, 'mwb_membership_plan_price', true );
		$plan_title = get_the_title( $plan_id );

		if ( empty( $content ) ) {

			$content = apply_filters( 'mwb_mebership_buy_now_btn_txt', esc_html__( 'Buy Now!', 'membership-for-woocommerce' ) );

		}

		$this->global_class->payment_gateways_html( $plan_id );

		$buy_button .= '<form method="post" class="mwb_membership_buy_now_btn">
							<input type="hidden" id="mwb_membership_plan_id" name="plan_id" value="' . $plan_id . '">
							<input type="hidden" id="mwb_membership_plan_price" value="' . esc_html( $plan_price ) . '">
							<input type="hidden" name="membership_title" id="mwb_membership_title" value="' . $plan_title . '">
							<input type="button" data-mode="' . $mode . '" class="mwb_membership_buynow" name="mwb_membership_buynow" value="' . $content . '">
						</form>';
		$buy_button = apply_filters( 'membership_plan_buy_button_shortcode', $buy_button );

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

		$mode = $this->mwb_membership_validate_mode();

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

			$content = apply_filters( 'mwb_mebership_no_thanks_btn_txt', esc_html__( 'No Thanks!', 'membership-for-woocommerce' ) );

		}

		$no_thanks_button .= '<a class="mwb_membership_no_thanks button alt thickbox" data-mode="' . $mode . '" href="' . ( ! empty( $prod_id ) ? get_permalink( $prod_id ) : wc_get_page_permalink( 'shop' ) ) . '">' . $content . '</a>';
		$no_thanks_button  = apply_filters( 'membership_plan_no_thanks_button_shortcode', $no_thanks_button );

		return $no_thanks_button;
	}

	/**
	 * Hide all other shiiping methods, if free membership shipping available.
	 *
	 * @param array  $rates An array of shipping method rates.
	 * @param [type] $package Package of the shipping method.
	 * @return string
	 */
	public function mwb_membership_unset_shipping_if_membership_available( $rates, $package ) {

		$all_methods = array();
		$user = wp_get_current_user();
		if ( $this->global_class->plans_exist_check() == true ) {

			if ( ! is_user_logged_in() || ! in_array( 'member', (array) $user->roles ) ) {

				foreach ( $rates as $rate_key => $rate ) {
					// Excluding membership shipping methods.

					if ( 'mwb_membership_shipping' === $rate->get_method_id() ) {
						unset( $rates[ $rate_key ] );
					}
				}

				return $rates;
			}
		}

		$all_methods = array();

		foreach ( $rates as $rate_id => $rate ) {

			if ( 'mwb_membership_shipping' == $rate->method_id ) {

				$all_methods[ $rate_id ] = $rate;
				break;
			}
		}

		if ( empty( $all_methods ) ) {

			return $rates;
		} else {

			return $all_methods;
		}
	}



	/**
	 * Register the AJAX Callback for file upload.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_upload_receipt() {

		// Verify nonce.
		check_ajax_referer( 'auth_adv_nonce', 'auth_nonce' );

		if ( ! empty( $_FILES['receipt']['name'] ) ) {

			// phpcs:disable
			$file = map_deep( wp_unslash( $_FILES['receipt'] ), 'sanitize_text_field' ); // phpcs:ignore
			// phpcs:enable

			$file_ext = substr( strrchr( $file['name'], '.' ), 1 );

			$gateway_opt = new Mwb_Membership_Adv_Bank_Transfer();

			$settings   = ! empty( $gateway_opt->settings ) ? $gateway_opt->settings : array();
			$extensions = ! empty( $settings['support_formats'] ) ? $settings['support_formats'] : array();

			// If jpg file is selected and jpg is one of supported formats, 'jpeg' will be added as supported extension for jpg.
			if ( 'jpg' == $file_ext && in_array( 'jpg', $extensions, true ) ) {

				// if jpeg is already on of the supported formats, then don't add the extra supported extension.
				if ( ! in_array( 'jpeg', $extensions, true ) ) {
					$extensions[] = 'jpeg';
				}
			}
			$activity_class = new Membership_Activity_Helper( 'advance bacs receipt', 'uploads' );
			$receipt_data   = $activity_class->do_upload( $file, $extensions );
			echo wp_json_encode( $receipt_data );
			wp_die();
		}
	}

	/**
	 * Register the AJAX Callback for file removal.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_remove_current_receipt() {

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
	public function mwb_membership_get_states_public() {

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
	 * Ajax call for membership process payment.
	 *
	 * @param mixed $order_id  id of order.
	 */
	public function mwb_membership_process_payment( $order_id ) {

		$fields = array();
		$order = wc_get_order( $order_id );

		foreach ( $order->get_items() as $item_id => $item ) {
			$plan_id = $item->get_meta( '_mwb_plan_id' );
			$member_id = $item->get_meta( '_member_id' );
			$product_id = $item->get_data()['product_id'];
		}
		$mwb_membership_default_product = get_option( 'mwb_membership_default_product', '' );
		if ( $product_id == $mwb_membership_default_product ) {
			if ( $plan_id ) {

				if ( 'completed' == $order->get_status() ) {
					$order_st = 'complete';
				} elseif ( 'on-hold' == $order->get_status() || 'refunded' == $order->get_status() ) {
					$order_st = 'hold';
				} elseif ( 'pending' == $order->get_status() || 'failed' == $order->get_status() || 'processing' == $order->get_status() ) {
					$order_st = 'pending';
				} elseif ( 'cancelled' == $order->get_status() ) {
					$order_st = 'cancelled';
				}
				update_post_meta( $member_id, 'member_status', $order_st );

			} else {
				$plan_id   = WC()->session->get( 'plan_id' );
				$items    = $order->get_data()['line_items'];
				$keys     = array_keys( $items );

				wc_add_order_item_meta( $keys[0], '_mwb_plan_id', $plan_id );
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
					$user->set_role( 'member' ); // set them to whatever role you want using the full word.

					wc_add_order_item_meta( $keys[0], '_member_id', $member_data['member_id'] );

					if ( ! $member_id ) {
						$member_id = $member_data['member_id'];
					}

					$club_membership = get_post_meta( $plan_id, 'mwb_membership_club', true );

					$plan_obj = get_post_meta( $member_id, 'plan_obj', true );

					if ( ! empty( $club_membership ) ) {

						foreach ( $club_membership as $mem_ids ) {

							$product_ids = get_post_meta( $mem_ids, 'mwb_membership_plan_target_ids', true );
							if ( ! empty( $product_ids ) ) {
								$plan_obj['mwb_membership_plan_target_ids'] = unserialize( $plan_obj['mwb_membership_plan_target_ids'] );
								$plan_obj['mwb_membership_plan_target_ids'] = array_merge( $plan_obj['mwb_membership_plan_target_ids'], $product_ids );
								$plan_obj['mwb_membership_plan_target_ids'] = serialize( $plan_obj['mwb_membership_plan_target_ids'] );
							}

							$post_ids = get_post_meta( $mem_ids, 'mwb_membership_plan_post_target_ids', true );
							if ( ! empty( $post_ids ) ) {
								$plan_obj['mwb_membership_plan_post_target_ids'] = unserialize( $plan_obj['mwb_membership_plan_post_target_ids'] );
								$plan_obj['mwb_membership_plan_post_target_ids'] = array_merge( $plan_obj['mwb_membership_plan_post_target_ids'], $post_ids );
								$plan_obj['mwb_membership_plan_post_target_ids'] = serialize( $plan_obj['mwb_membership_plan_post_target_ids'] );
							}

							$cat_ids = get_post_meta( $mem_ids, 'mwb_membership_plan_target_categories', true );
							if ( ! empty( $cat_ids ) ) {
								$plan_obj['mwb_membership_plan_target_categories'] = unserialize( $plan_obj['mwb_membership_plan_target_categories'] );
								$plan_obj['mwb_membership_plan_target_categories'] = array_merge( $plan_obj['mwb_membership_plan_target_categories'], $cat_ids );
								$plan_obj['mwb_membership_plan_target_categories'] = serialize( $plan_obj['mwb_membership_plan_target_categories'] );
							}

							$tag_ids = get_post_meta( $mem_ids, 'mwb_membership_plan_target_tags', true );
							if ( ! empty( $tag_ids ) ) {
								$plan_obj['mwb_membership_plan_target_tags'] = unserialize( $plan_obj['mwb_membership_plan_target_tags'] );
								$plan_obj['mwb_membership_plan_target_tags'] = array_merge( $plan_obj['mwb_membership_plan_target_tags'], $tag_ids );
								$plan_obj['mwb_membership_plan_target_tags'] = serialize( $plan_obj['mwb_membership_plan_target_tags'] );
							}

							$ptags = get_post_meta( $mem_ids, 'mwb_membership_plan_target_post_tags', true );
							if ( ! empty( $ptags ) ) {
								$plan_obj['mwb_membership_plan_target_post_tags'] = unserialize( $plan_obj['mwb_membership_plan_target_post_tags'] );
								$plan_obj['mwb_membership_plan_target_post_tags'] = array_merge( $plan_obj['mwb_membership_plan_target_post_tags'], $ptags );
								$plan_obj['mwb_membership_plan_target_post_tags'] = serialize( $plan_obj['mwb_membership_plan_target_post_tags'] );
							}

							$pcats = get_post_meta( $mem_ids, 'mwb_membership_plan_target_post_categories', true );
							if ( ! empty( $pcats ) ) {
								$plan_obj['mwb_membership_plan_target_post_categories'] = unserialize( $plan_obj['mwb_membership_plan_target_post_categories'] );
								$plan_obj['mwb_membership_plan_target_post_categories'] = array_merge( $plan_obj['mwb_membership_plan_target_post_categories'], $pcats );
								$plan_obj['mwb_membership_plan_target_post_categories'] = serialize( $plan_obj['mwb_membership_plan_target_post_categories'] );
							}
							update_post_meta( $member_id, 'plan_obj', $plan_obj );

						}
					}
				}
			}
			$member_status = get_post_meta( $member_id, 'member_status' );

			// If manually completing membership then set its expiry date.
			if ( 'complete' == $member_status[0] ) {

				// Getting current activation date.
				$current_date = gmdate( 'Y-m-d' );

				$plan_obj = get_post_meta( $member_id, 'plan_obj', true );

				// Save expiry date in post.
				if ( ! empty( $plan_obj ) ) {

					$access_type = get_post_meta( $plan_obj['ID'], 'mwb_membership_plan_access_type', true );

					if ( 'delay_type' == $access_type ) {
						$time_duration      = get_post_meta( $plan_obj['ID'], 'mwb_membership_plan_time_duration', true );
						$time_duration_type = get_post_meta( $plan_obj['ID'], 'mwb_membership_plan_time_duration_type', true );

						$current_date = gmdate( 'Y-m-d', strtotime( $current_date . ' + ' . $time_duration . ' ' . $time_duration_type ) );

					}

					if ( 'lifetime' == $plan_obj['mwb_membership_plan_name_access_type'] ) {

						update_post_meta( $member_id, 'member_expiry', 'Lifetime' );

					} elseif ( 'limited' == $plan_obj['mwb_membership_plan_name_access_type'] ) {

						$duration = $plan_obj['mwb_membership_plan_duration'] . ' ' . $plan_obj['mwb_membership_plan_duration_type'];

						$expiry_date = strtotime( $current_date . $duration );

						update_post_meta( $member_id, 'member_expiry', $expiry_date );
					}
				}

				$user_id = get_current_user_id();
				$user = get_userdata( $user_id );
				$user_name = $user->data->display_name;
				$customer_email = WC()->mailer()->emails['membership_creation_email'];
				if ( ! empty( $customer_email ) ) {

					$email_status = $customer_email->trigger( $user_id, $plan_obj, $user_name, $expiry_date );

				}
			}
		}
	}

	/**
	 * Handle paypal transaction data.
	 */
	public function mwb_membership_save_transaction() {

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

		$access = false;
		$all_member_plans = array();
		$all_member_category = array();
		$all_member_tag = array();

		if ( ! empty( $product ) ) {

			$exclude = get_post_meta( $product->get_id(), '_mwb_membership_exclude', true );

			if ( 'yes' === $exclude ) {
				$access = true;
				return $access;
			}
			$user_id = get_current_user_id();

			$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );

			if ( ! empty( $current_memberships && is_array( $current_memberships ) ) ) {

				foreach ( $current_memberships as $key => $membership_id ) {

					if ( 'publish' == get_post_status( $membership_id ) ) {

						// Get Saved Plan Details.
						$membership_plan = get_post_meta( $membership_id, 'plan_obj', true );
						array_push( $all_member_plans, $membership_plan->ID );
						if ( empty( $membership_plan ) ) {
							continue;
						}

						$accessible_prod = $membership_plan['mwb_membership_plan_target_ids'] ? maybe_unserialize( $membership_plan['mwb_membership_plan_target_ids'] ) : array();
						$accessible_cat  = $membership_plan['mwb_membership_plan_target_categories'] ? maybe_unserialize( $membership_plan['mwb_membership_plan_target_categories'] ) : array();
						$accessible_tag  = $membership_plan['mwb_membership_plan_target_tags'] ? maybe_unserialize( $membership_plan['mwb_membership_plan_target_tags'] ) : array();

						if ( in_array( $product->get_id(), $accessible_prod ) || ( ! empty( $accessible_cat ) && has_term( $accessible_cat, 'product_cat' ) ) || ( ! empty( $accessible_tag ) && has_term( $accessible_tag, 'product_tag' ) ) ) {

							$access = true;

							$membership_status = get_post_meta( $membership_id, 'member_status', true );

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

			foreach ( $all_member_tag as $key => $value ) {

				foreach ( array_keys( $this->under_review_products, $value ) as $keys ) {
					unset( $this->under_review_products[ $keys ] );
				}
			}

			if ( ! empty( $all_member_plans ) ) {
				$args = array(
					'post_type'   => 'mwb_cpt_membership',
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
									$all_plan_accessible_prod = $single_plan->mwb_membership_plan_target_ids ? maybe_unserialize( $single_plan->mwb_membership_plan_target_ids ) : array();

									$all_plan_accessible_cat  = $single_plan->mwb_membership_plan_target_categories ? maybe_unserialize( $single_plan->mwb_membership_plan_target_categories ) : array();

									$all_plan_accessible_tag  = $single_plan->mwb_membership_plan_target_tags ? maybe_unserialize( $single_plan->mwb_membership_plan_target_tags ) : array();
								}
							}
						}
						if ( in_array( $product->get_id(), $all_plan_accessible_prod ) || ( ! empty( $all_plan_accessible_cat ) && has_term( $all_plan_accessible_cat, 'product_cat' ) ) || ( ! empty( $all_plan_accessible_tag ) && has_term( $all_plan_accessible_tag, 'product_tag' ) ) ) {

							$access = true;

							$membership_status = get_post_meta( $membership_id, 'member_status', true );

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
	public function mwb_membership_add_cart_discount( $cart ) {

		$cart_total = $cart->subtotal;

		$user_id = get_current_user_id();

		$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );
		$applied_offer_type_percentage = '';
		$applied_offer_price_percentage = '';
		$applied_offer_type_fixed = '';
		$applied_offer_price_fixed = '';

		if ( ! empty( $current_memberships && is_array( $current_memberships ) ) ) {

			foreach ( $current_memberships as $key => $membership_id ) {

				// Get Saved Plan Details.
				$membership_plan   = get_post_meta( $membership_id, 'plan_obj', true );
				$membership_status = get_post_meta( $membership_id, 'member_status', true );

				if ( empty( $membership_plan ) ) {
					continue;
				}

				$offer_type  = $membership_plan['mwb_membership_plan_offer_price_type'];
				$offer_price = ! empty( $membership_plan['mwb_memebership_plan_discount_price'] ) ? sanitize_text_field( $membership_plan['mwb_memebership_plan_discount_price'] ) : '';

				if ( 'complete' == $membership_status && '%' == $offer_type ) {

					// If % discount is given.

					if ( empty( $applied_offer_price_percentage ) ) {
						$applied_offer_price_percentage = $offer_price;
						$applied_offer_type_percentage = $offer_type;
					} elseif ( $applied_offer_price_percentage < $offer_price ) {
						$applied_offer_price_percentage = $offer_price;
						$applied_offer_type_percentage = $offer_type;
					}
				}
				if ( 'complete' == $membership_status && 'fixed' == $offer_type ) {

					// If % discount is given.

					if ( empty( $applied_offer_price_fixed ) ) {
						$applied_offer_price_fixed = $offer_price;
						$applied_offer_type_fixed = $offer_type;
					} elseif ( $applied_offer_price_fixed < $offer_price ) {
						$applied_offer_price_fixed = $offer_price;
						$applied_offer_type_fixed = $offer_type;

					}
				}
			}
		}

		if ( '%' == $applied_offer_type_percentage && ! empty( $applied_offer_price_percentage ) ) {

			// Discount % is given( no negatives, not more than 100, if 100% then price zero ).
			$applied_offer_price_percentage = floatval( sanitize_text_field( $applied_offer_price_percentage ) );

			// Range should be 0-100 only.
			$applied_offer_price_percentage = ( 100 < $applied_offer_price_percentage ) ? 100 : $applied_offer_price_percentage;
			$applied_offer_price_percentage = ( 0 > $applied_offer_price_percentage ) ? 0 : $applied_offer_price_percentage;
			$discount_percentage = $cart_total * ( $applied_offer_price_percentage / 100 );
		}

				// If fixed discount is given.
		if ( 'fixed' == $applied_offer_type_fixed && ! empty( $applied_offer_price_fixed ) ) {
			// When fixed price is given.
			$applied_offer_price_fixed = ( 0 > $applied_offer_price_fixed ) ? 0 : $applied_offer_price_fixed;
			$applied_offer_price_fixed = ( $cart_total < $applied_offer_price_fixed ) ? 0 : $applied_offer_price_fixed;

			$discount_fixed = $applied_offer_price_fixed;
		}

		if ( ! empty( $discount_percentage ) || ! empty( $discount_fixed ) ) {
			$discount = $discount_percentage > $discount_fixed ? $discount_percentage : $discount_fixed;
			$cart->add_fee( 'Membership Discount', -$discount, false );
		}

	}

	/**
	 * Check membership expiration on daily basis.
	 */
	public function mwb_membership_cron_expiry_check() {

		// Get all limited memberships.
		$limited_members = get_posts(
			array(
				'numberposts' => -1,
				'fields'      => 'ids', // return only ids.
				'post_type'   => 'mwb_cpt_members',
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

				$post   = get_post( $member_id );
				$user = get_userdata( $post->post_author );
				$expiry_date = get_post_meta( $member_id, 'member_expiry', true );
				$plan_obj = get_post_meta( $member_id, 'plan_obj', true );

				$current_date = time();

				$expiry_current = gmdate( 'Y-m-d', strtotime( $current_date . '+ 7 day' ) );

				$expiry_mail = gmdate( 'Y-m-d', strtotime( $expiry_date ) );

				$expiry = get_post_meta( $member_id, 'member_expiry', true );

				if ( 'Lifetime' == $expiry ) {
					$expiry_mail = 'Lifetime';
				} else {
					$expiry_mail = esc_html( ! empty( $expiry ) ? gmdate( 'Y-m-d', $expiry ) : '' );
				}

				if ( $expiry_date == $expiry_current ) {

					$user_name = $user->data->display_name;
					$customer_email = WC()->mailer()->emails['membership_to_expire_email'];
					if ( ! empty( $customer_email ) ) {

						$email_status = $customer_email->trigger( $post->post_author, $plan_obj, $user_name, $expiry_mail );

					}
				}

				if ( $expiry_date < $current_date ) {

					// Set member status to Expired.
					update_post_meta( $member_id, 'member_status', 'expired' );

					$customer_email = '';
					if ( ! empty( WC()->mailer()->emails['membership_expired_email'] ) ) {
						$customer_email = WC()->mailer()->emails['membership_expired_email'];
					}
					$expiry_mail = gmdate( 'Y-m-d', strtotime( $expiry_date ) );

					$expiry = get_post_meta( $member_id, 'member_expiry', true );

					if ( 'Lifetime' == $expiry ) {
						$expiry_mail = 'Lifetime';
					} else {
						$expiry_mail = esc_html( ! empty( $expiry ) ? gmdate( 'Y-m-d', $expiry ) : '' );
					}

					if ( ! empty( $customer_email ) ) {

						$email_status = $customer_email->trigger( $post->post_author, $plan_obj, $user_name, $expiry_mail );
					}
				}
			}
		}

		// Expired memberships.
		$expired_members = get_posts(
			array(
				'numberposts' => -1,
				'fields'      => 'ids', // return only ids.
				'post_type'   => 'mwb_cpt_members',
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

						$status = get_post_meta( $m_id, 'member_status', true );

						if ( 'complete' == $status ) {

							$other_member_exists = true;
						}
					}

					if ( 1 == count( $memberships ) ) {
						if ( false == $other_member_exists ) {
							$user->remove_role( 'member' );
						}
					} else {

						$remove_role = true;

						foreach ( $memberships as $key => $m_id ) {

							$status = get_post_meta( $m_id, 'member_status', true );

							if ( 'expired' != $status ) {

								$remove_role = false;
								break;
							}
						}

						// If removal required then remove role.
						if ( false == $other_member_exists ) {
							$user->remove_role( 'member' );
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
	public function mwb_membership_checkout() {

		check_ajax_referer( 'auth_adv_nonce', 'nonce' );
		$plan_id    = isset( $_POST['plan_id'] ) ? sanitize_text_field( wp_unslash( $_POST['plan_id'] ) ) : '';
		$plan_price = isset( $_POST['plan_price'] ) ? sanitize_text_field( wp_unslash( $_POST['plan_price'] ) ) : '';
		$plan_title = isset( $_POST['plan_title'] ) ? sanitize_text_field( wp_unslash( $_POST['plan_title'] ) ) : '';

		$mwb_membership_default_product = get_option( 'mwb_membership_default_product', '' );

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
	 * WooCommerce add cart item data.
	 *
	 * @param array $cart_item_data cart item data.
	 * @param int   $product_id product id.
	 * @return array
	 */
	public function add_membership_product_price_to_cart_item_data( $cart_item_data, $product_id ) {
		$product = wc_get_product( $product_id );

		global $wp_session;

		if ( empty( $wp_session ) ) {

			$cart_item_data['plan_price'] = WC()->session->get( 'plan_price' );
			$cart_item_data['plan_title'] = WC()->session->get( 'plan_title' );
		} else {

			$cart_item_data['plan_price'] = $wp_session['plan_price'];
			$cart_item_data['plan_title'] = $wp_session['plan_title'];
		}
		$cart_item_data = apply_filters( 'add_membership_product_price_to_cart_item_data', $cart_item_data );

		return $cart_item_data;

	}

	/**
	 * Set topup product price at run time.
	 *
	 * @param OBJECT $cart cart.
	 */
	public function mwb_membership_set_membership_product_price( $cart ) {

		$mwb_membership_default_product = get_option( 'mwb_membership_default_product', '' );

		$product = wc_get_product( $mwb_membership_default_product );

		if ( ! $product && empty( $cart->cart_contents ) ) {
			return;
		}

		foreach ( $cart->cart_contents as $key => $value ) {

			if ( isset( $value['plan_price'] ) && $value['plan_price'] && $product->get_id() == $value['product_id'] ) {
				$value['data']->set_price( $value['plan_price'] );
			}

			if ( isset( $value['plan_title'] ) && $value['plan_title'] && $product->get_id() == $value['product_id'] ) {

				// Set the new name (WooCommerce versions 2.5.x to 3+).
				if ( method_exists( $value['data'], 'set_name' ) ) {
					$value['data']->set_name( $value['plan_title'] );
				} else {
					$value['data']->post->post_title = $value['plan_title'];
				}
			}
		}
	}

	/**
	 * Make rechargeable product purchasable
	 *
	 * @param boolean $is_purchasable allow product to be purchased.
	 * @param mixed   $product object of product.
	 * @return boolean
	 */
	public function mwb_membership_make_membership_product_purchasable( $is_purchasable, $product ) {

		$mwb_membership_default_product = get_option( 'mwb_membership_default_product', '' );

		$membership_product = wc_get_product( $mwb_membership_default_product );

		if ( $membership_product ) {

			if ( $mwb_membership_default_product == $product->get_id() ) {

				$is_purchasable = true;
			}
		}
		$is_purchasable = apply_filters( 'add_membership_product_price_to_is_purchasable', $is_purchasable );

		$user = wp_get_current_user();
		if ( is_user_logged_in() && in_array( 'member', (array) $user->roles ) ) {
			$data                = $this->custom_query_data;
			$user_id             = get_current_user_id();
			$existing_plan_id    = array();
			$existing_plan_product   = array();
			$plan_existing       = false;
			$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );

			if ( ! empty( $current_memberships ) && is_array( $current_memberships ) ) {

				foreach ( $current_memberships as $key => $membership_id ) {

					$member_status = get_post_meta( $membership_id, 'member_status', true );

					if ( ! empty( $member_status ) && 'complete' == $member_status ) {

						$active_plan = get_post_meta( $membership_id, 'plan_obj', true );
						array_push( $existing_plan_id, $active_plan['ID'] );
						$target_ids      = get_post_meta( $active_plan['ID'], 'mwb_membership_plan_target_ids', true );
						$target_cat_ids  = get_post_meta( $active_plan['ID'], 'mwb_membership_plan_target_categories', true );
						$target_tag_ids  = get_post_meta( $active_plan['ID'], 'mwb_membership_plan_target_tags', true );

						if ( in_array( $product->get_id(), $target_ids ) || ( ! empty( $target_cat_ids ) && has_term( $target_cat_ids, 'product_cat' ) ) || ( ! empty( $target_tag_ids ) && has_term( $target_tag_ids, 'product_tag' ) ) ) {

							array_push( $existing_plan_product, $product->get_id() );

						}
					}
				}
				if ( false == $plan_existing ) {

					foreach ( $data as $plan ) {
						$mwb_membership_default_plans_page_id = get_option( 'mwb_membership_default_plans_page', '' );

						if ( ! empty( $mwb_membership_default_plans_page_id ) && 'publish' == get_post_status( $mwb_membership_default_plans_page_id ) ) {
							$page_link = get_page_link( $mwb_membership_default_plans_page_id );
						}

						if ( ! in_array( $plan['ID'], $existing_plan_id ) ) {

								$page_link_found = false;
								$target_ids      = get_post_meta( $plan['ID'], 'mwb_membership_plan_target_ids', true );
								$target_cat_ids  = get_post_meta( $plan['ID'], 'mwb_membership_plan_target_categories', true );
								$target_tag_ids  = get_post_meta( $plan['ID'], 'mwb_membership_plan_target_tags', true );

							if ( in_array( $product->get_id(), $target_ids ) || ( ! empty( $target_cat_ids ) && has_term( $target_cat_ids, 'product_cat' ) ) || ( ! empty( $target_tag_ids ) && has_term( $target_tag_ids, 'product_tag' ) ) ) {

								if ( ! in_array( $product->get_id(), $existing_plan_product ) ) {
									$is_purchasable = false;

									if ( $product->is_type( 'variable' ) ) {
										$product = wc_get_product( $product->get_id() );
										$current_products = $product->get_children();
										foreach ( $current_products as $key => $value ) {
											// code...

											array_push( $this->exclude_other_plan_products, $value );
										}
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
	 * Undocumented function
	 *
	 * @return void
	 */
	public function mwb_membership_buy_now_add_to_cart() {
		// select product ID.

		if ( WC()->session->__isset( 'product_id' ) ) {

			$cart_item_data = add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_membership_product_price_to_cart_item_data' ), 10, 2 );
			// if cart empty, add it to cart.
			WC()->cart->empty_cart();
			WC()->cart->add_to_cart( WC()->session->get( 'product_id' ) );
		}
		WC()->session->__unset( 'product_id' );
		global $post;

	}

	/**
	 *  Adding distraction free mode to the offers page.
	 *
	 * @param mixed $page_template Default template for the page.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_plan_page_template( $page_template ) {

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
					's'              => '[mwb_membership_default_page_identification]',
					'order'          => 'ASC',
					'orderby'        => 'ID',
				)
			),
			$pages_available
		);

		foreach ( $pages_available as $single_page ) {

			if ( is_page( $single_page->ID ) ) {

				$page_template = plugin_dir_path( __FILE__ ) . '/partials/templates/membership-templates/mwb-membership-template.php';
			}
		}
		$page_template = apply_filters( 'mwb_membership_plan_page_template', $page_template );
		return $page_template;
	}


	/**
	 * Creating shipping method for membership.
	 *
	 * @param array $methods an array of shipping methods.
	 *
	 * @since 1.0.0
	 */
	public function mwb_membership_for_woo_create_shipping_method( $methods ) {

		if ( ! class_exists( 'Mwb_Membership_free_shipping_method' ) ) {
			/**
			 * Custom shipping class for membership.
			 */
			require_once plugin_dir_path( __FILE__ ) . '/classes/class-mwb-membership-free-shipping-method.php'; // Including class file.
			new Mwb_Membership_Free_Shipping_Method();
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
	public function mwb_membership_for_woo_add_shipping_method( $methods ) {

		$methods['mwb_membership_shipping'] = 'Mwb_Membership_Free_Shipping_Method';

		$methods = apply_filters( 'mwb_membership_for_woo_add_shipping_method', $methods );

		return $methods;
	}

		/**
		 * Adding membership shipping method.
		 *
		 * @param array $methods an array of shipping methods.
		 *
		 * @since 1.0.0
		 */
	public function mwb_membership_add_to_cart_url( $methods ) {

		global $product;

		$user = wp_get_current_user();
		if ( is_user_logged_in() || in_array( 'member', (array) $user->roles ) ) {
			$data                = $this->custom_query_data;
			$user_id             = get_current_user_id();
			$existing_plan_id    = array();
			$existing_plan_product   = array();

			$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );

			if ( ! empty( $current_memberships ) && is_array( $current_memberships ) ) {

				foreach ( $current_memberships as $key => $membership_id ) {

					$member_status = get_post_meta( $membership_id, 'member_status', true );

					if ( ! empty( $member_status ) && 'complete' == $member_status ) {

						$active_plan = get_post_meta( $membership_id, 'plan_obj', true );
						array_push( $existing_plan_id, $active_plan['ID'] );
						$target_ids      = get_post_meta( $active_plan['ID'], 'mwb_membership_plan_target_ids', true );
						$target_cat_ids  = get_post_meta( $active_plan['ID'], 'mwb_membership_plan_target_categories', true );
						$target_tag_ids  = get_post_meta( $active_plan['ID'], 'mwb_membership_plan_target_tags', true );

						if ( in_array( get_the_ID(), $target_ids ) || ( ! empty( $target_cat_ids ) && has_term( $target_cat_ids, 'product_cat' ) ) || ( ! empty( $target_tag_ids ) && has_term( $target_tag_ids, 'product_tag' ) ) ) {

							array_push( $existing_plan_product, get_the_ID() );

						}
					}
				}
			}
			foreach ( $data as $plan ) {
				$mwb_membership_default_plans_page_id = get_option( 'mwb_membership_default_plans_page', '' );

				if ( ! empty( $mwb_membership_default_plans_page_id ) && 'publish' == get_post_status( $mwb_membership_default_plans_page_id ) ) {
							$page_link = get_page_link( $mwb_membership_default_plans_page_id );
				}

				if ( ! in_array( $plan['ID'], $existing_plan_id ) ) {

							$page_link_found = false;
							$target_ids      = get_post_meta( $plan['ID'], 'mwb_membership_plan_target_ids', true );
							$target_cat_ids  = get_post_meta( $plan['ID'], 'mwb_membership_plan_target_categories', true );
							$target_tag_ids  = get_post_meta( $plan['ID'], 'mwb_membership_plan_target_tags', true );

					if ( in_array( get_the_ID(), $target_ids ) || ( ! empty( $target_cat_ids ) && has_term( $target_cat_ids, 'product_cat' ) ) || ( ! empty( $target_tag_ids ) && has_term( $target_tag_ids, 'product_tag' ) ) ) {

						if ( ! in_array( get_the_ID(), $existing_plan_product ) ) {

							 $methods = '<div class="not_accessible"></div>';
							 echo wp_kses_post( $methods );
						}
					}
				}
			}
		}

	}




}
// End of class.
