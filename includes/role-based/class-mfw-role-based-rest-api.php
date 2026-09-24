<?php
/**
 * REST API for the role-based module (Advanced Features Roadmap — "Integrations &
 * extensibility"): list levels, and read/assign/revoke a member's levels — for headless
 * frontends, mobile apps, or external automation, without going through wp-admin.
 *
 * Auth relies entirely on WordPress core's own REST authentication (cookie+nonce for
 * logged-in browser sessions, or Application Passwords for external clients) plus a
 * capability check per route — no custom authentication scheme is invented here.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Rest_Api.
 */
class Mfw_Role_Based_Rest_Api {

	const NAMESPACE_KEY = 'mfw-role-based/v1';

	/**
	 * @since 3.3.0
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	/**
	 * @since 3.3.0
	 */
	public static function register_routes() {

		register_rest_route(
			self::NAMESPACE_KEY,
			'/levels',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'get_levels' ),
				'permission_callback' => array( __CLASS__, 'can_manage' ),
			)
		);

		register_rest_route(
			self::NAMESPACE_KEY,
			'/members/(?P<user_id>\d+)',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'get_member' ),
				'permission_callback' => array( __CLASS__, 'can_manage' ),
				'args'                => array(
					'user_id' => array( 'validate_callback' => 'is_numeric' ),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE_KEY,
			'/members/(?P<user_id>\d+)/assign',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( __CLASS__, 'assign' ),
				'permission_callback' => array( __CLASS__, 'can_manage' ),
				'args'                => array(
					'user_id'  => array( 'validate_callback' => 'is_numeric' ),
					'level_id' => array( 'required' => true, 'validate_callback' => 'is_numeric' ),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE_KEY,
			'/members/(?P<user_id>\d+)/revoke',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( __CLASS__, 'revoke' ),
				'permission_callback' => array( __CLASS__, 'can_manage' ),
				'args'                => array(
					'user_id'  => array( 'validate_callback' => 'is_numeric' ),
					'level_id' => array( 'required' => true, 'validate_callback' => 'is_numeric' ),
				),
			)
		);
	}

	/**
	 * @since 3.3.0
	 * @return bool
	 */
	public static function can_manage() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * @since 3.3.0
	 * @return WP_REST_Response
	 */
	public static function get_levels() {

		$levels = array_map(
			function ( $level ) {
				return array(
					'id'               => (int) $level['id'],
					'name'             => $level['name'],
					'wp_role'          => $level['wp_role'],
					'rank'             => (int) $level['level_rank'],
					'discount_percent' => (float) $level['discount_percent'],
					'expiry_days'      => (int) $level['expiry_days'],
					'status'           => $level['status'],
				);
			},
			Mfw_Role_Based_Repository::get_levels()
		);

		return rest_ensure_response( $levels );
	}

	/**
	 * @since 3.3.0
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function get_member( $request ) {

		$user_id = absint( $request['user_id'] );
		$user    = get_user_by( 'id', $user_id );

		if ( ! $user ) {
			return new WP_Error( 'mfw_member_not_found', __( 'No such user.', 'membership-for-woocommerce' ), array( 'status' => 404 ) );
		}

		$levels = array_map(
			function ( $level ) use ( $user_id ) {
				$assignment = Mfw_Role_Based_Repository::get_user_level_assignment( $user_id, $level['id'] );
				return array(
					'id'          => (int) $level['id'],
					'name'        => $level['name'],
					'assigned_at' => $assignment['assigned_at'] ?? null,
					'expires_at'  => $assignment['expires_at'] ?? null,
				);
			},
			Mfw_Role_Based_Repository::get_user_levels( $user_id )
		);

		return rest_ensure_response(
			array(
				'user_id' => $user_id,
				'levels'  => $levels,
			)
		);
	}

	/**
	 * @since 3.3.0
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function assign( $request ) {

		$user_id  = absint( $request['user_id'] );
		$level_id = absint( $request->get_param( 'level_id' ) );

		if ( ! Mfw_Role_Based_Repository::assign_level_to_user( $user_id, $level_id, get_current_user_id() ) ) {
			return new WP_Error( 'mfw_assign_failed', __( 'Could not assign that level to that user.', 'membership-for-woocommerce' ), array( 'status' => 400 ) );
		}

		return rest_ensure_response( array( 'assigned' => true ) );
	}

	/**
	 * @since 3.3.0
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function revoke( $request ) {

		$user_id  = absint( $request['user_id'] );
		$level_id = absint( $request->get_param( 'level_id' ) );

		if ( ! Mfw_Role_Based_Repository::revoke_level_from_user( $user_id, $level_id, get_current_user_id() ) ) {
			return new WP_Error( 'mfw_revoke_failed', __( 'Could not revoke that level from that user.', 'membership-for-woocommerce' ), array( 'status' => 400 ) );
		}

		return rest_ensure_response( array( 'revoked' => true ) );
	}
}
