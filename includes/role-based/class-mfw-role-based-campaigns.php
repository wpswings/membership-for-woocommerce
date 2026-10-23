<?php
/**
 * Email campaigns for the role-based module: compose a message and send it to Members,
 * Non-Members, specific levels, or everyone — independent of the assign/revoke notification
 * emails in Mfw_Role_Based_Notifications, which are per-event rather than admin-composed.
 *
 * Sending is synchronous (a plain wp_mail() loop, matching this plugin's existing
 * notification style — no queue/cron infrastructure exists anywhere in this codebase). This
 * is a known scale limitation for very large recipient lists — see the architecture doc.
 *
 * Advanced Features Roadmap additions: branded HTML templates, an unsubscribe/preference
 * link on every send, scheduled ("send later") and drip (triggered by a level assignment)
 * sending via WP-Cron, and open/click tracking.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Campaigns.
 */
class Mfw_Role_Based_Campaigns {

	const AUDIENCE_MEMBERS     = 'members';
	const AUDIENCE_NON_MEMBERS = 'non_members';
	const AUDIENCE_LEVELS      = 'levels';
	const AUDIENCE_ALL         = 'all';

	const SEND_MODE_NOW       = 'now';
	const SEND_MODE_SCHEDULED = 'scheduled';
	const SEND_MODE_DRIP      = 'drip';

	const SCHEDULED_SEND_CRON_HOOK = 'mfw_role_based_send_scheduled_campaign';

	const DRIP_SEND_CRON_HOOK = 'mfw_role_based_send_drip_campaign';

	const UNSUBSCRIBE_QUERY_VAR = 'mfw_campaign_unsubscribe';

	/**
	 * @since 3.1.3
	 */
	public static function init() {
		add_action( self::SCHEDULED_SEND_CRON_HOOK, array( __CLASS__, 'send_scheduled_campaign' ) );
		add_action( self::DRIP_SEND_CRON_HOOK, array( __CLASS__, 'send_drip_campaign' ) );
		add_action( 'mfw_role_based_after_assign_level', array( __CLASS__, 'maybe_schedule_drip_campaigns' ), 10, 2 );
		add_action( 'init', array( __CLASS__, 'maybe_handle_unsubscribe' ) );
	}

	/**
	 * @since 3.1.3
	 * @return string[] Valid audience keys.
	 */
	public static function audiences() {
		return array( self::AUDIENCE_MEMBERS, self::AUDIENCE_NON_MEMBERS, self::AUDIENCE_LEVELS, self::AUDIENCE_ALL );
	}

	/**
	 * @since 3.1.3
	 * @param string $audience  One of self::audiences().
	 * @param int[]  $level_ids Level ids, only used when $audience is AUDIENCE_LEVELS.
	 * @return int[] User ids.
	 */
	public static function resolve_audience_user_ids( $audience, $level_ids = array() ) {

		switch ( $audience ) {
			case self::AUDIENCE_MEMBERS:
				return Mfw_Role_Based_Repository::get_member_user_ids();

			case self::AUDIENCE_NON_MEMBERS:
				return Mfw_Role_Based_Repository::get_non_member_user_ids();

			case self::AUDIENCE_LEVELS:
				return Mfw_Role_Based_Repository::get_user_ids_for_levels( $level_ids );

			case self::AUDIENCE_ALL:
			default:
				return array_map( 'intval', get_users( array( 'fields' => 'ID' ) ) );
		}
	}

