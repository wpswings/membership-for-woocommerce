<?php
/**
 * Activation-time mode selection screen (WPS-7878).
 *
 * A brand-new, standalone admin page — independent of the existing React setup wizard in
 * /src, so that wizard is never edited. On first activation it shows a "choose your mode"
 * screen; once a mode is chosen it hands rendering over to Mfw_Role_Based_Mode_Switch
 * (WPS-7883) via an action hook, so the same page later doubles as the "switch mode" screen.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/mode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Onboarding_Mode_Screen.
 */
class Mfw_Onboarding_Mode_Screen {

	const PAGE_SLUG = 'mfw_choose_membership_mode';

	const NONCE_ACTION = 'mfw_save_membership_mode';

	/**
	 * @since 3.1.3
	 */
	public static function init() {

		add_filter( 'wps_add_plugins_menus_array', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_notices', array( __CLASS__, 'maybe_render_first_run_notice' ) );
		add_action( 'wp_ajax_mfw_save_membership_mode_initial', array( __CLASS__, 'ajax_save_initial_mode' ) );
	}

	/**
	 * Hooked to the plugin's existing `wps_add_plugins_menus_array` filter so the page
	 * is added by the plugin's own (unmodified) admin_menu callback.
	 *
	 * @since 3.1.3
	 * @param array $menus Existing menu entries.
	 * @return array
	 */
	public static function register_menu( $menus ) {

		$menus[] = array(
			'name'      => __( 'Membership Mode', 'membership-for-woocommerce' ),
			'slug'      => self::PAGE_SLUG,
			'menu_link' => self::PAGE_SLUG,
			'instance'  => __CLASS__,
			'function'  => 'render_page',
		);

		return $menus;
	}

	/**
	 * A dismissible-free nudge pointing admins at the mode screen until a mode is chosen.
	 * Never blocks or redirects — the plugin keeps working in default purchase mode either way.
	 *
	 * @since 3.1.3
	 */
	public static function maybe_render_first_run_notice() {

		if ( Mfw_Mode_Controller::is_mode_selected() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen && false !== strpos( (string) $screen->id, self::PAGE_SLUG ) ) {
			return;
		}

		$url = admin_url( 'admin.php?page=' . self::PAGE_SLUG );
		printf(
			'<div class="notice notice-info"><p>%1$s <a href="%2$s">%3$s</a></p></div>',
			esc_html__( 'Membership For WooCommerce: choose how memberships work on this site.', 'membership-for-woocommerce' ),
			esc_url( $url ),
			esc_html__( 'Choose membership mode', 'membership-for-woocommerce' )
		);
	}

	/**
	 * Renders the admin page: the initial chooser if no mode is set yet, otherwise
	 * defers to whatever hooks into the switch-UI action (Mfw_Role_Based_Mode_Switch).
	 *
	 * @since 3.1.3
	 */
	public static function render_page() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$mfw_pro_is_active = function_exists( 'check_membership_pro_plugin_is_active' ) && check_membership_pro_plugin_is_active();
		$mode_selected     = Mfw_Mode_Controller::is_mode_selected();
		?>
		<div class="mfw-redesign-shell mfw-role-based-settings mfw-membership-mode-page">
			<div class="mfw-redesign-shell__status-strip">
				<span class="mfw-redesign-shell__status-badge"><?php echo $mfw_pro_is_active ? esc_html__( 'PRO ACTIVE', 'membership-for-woocommerce' ) : esc_html__( 'LITE ACTIVE', 'membership-for-woocommerce' ); ?></span>
				<span class="mfw-redesign-shell__status-text"><?php echo $mfw_pro_is_active ? esc_html__( 'Membership For WooCommerce Pro', 'membership-for-woocommerce' ) : esc_html__( 'Membership For WooCommerce', 'membership-for-woocommerce' ); ?> &middot; <?php esc_html_e( 'Membership Mode', 'membership-for-woocommerce' ); ?></span>
				<span class="mfw-redesign-shell__status-version"><?php echo esc_html( 'v' . MEMBERSHIP_FOR_WOOCOMMERCE_VERSION ); ?></span>
			</div>

			<main class="wps-main wps-bg-white wps-r-8 mfw-redesign-main">
				<section class="wps-section mfw-redesign-main__section">
					<div class="mfw-redesign-main__content-wrap">
						<div class="mfw-redesign-main__content-col">
							<div class="mfw-redesign-main__hero">
								<div class="mfw-redesign-main__hero-copy">
									<p class="mfw-redesign-main__hero-eyebrow"><?php esc_html_e( 'Settings', 'membership-for-woocommerce' ); ?></p>
									<h2 class="mfw-redesign-main__hero-title"><?php esc_html_e( 'Membership Mode', 'membership-for-woocommerce' ); ?></h2>
									<p class="mfw-redesign-main__hero-description">
										<?php
										echo $mode_selected
											? esc_html__( 'Choose how customers get membership access on this site. You can switch at any time — no plans, members, roles or levels are ever deleted.', 'membership-for-woocommerce' )
											: esc_html__( 'Pick how memberships work on this site. This is a one-time choice you can change later from this same page.', 'membership-for-woocommerce' );
										?>
									</p>
								</div>
							</div>

							<div class="mfw-redesign-main__tab-content">
								<?php
								if ( ! $mode_selected ) {
									self::render_chooser();
								} else {
									/**
									 * Fires on the Membership Mode page once a mode has already been chosen,
									 * so the mode-switch UI (WPS-7883) can render itself here.
									 *
									 * @since 3.1.3
									 */
									do_action( 'mfw_render_mode_switch_ui' );
								}
								?>
							</div>
						</div>

						<?php require MEMBERSHIP_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/membership-for-woocommerce-admin-sidebar.php'; ?>
					</div>
				</section>
			</main>
		</div>
		<?php
		if ( class_exists( 'Membership_For_Woocommerce_Talk_To_Expert_Form' ) ) {
			Membership_For_Woocommerce_Talk_To_Expert_Form::wps_mfw_render_modal();
		}
	}

