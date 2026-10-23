<?php
/**
 * Standalone WordPress role management — "add roles separately" from a Level, mirroring
 * the reference "Members" plugin's dedicated Roles screen (list with Edit/Clone/Delete,
 * an Add/Edit form with a tabbed + searchable capability picker) rather than only being
 * able to create a role as a side effect of creating a Level.
 *
 * A role created/edited here is a plain WordPress role — nothing here is a "Level" (no
 * discount, expiry, price, etc.). Roles managed here can still be picked in the Level
 * form's "WordPress Role" dropdown afterward, same as any other role.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Roles_Manager.
 */
class Mfw_Role_Based_Roles_Manager {

	const NONCE_ACTION = 'mfw_role_based_admin';

	/**
	 * WordPress's five built-in roles — never deletable here, mirroring the reference
	 * plugin's own restriction against removing roles WordPress itself depends on.
	 */
	const PROTECTED_ROLES = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' );

	/**
	 * @since 3.4.0
	 */
	public static function init() {
		add_action( 'wp_ajax_mfw_role_based_save_role', array( __CLASS__, 'ajax_save_role' ) );
		add_action( 'wp_ajax_mfw_role_based_clone_role', array( __CLASS__, 'ajax_clone_role' ) );
		add_action( 'wp_ajax_mfw_role_based_delete_role', array( __CLASS__, 'ajax_delete_role' ) );
	}

	/**
	 * @since 3.4.0
	 * @return array<string,int> Role slug => number of users currently holding it.
	 */
	public static function get_role_user_counts() {
		$counts = count_users();
		return isset( $counts['avail_roles'] ) ? $counts['avail_roles'] : array();
	}

	/**
	 * @since 3.4.0
	 * @param string $slug Role slug.
	 * @return bool
	 */
	public static function is_role_deletable( $slug ) {
		if ( in_array( $slug, self::PROTECTED_ROLES, true ) ) {
			return false;
		}
		$counts = self::get_role_user_counts();
		return empty( $counts[ $slug ] );
	}

	/**
	 * @since 3.4.0
	 */
	public static function ajax_save_role() {

		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'membership-for-woocommerce' ) ), 403 );
		}

		$slug         = isset( $_POST['slug'] ) ? sanitize_key( wp_unslash( $_POST['slug'] ) ) : '';
		$name         = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$capabilities = isset( $_POST['capabilities'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['capabilities'] ) ) : array();

		if ( ! $slug || ! $name ) {
			wp_send_json_error( array( 'message' => __( 'Please provide a role name.', 'membership-for-woocommerce' ) ) );
		}

		$caps = array( 'read' => true );
		foreach ( $capabilities as $cap ) {
			$caps[ $cap ] = true;
		}

		$role = get_role( $slug );

		if ( $role ) {
			// Editing: bring the role's capabilities exactly in line with what's checked,
			// scoped to the same known-capability list as Level editing — see
			// Mfw_Role_Based_Repository::sync_role_capabilities() for why that scoping matters.
			Mfw_Role_Based_Repository::sync_role_capabilities( $slug, $capabilities, Mfw_Role_Based_Admin::all_known_capabilities() );
		} else {
			add_role( $slug, $name, $caps );
		}

		// Role display names aren't editable in place by WordPress's role API — remove and
		// re-add is the standard way to rename one without touching its capabilities, which
		// sync_role_capabilities() already just handled above.
		global $wp_roles;
		if ( $wp_roles && isset( $wp_roles->roles[ $slug ] ) && $wp_roles->roles[ $slug ]['name'] !== $name ) {
			$wp_roles->roles[ $slug ]['name']          = $name;
			$wp_roles->role_names[ $slug ]             = $name;
			update_option( $wp_roles->role_key, $wp_roles->roles );
		}

		wp_send_json_success( array( 'slug' => $slug ) );
	}

	/**
	 * @since 3.4.0
	 */
	public static function ajax_clone_role() {

		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'membership-for-woocommerce' ) ), 403 );
		}

		$source_slug = isset( $_POST['slug'] ) ? sanitize_key( wp_unslash( $_POST['slug'] ) ) : '';
		$source_role = get_role( $source_slug );

		if ( ! $source_role ) {
			wp_send_json_error( array( 'message' => __( 'That role no longer exists.', 'membership-for-woocommerce' ) ) );
		}

		global $wp_roles;
		$source_name = isset( $wp_roles->role_names[ $source_slug ] ) ? $wp_roles->role_names[ $source_slug ] : $source_slug;

		$new_slug = $source_slug . '_copy';
		$suffix   = 1;
		while ( get_role( $new_slug ) ) {
			++$suffix;
			$new_slug = $source_slug . '_copy_' . $suffix;
		}

		/* translators: %s: original role name. */
		$new_name = sprintf( __( '%s (Copy)', 'membership-for-woocommerce' ), $source_name );

		add_role( $new_slug, $new_name, $source_role->capabilities );

		wp_send_json_success( array( 'slug' => $new_slug ) );
	}

	/**
	 * @since 3.4.0
	 */
	public static function ajax_delete_role() {

		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'membership-for-woocommerce' ) ), 403 );
		}

		$slug = isset( $_POST['slug'] ) ? sanitize_key( wp_unslash( $_POST['slug'] ) ) : '';

		if ( ! self::is_role_deletable( $slug ) ) {
			wp_send_json_error( array( 'message' => __( 'This role can\'t be deleted — it\'s either a default WordPress role or currently held by at least one user.', 'membership-for-woocommerce' ) ) );
		}

		remove_role( $slug );

		wp_send_json_success();
	}
}
