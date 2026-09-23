<?php
/**
 * Assign/revoke role-membership levels for specific users (closes the gap flagged after
 * the initial WPS-7875 delivery). Two surfaces, matching how the reference "Members"
 * plugin does it: a checkbox panel on the user profile screen, and bulk actions on the
 * Users list table.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_User_Assignment.
 */
class Mfw_Role_Based_User_Assignment {

	const NONCE_ACTION = 'mfw_role_based_user_levels';

	/**
	 * @since 3.2.0
	 */
	public static function init() {

		if ( ! Mfw_Mode_Controller::is_role_mode() ) {
			return;
		}

		add_action( 'show_user_profile', array( __CLASS__, 'render_profile_fields' ) );
		add_action( 'edit_user_profile', array( __CLASS__, 'render_profile_fields' ) );
		add_action( 'personal_options_update', array( __CLASS__, 'save_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( __CLASS__, 'save_profile_fields' ) );

		add_filter( 'bulk_actions-users', array( __CLASS__, 'register_bulk_actions' ) );
		add_filter( 'handle_bulk_actions-users', array( __CLASS__, 'handle_bulk_actions' ), 10, 3 );
		add_action( 'admin_notices', array( __CLASS__, 'render_bulk_action_notice' ) );
	}

	/**
	 * @since 3.2.0
	 * @param WP_User $user The user being edited.
	 */
	public static function render_profile_fields( $user ) {

		if ( ! current_user_can( 'promote_users' ) ) {
			return;
		}

		$levels        = Mfw_Role_Based_Repository::get_levels();
		$assigned_ids  = Mfw_Role_Based_Repository::get_user_level_ids( $user->ID );
		$nonce         = wp_create_nonce( self::NONCE_ACTION );
		?>
		<h2><?php esc_html_e( 'Role-Based Membership', 'membership-for-woocommerce' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'Membership Levels', 'membership-for-woocommerce' ); ?></th>
				<td>
					<?php wp_nonce_field( self::NONCE_ACTION, 'mfw_role_based_user_levels_nonce' ); ?>
					<?php if ( empty( $levels ) ) : ?>
						<p><em><?php esc_html_e( 'No role-membership levels have been created yet.', 'membership-for-woocommerce' ); ?></em></p>
					<?php endif; ?>
					<?php foreach ( (array) $levels as $level ) : ?>
						<label style="display:block;margin-bottom:4px;">
							<input
								type="checkbox"
								name="mfw_role_membership_level_ids[]"
								value="<?php echo esc_attr( $level['id'] ); ?>"
								<?php checked( in_array( (int) $level['id'], $assigned_ids, true ) ); ?>
							/>
							<?php echo esc_html( $level['name'] ); ?>
							<span style="color:#888;">(<?php echo esc_html( $level['wp_role'] ); ?>)</span>
						</label>
					<?php endforeach; ?>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * @since 3.2.0
	 * @param int $user_id The user being saved.
	 */
	public static function save_profile_fields( $user_id ) {

		if ( ! isset( $_POST['mfw_role_based_user_levels_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mfw_role_based_user_levels_nonce'] ) ), self::NONCE_ACTION ) ) {
			return;
		}

		if ( ! current_user_can( 'promote_users' ) ) {
			return;
		}

		$submitted_ids = isset( $_POST['mfw_role_membership_level_ids'] )
			? array_map( 'absint', (array) wp_unslash( $_POST['mfw_role_membership_level_ids'] ) )
			: array();

		$current_ids = Mfw_Role_Based_Repository::get_user_level_ids( $user_id );
		$actor_id    = get_current_user_id();

		foreach ( array_diff( $submitted_ids, $current_ids ) as $level_id ) {
			Mfw_Role_Based_Repository::assign_level_to_user( $user_id, $level_id, $actor_id );
		}

		foreach ( array_diff( $current_ids, $submitted_ids ) as $level_id ) {
			Mfw_Role_Based_Repository::revoke_level_from_user( $user_id, $level_id, $actor_id );
		}
	}

	/**
	 * Adds one "Assign: <level>" and one "Revoke: <level>" bulk action per level, since
	 * WordPress bulk actions can't take a runtime parameter from the dropdown itself.
	 *
	 * @since 3.2.0
	 * @param array $actions Existing bulk actions.
	 * @return array
	 */
	public static function register_bulk_actions( $actions ) {

		foreach ( (array) Mfw_Role_Based_Repository::get_levels() as $level ) {
			/* translators: %s: level name. */
			$actions[ 'mfw_assign_level_' . $level['id'] ] = sprintf( __( 'Assign membership level: %s', 'membership-for-woocommerce' ), $level['name'] );
			/* translators: %s: level name. */
			$actions[ 'mfw_revoke_level_' . $level['id'] ] = sprintf( __( 'Revoke membership level: %s', 'membership-for-woocommerce' ), $level['name'] );
		}

		return $actions;
	}

	/**
	 * @since 3.2.0
	 * @param string $redirect_to Redirect URL.
	 * @param string $doaction    The bulk action being performed.
	 * @param int[]  $user_ids    Selected user ids.
	 * @return string
	 */
	public static function handle_bulk_actions( $redirect_to, $doaction, $user_ids ) {

		if ( ! current_user_can( 'promote_users' ) ) {
			return $redirect_to;
		}

		$actor_id = get_current_user_id();
		$affected = 0;

		if ( preg_match( '/^mfw_assign_level_(\d+)$/', $doaction, $matches ) ) {
			foreach ( $user_ids as $user_id ) {
				if ( Mfw_Role_Based_Repository::assign_level_to_user( $user_id, absint( $matches[1] ), $actor_id ) ) {
					++$affected;
				}
			}
		} elseif ( preg_match( '/^mfw_revoke_level_(\d+)$/', $doaction, $matches ) ) {
			foreach ( $user_ids as $user_id ) {
				if ( Mfw_Role_Based_Repository::revoke_level_from_user( $user_id, absint( $matches[1] ), $actor_id ) ) {
					++$affected;
				}
			}
		} else {
			return $redirect_to;
		}

		return add_query_arg( 'mfw_role_based_bulk_affected', $affected, $redirect_to );
	}

	/**
	 * @since 3.2.0
	 */
	public static function render_bulk_action_notice() {

		if ( ! isset( $_REQUEST['mfw_role_based_bulk_affected'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$affected = absint( $_REQUEST['mfw_role_based_bulk_affected'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			/* translators: %d: number of users affected. */
			esc_html( sprintf( _n( 'Updated role-membership level for %d user.', 'Updated role-membership level for %d users.', $affected, 'membership-for-woocommerce' ), $affected ) )
		);
	}
}