	/**
	 * Feature list shown on each mode card, shared by the first-run chooser and the switch UI.
	 *
	 * @since 3.1.3
	 * @return array[] Keyed by mode: title, tagline, features, url (the mode's settings page).
	 */
	public static function get_mode_details() {

		return array(
			Mfw_Mode_Controller::MODE_PURCHASE => array(
				'title'    => __( 'Purchase-based', 'membership-for-woocommerce' ),
				'tagline'  => __( 'Customers buy a membership plan to gain access.', 'membership-for-woocommerce' ),
				'features' => array(
					__( 'Sell membership plans as WooCommerce products', 'membership-for-woocommerce' ),
					__( 'Plan-based discounts, free shipping and product access', 'membership-for-woocommerce' ),
					__( 'Membership duration, renewals and plan pages', 'membership-for-woocommerce' ),
				),
				'url'      => admin_url( 'admin.php?page=membership_for_woocommerce_menu' ),
			),
			Mfw_Mode_Controller::MODE_ROLE     => array(
				'title'    => __( 'Role-based', 'membership-for-woocommerce' ),
				'tagline'  => __( 'Access is granted by WordPress role and membership level.', 'membership-for-woocommerce' ),
				'features' => array(
					__( 'Levels mapped to WordPress roles and capabilities', 'membership-for-woocommerce' ),
					__( 'Free self-signup, paid levels and spend-based tier ladder', 'membership-for-woocommerce' ),
					__( 'Content restriction, private site, campaigns and reports', 'membership-for-woocommerce' ),
				),
				'url'      => admin_url( 'admin.php?page=' . Mfw_Role_Based_Admin::PAGE_SLUG ),
			),
		);
	}