	/**
	 * Sends (or schedules) a composed campaign. `now` sends immediately and logs it as
	 * 'sent'; `scheduled` logs it as 'scheduled' and books a single WP-Cron event for the
	 * requested time; `drip` logs it as 'scheduled' with no immediate send — it fires later,
	 * per-recipient, from maybe_schedule_drip_campaigns() whenever someone is assigned the
	 * trigger level.
	 *
	 * @since 3.1.3
	 * @param array $args {
	 *     @type string $name            Campaign name (admin's own reference).
	 *     @type string $subject         Email subject.
	 *     @type string $message         Email body (HTML).
	 *     @type string $audience        One of self::audiences().
	 *     @type int[]  $level_ids       Level ids, only used when $audience is AUDIENCE_LEVELS.
	 *     @type int    $actor_id        User id composing the campaign.
	 *     @type string $send_mode       One of self::SEND_MODE_*.
	 *     @type string $scheduled_at    MySQL datetime, only used when send_mode is 'scheduled'.
	 *     @type int    $drip_level_id   Trigger level id, only used when send_mode is 'drip'.
	 *     @type int    $drip_delay_days Days after assignment to send, only used when send_mode is 'drip'.
	 * }
	 * @return int|false Recipient count for an immediate send, the campaign id for a
	 *                    scheduled/drip campaign, or false on failure.
	 */
	public static function submit( $args ) {

		$send_mode = in_array( $args['send_mode'] ?? self::SEND_MODE_NOW, array( self::SEND_MODE_NOW, self::SEND_MODE_SCHEDULED, self::SEND_MODE_DRIP ), true )
			? $args['send_mode']
			: self::SEND_MODE_NOW;

		if ( self::SEND_MODE_NOW === $send_mode ) {
			return self::send( $args['name'], $args['subject'], $args['message'], $args['audience'], $args['level_ids'] ?? array(), $args['actor_id'] ?? 0 );
		}

		$campaign_id = Mfw_Role_Based_Repository::log_campaign(
			array(
				'name'            => $args['name'],
				'subject'         => $args['subject'],
				'message'         => $args['message'],
				'audience'        => $args['audience'],
				'level_ids'       => $args['level_ids'] ?? array(),
				'recipient_count' => 0,
				'actor_id'        => $args['actor_id'] ?? 0,
				'status'          => 'scheduled',
				'send_mode'       => $send_mode,
				'scheduled_at'    => self::SEND_MODE_SCHEDULED === $send_mode ? ( $args['scheduled_at'] ?? null ) : null,
				'drip_level_id'   => self::SEND_MODE_DRIP === $send_mode ? ( $args['drip_level_id'] ?? null ) : null,
				'drip_delay_days' => self::SEND_MODE_DRIP === $send_mode ? ( $args['drip_delay_days'] ?? 0 ) : null,
			)
		);

		if ( $campaign_id && self::SEND_MODE_SCHEDULED === $send_mode && ! empty( $args['scheduled_at'] ) ) {
			$timestamp = strtotime( $args['scheduled_at'] );
			if ( $timestamp ) {
				wp_schedule_single_event( $timestamp, self::SCHEDULED_SEND_CRON_HOOK, array( $campaign_id ) );
			}
		}

		return $campaign_id;
	}

	/**
	 * WP-Cron callback for a 'scheduled' campaign whose time has come.
	 *
	 * @since 3.3.0
	 * @param int $campaign_id Campaign id.
	 */
	public static function send_scheduled_campaign( $campaign_id ) {

		$campaign = Mfw_Role_Based_Repository::get_campaign( $campaign_id );
		if ( ! $campaign || 'scheduled' !== $campaign['status'] ) {
			return;
		}

		$result = self::deliver(
			$campaign['name'],
			$campaign['subject'],
			$campaign['message'],
			$campaign['audience'],
			json_decode( $campaign['level_ids'], true ) ?: array(), // phpcs:ignore WordPress.PHP.DisallowShortTernary
			$campaign_id // Reuse this campaign's own row — tracking links must point at it.
		);

		Mfw_Role_Based_Repository::log_campaign(
			array(
				'id'              => $campaign_id,
				'name'            => $campaign['name'],
				'subject'         => $campaign['subject'],
				'message'         => $campaign['message'],
				'audience'        => $campaign['audience'],
				'level_ids'       => json_decode( $campaign['level_ids'], true ) ?: array(), // phpcs:ignore WordPress.PHP.DisallowShortTernary
				'recipient_count' => $result['sent'],
				'actor_id'        => $campaign['actor_id'],
				'status'          => 'sent',
				'send_mode'       => $campaign['send_mode'],
			)
		);

		Mfw_Role_Based_Webhooks::on_campaign_sent( $campaign_id, $campaign['subject'], $result['sent'] );
	}

