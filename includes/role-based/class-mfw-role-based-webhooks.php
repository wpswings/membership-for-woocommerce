<?php
/**
 * Outbound webhooks (Advanced Features Roadmap — "Integrations & extensibility"): fires a
 * non-blocking HTTP POST to admin-configured URL(s) when a role-membership event happens,
 * so external systems (CRM, Slack, Zapier/Make) can react without polling.
 *
 * Built entirely on the `do_action` hooks the repository/campaigns classes already fire —
 * no new event-detection logic needed.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Webhooks.
 */
class Mfw_Role_Based_Webhooks {

	const OPTION_KEY = 'mfw_role_based_webhooks';

	const EVENT_ASSIGNED       = 'level_assigned';
	const EVENT_REVOKED        = 'level_revoked';
	const EVENT_CAMPAIGN_SENT  = 'campaign_sent';

	/**
	 * @since 3.3.0
	 */
	public static function init() {
		add_action( 'mfw_role_based_after_assign_level', array( __CLASS__, 'on_assign' ), 10, 2 );
		add_action( 'mfw_role_based_after_revoke_level', array( __CLASS__, 'on_revoke' ), 10, 2 );
	}

	/**
	 * @since 3.3.0
	 * @return string[] Valid event keys.
	 */
	public static function events() {
		return array( self::EVENT_ASSIGNED, self::EVENT_REVOKED, self::EVENT_CAMPAIGN_SENT );
	}

	/**
	 * @since 3.3.0
	 * @return array<string,string> event key => webhook URL (only configured ones).
	 */
	public static function get_webhooks() {
		$saved = get_option( self::OPTION_KEY, array() );
		return is_array( $saved ) ? $saved : array();
	}

	/**
	 * @since 3.3.0
	 * @param array<string,string> $webhooks event key => webhook URL.
	 */
	public static function save_webhooks( $webhooks ) {
		$clean = array();
		foreach ( self::events() as $event ) {
			if ( ! empty( $webhooks[ $event ] ) && wp_http_validate_url( $webhooks[ $event ] ) ) {
				$clean[ $event ] = esc_url_raw( $webhooks[ $event ] );
			}
		}
		update_option( self::OPTION_KEY, $clean );
	}

	/**
	 * @since 3.3.0
	 * @param string $event   One of self::events().
	 * @param array  $payload Data to send as the webhook's JSON body.
	 */
	private static function fire( $event, $payload ) {

		$webhooks = self::get_webhooks();
		if ( empty( $webhooks[ $event ] ) ) {
			return;
		}

		$payload['event']     = $event;
		$payload['site']      = home_url( '/' );
		$payload['timestamp'] = current_time( 'mysql' );

		// Non-blocking: a slow/unreachable webhook endpoint must never delay the request
		// that triggered it (a member assignment, a campaign send).
		wp_remote_post(
			$webhooks[ $event ],
			array(
				'timeout'  => 5,
				'blocking' => false,
				'headers'  => array( 'Content-Type' => 'application/json' ),
				'body'     => wp_json_encode( $payload ),
			)
		);
	}

	/**
	 * @since 3.3.0
	 * @param int   $user_id User id.
	 * @param array $level   Level row.
	 */
	public static function on_assign( $user_id, $level ) {
		self::fire(
			self::EVENT_ASSIGNED,
			array(
				'user_id'   => $user_id,
				'level_id'  => (int) $level['id'],
				'level_name' => $level['name'],
			)
		);
	}

	/**
	 * @since 3.3.0
	 * @param int   $user_id User id.
	 * @param array $level   Level row.
	 */
	public static function on_revoke( $user_id, $level ) {
		self::fire(
			self::EVENT_REVOKED,
			array(
				'user_id'   => $user_id,
				'level_id'  => (int) $level['id'],
				'level_name' => $level['name'],
			)
		);
	}

	/**
	 * Called directly by Mfw_Role_Based_Campaigns after a send completes (campaigns don't
	 * fire a dedicated `do_action` today, so this is invoked, not hooked).
	 *
	 * @since 3.3.0
	 * @param int    $campaign_id Campaign id.
	 * @param string $subject     Campaign subject.
	 * @param int    $recipient_count Number of recipients.
	 */
	public static function on_campaign_sent( $campaign_id, $subject, $recipient_count ) {
		self::fire(
			self::EVENT_CAMPAIGN_SENT,
			array(
				'campaign_id'     => $campaign_id,
				'subject'         => $subject,
				'recipient_count' => $recipient_count,
			)
		);
	}
}
