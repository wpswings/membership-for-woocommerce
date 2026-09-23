<?php
/**
 * Frontend for the role-based membership module (WPS-7881): My Account tab, restriction
 * enforcement (posts/pages/products/taxonomy terms), Private Site, REST hiding, and
 * shortcodes. Fully independent of the purchase-based flow's frontend
 * (public/class-membership-for-woocommerce-public.php), which is untouched.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based/public
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Public.
 */
class Mfw_Role_Based_Public {

	const ACCOUNT_ENDPOINT = 'role-membership';

	/**
	 * @since 3.2.0
	 */
	public static function init() {

		add_action( 'init', array( __CLASS__, 'register_account_endpoint' ) );
		add_filter( 'woocommerce_account_menu_items', array( __CLASS__, 'add_account_menu_item' ) );
		add_action( 'woocommerce_account_' . self::ACCOUNT_ENDPOINT . '_endpoint', array( __CLASS__, 'render_account_tab' ) );
		add_action( 'admin_post_mfw_role_based_save_campaign_preference', array( __CLASS__, 'handle_save_campaign_preference' ) );

		// Post/page restriction: replace content with a message rather than blocking the
		// whole page — friendlier UX, and lets a restricted page still appear in listings.
		add_filter( 'the_content', array( __CLASS__, 'filter_restricted_content' ), 8 );
		add_filter( 'the_excerpt', array( __CLASS__, 'filter_restricted_content' ), 8 );

		// Taxonomy term archive restriction has no "content" to swap, so it blocks the page.
		add_action( 'template_redirect', array( __CLASS__, 'enforce_term_restriction' ) );
		add_action( 'template_redirect', array( __CLASS__, 'enforce_private_site' ), 0 );

		add_filter( 'woocommerce_is_purchasable', array( __CLASS__, 'enforce_product_restriction' ), 20, 2 );

		add_filter( 'rest_request_before_callbacks', array( __CLASS__, 'enforce_rest_restriction' ), 10, 3 );

		add_shortcode( 'wps_role_membership_restricted', array( __CLASS__, 'shortcode_restricted_content' ) );
		add_shortcode( 'wps_role_membership_level_name', array( __CLASS__, 'shortcode_level_name' ) );

		Mfw_Role_Based_Login::init();
		Mfw_Role_Based_Pricing::init();
		Mfw_Role_Based_Self_Service::init();
		Mfw_Role_Based_Campaign_Tracking::init();
	}

	/**
	 * @since 3.2.0
	 */
	public static function register_account_endpoint() {
		add_rewrite_endpoint( self::ACCOUNT_ENDPOINT, EP_ROOT | EP_PAGES );
	}

	/**
	 * @since 3.2.0
	 * @param array $items Existing My Account menu items.
	 * @return array
	 */
	public static function add_account_menu_item( $items ) {

		$items[ self::ACCOUNT_ENDPOINT ] = __( 'Membership', 'membership-for-woocommerce' );
		return $items;
	}

	/**
	 * @since 3.2.0
	 */
	public static function render_account_tab() {

		$user_id = get_current_user_id();
		$levels  = Mfw_Role_Based_Repository::get_user_levels( $user_id );

		include dirname( __FILE__ ) . '/partials/myaccount-role-tab.php';
	}

