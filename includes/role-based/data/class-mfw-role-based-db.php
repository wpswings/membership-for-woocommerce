<?php
/**
 * DB schema for the role-based membership module (WPS-7879).
 *
 * Fully independent of the purchase-based flow's data model (which uses CPTs + meta) —
 * the role-based module has its own tables, created/upgraded via dbDelta and versioned
 * with a dedicated option so the schema can evolve safely.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based/data
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Db.
 */
class Mfw_Role_Based_Db {

	const DB_VERSION_OPTION = 'mfw_role_based_db_version';

	const DB_VERSION = '1.9.0';

	/**
	 * @since 3.1.3
	 * @return string
	 */
	public static function levels_table() {
		global $wpdb;
		return $wpdb->prefix . 'mfw_role_membership_levels';
	}

	/**
	 * @since 3.1.3
	 * @return string
	 */
	public static function restrictions_table() {
		global $wpdb;
		return $wpdb->prefix . 'mfw_role_membership_restrictions';
	}

	/**
	 * @since 3.1.3
	 * @return string
	 */
	public static function user_log_table() {
		global $wpdb;
		return $wpdb->prefix . 'mfw_role_membership_user_log';
	}

	/**
	 * @since 3.1.3
	 * @return string
	 */
	public static function campaigns_table() {
		global $wpdb;
		return $wpdb->prefix . 'mfw_role_membership_campaigns';
	}

	/**
	 * @since 3.3.0
	 * @return string
	 */
	public static function campaign_events_table() {
		global $wpdb;
		return $wpdb->prefix . 'mfw_role_membership_campaign_events';
	}

	/**
	 * Creates/upgrades the role-based module's tables if the stored schema version
	 * is behind DB_VERSION. Safe to call on every admin_init — dbDelta() itself is
	 * idempotent, and the version check keeps this cheap in the common case.
	 *
	 * @since 3.1.3
	 */
	public static function maybe_install() {

		$installed_version = get_option( self::DB_VERSION_OPTION );

		if ( $installed_version === self::DB_VERSION ) {
			return;
		}

		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		$levels_table          = self::levels_table();
		$restrictions_table    = self::restrictions_table();
		$user_log_table        = self::user_log_table();
		$campaigns_table       = self::campaigns_table();
		$campaign_events_table = self::campaign_events_table();

		// Column is named level_rank, not rank: RANK became a reserved word in MySQL 8.0.2+
		// (window functions), and dbDelta/$wpdb never backtick-quote raw ORDER BY clauses,
		// so an unquoted `rank` column silently breaks table creation and every query on it.
		$sql = "CREATE TABLE {$levels_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(191) NOT NULL,
			description LONGTEXT NULL,
			wp_role VARCHAR(191) NOT NULL,
			capabilities LONGTEXT NULL,
			level_rank INT UNSIGNED NOT NULL DEFAULT 0,
			discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
			discount_category_ids LONGTEXT NULL,
			expiry_days INT UNSIGNED NOT NULL DEFAULT 0,
			self_signup_enabled TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
			price DECIMAL(10,2) NOT NULL DEFAULT 0,
			upgrade_spend_threshold DECIMAL(10,2) NOT NULL DEFAULT 0,
			tier_ladder TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
			wc_product_id BIGINT UNSIGNED NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'active',
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY wp_role (wp_role),
			KEY status (status)
		) {$charset_collate};

		CREATE TABLE {$restrictions_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			level_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			object_type VARCHAR(20) NOT NULL,
			object_id BIGINT UNSIGNED NOT NULL,
			min_rank INT UNSIGNED NULL,
			drip_days INT UNSIGNED NOT NULL DEFAULT 0,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY level_id (level_id),
			KEY object_lookup (object_type, object_id)
		) {$charset_collate};

		CREATE TABLE {$user_log_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT UNSIGNED NOT NULL,
			level_id BIGINT UNSIGNED NOT NULL,
			action VARCHAR(20) NOT NULL,
			actor_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY level_id (level_id)
		) {$charset_collate};

		CREATE TABLE {$campaigns_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(191) NOT NULL,
			subject VARCHAR(255) NOT NULL,
			message LONGTEXT NULL,
			audience VARCHAR(20) NOT NULL,
			level_ids LONGTEXT NULL,
			recipient_count INT UNSIGNED NOT NULL DEFAULT 0,
			actor_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			status VARCHAR(20) NOT NULL DEFAULT 'sent',
			send_mode VARCHAR(20) NOT NULL DEFAULT 'now',
			scheduled_at DATETIME NULL,
			drip_level_id BIGINT UNSIGNED NULL,
			drip_delay_days INT UNSIGNED NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY audience (audience),
			KEY status (status)
		) {$charset_collate};

		CREATE TABLE {$campaign_events_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			campaign_id BIGINT UNSIGNED NOT NULL,
			user_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			event_type VARCHAR(20) NOT NULL,
			url TEXT NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY campaign_id (campaign_id),
			KEY event_type (event_type)
		) {$charset_collate};";

		dbDelta( $sql );

		// 1.9.0 made tier-ladder membership an explicit flag instead of "threshold > 0", so a
		// ₹0 base tier can be part of the ladder. Levels that were on the ladder under the old
		// rule stay on it.
		if ( $installed_version && version_compare( $installed_version, '1.9.0', '<' ) ) {
			$wpdb->query( "UPDATE {$levels_table} SET tier_ladder = 1 WHERE upgrade_spend_threshold > 0" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}

		update_option( self::DB_VERSION_OPTION, self::DB_VERSION );
	}

	/**
	 * Drops the role-based module's own tables. Only ever called from an explicit,
	 * opt-in uninstall path — never from mode switching (switching is non-destructive).
	 *
	 * @since 3.1.3
	 */
	public static function drop_tables() {
		global $wpdb;
		$wpdb->query( 'DROP TABLE IF EXISTS ' . self::campaign_events_table() ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->query( 'DROP TABLE IF EXISTS ' . self::campaigns_table() ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->query( 'DROP TABLE IF EXISTS ' . self::user_log_table() ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->query( 'DROP TABLE IF EXISTS ' . self::restrictions_table() ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->query( 'DROP TABLE IF EXISTS ' . self::levels_table() ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		delete_option( self::DB_VERSION_OPTION );
	}
}
