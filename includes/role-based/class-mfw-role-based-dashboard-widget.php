<?php
/**
 * WP Dashboard widget (Advanced Features Roadmap — "Admin & reporting"): at-a-glance
 * member/campaign stats without opening the full "Role-Based Membership" admin page.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Dashboard_Widget.
 */
class Mfw_Role_Based_Dashboard_Widget {

	/**
	 * @since 3.3.0
	 */
	public static function init() {

		if ( ! Mfw_Mode_Controller::is_role_mode() ) {
			return;
		}

		add_action( 'wp_dashboard_setup', array( __CLASS__, 'register_widget' ) );
	}

	/**
	 * @since 3.3.0
	 */
	public static function register_widget() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_add_dashboard_widget(
			'mfw_role_based_dashboard_widget',
			__( 'Role-Based Membership', 'membership-for-woocommerce' ),
			array( __CLASS__, 'render' )
		);
	}

	/**
	 * @since 3.3.0
	 */
	public static function render() {

		$member_count     = count( Mfw_Role_Based_Repository::get_member_user_ids() );
		$non_member_count = count( Mfw_Role_Based_Repository::get_non_member_user_ids() );
		$levels           = Mfw_Role_Based_Repository::get_levels();
		$recent_logins    = Mfw_Role_Based_Repository::get_login_log( 5 );
		$recent_campaigns = Mfw_Role_Based_Repository::get_campaigns( 3 );
		$settings_url     = admin_url( 'admin.php?page=' . Mfw_Role_Based_Admin::PAGE_SLUG );
		?>
		<p>
			<strong><?php echo esc_html( $member_count ); ?></strong> <?php esc_html_e( 'members', 'membership-for-woocommerce' ); ?>
			&nbsp;&middot;&nbsp;
			<strong><?php echo esc_html( $non_member_count ); ?></strong> <?php esc_html_e( 'non-members', 'membership-for-woocommerce' ); ?>
			&nbsp;&middot;&nbsp;
			<strong><?php echo esc_html( count( $levels ) ); ?></strong> <?php esc_html_e( 'levels', 'membership-for-woocommerce' ); ?>
		</p>

		<?php if ( ! empty( $recent_logins ) ) : ?>
			<p><strong><?php esc_html_e( 'Recent logins', 'membership-for-woocommerce' ); ?></strong></p>
			<ul>
				<?php foreach ( $recent_logins as $entry ) : ?>
					<?php $user = get_userdata( $entry['user_id'] ); ?>
					<li>
						<?php echo esc_html( $user ? $user->display_name : __( 'Deleted user', 'membership-for-woocommerce' ) ); ?>
						&mdash;
						<?php echo esc_html( human_time_diff( strtotime( $entry['created_at'] ), current_time( 'timestamp' ) ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested ?>
						<?php esc_html_e( 'ago', 'membership-for-woocommerce' ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( ! empty( $recent_campaigns ) ) : ?>
			<p><strong><?php esc_html_e( 'Recent campaigns', 'membership-for-woocommerce' ); ?></strong></p>
			<ul>
				<?php foreach ( $recent_campaigns as $campaign ) : ?>
					<li>
						<?php echo esc_html( $campaign['subject'] ); ?>
						&mdash;
						<?php
						printf(
							/* translators: %d: recipient count. */
							esc_html__( '%d sent', 'membership-for-woocommerce' ),
							(int) $campaign['recipient_count']
						);
						?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<p><a href="<?php echo esc_url( $settings_url ); ?>"><?php esc_html_e( 'Manage Role-Based Membership', 'membership-for-woocommerce' ); ?> &rarr;</a></p>
		<?php
	}
}
