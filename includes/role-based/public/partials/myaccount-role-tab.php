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

$user_id     = get_current_user_id();
$date_format = get_option( 'date_format' );
?>
<div class="mfw-role-card mfw-role-card--account">
	<h2><?php esc_html_e( 'Your Membership', 'membership-for-woocommerce' ); ?></h2>

	<?php if ( ! empty( $levels ) ) : ?>
		<?php foreach ( $levels as $mfw_user_level ) : ?>
			<?php $assignment = Mfw_Role_Based_Repository::get_user_level_assignment( $user_id, $mfw_user_level['id'] ); ?>
			<section class="mfw-role-plan mfw-role-plan--current">
				<div class="mfw-role-plan__head">
					<span class="mfw-role-plan__eyebrow"><?php esc_html_e( 'Current plan', 'membership-for-woocommerce' ); ?></span>
					<span class="mfw-role-status"><?php esc_html_e( 'Active', 'membership-for-woocommerce' ); ?></span>
				</div>

				<h3 class="mfw-role-plan__name mfw-role-level-title"><?php echo esc_html( $mfw_user_level['name'] ); ?></h3>

				<?php if ( $assignment && ( ! empty( $assignment['assigned_at'] ) || ! empty( $assignment['expires_at'] ) ) ) : ?>
					<p class="mfw-role-plan__meta">
						<?php
						$meta_parts = array();
						if ( ! empty( $assignment['assigned_at'] ) ) {
							/* translators: %s: date the user joined the level. */
							$meta_parts[] = sprintf( esc_html__( 'Member since %s', 'membership-for-woocommerce' ), esc_html( date_i18n( $date_format, strtotime( $assignment['assigned_at'] ) ) ) );
						}
						if ( ! empty( $assignment['expires_at'] ) ) {
							/* translators: %s: expiry date. */
							$meta_parts[] = sprintf( esc_html__( 'Expires %s', 'membership-for-woocommerce' ), esc_html( date_i18n( $date_format, strtotime( $assignment['expires_at'] ) ) ) );
						}
						echo wp_kses_post( implode( ' &middot; ', $meta_parts ) );
						?>
					</p>
				<?php endif; ?>

				<?php if ( ! empty( $mfw_user_level['description'] ) ) : ?>
					<p class="mfw-role-plan__description"><?php echo esc_html( $mfw_user_level['description'] ); ?></p>
				<?php endif; ?>

				<p class="mfw-role-plan__label"><?php esc_html_e( 'Your benefits', 'membership-for-woocommerce' ); ?></p>
				<?php Mfw_Role_Based_Public::render_level_benefits( $mfw_user_level ); ?>
			</section>
		<?php endforeach; ?>

		<?php $next_tier = Mfw_Role_Based_Repository::get_next_tier( $user_id ); ?>
		<?php if ( $next_tier ) : ?>
			<?php
			$threshold     = (float) $next_tier['level']['upgrade_spend_threshold'];
			$percent       = $threshold > 0 ? min( 100, round( ( $next_tier['spent'] / $threshold ) * 100 ) ) : 100;
			$next_discount = (float) $next_tier['level']['discount_percent'];
			?>
			<section class="mfw-role-plan mfw-role-plan--upgrade">
				<div class="mfw-role-plan__head">
					<span class="mfw-role-plan__eyebrow"><?php esc_html_e( 'Next level', 'membership-for-woocommerce' ); ?></span>
					<?php if ( $next_tier['remaining'] <= 0 ) : ?>
						<span class="mfw-role-status"><?php esc_html_e( 'Unlocked', 'membership-for-woocommerce' ); ?></span>
					<?php else : ?>
						<span class="mfw-role-status mfw-role-status--muted"><?php echo esc_html( $percent . '%' ); ?></span>
					<?php endif; ?>
				</div>

				<h3 class="mfw-role-plan__name">
					<?php
					printf(
						/* translators: %s: next tier level name. */
						esc_html__( 'Upgrade to %s', 'membership-for-woocommerce' ),
						'<span class="mfw-role-level-title">' . esc_html( $next_tier['level']['name'] ) . '</span>'
					);
					?>
				</h3>

				<p class="mfw-role-plan__description">
					<?php
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

				<div class="mfw-role-progress">
					<div class="mfw-role-progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr( $percent ); ?>">
						<div class="mfw-role-progress-bar__fill" style="width:<?php echo esc_attr( $percent ); ?>%;"></div>
					</div>
					<div class="mfw-role-progress__legend">
						<span>
							<?php
							printf(
								/* translators: %s: amount spent so far. */
								esc_html__( '%s spent', 'membership-for-woocommerce' ),
								wp_kses_post( wc_price( $next_tier['spent'] ) )
							);
							?>
						</span>
						<span>
							<?php
							printf(
								/* translators: %s: amount required for next tier. */
								esc_html__( 'Goal %s', 'membership-for-woocommerce' ),
								wp_kses_post( wc_price( $threshold ) )
							);
							?>
						</span>
					</div>
				</div>

				<p class="mfw-role-plan__label"><?php esc_html_e( 'What you\'ll get', 'membership-for-woocommerce' ); ?></p>
				<?php Mfw_Role_Based_Public::render_level_benefits( $next_tier['level'] ); ?>

				<?php if ( $next_tier['remaining'] > 0 && function_exists( 'wc_get_page_permalink' ) ) : ?>
					<div class="mfw-role-plan__actions">
						<a class="mfw-role-btn" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
							<?php esc_html_e( 'Continue shopping', 'membership-for-woocommerce' ); ?>
						</a>
					</div>
				<?php endif; ?>
			</section>
		<?php endif; ?>
	<?php else : ?>
		<section class="mfw-role-plan mfw-role-plan--empty">
			<h3 class="mfw-role-plan__name"><?php esc_html_e( 'You are not a member yet', 'membership-for-woocommerce' ); ?></h3>
			<p class="mfw-role-plan__description">
				<?php
				if ( Mfw_Role_Based_Repository::get_self_signup_levels() ) {
					esc_html_e( 'Choose a plan below to get member discounts and access to members-only content.', 'membership-for-woocommerce' );
				} else {
					esc_html_e( 'You do not currently have a role-based membership level.', 'membership-for-woocommerce' );
				}
				?>
			</p>
		</section>
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

	<div class="mfw-role-account-section">
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
</div>
