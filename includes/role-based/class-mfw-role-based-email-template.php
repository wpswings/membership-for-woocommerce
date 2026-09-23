<?php
/**
 * Branded HTML email wrapper (Advanced Features Roadmap — "Campaigns & communication").
 * Wraps every role-based-module email (per-event notifications, expiry reminders, and
 * campaigns) in a consistent header/footer instead of a bare HTML fragment.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Email_Template.
 */
class Mfw_Role_Based_Email_Template {

	const HEADER_COLOR_OPTION = 'mfw_role_based_email_header_color';

	const FOOTER_TEXT_OPTION = 'mfw_role_based_email_footer_text';

	/**
	 * @since 3.3.0
	 * @return string
	 */
	public static function get_header_color() {
		$color = get_option( self::HEADER_COLOR_OPTION, '#2196f3' );
		return preg_match( '/^#[0-9a-fA-F]{6}$/', $color ) ? $color : '#2196f3';
	}

	/**
	 * @since 3.3.0
	 * @return string
	 */
	public static function get_footer_text() {
		return get_option( self::FOOTER_TEXT_OPTION, '' );
	}

	/**
	 * @since 3.3.0
	 * @param string $header_color Hex color, e.g. '#2196f3'.
	 * @param string $footer_text  Plain text or simple HTML footer.
	 */
	public static function save_settings( $header_color, $footer_text ) {
		if ( preg_match( '/^#[0-9a-fA-F]{6}$/', $header_color ) ) {
			update_option( self::HEADER_COLOR_OPTION, $header_color );
		}
		update_option( self::FOOTER_TEXT_OPTION, wp_kses_post( $footer_text ) );
	}

	/**
	 * Wraps a message body in a simple, table-based HTML email shell (table layout for
	 * maximum email-client compatibility, matching common transactional-email practice).
	 *
	 * @since 3.3.0
	 * @param string $subject      Used as the header title.
	 * @param string $body_html    Inner content, already HTML.
	 * @param string $footnote     Optional extra line appended after the configured footer
	 *                             text (e.g. an unsubscribe link) — kept separate so callers
	 *                             don't need to know/duplicate the configured footer text.
	 * @return string
	 */
	public static function wrap( $subject, $body_html, $footnote = '' ) {

		$color       = self::get_header_color();
		$site_name   = get_bloginfo( 'name' );
		$footer_text = self::get_footer_text();

		ob_start();
		?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title><?php echo esc_html( $subject ); ?></title></head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:sans-serif;">
	<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:24px 0;">
		<tr>
			<td align="center">
				<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;">
					<tr>
						<td style="background:<?php echo esc_attr( $color ); ?>;padding:20px 32px;">
							<span style="color:#ffffff;font-size:18px;font-weight:bold;"><?php echo esc_html( $site_name ); ?></span>
						</td>
					</tr>
					<tr>
						<td style="padding:32px;color:#333333;font-size:14px;line-height:1.6;">
							<?php echo $body_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</td>
					</tr>
					<?php if ( $footer_text || $footnote ) : ?>
						<tr>
							<td style="padding:16px 32px;color:#888888;font-size:12px;border-top:1px solid #eeeeee;">
								<?php if ( $footer_text ) : ?>
									<p><?php echo wp_kses_post( $footer_text ); ?></p>
								<?php endif; ?>
								<?php if ( $footnote ) : ?>
									<p><?php echo wp_kses_post( $footnote ); ?></p>
								<?php endif; ?>
							</td>
						</tr>
					<?php endif; ?>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
		<?php
		return ob_get_clean();
	}

	/**
	 * @since 3.3.0
	 * @return string
	 */
	public static function html_content_type() {
		return 'text/html';
	}
}
