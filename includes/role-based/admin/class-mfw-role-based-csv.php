<?php
/**
 * CSV export/import for the role-based module (Advanced Features Roadmap — "Admin &
 * reporting"): export members-with-levels and campaign history, and bulk-import
 * member↔level assignments. Distinct from the existing JSON level-definition
 * export/import in Mfw_Role_Based_Admin, which is about level *definitions*, not
 * who-holds-what.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Csv.
 */
class Mfw_Role_Based_Csv {

	/**
	 * @since 3.3.0
	 */
	public static function init() {
		add_action( 'admin_post_mfw_role_based_export_members_csv', array( __CLASS__, 'export_members' ) );
		add_action( 'admin_post_mfw_role_based_export_campaigns_csv', array( __CLASS__, 'export_campaigns' ) );
		add_action( 'admin_post_mfw_role_based_import_members_csv', array( __CLASS__, 'import_members' ) );
	}

	/**
	 * @since 3.3.0
	 * @param string $filename Download filename.
	 */
	private static function start_download( $filename ) {
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $filename ) . '"' );
	}

	/**
	 * @since 3.3.0
	 */
	public static function export_members() {

		if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'mfw_role_based_admin', 'mfw_csv_nonce' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'membership-for-woocommerce' ) );
		}

		self::start_download( 'role-membership-members.csv' );

		$out = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		fputcsv( $out, array( 'user_id', 'user_login', 'user_email', 'level_name', 'assigned_at', 'expires_at' ) );

		foreach ( Mfw_Role_Based_Repository::get_member_user_ids() as $user_id ) {
			$user = get_user_by( 'id', $user_id );
			if ( ! $user ) {
				continue;
			}
			foreach ( Mfw_Role_Based_Repository::get_user_levels( $user_id ) as $level ) {
				$assignment = Mfw_Role_Based_Repository::get_user_level_assignment( $user_id, $level['id'] );
				fputcsv(
					$out,
					array(
						$user_id,
						$user->user_login,
						$user->user_email,
						$level['name'],
						$assignment['assigned_at'] ?? '',
						$assignment['expires_at'] ?? '',
					)
				);
			}
		}

		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		exit;
	}

	/**
	 * @since 3.3.0
	 */
	public static function export_campaigns() {

		if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'mfw_role_based_admin', 'mfw_csv_nonce' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'membership-for-woocommerce' ) );
		}

		self::start_download( 'role-membership-campaigns.csv' );

		$out = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		fputcsv( $out, array( 'name', 'subject', 'audience', 'status', 'recipient_count', 'created_at' ) );

		foreach ( Mfw_Role_Based_Repository::get_campaigns( 1000 ) as $campaign ) {
			fputcsv(
				$out,
				array(
					$campaign['name'],
					$campaign['subject'],
					$campaign['audience'],
					$campaign['status'],
					$campaign['recipient_count'],
					$campaign['created_at'],
				)
			);
		}

		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		exit;
	}

	/**
	 * Bulk-imports member↔level assignments from a CSV with columns `user_email` (or
	 * `user_id`) and `level_name` (or `level_id`) — accepts either so an admin exporting
	 * from export_members() above can re-import it, or hand-build a simpler sheet.
	 *
	 * @since 3.3.0
	 */
	public static function import_members() {

		if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'mfw_role_based_admin', 'mfw_csv_import_nonce' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'membership-for-woocommerce' ) );
		}

		$redirect = admin_url( 'admin.php?page=' . Mfw_Role_Based_Admin::PAGE_SLUG . '#levels' );

		if ( empty( $_FILES['mfw_members_csv']['tmp_name'] ) ) {
			wp_safe_redirect( add_query_arg( 'mfw_csv_import', 'missing', $redirect ) );
			exit;
		}

		$handle = fopen( sanitize_text_field( wp_unslash( $_FILES['mfw_members_csv']['tmp_name'] ) ), 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		if ( ! $handle ) {
			wp_safe_redirect( add_query_arg( 'mfw_csv_import', 'invalid', $redirect ) );
			exit;
		}

		$header    = fgetcsv( $handle );
		$processed = 0;

		if ( is_array( $header ) ) {

			$header = array_map( 'strtolower', array_map( 'trim', $header ) );

			$levels_by_name = array();
			foreach ( Mfw_Role_Based_Repository::get_levels() as $level ) {
				$levels_by_name[ strtolower( $level['name'] ) ] = (int) $level['id'];
			}

			while ( ( $row = fgetcsv( $handle ) ) !== false ) { // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments

				$row_data = array_combine( $header, array_pad( $row, count( $header ), '' ) );
				if ( ! $row_data ) {
					continue;
				}

				$user_id = 0;
				if ( ! empty( $row_data['user_id'] ) ) {
					$user_id = absint( $row_data['user_id'] );
				} elseif ( ! empty( $row_data['user_email'] ) ) {
					$user    = get_user_by( 'email', sanitize_email( $row_data['user_email'] ) );
					$user_id = $user ? $user->ID : 0;
				}

				$level_id = 0;
				if ( ! empty( $row_data['level_id'] ) ) {
					$level_id = absint( $row_data['level_id'] );
				} elseif ( ! empty( $row_data['level_name'] ) ) {
					$level_id = $levels_by_name[ strtolower( trim( $row_data['level_name'] ) ) ] ?? 0;
				}

				if ( $user_id && $level_id && Mfw_Role_Based_Repository::assign_level_to_user( $user_id, $level_id, get_current_user_id() ) ) {
					++$processed;
				}
			}
		}

		fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose

		wp_safe_redirect( add_query_arg( 'mfw_csv_import', $processed, $redirect ) );
		exit;
	}
}