	/**
	 * Renders one mode card.
	 *
	 * @since 3.1.3
	 * @param string $mode    Mode key.
	 * @param array  $details One entry from get_mode_details().
	 * @param bool   $current Whether this is the active mode.
	 * @param string $actions Pre-escaped HTML for the card's action area.
	 */
	public static function render_mode_card( $mode, $details, $current, $actions ) {
		?>
		<div class="mfw-mode-card<?php echo $current ? ' is-current' : ''; ?>" data-mode="<?php echo esc_attr( $mode ); ?>">
			<div class="mfw-mode-card__head">
				<span class="mfw-mode-card__icon dashicons <?php echo Mfw_Mode_Controller::MODE_ROLE === $mode ? 'dashicons-groups' : 'dashicons-cart'; ?>" aria-hidden="true"></span>
				<?php if ( $current ) : ?>
					<span class="mfw-mode-card__badge"><?php esc_html_e( 'Current mode', 'membership-for-woocommerce' ); ?></span>
				<?php endif; ?>
			</div>
			<h3 class="mfw-mode-card__title"><?php echo esc_html( $details['title'] ); ?></h3>
			<p class="mfw-mode-card__tagline"><?php echo esc_html( $details['tagline'] ); ?></p>
			<ul class="mfw-mode-card__features">
				<?php foreach ( $details['features'] as $feature ) : ?>
					<li><span class="dashicons dashicons-yes" aria-hidden="true"></span><?php echo esc_html( $feature ); ?></li>
				<?php endforeach; ?>
			</ul>
			<div class="mfw-mode-card__actions">
				<?php echo $actions; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts by the caller. ?>
			</div>
		</div>
		<?php
	}

	/**
	 * The first-run "purchase vs role" chooser.
	 *
	 * @since 3.1.3
	 */
	private static function render_chooser() {

		$nonce = wp_create_nonce( self::NONCE_ACTION );
		?>
		<div class="mfw-card">
			<h2><?php esc_html_e( 'Choose your membership mode', 'membership-for-woocommerce' ); ?></h2>
			<div class="mfw-mode-cards">
				<?php
				foreach ( self::get_mode_details() as $mode => $details ) {
					$actions = sprintf(
						'<button type="button" class="mfw-btn mfw-choose-mode" data-mode="%1$s">%2$s</button>',
						esc_attr( $mode ),
						/* translators: %s: mode name. */
						esc_html( sprintf( __( 'Use %s mode', 'membership-for-woocommerce' ), strtolower( $details['title'] ) ) )
					);
					self::render_mode_card( $mode, $details, false, $actions );
				}
				?>
			</div>
		</div>
		<script>
		( function() {
			var buttons = document.querySelectorAll( '.mfw-choose-mode' );
			for ( var i = 0; i < buttons.length; i++ ) {
				buttons[ i ].addEventListener( 'click', function ( e ) {
					var mode = e.currentTarget.getAttribute( 'data-mode' );
					e.currentTarget.classList.add( 'is-loading' );
					e.currentTarget.disabled = true;
					var data = new FormData();
					data.append( 'action', 'mfw_save_membership_mode_initial' );
					data.append( 'mode', mode );
					data.append( 'nonce', '<?php echo esc_js( $nonce ); ?>' );
					fetch( ajaxurl, { method: 'POST', credentials: 'same-origin', body: data } )
						.then( function () { window.location.reload(); } );
				} );
			}
		} )();
		</script>
		<?php
	}

	/**
	 * AJAX handler for the initial (activation-time) mode choice.
	 *
	 * @since 3.1.3
	 */
	public static function ajax_save_initial_mode() {

		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'membership-for-woocommerce' ) ), 403 );
		}

		// Only meaningful the first time; subsequent changes go through the
		// confirmation-modal switch flow in Mfw_Role_Based_Mode_Switch.
		if ( Mfw_Mode_Controller::is_mode_selected() ) {
			wp_send_json_error( array( 'message' => __( 'A mode has already been selected.', 'membership-for-woocommerce' ) ) );
		}

		$mode = isset( $_POST['mode'] ) ? sanitize_key( wp_unslash( $_POST['mode'] ) ) : '';

		if ( ! Mfw_Mode_Controller::set_mode( $mode ) ) {
			wp_send_json_error( array( 'message' => __( 'Unrecognized mode.', 'membership-for-woocommerce' ) ) );
		}

		wp_send_json_success( array( 'mode' => $mode ) );
	}
}
