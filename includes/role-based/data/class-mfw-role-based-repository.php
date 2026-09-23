<?php
/**
 * Data-access layer for the role-based membership module (WPS-7879).
 *
 * Deliberately independent of Membership_For_Woocommerce_Global_Functions — the epic
 * calls for no shared restriction logic between the two flows, so this class re-implements
 * its own "does this user have access" primitive rather than reusing the purchase-based one.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based/data
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Repository.
 */
class Mfw_Role_Based_Repository {

	/**
	 * User meta key holding a map of currently-assigned levels: level_id => {assigned_at,
	 * expires_at, reminded}. A user can hold more than one level at once. `expires_at` is
	 * computed once at assignment time from the level's `expiry_days` (as it was then) —
	 * changing a level's expiry_days later never retroactively changes an existing
	 * assignment's expiry. `reminded` tracks whether the pre-expiry reminder email has
	 * already gone out, so Mfw_Role_Based_Expiry never sends it twice.
	 */
	const USER_LEVELS_META_KEY = 'mfw_role_membership_level_ids';

	/**
	 * User meta key: '1' if the user has opted out of campaign emails.
	 */
	const CAMPAIGN_OPT_OUT_META_KEY = 'mfw_role_membership_campaign_opt_out';

	/**
	 * Capability that always bypasses restriction checks, regardless of level —
	 * mirrors the "Members" plugin's restrict_content escape-hatch capability.
	 */
	const BYPASS_CAPABILITY = 'mfw_restrict_content';

