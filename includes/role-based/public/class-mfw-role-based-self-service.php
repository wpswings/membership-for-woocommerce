<?php
/**
 * Self-service signup and level switching (Advanced Features Roadmap — "Membership
 * lifecycle"). Only levels the admin has explicitly marked `self_signup_enabled` are ever
 * selectable here — admin-only levels (the default) never appear in either flow.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based/public
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Self_Service.
 */
class Mfw_Role_Based_Self_Service {

	const REGISTER_SHORTCODE = 'wps_role_membership_register';

	const NONCE_ACTION = 'mfw_role_based_self_service';

	/**
	 * @since 3.3.0
	 */
	public static function init() {
		add_shortcode( self::REGISTER_SHORTCODE, array( __CLASS__, 'render_register_shortcode' ) );
		add_action( 'admin_post_nopriv_mfw_role_based_register', array( __CLASS__, 'handle_register' ) );
		add_action( 'admin_post_mfw_role_based_register', array( __CLASS__, 'handle_register' ) );
		add_action( 'admin_post_mfw_role_based_switch_level', array( __CLASS__, 'handle_switch_level' ) );
		add_action( 'mfw_role_membership_myaccount_after', array( __CLASS__, 'render_switch_levels' ) );
	}

	/**
	 * Finds the first published page carrying [wps_role_membership_register], so the login
	 * page (and anywhere else) can link a brand-new visitor to it without the admin having to
	 * configure a second "which page is registration" setting — the shortcode's presence on a
	 * page already answers that question, and it's the same signal enforce_private_site()
	 * uses to exempt this page from the Private Site redirect.
	 *
	 * @since 3.5.0
	 * @return string Empty string if no such page exists (e.g. self-signup isn't set up yet).
	 */
	public static function get_register_page_url() {

		if ( empty( Mfw_Role_Based_Repository::get_self_signup_levels() ) ) {
			return '';
		}

		global $wpdb;
		$page_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish' AND post_content LIKE %s ORDER BY ID ASC LIMIT 1",
				'%' . $wpdb->esc_like( '[' . self::REGISTER_SHORTCODE ) . '%'
			)
		);

		return $page_id ? get_permalink( (int) $page_id ) : '';
	}

	/**
	 * [wps_role_membership_register] — a public signup form. Only shown when at least one
	 * level is open for self-signup; otherwise renders nothing (there's nothing a visitor
	 * could select), so a page carrying this shortcode degrades gracefully if the admin
	 * later disables self-signup on every level rather than showing a broken empty form.
	 *
	 * @since 3.3.0
	 * @return string
	 */
	public static function render_register_shortcode() {

		if ( is_user_logged_in() ) {
			return '<div class="mfw-role-card"><p>' . esc_html__( 'You are already logged in.', 'membership-for-woocommerce' ) . '</p></div>';
		}

		$levels = Mfw_Role_Based_Repository::get_self_signup_levels();
		if ( empty( $levels ) ) {
			return '';
		}

		ob_start();
		?>
		<div class="mfw-role-card">
			<h2><?php esc_html_e( 'Join Membership', 'membership-for-woocommerce' ); ?></h2>
			<?php if ( isset( $_GET['mfw_register'] ) && 'failed' === $_GET['mfw_register'] ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<p class="mfw-role-error"><?php esc_html_e( 'That username or email is already in use, or a required field was missing.', 'membership-for-woocommerce' ); ?></p>
			<?php endif; ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="mfw-role-register-form">
				<input type="hidden" name="action" value="mfw_role_based_register" />
				<input type="hidden" name="mfw_nonce" value="<?php echo esc_attr( wp_create_nonce( self::NONCE_ACTION ) ); ?>" />
				<div class="mfw-role-field">
					<label for="mfw-register-username"><?php esc_html_e( 'Username', 'membership-for-woocommerce' ); ?></label>
					<input type="text" id="mfw-register-username" name="user_login" required />
				</div>
				<div class="mfw-role-field">
					<label for="mfw-register-email"><?php esc_html_e( 'Email', 'membership-for-woocommerce' ); ?></label>
					<input type="email" id="mfw-register-email" name="user_email" required />
				</div>
				<div class="mfw-role-field">
					<label for="mfw-register-level"><?php esc_html_e( 'Membership Level', 'membership-for-woocommerce' ); ?></label>
					<select name="level_id" id="mfw-register-level" required>
						<?php foreach ( $levels as $level ) : ?>
							<option value="<?php echo esc_attr( $level['id'] ); ?>" data-description="<?php echo esc_attr( $level['description'] ?? '' ); ?>">
								<?php
								if ( Mfw_Role_Based_Paid_Access::is_purchasable( $level ) ) {
									printf(
										/* translators: 1: level name, 2: formatted price. */
										esc_html__( '%1$s — %2$s', 'membership-for-woocommerce' ),
										esc_html( $level['name'] ),
										esc_html( wp_strip_all_tags( wc_price( $level['price'] ) ) )
									);
								} else {
									echo esc_html( $level['name'] . ' — ' . __( 'Free', 'membership-for-woocommerce' ) );
								}
								?>
							</option>
						<?php endforeach; ?>
					</select>
					<p id="mfw-register-level-description" class="mfw-role-level-description"></p>
					<p class="mfw-role-level-description"><?php esc_html_e( 'Choosing a paid level takes you to checkout after registering; access is granted once payment completes.', 'membership-for-woocommerce' ); ?></p>
				</div>
				<p style="position:absolute;left:-9999px;" aria-hidden="true">
					<label>
						<?php esc_html_e( 'Leave this field empty', 'membership-for-woocommerce' ); ?>
						<input type="text" name="mfw_hp" tabindex="-1" autocomplete="off" />
					</label>
				</p>
				<button type="submit" class="mfw-role-btn"><?php esc_html_e( 'Register', 'membership-for-woocommerce' ); ?></button>
			</form>
		</div>
		<script>
		( function () {
			var select = document.getElementById( 'mfw-register-level' );
			var output = document.getElementById( 'mfw-register-level-description' );
			if ( ! select || ! output ) {
				return;
			}
			function render() {
				var text = select.options[ select.selectedIndex ].getAttribute( 'data-description' );
				output.textContent = text || '';
				output.style.display = text ? '' : 'none';
			}
			select.addEventListener( 'change', render );
			render();
		} )();
		</script>
		<?php
		return ob_get_clean();
	}

	/**
	 * @since 3.3.0
	 */
	public static function handle_register() {

		$referer = wp_get_referer() ? wp_get_referer() : home_url( '/' );

		if ( ! isset( $_POST['mfw_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mfw_nonce'] ) ), self::NONCE_ACTION ) ) {
			wp_safe_redirect( add_query_arg( 'mfw_register', 'failed', $referer ) );
			exit;
		}

		// Honeypot: a real visitor never fills this hidden field in; a bot usually does.
		if ( ! empty( $_POST['mfw_hp'] ) ) {
			wp_safe_redirect( add_query_arg( 'mfw_register', 'failed', $referer ) );
			exit;
		}

		$username = isset( $_POST['user_login'] ) ? sanitize_user( wp_unslash( $_POST['user_login'] ) ) : '';
		$email    = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
		$level_id = isset( $_POST['level_id'] ) ? absint( $_POST['level_id'] ) : 0;

		$level = Mfw_Role_Based_Repository::get_level( $level_id );

		if ( ! $username || ! is_email( $email ) || ! $level || empty( $level['self_signup_enabled'] ) || username_exists( $username ) || email_exists( $email ) ) {
			wp_safe_redirect( add_query_arg( 'mfw_register', 'failed', $referer ) );
			exit;
		}

		$password = wp_generate_password();
		$user_id  = wp_create_user( $username, $password, $email );

		if ( is_wp_error( $user_id ) ) {
			wp_safe_redirect( add_query_arg( 'mfw_register', 'failed', $referer ) );
			exit;
		}

		wp_new_user_notification( $user_id, null, 'user' ); // Core's own "here's how to set your password" email.

		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id );

		// A paid level: the account exists, but access is granted only once payment
		// completes (Mfw_Role_Based_Paid_Access::grant_access_from_order()) — send them
		// straight to checkout with the level's product in the cart instead of assigning
		// it now. A free level: unchanged, assign immediately.
		if ( Mfw_Role_Based_Paid_Access::is_purchasable( $level ) ) {
			WC()->cart->empty_cart();
			WC()->cart->add_to_cart( $level['wc_product_id'] );
			wp_safe_redirect( wc_get_checkout_url() );
			exit;
		}

		Mfw_Role_Based_Repository::assign_level_to_user( $user_id, $level_id, $user_id );

		$redirect = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'role-membership' ) : home_url( '/' );
		wp_safe_redirect( $redirect );
		exit;
	}

	/**
	 * Renders a "switch level" section for the My Account "Membership" tab, listing
	 * self-signup-enabled levels the user doesn't currently hold. Fired via the
	 * `mfw_role_membership_myaccount_after` action from the account tab template.
	 *
	 * @since 3.3.0
	 */
	public static function render_switch_levels() {

		$user_id         = get_current_user_id();
		$current_ids     = Mfw_Role_Based_Repository::get_user_level_ids( $user_id );
		$available       = array_filter(
			Mfw_Role_Based_Repository::get_self_signup_levels(),
			function ( $level ) use ( $current_ids ) {
				return ! in_array( (int) $level['id'], $current_ids, true );
			}
		);

		if ( empty( $available ) ) {
			return;
		}

		$nonce = wp_create_nonce( self::NONCE_ACTION );
		?>
		<h3><?php esc_html_e( 'Switch Membership Level', 'membership-for-woocommerce' ); ?></h3>
		<ul class="mfw-role-level-list">
			<?php foreach ( $available as $level ) : ?>
				<li>
					<span class="mfw-role-level-name"><?php echo esc_html( $level['name'] ); ?></span>
					<?php if ( Mfw_Role_Based_Paid_Access::is_purchasable( $level ) ) : ?>
						<span class="mfw-role-price-badge"><?php echo esc_html( wp_strip_all_tags( wc_price( $level['price'] ) ) ); ?></span>
						<?php if ( ! empty( $level['description'] ) ) : ?>
							<p class="mfw-role-level-description"><?php echo esc_html( $level['description'] ); ?></p>
						<?php endif; ?>
						<div>
							<a class="mfw-role-btn" href="<?php echo esc_url( Mfw_Role_Based_Paid_Access::get_buy_url( $level['id'] ) ); ?>">
								<?php esc_html_e( 'Buy Access', 'membership-for-woocommerce' ); ?>
							</a>
						</div>
					<?php else : ?>
						<span class="mfw-role-price-badge mfw-role-price-badge--free"><?php esc_html_e( 'Free', 'membership-for-woocommerce' ); ?></span>
						<?php if ( ! empty( $level['description'] ) ) : ?>
							<p class="mfw-role-level-description"><?php echo esc_html( $level['description'] ); ?></p>
						<?php endif; ?>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<input type="hidden" name="action" value="mfw_role_based_switch_level" />
							<input type="hidden" name="mfw_nonce" value="<?php echo esc_attr( $nonce ); ?>" />
							<input type="hidden" name="level_id" value="<?php echo esc_attr( $level['id'] ); ?>" />
							<button type="submit" class="mfw-role-btn mfw-role-btn--outline">
								<?php esc_html_e( 'Switch', 'membership-for-woocommerce' ); ?>
							</button>
						</form>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}

	/**
	 * A logged-in member switches to a different self-signup-enabled level: their current
	 * self-signup levels are revoked (admin-assigned levels are left untouched — a member
	 * can't self-revoke something an admin gave them) and the new one is assigned.
	 *
	 * @since 3.3.0
	 */
	public static function handle_switch_level() {

		if ( ! is_user_logged_in() ) {
			wp_die( esc_html__( 'You must be logged in.', 'membership-for-woocommerce' ) );
		}

		if ( ! isset( $_POST['mfw_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mfw_nonce'] ) ), self::NONCE_ACTION ) ) {
			wp_die( esc_html__( 'Security check failed.', 'membership-for-woocommerce' ) );
		}

		$user_id     = get_current_user_id();
		$new_level_id = isset( $_POST['level_id'] ) ? absint( $_POST['level_id'] ) : 0;
		$new_level   = Mfw_Role_Based_Repository::get_level( $new_level_id );

		if ( ! $new_level || empty( $new_level['self_signup_enabled'] ) ) {
			wp_die( esc_html__( 'That level is not available for self-service switching.', 'membership-for-woocommerce' ) );
		}

		// Paid levels never switch instantly for free — this endpoint is only ever meant
		// to be reached via the free-level "Switch" button, but a direct POST could
		// otherwise bypass payment entirely, so this is enforced server-side too, not just
		// by hiding the button in the UI.
		if ( Mfw_Role_Based_Paid_Access::is_purchasable( $new_level ) ) {
			wp_safe_redirect( Mfw_Role_Based_Paid_Access::get_buy_url( $new_level_id ) );
			exit;
		}

		$self_signup_ids = wp_list_pluck( Mfw_Role_Based_Repository::get_self_signup_levels(), 'id' );

		foreach ( array_intersect( Mfw_Role_Based_Repository::get_user_level_ids( $user_id ), array_map( 'absint', $self_signup_ids ) ) as $old_level_id ) {
			Mfw_Role_Based_Repository::revoke_level_from_user( $user_id, $old_level_id, $user_id );
		}

		Mfw_Role_Based_Repository::assign_level_to_user( $user_id, $new_level_id, $user_id );

		$redirect = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'role-membership' ) : home_url( '/' );
		wp_safe_redirect( $redirect );
		exit;
	}
}
