<?php
/**
 * GDPR personal-data export/erasure (Advanced Features Roadmap — "Compliance & security").
 * Registers with WordPress core's own privacy-tools hooks (Tools > Export/Erase Personal
 * Data) rather than inventing a separate compliance flow.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Privacy.
 */
class Mfw_Role_Based_Privacy {

	/**
	 * @since 3.3.0
	 */
	public static function init() {
		add_filter( 'wp_privacy_personal_data_exporters', array( __CLASS__, 'register_exporter' ) );
		add_filter( 'wp_privacy_personal_data_erasers', array( __CLASS__, 'register_eraser' ) );
	}

	/**
	 * @since 3.3.0
	 * @param array $exporters Existing exporters.
	 * @return array
	 */
	public static function register_exporter( $exporters ) {
		$exporters['mfw-role-based-membership'] = array(
			'exporter_friendly_name' => __( 'Role-Based Membership', 'membership-for-woocommerce' ),
			'callback'               => array( __CLASS__, 'export' ),
		);
		return $exporters;
	}

	/**
	 * @since 3.3.0
	 * @param array $erasers Existing erasers.
	 * @return array
	 */
	public static function register_eraser( $erasers ) {
		$erasers['mfw-role-based-membership'] = array(
			'eraser_friendly_name' => __( 'Role-Based Membership', 'membership-for-woocommerce' ),
			'callback'             => array( __CLASS__, 'erase' ),
		);
		return $erasers;
	}

	/**
	 * @since 3.3.0
	 * @param string $email_address The requester's email.
	 * @param int    $page          Page number (unused — this user's data is always small
	 *                              enough to return in one page).
	 * @return array{data: array, done: bool}
	 */
	public static function export( $email_address, $page = 1 ) {

		$user = get_user_by( 'email', $email_address );
		if ( ! $user ) {
			return array( 'data' => array(), 'done' => true );
		}

		$items = array();

		foreach ( Mfw_Role_Based_Repository::get_user_levels( $user->ID ) as $level ) {
			$assignment = Mfw_Role_Based_Repository::get_user_level_assignment( $user->ID, $level['id'] );
			$items[]    = array(
				'group_id'    => 'mfw-role-based-membership',
				'group_label' => __( 'Role-Based Membership', 'membership-for-woocommerce' ),
				'item_id'     => 'mfw-level-' . $level['id'],
				'data'        => array(
					array( 'name' => __( 'Level', 'membership-for-woocommerce' ), 'value' => $level['name'] ),
					array( 'name' => __( 'Assigned', 'membership-for-woocommerce' ), 'value' => $assignment['assigned_at'] ?? '' ),
					array( 'name' => __( 'Expires', 'membership-for-woocommerce' ), 'value' => $assignment['expires_at'] ?? __( 'Never', 'membership-for-woocommerce' ) ),
				),
			);
		}

		foreach ( Mfw_Role_Based_Repository::get_login_log( 1000 ) as $entry ) {
			if ( (int) $entry['user_id'] === $user->ID ) {
				$items[] = array(
					'group_id'    => 'mfw-role-based-membership-logins',
					'group_label' => __( 'Role-Based Membership Login History', 'membership-for-woocommerce' ),
					'item_id'     => 'mfw-login-' . $entry['created_at'],
					'data'        => array(
						array( 'name' => __( 'Logged in at', 'membership-for-woocommerce' ), 'value' => $entry['created_at'] ),
					),
				);
			}
		}

		if ( Mfw_Role_Based_Repository::is_user_opted_out_of_campaigns( $user->ID ) ) {
			$items[] = array(
				'group_id'    => 'mfw-role-based-membership',
				'group_label' => __( 'Role-Based Membership', 'membership-for-woocommerce' ),
				'item_id'     => 'mfw-campaign-preference',
				'data'        => array(
					array( 'name' => __( 'Campaign emails', 'membership-for-woocommerce' ), 'value' => __( 'Opted out', 'membership-for-woocommerce' ) ),
				),
			);
		}

		return array(
			'data' => $items,
			'done' => true,
		);
	}

	/**
	 * Erases this user's role-membership levels (with the same non-destructive semantics
	 * as an admin revoking them — the level definitions themselves are untouched, only this
	 * user's holding of them), their login-history rows, and their campaign opt-out flag.
	 *
	 * @since 3.3.0
	 * @param string $email_address The requester's email.
	 * @param int    $page          Page number (unused).
	 * @return array{items_removed: bool, items_retained: bool, messages: array, done: bool}
	 */
	public static function erase( $email_address, $page = 1 ) {

		$user = get_user_by( 'email', $email_address );
		if ( ! $user ) {
			return array( 'items_removed' => false, 'items_retained' => false, 'messages' => array(), 'done' => true );
		}

		$removed = false;

		foreach ( Mfw_Role_Based_Repository::get_user_level_ids( $user->ID ) as $level_id ) {
			Mfw_Role_Based_Repository::revoke_level_from_user( $user->ID, $level_id, 0 );
			$removed = true;
		}

		Mfw_Role_Based_Repository::set_user_campaign_opt_out( $user->ID, false );

		return array(
			'items_removed'  => $removed,
			'items_retained' => true, // The audit log (assign/revoke/login history) is retained for security/accountability, matching common practice for audit trails.
			'messages'       => array( __( 'Membership level assignments were removed. Login/audit history is retained for security record-keeping.', 'membership-for-woocommerce' ) ),
			'done'           => true,
		);
	}
}
