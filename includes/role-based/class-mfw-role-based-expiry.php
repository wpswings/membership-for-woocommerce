<?php
/**
 * Time-limited levels: auto-expiry and renewal reminders (Advanced Features Roadmap —
 * "Membership lifecycle"). A level's `expiry_days` (0 = never) is used to compute a fixed
 * `expires_at` at assignment time (see Mfw_Role_Based_Repository::assign_level_to_user());
 * this class is the daily WP-Cron job that acts on it.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Expiry.
 */
class Mfw_Role_Based_Expiry {

	const CRON_HOOK = 'mfw_role_based_expiry_check';

	/**
	 * How many days before expiry the one-time reminder email goes out.
	 */
	const REMINDER_DAYS_BEFORE = 3;

	/**
	 * @since 3.3.0
	 */
	public static function init() {

		add_action( self::CRON_HOOK, array( __CLASS__, 'run' ) );

		if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_schedule_event( time(), 'daily', self::CRON_HOOK );
		}
	}

	/**
	 * Unschedules the cron job. Not currently called automatically anywhere (init() only
	 * runs once role mode is already active, so there's no clean hook point to catch a
	 * switch AWAY from role mode mid-request — the same timing gap documented for the
	 * membership-login-page creation in Mfw_Mode_Controller::set_mode()). Harmless either
	 * way: run() itself no-ops immediately if role mode isn't active. Exposed for a future
	 * uninstall routine to call explicitly.
	 *
	 * @since 3.3.0
	 */
	public static function unschedule() {
		$timestamp = wp_next_scheduled( self::CRON_HOOK );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, self::CRON_HOOK );
		}
	}

	/**
	 * The cron callback: revokes anything past its expires_at, and sends a one-time
	 * reminder for anything expiring within REMINDER_DAYS_BEFORE that hasn't been
	 * reminded yet.
	 *
	 * @since 3.3.0
	 */
	public static function run() {

		if ( ! Mfw_Mode_Controller::is_role_mode() ) {
			return;
		}

		$now              = current_time( 'timestamp' ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
		$reminder_cutoff  = strtotime( '+' . self::REMINDER_DAYS_BEFORE . ' days', $now );

		foreach ( Mfw_Role_Based_Repository::get_all_expiring_assignments() as $assignment ) {

			$expires_timestamp = strtotime( $assignment['expires_at'] );
			if ( ! $expires_timestamp ) {
				continue;
			}

			if ( $expires_timestamp <= $now ) {
				Mfw_Role_Based_Repository::revoke_level_from_user( $assignment['user_id'], $assignment['level_id'], 0 );
				continue;
			}

			if ( ! $assignment['reminded'] && $expires_timestamp <= $reminder_cutoff ) {
				self::send_reminder( $assignment['user_id'], $assignment['level_id'], $assignment['expires_at'] );
				Mfw_Role_Based_Repository::mark_expiry_reminded( $assignment['user_id'], $assignment['level_id'] );
			}
		}
	}

	/**
	 * @since 3.3.0
	 * @param int    $user_id    User id.
	 * @param int    $level_id   Level id.
	 * @param string $expires_at MySQL datetime.
	 */
	private static function send_reminder( $user_id, $level_id, $expires_at ) {

		$user  = get_user_by( 'id', $user_id );
		$level = Mfw_Role_Based_Repository::get_level( $level_id );

		if ( ! $user || ! $level ) {
			return;
		}

		/* translators: %s: level name. */
		$subject = sprintf( __( 'Your %s membership is expiring soon', 'membership-for-woocommerce' ), $level['name'] );

		/* translators: 1: display name, 2: level name, 3: expiry date. */
		$message = sprintf(
			__( 'Hi %1$s, your "%2$s" membership level is set to expire on %3$s. Contact us if you\'d like it renewed.', 'membership-for-woocommerce' ),
			$user->display_name,
			$level['name'],
			date_i18n( get_option( 'date_format' ), strtotime( $expires_at ) )
		);

		if ( class_exists( 'Mfw_Role_Based_Email_Template' ) ) {
			$message = Mfw_Role_Based_Email_Template::wrap( $subject, wpautop( esc_html( $message ) ) );
			add_filter( 'wp_mail_content_type', array( 'Mfw_Role_Based_Email_Template', 'html_content_type' ) );
			wp_mail( $user->user_email, $subject, $message );
			remove_filter( 'wp_mail_content_type', array( 'Mfw_Role_Based_Email_Template', 'html_content_type' ) );
		} else {
			wp_mail( $user->user_email, $subject, $message );
		}
	}
}
