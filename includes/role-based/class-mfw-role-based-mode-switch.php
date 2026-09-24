<?php
/**
 * Mode-switching UI and behavior (WPS-7883).
 *
 * Renders into the "Membership Mode" page (registered by Mfw_Onboarding_Mode_Screen) once
 * a mode has already been chosen, via the `mfw_render_mode_switch_ui` action.
 *
 * Behavior spec (resolved product decision): switching modes ONLY changes (a) the
 * `wps_membership_mode` option and (b) which admin configuration menus are visible. It
 * never touches user meta, post meta, or any DB rows belonging to either flow — an existing
 * purchase-mode customer's access is silently preserved regardless of which mode is active.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Mode_Switch.
 */
class Mfw_Role_Based_Mode_Switch {

	const NONCE_ACTION = 'mfw_switch_membership_mode';

	/**
	 * @since 3.1.3
	 */
	public static function init() {

		add_action( 'mfw_render_mode_switch_ui', array( __CLASS__, 'render' ) );
		add_action( 'wp_ajax_mfw_switch_membership_mode', array( __CLASS__, 'ajax_switch_mode' ) );
	}

	/**
	 * @since 3.1.3
	 */
	public static function render() {

		$current_mode = Mfw_Mode_Controller::get_mode();
		$nonce        = wp_create_nonce( self::NONCE_ACTION );
		?>
		<p>
			<?php
			printf(
				/* translators: %s: current mode label. */
				esc_html__( 'Current mode: %s', 'membership-for-woocommerce' ),
				'<strong>' . ( Mfw_Mode_Controller::MODE_PURCHASE === $current_mode
					? esc_html__( 'Purchase-based', 'membership-for-woocommerce' )
					: esc_html__( 'Role-based', 'membership-for-woocommerce' ) ) . '</strong>'
			);
			?>
		</p>
		<p><?php esc_html_e( 'Switching does not delete any data — plans, members, roles, and levels from both modes are kept, and access already granted stays intact.', 'membership-for-woocommerce' ); ?></p>
		<button type="button" class="mfw-btn mfw-btn__outline" id="mfw-switch-mode-btn" data-target-mode="<?php echo esc_attr( Mfw_Mode_Controller::MODE_PURCHASE === $current_mode ? Mfw_Mode_Controller::MODE_ROLE : Mfw_Mode_Controller::MODE_PURCHASE ); ?>">
			<?php
			echo Mfw_Mode_Controller::MODE_PURCHASE === $current_mode
				? esc_html__( 'Switch to role-based mode', 'membership-for-woocommerce' )
				: esc_html__( 'Switch to purchase-based mode', 'membership-for-woocommerce' );
			?>
		</button>

		<div id="mfw-switch-mode-modal" class="mfw-modal-backdrop">
			<div class="mfw-modal">
				<p><?php esc_html_e( 'Are you sure you want to switch membership modes? This does not delete any data and can be reversed at any time.', 'membership-for-woocommerce' ); ?></p>
				<button type="button" class="mfw-btn" id="mfw-confirm-switch-mode"><?php esc_html_e( 'Confirm switch', 'membership-for-woocommerce' ); ?></button>
				<button type="button" class="mfw-btn mfw-btn__outline" id="mfw-cancel-switch-mode"><?php esc_html_e( 'Cancel', 'membership-for-woocommerce' ); ?></button>
			</div>
		</div>
		<script>
		( function () {
			var openBtn    = document.getElementById( 'mfw-switch-mode-btn' );
			var modal      = document.getElementById( 'mfw-switch-mode-modal' );
			var confirmBtn = document.getElementById( 'mfw-confirm-switch-mode' );
			var cancelBtn  = document.getElementById( 'mfw-cancel-switch-mode' );

			openBtn.addEventListener( 'click', function () { modal.style.display = 'block'; } );
			cancelBtn.addEventListener( 'click', function () { modal.style.display = 'none'; } );

			confirmBtn.addEventListener( 'click', function () {
				var data = new FormData();
				data.append( 'action', 'mfw_switch_membership_mode' );
				data.append( 'nonce', <?php echo wp_json_encode( $nonce ); ?> );
				data.append( 'mode', openBtn.getAttribute( 'data-target-mode' ) );
				fetch( ajaxurl, { method: 'POST', credentials: 'same-origin', body: data } )
					.then( function () { window.location.reload(); } );
			} );
		} )();
		</script>
		<?php
	}

	/**
	 * @since 3.1.3
	 */
	public static function ajax_switch_mode() {

		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'membership-for-woocommerce' ) ), 403 );
		}

		$mode = isset( $_POST['mode'] ) ? sanitize_key( wp_unslash( $_POST['mode'] ) ) : '';

		if ( ! Mfw_Mode_Controller::set_mode( $mode ) ) {
			wp_send_json_error( array( 'message' => __( 'Unrecognized mode.', 'membership-for-woocommerce' ) ) );
		}

		wp_send_json_success( array( 'mode' => $mode ) );
	}
}
