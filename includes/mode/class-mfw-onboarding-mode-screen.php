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

		echo '<div class="mfw-membership-mode-page">';
		echo '<div class="wps-header-container"><span class="wps-header-title">' . esc_html__( 'Membership Mode', 'membership-for-woocommerce' ) . '</span></div>';
		echo '<div class="mfw-card">';

		if ( ! Mfw_Mode_Controller::is_mode_selected() ) {
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

		echo '</div>';
		echo '</div>';
	}

	/**
	 * The first-run "purchase vs role" chooser.
	 *
	 * @since 3.1.3
	 */
	private static function render_chooser() {

		$nonce = wp_create_nonce( self::NONCE_ACTION );
		?>
		<p><?php esc_html_e( 'This is a one-time choice you can change later from this same page.', 'membership-for-woocommerce' ); ?></p>
		<div class="mfw-mode-cards">
			<div class="mfw-mode-card">
				<h2><?php esc_html_e( 'Purchase-based', 'membership-for-woocommerce' ); ?></h2>
				<p><?php esc_html_e( 'Customers buy a membership plan/product to gain access. This is the existing behavior.', 'membership-for-woocommerce' ); ?></p>
				<button type="button" class="mfw-btn mfw-choose-mode" data-mode="<?php echo esc_attr( Mfw_Mode_Controller::MODE_PURCHASE ); ?>">
					<?php esc_html_e( 'Use purchase-based mode', 'membership-for-woocommerce' ); ?>
				</button>
			</div>
			<div class="mfw-mode-card">
				<h2><?php esc_html_e( 'Role-based', 'membership-for-woocommerce' ); ?></h2>
				<p><?php esc_html_e( 'Access is granted by WordPress role/capability instead of purchase — no checkout required.', 'membership-for-woocommerce' ); ?></p>
				<button type="button" class="mfw-btn mfw-choose-mode" data-mode="<?php echo esc_attr( Mfw_Mode_Controller::MODE_ROLE ); ?>">
					<?php esc_html_e( 'Use role-based mode', 'membership-for-woocommerce' ); ?>
				</button>
			</div>
		</div>
		<script>
		( function() {
			var buttons = document.querySelectorAll( '.mfw-choose-mode' );
			for ( var i = 0; i < buttons.length; i++ ) {
				buttons[ i ].addEventListener( 'click', function ( e ) {
					var mode = e.currentTarget.getAttribute( 'data-mode' );
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
