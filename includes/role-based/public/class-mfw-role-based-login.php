<?php
/**
 * Membership login page + flow for the role-based module: a standalone WP page
 * (auto-created the moment role mode is switched on) carrying a `[wps_role_membership_login]`
 * shortcode, wired to Private Site so logged-out visitors land there instead of wp-login.php.
 *
 * Deliberately built on WordPress core's own `wp_login_form()`/`wp_signon()` handling
 * (via wp-login.php) rather than a hand-rolled authentication form — that keeps nonce
 * handling, rate-limiting hooks, and password checks exactly as secure as core itself.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based/public
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Login.
 */
class Mfw_Role_Based_Login {

	const LOGIN_PAGE_OPTION = 'mfw_role_based_login_page_id';

	const SHORTCODE = 'wps_role_membership_login';

	/**
	 * @since 3.2.0
	 */
	public static function init() {
		add_shortcode( self::SHORTCODE, array( __CLASS__, 'render_login_shortcode' ) );
		add_action( 'wp_login_failed', array( __CLASS__, 'redirect_back_on_failure' ) );
		add_action( 'wp_login', array( __CLASS__, 'record_login' ), 10, 2 );
	}

	/**
	 * Logs a successful login to the member login-activity table, shown in the admin under
	 * "Role-Based Membership". Only recorded for users who actually hold a role-membership
	 * level — a plain admin/staff login isn't "membership" activity.
	 *
	 * @since 3.2.0
	 * @param string  $user_login Unused.
	 * @param WP_User $user       The user who just logged in.
	 */
	public static function record_login( $user_login, $user ) {
		if ( Mfw_Role_Based_Repository::user_has_role_access( $user->ID ) ) {
			Mfw_Role_Based_Repository::record_login( $user->ID );
		}
	}

	/**
	 * Creates the "Membership Login" page once, the moment role mode becomes active
	 * (called from Mfw_Mode_Controller::set_mode()). Re-creates it if the stored page id
	 * was since trashed/deleted, but never duplicates it otherwise.
	 *
	 * @since 3.2.0
	 */
	public static function maybe_create_login_page() {

		$page_id = (int) get_option( self::LOGIN_PAGE_OPTION );

		if ( $page_id && 'publish' === get_post_status( $page_id ) ) {
			return;
		}

		$page_id = wp_insert_post(
			array(
				'post_title'   => __( 'Membership Login', 'membership-for-woocommerce' ),
				'post_content' => '[' . self::SHORTCODE . ']',
				'post_status'  => 'publish',
				'post_type'    => 'page',
			)
		);

		if ( $page_id && ! is_wp_error( $page_id ) ) {
			update_option( self::LOGIN_PAGE_OPTION, $page_id );
		}
	}

	/**
	 * @since 3.2.0
	 * @return string The membership login page URL, or wp_login_url() if it doesn't exist yet.
	 */
	public static function get_login_page_url() {

		$page_id = (int) get_option( self::LOGIN_PAGE_OPTION );

		if ( $page_id && 'publish' === get_post_status( $page_id ) ) {
			return get_permalink( $page_id );
		}

		return wp_login_url();
	}

	/**
	 * @since 3.2.0
	 * @param int $page_id The membership login page id.
	 * @return bool
	 */
	public static function is_login_page( $page_id ) {
		$stored = (int) get_option( self::LOGIN_PAGE_OPTION );
		return $stored && $stored === (int) $page_id;
	}

