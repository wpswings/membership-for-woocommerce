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

		$form = self::render_register_form();
		if ( '' === $form ) {
			return '';
		}

		return '<div class="mfw-role-card"><h2>' . esc_html__( 'Join Membership', 'membership-for-woocommerce' ) . '</h2>' . $form . '</div>';
	}

	/**
	 * The registration form itself, shared by [wps_role_membership_register] and the
	 * membership login page (so a visitor without an account can sign up right there).
	 *
	 * @since 3.5.0
	 * @param array $args {
	 *     @type bool   $level_optional Offer "just create an account" instead of requiring a
	 *                                  level — the form then renders even with no
	 *                                  self-signup levels configured.
	 *     @type string $redirect_to    Where to send the new user afterwards (e.g. back to a
	 *                                  Buy Access link). Must be on this site.
	 * }
	 * @return string Empty string if there's nothing to register for.
	 */
	public static function render_register_form( $args = array() ) {

		$args = wp_parse_args(
			$args,
			array(
				'level_optional' => false,
				'redirect_to'    => '',
			)
		);

		$levels = Mfw_Role_Based_Repository::get_self_signup_levels();
		if ( empty( $levels ) && ! $args['level_optional'] ) {
			return '';
		}

		$errors = array(
			'exists'   => __( 'That username or email is already registered. Please log in instead.', 'membership-for-woocommerce' ),
			'password' => __( 'Please choose a password of at least 6 characters.', 'membership-for-woocommerce' ),
			'failed'   => __( 'That username or email is already in use, or a required field was missing.', 'membership-for-woocommerce' ),
		);
		$error  = isset( $_GET['mfw_register'] ) ? sanitize_key( wp_unslash( $_GET['mfw_register'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		ob_start();
		?>
		<?php if ( isset( $errors[ $error ] ) ) : ?>
			<p class="mfw-role-error"><?php echo esc_html( $errors[ $error ] ); ?></p>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="mfw-role-register-form">
			<input type="hidden" name="action" value="mfw_role_based_register" />
			<input type="hidden" name="mfw_nonce" value="<?php echo esc_attr( wp_create_nonce( self::NONCE_ACTION ) ); ?>" />
			<?php if ( $args['redirect_to'] ) : ?>
				<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $args['redirect_to'] ); ?>" />
			<?php endif; ?>
			<div class="mfw-role-field">
				<label for="mfw-register-username"><?php esc_html_e( 'Username', 'membership-for-woocommerce' ); ?></label>
				<input type="text" id="mfw-register-username" name="user_login" autocomplete="username" required />
			</div>
			<div class="mfw-role-field">
				<label for="mfw-register-email"><?php esc_html_e( 'Email', 'membership-for-woocommerce' ); ?></label>
				<input type="email" id="mfw-register-email" name="user_email" autocomplete="email" required />
			</div>
			<div class="mfw-role-field">
				<label for="mfw-register-password"><?php esc_html_e( 'Password', 'membership-for-woocommerce' ); ?></label>
				<input type="password" id="mfw-register-password" name="user_pass" autocomplete="new-password" minlength="6" required />
			</div>
			<?php if ( ! empty( $levels ) ) : ?>
				<div class="mfw-role-field">
					<label for="mfw-register-level"><?php esc_html_e( 'Membership Level', 'membership-for-woocommerce' ); ?></label>
					<select name="level_id" id="mfw-register-level" <?php echo $args['level_optional'] ? '' : 'required'; ?>>
						<?php if ( $args['level_optional'] ) : ?>
							<option value="0" data-description=""><?php esc_html_e( 'No plan for now — just create my account', 'membership-for-woocommerce' ); ?></option>
						<?php endif; ?>
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
			<?php endif; ?>
			<p style="position:absolute;left:-9999px;" aria-hidden="true">
				<label>
					<?php esc_html_e( 'Leave this field empty', 'membership-for-woocommerce' ); ?>
					<input type="text" name="mfw_hp" tabindex="-1" autocomplete="off" />
				</label>
			</p>
			<button type="submit" class="mfw-role-btn"><?php esc_html_e( 'Register', 'membership-for-woocommerce' ); ?></button>
		</form>
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

		$referer = wp_get_referer() ? remove_query_arg( 'mfw_register', wp_get_referer() ) : home_url( '/' );

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
		$password = isset( $_POST['user_pass'] ) ? (string) wp_unslash( $_POST['user_pass'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- passwords must not be altered.
		$level_id = isset( $_POST['level_id'] ) ? absint( $_POST['level_id'] ) : 0;

		// Only ever an on-site destination (e.g. back to the Buy Access link that sent them here).
		$redirect_to = isset( $_POST['redirect_to'] ) ? wp_validate_redirect( esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ), '' ) : '';

		if ( ! $username || ! is_email( $email ) ) {
			wp_safe_redirect( add_query_arg( 'mfw_register', 'failed', $referer ) );
			exit;
		}

		if ( username_exists( $username ) || email_exists( $email ) ) {
			wp_safe_redirect( add_query_arg( 'mfw_register', 'exists', $referer ) );
			exit;
		}

		if ( '' !== $password && strlen( $password ) < 6 ) {
			wp_safe_redirect( add_query_arg( 'mfw_register', 'password', $referer ) );
			exit;
		}

		// A level is optional (plain account registration from the login page), but if one
		// was picked it must really be open for self-signup.
		$level = null;
		if ( $level_id ) {
			$level = Mfw_Role_Based_Repository::get_level( $level_id );
			if ( ! $level || 'active' !== $level['status'] || empty( $level['self_signup_enabled'] ) || Mfw_Role_Based_Repository::level_has_unsafe_public_capabilities( $level ) ) {
				wp_safe_redirect( add_query_arg( 'mfw_register', 'failed', $referer ) );
				exit;
			}
		}

		$chose_password = '' !== $password;
		$user_id        = wp_create_user( $username, $chose_password ? $password : wp_generate_password(), $email );

		if ( is_wp_error( $user_id ) ) {
			wp_safe_redirect( add_query_arg( 'mfw_register', 'failed', $referer ) );
			exit;
		}

		// The visitor picked their own password: just tell the admin. Otherwise send core's
		// "here's how to set your password" email so they can log in again later.
		wp_new_user_notification( $user_id, null, $chose_password ? 'admin' : 'user' );

		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id );

		$account_url = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'role-membership' ) : home_url( '/' );

		// A paid level: the account exists, but access is granted only once payment
		// completes (Mfw_Role_Based_Paid_Access::grant_access_from_order()). The cart isn't
		// loaded on admin-post.php, so hand off to the frontend "Buy Access" link, which
		// fills the cart and goes to checkout. A free level: assign immediately.
		if ( $level && Mfw_Role_Based_Paid_Access::is_purchasable( $level ) ) {
			wp_safe_redirect( Mfw_Role_Based_Paid_Access::get_buy_url( $level_id ) );
			exit;
		}

		if ( $level ) {
			Mfw_Role_Based_Repository::assign_level_to_user( $user_id, $level_id, $user_id );
		}

		wp_safe_redirect( $redirect_to ? $redirect_to : $account_url );
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
		<div class="mfw-role-account-section">
			<h3><?php echo empty( $current_ids ) ? esc_html__( 'Choose a plan', 'membership-for-woocommerce' ) : esc_html__( 'Other plans', 'membership-for-woocommerce' ); ?></h3>
			<div class="mfw-role-plan-grid">
				<?php foreach ( $available as $level ) : ?>
					<?php $is_purchasable = Mfw_Role_Based_Paid_Access::is_purchasable( $level ); ?>
					<section class="mfw-role-plan mfw-role-plan--option">
						<div class="mfw-role-plan__head">
							<h4 class="mfw-role-plan__name mfw-role-level-title"><?php echo esc_html( $level['name'] ); ?></h4>
							<?php if ( $is_purchasable ) : ?>
								<span class="mfw-role-price-badge"><?php echo esc_html( wp_strip_all_tags( wc_price( $level['price'] ) ) ); ?></span>
							<?php else : ?>
								<span class="mfw-role-price-badge mfw-role-price-badge--free"><?php esc_html_e( 'Free', 'membership-for-woocommerce' ); ?></span>
							<?php endif; ?>
						</div>
						<?php if ( ! empty( $level['description'] ) ) : ?>
							<p class="mfw-role-plan__description"><?php echo esc_html( $level['description'] ); ?></p>
						<?php endif; ?>
						<?php Mfw_Role_Based_Public::render_level_benefits( $level ); ?>
						<div class="mfw-role-plan__actions">
							<?php if ( $is_purchasable ) : ?>
								<a class="mfw-role-btn" href="<?php echo esc_url( Mfw_Role_Based_Paid_Access::get_buy_url( $level['id'] ) ); ?>">
									<?php esc_html_e( 'Buy Access', 'membership-for-woocommerce' ); ?>
								</a>
							<?php else : ?>
								<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
									<input type="hidden" name="action" value="mfw_role_based_switch_level" />
									<input type="hidden" name="mfw_nonce" value="<?php echo esc_attr( $nonce ); ?>" />
									<input type="hidden" name="level_id" value="<?php echo esc_attr( $level['id'] ); ?>" />
									<button type="submit" class="mfw-role-btn">
										<?php echo empty( $current_ids ) ? esc_html__( 'Join', 'membership-for-woocommerce' ) : esc_html__( 'Switch', 'membership-for-woocommerce' ); ?>
									</button>
								</form>
							<?php endif; ?>
						</div>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * A logged-in member switches to a different self-signup-enabled level: their current
	 * free self-signup levels are revoked (admin-assigned and paid levels are left
	 * untouched — a member can't self-revoke something an admin gave them, and never loses a
	 * purchase by switching) and the new one is assigned.
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

		if ( ! $new_level || 'active' !== $new_level['status'] || empty( $new_level['self_signup_enabled'] ) || Mfw_Role_Based_Repository::level_has_unsafe_public_capabilities( $new_level ) ) {
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

		// Only free self-signup levels are swapped out. A level the member paid for is never
		// silently revoked by switching to a free one — that would throw away a purchase.
		$switchable_ids = array();
		foreach ( Mfw_Role_Based_Repository::get_self_signup_levels() as $self_signup_level ) {
			if ( ! Mfw_Role_Based_Paid_Access::is_purchasable( $self_signup_level ) ) {
				$switchable_ids[] = (int) $self_signup_level['id'];
			}
		}

		foreach ( array_intersect( Mfw_Role_Based_Repository::get_user_level_ids( $user_id ), $switchable_ids ) as $old_level_id ) {
			Mfw_Role_Based_Repository::revoke_level_from_user( $user_id, $old_level_id, $user_id );
		}

		Mfw_Role_Based_Repository::assign_level_to_user( $user_id, $new_level_id, $user_id );

		$redirect = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'role-membership' ) : home_url( '/' );
		wp_safe_redirect( $redirect );
		exit;
	}
}