	/**
	 * Saves the "do not send me campaign emails" preference from the My Account tab —
	 * the self-service side of the unsubscribe/preference center (the other side being the
	 * one-click unsubscribe link on every campaign email, handled by
	 * Mfw_Role_Based_Campaigns::maybe_handle_unsubscribe()).
	 *
	 * @since 3.3.0
	 */
	public static function handle_save_campaign_preference() {

		if ( ! is_user_logged_in() ) {
			wp_die( esc_html__( 'You must be logged in.', 'membership-for-woocommerce' ) );
		}

		if ( ! isset( $_POST['mfw_preference_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mfw_preference_nonce'] ) ), 'mfw_role_based_campaign_preference' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'membership-for-woocommerce' ) );
		}

		Mfw_Role_Based_Repository::set_user_campaign_opt_out( get_current_user_id(), ! empty( $_POST['opt_out'] ) );

		$redirect = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'role-membership' ) : home_url( '/' );
		wp_safe_redirect( $redirect );
		exit;
	}

	/**
	 * Content-gating for posts/pages/products via content replacement, mirroring the
	 * reference "Members" plugin's UX: swap in a message rather than blocking the whole
	 * page. A per-post override message is read from post meta
	 * `mfw_role_membership_restricted_message`, falling back to a site-wide default option
	 * `mfw_role_based_default_restricted_message`.
	 *
	 * @since 3.2.0
	 * @param string $content Original content/excerpt.
	 * @return string
	 */
	public static function filter_restricted_content( $content ) {

		if ( ! is_singular() || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return $content;
		}

		$user_id = get_current_user_id();

		if ( Mfw_Role_Based_Repository::user_can_access_object( $user_id, get_post_type( $post_id ), $post_id ) ) {
			return $content;
		}

		$message = get_post_meta( $post_id, 'mfw_role_membership_restricted_message', true );

		if ( ! $message ) {
			$message = get_option(
				'mfw_role_based_default_restricted_message',
				__( 'This content is restricted to certain membership levels.', 'membership-for-woocommerce' )
			);
		}

		$output  = '<p class="mfw-role-membership-restricted">' . esc_html( $message ) . '</p>';
		$output .= self::buy_access_links( get_post_type( $post_id ), $post_id );

		return $output;
	}

	/**
	 * "Charge members for protected content": if any of the level(s) that would unlock this
	 * object are purchasable (have a price set), renders a "Buy Access" link per such level
	 * right at the point of restriction — the actual paywall moment, rather than only a
	 * generic upgrade banner pointing elsewhere.
	 *
	 * @since 3.4.0
	 * @param string $object_type 'post'|'page'|'product'.
	 * @param int    $object_id   Object id.
	 * @return string
	 */
	private static function buy_access_links( $object_type, $object_id ) {

		$level_ids = Mfw_Role_Based_Repository::get_restriction_level_ids( $object_type, $object_id );
		if ( empty( $level_ids ) ) {
			return '';
		}

		$links = array();

		foreach ( $level_ids as $level_id ) {
			$level = Mfw_Role_Based_Repository::get_level( $level_id );
			if ( $level && Mfw_Role_Based_Paid_Access::is_purchasable( $level ) ) {
				$link = sprintf(
					'<a class="mfw-role-membership-buy-access" href="%1$s">%2$s</a>',
					esc_url( Mfw_Role_Based_Paid_Access::get_buy_url( $level_id ) ),
					esc_html(
						sprintf(
							/* translators: 1: level name, 2: formatted price. */
							__( 'Buy access to %1$s — %2$s', 'membership-for-woocommerce' ),
							$level['name'],
							wp_strip_all_tags( wc_price( $level['price'] ) )
						)
					)
				);
				if ( ! empty( $level['description'] ) ) {
					$link .= ' <span class="mfw-role-level-description">' . esc_html( $level['description'] ) . '</span>';
				}
				$links[] = $link;
			}
		}

		if ( empty( $links ) ) {
			return '';
		}

		return '<p class="mfw-role-membership-buy-access-list">' . implode( ' &nbsp;|&nbsp; ', $links ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Taxonomy term archive restriction (WPS-7880/7881 gap-analysis item 4). Term archives
	 * have no single "content" to filter, so this blocks the page outright.
	 *
	 * @since 3.2.0
	 */
	public static function enforce_term_restriction() {

		if ( ! is_tax() && ! is_category() && ! is_tag() ) {
			return;
		}

		$term = get_queried_object();
		if ( ! $term instanceof WP_Term ) {
			return;
		}

		$user_id = get_current_user_id();

		if ( ! Mfw_Role_Based_Repository::user_can_access_object( $user_id, 'term', $term->term_id ) ) {
			wp_die(
				esc_html__( 'This section is restricted to certain membership levels.', 'membership-for-woocommerce' ),
				esc_html__( 'Restricted', 'membership-for-woocommerce' ),
				array( 'response' => 403 )
			);
		}
	}

	/**
	 * Private Site (WPS-7880/7881 gap-analysis item 7): while enabled and role mode is
	 * active, logged-out visitors are redirected to the login screen for every front-end
	 * request except the login/registration/password-reset pages themselves.
	 *
	 * @since 3.2.0
	 */
	public static function enforce_private_site() {

		global $pagenow;

		if ( is_admin() || is_user_logged_in() || in_array( $pagenow, array( 'wp-login.php', 'wp-register.php' ), true ) ) {
			return;
		}

		if ( ! Mfw_Mode_Controller::is_role_mode() || ! Mfw_Role_Based_Admin::is_private_site_enabled() ) {
			return;
		}

		// Never redirect the membership login page itself — it's how a logged-out
		// visitor is meant to reach the site at all under Private Site.
		if ( is_page() && Mfw_Role_Based_Login::is_login_page( get_queried_object_id() ) ) {
			return;
		}

		// Nor the self-signup page (wherever the admin has placed the
		// [wps_role_membership_register] shortcode) — Private Site is meant to keep
		// non-members out of the site's content, not to block the one page that lets a new
		// visitor become a member in the first place.
		if ( is_singular() && has_shortcode( get_post()->post_content, Mfw_Role_Based_Self_Service::REGISTER_SHORTCODE ) ) {
			return;
		}

		$current_url = home_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ?? '/' ) ) );
		$login_url   = Mfw_Role_Based_Login::get_login_page_url();

		wp_safe_redirect( add_query_arg( 'redirect_to', rawurlencode( $current_url ), $login_url ) );
		exit;
	}

	/**
	 * Product-purchasability gating, independent of
	 * wps_membership_make_membership_product_purchasable() in the purchase-based flow.
	 *
	 * @since 3.2.0
	 * @param bool       $is_purchasable Current purchasable state.
	 * @param WC_Product $product        Product being checked.
	 * @return bool
	 */
	public static function enforce_product_restriction( $is_purchasable, $product ) {

		if ( ! $is_purchasable ) {
			return $is_purchasable;
		}

		$user_id = get_current_user_id();

		if ( ! Mfw_Role_Based_Repository::user_can_access_object( $user_id, 'product', $product->get_id() ) ) {
			return false;
		}

		return $is_purchasable;
	}

	/**
	 * Hides restricted posts from single-item REST reads (WPS-7880/7881 gap-analysis item
	 * "REST/Gutenberg hiding"). Collection/list endpoints are not filtered in this pass —
	 * documented as a known limitation.
	 *
	 * @since 3.2.0
	 * @param mixed           $response Response to replace, or empty to continue.
	 * @param array           $handler  Route handler.
	 * @param WP_REST_Request $request  Request.
	 * @return mixed
	 */
	public static function enforce_rest_restriction( $response, $handler, $request ) {

		if ( ! Mfw_Mode_Controller::is_role_mode() ) {
			return $response;
		}

		if ( 'GET' !== $request->get_method() ) {
			return $response;
		}

		if ( ! preg_match( '#^/wp/v2/(posts|pages)/(\d+)$#', $request->get_route(), $matches ) ) {
			return $response;
		}

		$post_id = absint( $matches[2] );
		$user_id = get_current_user_id();

		if ( ! Mfw_Role_Based_Repository::user_can_access_object( $user_id, get_post_type( $post_id ), $post_id ) ) {
			return new WP_Error(
				'mfw_role_membership_restricted',
				__( 'This content is restricted to certain membership levels.', 'membership-for-woocommerce' ),
				array( 'status' => 403 )
			);
		}

		return $response;
	}

	/**
	 * [wps_role_membership_restricted level_id="1"]...[/wps_role_membership_restricted]
	 * Omitting level_id shows the content to any member holding at least one active level.
	 *
	 * @since 3.2.0
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Wrapped content.
	 * @return string
	 */
	public static function shortcode_restricted_content( $atts, $content = '' ) {

		$atts = shortcode_atts( array( 'level_id' => 0 ), $atts, 'wps_role_membership_restricted' );

		$user_levels = Mfw_Role_Based_Repository::get_user_levels( get_current_user_id() );

		if ( empty( $user_levels ) ) {
			return '';
		}

		if ( absint( $atts['level_id'] ) && ! in_array( absint( $atts['level_id'] ), wp_list_pluck( $user_levels, 'id' ), true ) ) {
			return '';
		}

		return do_shortcode( $content );
	}

	/**
	 * [wps_role_membership_level_name] — outputs the current user's highest-rank level name.
	 *
	 * @since 3.2.0
	 * @return string
	 */
	public static function shortcode_level_name() {

		$level = Mfw_Role_Based_Repository::get_user_level( get_current_user_id() );
		return $level ? esc_html( $level['name'] ) : '';
	}
}