	/**
	 * Whenever a user is assigned a level, checks for any active 'drip' campaign whose
	 * trigger level matches, and books a single delayed send just for that one user.
	 *
	 * @since 3.3.0
	 * @param int   $user_id User id.
	 * @param array $level   The level row just assigned.
	 */
	public static function maybe_schedule_drip_campaigns( $user_id, $level ) {

		foreach ( Mfw_Role_Based_Repository::get_campaigns_by_status( 'scheduled' ) as $campaign ) {

			if ( self::SEND_MODE_DRIP !== $campaign['send_mode'] || (int) $campaign['drip_level_id'] !== (int) $level['id'] ) {
				continue;
			}

			$delay_days = max( 0, (int) $campaign['drip_delay_days'] );
			$timestamp  = strtotime( "+{$delay_days} days", current_time( 'timestamp' ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested

			wp_schedule_single_event( $timestamp, self::DRIP_SEND_CRON_HOOK, array( $campaign['id'], $user_id ) );
		}
	}

	/**
	 * WP-Cron callback: sends one drip campaign to one recipient.
	 *
	 * @since 3.3.0
	 * @param int $campaign_id Campaign id.
	 * @param int $user_id     User id.
	 */
	public static function send_drip_campaign( $campaign_id, $user_id ) {

		$campaign = Mfw_Role_Based_Repository::get_campaign( $campaign_id );
		$user     = get_user_by( 'id', $user_id );

		if ( ! $campaign || ! $user || Mfw_Role_Based_Repository::is_user_opted_out_of_campaigns( $user_id ) ) {
			return;
		}

		self::send_to_user( $user, $campaign_id, $campaign['subject'], $campaign['message'] );

		Mfw_Role_Based_Repository::log_campaign(
			array(
				'id'              => $campaign_id,
				'name'            => $campaign['name'],
				'subject'         => $campaign['subject'],
				'message'         => $campaign['message'],
				'audience'        => $campaign['audience'],
				'level_ids'       => json_decode( $campaign['level_ids'], true ) ?: array(), // phpcs:ignore WordPress.PHP.DisallowShortTernary
				'recipient_count' => (int) $campaign['recipient_count'] + 1,
				'actor_id'        => $campaign['actor_id'],
				'status'          => 'sent', // A drip campaign shows as 'sent' once at least one recipient has received it.
				'send_mode'       => $campaign['send_mode'],
				'drip_level_id'   => $campaign['drip_level_id'],
				'drip_delay_days' => $campaign['drip_delay_days'],
			)
		);

		Mfw_Role_Based_Webhooks::on_campaign_sent( $campaign_id, $campaign['subject'], 1 );
	}

	/**
	 * Sends the campaign immediately to its whole audience and logs it as 'sent'. Message
	 * supports {display_name}, {user_email}, {site_name} placeholders, replaced per-recipient.
	 * Skips any user who has opted out of campaign emails.
	 *
	 * @since 3.1.3
	 * @param string $name      Campaign name (admin's own reference, not shown to recipients).
	 * @param string $subject   Email subject.
	 * @param string $message   Email body (HTML).
	 * @param string $audience  One of self::audiences().
	 * @param int[]  $level_ids Level ids, only used when $audience is AUDIENCE_LEVELS.
	 * @param int    $actor_id  User id sending the campaign.
	 * @return int Number of emails actually sent.
	 */
	public static function send( $name, $subject, $message, $audience, $level_ids, $actor_id ) {

		$result = self::deliver( $name, $subject, $message, $audience, $level_ids );

		Mfw_Role_Based_Repository::log_campaign(
			array(
				'id'              => $result['campaign_id'],
				'name'            => $name,
				'subject'         => $subject,
				'message'         => $message,
				'audience'        => $audience,
				'level_ids'       => $level_ids,
				'recipient_count' => $result['sent'],
				'actor_id'        => $actor_id,
				'status'          => 'sent',
				'send_mode'       => self::SEND_MODE_NOW,
			)
		);

		Mfw_Role_Based_Webhooks::on_campaign_sent( $result['campaign_id'], $subject, $result['sent'] );

		return $result['sent'];
	}

	/**
	 * The actual send loop, shared by immediate and scheduled sends (logging differs
	 * between the two call sites, so it isn't done here).
	 *
	 * @since 3.3.0
	 * @param string $name      Campaign name.
	 * @param string $subject   Email subject.
	 * @param string $message   Email body (HTML).
	 * @param string   $audience    One of self::audiences().
	 * @param int[]    $level_ids   Level ids, only used when $audience is AUDIENCE_LEVELS.
	 * @param int|null $campaign_id An existing campaign row's id to attribute tracking/
	 *                              unsubscribe links to (a scheduled campaign already has
	 *                              one). Null creates a fresh placeholder row — used only by
	 *                              an immediate send, which has no row yet at this point.
	 * @return array{sent:int, campaign_id:int} Recipients actually emailed, and the
	 *                                          campaign id tracking links were attributed to.
	 */
	private static function deliver( $name, $subject, $message, $audience, $level_ids, $campaign_id = null ) {

		$user_ids = self::resolve_audience_user_ids( $audience, $level_ids );
		$sent     = 0;

		if ( null === $campaign_id ) {
			// A campaign id is needed for tracking/unsubscribe links, but doesn't exist yet
			// for an immediate send until after delivery — log a placeholder row first so
			// events can reference it; the caller updates it with the final result.
			$campaign_id = Mfw_Role_Based_Repository::log_campaign(
				array(
					'name'      => $name,
					'subject'   => $subject,
					'message'   => $message,
					'audience'  => $audience,
					'level_ids' => $level_ids,
					'status'    => 'sending',
					'send_mode' => self::SEND_MODE_NOW,
				)
			);
		}

		foreach ( $user_ids as $user_id ) {

			$user = get_user_by( 'id', $user_id );
			if ( ! $user || ! is_email( $user->user_email ) || Mfw_Role_Based_Repository::is_user_opted_out_of_campaigns( $user_id ) ) {
				continue;
			}

			if ( self::send_to_user( $user, $campaign_id, $subject, $message ) ) {
				++$sent;
			}
		}

		return array(
			'sent'        => $sent,
			'campaign_id' => $campaign_id,
		);
	}

	/**
	 * @since 3.3.0
	 * @param WP_User $user        Recipient.
	 * @param int     $campaign_id Campaign id (for tracking/unsubscribe), 0 if not yet known.
	 * @param string  $subject     Email subject.
	 * @param string  $message     Raw message with placeholders, before templating.
	 * @return bool
	 */
	private static function send_to_user( $user, $campaign_id, $subject, $message ) {

		$personalized = strtr(
			$message,
			array(
				'{display_name}' => $user->display_name,
				'{user_email}'   => $user->user_email,
				'{site_name}'    => get_bloginfo( 'name' ),
			)
		);

		if ( class_exists( 'Mfw_Role_Based_Campaign_Tracking' ) ) {
			$personalized = Mfw_Role_Based_Campaign_Tracking::wrap_links( $personalized, $campaign_id, $user->ID );
		}

		$body = class_exists( 'Mfw_Role_Based_Email_Template' )
			? Mfw_Role_Based_Email_Template::wrap( $subject, $personalized, self::unsubscribe_link_html( $user->ID ) )
			: $personalized;

		if ( class_exists( 'Mfw_Role_Based_Campaign_Tracking' ) ) {
			$body = Mfw_Role_Based_Campaign_Tracking::append_open_pixel( $body, $campaign_id, $user->ID );
		}

		add_filter( 'wp_mail_content_type', array( 'Mfw_Role_Based_Email_Template', 'html_content_type' ) );
		$result = wp_mail( $user->user_email, $subject, $body );
		remove_filter( 'wp_mail_content_type', array( 'Mfw_Role_Based_Email_Template', 'html_content_type' ) );

		return $result;
	}

	/**
	 * @since 3.3.0
	 * @param int $user_id User id.
	 * @return string A ready-to-embed "unsubscribe" link.
	 */
	private static function unsubscribe_link_html( $user_id ) {
		$url = self::unsubscribe_url( $user_id );
		return '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Unsubscribe from these emails', 'membership-for-woocommerce' ) . '</a>';
	}

	/**
	 * @since 3.3.0
	 * @param int $user_id User id.
	 * @return string
	 */
	public static function unsubscribe_url( $user_id ) {
		return add_query_arg(
			array(
				self::UNSUBSCRIBE_QUERY_VAR => 1,
				'u'                         => $user_id,
				't'                         => self::unsubscribe_token( $user_id ),
			),
			home_url( '/' )
		);
	}

	/**
	 * A simple tamper-resistant token (not a nonce — nonces expire and are tied to a
	 * logged-in session, neither of which fits an emailed link opened days later by a
	 * logged-out recipient) so an unsubscribe link can't be used to opt out an arbitrary
	 * other user by guessing their id.
	 *
	 * @since 3.3.0
	 * @param int $user_id User id.
	 * @return string
	 */
	private static function unsubscribe_token( $user_id ) {
		return substr( wp_hash( 'mfw_campaign_unsubscribe|' . $user_id . '|' . wp_salt() ), 0, 20 );
	}

	/**
	 * @since 3.3.0
	 */
	public static function maybe_handle_unsubscribe() {

		if ( empty( $_GET[ self::UNSUBSCRIBE_QUERY_VAR ] ) || empty( $_GET['u'] ) || empty( $_GET['t'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$user_id = absint( $_GET['u'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$token   = sanitize_text_field( wp_unslash( $_GET['t'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! hash_equals( self::unsubscribe_token( $user_id ), $token ) ) {
			wp_die( esc_html__( 'This unsubscribe link is invalid.', 'membership-for-woocommerce' ) );
		}

		Mfw_Role_Based_Repository::set_user_campaign_opt_out( $user_id, true );

		wp_die(
			esc_html__( 'You have been unsubscribed from membership campaign emails.', 'membership-for-woocommerce' ),
			esc_html__( 'Unsubscribed', 'membership-for-woocommerce' ),
			array( 'response' => 200 )
		);
	}
}
