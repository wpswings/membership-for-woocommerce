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
		$target_mode  = Mfw_Mode_Controller::MODE_PURCHASE === $current_mode ? Mfw_Mode_Controller::MODE_ROLE : Mfw_Mode_Controller::MODE_PURCHASE;
		$modes        = Mfw_Onboarding_Mode_Screen::get_mode_details();
		$nonce        = wp_create_nonce( self::NONCE_ACTION );
		?>
		<div class="mfw-card">
			<h2><?php esc_html_e( 'Active membership mode', 'membership-for-woocommerce' ); ?></h2>
			<div class="mfw-mode-cards">
				<?php
				foreach ( $modes as $mode => $details ) {
					if ( $mode === $current_mode ) {
						$actions = sprintf(
							'<a class="mfw-btn" href="%1$s">%2$s</a>',
							esc_url( $details['url'] ),
							esc_html__( 'Open settings', 'membership-for-woocommerce' )
						);
					} else {
						$actions = sprintf(
							'<button type="button" class="mfw-btn mfw-btn__outline" id="mfw-switch-mode-btn" data-target-mode="%1$s">%2$s</button>',
							esc_attr( $mode ),
							/* translators: %s: mode name. */
							esc_html( sprintf( __( 'Switch to %s mode', 'membership-for-woocommerce' ), strtolower( $details['title'] ) ) )
						);
					}
					Mfw_Onboarding_Mode_Screen::render_mode_card( $mode, $details, $mode === $current_mode, $actions );
				}
				?>
			</div>
		</div>

		<div class="mfw-card mfw-mode-safe-note">
			<span class="dashicons dashicons-shield" aria-hidden="true"></span>
			<div>
				<h2><?php esc_html_e( 'Switching is safe', 'membership-for-woocommerce' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Switching does not delete any data — plans, members, roles, and levels from both modes are kept, and access already granted stays intact. Only the active settings menus and storefront behaviour change.', 'membership-for-woocommerce' ); ?></p>
			</div>
		</div>

		<div id="mfw-switch-mode-modal" class="mfw-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="mfw-switch-mode-title">
			<div class="mfw-modal">
				<h2 id="mfw-switch-mode-title">
					<?php
					/* translators: %s: mode name. */
					echo esc_html( sprintf( __( 'Switch to %s mode?', 'membership-for-woocommerce' ), strtolower( $modes[ $target_mode ]['title'] ) ) );
					?>
				</h2>
				<p><?php esc_html_e( 'Are you sure you want to switch membership modes? This does not delete any data and can be reversed at any time.', 'membership-for-woocommerce' ); ?></p>
				<p class="mfw-modal__error" id="mfw-switch-mode-error" role="alert"></p>
				<div class="mfw-modal__actions">
					<button type="button" class="mfw-btn mfw-btn__outline" id="mfw-cancel-switch-mode"><?php esc_html_e( 'Cancel', 'membership-for-woocommerce' ); ?></button>
					<button type="button" class="mfw-btn" id="mfw-confirm-switch-mode"><?php esc_html_e( 'Confirm switch', 'membership-for-woocommerce' ); ?></button>
				</div>
			</div>
		</div>
		<script>
		( function () {
			var openBtn    = document.getElementById( 'mfw-switch-mode-btn' );
			var modal      = document.getElementById( 'mfw-switch-mode-modal' );
			var confirmBtn = document.getElementById( 'mfw-confirm-switch-mode' );
			var cancelBtn  = document.getElementById( 'mfw-cancel-switch-mode' );
			var errorBox   = document.getElementById( 'mfw-switch-mode-error' );
			var failText   = <?php echo wp_json_encode( __( 'Could not switch modes. Please try again.', 'membership-for-woocommerce' ) ); ?>;

			function closeModal() {
				if ( ! confirmBtn.disabled ) {
					modal.style.display = 'none';
				}
			}

			openBtn.addEventListener( 'click', function () {
				errorBox.textContent = '';
				modal.style.display = 'block';
				confirmBtn.focus();
			} );
			cancelBtn.addEventListener( 'click', closeModal );
			modal.addEventListener( 'click', function ( e ) {
				if ( e.target === modal ) {
					closeModal();
				}
			} );
			document.addEventListener( 'keydown', function ( e ) {
				if ( 'Escape' === e.key && 'block' === modal.style.display ) {
					closeModal();
				}
			} );

			confirmBtn.addEventListener( 'click', function () {
				var data = new FormData();
				data.append( 'action', 'mfw_switch_membership_mode' );
				data.append( 'nonce', <?php echo wp_json_encode( $nonce ); ?> );
				data.append( 'mode', openBtn.getAttribute( 'data-target-mode' ) );

				confirmBtn.classList.add( 'is-loading' );
				confirmBtn.disabled = true;
				cancelBtn.disabled  = true;
				errorBox.textContent = '';

				fetch( ajaxurl, { method: 'POST', credentials: 'same-origin', body: data } )
					.then( function ( response ) { return response.json(); } )
					.then( function ( json ) {
						if ( json && json.success ) {
							window.location.reload();
							return;
						}
						throw { mfwMessage: json && json.data && json.data.message ? json.data.message : failText };
					} )
					.catch( function ( err ) {
						confirmBtn.classList.remove( 'is-loading' );
						confirmBtn.disabled = false;
						cancelBtn.disabled  = false;
						errorBox.textContent = err && err.mfwMessage ? err.mfwMessage : failText;
					} );
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
