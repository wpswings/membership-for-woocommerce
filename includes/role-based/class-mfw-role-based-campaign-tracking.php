<?php
/**
 * Campaign open/click tracking (Advanced Features Roadmap — "Campaigns & communication").
 * A 1x1 tracking pixel records opens; every link in a campaign's message is rewritten to
 * go through a redirect endpoint that records the click before forwarding the recipient on.
 *
 * Both endpoints are served via `template_redirect` on plain query vars rather than
 * `add_rewrite_rule()`, so no permalink flush is needed for this to work immediately.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Campaign_Tracking.
 */
class Mfw_Role_Based_Campaign_Tracking {

	const OPEN_QUERY_VAR  = 'mfw_campaign_open';
	const CLICK_QUERY_VAR = 'mfw_campaign_click';

	/**
	 * @since 3.3.0
	 */
	public static function init() {
		add_action( 'template_redirect', array( __CLASS__, 'maybe_serve_open_pixel' ), 0 );
		add_action( 'template_redirect', array( __CLASS__, 'maybe_handle_click' ), 0 );
	}

	/**
	 * @since 3.3.0
	 * @param int $campaign_id Campaign id.
	 * @param int $user_id     Recipient id.
	 * @return string
	 */
	private static function token( $campaign_id, $user_id ) {
		return substr( wp_hash( 'mfw_campaign_track|' . $campaign_id . '|' . $user_id . '|' . wp_salt() ), 0, 20 );
	}

	/**
	 * @since 3.3.0
	 * @param int $campaign_id Campaign id.
	 * @param int $user_id     Recipient id.
	 * @return string
	 */
	private static function open_pixel_url( $campaign_id, $user_id ) {
		return add_query_arg(
			array(
				self::OPEN_QUERY_VAR => 1,
				'c'                  => $campaign_id,
				'u'                  => $user_id,
				't'                  => self::token( $campaign_id, $user_id ),
			),
			home_url( '/' )
		);
	}

	/**
	 * Appends a 1x1 transparent-image tag right before </body> (or at the very end if the
	 * message has no closing body tag, e.g. before templating wraps it).
	 *
	 * @since 3.3.0
	 * @param string $html        Email HTML.
	 * @param int    $campaign_id Campaign id.
	 * @param int    $user_id     Recipient id.
	 * @return string
	 */
	public static function append_open_pixel( $html, $campaign_id, $user_id ) {

		if ( ! $campaign_id ) {
			return $html;
		}

		$pixel = '<img src="' . esc_url( self::open_pixel_url( $campaign_id, $user_id ) ) . '" width="1" height="1" alt="" style="display:none;" />';

		if ( false !== stripos( $html, '</body>' ) ) {
			return str_ireplace( '</body>', $pixel . '</body>', $html );
		}

		return $html . $pixel;
	}

	/**
	 * @since 3.3.0
	 */
	public static function maybe_serve_open_pixel() {

		if ( empty( $_GET[ self::OPEN_QUERY_VAR ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$campaign_id = absint( $_GET['c'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$user_id     = absint( $_GET['u'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$token       = sanitize_text_field( wp_unslash( $_GET['t'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $campaign_id && $user_id && hash_equals( self::token( $campaign_id, $user_id ), $token ) ) {
			Mfw_Role_Based_Repository::record_campaign_event( $campaign_id, $user_id, 'open' );
		}

		nocache_headers();
		header( 'Content-Type: image/gif' );
		// A minimal valid 1x1 transparent GIF, base64-decoded.
		echo base64_decode( 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBTAA7' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		exit;
	}

	/**
	 * Rewrites every http(s) link in the message to go through the click-tracking
	 * redirect endpoint. Skips the unsubscribe link itself (added separately, after this
	 * runs, by the email template) and any link already pointing at this tracking endpoint
	 * (defensive, in case of a resend).
	 *
	 * @since 3.3.0
	 * @param string $html        Email HTML (or plain text — plain links are left alone,
	 *                            since a plain-text link the recipient must copy/paste isn't
	 *                            usefully "clicked" in the tracked sense anyway).
	 * @param int    $campaign_id Campaign id.
	 * @param int    $user_id     Recipient id.
	 * @return string
	 */
	public static function wrap_links( $html, $campaign_id, $user_id ) {

		if ( ! $campaign_id ) {
			return $html;
		}

		return preg_replace_callback(
			'/href="(https?:\/\/[^"]+)"/i',
			function ( $matches ) use ( $campaign_id, $user_id ) {

				$original_url = $matches[1];

				if ( false !== strpos( $original_url, self::CLICK_QUERY_VAR ) ) {
					return $matches[0];
				}

				$tracked_url = add_query_arg(
					array(
						self::CLICK_QUERY_VAR => 1,
						'c'                    => $campaign_id,
						'u'                    => $user_id,
						't'                    => self::token( $campaign_id, $user_id ),
						'url'                  => rawurlencode( $original_url ),
					),
					home_url( '/' )
				);

				return 'href="' . esc_url( $tracked_url ) . '"';
			},
			$html
		);
	}

	/**
	 * @since 3.3.0
	 */
	public static function maybe_handle_click() {

		if ( empty( $_GET[ self::CLICK_QUERY_VAR ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$campaign_id  = absint( $_GET['c'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$user_id      = absint( $_GET['u'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$token        = sanitize_text_field( wp_unslash( $_GET['t'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$original_url = isset( $_GET['url'] ) ? esc_url_raw( rawurldecode( wp_unslash( $_GET['url'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! $original_url ) {
			wp_safe_redirect( home_url( '/' ) );
			exit;
		}

		if ( $campaign_id && $user_id && hash_equals( self::token( $campaign_id, $user_id ), $token ) ) {
			Mfw_Role_Based_Repository::record_campaign_event( $campaign_id, $user_id, 'click', $original_url );
		}

		// wp_safe_redirect() would silently refuse an off-site URL (falling back to
		// wp-admin) — a campaign message legitimately can link off-site, and its content
		// already went through wp_kses_post() when the campaign was composed by an admin,
		// so a plain wp_redirect() to the already-sanitized URL is appropriate here.
		wp_redirect( $original_url ); // phpcs:ignore WordPress.Security.SafeRedirect
		exit;
	}
}
