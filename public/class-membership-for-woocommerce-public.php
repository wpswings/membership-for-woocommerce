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

		$button_text         = get_option( 'wps_membership_change_buy_now_text', '' );
		$wps_mfw_single_plan = isset( $_GET['plan_id'] ) && isset( $_GET['prod_id'] ) ? 'yes' : '';
		wp_localize_script(
			$this->plugin_name,
			'membership_public_obj',
			array(
				'ajaxurl'                 => admin_url( 'admin-ajax.php' ),
				'nonce'                   => wp_create_nonce( 'auth_adv_nonce' ),
				'buy_now_text'            => $button_text,
				'single_plan'             => $wps_mfw_single_plan,
				'plan_page_template'      => get_option( 'wps_membership_plan_page_temp' ),
				'dark_mode'               => get_option( 'wps_membership_plan_page_dark_mode' ),
				'enable_new_layout'       => get_option( 'wps_msfw_enable_new_layout_settings' ),
				'new_layout_color'        => empty( get_option( 'wps_msfw_new_layout_color' ) ) ? '#ff7700' : get_option( 'wps_msfw_new_layout_color' ),
				'new_dashboard_color'     => empty( get_option( 'wps_msfw_dashboard_color' ) ) ? '#7BCD66' : get_option( 'wps_msfw_dashboard_color' ),
				'enable_login_and_signup' => $this->wps_mfw_is_login_and_signup_enable() ? 'on' : 'off',
				'account_page_colors'     => ! empty( get_option( 'wps_mfw_login_form_color' ) ) ? get_option( 'wps_mfw_login_form_color' ) : '#9BC53D',
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

		// enqueue google captcha api library.
		if ( $this->wps_mfw_is_google_captcha_enable() ) {

			wp_enqueue_script( 'wpcaptcha-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), $this->version, true );
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
				'post_type'   => 'wps_cpt_membership',
				'post_status' => 'publish',
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

		if ( 'on' === get_option( 'wps_msfw_enable_members_dashboard', 'off' ) ) {

			$logout = $items['customer-logout'];
			unset( $items['customer-logout'] );

			// Placing the custom tab just above logout tab.
			$items['wps-membership-tab'] = esc_html__( 'Membership Details', 'membership-for-woocommerce' );
			$items['customer-logout']    = $logout;
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

			$title = esc_html__( 'Membership Details', 'membership-for-woocommerce' );
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
			'wps_membership_email_subject'     => esc_html__( 'Thank you for Shopping, Do not reply.', 'membership-for-woocommerce' ),
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
	 * Hide the auto-generated membership product description on the frontend.
	 *
	 * @param string     $description Product description.
	 * @param WC_Product $product Product object.
	 * @return string
	 */
	public function wps_membership_hide_default_product_content( $description, $product ) {
		if ( is_admin() || empty( $product ) || ! is_object( $product ) ) {
			return $description;
		}

		$default_product_id = absint( get_option( 'wps_membership_default_product', 0 ) );
		if ( $default_product_id > 0 && (int) $product->get_id() === $default_product_id ) {
			return '';
		}

		return $description;
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
		$membership_product             = wc_get_product( $wps_membership_default_product );
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

		if ( true == $this->global_class->plans_exist_check() ) {
			$matching_plan_ids = $this->get_matching_plan_ids_for_product( $product->get_id() );
			if ( ! empty( $matching_plan_ids ) ) {
				$user           = wp_get_current_user();
				$is_member_meta = get_user_meta( $user->ID, 'is_member' );
				$is_member_meta = ! empty( $is_member_meta ) && is_array( $is_member_meta ) ? $is_member_meta : array();
				$is_member      = is_user_logged_in() && in_array( 'member', (array) $is_member_meta, true );

				if ( ! $is_member ) {
					$is_purchasable = false;
				} else {
					$is_purchasable = ( true == $this->is_accessible_to_member( $product ) );

					if ( ! empty( $this->under_review_products ) && in_array( $product->get_id(), $this->under_review_products, true ) ) {
						$is_purchasable = false;
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

		$user           = wp_get_current_user();
		$is_member_meta = get_user_meta( $user->ID, 'is_member' );
		$is_member_meta = ! empty( $is_member_meta ) && is_array( $is_member_meta ) ? $is_member_meta : array();
		if ( true == $this->global_class->plans_exist_check() ) {

			if ( ! is_user_logged_in() && ! in_array( 'member', (array) $is_member_meta, true ) ) {
				$matching_plan_ids = $this->get_matching_plan_ids_for_product( $product->get_id() );
				if ( ! empty( $matching_plan_ids ) ) {
					return '';
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

		if ( empty( $product ) || ! is_object( $product ) ) {
			return;
		}

		$product_id = $product->get_id();
		if ( empty( $product_id ) ) {
			return;
		}

		$matching_plan_ids = $this->get_matching_plan_ids_for_product( $product_id );
		if ( empty( $matching_plan_ids ) ) {
			return;
		}

		$user                  = wp_get_current_user();
		$is_member_meta        = get_user_meta( $user->ID, 'is_member', true );
		$is_member_meta        = ! empty( $is_member_meta ) && is_array( $is_member_meta ) ? $is_member_meta : array();
		$is_member             = is_user_logged_in() && in_array( 'member', (array) $is_member_meta, true );
		$is_membership_product = $this->wps_membership_products_on_shop_page( true, $product );
		$already_included      = array();
		$owned_plan_ids        = array();
		$pending_plan_ids      = array();
		$offer_cards           = array();
		$show_review_notice    = false;
		$page_link             = '';

		$wps_membership_default_plans_page_id = get_option( 'wps_membership_default_plans_page', '' );
		if ( ! empty( $wps_membership_default_plans_page_id ) && 'publish' === get_post_status( $wps_membership_default_plans_page_id ) ) {
			$page_link = get_page_link( $wps_membership_default_plans_page_id );
		}

		if ( empty( $page_link ) ) {
			return;
		}

		$current_memberships = get_user_meta( get_current_user_id(), 'mfw_membership_id', true );
		if ( ! empty( $current_memberships ) && is_array( $current_memberships ) ) {
			foreach ( $current_memberships as $membership_id ) {
				$member_status   = wps_membership_get_meta_data( $membership_id, 'member_status', true );
				$active_plan     = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
				$active_plan_id  = ! empty( $active_plan['ID'] ) ? absint( $active_plan['ID'] ) : 0;

				if ( empty( $active_plan_id ) ) {
					continue;
				}

				$included_plan_ids = $this->get_all_included_membership( $active_plan_id );
				$included_plan_ids = ! empty( $included_plan_ids ) && is_array( $included_plan_ids ) ? array_map( 'absint', $included_plan_ids ) : array();

				if ( ! empty( $member_status ) && 'expired' !== $member_status ) {
					$owned_plan_ids[] = $active_plan_id;
					$owned_plan_ids   = array_merge( $owned_plan_ids, $included_plan_ids );
				}

				if ( ! empty( $member_status ) && 'complete' !== $member_status && 'expired' !== $member_status ) {
					$pending_plan_ids[] = $active_plan_id;
					$pending_plan_ids   = array_merge( $pending_plan_ids, $included_plan_ids );
				}
			}
		}

		$owned_plan_ids   = array_values( array_unique( array_filter( array_map( 'absint', $owned_plan_ids ) ) ) );
		$pending_plan_ids = array_values( array_unique( array_filter( array_map( 'absint', $pending_plan_ids ) ) ) );

		if ( 'yes' === wps_membership_get_meta_data( $product_id, '_wps_membership_exclude', true ) ) {
			return;
		}

		if ( ! $product->is_purchasable() && $this->global_class->plans_exist_check() && function_exists( 'is_product' ) && is_product() ) {
			foreach ( $matching_plan_ids as $plan_id ) {
				$plan_title = get_the_title( $plan_id );
				if ( empty( $plan_title ) ) {
					continue;
				}

				if ( ! empty( $this->under_review_products ) && in_array( $product_id, $this->under_review_products, true ) && in_array( $plan_id, $pending_plan_ids, true ) ) {
					$show_review_notice = true;
					continue;
				}

				if ( $is_member && in_array( $plan_id, $owned_plan_ids, true ) ) {
					continue;
				}

				$offer_cards[] = array(
					'plan_id' => $plan_id,
					'title'   => $plan_title,
					'link'    => add_query_arg(
						array(
							'plan_id' => $plan_id,
							'prod_id' => $product_id,
						),
						$page_link
					),
				);
				$already_included[] = $plan_id;
			}
		}

		if ( true === $is_membership_product && $is_member ) {
			foreach ( $matching_plan_ids as $plan_id ) {
				if ( in_array( $plan_id, $owned_plan_ids, true ) || in_array( $plan_id, $already_included, true ) ) {
					continue;
				}

				$plan_title = get_the_title( $plan_id );
				if ( empty( $plan_title ) ) {
					continue;
				}

				$offer_cards[] = array(
					'plan_id' => $plan_id,
					'title'   => $plan_title,
					'link'    => add_query_arg(
						array(
							'plan_id' => $plan_id,
							'prod_id' => $product_id,
						),
						$page_link
					),
				);
			}
		}

		$offer_cards = array_values( array_reduce(
			$offer_cards,
			function( $carry, $offer_card ) {
				$plan_id = ! empty( $offer_card['plan_id'] ) ? absint( $offer_card['plan_id'] ) : 0;
				if ( empty( $plan_id ) ) {
					return $carry;
				}

				$carry[ $plan_id ] = $offer_card;
				return $carry;
			},
			array()
		) );

		if ( empty( $offer_cards ) && ! $show_review_notice ) {
			return;
		}

		if ( $show_review_notice ) {
			?>
			<div class="product-meta product-meta-review">
				<span><b><?php esc_html_e( 'Membership Under Review', 'membership-for-woocommerce' ); ?></b></span>
			</div>
			<?php
		}

		if ( empty( $offer_cards ) ) {
			return;
		}

		$section_title = $is_member ? esc_html__( 'More membership options', 'membership-for-woocommerce' ) : esc_html__( 'Membership options for this product', 'membership-for-woocommerce' );
		$section_copy  = $is_member ? esc_html__( 'Compare the available plans attached to this product and open the one that fits the benefits you want to add.', 'membership-for-woocommerce' ) : esc_html__( 'Choose a membership plan to unlock member pricing and complete this purchase through the plan flow.', 'membership-for-woocommerce' );
		$cta_label     = $is_member ? esc_html__( 'Review Plan', 'membership-for-woocommerce' ) : esc_html__( 'Choose Plan', 'membership-for-woocommerce' );
		?>
		<section class="mfw-single-product-membership mfw-single-product-membership--<?php echo esc_attr( $is_member ? 'member' : 'guest' ); ?>">
			<div class="mfw-single-product-membership__intro">
				<span class="mfw-single-product-membership__eyebrow"><?php esc_html_e( 'Membership Access', 'membership-for-woocommerce' ); ?></span>
				<h3><?php echo esc_html( $section_title ); ?></h3>
				<p><?php echo esc_html( $section_copy ); ?></p>
			</div>
			<div class="mfw-single-product-membership__grid mfw-single-product-membership__grid--count-<?php echo esc_attr( min( 4, max( 1, count( $offer_cards ) ) ) ); ?>">
				<?php foreach ( $offer_cards as $offer_card ) : ?>
					<article class="mfw-single-product-membership__card">
						<span class="mfw-single-product-membership__card-label"><?php esc_html_e( 'Plan', 'membership-for-woocommerce' ); ?></span>
						<h4><?php echo esc_html( $offer_card['title'] ); ?></h4>
						<p><?php echo esc_html( $is_member ? __( 'Open this plan to review its benefits and continue with the membership purchase path for this product.', 'membership-for-woocommerce' ) : __( 'Open this plan, review the included perks, and continue to buy this product as a member.', 'membership-for-woocommerce' ) ); ?></p>
						<a class="button alt mfw-membership mfw-single-product-membership__cta" href="<?php echo esc_url( $offer_card['link'] ); ?>" target="_blank"><?php echo esc_html( $cta_label ); ?></a>
					</article>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
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
		$terms                   = wp_get_post_terms( $product_id, 'product_tag' );
		if ( count( $terms ) > 0 && is_array( $terms ) ) {
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
	 * Get all membership plan titles mapped to a product.
	 *
	 * @param int $product_id Product ID.
	 * @return array
	 */
	public function get_matching_plan_ids_for_product( $product_id ) {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return array();
		}

		$product_id = absint( $product_id );
		if ( empty( $product_id ) ) {
			return array();
		}

		$plan_ids = array();
		$data     = $this->custom_query_data;

		if ( ! empty( $data ) && is_array( $data ) ) {
			foreach ( $data as $plan ) {
				$plan_id = ! empty( $plan['ID'] ) ? absint( $plan['ID'] ) : 0;
				if ( empty( $plan_id ) ) {
					continue;
				}

				$target_ids         = maybe_unserialize( wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_target_ids', true ) );
				$target_cat_ids     = maybe_unserialize( wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_target_categories', true ) );
				$target_tag_ids     = maybe_unserialize( wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_target_tags', true ) );
				$offer_product_ids  = maybe_unserialize( wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_target_disc_ids', true ) );
				$offer_category_ids = maybe_unserialize( wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_target_disc_categories', true ) );
				$offer_tag_ids      = maybe_unserialize( wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_target_disc_tags', true ) );

				$target_ids         = is_array( $target_ids ) ? array_map( 'absint', $target_ids ) : array();
				$target_cat_ids     = is_array( $target_cat_ids ) ? array_map( 'absint', $target_cat_ids ) : array();
				$target_tag_ids     = is_array( $target_tag_ids ) ? array_map( 'absint', $target_tag_ids ) : array();
				$offer_product_ids  = is_array( $offer_product_ids ) ? array_map( 'absint', $offer_product_ids ) : array();
				$offer_category_ids = is_array( $offer_category_ids ) ? array_map( 'absint', $offer_category_ids ) : array();
				$offer_tag_ids      = is_array( $offer_tag_ids ) ? array_map( 'absint', $offer_tag_ids ) : array();

				$all_product_ids  = array_unique( array_merge( $target_ids, $offer_product_ids ) );
				$all_category_ids = array_unique( array_merge( $target_cat_ids, $offer_category_ids ) );
				$all_tag_ids      = array_unique( array_merge( $target_tag_ids, $offer_tag_ids ) );

				$matches_product = in_array( $product_id, $all_product_ids, true );
				$matches_cat     = ! empty( $all_category_ids ) && has_term( $all_category_ids, 'product_cat', $product_id );
				$matches_tag     = ! empty( $all_tag_ids ) && has_term( $all_tag_ids, 'product_tag', $product_id );

				if ( $matches_product || $matches_cat || $matches_tag ) {
					$plan_ids[] = $plan_id;
				}
			}
		}

		$attached_plan_id = absint( wps_membership_get_meta_data( $product_id, 'wps_membership_plan_with_product', true ) );
		if ( ! empty( $attached_plan_id ) ) {
			$plan_ids[] = $attached_plan_id;
		}

		$plan_ids = array_values( array_unique( array_filter( array_map( 'absint', $plan_ids ) ) ) );
		return $plan_ids;
	}

	/**
	 * Get all non-expired membership plan IDs for a user grouped by access status.
	 *
	 * @param int $user_id User ID.
	 * @return array
	 */
	public function get_user_membership_plan_ids_by_status( $user_id ) {
		$plan_ids = array(
			'owned'    => array(),
			'complete' => array(),
			'pending'  => array(),
		);

		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return $plan_ids;
		}

		$user_id = absint( $user_id );
		if ( empty( $user_id ) ) {
			return $plan_ids;
		}

		$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );
		if ( empty( $current_memberships ) || ! is_array( $current_memberships ) ) {
			return $plan_ids;
		}

		foreach ( $current_memberships as $membership_id ) {
			if ( 'publish' !== get_post_status( $membership_id ) && 'draft' !== get_post_status( $membership_id ) ) {
				continue;
			}

			$membership_status = wps_membership_get_meta_data( $membership_id, 'member_status', true );
			if ( empty( $membership_status ) || 'expired' === $membership_status ) {
				continue;
			}

			$membership_plan = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
			$active_plan_id  = 0;
			if ( is_array( $membership_plan ) && ! empty( $membership_plan['ID'] ) ) {
				$active_plan_id = absint( $membership_plan['ID'] );
			} elseif ( is_object( $membership_plan ) && ! empty( $membership_plan->ID ) ) {
				$active_plan_id = absint( $membership_plan->ID );
			}

			if ( empty( $active_plan_id ) ) {
				continue;
			}

			$membership_plan_ids = array_merge( array( $active_plan_id ), $this->get_all_included_membership( $active_plan_id ) );
			$membership_plan_ids = array_values( array_unique( array_filter( array_map( 'absint', $membership_plan_ids ) ) ) );
			$plan_ids['owned']   = array_merge( $plan_ids['owned'], $membership_plan_ids );

			if ( 'complete' === $membership_status ) {
				$plan_ids['complete'] = array_merge( $plan_ids['complete'], $membership_plan_ids );
			} elseif ( in_array( $membership_status, array( 'pending', 'hold' ), true ) ) {
				$plan_ids['pending'] = array_merge( $plan_ids['pending'], $membership_plan_ids );
			}
		}

		$plan_ids['owned']    = array_values( array_unique( array_filter( array_map( 'absint', $plan_ids['owned'] ) ) ) );
		$plan_ids['complete'] = array_values( array_unique( array_filter( array_map( 'absint', $plan_ids['complete'] ) ) ) );
		$plan_ids['pending']  = array_values( array_unique( array_filter( array_map( 'absint', $plan_ids['pending'] ) ) ) );
		return $plan_ids;
	}

	/**
	 * Get all membership plan titles mapped to a product.
	 *
	 * @param int $product_id Product ID.
	 * @return array
	 */
	public function get_matching_plan_titles_for_product( $product_id ) {
		$plan_titles = array();

		foreach ( $this->get_matching_plan_ids_for_product( $product_id ) as $plan_id ) {
			$plan_title = get_the_title( $plan_id );
			if ( ! empty( $plan_title ) ) {
				$plan_titles[] = $plan_title;
			}
		}

		return array_values( array_unique( $plan_titles ) );
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

		$product_id         = is_object( $product ) ? $product->get_id() : get_the_ID();
		$is_product_exclude = false;
		if ( $this->global_class->plans_exist_check() == true ) {

			$data = $this->custom_query_data;
			if ( ! empty( $data ) && is_array( $data ) ) {

				$exclude_product = apply_filters( 'wps_membership_exclude_product', array(), $product_id );
				$is_product_exclude = apply_filters( 'wps_membership_is_exclude_product', $exclude_product, $data, $is_product_exclude );

				if ( $is_product_exclude ) {
					return;
				}

				$output = implode( ' | ', $this->get_matching_plan_titles_for_product( $product_id ) );
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
								<span class="mfw-product-meta-membership__eyebrow"><?php esc_html_e( 'Membership Offer', 'membership-for-woocommerce' ); ?></span>
								<strong class="mfw-product-meta-membership__title"><?php esc_html_e( 'Eligible for member pricing', 'membership-for-woocommerce' ); ?></strong>
								<span class="mfw-product-meta-membership__plans"><?php echo esc_html( $output ); ?></span>
							</div>
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
	 * @since    3.0.2
	 */
	public function wps_membership_validate_mode() {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return false;
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

		$mode = $this->wps_membership_validate_mode();

		ob_start();

		if ( isset( $_GET['plan_id'] ) && isset( $_GET['prod_id'] ) ) {

			$plan_id = sanitize_text_field( wp_unslash( $_GET['plan_id'] ) );
			$prod_id = sanitize_text_field( wp_unslash( $_GET['prod_id'] ) );

			$plan_title = get_the_title( $plan_id );
			$plan_price = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_price', true );

			if ( function_exists( 'wps_mmcsfw_admin_fetch_currency_rates_from_base_currency' ) ) {
				$plan_price = wps_mmcsfw_admin_fetch_currency_rates_from_base_currency( '', $plan_price );
			}

			$plan_currency = get_woocommerce_currency_symbol();
			if ( function_exists( 'wps_mmcsfw_get_custom_currency_symbol' ) ) {
				$plan_currency = wps_mmcsfw_get_custom_currency_symbol( '' );
			}

			$plan_desc       = get_post_field( 'post_content', $plan_id );
			$plan_info       = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_info', true );
			$product_title   = get_the_title( $prod_id );
			$product_link    = get_permalink( $prod_id );
			$plan_summary    = wp_trim_words( wp_strip_all_tags( $plan_desc ), 26, '...' );
			$plan_info_html  = ! empty( $plan_info ) ? wp_kses_post( $plan_info ) : '<p>' . esc_html__( 'Unlock pricing benefits, member access, and offer-based perks connected to this plan.', 'membership-for-woocommerce' ) . '</p>';

			/**
			 * Filter for membership plan.
			 *
			 * @since 1.0.0
			 */
			$offer_banner_text = apply_filters( 'wps_membership_plan_default_banner_txt', esc_html__( 'One Membership, Many Benefits', 'membership-for-woocommerce' ) );
			/**
			 * Filter for membership plan.
			 *
			 * @since 1.0.0
			 */
			$offer_buy_now_txt = apply_filters( 'wps_membership_plan_default_buy_now_txt', esc_html__( 'Buy Now!', 'membership-for-woocommerce' ) );
			/**
			 * Filter for membership plan.
			 *
			 * @since 1.0.0
			 */
			$offer_no_thnks_txt = apply_filters( 'wps_membership_plan_default_no_thanks_txt', esc_html__( 'No thanks!', 'membership-for-woocommerce' ) );
			?>
			<div class="wps-mfw-plan-page">
				<div class="wps-mfw-plan-shell">
					<section class="wps-mfw-plan-hero">
						<div class="wps-mfw-plan-hero__copy">
							<span class="wps-mfw-plan-eyebrow"><?php esc_html_e( 'Membership Offer', 'membership-for-woocommerce' ); ?></span>
							<h1 class="wps-mfw-plan-title"><?php echo esc_html( ucwords( $plan_title ) ); ?></h1>
							<p class="wps-mfw-plan-tagline"><?php echo esc_html( trim( $offer_banner_text ) ); ?></p>
							<div class="wps-mfw-plan-price-inline">
								<span><?php esc_html_e( 'Today', 'membership-for-woocommerce' ); ?></span>
								<strong><?php echo esc_html( sprintf( '%s %s', $plan_currency, $plan_price ) ); ?></strong>
							</div>
							<div class="wps-mfw-plan-desc"><?php echo wp_kses_post( $plan_desc ); ?></div>
						</div>

						<aside class="wps-mfw-plan-summary">
							<span class="wps-mfw-plan-summary__badge"><?php esc_html_e( 'Selected Plan', 'membership-for-woocommerce' ); ?></span>
							<h2><?php echo esc_html( ucwords( $plan_title ) ); ?></h2>
							<p class="wps-mfw-plan-summary__product">
								<?php esc_html_e( 'Linked product:', 'membership-for-woocommerce' ); ?>
								<strong><?php echo esc_html( $product_title ); ?></strong>
							</p>
							<div class="wps-mfw-plan-summary__price"><?php echo esc_html( sprintf( '%s %s', $plan_currency, $plan_price ) ); ?></div>

							<div class="wps-mfw-plan-actions">
								<form class="wps_membership_buy_now_btn wps-mfw-plan-buy-form thickbox" method="post">
									<input type="hidden" id="wps_membership_plan_price" value="<?php echo esc_attr( $plan_price ); ?>">
									<input type="hidden" id="wps_membership_plan_id" value="<?php echo esc_attr( $plan_id ); ?>">
									<input type="hidden" name="membership_title" id="wps_membership_title" value="<?php echo esc_attr( $plan_title ); ?>">
									<input type="hidden" name="membership_id" value="<?php echo esc_attr( $plan_id ); ?>">
									<input type="submit" data-mode="<?php echo esc_attr( $mode ); ?>" class="wps_membership_buynow" name="wps_membership_buynow" value="<?php echo esc_attr( $offer_buy_now_txt ); ?>">
								</form>
								<a class="wps_membership_no_thanks button alt wps-mfw-plan-secondary" href="<?php echo esc_url( $product_link ); ?>"><?php echo esc_html( $offer_no_thnks_txt ); ?></a>
							</div>
						</aside>
					</section>

					<section class="wps-mfw-plan-details">
						<div class="wps-mfw-plan-details__intro">
							<span class="wps-mfw-plan-eyebrow"><?php esc_html_e( 'Plan Details', 'membership-for-woocommerce' ); ?></span>
							<h3><?php esc_html_e( 'What you unlock', 'membership-for-woocommerce' ); ?></h3>
							<p><?php echo esc_html( $plan_summary ); ?></p>
						</div>
						<div class="wps-mfw-plan-info"><?php echo $plan_info_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					</section>
				</div>
			</div>
			<?php
		} else {

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
			?>
			<div class="wps-mfw-plan-page wps-mfw-plan-page--empty">
				<div class="wps-mfw-plan-shell">
					<section class="wps-mfw-plan-empty-state">
						<span class="wps-mfw-plan-eyebrow"><?php esc_html_e( 'Membership Offer', 'membership-for-woocommerce' ); ?></span>
						<h1><?php esc_html_e( 'Session expired', 'membership-for-woocommerce' ); ?></h1>
						<p><?php echo esc_html( $error_msg ); ?></p>
						<a href="<?php echo esc_url( $shop_page_url ); ?>" class="wps-mfw-plan-empty-state__link"><?php echo esc_html( $link_text ); ?></a>
					</section>
				</div>
			</div>
			<?php
		}

		$output = ob_get_clean();

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

				$price .= '<div class="wps_membership_plan_content_price">' . sprintf( ' %s %s ', esc_attr( $plan_currency ), esc_attr( $plan_price ) ) . '</div>';
			} else {

				$price .= '<div class="wps_membership_plan_content_price">' . esc_attr( $content ) . '</div>';
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

				$title .= '<span class="wps_membership_plan_content_title_for_page">' . ucwords( esc_html( $plan_title ) ) . '</span>';
			} else {

				$title .= '<span class="wps_membership_plan_content_title_for_page">' . esc_html( $content ) . '</span>';
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

				$title .= '<div class="wps_membership_plan_content_title">' . ucwords( esc_attr( $plan_title ) ) . '</div>';
			} else {

				$title .= '<div class="wps_membership_plan_content_title">' . esc_attr( $content ) . '</div>';
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

		$description = '';

		if ( ! function_exists( 'check_membership_pro_plugin_is_active' ) || ! check_membership_pro_plugin_is_active() ) {
			return $description;
		}

		$plugin_admin = new Membership_For_Woocommerce_Admin( '', '' );
		$plugin_admin->set_plan_creation_fields( $plan_id );
		$plan     = $plugin_admin->settings_fields;
		$instance = $plugin_admin->global_class;

		if ( empty( $plan ) || ! is_array( $plan ) ) {
			return $description;
		}

		$detail_items = array();

		$plan_type       = ! empty( $plan['wps_membership_plan_name_access_type'] ) ? $plan['wps_membership_plan_name_access_type'] : '';
		$plan_duration   = ! empty( $plan['wps_membership_plan_duration'] ) ? $plan['wps_membership_plan_duration'] : '';
		$duration_type   = ! empty( $plan['wps_membership_plan_duration_type'] ) ? $plan['wps_membership_plan_duration_type'] : '';
		$plan_start      = ! empty( $plan['wps_membership_plan_start'] ) ? $plan['wps_membership_plan_start'] : '';
		$plan_end        = ! empty( $plan['wps_membership_plan_end'] ) ? $plan['wps_membership_plan_end'] : '';
		$plan_access     = ! empty( $plan['wps_membership_plan_user_access'] ) ? $plan['wps_membership_plan_user_access'] : '';
		$access_type     = ! empty( $plan['wps_membership_plan_access_type'] ) ? $plan['wps_membership_plan_access_type'] : '';
		$delay_duration  = ! empty( $plan['wps_membership_plan_time_duration'] ) ? $plan['wps_membership_plan_time_duration'] : '';
		$delay_type      = ! empty( $plan['wps_membership_plan_time_duration_type'] ) ? $plan['wps_membership_plan_time_duration_type'] : '';
		$cart_discount   = ! empty( $plan['wps_memebership_plan_discount_price'] ) ? $plan['wps_memebership_plan_discount_price'] : '';
		$cart_price_type = ! empty( $plan['wps_membership_plan_offer_price_type'] ) ? $plan['wps_membership_plan_offer_price_type'] : '';
		$free_shipping   = ! empty( $plan['wps_memebership_plan_free_shipping'] ) ? $plan['wps_memebership_plan_free_shipping'] : '';
		$product_ids     = ! empty( $plan['wps_membership_plan_target_ids'] ) ? maybe_unserialize( $plan['wps_membership_plan_target_ids'] ) : array();
		$category_ids    = ! empty( $plan['wps_membership_plan_target_categories'] ) ? maybe_unserialize( $plan['wps_membership_plan_target_categories'] ) : array();
		$tag_ids         = ! empty( $plan['wps_membership_plan_target_tags'] ) ? maybe_unserialize( $plan['wps_membership_plan_target_tags'] ) : array();
		$club_ids        = ! empty( $plan['wps_membership_club'] ) ? $plan['wps_membership_club'] : array();
		$product_discount = ! empty( $plan['wps_memebership_product_discount_price'] ) ? $plan['wps_memebership_product_discount_price'] : '';
		$product_discount_type = ! empty( $plan['wps_membership_product_offer_price_type'] ) ? $plan['wps_membership_product_offer_price_type'] : '';
		$subscription_enabled = ! empty( $plan['wps_membership_subscription'] ) ? $plan['wps_membership_subscription'] : '';
		$subscription_duration = ! empty( $plan['wps_membership_subscription_expiry'] ) ? $plan['wps_membership_subscription_expiry'] : '';
		$subscription_duration_type = ! empty( $plan['wps_membership_subscription_expiry_type'] ) ? $plan['wps_membership_subscription_expiry_type'] : '';

		$plan_currency = '';
		if ( 'fixed' === $cart_price_type || 'fixed' === $product_discount_type ) {
			$plan_currency = get_woocommerce_currency_symbol();
			if ( function_exists( 'wps_mmcsfw_get_custom_currency_symbol' ) ) {
				$plan_currency = wps_mmcsfw_get_custom_currency_symbol( '' );
			}
		}

		if ( 'fixed' === $cart_price_type && function_exists( 'wps_mmcsfw_admin_fetch_currency_rates_from_base_currency' ) ) {
			$cart_discount = wps_mmcsfw_admin_fetch_currency_rates_from_base_currency( '', $cart_discount );
		}

		if ( 'fixed' === $product_discount_type && function_exists( 'wps_mmcsfw_admin_fetch_currency_rates_from_base_currency' ) ) {
			$product_discount = wps_mmcsfw_admin_fetch_currency_rates_from_base_currency( '', $product_discount );
		}

		if ( ! empty( $plan_type ) ) {
			$detail_items[] = array( 'label' => __( 'Plan Type', 'membership-for-woocommerce' ), 'value' => $plan_type );
		}

		if ( 'limited' === $plan_type && ! empty( $plan_duration ) ) {
			$detail_items[] = array( 'label' => __( 'Plan Duration', 'membership-for-woocommerce' ), 'value' => sprintf( '%s %s', esc_html( $plan_duration ), esc_html( 1 === intval( $plan_duration ) ? str_replace( 's', '', $duration_type ) : $duration_type ) ) );
		} elseif ( 'date_ranged' === $plan_type && ! empty( $plan_start ) && ! empty( $plan_end ) ) {
			$detail_items[] = array( 'label' => __( 'Duration', 'membership-for-woocommerce' ), 'value' => sprintf( '%s - %s', esc_html( $plan_start ), esc_html( $plan_end ) ) );
		}

		if ( 'yes' === $subscription_enabled && wps_membership_is_plugin_active( 'subscriptions-for-woocommerce/subscriptions-for-woocommerce.php' ) ) {
			$detail_items[] = array( 'label' => __( 'Subscription', 'membership-for-woocommerce' ), 'value' => sprintf( '%s %s', esc_html( $subscription_duration ), esc_html( $subscription_duration_type ) ) );
		}

		if ( ! empty( $plan_access ) ) {
			$detail_items[] = array( 'label' => __( 'Plan Access', 'membership-for-woocommerce' ), 'value' => $plan_access );
		}

		if ( ! empty( $access_type ) ) {
			$detail_items[] = array( 'label' => __( 'Access Type', 'membership-for-woocommerce' ), 'value' => $access_type );
		}

		if ( 'delay_type' === $access_type && ! empty( $delay_duration ) ) {
			$detail_items[] = array( 'label' => __( 'Delay Duration', 'membership-for-woocommerce' ), 'value' => sprintf( '%s %s', esc_html( $delay_duration ), esc_html( $delay_type ) ) );
		}

		if ( '' !== $cart_discount ) {
			$detail_items[] = array( 'label' => __( 'Cart Discount', 'membership-for-woocommerce' ), 'value' => sprintf( '%s%s %s', esc_html( $plan_currency ), esc_html( $cart_discount ), esc_html( $cart_price_type ) ) );
		}

		if ( '' !== $product_discount ) {
			$detail_items[] = array( 'label' => __( 'Product Discount', 'membership-for-woocommerce' ), 'value' => sprintf( '%s%s %s', esc_html( $plan_currency ), esc_html( $product_discount ), esc_html( $product_discount_type ) ) );
		}

		if ( ! empty( $club_ids ) && is_array( $club_ids ) ) {
			$club_titles = array();
			foreach ( $club_ids as $club_id ) {
				$club_titles[] = get_the_title( $club_id );
			}
			$detail_items[] = array( 'label' => __( 'Included Memberships', 'membership-for-woocommerce' ), 'value' => implode( ', ', array_filter( $club_titles ) ) );
		}

		if ( '' !== $free_shipping ) {
			$detail_items[] = array( 'label' => __( 'Free Shipping', 'membership-for-woocommerce' ), 'value' => $free_shipping );
		}

		if ( ! empty( $product_ids ) && is_array( $product_ids ) ) {
			$product_titles = array();
			foreach ( $product_ids as $product_id ) {
				$product_titles[] = $instance->get_product_title( $product_id );
			}
			$detail_items[] = array( 'label' => __( 'Included Products', 'membership-for-woocommerce' ), 'value' => implode( ', ', array_filter( $product_titles ) ) );
		}

		if ( ! empty( $category_ids ) && is_array( $category_ids ) ) {
			$category_titles = array();
			foreach ( $category_ids as $category_id ) {
				$category_titles[] = $instance->get_category_title( $category_id );
			}
			$detail_items[] = array( 'label' => __( 'Offered Categories', 'membership-for-woocommerce' ), 'value' => implode( ', ', array_filter( $category_titles ) ) );
		}

		if ( ! empty( $tag_ids ) && is_array( $tag_ids ) ) {
			$product_tags = array();
			foreach ( $tag_ids as $tag_id ) {
				$tag_term = get_term_by( 'id', $tag_id, 'product_tag' );
				if ( ! empty( $tag_term->name ) ) {
					$product_tags[] = $tag_term->name;
				}
			}
			$detail_items[] = array( 'label' => __( 'Product Tags', 'membership-for-woocommerce' ), 'value' => implode( ', ', array_filter( $product_tags ) ) );
		}

		if ( empty( $detail_items ) ) {
			return $description;
		}

		$description .= '<details class="wps-mfw-plan-card__details">';
		$description .= '<summary>' . esc_html__( 'Membership Details', 'membership-for-woocommerce' ) . '</summary>';
		$description .= '<div class="wps-mfw-plan-card__details-body">';
		$description .= '<ul class="wps-mfw-plan-card__specs">';

		foreach ( $detail_items as $detail_item ) {
			if ( empty( $detail_item['value'] ) ) {
				continue;
			}

			$description .= '<li>';
			$description .= '<span class="wps-mfw-plan-card__spec-label">' . esc_html( $detail_item['label'] ) . '</span>';
			$description .= '<span class="wps-mfw-plan-card__spec-value">' . wp_kses_post( $detail_item['value'] ) . '</span>';
			$description .= '</li>';
		}

		$description .= '</ul>';
		$description .= '</div>';
		$description .= '</details>';

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

				$description .= '<div class="wps_membership_plan_content_description">' . wp_kses_post( $plan_desc ) . '</div>';
				$description .= $this->get_plan_details( $plan_id );
			} else {
				$description .= '<div class="wps_membership_plan_content_desc">' . wp_kses_post( $content ) . '</div>';
				$description .= $this->get_plan_details( $plan_id );
			}

			$plan_info = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_info', true );
			if ( ! empty( $plan_info ) ) {

				$description .= '<div class="wps_membership_plan_info">' . wp_kses_post( $plan_info ) . '</div>';
			}
		} else {

			$data         = $this->custom_query_data;
			$mode         = $this->wps_membership_validate_mode();
			$plan_count   = ! empty( $data ) ? count( $data ) : 0;
			$button_label = esc_html__( 'Choose Plan', 'membership-for-woocommerce' );
			$home_link    = home_url( '/' );
			$shop_link    = wc_get_page_permalink( 'shop' );

			$description .= '<div class="wps-mfw-plan-directory">';
			$description .= '<section class="wps-mfw-plan-directory__hero">';
			$description .= '<div class="wps-mfw-plan-directory__copy">';
			$description .= '<span class="wps-mfw-plan-directory__eyebrow">' . esc_html__( 'Membership Plans', 'membership-for-woocommerce' ) . '</span>';
			$description .= '<h2>' . esc_html__( 'Choose the plan that fits your store strategy', 'membership-for-woocommerce' ) . '</h2>';
			$description .= '<p>' . esc_html__( 'Compare plan value, member perks, and access rules in one place. Each option is purchase-ready and built to route customers through the normal WooCommerce checkout flow.', 'membership-for-woocommerce' ) . '</p>';
			$description .= '<div class="wps-mfw-plan-directory__actions">';
			$description .= '<a class="wps-mfw-plan-directory__home-link" href="' . esc_url( $home_link ) . '">' . esc_html__( 'Back to Home', 'membership-for-woocommerce' ) . '</a>';
			$description .= '<a class="wps-mfw-plan-directory__home-link wps-mfw-plan-directory__home-link--secondary" href="' . esc_url( $shop_link ) . '">' . esc_html__( 'Go to Shop', 'membership-for-woocommerce' ) . '</a>';
			$description .= '</div>';
			$description .= '</div>';
			$description .= '<div class="wps-mfw-plan-directory__stats">';
			$description .= '<div class="wps-mfw-plan-directory__stat">';
			$description .= '<strong>' . esc_html( $plan_count ) . '</strong>';
			$description .= '<span>' . esc_html__( 'Active plans', 'membership-for-woocommerce' ) . '</span>';
			$description .= '</div>';
			$description .= '<div class="wps-mfw-plan-directory__note">' . esc_html__( 'Use the plan details panel on each card to review duration, discounts, access logic, and included benefits before checkout.', 'membership-for-woocommerce' ) . '</div>';
			$description .= '</div>';
			$description .= '</section>';

			if ( ! empty( $data ) ) {
				$description .= '<div class="wps-mfw-plan-grid">';

				foreach ( $data as $plan ) {
					$plan_title    = ! empty( $plan['post_title'] ) ? $plan['post_title'] : '';
					$plan_id       = ! empty( $plan['ID'] ) ? absint( $plan['ID'] ) : 0;
					$plan_desc     = get_post_field( 'post_content', $plan_id );
					$plan_info     = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_info', true );
					$plan_price    = floatval( wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_price', true ) );
					$plan_type     = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_name_access_type', true );
					$plan_access   = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_user_access', true );
					$plan_length   = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_duration', true );
					$length_type   = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_duration_type', true );
					$free_shipping = wps_membership_get_meta_data( $plan_id, 'wps_memebership_plan_free_shipping', true );
					$subscription  = wps_membership_get_meta_data( $plan_id, 'wps_membership_subscription', true );
					$discount      = wps_membership_get_meta_data( $plan_id, 'wps_memebership_plan_discount_price', true );
					$product_discount = wps_membership_get_meta_data( $plan_id, 'wps_memebership_product_discount_price', true );
					$product_ids   = maybe_unserialize( wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_target_ids', true ) );
					$category_ids  = maybe_unserialize( wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_target_categories', true ) );

					if ( function_exists( 'wps_mmcsfw_admin_fetch_currency_rates_from_base_currency' ) ) {
						$plan_price = wps_mmcsfw_admin_fetch_currency_rates_from_base_currency( '', $plan_price );
					}

					$plan_currency = get_woocommerce_currency_symbol();
					if ( function_exists( 'wps_mmcsfw_get_custom_currency_symbol' ) ) {
						$plan_currency = wps_mmcsfw_get_custom_currency_symbol( '' );
					}

					$summary_source = ! empty( $plan_desc ) ? $plan_desc : $plan_info;
					$plan_summary   = wp_trim_words( wp_strip_all_tags( $summary_source ), 26, '...' );
					if ( empty( $plan_summary ) ) {
						$plan_summary = esc_html__( 'Unlock member-only pricing, controlled access, and plan-based perks from a single purchase.', 'membership-for-woocommerce' );
					}

					$plan_badges = array();
					if ( 'yes' === $subscription ) {
						$plan_badges[] = esc_html__( 'Subscription ready', 'membership-for-woocommerce' );
					}
					if ( 'yes' === $free_shipping ) {
						$plan_badges[] = esc_html__( 'Free shipping', 'membership-for-woocommerce' );
					}
					if ( '' !== $discount || '' !== $product_discount ) {
						$plan_badges[] = esc_html__( 'Discount perks', 'membership-for-woocommerce' );
					}

					if ( 'limited' === $plan_type && ! empty( $plan_length ) ) {
						$plan_badges[] = sprintf(
							/* translators: 1: duration number, 2: duration unit. */
							esc_html__( '%1$s %2$s access', 'membership-for-woocommerce' ),
							esc_html( $plan_length ),
							esc_html( $length_type )
						);
					} elseif ( 'date_ranged' === $plan_type ) {
						$plan_badges[] = esc_html__( 'Date-ranged access', 'membership-for-woocommerce' );
					} elseif ( ! empty( $plan_type ) ) {
						$plan_badges[] = ucwords( str_replace( '_', ' ', $plan_type ) );
					}

					$plan_highlights = array();
					if ( ! empty( $plan_access ) ) {
						$plan_highlights[] = array(
							'label' => esc_html__( 'Access', 'membership-for-woocommerce' ),
							'value' => $plan_access,
						);
					}
					if ( ! empty( $product_ids ) && is_array( $product_ids ) ) {
						$plan_highlights[] = array(
							'label' => esc_html__( 'Products', 'membership-for-woocommerce' ),
							'value' => sprintf(
								/* translators: %d: number of products. */
								_n( '%d product', '%d products', count( $product_ids ), 'membership-for-woocommerce' ),
								count( $product_ids )
							),
						);
					}
					if ( ! empty( $category_ids ) && is_array( $category_ids ) ) {
						$plan_highlights[] = array(
							'label' => esc_html__( 'Categories', 'membership-for-woocommerce' ),
							'value' => sprintf(
								/* translators: %d: number of categories. */
								_n( '%d category', '%d categories', count( $category_ids ), 'membership-for-woocommerce' ),
								count( $category_ids )
							),
						);
					}

					$description .= '<article class="wps-mfw-plan-card">';
					$description .= '<div class="wps-mfw-plan-card__header">';
					$description .= '<span class="wps-mfw-plan-card__eyebrow">' . esc_html__( 'Membership', 'membership-for-woocommerce' ) . '</span>';
					$description .= '<h3>' . esc_html( ucwords( $plan_title ) ) . '</h3>';
					$description .= '<div class="wps-mfw-plan-card__price">' . esc_html( sprintf( '%s%s', $plan_currency, $plan_price ) ) . '</div>';
					$description .= '<p class="wps-mfw-plan-card__summary">' . esc_html( $plan_summary ) . '</p>';
					$description .= '</div>';

					if ( ! empty( $plan_badges ) ) {
						$description .= '<div class="wps-mfw-plan-card__badges">';
						foreach ( $plan_badges as $plan_badge ) {
							$description .= '<span>' . esc_html( $plan_badge ) . '</span>';
						}
						$description .= '</div>';
					}

					if ( ! empty( $plan_highlights ) ) {
						$description .= '<div class="wps-mfw-plan-card__facts">';
						foreach ( $plan_highlights as $plan_highlight ) {
							$description .= '<div class="wps-mfw-plan-card__fact">';
							$description .= '<span>' . esc_html( $plan_highlight['label'] ) . '</span>';
							$description .= '<strong>' . esc_html( $plan_highlight['value'] ) . '</strong>';
							$description .= '</div>';
						}
						$description .= '</div>';
					}

					if ( ! empty( $plan_info ) ) {
						$description .= '<div class="wps-mfw-plan-card__info">' . wp_kses_post( $plan_info ) . '</div>';
					}

					$description .= '<form method="post" class="wps_membership_buy_now_btn wps-mfw-plan-card__form">';
					$description .= '<input type="hidden" id="wps_membership_plan_id" name="plan_id" value="' . esc_attr( $plan_id ) . '">';
					$description .= '<input type="hidden" id="wps_membership_plan_price" value="' . esc_attr( $plan_price ) . '">';
					$description .= '<input type="hidden" name="membership_title" id="wps_membership_title" value="' . esc_attr( $plan_title ) . '">';
					$description .= '<input type="button" data-mode="' . esc_attr( $mode ) . '" class="wps_membership_buynow" name="wps_membership_buynow" value="' . esc_attr( $button_label ) . '">';
					$description .= '</form>';
					$description .= $this->get_plan_details( $plan_id );
					$description .= '</article>';
				}

				$description .= '</div>';
			} else {
				$description .= '<div class="wps-mfw-plan-directory__empty">';
				$description .= '<h3>' . esc_html__( 'Plans Not Available', 'membership-for-woocommerce' ) . '</h3>';
				$description .= '<p>' . esc_html__( 'No published membership plans were found. Create a plan in the plugin settings and it will appear here automatically.', 'membership-for-woocommerce' ) . '</p>';
				$description .= '</div>';
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

				$description .= '<div class="wps_membership_plan_content_desc">' . wp_kses_post( $plan_desc ) . '</div>';
			} else {
				$description .= '<div class="wps_membership_plan_content_desc">' . wp_kses_post( $content ) . '</div>';
			}

			$plan_info = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_info', true );
			if ( ! empty( $plan_info ) ) {

				$description .= '<div class="wps_membership_plan_info">' . wp_kses_post( $plan_info ) . '</div>';
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
		$mode    = $this->wps_membership_validate_mode();
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
								<input type="hidden" id="wps_membership_plan_id" name="plan_id" value="' . esc_attr( $plan_id ) . '">
								<input type="hidden" id="wps_membership_plan_price" value="' . esc_attr( $plan_price ) . '">
								<input type="hidden" name="membership_title" id="wps_membership_title" value="' . esc_attr( $plan_title ) . '">
								<input type="button" data-mode="' . esc_attr( $mode ) . '" class="wps_membership_buynow" name="wps_membership_buynow" value="' . esc_attr( $content ) . '">
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
							<input type="hidden" id="wps_membership_plan_id" name="plan_id" value="' . esc_attr( $plan_id ) . '">
							<input type="hidden" id="wps_membership_plan_price" value="' . esc_attr( $plan_price ) . '">
							<input type="hidden" name="membership_title" id="wps_membership_title" value="' . esc_attr( $plan_title ) . '">
							<input type="button" data-mode="' . esc_attr( $mode ) . '" class="wps_membership_buynow" name="wps_membership_buynow" value="' . esc_attr( $content ) . '">
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
		$mode             = $this->wps_membership_validate_mode();
		/**
		 * If shortcode attribute is set then get the plan_id from attribute else
		 * if on default page get the plan_id from query.
		 */

		$plan_id = ! empty( $atts['plan_id'] ) ? $atts['plan_id'] : '';
		if ( empty( $plan_id ) ) {

			$plan_id = isset( $_GET['plan_id'] ) ? sanitize_text_field( wp_unslash( $_GET['plan_id'] ) ) : '';
			$prod_id = isset( $_GET['prod_id'] ) ? sanitize_text_field( wp_unslash( $_GET['prod_id'] ) ) : '';
		}

		if ( empty( $plan_id ) && empty( $prod_id ) ) {
			return $no_thanks_button;
		}

		if ( empty( $content ) ) {
			/**
			 * Filter for no thanks button.
			 *
			 * @since 1.0.0
			 */
			$content = apply_filters( 'wps_mebership_no_thanks_btn_txt', esc_html__( 'No Thanks!', 'membership-for-woocommerce' ) );
		}

		$no_thanks_button .= '<a class="wps_membership_no_thanks button alt thickbox" data-mode="' . esc_attr( $mode ) . '" href="' . ( ! empty( $prod_id ) ? get_permalink( $prod_id ) : wc_get_page_permalink( 'shop' ) ) . '">' . esc_attr( $content ) . '</a>';
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

		$all_methods                    = array();
		$user                           = wp_get_current_user();
		$is_allowed_membership_shipping = false;
		$is_member_meta                 = get_user_meta( $user->ID, 'is_member' );
		if ( $this->global_class->plans_exist_check() == true ) {

			$user_id             = get_current_user_id();
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
				if ( ! empty( $rates ) && is_array( $rates ) ) {
					foreach ( $rates as $rate_key => $rate ) {
						// Excluding membership shipping methods.
						if ( 'wps_membership_shipping' === $rate->get_method_id() ) {

							unset( $rates[ $rate_key ] );
						}
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
	 *
	 * @return void
	 */
	public function wps_membership_get_states_public() {
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}
		// Nonce verify.
		check_ajax_referer( 'auth_adv_nonce', 'nonce' );

		$country_code  = ! empty( $_POST['country'] ) ? sanitize_text_field( wp_unslash( $_POST['country'] ) ) : '';
		$country_class = new WC_Countries();
		$states        = $country_class->__get( 'states' );
		$states        = ! empty( $states[ $country_code ] ) ? $states[ $country_code ] : array();
		$result        = '';
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

				// $product_disc_ids.
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

		$fields  = array();
		$order   = wc_get_order( $order_id );
		$plan_id = '';
		foreach ( $order->get_items() as $item_id => $item ) {

			$plan_id    = $item->get_meta( '_wps_plan_id' );
			$member_id  = $item->get_meta( '_member_id' );
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
				} elseif ( 'completed' == $order->get_status() ) {
						$order_st = 'complete';
				} elseif ( 'on-hold' == $order->get_status() || 'refunded' == $order->get_status() ) {
					$order_st = 'hold';
				} elseif ( 'pending' == $order->get_status() || 'processing' == $order->get_status() || 'failed' == $order->get_status() ) {
					$order_st = 'pending';
				} elseif ( 'cancelled' == $order->get_status() ) {
					$order_st = 'cancelled';
				}
				wps_membership_update_meta_data( $member_id, 'member_status', $order_st );

			} else {

				if ( ! empty( WC()->session ) && WC()->session->has_session() ) {

					$plan_id = WC()->session->get( 'plan_id' );
				}
				$this->wps_msfw_membership_update_meta_data( $order, $plan_id, $member_id, $fields, $order_id );
			}
			$this->wps_process_payment_callback( $member_id );
		} else {

			$plan_id             = wps_membership_get_meta_data( $product_id, 'wps_membership_plan_with_product', true );
			$is_plan_assigned    = false;
			$user                = get_user_by( 'email', $order->get_billing_email() );
			$is_member_meta      = get_user_meta( $user->ID, 'is_member' );
			$user_id             = $user->ID;
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

		$items = $order->get_data()['line_items'];
		$keys  = array_keys( $items );
		wc_add_order_item_meta( $keys[0], '_wps_plan_id', $plan_id );

		$billing_data                            = $order->get_data()['billing'];
		$order_data                              = $order->get_data();
		$order_status                            = $order_data['status'];
		$fields['membership_billing_first_name'] = $billing_data['first_name'];
		$fields['membership_billing_last_name']  = $billing_data['last_name'];
		$fields['membership_billing_company']    = $billing_data['company'];
		$fields['membership_billing_country']    = $billing_data['country'];
		$fields['membership_billing_address_1']  = $billing_data['address_1'];
		$fields['membership_billing_address_2']  = $billing_data['address_2'];
		$fields['membership_billing_city']       = $billing_data['city'];
		$fields['membership_billing_state']      = $billing_data['state'];
		$fields['membership_billing_postcode']   = $billing_data['postcode'];
		$fields['membership_billing_phone']      = $billing_data['phone'];
		$fields['membership_billing_email']      = $billing_data['email'];

		// If all goes well, a membership for customer will be created.
		$member_data = $this->global_class->create_membership_for_customer( $fields, $plan_id, $order_status );
		if ( ! empty( $member_data ) && is_array( $member_data ) ) {

			$user_id             = ! empty( $member_data['user_id'] ) ? $member_data['user_id'] : '';
			$mem__id             = ! empty( $member_data['member_id'] ) ? $member_data['member_id'] : '';
			$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );
			$current_memberships = ! empty( $current_memberships ) ? $current_memberships : array();
			array_push( $current_memberships, $mem__id );

			// Assign membership plan to user and assign 'member' role to it.
			update_user_meta( $user_id, 'mfw_membership_id', $current_memberships );

			$user = new WP_User( $user_id ); // create a new user object for this user.
			wc_add_order_item_meta( $keys[0], '_member_id', $mem__id );

			if ( ! $member_id ) {
				$member_id = $mem__id;
			}

			wps_membership_update_meta_data( $member_id, 'member_order_id', $order_id );
			$plan_obj = wps_membership_get_meta_data( $member_id, 'plan_obj', true );
			if ( 'yes' == $plan_obj['wps_membership_subscription'] ) {

				$available_plan = get_option( 'all_subscription_plan' );
				$available_plan = $available_plan . '-' . $member_id;
				update_option( 'all_subscription_plan', $available_plan );
			}
			$this->assign_club_membership_to_member( $plan_id, $plan_obj, $member_id );

			// sending membership creation mail.
			$expiry_date = wps_membership_get_meta_data( $member_id, 'member_expiry', true );
			if ( 'Lifetime' == $expiry_date ) {

				$expiry_date = 'Lifetime';
			} else {

				$expiry_date = esc_html( ! empty( $expiry_date ) ? gmdate( 'Y-m-d', $expiry_date ) : '' );
			}

			$user = get_userdata( $user_id );
			if ( ! empty( $user ) ) {

				$user_name = $user->data->display_name;
				if ( key_exists( 'membership_creation_email', WC()->mailer()->emails ) ) {

					$customer_email = WC()->mailer()->emails['membership_creation_email'];
					if ( ! empty( $customer_email ) ) {
						$email_status = $customer_email->trigger( $user_id, $plan_obj, $user_name, $expiry_date, $order_id );
					}
				}
			}
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

		// If manually completing membership then set its expiry date.
		$member_status = wps_membership_get_meta_data( $member_id, 'member_status', true );
		$member_status = ! empty( $member_status ) && is_array( $member_status ) ? $member_status : array();
		if ( isset( $member_status[0] ) && 'complete' == $member_status[0] ) {

			// Getting current activation date.
			$current_date = gmdate( 'Y-m-d' );
			$today_date   = gmdate( 'Y-m-d' );
			$plan_obj     = wps_membership_get_meta_data( $member_id, 'plan_obj', true );
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

			$user_id   = get_current_user_id();
			$user      = get_userdata( $user_id );
			$user_name = $user->data->display_name;
			$order_id  = wps_membership_get_meta_data( $member_id, 'member_order_id', true );
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
		$user_id    = get_current_user_id();
		$user_meta  = '';
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
		if ( empty( $product ) || ! is_object( $product ) ) {
			/**
			 * Filter to access member.
			 *
			 * @since 1.0.0
			 */
			return apply_filters( 'is_accessible_to_member', $access );
		}

		$product_id = absint( $product->get_id() );
		$exclude    = wps_membership_get_meta_data( $product_id, '_wps_membership_exclude', true );
		if ( 'yes' === $exclude ) {
			$access = true;
			return apply_filters( 'is_accessible_to_member', $access );
		}

		$this->under_review_products = array_values( array_diff( (array) $this->under_review_products, array( $product_id ) ) );
		$this->another_plan_products = array_values( array_diff( (array) $this->another_plan_products, array( $product_id ) ) );

		$matching_plan_ids = $this->get_matching_plan_ids_for_product( $product_id );
		if ( empty( $matching_plan_ids ) ) {
			return apply_filters( 'is_accessible_to_member', $access );
		}

		$member_plan_ids = $this->get_user_membership_plan_ids_by_status( get_current_user_id() );
		$owned_plan_ids  = ! empty( $member_plan_ids['owned'] ) ? $member_plan_ids['owned'] : array();
		$complete_ids    = ! empty( $member_plan_ids['complete'] ) ? $member_plan_ids['complete'] : array();
		$pending_ids     = ! empty( $member_plan_ids['pending'] ) ? $member_plan_ids['pending'] : array();

		$matching_owned_plans   = array_intersect( $matching_plan_ids, $owned_plan_ids );
		$matching_complete_plan = array_intersect( $matching_plan_ids, $complete_ids );
		$matching_pending_plan  = array_intersect( $matching_plan_ids, $pending_ids );

		if ( ! empty( $matching_complete_plan ) ) {
			$access = true;
		} elseif ( ! empty( $matching_pending_plan ) ) {
			$this->under_review_products[] = $product_id;
			$this->under_review_products   = array_values( array_unique( array_map( 'absint', $this->under_review_products ) ) );
			$access                        = true;
		} elseif ( ! empty( $matching_owned_plans ) ) {
			$access = true;
		} else {
			$this->another_plan_products[] = $product_id;
			$this->another_plan_products   = array_values( array_unique( array_map( 'absint', $this->another_plan_products ) ) );
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
		$cart_total                     = $cart->subtotal;
		$cart_tax                       = ! empty( $cart->tax_total ) ? $cart->tax_total : 0;
		$cart_total                     = (float) $cart_total - $cart_tax;
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

					$plan_existing   = true;
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
			$discount_percentage                    = floatval( $cart_total * ( $applied_offer_price_percentage_on_cart / 100 ) );
		}

		// If fixed discount is given.
		if ( ! empty( $applied_offer_price_fixed ) ) {
			// When fixed price is given.
			$applied_offer_price_fixed_on_cart = max( $applied_offer_price_fixed );
			$applied_offer_price_fixed_on_cart = ( 0 > $applied_offer_price_fixed_on_cart ) ? 0 : $applied_offer_price_fixed_on_cart;
			$discount_fixed                    = floatval( $applied_offer_price_fixed_on_cart );
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

			$mfw_mem_dis_name = esc_html__( 'Membership Discount', 'membership-for-woocommerce' );
			$cart->add_fee( $mfw_mem_dis_name, -$discount, false );
			update_option( 'wps_mfw_cart_discount', $discount );
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

			$user_id   = '';
			$user_name = '';
			foreach ( $delay_members as $member_id ) {

				$plan_obj     = wps_membership_get_meta_data( $member_id, 'plan_obj', true );
				$member_status = wps_membership_get_meta_data( $member_id, 'member_status', true );
				$order         = new WC_Order( wps_membership_get_meta_data( $member_id, 'member_order_id', true ) );
				$order_status = $order->status;
				if ( 'pending' == $member_status ) {

					// Save expiry date in post.
					if ( ! empty( $plan_obj ) ) {
						// Getting current activation date.

						$access_type = wps_membership_get_meta_data( $plan_obj['ID'], 'wps_membership_plan_access_type', true );
						if ( 'delay_type' == $access_type ) {

							$time_duration      = wps_membership_get_meta_data( $plan_obj['ID'], 'wps_membership_plan_time_duration', true );
							$time_duration_type = wps_membership_get_meta_data( $plan_obj['ID'], 'wps_membership_plan_time_duration_type', true );
							$delay_date         = wps_membership_get_meta_data( $member_id, 'membership_delay_date', true );
							$expiry_date        = wps_membership_get_meta_data( $member_id, 'member_expiry', true );
							// Getting current activation date.
							$current_date = gmdate( 'Y-m-d' );
							if ( $current_date >= $delay_date ) {
								if ( 'completed' == $order_status ) {

									wps_membership_update_meta_data( $member_id, 'member_status', 'complete' );
									$order_id = wps_membership_get_meta_data( $member_id, 'member_order_id', true );
									$plan     = wps_membership_get_meta_data( $member_id, 'plan_obj', true );
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
												wps_membership_update_meta_data( $subscription_i_d, 'wps_susbcription_end', 0 );
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

			$user_id   = '';
			$user_name = '';
			foreach ( $limited_members as $member_id ) {

				$member_status = wps_membership_get_meta_data( $member_id, 'member_status', true );
				$post          = get_post( $member_id );
				$user          = get_userdata( $post->post_author );
				$expiry_date   = wps_membership_get_meta_data( $member_id, 'member_expiry', true );
				$plan_obj      = wps_membership_get_meta_data( $member_id, 'plan_obj', true );
				$today_date    = gmdate( 'Y-m-d' );
				$current_date  = time();
				$order         = new WC_Order( wps_membership_get_meta_data( $member_id, 'member_order_id', true ) );
				$order_status  = $order->status;
				$order_id      = wps_membership_get_meta_data( $member_id, 'member_order_id', true );
				$expiry_mail   = gmdate( 'Y-m-d', strtotime( $expiry_date ) );
				$expiry        = wps_membership_get_meta_data( $member_id, 'member_expiry', true );
				if ( 'Lifetime' == $expiry ) {

					$expiry_mail = 'Lifetime';
				} else {

					$expiry_mail = esc_html( ! empty( $expiry ) ? gmdate( 'Y-m-d', $expiry ) : '' );
				}

				$number_of_day_to_send_expiry_mail = get_option( 'wps_membership_number_of_expiry_days' );
				$expiry_current                    = gmdate( 'Y-m-d', strtotime( $expiry_mail . '- ' . $number_of_day_to_send_expiry_mail . ' day' ) );
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

						$plan            = wps_membership_get_meta_data( $member_id, 'plan_obj', true );
						$is_subscription = $plan['wps_membership_subscription'];

						if ( 'yes' != $is_subscription ) {

							wps_membership_update_meta_data( $member_id, 'member_status', 'expired' );
						}

						$customer_email = '';
						if ( ! empty( WC()->mailer()->emails['membership_expired_email'] ) ) {
							$customer_email = WC()->mailer()->emails['membership_expired_email'];
						}

						$expiry_mail = gmdate( 'Y-m-d', strtotime( $expiry_date ) );
						$expiry      = wps_membership_get_meta_data( $member_id, 'member_expiry', true );
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
					$memberships         = get_user_meta( $author_id, 'mfw_membership_id', true );
					$memberships         = ! empty( $memberships ) && is_array( $memberships ) ? $memberships : array();
					array_push( $already_processed_users, $author_id );
					if ( ! empty( $memberships ) && is_array( $memberships ) ) {
						foreach ( $memberships as $key => $m_id ) {

							$status = wps_membership_get_meta_data( $m_id, 'member_status', true );
							if ( 'complete' == $status ) {

								$other_member_exists = true;
							}
						}
					}

					if ( 1 == count( $memberships ) ) {
						if ( false == $other_member_exists ) {

							update_user_meta( $author_id, 'is_member', '' );
						}
					} else {

						$remove_role = true;
						if ( ! empty( $memberships ) && is_array( $memberships ) ) {
							foreach ( $memberships as $key => $m_id ) {

								$status = wps_membership_get_meta_data( $m_id, 'member_status', true );
								if ( 'expired' != $status ) {

									$remove_role = false;
									break;
								}
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
	 * Undocumented function
	 *
	 * @return void
	 */
	public function wps_membership_checkout() {
		global $wp_session;
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}

		check_ajax_referer( 'auth_adv_nonce', 'nonce' );

		$plan_id                        = isset( $_POST['plan_id'] ) ? sanitize_text_field( wp_unslash( $_POST['plan_id'] ) ) : '';
		$plan_price                     = isset( $_POST['plan_price'] ) ? sanitize_text_field( wp_unslash( $_POST['plan_price'] ) ) : '';
		$plan_title                     = isset( $_POST['plan_title'] ) ? sanitize_text_field( wp_unslash( $_POST['plan_title'] ) ) : '';
		$wps_membership_default_product = get_option( 'wps_membership_default_product', '' );
		$wps_membership_default_product = absint( get_option( 'wps_membership_default_product', '' ) );
		// Ensure Woo session/cart are initialized in this custom AJAX request.
		if ( function_exists( 'wc_load_cart' ) && ( ! WC()->cart ) ) {
			wc_load_cart(); // also initializes WC()->session.
		}

		// Make sure a customer session cookie exists (critical for new users / first request).
		WC()->session->set_customer_session_cookie( true );

		// Persist plan selection in both legacy and WooCommerce session stores.
		if ( isset( $wp_session ) && is_array( $wp_session ) ) {
			$wp_session['plan_id']    = $plan_id;
			$wp_session['plan_title'] = $plan_title;
			$wp_session['plan_price'] = $plan_price;
		}

		WC()->session->set( 'plan_id', $plan_id );
		WC()->session->set( 'plan_title', $plan_title );
		WC()->session->set( 'plan_price', $plan_price );
		WC()->session->set( 'product_id', (int) $wps_membership_default_product );
		if ( is_object( WC()->session ) && method_exists( WC()->session, 'save_data' ) ) {
			WC()->session->save_data();
		}

		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_membership_product_price_to_cart_item_data' ), 10, 2 );
		// This flow relies on the next front-end request hitting the cart page, where
		// template_redirect either injects the hidden membership product or prints the
		// cart-conflict notice if other products are already present.
		$redirect_url = wc_get_cart_url();
		echo wp_json_encode( $redirect_url );
		wp_die();
	}

	/**
	 * Function of callback on form submission.
	 *
	 * @return void
	 */
	public function wps_mfw_registration_form_submission_callback() {
		global $wp_session;
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}

		if ( isset( $_POST['wps_regiser_form_submit'] ) ) {

			$value_check = isset( $_POST['wps_nonce_name'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_nonce_name'] ) ) : '';
			wp_verify_nonce( $value_check, 'wps-form-nonce' );

			$plan_id                        = isset( $_POST['wps_register_form_plan'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_plan'] ) ) : '';
			$plan_title                     = get_the_title( $plan_id );
			$plan_price                     = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_price', true );
			$wps_fname                      = isset( $_POST['wps_register_form_fname'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_fname'] ) ) : '';
			$wps_lname                      = isset( $_POST['wps_register_form_lname'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_lname'] ) ) : '';
			$wps_country                    = isset( $_POST['wps_register_form_country'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_country'] ) ) : '';
			$wps_address1                   = isset( $_POST['wps_register_form_address1'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_address1'] ) ) : '';
			$wps_city                       = isset( $_POST['wps_register_form_city'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_city'] ) ) : '';
			$wps_pincode                    = isset( $_POST['wps_register_form_pincode'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_pincode'] ) ) : '';
			$wps_phone                      = isset( $_POST['wps_register_form_phone_no'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_phone_no'] ) ) : '';
			$wps_email                      = isset( $_POST['wps_register_form_email'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_email'] ) ) : '';
			$wps_state                      = isset( $_POST['wps_register_form_state'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_register_form_state'] ) ) : '';
			$wps_membership_default_product = absint( get_option( 'wps_membership_default_product', '' ) );
			if ( null !== WC() && null !== WC()->session ) {
				WC()->session->set_customer_session_cookie( true );
			}
			if ( isset( $wp_session ) && is_array( $wp_session ) ) {
				$wp_session['plan_price'] = $plan_price;
				$wp_session['plan_title'] = $plan_title;
				$wp_session['plan_id']    = $plan_id;
			}
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
			if ( method_exists( WC()->session, 'save_data' ) ) {
				WC()->session->save_data();
			}
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
		global $wp_session;
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return $cart_item_data;
		}

		$session_plan_price = ( null !== WC() && null !== WC()->session ) ? WC()->session->get( 'plan_price' ) : '';
		$session_plan_title = ( null !== WC() && null !== WC()->session ) ? WC()->session->get( 'plan_title' ) : '';
		$session_plan_id    = ( null !== WC() && null !== WC()->session ) ? WC()->session->get( 'plan_id' ) : '';

		if ( empty( $session_plan_price ) && isset( $wp_session['plan_price'] ) ) {
			$session_plan_price = $wp_session['plan_price'];
		}
		if ( empty( $session_plan_title ) && isset( $wp_session['plan_title'] ) ) {
			$session_plan_title = $wp_session['plan_title'];
		}
		if ( empty( $session_plan_id ) && isset( $wp_session['plan_id'] ) ) {
			$session_plan_id = $wp_session['plan_id'];
		}

		if ( '' !== $session_plan_price && null !== $session_plan_price ) {
			$cart_item_data['plan_price'] = $session_plan_price;
		}
		if ( '' !== $session_plan_title && null !== $session_plan_title ) {
			$cart_item_data['plan_title'] = $session_plan_title;
		}
		if ( '' !== $session_plan_id && null !== $session_plan_id ) {
			$cart_item_data['plan_id'] = $session_plan_id; // In case of subscription.
		}

		if ( null !== WC() && null !== WC()->session && WC()->session->__isset( 'form_submit' ) ) {

			$cart_item_data['form_submit']  = 'yes';
			$cart_item_data['wps_fname']    = WC()->session->get( 'wps_fname' );
			$cart_item_data['wps_lname']    = WC()->session->get( 'wps_lname' );
			$cart_item_data['wps_country']  = WC()->session->get( 'wps_country' );
			$cart_item_data['wps_address1'] = WC()->session->get( 'wps_address1' );
			$cart_item_data['wps_city']     = WC()->session->get( 'wps_city' );
			$cart_item_data['wps_pincode']  = WC()->session->get( 'wps_pincode' );
			$cart_item_data['wps_phone']    = WC()->session->get( 'wps_phone' );
			$cart_item_data['wps_email']    = WC()->session->get( 'wps_email' );
			$cart_item_data['wps_state']    = WC()->session->get( 'wps_state' );
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

		// This is necessary for WC 3.0+.
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		// Avoiding hook repetition (when using price calculations for example | optional).
		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
			return;
		}

		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}

		$wps_membership_default_product = get_option( 'wps_membership_default_product', '' );
		$product                       = wc_get_product( $wps_membership_default_product );
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

							$wps_membership_plan_duration            = wps_membership_get_meta_data( $cart_contents_value['plan_id'], 'wps_membership_plan_duration', true );
							$wps_membership_plan_duration_type       = wps_membership_get_meta_data( $cart_contents_value['plan_id'], 'wps_membership_plan_duration_type', true );
							$wps_membership_subscription_expiry      = wps_membership_get_meta_data( $cart_contents_value['plan_id'], 'wps_membership_subscription_expiry', true );
							$wps_membership_subscription_expiry_type = wps_membership_get_meta_data( $cart_contents_value['plan_id'], 'wps_membership_subscription_expiry_type', true );

							wps_membership_update_meta_data( $wps_membership_default_product, '_wps_sfw_product', $wps_sfw_product );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_number', intval( $wps_membership_plan_duration ) );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_interval', substr( $wps_membership_plan_duration_type, 0, -1 ) );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_number', intval( $wps_membership_subscription_expiry ) );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_interval', $wps_membership_subscription_expiry_type );

							// get membership subscription settings values.
							$wps_mfw_enable_free_trial_settings        = wps_membership_get_meta_data( $cart_contents_value['plan_id'], 'wps_mfw_enable_free_trial_settings', true );
							$wps_sfw_subscription_initial_signup_price = floatval( wps_membership_get_meta_data( $cart_contents_value['plan_id'], 'wps_sfw_subscription_initial_signup_price', true ) );
							$wps_sfw_subscription_free_trial_number    = floatval( wps_membership_get_meta_data( $cart_contents_value['plan_id'], 'wps_sfw_subscription_free_trial_number', true ) );
							$wps_sfw_subscription_free_trial_interval  = wps_membership_get_meta_data( $cart_contents_value['plan_id'], 'wps_sfw_subscription_free_trial_interval', true );
							$plan_price                                = $cart_contents_value['plan_price'];
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_membership_plan_price', $plan_price );
							// calculating free trial membership.
							if ( 'yes' === $wps_mfw_enable_free_trial_settings && $wps_sfw_subscription_free_trial_number > 0 ) {

								$plan_price = $wps_sfw_subscription_initial_signup_price;
								wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_free_trial_number', intval( $wps_sfw_subscription_free_trial_number ) );
								wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_free_trial_interval', substr( $wps_sfw_subscription_free_trial_interval, 0, -1 ) );
							} else {

								wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_free_trial_number', '' );
								wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_free_trial_interval', '' );
							}
							// initial fee calculation.
							if ( $wps_sfw_subscription_initial_signup_price > 0 ) {

								$plan_price = $plan_price + $wps_sfw_subscription_initial_signup_price;
								wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_initial_signup_price', intval( $wps_sfw_subscription_initial_signup_price ) );
							} else {

								$plan_price = $wps_sfw_subscription_initial_signup_price;
								wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_initial_signup_price', '' );
							}
							wps_membership_update_meta_data( $wps_membership_default_product, '_regular_price', intval( $plan_price ) );
						} else {

							wps_membership_update_meta_data( $wps_membership_default_product, '_wps_sfw_product', 'no' );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_number', '' );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_interval', '' );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_number', '' );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_interval', '' );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_free_trial_number', '' );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_free_trial_interval', '' );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_initial_signup_price', '' );
						}
					} else {

						wps_membership_update_meta_data( $wps_membership_default_product, '_wps_sfw_product', 'no' );
						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_number', '' );
						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_interval', '' );
						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_number', '' );
						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_interval', '' );
						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_free_trial_number', '' );
						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_free_trial_interval', '' );
						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_initial_signup_price', '' );
					}
				} else if ( $wps_attached_plan_id ) {

					$wps_membership_default_product = $cart_contents_value['product_id'];
					$wps_sfw_product                = wps_membership_get_meta_data( $wps_attached_plan_id, 'wps_membership_subscription', true );
					if ( ! empty( $wps_sfw_product ) && 'yes' == $wps_sfw_product ) {

						$wps_membership_plan_name_access_type = wps_membership_get_meta_data( $wps_attached_plan_id, 'wps_membership_plan_name_access_type', true );
						if ( 'limited' == $wps_membership_plan_name_access_type ) {

							$wps_membership_plan_duration            = wps_membership_get_meta_data( $wps_attached_plan_id, 'wps_membership_plan_duration', true );
							$wps_membership_plan_duration_type       = wps_membership_get_meta_data( $wps_attached_plan_id, 'wps_membership_plan_duration_type', true );
							$wps_membership_subscription_expiry      = wps_membership_get_meta_data( $wps_attached_plan_id, 'wps_membership_subscription_expiry', true );
							$wps_membership_subscription_expiry_type = wps_membership_get_meta_data( $wps_attached_plan_id, 'wps_membership_subscription_expiry_type', true );

							wps_membership_update_meta_data( $wps_membership_default_product, '_wps_sfw_product', $wps_sfw_product );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_number', intval( $wps_membership_plan_duration ) );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_interval', substr( $wps_membership_plan_duration_type, 0, -1 ) );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_number', intval( $wps_membership_subscription_expiry ) );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_interval', $wps_membership_subscription_expiry_type );

							// get membership subscription settings values.
							$wps_mfw_enable_free_trial_settings        = wps_membership_get_meta_data( $cart_contents_value['plan_id'], 'wps_mfw_enable_free_trial_settings', true );
							$wps_sfw_subscription_initial_signup_price = floatval( wps_membership_get_meta_data( $cart_contents_value['plan_id'], 'wps_sfw_subscription_initial_signup_price', true ) );
							$wps_sfw_subscription_free_trial_number    = floatval( wps_membership_get_meta_data( $cart_contents_value['plan_id'], 'wps_sfw_subscription_free_trial_number', true ) );
							$wps_sfw_subscription_free_trial_interval  = wps_membership_get_meta_data( $cart_contents_value['plan_id'], 'wps_sfw_subscription_free_trial_interval', true );
							$plan_price                                = $cart_contents_value['plan_price'];
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_membership_plan_price', $plan_price );
							// calculating free trial membership.
							if ( 'yes' === $wps_mfw_enable_free_trial_settings && $wps_sfw_subscription_free_trial_number > 0 ) {

								$plan_price = $wps_sfw_subscription_initial_signup_price;
								wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_free_trial_number', intval( $wps_sfw_subscription_free_trial_number ) );
								wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_free_trial_interval', substr( $wps_sfw_subscription_free_trial_interval, 0, -1 ) );
							} else {

								wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_free_trial_number', '' );
								wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_free_trial_interval', '' );
							}
							// initial fee calculation.
							if ( $wps_sfw_subscription_initial_signup_price > 0 ) {

								$plan_price = $plan_price + $wps_sfw_subscription_initial_signup_price;
								wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_initial_signup_price', intval( $wps_sfw_subscription_initial_signup_price ) );
							} else {

								$plan_price = $wps_sfw_subscription_initial_signup_price;
								wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_initial_signup_price', '' );
							}
							wps_membership_update_meta_data( $wps_membership_default_product, '_regular_price', intval( $plan_price ) );
						} else {

							wps_membership_update_meta_data( $wps_membership_default_product, '_wps_sfw_product', 'no' );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_number', '' );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_interval', '' );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_number', '' );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_interval', '' );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_free_trial_number', '' );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_free_trial_interval', '' );
							wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_initial_signup_price', '' );
						}
					} else {

						wps_membership_update_meta_data( $wps_membership_default_product, '_wps_sfw_product', 'no' );
						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_number', '' );
						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_interval', '' );
						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_number', '' );
						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_expiry_interval', '' );
						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_free_trial_number', '' );
						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_free_trial_interval', '' );
						wps_membership_update_meta_data( $wps_membership_default_product, 'wps_sfw_subscription_initial_signup_price', '' );
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

		$active_plan_id = absint( $active_plan_id );
		if ( empty( $active_plan_id ) ) {
			return array();
		}

		$included_membership = array();
		$visited_plan_ids    = array( $active_plan_id );
		$pending_plan_ids    = array( $active_plan_id );

		while ( ! empty( $pending_plan_ids ) ) {
			$current_plan_id = absint( array_shift( $pending_plan_ids ) );
			$club_membership = wps_membership_get_meta_data( $current_plan_id, 'wps_membership_club', true );
			$club_membership = ! empty( $club_membership ) && is_array( $club_membership ) ? array_map( 'absint', $club_membership ) : array();

			foreach ( $club_membership as $club_membership_value ) {
				$club_membership_value = absint( $club_membership_value );
				if ( empty( $club_membership_value ) || in_array( $club_membership_value, $visited_plan_ids, true ) ) {
					continue;
				}

				$visited_plan_ids[]    = $club_membership_value;
				$included_membership[] = $club_membership_value;
				$pending_plan_ids[]    = $club_membership_value;
			}
		}

		return array_values( array_unique( array_filter( array_map( 'absint', $included_membership ) ) ) );
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
		$membership_product             = wc_get_product( $wps_membership_default_product );
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

		$matching_plan_ids = $this->get_matching_plan_ids_for_product( $product->get_id() );
		if ( empty( $matching_plan_ids ) ) {
			return $is_purchasable;
		}

		if ( ! is_user_logged_in() ) {
			return $is_purchasable;
		}

		$member_plan_ids = $this->get_user_membership_plan_ids_by_status( get_current_user_id() );
		$complete_ids    = ! empty( $member_plan_ids['complete'] ) ? $member_plan_ids['complete'] : array();
		$pending_ids     = ! empty( $member_plan_ids['pending'] ) ? $member_plan_ids['pending'] : array();

		if ( ! empty( array_intersect( $matching_plan_ids, $complete_ids ) ) ) {
			return true;
		}

		if ( ! empty( array_intersect( $matching_plan_ids, $pending_ids ) ) ) {
			return false;
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
				'post_type'   => 'product',
				'numberposts' => -1,
				'post_status' => 'publish',
				'fields'      => 'ids',
				'tax_query'   => array(
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
			$product_id = absint( WC()->session->get( 'product_id' ) );

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

					$cart_item_data = $this->add_membership_product_price_to_cart_item_data( array(), $product_id );
					WC()->cart->empty_cart();
					$added_to_cart = WC()->cart->add_to_cart( $product_id, 1, 0, array(), $cart_item_data );
					if ( $added_to_cart ) {
						$redirect_url = WC()->session->__isset( 'form_submit' ) ? wc_get_checkout_url() : wc_get_cart_url();
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
						WC()->cart->calculate_totals();
						wp_safe_redirect( $redirect_url );
						exit;
					}
				}
			} else {

				$cart_item_data = $this->add_membership_product_price_to_cart_item_data( array(), $product_id );
				WC()->cart->empty_cart();
				$added_to_cart = WC()->cart->add_to_cart( $product_id, 1, 0, array(), $cart_item_data );
				if ( $added_to_cart ) {
					$redirect_url = WC()->session->__isset( 'form_submit' ) ? wc_get_checkout_url() : wc_get_cart_url();
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
					WC()->cart->calculate_totals();
					wp_safe_redirect( $redirect_url );
					exit;
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
			if ( null != WC()->cart && 1 < WC()->cart->get_cart_contents_count() ) {

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
		$only_virtual       = false;
		if ( null != WC() && null != WC()->cart ) {
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

				$_product = $cart_item['data'];
				if ( $_product->get_id() == $default_product_id ) {
					if ( array_key_exists( 'form_submit', $cart_item ) && 'yes' == $cart_item['form_submit'] ) {

						$only_virtual = true;
					}
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

		if ( ! empty( $pages_available ) && is_array( $pages_available ) ) {
			foreach ( $pages_available as $single_page ) {
				if ( is_page( $single_page->ID ) ) {

					$page_template = plugin_dir_path( __FILE__ ) . '/partials/templates/membership-templates/wps-membership-template.php';
				}
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
		$user           = wp_get_current_user();
		$is_member_meta = get_user_meta( $user->ID, 'is_member' );
		if ( is_user_logged_in() || in_array( 'member', (array) $is_member_meta ) ) {

			$data                  = $this->custom_query_data;
			$user_id               = get_current_user_id();
			$existing_plan_id      = array();
			$existing_plan_product = array();
			$page_link             = '';
			$current_memberships   = get_user_meta( $user_id, 'mfw_membership_id', true );
			if ( ! empty( $current_memberships ) && is_array( $current_memberships ) ) {
				foreach ( $current_memberships as $key => $membership_id ) {

					$member_status = wps_membership_get_meta_data( $membership_id, 'member_status', true );
					if ( ! empty( $member_status ) && 'complete' == $member_status ) {

						$active_plan     = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
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
						$product_terms   = $this->get_product_terms( get_the_ID() );
						if ( ! empty( $product_terms ) && is_array( $product_terms ) ) {
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

			if ( ! empty( $data ) && is_array( $data ) ) {
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
						$product_terms   = $this->get_product_terms( get_the_ID() );
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
		$user           = wp_get_current_user();
		$is_member_meta = get_user_meta( $user->ID, 'is_member' );
		$is_member_meta = ! empty( $is_member_meta ) && is_array( $is_member_meta ) ? $is_member_meta : array();
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
			return $subscription_status;
		}

		$member_id        = '';
		$subscription     = get_post( $subscription_i_d );
		$parent_order_id  = $subscription->wps_parent_order;
		$order            = wc_get_order( $parent_order_id );
		if ( ! empty( $order ) ) {
			foreach ( $order->get_items() as $item_id => $order_item ) {

				if ( ! empty( $order_item->get_meta( '_member_id' ) ) ) {

					$member_id = $order_item->get_meta( '_member_id' );
				}
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
			return $wps_next_payment_date;
		}

		$order_id  = wps_membership_get_meta_data( $subscription_i_d, 'wps_parent_order', true );
		$order     = wc_get_order( $order_id );
		$member_id = get_member_id_from_order( $order );
		if ( ! empty( $member_id ) ) {

			$expiry_date = wps_membership_get_meta_data( $member_id, 'member_expiry', true );
			$plan        = wps_membership_get_meta_data( $member_id, 'plan_obj', true );
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
			return $wps_susbcription_end;
		}

		$order_id  = wps_membership_get_meta_data( $subscription_i_d, 'wps_parent_order', true );
		$order     = wc_get_order( $order_id );
		$member_id = get_member_id_from_order( $order );
		if ( ! empty( $member_id ) ) {

			$expiry_date = wps_membership_get_meta_data( $member_id, 'member_expiry', true );
			$plan        = wps_membership_get_meta_data( $member_id, 'plan_obj', true );
			if ( 'yes' == $plan['wps_membership_subscription'] ) {
				if ( ! empty( $plan['wps_membership_subscription_expiry'] ) ) {
					if ( function_exists( 'wps_sfw_susbcription_expiry_date' ) ) {

						$access_type = wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_access_type', true );
						// $current_date = gmdate( 'Y-m-d' );
						$current_time = current_time( 'timestamp' );
						if ( 'delay_type' == $access_type ) {

							$time_duration      = wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_time_duration', true );
							$time_duration_type = wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_time_duration_type', true );
							$current_time = strtotime( gmdate( 'Y-m-d', strtotime( $current_time . ' + ' . $time_duration . ' ' . $time_duration_type ) ) );
						}
						$wps_susbcription_end = wps_sfw_susbcription_expiry_date( $subscription_i_d, $current_time );
						wps_membership_update_meta_data( $subscription_i_d, 'wps_susbcription_end', $wps_susbcription_end );
					}
				} else {
					wps_membership_update_meta_data( $subscription_i_d, 'wps_susbcription_end', 0 );
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
		global $woocommerce;
		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {
			return;
		}

		$membership_name                = '';
		$wps_user                       = get_user_by( 'email', $fields['billing_email'] );
		$is_not_membership_applicable   = false;
		$is_membership_product          = false;
		$wps_membership_default_product = get_option( 'wps_membership_default_product', '' );
		if ( null != WC() && null != WC()->cart ) {
			foreach ( WC()->cart->get_cart() as $cart_item ) {

				$product = $cart_item['data'];
				if ( ! empty( $product ) ) {

					if ( $product->get_id() == $wps_membership_default_product ) {
						$membership_name = $product->get_title();
						$is_membership_product = true;
					}
				}
			}
		}

		$is_member_meta = get_user_meta( $wps_user->ID, 'is_member' );
		$is_member_meta = ! empty( $is_member_meta ) && is_array( $is_member_meta ) ? $is_member_meta : array();
		if ( is_user_logged_in() || in_array( 'member', (array) $is_member_meta ) ) {

			$data                = $this->custom_query_data;
			$user_id             = $wps_user->ID;
			$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );
			if ( ! empty( $current_memberships ) && is_array( $current_memberships ) ) {
				foreach ( $current_memberships as $key => $membership_id ) {

					$member_status = wps_membership_get_meta_data( $membership_id, 'member_status', true );
					if ( ! empty( $member_status ) && ( 'complete' == $member_status || 'pending' == $member_status || 'hold' == $member_status ) ) {

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

		if ( ! empty( $order_id ) ) {

			$order = new WC_Order( $order_id );
			$items = $order->get_items();
			foreach ( $items as $item ) {

				$product_id       = $item['product_id'];
				$product          = wc_get_product( $product_id );
				$plan_id          = wps_membership_get_meta_data( $product_id, 'wps_membership_plan_with_product', true );
				$is_plan_assigned = false;
				$user             = get_user_by( 'email', $order->get_billing_email() );
				if ( $plan_id ) {

					$is_plan_assigned = false;
				}

				if ( ! empty( $user ) ) {

					$user_id             = $user->ID;
					$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );
					if ( ! empty( $current_memberships ) && is_array( $current_memberships ) && $plan_id ) {
						foreach ( $current_memberships as $key => $membership_id ) {

							$active_plan = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
							$status      = wps_membership_get_meta_data( $membership_id, 'member_status', true );
							if ( ! empty( $active_plan['ID'] ) ) {
								if ( $plan_id == $active_plan['ID'] && 'cancelled' != $status && ! empty( $status ) ) {

									$is_plan_assigned = true;
									break;

								}
							}
						}
					}
				}

				if ( ( null != $product && 'Membership Product' == $product->get_title() ) || ! $is_plan_assigned ) {
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

		$price               = '';
		$is_plan_assigned    = false;
		$post_id             = get_the_ID();
		$user                = wp_get_current_user();
		$is_member_meta      = get_user_meta( $user->ID, 'is_member' );
		$is_member_meta      = ! empty( $is_member_meta ) && is_array( $is_member_meta ) ? $is_member_meta : array();
		$user_id             = get_current_user_id();
		$current_memberships = get_user_meta( $user_id, 'mfw_membership_id', true );
		$plan_id             = wps_membership_get_meta_data( $post_id, 'wps_membership_plan_with_product', true );
		$matching_plan_titles = $this->get_matching_plan_titles_for_product( $post_id );
		$matching_plan_output = implode( ' | ', $matching_plan_titles );
		if ( $plan_id ) {
			$is_plan_assigned = false;
		}

		if ( is_user_logged_in() ) {
			if ( in_array( 'member', (array) $is_member_meta ) ) {

				if ( ! empty( $current_memberships ) && is_array( $current_memberships ) && $plan_id ) {
					foreach ( $current_memberships as $key => $membership_id ) {

						$active_plan = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
						$status      = wps_membership_get_meta_data( $membership_id, 'member_status', true );
						if ( ! empty( $active_plan['ID'] ) ) {
							if ( $plan_id == $active_plan['ID'] && 'complete' == $status ) {

								$is_plan_assigned = true;
							}
						}
					}

					if ( ! $is_plan_assigned ) {
						if ( ! is_single() ) {

							$result = get_post( $plan_id );
							?>
							<div class="mfw-product-meta-membership-wrap mfw-product-meta-membership-wrap--assigned">
								<div class="product-meta mfw-product-meta-membership">
									<span class="mfw-product-meta-membership__eyebrow"><?php esc_html_e( 'Membership Plan', 'membership-for-woocommerce' ); ?></span>
									<strong class="mfw-product-meta-membership__title"><?php esc_html_e( 'Includes plan access', 'membership-for-woocommerce' ); ?></strong>
									<span class="mfw-product-meta-membership__plans"><?php echo esc_html( ! empty( $matching_plan_output ) ? $matching_plan_output : $result->post_title ); ?></span>
								</div>
							</div>
							<?php
						} else {

							$result = get_post( $plan_id );
							?>
							<div class="wps-info-membership-alert">
								<p >
									<?php esc_html_e( 'Buy this product and become a member of ', 'membership-for-woocommerce' ); ?>
									<?php echo esc_html( ! empty( $matching_plan_output ) ? $matching_plan_output : $result->post_title ); ?>
									<?php esc_html_e( ' membership plan. ', 'membership-for-woocommerce' ); ?>
	
								</p>
							</div>
							<?php
						}
					}
				}
			} elseif ( $plan_id ) {

				if ( ! is_single() ) {

					$result = get_post( $plan_id );
					?>
						<div class="mfw-product-meta-membership-wrap">
							<div class="product-meta mfw-product-meta-membership">
								<span class="mfw-product-meta-membership__eyebrow"><?php esc_html_e( 'Membership Plan', 'membership-for-woocommerce' ); ?></span>
								<strong class="mfw-product-meta-membership__title"><?php esc_html_e( 'Includes plan access', 'membership-for-woocommerce' ); ?></strong>
								<span class="mfw-product-meta-membership__plans"><?php echo esc_html( ! empty( $matching_plan_output ) ? $matching_plan_output : $result->post_title ); ?></span>
							</div>
						</div>
						<?php
				} else {

					$result = get_post( $plan_id );
					?>
						<div class="wps-info-membership-alert">
							<p >
							<?php esc_html_e( 'Buy this product and become a member of ', 'membership-for-woocommerce' ); ?>
							<?php echo esc_html( ! empty( $matching_plan_output ) ? $matching_plan_output : $result->post_title ); ?>
							<?php esc_html_e( ' membership plan. ', 'membership-for-woocommerce' ); ?>
							</p>
						</div>
						<?php
				}
			}
		} elseif ( $plan_id ) {
			if ( ! is_single() ) {

				$result   = get_post( $plan_id );
				?>
					<div class="mfw-product-meta-membership-wrap">
						<div class="product-meta mfw-product-meta-membership">
							<span class="mfw-product-meta-membership__eyebrow"><?php esc_html_e( 'Membership Plan', 'membership-for-woocommerce' ); ?></span>
							<strong class="mfw-product-meta-membership__title"><?php esc_html_e( 'Includes plan access', 'membership-for-woocommerce' ); ?></strong>
							<span class="mfw-product-meta-membership__plans"><?php echo esc_html( ! empty( $matching_plan_output ) ? $matching_plan_output : $result->post_title ); ?></span>
						</div>
					</div>
					<?php
			} else {
				$result   = get_post( $plan_id );
				?>
					<div class="wps-info-membership-alert">
						<p >
						<?php esc_html_e( 'Buy this product and become a member of ', 'membership-for-woocommerce' ); ?>
						<?php echo esc_html( ! empty( $matching_plan_output ) ? $matching_plan_output : $result->post_title ); ?>
						<?php esc_html_e( ' membership plan. ', 'membership-for-woocommerce' ); ?>

						</p>
					</div>
					<?php
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
				'post_type'   => 'wps_cpt_membership',
				'post_status' => 'publish',
				'numberposts' => -1,

			)
		);

		$wps_membership_plans_page_id = get_option( 'wps_membership_default_plans_page', true );
		$page_url                     = get_permalink( $wps_membership_plans_page_id );
		$output                       = '';
		$output                      .= '<form method="POST">';
		$output                      .= '<div class="div_wrapper wps-register-form-wrapper">';
		$output                      .= '<div><label for="wps_register_form_plan"> ' . esc_html__( 'Select Plan', 'membership-for-woocommerce' ) . ' </lable><select required id="wps_register_form_plan" name="wps_register_form_plan"><option value="">' . __( 'Choose Plan', 'membership-for-woocommerce' ) . '</option>';
		if ( ! empty( $wps_plan ) && is_array( $wps_plan ) ) {
			foreach ( $wps_plan as $key => $value ) {

				$output .= '<option value="' . esc_attr( $value->ID ) . '">' . esc_html( $value->post_title ) . '</option>';
			}
		}

		$output .= '</select><a style="margin-left:10px;" href="' . esc_url( $page_url ) . '" target="_blank">' . esc_html__( ' Click here for all plans details ', 'membership-for-woocommerce' ) . '</a></div>';
		$output .= '<div><label for="wps_register_form_fname">' . esc_html__( 'First Name', 'membership-for-woocommerce' ) . '</label><input type="text" id="wps_register_form_fname" name="wps_register_form_fname" required placeholder="' . esc_html__( 'First Name', 'membership-for-woocommerce' ) . '"></div>';
		$output .= '<div><label for="wps_register_form_lname">' . esc_html__( 'Last Name', 'membership-for-woocommerce' ) . '</label><input type="text" id="wps_register_form_lname" name="wps_register_form_lname" required placeholder="' . esc_html__( 'Last Name', 'membership-for-woocommerce' ) . '"></div>';
		$output .= '<div><label for="wps_register_form_country">' . esc_html__( 'Country', 'membership-for-woocommerce' ) . '</label><input type="text" id="wps_register_form_country" name="wps_register_form_country" required placeholder="' . esc_html__( 'Country', 'membership-for-woocommerce' ) . '"></div>';
		$output .= '<div><label for="wps_register_form_address1">' . esc_html__( 'Street ', 'membership-for-woocommerce' ) . '</label><input type="text" id="wps_register_form_address1" name="wps_register_form_address1" required placeholder="' . esc_html__( 'Street Address', 'membership-for-woocommerce' ) . '"></div>';
		$output .= '<div><label for="wps_register_form_city">' . esc_html__( 'City ', 'membership-for-woocommerce' ) . '</label><input type="text" id="wps_register_form_city" name="wps_register_form_city" required placeholder="' . esc_html__( 'City ', 'membership-for-woocommerce' ) . '"></div>';
		$output .= '<div><label for="wps_register_form_state">' . esc_html__( 'State ', 'membership-for-woocommerce' ) . '</label><input type="text" id="wps_register_form_state" name="wps_register_form_state" required placeholder="' . esc_html__( 'State ', 'membership-for-woocommerce' ) . '"></div>';
		$output .= '<div><label for="wps_register_form_pincode">' . esc_html__( 'Pin Code ', 'membership-for-woocommerce' ) . '</label><input type="number" id="wps_register_form_pincode" name="wps_register_form_pincode" required placeholder="' . esc_html__( 'Pin Code', 'membership-for-woocommerce' ) . '"></div>';
		$output .= '<div><label for="wps_register_form_phone_no">' . esc_html__( 'Phone No ', 'membership-for-woocommerce' ) . '</label><input type="number" id="wps_register_form_phone_no" name="wps_register_form_phone_no" required placeholder="' . esc_html__( 'Phone Number', 'membership-for-woocommerce' ) . '"></div>';
		$output .= '<div><label for="wps_register_form_email">' . esc_html__( 'Email Address ', 'membership-for-woocommerce' ) . '</label><input type="email" id="wps_register_form_email" name="wps_register_form_email" required placeholder="' . esc_html__( 'Email Address ', 'membership-for-woocommerce' ) . '"></div>';
		$output .= '<div></form>';
		$nonce = wp_create_nonce( 'wps-form-nonce' );
		$output .= '<input type="hidden" name="wps_nonce_name" value="' . esc_attr( $nonce ) . '" />';
		$output .= '<div><input type="submit" id="wps_regiser_form_submit" class="button" name="wps_regiser_form_submit" value="' . esc_html__( 'Register', 'membership-for-woocommerce' ) . '">';
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
			return $redirection_url;
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

			$product_id         = ! empty( $product ) && is_object( $product ) ? $product->get_id() : get_the_ID();
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
						$target_ids     = ! empty( $target_ids ) && is_array( $target_ids ) ? $target_ids : array();
						$target_cat_ids = wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_categories', true );
						$target_cat_ids = ! empty( $target_cat_ids ) && is_array( $target_cat_ids ) ? $target_cat_ids : array();
						$target_tag_ids = wps_membership_get_meta_data( $plan['ID'], 'wps_membership_plan_target_tags', true );
						$target_tag_ids = ! empty( $target_tag_ids ) && is_array( $target_tag_ids ) ? $target_tag_ids : array();
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

	/**
	 * This function is used to calculate total discount benefits.
	 *
	 * @param  object $order_data $order_data.
	 * @return void
	 */
	public function wps_mfw_calculate_total_discount_benefits( $order_data ) {

		// This function is triggered by two hooks, so we need to verify whether the parameter is an ID or an object.
		if ( ! is_object( $order_data ) ) {

			$order = wc_get_order( $order_data );
		} else {

			$order = $order_data;
		}

		$user_id               = $order->get_user_id();
		$wps_mfw_cart_discount = get_option( 'wps_mfw_cart_discount', true );
		$wps_mfw_cart_discount = ! empty( $wps_mfw_cart_discount ) ? $wps_mfw_cart_discount : 0;
		if ( ! empty( $user_id ) && $wps_mfw_cart_discount > 1 ) {

			$wps_mfw_total_discount_amount = get_user_meta( $user_id, 'wps_mfw_total_discount_amount', true );
			$wps_mfw_total_discount_amount = ! empty( $wps_mfw_total_discount_amount ) ? $wps_mfw_total_discount_amount : 0;
			$updated_amount                = $wps_mfw_cart_discount + $wps_mfw_total_discount_amount;
			update_user_meta( $user_id, 'wps_mfw_total_discount_amount', $updated_amount );
			delete_option( 'wps_mfw_cart_discount' );
		}
	}

	/**
	 * This function is used to stop whatsapp notification.
	 *
	 * @param  string $user_id user id.
	 * @return void
	 */
	public function wps_mfw_unsubscribe_whatsapp_notification( $user_id ) {

		$wps_mfw_stop_whatsapp = get_user_meta( $user_id, 'wps_mfw_stop_whatsapp', true );
		$wps_mfw_stop_sms      = get_user_meta( $user_id, 'wps_mfw_stop_sms', true );
		$wps_mfw_email_sms     = get_user_meta( $user_id, 'wps_mfw_email_sms', true );
		if ( 'on' === get_option( 'wps_mfw_mute_offer_notify', 'on' ) ) :
			?>
			<div class="wps_wpr_offer_notify_main_wrappers">
				<h4 class="wps_wpr_offer_notify_settings_heading"><?php esc_html_e( 'Deactivate Notification', 'membership-for-woocommerce' ); ?></h4>
				<main class="wps_wpr_main_offer_wrapper">
					<section>
						<?php if ( 'on' === get_option( 'wps_wpr_enable_whatsapp_api_feature' ) ) : ?>
							<article>
								<div class="wps_wpr_enable_offer_setting_wrapper">
									<label for="wps_mfw_stop_whatsapp_notify"><input type="checkbox" class="wps_msfw_stop_notifications" id="wps_mfw_stop_whatsapp_notify" value="yes" <?php checked( $wps_mfw_stop_whatsapp, 'yes' ); ?> data-type="whatsapp"><?php esc_html_e( 'Whatsapp Notification', 'membership-for-woocommerce' ); ?></label>
								</div>
							</article>
							<?php
						endif;
						if ( 'on' === get_option( 'wps_wpr_enable_sms_api_feature' ) ) :
							?>
							<article>
								<div class="wps_wpr_enable_offer_setting_wrapper">
									<label for="wps_mfw_stop_sms_notify"><input type="checkbox" class="wps_msfw_stop_notifications" id="wps_mfw_stop_sms_notify" value="yes" <?php checked( $wps_mfw_stop_sms, 'yes' ); ?> data-type="sms"><?php esc_html_e( 'SMS Notifications', 'membership-for-woocommerce' ); ?></label>
								</div>
							</article>
							<?php
						endif;
						if ( 'on' === get_option( 'wps_wpr_enable_email_api_feature' ) ) :
							?>
							<article>
								<div class="wps_wpr_enable_offer_setting_wrapper">
									<label for="wps_mfw_stop_email_notify"><input type="checkbox" class="wps_msfw_stop_notifications" id="wps_mfw_stop_email_notify" value="yes" <?php checked( $wps_mfw_email_sms, 'yes' ); ?> data-type="email"><?php esc_html_e( 'Email Notifications', 'membership-for-woocommerce' ); ?></label>
								</div>
							</article>
						<?php endif; ?>
					</section>
					<div class="mfw_whatsapp_stop_notice" style="display:none"></div>
				</main>
			</div>
			<?php
		endif;
	}

	/**
	 * Check is signup and login features is enable.
	 *
	 * @return bool
	 */
	public function wps_mfw_is_login_and_signup_enable() {

		$flag = false;
		if ( 'on' === get_option( 'wps_mfw_enable_override_login_signup' ) ) {

			$flag = true;
		}
		return $flag;
	}

	/**
	 * Check is google captcha is enable.
	 *
	 * @return bool
	 */
	public function wps_mfw_is_google_captcha_enable() {

		$flag = false;
		if ( 'on' === get_option( 'wps_mfw_enable_google_recaptcha' ) ) {

			$flag = true;
		}
		return $flag;
	}

	/**
	 * This function is used to override login and singup page.
	 *
	 * @param  string $path path.
	 * @param  string $template_name template name.
	 * @return string
	 */
	public function wps_mfw_override_login_page( $path, $template_name ) {

		if ( $this->wps_mfw_is_login_and_signup_enable() ) {
			if ( 'myaccount/form-login.php' == $template_name ) {

				return plugin_dir_path( __FILE__ ) . '/woocommerce/templates/myaccount/form-login.php';
			}
		}
		return $path;
	}

	/**
	 * This function is used to change my account name for guest users
	 *
	 * @param  string $title title.
	 * @return string
	 */
	public function wps_mfw_change_my_account_title_for_guests( $title ) {

		if ( $this->wps_mfw_is_login_and_signup_enable() && ( is_account_page() && ! is_user_logged_in() && in_the_loop() ) ) {
			if ( strtolower( $title ) === 'my account' ) {

				$title = '';
			}
		}
		return $title;
	}

	/**
	 * Accept our terms and conditions to create account.
	 *
	 * @param  array  $errors   errors.
	 * @param  string $username username.
	 * @param  string $email    email.
	 * @return array
	 */
	public function wps_mfw_woocommerce_register_post( $errors, $username, $email ) {

		// nonce verification.
		$nonce_value = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$nonce_value = isset( $_POST['woocommerce-register-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['woocommerce-register-nonce'] ) ) : $nonce_value; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( $this->wps_mfw_is_login_and_signup_enable() && wp_verify_nonce( $nonce_value, 'woocommerce-register' ) ) {

			// check google captcha is enable.
			if ( $this->wps_mfw_is_google_captcha_enable() ) {

				// return if captcha is not set.
				if ( empty( $_POST['g-recaptcha-response'] ) ) {

					return new WP_Error( 'registration-error-missing-password', __( 'Please verify that you are not a robot!.', 'membership-for-woocommerce' ) );
				}

				// verify of catpcha.
				$recaptcha_secret    = get_option( 'wps_mfw_captcha_secret_key' );
				$recaptcha_response  = ! empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';
				$remote_addr        = ! empty( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
				$verify_url          = 'https://www.google.com/recaptcha/api/siteverify';
				$response            = wp_remote_get(
					$verify_url,
					array(
						'body' => array(
							'secret'   => $recaptcha_secret,
							'response' => $recaptcha_response,
							'remoteip' => $remote_addr,
						),
					)
				);

				// get succes/error and return notices.
				$result = json_decode( wp_remote_retrieve_body( $response ), true );
				if ( ! $result['success'] ) {

					return new WP_Error( 'registration-error-missing-password', __( 'reCAPTCHA verification failed : invalid-input-response.', 'membership-for-woocommerce' ) );
				}
			}

			// check terms is accept or not and throw error.
			if ( empty( $_POST['wps_mfw_terms_and_condition'] ) ) {

				return new WP_Error( 'registration-error-missing-password', __( 'Please review and accept our Terms and Conditions policy to proceed!.', 'membership-for-woocommerce' ) );
			}
		}
		return $errors;
	}

	/**
	 * Verify captcha when user is logging on the site.
	 *
	 * @param  object $validation_error validation_error.
	 * @param  string $user_login user_login.
	 * @param  string $user_password user_password.
	 * @return object
	 */
	public function wps_mfw_woocommerce_login_process( $validation_error, $user_login, $user_password ) {

		if ( $this->wps_mfw_is_login_and_signup_enable() ) {

			$user = get_user_by( 'email', $user_login );

			if ( $this->wps_mfw_is_google_captcha_enable() ) {

				$wp_nonce = ! empty( $_REQUEST['woocommerce-login-nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['woocommerce-login-nonce'] ) ) : ( isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '' );
				if ( wp_verify_nonce( $wp_nonce, 'woocommerce-login' ) ) {

					if ( empty( $_POST['g-recaptcha-response'] ) ) {

						return new WP_Error( 'registration-error-missing-password', __( 'Please verify that you are not a robot!.', 'membership-for-woocommerce' ) );
					}

					// verify of catpcha.
					$recaptcha_secret   = get_option( 'wps_mfw_captcha_secret_key' );
					$recaptcha_response = ! empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';
					$remote_addr        = ! empty( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
					$verify_url         = 'https://www.google.com/recaptcha/api/siteverify';
					$response           = wp_remote_get(
						$verify_url,
						array(
							'body' => array(
								'secret'   => $recaptcha_secret,
								'response' => $recaptcha_response,
								'remoteip' => $remote_addr,
							),
						)
					);

					// get succes/error and return notices.
					$result = json_decode( wp_remote_retrieve_body( $response ), true );
					if ( ! $result['success'] ) {

						$validation_error->add( 'wps_mfw_captcha_error', __( 'reCAPTCHA verification failed : invalid-input-response.', 'membership-for-woocommerce' ) );
					}
				}
			}
		}
		return $validation_error;
	}

	/**
	 * This function is used to assign membership when new user register on site.
	 *
	 * @param  string $user_id user id.
	 * @return void
	 */
	public function wps_msfw_assign_membership_to_new_user( $user_id ) {

		// when user is regitering from membership registration form, we will make him members.
		if ( $this->wps_mfw_is_login_and_signup_enable() ) {

			update_user_meta( $user_id, 'is_member', 'member' );
		}

		// check login and sigup feature is enable or not.
		$wps_msfw_enable_assign_default_membership_setting = get_option( 'wps_msfw_enable_assign_default_membership_setting' );
		$wps_msfw_membership_assign_to_new_user            = get_option( 'wps_msfw_membership_assign_to_new_user' );
		if ( 'on' === $wps_msfw_enable_assign_default_membership_setting && ! empty( $wps_msfw_membership_assign_to_new_user ) ) {

			// assign membership to new user.
			$this->global_class->wps_msfw_assigned_membership_by_user_id( $user_id, $wps_msfw_membership_assign_to_new_user );
			// send welcome mail while new user register.
			$this->global_class->wps_mfw_membership_welcome_mail( $user_id );
		}
	}

	/**
	 * Adding class on body.
	 *
	 * @param  array $classes classes.
	 * @return array
	 */
	public function wps_mfw_adding_class_on_body( $classes ) {

		// You can also add conditionally, for example.
		if ( $this->wps_mfw_is_login_and_signup_enable() ) {
			if ( is_account_page() && ! is_user_logged_in() ) {

				$classes[] = 'wps_mfw_account_page_parent_wrapper';
			}
		}
		return $classes;
	}

	/**
	 * This function is used to show community users.
	 *
	 * @param  string $user_id user_id.
	 * @return void
	 */
	public function wps_mfw_members_community_html( $user_id ) {

		$wps_msfw_enable_to_show_members_community = get_option( 'wps_msfw_enable_to_show_members_community' );
		if ( 'on' === $wps_msfw_enable_to_show_members_community ) {

			global $wpdb;

			// Get the current user ID.
			$current_user_id = get_current_user_id();

			// Fetch membership data (non-empty) with user details.
			$results = $wpdb->get_results(
				"SELECT u.ID as user_id, um.meta_value 
				FROM $wpdb->users u
				JOIN $wpdb->usermeta um ON u.ID = um.user_id
				WHERE um.meta_key = 'mfw_membership_id' AND um.meta_value != ''",
				ARRAY_A
			);

			// Group users by their membership titles.
			$membership_to_users = array();
			$user_memberships    = array(); // Store the memberships per user.

			// Select appropriate meta function.
			$get_meta = function_exists( 'wps_membership_get_meta_data' ) ? 'wps_membership_get_meta_data' : 'get_post_meta';

			// Step 1: Pre-process memberships by user.
			if ( ! empty( $results ) && is_array( $results ) ) {
				foreach ( $results as $row ) {

					$user_id = (int) $row['user_id'];
					$membership_ids = @unserialize( $row['meta_value'] );

					if ( ! is_array( $membership_ids ) ) {
						continue;
					}

					// Avoid processing memberships multiple times.
					$user_memberships[ $user_id ] = array();
					$titles_seen = array();

					foreach ( $membership_ids as $membership_id ) {
						$plan = $get_meta( $membership_id, 'plan_obj', true );
						$status = $get_meta( $membership_id, 'member_status', true );

						// Only process memberships that are complete and valid.
						if ( empty( $plan ) || 'complete' !== $status ) {
							continue;
						}

						$title = $plan['post_title'];

						// Avoid processing the same title for a user multiple times.
						if ( isset( $titles_seen[ $title ] ) ) {
							continue;
						}

						// Mark the title as seen for this user.
						$titles_seen[ $title ] = true;

						// Add this user to the appropriate membership group.
						$membership_to_users[ $title ][] = $user_id;

						// Add the membership to the user’s membership list.
						$user_memberships[ $user_id ][] = $title;
					}
				}
			}

			// Step 2: Output users for the current user's memberships.
			if ( ! empty( $user_memberships ) && is_array( $user_memberships ) && isset( $user_memberships[ $current_user_id ] ) ) {
				foreach ( $user_memberships[ $current_user_id ] as $title ) {

					// Get the user IDs for this title (excluding current user).
					$user_ids = array_diff( $membership_to_users[ $title ], array( $current_user_id ) );

					if ( empty( $user_ids ) ) {

						continue;
					}

					// Fetch the user data in a single query.
					$id_list    = implode( ',', array_map( 'intval', $user_ids ) );
					$users_data = $wpdb->get_results(
						"
						SELECT ID, display_name, user_email 
						FROM $wpdb->users 
						WHERE ID IN ($id_list)
					",
						ARRAY_A
					);

					// Output the matching users.
					echo "<div class='wps-mfw_u-list-wrap'>";
					echo '<h3>' . esc_html( $title ) . ' ' . esc_html__( "User's Community", 'membership-for-woocommerce' ) . '</h3>';
					echo '<ul class="wps-mfw_u-list">';
					foreach ( $users_data as $user ) {

						$user_id     = intval( $user['ID'] );
						$display_name = esc_html( $user['display_name'] );
						$user_email   = esc_html( $user['user_email'] );

						// Get avatar URL or fallback to placeholder.
						$img_url = get_avatar_url( $user_id );
						if ( empty( $img_url ) ) {
							$img_url = 'https://secure.gravatar.com/avatar/?s=96&d=mm&r=g';
						}

						// Get background image or default.
						$user_bg_img = get_option( 'wps_msfw_user_community_bg_image' );
						if ( empty( $user_bg_img ) ) {
							$user_bg_img = MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/user-commu-cover.png';
						}

						echo '<li>';
							echo '<div class="wps-mfw_ul-img" style="background-image: url(\'' . esc_url( $user_bg_img ) . '\');">';
								echo '<img src="' . esc_url( $img_url ) . '" alt="' . esc_html( $display_name ) . '">';
							echo '</div>';
							echo '<div class="wps-mfw_ul-name">' . esc_html( $display_name ) . '</div>';
							echo '<div class="wps-mfw_ul-id">#' . esc_html( $user_id ) . '</div>';
							echo '<div class="wps-mfw_ul-email">' . esc_html( $user_email ) . '</div>';
							echo '<div class="wps-mfw_ul-cta">';
								echo '<button type="button" class="wps_msfw_sms" data-id="' . esc_html( $user_id ) . '">' . esc_html__( 'SMS', 'membership-for-woocommerce' ) . '</button>&nbsp';
								echo '<button type="button" class="wps_msfw_email" data-email="' . esc_html( $user_email ) . '">' . esc_html__( 'Mail', 'membership-for-woocommerce' ) . '</button>';
							echo '</div>';
						echo '</li>';
					}

					echo '</ul>';
					echo '</div>';
				}
			}

			// popup for send sms.
			?>
			<div class="wps-mfw_ul-popup-overlay">
				<div class="wps-mfw_ul-popup--shadow"></div>
				<div class="wps-mfw_ul-popup">
					<h2 class="wps-mfw_ul-message"><?php esc_html_e( 'Enter Message', 'membership-for-woocommerce' ); ?></h2>
					<textarea class="wps-mfw_ul-description" placeholder="<?php esc_html_e( 'Type your message here...', 'membership-for-woocommerce' ); ?>"></textarea>
					<p class="wps-mfw_uld-msg"></p>
					<div class="wps-mfw_ul-buttons">
						<button class="wps-mfw_ul-send"><?php esc_html_e( 'Send', 'membership-for-woocommerce' ); ?></button>
						<button class="wps-mfw_ul-close"><?php esc_html_e( 'Close', 'membership-for-woocommerce' ); ?></button>
					</div>
					<span class="wps_wpr_sms_community_loader"><img src='<?php echo esc_url( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/loader.gif'; ?>' width="50" height="50" /></span>
				</div>
			</div>

			<!-- popup html for email -->
			<div class="wps-mfw_ul-popup-2overlay">
				<div class="wps-mfw_ul-popup--shadow"></div>
				<div class="wps-mfw_ul-popup">
					<h3 class="wps-mfw_ul-message"><?php esc_html_e( 'User Email', 'membership-for-woocommerce' ); ?></h3>
					<input type="email" class="wps_msfw_send_mail_to_comm_user" value="" readonly>
					<h3 class="wps-mfw_ul-message"><?php esc_html_e( 'Enter Message', 'membership-for-woocommerce' ); ?></h3>
					<textarea class="wps-mfw_ul-description" id="wps-mfw_ul-email-description" placeholder="<?php esc_html_e( 'Type your message here...', 'membership-for-woocommerce' ); ?>"></textarea>
					<p class="wps-mfw_uld-msg"></p>
					<div class="wps-mfw_ul-buttons">
						<button class="wps-mfw_ul-email-send"><?php esc_html_e( 'Send', 'membership-for-woocommerce' ); ?></button>
						<button class="wps-mfw_ul-close"><?php esc_html_e( 'Close', 'membership-for-woocommerce' ); ?></button>
					</div>
					<span class="wps_wpr_sms_community_loader"><img src='<?php echo esc_url( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/loader.gif'; ?>' width="50" height="50" /></span>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Display data on the BuddyPress dashboard.
	 *
	 * @return void
	 */
	public function wps_msfw_show_membership_details_on_buddy_dash() {
		if ( $this->global_class->wps_mfsw_is_buddy_press_active() && get_current_user_id() && 'on' === get_option( 'wps_msfw_show_members_details_on_buddy_dash' ) ) {

			$user_id = bp_displayed_user_id() ? bp_displayed_user_id() : get_current_user_id();
			$user    = get_user_by( 'ID', $user_id );

			if ( ! $user ) {
				return;
			}

			$memberships       = get_user_meta( $user_id, 'mfw_membership_id', true );
			$active_plan_names = array();
			$discounts         = array();
			if ( ! empty( $memberships ) && is_array( $memberships ) ) {
				foreach ( $memberships as $membership_id ) {

					$plan     = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
					$status   = wps_membership_get_meta_data( $membership_id, 'member_status', true );

					if ( empty( $plan ) ) {
						continue;
					}

					if ( 'complete' === $status && ! empty( $plan['post_title'] ) ) {
						$active_plan_names[] = $plan['post_title'];
					}

					if ( ! empty( $plan['wps_memebership_plan_discount_price'] ) ) {
						$discounts[] = (float) $plan['wps_memebership_plan_discount_price'];
					}
				}
			}

			$active_plan_string = implode( ', ', $active_plan_names );
			$active_plan_string = ! empty( $active_plan_string ) ? $active_plan_string : esc_html__( 'No active plans', 'membership-for-woocommerce' );
			$max_discount       = ! empty( $discounts ) ? max( $discounts ) : 0;

			?>

			<div class="wps-msfw-buddy-press-membership-info-wrapper">
				<h3 class="wps-msfw-buddy-press-membership-info-title"><strong><?php esc_html_e( 'Membership Details', 'membership-for-woocommerce' ); ?></strong></h3>
				<table class="wps-msfw-buddy-press-membership-info-table">
					<tr>
						<th><?php esc_html_e( 'User Name', 'membership-for-woocommerce' ); ?></th>
						<td><?php echo esc_html( $user->display_name ); ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'User Email', 'membership-for-woocommerce' ); ?></th>
						<td><?php echo esc_html( $user->user_email ); ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Active Plan Name(s)', 'membership-for-woocommerce' ); ?></th>
						<td><?php echo esc_html( $active_plan_string ); ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Max Cart Discount', 'membership-for-woocommerce' ); ?></th>
						<td><?php echo wp_kses_post( wc_price( $max_discount ) ); ?></td>
					</tr>
				</table>
			</div>

			<?php
		}
	}

	/**
	 * Redirect user that are members and try to access BuddyPress Dashboard on user end.
	 *
	 * @return void
	 */
	public function wps_msfw_show_buddy_to_only_members_users() {
		// Bail early if BuddyPress isn't active, user isn't logged in, or restriction isn't enabled.
		if ( ! $this->global_class->wps_mfsw_is_buddy_press_active() || ! is_user_logged_in() || 'on' !== get_option( 'wps_msfw_restrict_buddy_dashboard' ) ) {
			return;
		}

		// Ensure we're on a BuddyPress user page.
		if ( ! function_exists( 'bp_is_user' ) || ! bp_is_user() ) {
			return;
		}

		// Get the displayed user ID or fallback to current user.
		$user_id = get_current_user_id();

		// Get the redirect page ID or URL.
		$redirect_page_id = get_option( 'wps_msfw_redirect_buddy_press_user' );
		if ( empty( $redirect_page_id ) ) {
			return;
		}

		// Redirect if the user is not marked as a member.
		if ( ! get_user_meta( $user_id, 'is_member', true ) ) {
			wp_safe_redirect( get_permalink( $redirect_page_id ) );
			exit;
		}
	}

	/**
	 * Display the PDF download option icon for member users only.
	 *
	 * @param string $html    The HTML content for the PDF download option.
	 * @param int    $post_id The ID of the post for which the PDF download option is being displayed.
	 *
	 * @throws InvalidArgumentException If the post ID or user ID is invalid.
	 * @throws Exception If the PDF download option could not be generated.
	 *
	 * @return string Modified HTML content with or without the PDF download option.
	 */
	public function wps_msfw_show_pdf_download_option_icon_only_to_members_users( $html, $post_id ) {

		// user is blocked.
		if ( ! $this->global_class->wps_mfw_is_user_block() ) {

			return '';
		}

		$user_id   = get_current_user_id();
		$is_member = get_user_meta( $user_id, 'is_member', true );

		$wps_msfw_enable_pdf_icon_for_member_users = get_option( 'wps_msfw_enable_pdf_icon_for_member_users' );

		// If the setting is enabled, then only allow members to see the icon.
		if ( 'on' === $wps_msfw_enable_pdf_icon_for_member_users ) {

			// Check if user is a member.
			if ( ! empty( $is_member ) && 'member' === $is_member ) {

				return $html; // Allow the icon (return the HTML as-is).
			} else {

				return ''; // Not a member – hide the icon.
			}
		}

		// If the setting is not enabled, return original HTML regardless of membership.
		return $html;
	}

	/**
	 * Prevents users from adding duplicate membership plans to the cart.
	 *
	 * @param WC_Cart $cart WooCommerce cart object.
	 */
	public function wps_msfw_block_duplicate_membership_in_cart( $cart ) {

		// Skip for admin, AJAX calls, or if not logged in.
		if ( is_admin() || ! is_user_logged_in() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return;
		}

		// Avoid running multiple times during WooCommerce lifecycle.
		if ( did_action( 'woocommerce_before_calculate_totals' ) > 1 ) {
			return;
		}

		$user_id      = get_current_user_id();
		$memberships  = get_user_meta( $user_id, 'mfw_membership_id', true );

		if ( empty( $memberships ) || ! is_array( $memberships ) ) {
			return;
		}

		$default_product_id = get_option( 'wps_membership_default_product' );
		$cart_items         = $cart->get_cart();

		if ( empty( $cart_items ) ) {
			return;
		}

		foreach ( $cart_items as $cart_item_key => $cart_item ) {

			// Skip if product is not the membership product.
			if ( (int) $default_product_id !== (int) $cart_item['product_id'] ) {
				return;
			}

			// Skip if cart item does not contain a plan title (i.e., not a membership product).
			if ( empty( $cart_item['plan_title'] ) ) {
				return;
			}

			foreach ( $memberships as $membership_id ) {

				$plan   = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
				$status = strtolower( wps_membership_get_meta_data( $membership_id, 'member_status', true ) );

				// Skip if plan is not complete or invalid.
				if ( empty( $plan ) || 'complete' !== $status ) {
					continue;
				}

				// Compare current plan with what's in the cart.
				if ( isset( $plan['post_title'] ) && $plan['post_title'] === $cart_item['plan_title'] ) {

					// Remove the duplicate and notify the user.
					wc_add_notice(
						sprintf(
							/* translators: %s: notice */                            esc_html__( "The %s membership plan is already active on your account. You don't need to buy it again", 'membership-for-woocommerce' ),
							esc_html( $plan['post_title'] )
						),
						'error'
					);
					$cart->remove_cart_item( $cart_item_key );
					break; // Stop checking other memberships for this item.
				}
			}
		}
	}

	/**
	 * Prevents users for placing duplicate membership order, this is work for guest user.
	 *
	 * @param  object $order   order.
	 * @param  array  $request request.
	 * @return void
	 * @throws \WC_REST_Exception If a duplicate membership is detected.
	 */
	public function wps_msfw_restrict_user_to_purchase_duplicate_membership_block( $order, $request ) {

		// Get billing email from request.
		$billing_email = $request['billing_address']['email'] ?? '';

		if ( empty( $billing_email ) || ! is_email( $billing_email ) ) {
			return;
		}

		$user = get_user_by( 'email', $billing_email );

		if ( ! $user || ! $user->ID ) {
			return; // Not a registered user.
		}

		$memberships = get_user_meta( $user->ID, 'mfw_membership_id', true );

		if ( empty( $memberships ) || ! is_array( $memberships ) ) {
			return;
		}

		$default_product_id = get_option( 'wps_membership_default_product' );
		$cart_items         = WC()->cart->get_cart();

		if ( empty( $cart_items ) ) {
			return;
		}

		foreach ( $cart_items as $cart_item_key => $cart_item ) {

			if ( (int) $default_product_id !== (int) $cart_item['product_id'] ) {
				break;
			}

			if ( empty( $cart_item['plan_title'] ) ) {
				break;
			}

			foreach ( $memberships as $membership_id ) {

				$plan   = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
				$status = strtolower( wps_membership_get_meta_data( $membership_id, 'member_status', true ) );

				if ( empty( $plan ) || 'complete' !== $status ) {
					continue;
				}

				if ( isset( $plan['post_title'] ) && $plan['post_title'] === $cart_item['plan_title'] ) {
					throw new \WC_REST_Exception(
						'woocommerce_rest_duplicate_membership',
						sprintf(
							/* translators: %s: notice */                            esc_html__( 'You already have the "%s" membership plan associated with this email. Please remove it from your cart to proceed.', 'membership-for-woocommerce' ),
							esc_html( $plan['post_title'] )
						),
						400
					);
				}
			}
		}
	}

	/**
	 * Enforce purchase limit on single product page before adding to cart.
	 *
	 * @param  bool $passed     passed.
	 * @param  int  $product_id product_id.
	 * @param  int  $quantity   quantity.
	 * @return bool
	 */
	public function wps_msfw_validate_quantity_before_add( $passed, $product_id, $quantity ) {

		$default_product_id = absint( get_option( 'wps_membership_default_product', 0 ) );
		if ( $default_product_id > 0 && (int) $product_id === $default_product_id ) {
			return $passed;
		}

		// restrict user based on his membership.
		$membership_ids = $this->global_class->wps_msfw_check_membership_id_is_valid( get_current_user_id() );
		if ( ! empty( $membership_ids ) && is_array( $membership_ids ) ) {

			$max_limit = 0;
			foreach ( $membership_ids as $id ) {

				$plan = wps_membership_get_meta_data( $id, 'plan_obj', true );
				if ( empty( $plan ) ) {

					continue;
				}

				if ( ! empty( $plan['post_status'] ) && 'publish' !== $plan['post_status'] ) {

					continue;
				}

				if ( ! empty( $plan['wps_set_maximum_product_purchase_limit'] ) ) {

					$max_limit = max( $max_limit, (int) $plan['wps_set_maximum_product_purchase_limit'] );
				}
			}

			// check qty limit and show error notice.
			if ( $max_limit > 0 && $quantity > $max_limit ) {
				wc_add_notice(
					sprintf(
						/* translators: %s: notice */                        esc_html__( 'Your membership level allows you to add a maximum of %d units of this product to your cart.', 'membership-for-woocommerce' ),
						$max_limit
					),
					'error'
				);
				return false;
			}
			// globally restriction ( it work when user have not any membership plan ).
		} elseif ( 'on' === get_option( 'wps_msfw_enable_product_limit_restriction_globally', '' ) ) {

			// check qty limit and show error notice.
			$wps_msfw_global_product_purchase_limit_qty = (int) get_option( 'wps_msfw_global_product_purchase_limit_qty', 0 );
			if ( $wps_msfw_global_product_purchase_limit_qty > 0 && $quantity > $wps_msfw_global_product_purchase_limit_qty ) {
				wc_add_notice(
					sprintf(
						/* translators: %s: notice */                        esc_html__( 'Only %d units allowed per product. Get a membership for higher limits.', 'membership-for-woocommerce' ),
						$wps_msfw_global_product_purchase_limit_qty
					),
					'error'
				);
				return false;
			}
		}

		return $passed;
	}

	/**
	 * This function restricts the purchase quantity of products based on the user's membership plan on cart page.
	 *
	 * @return bool
	 */
	public function wps_msfw_restrict_purchase_quantity_by_membership() {

		// restrict user based on his membership.
		$membership_ids      = $this->global_class->wps_msfw_check_membership_id_is_valid( get_current_user_id() );
		static $notice_added = false; // Prevent duplicate notices.
		if ( ! empty( $membership_ids ) && is_array( $membership_ids ) ) {

			// Find highest purchase limit among memberships.
			$max_limit = 0;
			foreach ( $membership_ids as $id ) {

				$plan = wps_membership_get_meta_data( $id, 'plan_obj', true );
				if ( empty( $plan ) ) {

					continue;
				}

				if ( ! empty( $plan['post_status'] ) && 'publish' !== $plan['post_status'] ) {

					continue;
				}

				if ( ! empty( $plan['wps_set_maximum_product_purchase_limit'] ) ) {

					$max_limit = max( $max_limit, (int) $plan['wps_set_maximum_product_purchase_limit'] );
				}
			}

			if ( $max_limit <= 0 ) {
				return; // No limit set.
			}

			// Enforce limit in cart.
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				if ( $cart_item['quantity'] > $max_limit ) {
					WC()->cart->set_quantity( $cart_item_key, $max_limit );

					if ( ! $notice_added ) {
						wc_add_notice(
							sprintf(
								/* translators: %s: notice */                                esc_html__( 'You can only purchase up to %d units of this product based on your membership limit.', 'membership-for-woocommerce' ),
								$max_limit
							),
							'error'
						);
						$notice_added = true;
					}
				}
			}
			// globally restriction ( it work when user have not any membership plan ).
		} elseif ( 'on' === get_option( 'wps_msfw_enable_product_limit_restriction_globally', '' ) ) {

			$wps_msfw_global_product_purchase_limit_qty = (int) get_option( 'wps_msfw_global_product_purchase_limit_qty', 0 );
			$default_product_id                         = absint( get_option( 'wps_membership_default_product', 0 ) );

			if ( $wps_msfw_global_product_purchase_limit_qty <= 0 ) {
				return;
			}

			// Enforce limit in cart.
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				if ( $default_product_id > 0 && (int) $cart_item['product_id'] === $default_product_id ) {
					continue;
				}
				if ( $cart_item['quantity'] > $wps_msfw_global_product_purchase_limit_qty ) {
					WC()->cart->set_quantity( $cart_item_key, $wps_msfw_global_product_purchase_limit_qty );

					if ( ! $notice_added ) {
						wc_add_notice(
							sprintf(
								/* translators: %s: notice */                                esc_html__( 'Only %d units allowed per product. Get a membership for higher limits.', 'membership-for-woocommerce' ),
								$wps_msfw_global_product_purchase_limit_qty
							),
							'error'
						);
						$notice_added = true;
					}
				}
			}
		}
	}
}
