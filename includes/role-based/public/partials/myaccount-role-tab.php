<?php
/**
 * My Account "Membership" tab content for the role-based module (WPS-7881).
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based/public/partials
 *
 * @var array[] $levels All of the current user's role-membership level rows.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id = get_current_user_id();
?>
<div class="mfw-role-card mfw-role-card--account">
	<h2><?php esc_html_e( 'Your Membership', 'membership-for-woocommerce' ); ?></h2>

	<?php if ( ! empty( $levels ) ) : ?>
		<ul class="mfw-role-level-list">
			<?php foreach ( $levels as $user_level ) : ?>
				<?php $assignment = Mfw_Role_Based_Repository::get_user_level_assignment( $user_id, $user_level['id'] ); ?>
				<li>
					<span class="mfw-role-level-name"><?php echo esc_html( $user_level['name'] ); ?></span>
					<?php if ( $assignment && ! empty( $assignment['expires_at'] ) ) : ?>
						<span class="mfw-role-level-expiry">
							<?php
							printf(
								/* translators: %s: expiry date. */
								esc_html__( 'expires %s', 'membership-for-woocommerce' ),
								esc_html( date_i18n( get_option( 'date_format' ), strtotime( $assignment['expires_at'] ) ) )
							);
							?>
						</span>
					<?php endif; ?>
					<?php if ( ! empty( $user_level['description'] ) ) : ?>
						<p class="mfw-role-level-description"><?php echo esc_html( $user_level['description'] ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php $next_tier = Mfw_Role_Based_Repository::get_next_tier( $user_id ); ?>
		<?php if ( $next_tier ) : ?>
			<?php
			$threshold = (float) $next_tier['level']['upgrade_spend_threshold'];
			$percent   = $threshold > 0 ? min( 100, round( ( $next_tier['spent'] / $threshold ) * 100 ) ) : 100;
			?>
			<div class="mfw-role-tier-progress">
				<h3>
					<?php
					printf(
						/* translators: %s: next tier level name. */
						esc_html__( 'Upgrade to %s', 'membership-for-woocommerce' ),
						esc_html( $next_tier['level']['name'] )
					);
					?>
				</h3>
				<p class="mfw-role-level-description">
					<?php
					$next_discount = (float) $next_tier['level']['discount_percent'];
					if ( $next_tier['remaining'] <= 0 ) {
						printf(
							/* translators: %s: next tier name. */
							esc_html__( 'You have spent enough to reach %s. Your upgrade will be applied automatically with your next order.', 'membership-for-woocommerce' ),
							esc_html( $next_tier['level']['name'] )
						);
					} elseif ( $next_discount > 0 ) {
						printf(
							/* translators: 1: amount still needed, 2: next tier name, 3: discount percent for the next tier. */
							esc_html__( 'Spend %1$s more to automatically move up to %2$s and unlock %3$s%% off every purchase.', 'membership-for-woocommerce' ),
							wp_kses_post( wc_price( $next_tier['remaining'] ) ),
							esc_html( $next_tier['level']['name'] ),
							esc_html( wc_format_localized_decimal( $next_discount ) )
						);
					} else {
						printf(
							/* translators: 1: amount still needed, 2: next tier name. */
							esc_html__( 'Spend %1$s more to automatically move up to %2$s.', 'membership-for-woocommerce' ),
							wp_kses_post( wc_price( $next_tier['remaining'] ) ),
							esc_html( $next_tier['level']['name'] )
						);
					}
					?>
				</p>
				<div class="mfw-role-progress-bar">
					<div class="mfw-role-progress-bar__fill" style="width:<?php echo esc_attr( $percent ); ?>%;"></div>
				</div>
				<p class="mfw-role-level-description">
					<?php
					printf(
						/* translators: 1: amount spent so far, 2: amount required for next tier. */
						esc_html__( '%1$s of %2$s', 'membership-for-woocommerce' ),
						wp_kses_post( wc_price( $next_tier['spent'] ) ),
						wp_kses_post( wc_price( $threshold ) )
					);
					?>
				</p>
			</div>
		<?php endif; ?>
	<?php else : ?>
		<p><?php esc_html_e( 'You do not currently have a role-based membership level.', 'membership-for-woocommerce' ); ?></p>
	<?php endif; ?>

	<?php
	/**
	 * Fires on the My Account "Membership" tab, after the level list — used by
	 * Mfw_Role_Based_Self_Service to render the "switch level" section.
	 *
	 * @since 3.3.0
	 */
	do_action( 'mfw_role_membership_myaccount_after' );
	?>

	<h3><?php esc_html_e( 'Email Preferences', 'membership-for-woocommerce' ); ?></h3>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="mfw_role_based_save_campaign_preference" />
		<?php wp_nonce_field( 'mfw_role_based_campaign_preference', 'mfw_preference_nonce' ); ?>
		<p>
			<label>
				<input type="checkbox" name="opt_out" value="1" <?php checked( Mfw_Role_Based_Repository::is_user_opted_out_of_campaigns( $user_id ) ); ?> />
				<?php esc_html_e( 'Do not send me membership campaign emails', 'membership-for-woocommerce' ); ?>
			</label>
		</p>
		<button type="submit" class="mfw-role-btn mfw-role-btn--outline"><?php esc_html_e( 'Save Preference', 'membership-for-woocommerce' ); ?></button>
	</form>
</div>