	/**
	 * @since 3.2.0
	 * @return array[] All levels, highest rank first.
	 */
	public static function get_levels() {
		global $wpdb;
		$table = Mfw_Role_Based_Db::levels_table();
		return $wpdb->get_results( "SELECT * FROM {$table} ORDER BY level_rank DESC, id DESC", ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * @since 3.2.0
	 * @param int $level_id Level id.
	 * @return array|null
	 */
	public static function get_level( $level_id ) {
		global $wpdb;
		$table = Mfw_Role_Based_Db::levels_table();
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $level_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Create or update a role-membership level. If `wp_role` does not already exist as a
	 * registered WordPress role, it is created on the fly with the chosen capabilities —
	 * this is our equivalent of a dedicated "create custom role" screen.
	 *
	 * @since 3.2.0
	 * @param array $data {
	 *     @type int    $id           Optional. Omit/zero to create.
	 *     @type string $name         Level name.
	 *     @type string $description  What a member gets — shown to customers before they buy/join
	 *                                 (self-signup form, My Account switch-level list, Buy Access links).
	 *     @type string $wp_role      WP role slug this level maps to.
	 *     @type array  $capabilities Capability slugs granted by this level.
	 *     @type int    $rank                   Numeric rank used for "minimum rank" restrictions; higher = more access.
	 *     @type float  $discount_percent       Member discount (0-100) applied to WooCommerce product prices for this level.
	 *     @type int[]  $discount_category_ids  Product category ids the discount is restricted to; empty = all products.
	 *     @type int    $expiry_days            Days after assignment this level auto-expires; 0 = never.
	 *     @type bool   $self_signup_enabled    Whether this level can be self-selected via the registration/upgrade flow.
	 *     @type float  $price                  Price to charge for self-signup access, via WooCommerce checkout. 0 = free.
	 *     @type float  $upgrade_spend_threshold Lifetime order total (via wc_get_customer_total_spent())
	 *                                           at which an existing member is auto-upgraded into this
	 *                                           level. Only used when $tier_ladder is set; 0 = base tier.
	 *     @type bool   $tier_ladder            Whether this level is part of the auto-upgrade tier ladder.
	 *     @type string $status                 'active'|'inactive'.
	 * }
	 * @param string[] $known_capabilities Every capability slug the admin UI's capability
	 *                                     picker knows about (Mfw_Role_Based_Admin::all_known_capabilities()).
	 *                                     Only capabilities in this list are ever added/removed
	 *                                     on the mapped WP role — anything else the role already
	 *                                     has (from WordPress core or another plugin) is left alone.
	 * @return int|false Level id on success, false on failure.
	 */
	public static function save_level( $data, $known_capabilities = array() ) {
		global $wpdb;
		$table = Mfw_Role_Based_Db::levels_table();

		$capabilities = array_values( array_unique( array_map( 'sanitize_key', (array) ( $data['capabilities'] ?? array() ) ) ) );

		$row = array(
			'name'                  => sanitize_text_field( $data['name'] ?? '' ),
			'description'           => sanitize_textarea_field( $data['description'] ?? '' ),
			'wp_role'               => sanitize_key( $data['wp_role'] ?? '' ),
			'capabilities'          => wp_json_encode( $capabilities ),
			'level_rank'            => absint( $data['rank'] ?? 0 ),
			'discount_percent'      => min( 100, max( 0, (float) ( $data['discount_percent'] ?? 0 ) ) ),
			'discount_category_ids' => wp_json_encode( array_map( 'absint', (array) ( $data['discount_category_ids'] ?? array() ) ) ),
			'expiry_days'           => absint( $data['expiry_days'] ?? 0 ),
			'self_signup_enabled'   => empty( $data['self_signup_enabled'] ) ? 0 : 1,
			'price'                 => max( 0, (float) ( $data['price'] ?? 0 ) ),
			'upgrade_spend_threshold' => max( 0, (float) ( $data['upgrade_spend_threshold'] ?? 0 ) ),
			'tier_ladder'           => empty( $data['tier_ladder'] ) ? 0 : 1,
			'status'                => in_array( $data['status'] ?? 'active', array( 'active', 'inactive' ), true ) ? $data['status'] : 'active',
		);

		if ( '' === $row['name'] || '' === $row['wp_role'] ) {
			return false;
		}

		self::ensure_wp_role_exists( $row['wp_role'], $row['name'], $capabilities );
		self::sync_role_capabilities( $row['wp_role'], $capabilities, $known_capabilities );

		$level_id         = isset( $data['id'] ) ? absint( $data['id'] ) : 0;
		$existing_level    = $level_id > 0 ? self::get_level( $level_id ) : null;
		$row['wc_product_id'] = self::sync_level_product(
			$existing_level ? (int) $existing_level['wc_product_id'] : 0,
			$row['name'],
			$row['price']
		);

		if ( $level_id > 0 ) {
			$updated = $wpdb->update( $table, $row, array( 'id' => $level_id ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
			return false === $updated ? false : $level_id;
		}

		$inserted = $wpdb->insert( $table, $row ); // phpcs:ignore WordPress.DB.SlowDBQuery
		return $inserted ? (int) $wpdb->insert_id : false;
	}

	/**
	 * Keeps a level's linked WooCommerce product (used to actually charge for self-signup
	 * access — "charge members for protected content") in sync with the level's price:
	 * creates one the first time a level gets a price, updates its name/price on every
	 * save, and trashes it if the price is cleared back to 0 (self-signup for that level
	 * simply becomes free again; the product isn't needed while unpriced, but nothing
	 * about the level or any existing member's access is touched).
	 *
	 * The product is virtual (no shipping) and hidden from the shop catalog/search — it's
	 * only ever reached via the "Buy Access" link this module generates itself.
	 *
	 * @since 3.4.0
	 * @param int    $existing_product_id Currently linked product id, 0 if none yet.
	 * @param string $level_name          Level name, used as the product title.
	 * @param float  $price               0 = no product needed.
	 * @return int|null The linked product id, or null if there isn't one (price is 0 and
	 *                  none existed before).
	 */
	private static function sync_level_product( $existing_product_id, $level_name, $price ) {

		if ( $price <= 0 ) {
			if ( $existing_product_id && get_post( $existing_product_id ) ) {
				wp_trash_post( $existing_product_id );
			}
			return null;
		}

		/* translators: %s: level name. */
		$title = sprintf( __( 'Membership Access: %s', 'membership-for-woocommerce' ), $level_name );

		if ( $existing_product_id && ( $product = wc_get_product( $existing_product_id ) ) ) { // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments, Generic.CodeAnalysis.AssignmentInCondition
			if ( 'trash' === get_post_status( $existing_product_id ) ) {
				wp_untrash_post( $existing_product_id );
			}
			$product->set_name( $title );
			$product->set_regular_price( $price );
			$product->set_price( $price );
			$product->save();
			return $existing_product_id;
		}

		$product = new WC_Product_Simple();
		$product->set_name( $title );
		$product->set_regular_price( $price );
		$product->set_price( $price );
		$product->set_virtual( true );
		$product->set_catalog_visibility( 'hidden' );
		$product->set_status( 'publish' );
		$product->save();

		return $product->get_id();
	}

	/**
	 * Creates the WP role if it doesn't exist yet, with `read` plus the level's initial
	 * capabilities. sync_role_capabilities() runs right after this on every save (create
	 * or edit), so it is the actual source of truth for an existing role's capabilities.
	 *
	 * @since 3.2.0
	 * @param string $role_slug    Role slug.
	 * @param string $display_name Role display name, used only when creating.
	 * @param array  $capabilities Capability slugs to grant when creating.
	 */
	private static function ensure_wp_role_exists( $role_slug, $display_name, $capabilities ) {

		if ( get_role( $role_slug ) ) {
			return;
		}

		$caps = array( 'read' => true );
		foreach ( $capabilities as $cap ) {
			$caps[ $cap ] = true;
		}

		add_role( $role_slug, $display_name, $caps );
	}

	/**
	 * Makes the mapped WP role's actual capabilities match what's checked for this level —
	 * this is the "full role/capability editing" behavior, not just a top-up: unchecking a
	 * capability really removes it from the role, matching the reference "Members" plugin.
	 *
	 * Deliberately scoped to `$known_capabilities` only: a capability the role already has
	 * that ISN'T in our capability picker (from WordPress core defaults on a built-in role,
	 * or from another plugin) is never touched, so mapping a level onto e.g. `editor` never
	 * strips editor's normal abilities.
	 *
	 * @since 3.2.0
	 * @param string   $role_slug          Role slug.
	 * @param string[] $desired_capabilities Capability slugs that should be granted.
	 * @param string[] $known_capabilities   Every capability slug the admin UI can toggle.
	 */
	public static function sync_role_capabilities( $role_slug, $desired_capabilities, $known_capabilities ) {

		$role = get_role( $role_slug );
		if ( ! $role || empty( $known_capabilities ) ) {
			return;
		}

		foreach ( $known_capabilities as $cap ) {
			if ( in_array( $cap, $desired_capabilities, true ) ) {
				if ( empty( $role->capabilities[ $cap ] ) ) {
					$role->add_cap( $cap );
				}
			} elseif ( ! empty( $role->capabilities[ $cap ] ) ) {
				$role->remove_cap( $cap );
			}
		}
	}

	/**
	 * @since 3.2.0
	 * @param int $level_id Level id.
	 * @return int Rows affected.
	 */
	public static function delete_level( $level_id ) {
		global $wpdb;
		$table = Mfw_Role_Based_Db::levels_table();

		$level = self::get_level( $level_id );
		if ( $level && ! empty( $level['wc_product_id'] ) && get_post( $level['wc_product_id'] ) ) {
			wp_trash_post( $level['wc_product_id'] ); // Trashed, not deleted — recoverable if the level was removed by mistake.
		}

		return (int) $wpdb->delete( $table, array( 'id' => absint( $level_id ) ) );
	}

	/**
	 * Exports every level as a plain array, suitable for wp_json_encode() (WPS-7885-style
	 * import/export, independent re-implementation — not a copy of any other plugin's format).
	 *
	 * @since 3.2.0
	 * @return array[]
	 */
	public static function export_levels() {
		$levels = self::get_levels();
		return array_map(
			function ( $level ) {
				return array(
					'name'                  => $level['name'],
					'description'           => $level['description'] ?? '',
					'wp_role'               => $level['wp_role'],
					'capabilities'          => json_decode( $level['capabilities'], true ),
					'rank'                  => (int) $level['level_rank'],
					'discount_percent'      => (float) $level['discount_percent'],
					'discount_category_ids' => json_decode( $level['discount_category_ids'], true ),
					'expiry_days'           => (int) $level['expiry_days'],
					'self_signup_enabled'   => (bool) $level['self_signup_enabled'],
					'price'                 => (float) $level['price'],
					'upgrade_spend_threshold' => (float) ( $level['upgrade_spend_threshold'] ?? 0 ),
					'tier_ladder'           => (bool) ( $level['tier_ladder'] ?? false ),
					'status'                => $level['status'],
				);
			},
			(array) $levels
		);
	}

	/**
	 * Imports levels from an array shaped like export_levels()'s output. Always creates
	 * new rows (never overwrites by id), so importing is safe to re-run.
	 *
	 * @since 3.2.0
	 * @param array $levels Array of level definitions.
	 * @return int Number of levels imported.
	 */
	public static function import_levels( $levels, $known_capabilities = array() ) {

		$imported = 0;

		foreach ( (array) $levels as $level ) {
			if ( empty( $level['name'] ) || empty( $level['wp_role'] ) ) {
				continue;
			}

			$level_id = self::save_level(
				array(
					'name'                  => $level['name'],
					'description'           => $level['description'] ?? '',
					'wp_role'               => $level['wp_role'],
					'capabilities'          => $level['capabilities'] ?? array(),
					'rank'                  => $level['rank'] ?? 0,
					'discount_percent'      => $level['discount_percent'] ?? 0,
					'discount_category_ids' => $level['discount_category_ids'] ?? array(),
					'expiry_days'           => $level['expiry_days'] ?? 0,
					'self_signup_enabled'   => $level['self_signup_enabled'] ?? false,
					'price'                 => $level['price'] ?? 0,
					'upgrade_spend_threshold' => $level['upgrade_spend_threshold'] ?? 0,
					// Files exported before tier_ladder existed: fall back to the old "threshold > 0" rule.
					'tier_ladder'           => $level['tier_ladder'] ?? ( (float) ( $level['upgrade_spend_threshold'] ?? 0 ) > 0 ),
					'status'                => $level['status'] ?? 'active',
				),
				$known_capabilities
			);

			if ( $level_id ) {
				++$imported;
			}
		}

		return $imported;
	}

	/**
	 * Assigns a user to a role-membership level: adds the WP role + its capabilities, and
	 * logs the event. A user may hold more than one level at once. Fires
	 * `mfw_role_based_after_assign_level` for the notifications module to hook into.
	 *
	 * @since 3.2.0
	 * @param int $user_id  User id.
	 * @param int $level_id Level id.
	 * @param int $actor_id User id performing the assignment (0 for system/automated).
	 * @return bool
	 */
	public static function assign_level_to_user( $user_id, $level_id, $actor_id = 0 ) {

		$level = self::get_level( $level_id );
		if ( ! $level ) {
			return false;
		}

		$user = get_user_by( 'id', $user_id );
		if ( ! $user ) {
			return false;
		}

		$user->add_role( $level['wp_role'] );

		foreach ( json_decode( $level['capabilities'], true ) ?: array() as $cap ) { // phpcs:ignore WordPress.PHP.DisallowShortTernary
			$user->add_cap( $cap );
		}

		$map                    = self::get_user_level_map( $user_id );
		$expiry_days            = (int) $level['expiry_days'];
		$map[ absint( $level_id ) ] = array(
			'assigned_at' => current_time( 'mysql' ),
			'expires_at'  => $expiry_days > 0 ? gmdate( 'Y-m-d H:i:s', strtotime( "+{$expiry_days} days", current_time( 'timestamp' ) ) ) : null, // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date, WordPress.DateTime.CurrentTimeTimestamp.Requested
			'reminded'    => false,
		);
		update_user_meta( $user_id, self::USER_LEVELS_META_KEY, $map );

		self::log_event( $user_id, $level_id, 'assigned', $actor_id );

		/**
		 * Fires after a user is assigned a role-membership level.
		 *
		 * @since 3.2.0
		 * @param int   $user_id User id.
		 * @param array $level   The level row.
		 */
		do_action( 'mfw_role_based_after_assign_level', $user_id, $level );

		return true;
	}

	/**
	 * Revokes a single role-membership level from a user. The mapped WP role/capabilities
	 * are only removed if no other level still assigned to the user also grants them, so
	 * revoking one of several held levels never strips access granted by another.
	 *
	 * @since 3.2.0
	 * @param int $user_id  User id.
	 * @param int $level_id Level id.
	 * @param int $actor_id User id performing the revocation (0 for system/automated).
	 * @return bool
	 */
	public static function revoke_level_from_user( $user_id, $level_id, $actor_id = 0 ) {

		$level = self::get_level( $level_id );
		if ( ! $level ) {
			return false;
		}

		$user = get_user_by( 'id', $user_id );
		if ( ! $user ) {
			return false;
		}

		$map = self::get_user_level_map( $user_id );
		unset( $map[ absint( $level_id ) ] );
		update_user_meta( $user_id, self::USER_LEVELS_META_KEY, $map );

		$remaining_ids       = array_map( 'absint', array_keys( $map ) );
		$remaining_levels    = array_filter( array_map( array( __CLASS__, 'get_level' ), $remaining_ids ) );
		$still_granted_roles = wp_list_pluck( $remaining_levels, 'wp_role' );
		$still_granted_caps  = array();
		foreach ( $remaining_levels as $remaining_level ) {
			$still_granted_caps = array_merge( $still_granted_caps, (array) ( json_decode( $remaining_level['capabilities'], true ) ?: array() ) ); // phpcs:ignore WordPress.PHP.DisallowShortTernary
		}

		if ( ! in_array( $level['wp_role'], $still_granted_roles, true ) ) {
			$user->remove_role( $level['wp_role'] );
		}

		foreach ( json_decode( $level['capabilities'], true ) ?: array() as $cap ) { // phpcs:ignore WordPress.PHP.DisallowShortTernary
			if ( ! in_array( $cap, $still_granted_caps, true ) ) {
				$user->remove_cap( $cap );
			}
		}

		self::log_event( $user_id, $level_id, 'revoked', $actor_id );

		/**
		 * Fires after a user's role-membership level is revoked.
		 *
		 * @since 3.2.0
		 * @param int   $user_id User id.
		 * @param array $level   The level row that was revoked.
		 */
		do_action( 'mfw_role_based_after_revoke_level', $user_id, $level );

		return true;
	}

	/**
	 * The raw level_id => {assigned_at, expires_at, reminded} map. Also transparently
	 * upgrades the pre-3.3.0 flat-array-of-ids format (no per-assignment metadata) if it's
	 * ever encountered, so nothing breaks for data written before this format existed.
	 *
	 * @since 3.3.0
	 * @param int $user_id User id.
	 * @return array<int, array{assigned_at:string, expires_at:?string, reminded:bool}>
	 */
	public static function get_user_level_map( $user_id ) {

		$raw = get_user_meta( $user_id, self::USER_LEVELS_META_KEY, true );

		if ( ! is_array( $raw ) ) {
			return array();
		}

		$map = array();

		foreach ( $raw as $key => $value ) {
			if ( is_array( $value ) ) {
				$map[ absint( $key ) ] = array(
					'assigned_at' => $value['assigned_at'] ?? current_time( 'mysql' ),
					'expires_at'  => $value['expires_at'] ?? null,
					'reminded'    => ! empty( $value['reminded'] ),
				);
			} else {
				// Legacy flat-array format: [0 => level_id, ...] with no metadata.
				$map[ absint( $value ) ] = array(
					'assigned_at' => current_time( 'mysql' ),
					'expires_at'  => null,
					'reminded'    => false,
				);
			}
		}

		return $map;
	}

	/**
	 * @since 3.2.0
	 * @param int $user_id User id.
	 * @return int[] Level ids currently assigned to the user.
	 */
	public static function get_user_level_ids( $user_id ) {
		return array_map( 'absint', array_keys( self::get_user_level_map( $user_id ) ) );
	}

	/**
	 * @since 3.3.0
	 * @param int $user_id  User id.
	 * @param int $level_id Level id.
	 * @return array{assigned_at:string, expires_at:?string, reminded:bool}|null
	 */
	public static function get_user_level_assignment( $user_id, $level_id ) {
		$map = self::get_user_level_map( $user_id );
		return $map[ absint( $level_id ) ] ?? null;
	}

	/**
	 * Marks an assignment as having had its pre-expiry reminder sent, so
	 * Mfw_Role_Based_Expiry never sends it twice.
	 *
	 * @since 3.3.0
	 * @param int $user_id  User id.
	 * @param int $level_id Level id.
	 */
	public static function mark_expiry_reminded( $user_id, $level_id ) {
		$map = self::get_user_level_map( $user_id );
		if ( isset( $map[ absint( $level_id ) ] ) ) {
			$map[ absint( $level_id ) ]['reminded'] = true;
			update_user_meta( $user_id, self::USER_LEVELS_META_KEY, $map );
		}
	}

	/**
	 * @since 3.2.0
	 * @param int $user_id User id.
	 * @return array[] The user's currently assigned level rows.
	 */
	public static function get_user_levels( $user_id ) {
		return array_values( array_filter( array_map( array( __CLASS__, 'get_level' ), self::get_user_level_ids( $user_id ) ) ) );
	}

	/**
	 * Backward-compatible single-level accessor: returns the user's highest-rank level.
	 *
	 * @since 3.2.0
	 * @param int $user_id User id.
	 * @return array|null
	 */
	public static function get_user_level( $user_id ) {
		$levels = self::get_user_levels( $user_id );
		return $levels ? $levels[0] : null; // get_levels()/get_user_levels() are already rank-sorted desc.
	}

	/**
	 * The role-based module's own "does this user have any active access" primitive —
	 * intentionally not shared with
	 * Membership_For_Woocommerce_Global_Functions::wps_mfw_check_user_has_active_membership().
	 *
	 * @since 3.2.0
	 * @param int $user_id User id.
	 * @return bool
	 */
	public static function user_has_role_access( $user_id ) {
		foreach ( self::get_user_levels( $user_id ) as $level ) {
			if ( 'active' === $level['status'] ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @since 3.2.0
	 * @param int $user_id User id.
	 * @return int Highest rank among the user's active levels, 0 if none.
	 */
	public static function get_user_max_rank( $user_id ) {
		$max = 0;
		foreach ( self::get_user_levels( $user_id ) as $level ) {
			if ( 'active' === $level['status'] ) {
				$max = max( $max, (int) $level['level_rank'] );
			}
		}
		return $max;
	}

	/**
	 * Active levels that are part of the auto-upgrade tier ladder, lowest tier first. Ladder
	 * membership is the explicit `tier_ladder` flag, so a base tier with a ₹0 threshold can be
	 * on the ladder too; levels without the flag are never touched by auto-upgrade.
	 *
	 * @since 3.5.0
	 * @return array[]
	 */
	public static function get_tier_ladder_levels() {
		global $wpdb;
		$table = Mfw_Role_Based_Db::levels_table();
		return $wpdb->get_results( // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT * FROM {$table} WHERE status = 'active' AND tier_ladder = 1 ORDER BY level_rank ASC, upgrade_spend_threshold ASC",
			ARRAY_A
		);
	}

	/**
	 * The user's highest rank among the ladder tiers they hold, or -1 if they hold none.
	 * Non-ladder levels (admin-assigned perks, etc.) are ignored, so a high-rank perk level
	 * never blocks tier progression.
	 *
	 * @since 3.5.0
	 * @param int $user_id User id.
	 * @return int
	 */
	public static function get_user_ladder_rank( $user_id ) {
		$rank = -1;
		foreach ( self::get_user_levels( $user_id ) as $level ) {
			if ( 'active' === $level['status'] && ! empty( $level['tier_ladder'] ) ) {
				$rank = max( $rank, (int) $level['level_rank'] );
			}
		}
		return $rank;
	}

	/**
	 * Configuration problems in the tier ladder that would make auto-upgrade behave
	 * unexpectedly — shown as a warning on the admin Levels tab.
	 *
	 * @since 3.5.0
	 * @return string[] Human-readable problem descriptions; empty if the ladder is fine.
	 */
	public static function get_tier_ladder_problems() {

		$ladder   = self::get_tier_ladder_levels();
		$problems = array();

		if ( 1 === count( $ladder ) ) {
			$problems[] = __( 'Only one level is in the tier ladder. Add at least one more level to the ladder so members have a tier to move up to.', 'membership-for-woocommerce' );
		}

		for ( $i = 1, $count = count( $ladder ); $i < $count; $i++ ) {
			$lower = $ladder[ $i - 1 ];
			$upper = $ladder[ $i ];

			if ( (int) $lower['level_rank'] === (int) $upper['level_rank'] ) {
				$problems[] = sprintf(
					/* translators: 1: level name, 2: level name, 3: rank. */
					__( '"%1$s" and "%2$s" have the same Rank (%3$d). Each tier in the ladder needs a different Rank.', 'membership-for-woocommerce' ),
					$lower['name'],
					$upper['name'],
					(int) $upper['level_rank']
				);
			} elseif ( (float) $upper['upgrade_spend_threshold'] <= (float) $lower['upgrade_spend_threshold'] ) {
				$problems[] = sprintf(
					/* translators: 1: higher-rank level name, 2: lower-rank level name. */
					__( '"%1$s" has a higher Rank than "%2$s" but does not need a higher spend. Members would skip straight to "%1$s".', 'membership-for-woocommerce' ),
					$upper['name'],
					$lower['name']
				);
			}
		}

		return $problems;
	}

	/**
	 * The next tier above a user's current ladder tier, and how much more they'd need to
	 * spend to reach it — used for the "upgrade progress" display on the My Account tab.
	 *
	 * @since 3.5.0
	 * @param int $user_id User id.
	 * @return array{level: array, spent: float, remaining: float}|null Null if the user is
	 *                                                                   already at the top
	 *                                                                   tier, or no ladder is
	 *                                                                   configured.
	 */
	public static function get_next_tier( $user_id ) {

		$current_rank = self::get_user_ladder_rank( $user_id );
		$spent        = function_exists( 'wc_get_customer_total_spent' ) ? (float) wc_get_customer_total_spent( $user_id ) : 0.0;

		foreach ( self::get_tier_ladder_levels() as $level ) {
			if ( (int) $level['level_rank'] > $current_rank ) {
				return array(
					'level'     => $level,
					'spent'     => $spent,
					'remaining' => max( 0, (float) $level['upgrade_spend_threshold'] - $spent ),
				);
			}
		}

		return null;
	}

	/**
	 * The discount percentage a user's role-membership levels entitle them to for a given
	 * product. When a user holds multiple active levels with different discounts, the
	 * highest applicable one applies — discounts are never stacked/summed, to keep the
	 * behavior predictable for admins. A level's `discount_category_ids` scopes its
	 * discount to specific product categories; an empty list means "all products".
	 *
	 * @since 3.2.0
	 * @param int      $user_id    User id.
	 * @param int|null $product_id Product id, or null to ignore category scoping (the
	 *                             highest discount_percent across all active levels).
	 * @return float 0-100.
	 */
	public static function get_user_discount_percent( $user_id, $product_id = null ) {

		$product_category_ids = null;
		if ( null !== $product_id && function_exists( 'wc_get_product_term_ids' ) ) {
			$product_category_ids = wc_get_product_term_ids( $product_id, 'product_cat' );
		}

		$max = 0.0;

		foreach ( self::get_user_levels( $user_id ) as $level ) {

			if ( 'active' !== $level['status'] ) {
				continue;
			}

			if ( null !== $product_category_ids ) {
				$scope = json_decode( $level['discount_category_ids'] ?? '', true );
				if ( ! empty( $scope ) && ! array_intersect( array_map( 'absint', $scope ), $product_category_ids ) ) {
					continue; // This level's discount doesn't apply to this product's categories.
				}
			}

			$max = max( $max, (float) $level['discount_percent'] );
		}

		return $max;
	}

	/**
	 * @since 3.2.0
	 * @param string $object_type 'post'|'page'|'product'|'term'.
	 * @param int    $object_id   Object id.
	 * @return array|null {level_ids: int[], min_rank: int|null, drip_days: int} or null if unrestricted.
	 */
	public static function get_restriction( $object_type, $object_id ) {
		global $wpdb;
		$table = Mfw_Role_Based_Db::restrictions_table();
		$rows  = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT level_id, min_rank, drip_days FROM {$table} WHERE object_type = %s AND object_id = %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$object_type,
				$object_id
			),
			ARRAY_A
		);

		if ( empty( $rows ) ) {
			return null;
		}

		$level_ids = array();
		$min_rank  = null;
		$drip_days = 0;

		foreach ( $rows as $row ) {
			if ( null !== $row['min_rank'] ) {
				$min_rank = (int) $row['min_rank'];
			} elseif ( (int) $row['level_id'] > 0 ) {
				$level_ids[] = (int) $row['level_id'];
			}
			$drip_days = max( $drip_days, (int) $row['drip_days'] );
		}

		return array(
			'level_ids' => $level_ids,
			'min_rank'  => $min_rank,
			'drip_days' => $drip_days,
		);
	}

	/**
	 * Backward-compatible accessor used by existing metabox UI.
	 *
	 * @since 3.2.0
	 * @param string $object_type Object type.
	 * @param int    $object_id   Object id.
	 * @return int[]
	 */
	public static function get_restriction_level_ids( $object_type, $object_id ) {
		$restriction = self::get_restriction( $object_type, $object_id );
		return $restriction ? $restriction['level_ids'] : array();
	}

	/**
	 * Replaces the restriction rule for a given object: either an explicit list of level
	 * ids, or a minimum-rank threshold (mutually exclusive), or neither to clear it.
	 *
	 * @since 3.2.0
	 * @param string   $object_type 'post'|'page'|'product'|'term'.
	 * @param int      $object_id   Object id.
	 * @param int[]    $level_ids   Explicit level ids allowed to access this object.
	 * @param int|null $min_rank    Minimum rank required, or null to use $level_ids instead.
	 * @param int      $drip_days   Days after assignment before an otherwise-qualifying
	 *                              member can access this object; 0 = immediately. Only
	 *                              meaningful with $level_ids (not $min_rank).
	 */
	public static function set_restriction( $object_type, $object_id, $level_ids, $min_rank = null, $drip_days = 0 ) {
		global $wpdb;
		$table     = Mfw_Role_Based_Db::restrictions_table();
		$drip_days = absint( $drip_days );

		$wpdb->delete( $table, array( 'object_type' => $object_type, 'object_id' => $object_id ) ); // phpcs:ignore WordPress.DB.SlowDBQuery

		if ( null !== $min_rank ) {
			$wpdb->insert( $table, array( 'level_id' => 0, 'object_type' => $object_type, 'object_id' => $object_id, 'min_rank' => absint( $min_rank ), 'drip_days' => 0 ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
			return;
		}

		foreach ( array_map( 'absint', (array) $level_ids ) as $level_id ) {
			if ( $level_id > 0 ) {
				$wpdb->insert( $table, array( 'level_id' => $level_id, 'object_type' => $object_type, 'object_id' => $object_id, 'min_rank' => null, 'drip_days' => $drip_days ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
			}
		}
	}

	/**
	 * @since 3.2.0
	 * @param int    $user_id     User id.
	 * @param string $object_type 'post'|'page'|'product'|'term'.
	 * @param int    $object_id   Object id.
	 * @return bool True if unrestricted, the user bypasses restrictions, or the user qualifies.
	 */
	public static function user_can_access_object( $user_id, $object_type, $object_id ) {

		$restriction = self::get_restriction( $object_type, $object_id );

		if ( null === $restriction ) {
			return true; // Not restricted.
		}

		if ( self::user_bypasses_restrictions( $user_id, $object_type, $object_id ) ) {
			return true;
		}

		if ( null !== $restriction['min_rank'] ) {
			return self::get_user_max_rank( $user_id ) >= $restriction['min_rank'];
		}

		if ( empty( $restriction['level_ids'] ) ) {
			return true;
		}

		if ( ! self::user_has_role_access( $user_id ) ) {
			return false;
		}

		$user_level_ids   = array_map( 'absint', wp_list_pluck( self::get_user_levels( $user_id ), 'id' ) );
		$matching_level_ids = array_intersect( $user_level_ids, $restriction['level_ids'] );

		if ( empty( $matching_level_ids ) ) {
			return false;
		}

		if ( empty( $restriction['drip_days'] ) ) {
			return true;
		}

		// Content dripping: qualifies as soon as ANY matching level has been held long
		// enough — a user holding two qualifying levels only needs one to have "aged in".
		foreach ( $matching_level_ids as $matching_level_id ) {
			$assignment = self::get_user_level_assignment( $user_id, $matching_level_id );
			if ( $assignment && self::has_drip_period_elapsed( $assignment['assigned_at'], $restriction['drip_days'] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @since 3.3.0
	 * @param string $assigned_at MySQL datetime the level was assigned.
	 * @param int    $drip_days   Days that must have elapsed.
	 * @return bool
	 */
	private static function has_drip_period_elapsed( $assigned_at, $drip_days ) {
		$assigned_timestamp = strtotime( $assigned_at );
		if ( ! $assigned_timestamp ) {
			return true; // Malformed/legacy data with no real assigned_at — don't block access.
		}
		return current_time( 'timestamp' ) >= strtotime( "+{$drip_days} days", $assigned_timestamp ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
	}

	/**
	 * Escape hatches, mirroring the "Members" plugin: the post/page/product author, anyone
	 * who can edit that specific object, and anyone holding the bypass capability always
	 * see restricted content regardless of level.
	 *
	 * @since 3.2.0
	 * @param int    $user_id     User id.
	 * @param string $object_type Object type.
	 * @param int    $object_id   Object id.
	 * @return bool
	 */
	public static function user_bypasses_restrictions( $user_id, $object_type, $object_id ) {

		if ( user_can( $user_id, self::BYPASS_CAPABILITY ) || user_can( $user_id, 'manage_options' ) ) {
			return true;
		}

		if ( 'term' === $object_type ) {
			return user_can( $user_id, 'manage_categories' );
		}

		$post = get_post( $object_id );
		if ( ! $post ) {
			return false;
		}

		if ( (int) $post->post_author === (int) $user_id ) {
			return true;
		}

		return user_can( $user_id, 'edit_post', $object_id );
	}

	/**
	 * @since 3.2.0
	 * @param int    $user_id  User id.
	 * @param int    $level_id Level id.
	 * @param string $action   'assigned'|'revoked'.
	 * @param int    $actor_id Actor user id.
	 */
	private static function log_event( $user_id, $level_id, $action, $actor_id ) {
		global $wpdb;
		$table = Mfw_Role_Based_Db::user_log_table();
		$wpdb->insert( // phpcs:ignore WordPress.DB.SlowDBQuery
			$table,
			array(
				'user_id'  => absint( $user_id ),
				'level_id' => absint( $level_id ),
				'action'   => $action,
				'actor_id' => absint( $actor_id ),
			)
		);
	}

	/**
	 * Records a successful login by a role-membership member — reuses the same audit-log
	 * table as assign/revoke (action = 'login') rather than a dedicated table, since this is
	 * conceptually the same "event in a member's membership history."
	 *
	 * @since 3.2.0
	 * @param int $user_id User id.
	 */
	public static function record_login( $user_id ) {
		$level = self::get_user_level( $user_id );
		self::log_event( $user_id, $level ? (int) $level['id'] : 0, 'login', $user_id );
	}

	/**
	 * @since 3.2.0
	 * @param int $limit Max rows.
	 * @return array[] Rows: {user_id, level_id, created_at}, most recent first.
	 */
	public static function get_login_log( $limit = 50 ) {
		global $wpdb;
		$table = Mfw_Role_Based_Db::user_log_table();
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT user_id, level_id, created_at FROM {$table} WHERE action = 'login' ORDER BY created_at DESC LIMIT %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$limit
			),
			ARRAY_A
		);
	}

	/**
	 * Dependency-free "chart" data (Advanced Features Roadmap — "Admin & reporting"): how
	 * many currently-active members each level has, for a simple bar display.
	 *
	 * @since 3.3.0
	 * @return array<string,int> Level name => member count.
	 */
	public static function get_level_distribution() {

		$distribution = array();

		foreach ( self::get_levels() as $level ) {
			$distribution[ $level['name'] ] = 0;
		}

		foreach ( self::get_member_user_ids() as $user_id ) {
			foreach ( self::get_user_levels( $user_id ) as $level ) {
				if ( isset( $distribution[ $level['name'] ] ) ) {
					++$distribution[ $level['name'] ];
				}
			}
		}

		return $distribution;
	}

	/**
	 * @since 3.3.0
	 * @param int $days How many trailing days to include.
	 * @return array<string,int> 'Y-m-d' => number of level assignments that day.
	 */
	public static function get_signups_per_day( $days = 30 ) {

		global $wpdb;
		$table = Mfw_Role_Based_Db::user_log_table();

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT DATE(created_at) AS day, COUNT(*) AS total FROM {$table} WHERE action = 'assigned' AND created_at >= %s GROUP BY DATE(created_at)", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				gmdate( 'Y-m-d 00:00:00', strtotime( "-{$days} days" ) )
			),
			ARRAY_A
		);

		$by_day = array();
		foreach ( $rows as $row ) {
			$by_day[ $row['day'] ] = (int) $row['total'];
		}

		$series = array();
		for ( $i = $days - 1; $i >= 0; $i-- ) {
			$day            = gmdate( 'Y-m-d', strtotime( "-{$i} days" ) );
			$series[ $day ] = $by_day[ $day ] ?? 0;
		}

		return $series;
	}

	/**
	 * Every user id currently holding at least one role-membership level ("members", for
	 * campaign audience targeting). WP_User_Query only confirms the meta key exists, not
	 * that it holds a non-empty array, so results are filtered in PHP afterward.
	 *
	 * @since 3.2.0
	 * @return int[]
	 */
	public static function get_member_user_ids() {

		$query = new WP_User_Query(
			array(
				'meta_key' => self::USER_LEVELS_META_KEY, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'fields'   => 'ID',
				'number'   => -1,
			)
		);

		$member_ids = array();

		foreach ( $query->get_results() as $user_id ) {
			if ( ! empty( self::get_user_level_ids( $user_id ) ) ) {
				$member_ids[] = (int) $user_id;
			}
		}

		return $member_ids;
	}

	/**
	 * Every registered user id NOT currently holding any role-membership level
	 * ("non-members", for campaign audience targeting).
	 *
	 * @since 3.2.0
	 * @return int[]
	 */
	public static function get_non_member_user_ids() {
		$all_ids    = array_map( 'intval', get_users( array( 'fields' => 'ID' ) ) );
		$member_ids = self::get_member_user_ids();
		return array_values( array_diff( $all_ids, $member_ids ) );
	}

	/**
	 * @since 3.3.0
	 * @return array[] Active levels with self_signup_enabled = 1.
	 */
	public static function get_self_signup_levels() {
		return array_values(
			array_filter(
				self::get_levels(),
				function ( $level ) {
					return 'active' === $level['status'] && ! empty( $level['self_signup_enabled'] );
				}
			)
		);
	}

	/**
	 * Every (user_id, level_id, expires_at, reminded) assignment across all members that has
	 * an expiry set — the working set Mfw_Role_Based_Expiry's daily cron scans. Only ever
	 * called from that cron job, so the per-member get_user_level_map() cost (one usermeta
	 * read each, cached by WP for the request) is acceptable at normal site scale — the same
	 * scale caveat documented for get_member_user_ids() applies here.
	 *
	 * @since 3.3.0
	 * @return array[] Rows: {user_id, level_id, expires_at, reminded}.
	 */
	public static function get_all_expiring_assignments() {

		$rows = array();

		foreach ( self::get_member_user_ids() as $user_id ) {
			foreach ( self::get_user_level_map( $user_id ) as $level_id => $assignment ) {
				if ( ! empty( $assignment['expires_at'] ) ) {
					$rows[] = array(
						'user_id'    => $user_id,
						'level_id'   => $level_id,
						'expires_at' => $assignment['expires_at'],
						'reminded'   => $assignment['reminded'],
					);
				}
			}
		}

		return $rows;
	}

	/**
	 * Members holding at least one of the given levels ("specific levels", for campaign
	 * audience targeting).
	 *
	 * @since 3.2.0
	 * @param int[] $level_ids Level ids.
	 * @return int[]
	 */
	public static function get_user_ids_for_levels( $level_ids ) {

		$level_ids = array_map( 'absint', (array) $level_ids );
		$matched   = array();

		foreach ( self::get_member_user_ids() as $user_id ) {
			if ( array_intersect( self::get_user_level_ids( $user_id ), $level_ids ) ) {
				$matched[] = $user_id;
			}
		}

		return $matched;
	}

	/**
	 * Records a sent campaign in the campaign history table.
	 *
	 * @since 3.2.0
	 * @param array $data {
	 *     @type string $name             Campaign name (for the admin's own reference).
	 *     @type string $subject          Email subject.
	 *     @type string $message          Email body (HTML).
	 *     @type string $audience         'members'|'non_members'|'levels'|'all'.
	 *     @type int[]  $level_ids        Level ids, when audience is 'levels'.
	 *     @type int    $recipient_count  Number of emails actually sent.
	 *     @type int    $actor_id         User id who sent the campaign.
	 * }
	 * @return int|false Campaign id on success, false on failure.
	 */
	public static function log_campaign( $data ) {
		global $wpdb;
		$table = Mfw_Role_Based_Db::campaigns_table();

		$row = array(
			'name'             => sanitize_text_field( $data['name'] ?? '' ),
			'subject'          => sanitize_text_field( $data['subject'] ?? '' ),
			'message'          => wp_kses_post( $data['message'] ?? '' ),
			'audience'         => sanitize_key( $data['audience'] ?? 'all' ),
			'level_ids'        => wp_json_encode( array_map( 'absint', (array) ( $data['level_ids'] ?? array() ) ) ),
			'recipient_count'  => absint( $data['recipient_count'] ?? 0 ),
			'actor_id'         => absint( $data['actor_id'] ?? 0 ),
			'status'           => sanitize_key( $data['status'] ?? 'sent' ),
			'send_mode'        => sanitize_key( $data['send_mode'] ?? 'now' ),
			'scheduled_at'     => ! empty( $data['scheduled_at'] ) ? $data['scheduled_at'] : null,
			'drip_level_id'    => ! empty( $data['drip_level_id'] ) ? absint( $data['drip_level_id'] ) : null,
			'drip_delay_days'  => isset( $data['drip_delay_days'] ) && '' !== $data['drip_delay_days'] ? absint( $data['drip_delay_days'] ) : null,
		);

		if ( ! empty( $data['id'] ) ) {
			$updated = $wpdb->update( $table, $row, array( 'id' => absint( $data['id'] ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
			return false === $updated ? false : absint( $data['id'] );
		}

		$inserted = $wpdb->insert( $table, $row ); // phpcs:ignore WordPress.DB.SlowDBQuery

		return $inserted ? (int) $wpdb->insert_id : false;
	}

	/**
	 * @since 3.2.0
	 * @param int $limit Max rows.
	 * @return array[] Rows, most recent first.
	 */
	public static function get_campaigns( $limit = 50 ) {
		global $wpdb;
		$table = Mfw_Role_Based_Db::campaigns_table();
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} ORDER BY created_at DESC LIMIT %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$limit
			),
			ARRAY_A
		);
	}

	/**
	 * @since 3.3.0
	 * @param int $campaign_id Campaign id.
	 * @return array|null
	 */
	public static function get_campaign( $campaign_id ) {
		global $wpdb;
		$table = Mfw_Role_Based_Db::campaigns_table();
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $campaign_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * @since 3.3.0
	 * @param string $status 'draft'|'scheduled'|'sent'.
	 * @return array[]
	 */
	public static function get_campaigns_by_status( $status ) {
		global $wpdb;
		$table = Mfw_Role_Based_Db::campaigns_table();
		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE status = %s", $status ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * @since 3.3.0
	 * @param int    $campaign_id Campaign id.
	 * @param int    $user_id     User id the event belongs to (0 if unknown, e.g. a click
	 *                            from an email client that stripped the recipient token).
	 * @param string $event_type  'open'|'click'.
	 * @param string $url         The destination URL, for 'click' events.
	 */
	public static function record_campaign_event( $campaign_id, $user_id, $event_type, $url = '' ) {
		global $wpdb;
		$table = Mfw_Role_Based_Db::campaign_events_table();
		$wpdb->insert( // phpcs:ignore WordPress.DB.SlowDBQuery
			$table,
			array(
				'campaign_id' => absint( $campaign_id ),
				'user_id'     => absint( $user_id ),
				'event_type'  => sanitize_key( $event_type ),
				'url'         => esc_url_raw( $url ),
			)
		);
	}

	/**
	 * @since 3.3.0
	 * @param int $campaign_id Campaign id.
	 * @return array{opens:int, unique_opens:int, clicks:int, unique_clicks:int}
	 */
	public static function get_campaign_stats( $campaign_id ) {
		global $wpdb;
		$table = Mfw_Role_Based_Db::campaign_events_table();

		$opens  = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE campaign_id = %d AND event_type = 'open'", $campaign_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$uopens = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT user_id) FROM {$table} WHERE campaign_id = %d AND event_type = 'open'", $campaign_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$clicks = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE campaign_id = %d AND event_type = 'click'", $campaign_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$uclicks = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT user_id) FROM {$table} WHERE campaign_id = %d AND event_type = 'click'", $campaign_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		return array(
			'opens'         => $opens,
			'unique_opens'  => $uopens,
			'clicks'        => $clicks,
			'unique_clicks' => $uclicks,
		);
	}

	/**
	 * Aggregate open/click totals across every campaign ever sent — for the Reports KPI
	 * tiles, where a single-campaign breakdown isn't the point.
	 *
	 * @since 3.3.0
	 * @return array{opens:int, clicks:int}
	 */
	public static function get_total_campaign_stats() {
		global $wpdb;
		$table = Mfw_Role_Based_Db::campaign_events_table();

		return array(
			'opens'  => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE event_type = 'open'" ), // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			'clicks' => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE event_type = 'click'" ), // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		);
	}

	/**
	 * @since 3.3.0
	 * @param int $user_id User id.
	 * @return bool
	 */
	public static function is_user_opted_out_of_campaigns( $user_id ) {
		return '1' === get_user_meta( $user_id, self::CAMPAIGN_OPT_OUT_META_KEY, true );
	}

	/**
	 * @since 3.3.0
	 * @param int  $user_id  User id.
	 * @param bool $opted_out True to opt out, false to opt back in.
	 */
	public static function set_user_campaign_opt_out( $user_id, $opted_out ) {
		if ( $opted_out ) {
			update_user_meta( $user_id, self::CAMPAIGN_OPT_OUT_META_KEY, '1' );
		} else {
			delete_user_meta( $user_id, self::CAMPAIGN_OPT_OUT_META_KEY );
		}
	}
}