	/**
	 * [wps_role_membership_login] — the full login flow: already-logged-in state, an error
	 * notice after a failed attempt, the login form itself (core wp_login_form()), and a
	 * lost-password link. Successful login redirects to ?redirect_to if present (Private
	 * Site sends visitors here with their originally-requested URL attached), falling back
	 * to the role-membership My Account tab.
	 *
	 * @since 3.2.0
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public static function render_login_shortcode( $atts ) {

		$atts = shortcode_atts( array( 'redirect' => '' ), $atts, self::SHORTCODE );

		ob_start();

		echo '<div class="mfw-role-card">';

		$heading = Mfw_Role_Based_Admin::get_login_heading();
		echo '<h2>' . esc_html( $heading ? $heading : __( 'Membership Login', 'membership-for-woocommerce' ) ) . '</h2>';

		if ( Mfw_Role_Based_Admin::is_login_notice_enabled() && Mfw_Role_Based_Admin::get_login_notice() ) {
			echo '<div class="mfw-role-notice">' . wp_kses_post( Mfw_Role_Based_Admin::get_login_notice() ) . '</div>';
		}

		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
			?>
			<div class="mfw-role-login-wrap">
				<p>
					<?php
					printf(
						/* translators: %s: display name. */
						esc_html__( 'You are already logged in as %s.', 'membership-for-woocommerce' ),
						esc_html( $user->display_name )
					);
					?>
				</p>
				<p class="mfw-role-links">
					<?php if ( function_exists( 'wc_get_account_endpoint_url' ) ) : ?>
						<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'role-membership' ) ); ?>">
							<?php esc_html_e( 'Go to your membership', 'membership-for-woocommerce' ); ?>
						</a>
						&nbsp;|&nbsp;
					<?php endif; ?>
					<a href="<?php echo esc_url( wp_logout_url( get_permalink() ) ); ?>"><?php esc_html_e( 'Log out', 'membership-for-woocommerce' ); ?></a>
				</p>
			</div>
			<?php
			echo '</div>';
			return ob_get_clean();
		}

		if ( isset( $_GET['login'] ) && 'failed' === $_GET['login'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			echo '<p class="mfw-role-error">' . esc_html__( 'Incorrect username or password.', 'membership-for-woocommerce' ) . '</p>';
		}

		$requested_redirect = isset( $_GET['redirect_to'] ) ? esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $requested_redirect ) {
			$redirect = $requested_redirect;
		} elseif ( $atts['redirect'] ) {
			$redirect = esc_url_raw( $atts['redirect'] );
		} elseif ( function_exists( 'wc_get_account_endpoint_url' ) ) {
			$redirect = wc_get_account_endpoint_url( 'role-membership' );
		} else {
			$redirect = home_url( '/' );
		}

		wp_login_form(
			array(
				'redirect'       => $redirect,
				'form_id'        => 'mfw-role-membership-loginform',
				'label_username' => __( 'Username or Email', 'membership-for-woocommerce' ),
				'label_password' => __( 'Password', 'membership-for-woocommerce' ),
				'label_remember' => __( 'Remember Me', 'membership-for-woocommerce' ),
				'label_log_in'   => __( 'Log In', 'membership-for-woocommerce' ),
				'remember'       => true,
			)
		);

		echo '<p class="mfw-role-links"><a href="' . esc_url( wp_lostpassword_url( get_permalink() ) ) . '">' . esc_html__( 'Lost your password?', 'membership-for-woocommerce' ) . '</a></p>';

		// Not every visitor hitting this page already has an account — most obviously under
		// Private Site, where this is the *only* page a logged-out visitor can reach at all.
		// Without this, a brand-new visitor has a username/password form and no way forward.
		$register_url = Mfw_Role_Based_Self_Service::get_register_page_url();
		if ( $register_url ) {
			echo '<p class="mfw-role-links">' . esc_html__( 'New here?', 'membership-for-woocommerce' ) . ' <a href="' . esc_url( $register_url ) . '">' . esc_html__( 'Join Membership', 'membership-for-woocommerce' ) . '</a></p>';
		}

		echo '</div>';

		return ob_get_clean();
	}

	/**
	 * Sends a failed login attempt back to whichever page the form was submitted from
	 * (via the referer, the standard approach for custom login forms) with an error flag,
	 * instead of falling through to wp-login.php's own error screen. Never engages for
	 * logins attempted directly on wp-login.php/wp-admin, so the default admin login is
	 * completely unaffected.
	 *
	 * @since 3.2.0
	 */
	public static function redirect_back_on_failure() {

		$referrer = wp_get_referer();

		if ( ! $referrer || false !== strpos( $referrer, 'wp-login' ) || false !== strpos( $referrer, 'wp-admin' ) ) {
			return;
		}

		wp_safe_redirect( add_query_arg( 'login', 'failed', $referrer ) );
		exit;
	}
}
