<?php
/**
 * Tiered membership: auto-upgrades an existing member to a higher level once their
 * lifetime order total (via WooCommerce's own wc_get_customer_total_spent()) reaches that
 * level's configured threshold.
 *
 * Deliberately scoped to members only (a user must already hold at least one role-based
 * level before this ever runs) — this is tier *progression* for existing members, not a
 * mechanism for enrolling brand-new members from spend alone. It only ever moves a member
 * up the ladder; it never downgrades or revokes for falling short, and it never touches a
 * level that isn't part of the ladder (tier_ladder = 0).
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Tiers.
 */
class Mfw_Role_Based_Tiers {

	/**
	 * True while an upgrade is swapping a member's tiers, so notifications can send one
	 * "you've been upgraded" email instead of a "revoked" + "assigned" pair.
	 *
	 * @var bool
	 */
	private static $upgrading = false;

	/**
	 * @since 3.5.0
	 */
	public static function init() {
		add_action( 'woocommerce_order_status_completed', array( __CLASS__, 'maybe_auto_upgrade' ) );
		add_action( 'woocommerce_order_status_processing', array( __CLASS__, 'maybe_auto_upgrade' ) );
		add_action( 'wp_ajax_mfw_role_based_recalculate_tiers', array( __CLASS__, 'ajax_recalculate_tiers' ) );
	}

	/**
	 * @since 3.5.0
	 * @return bool
	 */
	public static function is_upgrading() {
		return self::$upgrading;
	}

	/**
	 * Order-status callback. Runs on both `processing` and `completed` since either can be
	 * the "paid" state depending on the gateway (matches the pattern already used by
	 * Mfw_Role_Based_Paid_Access::grant_access_from_order()); re-running on the second
	 * status change is harmless since the comparison is always against the user's current
	 * tier, not the order that triggered the check.
	 *
	 * @since 3.5.0
	 * @param int $order_id Order id.
	 */
	public static function maybe_auto_upgrade( $order_id ) {

		if ( ! Mfw_Mode_Controller::is_role_mode() || ! function_exists( 'wc_get_order' ) ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order || ! $order->get_customer_id() ) {
			return;
		}

		// wc_get_customer_total_spent() caches its result in user meta; clear it so the order
		// that just got paid is always counted, whatever order WooCommerce ran its own hooks in.
		if ( function_exists( 'wc_delete_shop_order_transients' ) ) {
			wc_delete_shop_order_transients( $order );
		}

		self::evaluate_user( $order->get_customer_id() );
	}

	/**
	 * Moves a member up to the highest ladder tier their lifetime spend qualifies them for,
	 * if that's above the tier they hold now.
	 *
	 * @since 3.5.0
	 * @param int $user_id User id.
	 * @return array|null The level the user was moved to, or null if nothing changed.
	 */
	public static function evaluate_user( $user_id ) {

		$held_ids = array_map( 'absint', Mfw_Role_Based_Repository::get_user_level_ids( $user_id ) );

		// Not already a member — tier progression never enrolls someone new.
		if ( ! $user_id || empty( $held_ids ) ) {
			return null;
		}

		$ladder = Mfw_Role_Based_Repository::get_tier_ladder_levels();
		if ( empty( $ladder ) ) {
			return null;
		}

		$current_rank = Mfw_Role_Based_Repository::get_user_ladder_rank( $user_id );
		$spent        = function_exists( 'wc_get_customer_total_spent' ) ? (float) wc_get_customer_total_spent( $user_id ) : 0.0;

		$target = null;
		foreach ( $ladder as $level ) {
			if ( (float) $level['upgrade_spend_threshold'] <= $spent && (int) $level['level_rank'] > $current_rank ) {
				// Ladder is ordered lowest-rank first; keep going so $target ends up the
				// highest-rank tier the member currently qualifies for, not just the first.
				$target = $level;
			}
		}

		if ( ! $target || in_array( (int) $target['id'], $held_ids, true ) ) {
			return null;
		}

		$ladder_ids   = array_map( 'absint', wp_list_pluck( $ladder, 'id' ) );
		$replaced_ids = array_values( array_intersect( $held_ids, $ladder_ids ) );

		self::$upgrading = true;

		// Assign first, then revoke: revoke_level_from_user() only strips a role/capability if
		// no remaining level still grants it, so the new tier must already be held by then.
		Mfw_Role_Based_Repository::assign_level_to_user( $user_id, $target['id'] );

		// Only the member's OTHER ladder tier(s) are replaced — an admin-assigned, non-ladder
		// level they also hold is left untouched, same principle as the self-signup "switch
		// level" flow only ever touching self-signup levels.
		foreach ( $replaced_ids as $held_id ) {
			Mfw_Role_Based_Repository::revoke_level_from_user( $user_id, $held_id );
		}

		self::$upgrading = false;

		/**
		 * Fires after a member is automatically moved up the tier ladder.
		 *
		 * @since 3.5.0
		 * @param int   $user_id      User id.
		 * @param array $target       The level the member was moved to.
		 * @param int[] $replaced_ids Ladder level ids the member held before.
		 * @param float $spent        The member's lifetime spend.
		 */
		do_action( 'mfw_role_based_tier_upgraded', $user_id, $target, $replaced_ids, $spent );

		return $target;
	}

	/**
	 * Re-checks every member against the ladder. Order hooks only fire on new orders, so
	 * this is how members who had already spent enough before the ladder was set up (or
	 * before a threshold was lowered) get their upgrade.
	 *
	 * @since 3.5.0
	 * @return int Number of members upgraded.
	 */
	public static function recalculate_all() {

		if ( empty( Mfw_Role_Based_Repository::get_tier_ladder_levels() ) ) {
			return 0;
		}

		$upgraded = 0;
		foreach ( Mfw_Role_Based_Repository::get_member_user_ids() as $user_id ) {
			if ( self::evaluate_user( $user_id ) ) {
				++$upgraded;
			}
		}

		return $upgraded;
	}

	/**
	 * @since 3.5.0
	 */
	public static function ajax_recalculate_tiers() {

		check_ajax_referer( Mfw_Role_Based_Admin::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'membership-for-woocommerce' ) ), 403 );
		}

		$upgraded = self::recalculate_all();

		wp_send_json_success(
			array(
				'upgraded' => $upgraded,
				'message'  => sprintf(
					/* translators: %d: number of members upgraded. */
					_n( '%d member was upgraded.', '%d members were upgraded.', $upgraded, 'membership-for-woocommerce' ),
					$upgraded
				),
			)
		);
	}
}
