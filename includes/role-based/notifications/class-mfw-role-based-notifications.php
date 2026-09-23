<?php
/**
 * Notifications for the role-based membership module (WPS-7882).
 *
 * Email uses wp_mail(), matching this plugin's existing notification style (there are no
 * WC_Email subclasses anywhere in the codebase). SMS/WhatsApp is an explicit open scope
 * decision from the epic — no such integration exists in this plugin today, so it is left
 * as a documented filter extension point rather than guessed at.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based/notifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Notifications.
 */
class Mfw_Role_Based_Notifications {

	/**
	 * @since 3.2.0
	 */
	public static function init() {

		if ( ! Mfw_Mode_Controller::is_role_mode() ) {
			return;
		}

		add_action( 'mfw_role_based_after_assign_level', array( __CLASS__, 'notify_level_assigned' ), 10, 2 );
		add_action( 'mfw_role_based_after_revoke_level', array( __CLASS__, 'notify_level_revoked' ), 10, 2 );
	}

	/**
	 * @since 3.2.0
	 * @param int   $user_id User id.
	 * @param array $level   Level row.
	 */
	public static function notify_level_assigned( $user_id, $level ) {

		$user = get_user_by( 'id', $user_id );
		if ( ! $user ) {
			return;
		}

		if ( Mfw_Role_Based_Tiers::is_upgrading() ) {
			/* translators: %s: role-membership level name. */
			$subject = sprintf( __( 'You have been upgraded to %s', 'membership-for-woocommerce' ), $level['name'] );
			$message = (float) $level['discount_percent'] > 0
				/* translators: 1: display name, 2: role-membership level name, 3: discount percent. */
				? sprintf( __( 'Hi %1$s, thanks for shopping with us! Your purchases have moved you up to the "%2$s" membership level, which gives you %3$s%% off.', 'membership-for-woocommerce' ), $user->display_name, $level['name'], $level['discount_percent'] )
				/* translators: 1: display name, 2: role-membership level name. */
				: sprintf( __( 'Hi %1$s, thanks for shopping with us! Your purchases have moved you up to the "%2$s" membership level.', 'membership-for-woocommerce' ), $user->display_name, $level['name'] );
		} else {
			/* translators: %s: role-membership level name. */
			$subject = sprintf( __( 'You now have %s access', 'membership-for-woocommerce' ), $level['name'] );
			/* translators: 1: display name, 2: role-membership level name. */
			$message = sprintf(
				__( 'Hi %1$s, you have been granted the "%2$s" membership level.', 'membership-for-woocommerce' ),
				$user->display_name,
				$level['name']
			);
		}

		self::send( $user->user_email, $subject, $message, $user_id, $level );
	}

	/**
	 * @since 3.2.0
	 * @param int   $user_id User id.
	 * @param array $level   Level row.
	 */
	public static function notify_level_revoked( $user_id, $level ) {

		// A tier upgrade replaces the old tier — the member gets the "upgraded" email instead.
		if ( Mfw_Role_Based_Tiers::is_upgrading() ) {
			return;
		}

		$user = get_user_by( 'id', $user_id );
		if ( ! $user ) {
			return;
		}

		/* translators: %s: role-membership level name. */
		$subject = sprintf( __( 'Your %s access has ended', 'membership-for-woocommerce' ), $level['name'] );
		/* translators: 1: display name, 2: role-membership level name. */
		$message = sprintf(
			__( 'Hi %1$s, your "%2$s" membership level has been removed.', 'membership-for-woocommerce' ),
			$user->display_name,
			$level['name']
		);

		self::send( $user->user_email, $subject, $message, $user_id, $level );
	}

	/**
	 * Sends the email and gives other channels (SMS/WhatsApp, once the scope decision
	 * is made) a chance to act on the same event.
	 *
	 * @since 3.2.0
	 * @param string $to      Recipient email.
	 * @param string $subject Email subject.
	 * @param string $message Email body.
	 * @param int    $user_id User id.
	 * @param array  $level   Level row.
	 */
	private static function send( $to, $subject, $message, $user_id, $level ) {

		$html_message = Mfw_Role_Based_Email_Template::wrap( $subject, wpautop( esc_html( $message ) ) );

		add_filter( 'wp_mail_content_type', array( 'Mfw_Role_Based_Email_Template', 'html_content_type' ) );
		wp_mail( $to, $subject, $html_message );
		remove_filter( 'wp_mail_content_type', array( 'Mfw_Role_Based_Email_Template', 'html_content_type' ) );

		/**
		 * Extension point for additional notification channels (SMS, WhatsApp, etc.).
		 * No channel is registered by default — the epic leaves this scope decision open.
		 *
		 * @since 3.2.0
		 * @param int    $user_id User id.
		 * @param array  $level   Level row.
		 * @param string $subject Subject/short message used for email.
		 * @param string $message Full message body used for email.
		 */
		do_action( 'mfw_role_based_notification_channels', $user_id, $level, $subject, $message );
	}
}
