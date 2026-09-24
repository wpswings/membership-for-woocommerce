<?php
/**
 * Role-based membership settings + levels/capabilities manager (WPS-7880).
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based/admin/partials
 *
 * @var array[]  $levels               Rows from Mfw_Role_Based_Repository::get_levels().
 * @var WP_Roles $wp_roles             Site roles.
 * @var string   $nonce                AJAX nonce for Mfw_Role_Based_Admin::NONCE_ACTION.
 * @var array    $capability_groups    Mfw_Role_Based_Admin::capability_groups() result.
 * @var array    $role_capabilities_map Mfw_Role_Based_Admin::role_capabilities_map() result.
 * @var bool     $private_site         Whether Private Site is currently enabled.
 * @var string   $login_heading        Custom heading for the membership login page.
 * @var string   $login_notice         Admin-designed notice HTML for the membership login page.
 * @var bool     $login_notice_enabled Whether the notice is currently shown.
 * @var string   $login_page_url       The membership login page's URL.
 * @var array[]  $login_log            Rows from Mfw_Role_Based_Repository::get_login_log().
 * @var array[]  $campaigns            Rows from Mfw_Role_Based_Repository::get_campaigns().
 * @var int      $member_count         Count of users currently holding a role-membership level.
 * @var int      $non_member_count     Count of users currently holding no role-membership level.
 * @var string   $email_header_color   Mfw_Role_Based_Email_Template header color.
 * @var string   $email_footer_text    Mfw_Role_Based_Email_Template footer text.
 * @var array    $webhooks             Mfw_Role_Based_Webhooks::get_webhooks() result.
 * @var WP_Term[] $product_categories  WooCommerce product categories, for discount scoping.
 * @var array    $level_distribution   Mfw_Role_Based_Repository::get_level_distribution() result.
 * @var array    $signups_per_day      Mfw_Role_Based_Repository::get_signups_per_day() result.
 * @var array    $total_campaign_stats Mfw_Role_Based_Repository::get_total_campaign_stats() result.
 * @var int      $campaign_count       Count of campaigns in $campaigns.
 * @var array    $role_user_counts     Mfw_Role_Based_Roles_Manager::get_role_user_counts() result.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="mfw-role-based-settings">
	<div class="wps-header-container">
		<span class="wps-header-title"><?php esc_html_e( 'Role-Based Membership', 'membership-for-woocommerce' ); ?></span>
	</div>

	<?php if ( ! Mfw_Mode_Controller::is_role_mode() ) : ?>
		<div class="notice notice-warning">
			<p>
				<?php
				printf(
					/* translators: %s: link to the Membership Mode page. */
					esc_html__( 'Role-based mode is not currently active. Switch modes from the %s page to enable it for customers.', 'membership-for-woocommerce' ),
					'<a href="' . esc_url( admin_url( 'admin.php?page=mfw_choose_membership_mode' ) ) . '">' . esc_html__( 'Membership Mode', 'membership-for-woocommerce' ) . '</a>'
				);
				?>
			</p>
		</div>
	<?php endif; ?>

	<?php if ( isset( $_GET['mfw_import'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
		<div class="notice notice-info is-dismissible">
			<p>
				<?php
				$result = sanitize_text_field( wp_unslash( $_GET['mfw_import'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( is_numeric( $result ) ) {
					printf(
						/* translators: %d: number of levels imported. */
						esc_html( _n( 'Imported %d level.', 'Imported %d levels.', (int) $result, 'membership-for-woocommerce' ) ),
						(int) $result
					);
				} else {
					esc_html_e( 'Import failed: the file was missing or not valid JSON.', 'membership-for-woocommerce' );
				}
				?>
			</p>
		</div>
	<?php endif; ?>

	<div class="wps-navbar mfw-tabs-navbar">
		<ul class="wps-navbar__items">
			<li><a href="#general" class="mfw-tab-link" data-tab="general"><?php esc_html_e( 'General Settings', 'membership-for-woocommerce' ); ?></a></li>
			<li><a href="#login-page" class="mfw-tab-link" data-tab="login-page"><?php esc_html_e( 'Login Page', 'membership-for-woocommerce' ); ?></a></li>
			<li><a href="#roles" class="mfw-tab-link" data-tab="roles"><?php esc_html_e( 'Roles', 'membership-for-woocommerce' ); ?></a></li>
			<li><a href="#levels" class="mfw-tab-link" data-tab="levels"><?php esc_html_e( 'Levels & Capabilities', 'membership-for-woocommerce' ); ?></a></li>
			<li><a href="#campaigns" class="mfw-tab-link" data-tab="campaigns"><?php esc_html_e( 'Campaigns', 'membership-for-woocommerce' ); ?></a></li>
			<li><a href="#activity" class="mfw-tab-link" data-tab="activity"><?php esc_html_e( 'Login Activity', 'membership-for-woocommerce' ); ?></a></li>
			<li><a href="#advanced" class="mfw-tab-link" data-tab="advanced"><?php esc_html_e( 'Advanced', 'membership-for-woocommerce' ); ?></a></li>
			<li><a href="#import-export" class="mfw-tab-link" data-tab="import-export"><?php esc_html_e( 'Import / Export', 'membership-for-woocommerce' ); ?></a></li>
		</ul>
	</div>

	<div class="mfw-tab-panel" data-tab-panel="general">
	<div class="mfw-card">
		<h2><?php esc_html_e( 'General Settings', 'membership-for-woocommerce' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'Private Site', 'membership-for-woocommerce' ); ?></th>
				<td>
					<label class="mfw-toggle">
						<input type="checkbox" id="mfw-private-site" <?php checked( $private_site ); ?> />
						<span class="mfw-toggle__slider"></span>
					</label>
					<span><?php esc_html_e( 'Require visitors to log in to view the site (while role-based mode is active). Off = guest mode: anyone can browse the site, and only content you\'ve explicitly restricted asks for a membership level.', 'membership-for-woocommerce' ); ?></span>
					<p class="description"><?php esc_html_e( 'When this is on, set up a page with the [wps_role_membership_register] shortcode and enable "Self-Signup" on at least one level — otherwise a brand-new visitor has no way to create an account, only to log into an existing one.', 'membership-for-woocommerce' ); ?></p>
				</td>
			</tr>
		</table>
	</div>
	</div>

	<div class="mfw-tab-panel" data-tab-panel="login-page">
	<div class="mfw-card">
		<h2><?php esc_html_e( 'Membership Login Page', 'membership-for-woocommerce' ); ?></h2>
		<p>
			<?php
			printf(
				/* translators: %s: link to the actual login page. */
				esc_html__( 'Visitors log in at %s. Design what shows there below.', 'membership-for-woocommerce' ),
				'<a href="' . esc_url( $login_page_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $login_page_url ) . '</a>'
			);
			?>
		</p>
		<table class="form-table">
			<tr>
				<th><label for="mfw-login-heading"><?php esc_html_e( 'Heading', 'membership-for-woocommerce' ); ?></label></th>
				<td><input type="text" id="mfw-login-heading" class="regular-text" value="<?php echo esc_attr( $login_heading ); ?>" placeholder="<?php esc_attr_e( 'e.g. Welcome Back, Member!', 'membership-for-woocommerce' ); ?>" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Notice', 'membership-for-woocommerce' ); ?></th>
				<td>
					<label class="mfw-toggle">
						<input type="checkbox" id="mfw-login-notice-enabled" <?php checked( $login_notice_enabled ); ?> />
						<span class="mfw-toggle__slider"></span>
					</label>
					<span><?php esc_html_e( 'Show a notice above the login form (e.g. an announcement, downtime message, or promo).', 'membership-for-woocommerce' ); ?></span>
					<?php
					wp_editor(
						$login_notice,
						'mfw_login_notice',
						array(
							'textarea_name' => 'mfw_login_notice',
							'textarea_rows' => 6,
							'media_buttons' => false,
							'quicktags'     => true,
						)
					);
					?>
				</td>
			</tr>
		</table>
		<p><button type="button" class="mfw-btn" id="mfw-save-login-design"><?php esc_html_e( 'Save Login Page Design', 'membership-for-woocommerce' ); ?></button></p>
	</div>
	</div>

	<div class="mfw-tab-panel" data-tab-panel="roles">
	<div class="mfw-card">
		<h2><?php esc_html_e( 'WordPress Roles', 'membership-for-woocommerce' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Manage raw WordPress roles independently of Levels — a role created here can be picked in any Level\'s "WordPress Role" field afterward, same as any other role.', 'membership-for-woocommerce' ); ?></p>
		<div class="mfw-table-wrap">
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Name', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Slug', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Users', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'membership-for-woocommerce' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $wp_roles->roles as $role_slug => $role ) : ?>
						<tr
							data-slug="<?php echo esc_attr( $role_slug ); ?>"
							data-role="<?php echo esc_attr( wp_json_encode( array(
								'slug'         => $role_slug,
								'name'         => $role['name'],
								'capabilities' => array_keys( array_filter( $role['capabilities'] ) ),
							) ) ); ?>"
						>
							<td><?php echo esc_html( $role['name'] ); ?></td>
							<td><code><?php echo esc_html( $role_slug ); ?></code></td>
							<td><?php echo esc_html( $role_user_counts[ $role_slug ] ?? 0 ); ?></td>
							<td>
								<button type="button" class="mfw-btn mfw-btn__outline mfw-btn--sm mfw-edit-role" data-slug="<?php echo esc_attr( $role_slug ); ?>">
									<?php esc_html_e( 'Edit', 'membership-for-woocommerce' ); ?>
								</button>
								<button type="button" class="mfw-btn mfw-btn__outline mfw-btn--sm mfw-clone-role" data-slug="<?php echo esc_attr( $role_slug ); ?>">
									<?php esc_html_e( 'Clone', 'membership-for-woocommerce' ); ?>
								</button>
								<?php if ( Mfw_Role_Based_Roles_Manager::is_role_deletable( $role_slug ) ) : ?>
									<button type="button" class="mfw-btn mfw-btn__outline mfw-btn--sm mfw-delete-role" data-slug="<?php echo esc_attr( $role_slug ); ?>">
										<?php esc_html_e( 'Delete', 'membership-for-woocommerce' ); ?>
									</button>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<h2 id="mfw-role-form-title"><?php esc_html_e( 'Add Role', 'membership-for-woocommerce' ); ?></h2>
		<input type="hidden" id="mfw-role-slug" value="" />
		<table class="form-table">
			<tr>
				<th><label for="mfw-role-name"><?php esc_html_e( 'Role Name', 'membership-for-woocommerce' ); ?></label></th>
				<td><input type="text" id="mfw-role-name" class="regular-text" placeholder="<?php esc_attr_e( 'e.g. Gold Contributor', 'membership-for-woocommerce' ); ?>" /></td>
			</tr>
		</table>

		<div class="mfw-cap-picker">
			<div class="mfw-cap-picker__search">
				<input type="search" id="mfw-role-cap-search" placeholder="<?php esc_attr_e( 'Search capabilities…', 'membership-for-woocommerce' ); ?>" />
			</div>
			<div class="mfw-cap-picker__layout">
				<div class="mfw-cap-picker__tabs">
					<?php foreach ( array_keys( $capability_groups ) as $index => $group_label ) : ?>
						<button
							type="button"
							class="mfw-cap-picker__tab<?php echo 0 === $index ? ' active' : ''; ?>"
							data-cap-tab="<?php echo esc_attr( sanitize_title( $group_label ) ); ?>"
						>
							<?php echo esc_html( $group_label ); ?>
						</button>
					<?php endforeach; ?>
				</div>
				<div class="mfw-cap-picker__panels">
					<?php foreach ( $capability_groups as $group_label => $caps ) : ?>
						<div class="mfw-cap-picker__panel<?php echo 0 === array_search( $group_label, array_keys( $capability_groups ), true ) ? ' active' : ''; ?>" data-cap-panel="<?php echo esc_attr( sanitize_title( $group_label ) ); ?>">
							<p>
								<button type="button" class="mfw-cap-picker__select-all" data-target="<?php echo esc_attr( sanitize_title( $group_label ) ); ?>"><?php esc_html_e( 'Select All', 'membership-for-woocommerce' ); ?></button>
								&nbsp;|&nbsp;
								<button type="button" class="mfw-cap-picker__deselect-all" data-target="<?php echo esc_attr( sanitize_title( $group_label ) ); ?>"><?php esc_html_e( 'Deselect All', 'membership-for-woocommerce' ); ?></button>
							</p>
							<?php foreach ( $caps as $cap_slug => $cap_label ) : ?>
								<label class="mfw-cap-picker__item" data-cap-search="<?php echo esc_attr( strtolower( $cap_label . ' ' . $cap_slug ) ); ?>">
									<input type="checkbox" class="mfw-role-capability" data-group="<?php echo esc_attr( sanitize_title( $group_label ) ); ?>" value="<?php echo esc_attr( $cap_slug ); ?>" />
									<?php echo esc_html( $cap_label ); ?>
									<code><?php echo esc_html( $cap_slug ); ?></code>
								</label>
							<?php endforeach; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<p>
			<button type="button" class="mfw-btn" id="mfw-save-role"><?php esc_html_e( 'Add Role', 'membership-for-woocommerce' ); ?></button>
			<button type="button" class="mfw-btn mfw-btn__outline" id="mfw-cancel-edit-role" style="display:none;"><?php esc_html_e( 'Cancel', 'membership-for-woocommerce' ); ?></button>
		</p>
	</div>
	</div>

	<div class="mfw-tab-panel" data-tab-panel="levels">
	<div class="mfw-card">
		<h2><?php esc_html_e( 'Membership Levels', 'membership-for-woocommerce' ); ?></h2>

		<div class="mfw-table-wrap">
			<table class="widefat striped" id="mfw-role-levels-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Name', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'WordPress Role', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Rank', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Discount', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Price', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Tier Ladder', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Status', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'membership-for-woocommerce' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( (array) $levels as $level ) : ?>
						<tr
							data-id="<?php echo esc_attr( $level['id'] ); ?>"
							data-level="<?php echo esc_attr( wp_json_encode( array(
								'id'               => (int) $level['id'],
								'name'             => $level['name'],
								'description'      => $level['description'] ?? '',
								'wp_role'          => $level['wp_role'],
								'rank'             => (int) $level['level_rank'],
								'discount_percent' => (float) $level['discount_percent'],
								'discount_category_ids' => json_decode( $level['discount_category_ids'], true ),
								'expiry_days'      => (int) $level['expiry_days'],
								'self_signup_enabled' => (bool) $level['self_signup_enabled'],
								'price'            => (float) $level['price'],
								'upgrade_spend_threshold' => (float) ( $level['upgrade_spend_threshold'] ?? 0 ),
								'tier_ladder'      => ! empty( $level['tier_ladder'] ),
								'status'           => $level['status'],
								'capabilities'     => json_decode( $level['capabilities'], true ),
							) ) ); ?>"
						>
							<td><?php echo esc_html( $level['name'] ); ?></td>
							<td><?php echo esc_html( $level['wp_role'] ); ?></td>
							<td><?php echo esc_html( $level['level_rank'] ); ?></td>
							<td><?php echo esc_html( $level['discount_percent'] ); ?>%</td>
							<td>
								<?php if ( Mfw_Role_Based_Paid_Access::is_purchasable( $level ) ) : ?>
									<?php echo wp_kses_post( wc_price( $level['price'] ) ); ?>
									<?php if ( $level['wc_product_id'] ) : ?>
										<a href="<?php echo esc_url( get_edit_post_link( $level['wc_product_id'] ) ); ?>" target="_blank" rel="noopener noreferrer">↗</a>
									<?php endif; ?>
								<?php else : ?>
									&mdash;
								<?php endif; ?>
							</td>
							<td>
								<?php if ( ! empty( $level['tier_ladder'] ) && (float) $level['upgrade_spend_threshold'] > 0 ) : ?>
									<?php echo wp_kses_post( wc_price( $level['upgrade_spend_threshold'] ) ); ?> <?php esc_html_e( 'lifetime spend', 'membership-for-woocommerce' ); ?>
								<?php elseif ( ! empty( $level['tier_ladder'] ) ) : ?>
									<?php esc_html_e( 'Base tier', 'membership-for-woocommerce' ); ?>
								<?php else : ?>
									&mdash;
								<?php endif; ?>
							</td>
							<td><?php echo esc_html( $level['status'] ); ?></td>
							<td>
								<button type="button" class="mfw-btn mfw-btn__outline mfw-edit-level" data-id="<?php echo esc_attr( $level['id'] ); ?>">
									<?php esc_html_e( 'Edit', 'membership-for-woocommerce' ); ?>
								</button>
								<button type="button" class="mfw-btn mfw-btn__outline mfw-delete-level" data-id="<?php echo esc_attr( $level['id'] ); ?>">
									<?php esc_html_e( 'Delete', 'membership-for-woocommerce' ); ?>
								</button>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if ( empty( $levels ) ) : ?>
						<tr><td colspan="8"><?php esc_html_e( 'No levels yet.', 'membership-for-woocommerce' ); ?></td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>

		<?php $tier_ladder_problems = Mfw_Role_Based_Repository::get_tier_ladder_problems(); ?>
		<?php if ( $tier_ladder_problems ) : ?>
			<div class="notice notice-warning inline">
				<p><strong><?php esc_html_e( 'Tier ladder needs attention:', 'membership-for-woocommerce' ); ?></strong></p>
				<ul>
					<?php foreach ( $tier_ladder_problems as $tier_ladder_problem ) : ?>
						<li><?php echo esc_html( $tier_ladder_problem ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if ( Mfw_Role_Based_Repository::get_tier_ladder_levels() ) : ?>
			<p>
				<button type="button" class="mfw-btn mfw-btn__outline" id="mfw-recalculate-tiers"><?php esc_html_e( 'Recalculate Tiers Now', 'membership-for-woocommerce' ); ?></button>
				<span id="mfw-recalculate-tiers-result" aria-live="polite"></span>
			</p>
			<p class="description"><?php esc_html_e( 'Members are checked automatically whenever an order is paid. Use this after setting up or changing the ladder, so members who already spent enough are moved up now.', 'membership-for-woocommerce' ); ?></p>
		<?php endif; ?>

		<h2 id="mfw-level-form-title"><?php esc_html_e( 'Add Level', 'membership-for-woocommerce' ); ?></h2>
		<input type="hidden" id="mfw-level-id" value="0" />
		<table class="form-table">
		<tr>
			<th><label for="mfw-level-name"><?php esc_html_e( 'Name', 'membership-for-woocommerce' ); ?></label></th>
			<td><input type="text" id="mfw-level-name" class="regular-text" /></td>
		</tr>
		<tr>
			<th><label for="mfw-level-description"><?php esc_html_e( 'Description', 'membership-for-woocommerce' ); ?></label></th>
			<td>
				<textarea id="mfw-level-description" class="large-text" rows="3"></textarea>
				<p class="description"><?php esc_html_e( 'What a member gets with this level — shown to customers on the join/self-signup form, the My Account "switch level" list, and "Buy access" links, so they know what they\'re paying for before they purchase.', 'membership-for-woocommerce' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="mfw-level-role"><?php esc_html_e( 'WordPress Role', 'membership-for-woocommerce' ); ?></label></th>
			<td>
				<select id="mfw-level-role">
					<?php foreach ( $wp_roles->roles as $role_slug => $role ) : ?>
						<option value="<?php echo esc_attr( $role_slug ); ?>"><?php echo esc_html( $role['name'] ); ?></option>
					<?php endforeach; ?>
				</select>
				<p class="description"><?php esc_html_e( 'Selecting an existing role ticks its current capabilities below, so you can see exactly what it already grants.', 'membership-for-woocommerce' ); ?></p>
				<p class="description"><?php esc_html_e( 'Or type a new role slug below to create it automatically with the capabilities you choose.', 'membership-for-woocommerce' ); ?></p>
				<input type="text" id="mfw-level-new-role" placeholder="<?php esc_attr_e( 'New role slug (optional)', 'membership-for-woocommerce' ); ?>" />
			</td>
		</tr>
		<tr>
			<th><label for="mfw-level-rank"><?php esc_html_e( 'Rank', 'membership-for-woocommerce' ); ?></label></th>
			<td>
				<input type="number" min="0" id="mfw-level-rank" value="0" style="width:80px;" />
				<p class="description"><?php esc_html_e( 'Higher rank = more access. Used by "minimum rank" restrictions.', 'membership-for-woocommerce' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="mfw-level-discount"><?php esc_html_e( 'Member Discount', 'membership-for-woocommerce' ); ?></label></th>
			<td>
				<input type="number" min="0" max="100" step="0.01" id="mfw-level-discount" value="0" style="width:80px;" />%
				<p class="description"><?php esc_html_e( 'Percentage off every WooCommerce product for members holding this level. If a user holds multiple levels, the highest discount applies (never stacked).', 'membership-for-woocommerce' ); ?></p>
				<?php if ( ! empty( $product_categories ) && ! is_wp_error( $product_categories ) ) : ?>
					<p class="description"><?php esc_html_e( 'Limit the discount to specific product categories (leave all unchecked for every product):', 'membership-for-woocommerce' ); ?></p>
					<div style="max-height:120px;overflow-y:auto;">
						<?php foreach ( $product_categories as $category ) : ?>
							<label style="display:block;">
								<input type="checkbox" class="mfw-level-discount-category" value="<?php echo esc_attr( $category->term_id ); ?>" />
								<?php echo esc_html( $category->name ); ?>
							</label>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="mfw-level-expiry-days"><?php esc_html_e( 'Auto-Expiry', 'membership-for-woocommerce' ); ?></label></th>
			<td>
				<input type="number" min="0" id="mfw-level-expiry-days" value="0" style="width:80px;" /> <?php esc_html_e( 'days after assignment (0 = never expires)', 'membership-for-woocommerce' ); ?>
				<p class="description"><?php esc_html_e( 'A daily check revokes the level automatically once it expires, and a one-time reminder email goes out 3 days before.', 'membership-for-woocommerce' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="mfw-level-self-signup"><?php esc_html_e( 'Self-Signup', 'membership-for-woocommerce' ); ?></label></th>
			<td>
				<label>
					<input type="checkbox" id="mfw-level-self-signup" />
					<?php esc_html_e( 'Allow visitors to select this level via the public registration shortcode and let members switch to it from My Account', 'membership-for-woocommerce' ); ?>
				</label>
			</td>
		</tr>
		<tr>
			<th><label for="mfw-level-price"><?php esc_html_e( 'Price', 'membership-for-woocommerce' ); ?></label></th>
			<td>
				<?php echo esc_html( get_woocommerce_currency_symbol() ); ?>
				<input type="number" min="0" step="0.01" id="mfw-level-price" value="0" style="width:100px;" />
				<p class="description">
					<?php esc_html_e( 'Charge for this level via WooCommerce checkout ("Buy Access") instead of free self-signup. 0 = free. Requires Self-Signup above to be checked — access is granted automatically once the order is paid.', 'membership-for-woocommerce' ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th><label for="mfw-level-tier-ladder"><?php esc_html_e( 'Tier Ladder', 'membership-for-woocommerce' ); ?></label></th>
			<td>
				<label>
					<input type="checkbox" id="mfw-level-tier-ladder" value="1" />
					<?php esc_html_e( 'Part of the auto-upgrade tier ladder', 'membership-for-woocommerce' ); ?>
				</label>
				<p class="description">
					<?php esc_html_e( 'Tiered membership: members are automatically moved up the ladder as their lifetime order total grows. Add two or more levels to the ladder, each with a higher Rank and a higher spend than the one below (e.g. Silver: Rank 1, spend 0 — Gold: Rank 2, spend 5,000 — Platinum: Rank 3, spend 20,000). Members only ever move up, never down, and levels outside the ladder are never changed.', 'membership-for-woocommerce' ); ?>
				</p>
			</td>
		</tr>
		<tr id="mfw-level-upgrade-threshold-row">
			<th><label for="mfw-level-upgrade-threshold"><?php esc_html_e( 'Auto-Upgrade At Spend', 'membership-for-woocommerce' ); ?></label></th>
			<td>
				<?php echo esc_html( get_woocommerce_currency_symbol() ); ?>
				<input type="number" min="0" step="0.01" id="mfw-level-upgrade-threshold" value="0" style="width:100px;" />
				<p class="description">
					<?php esc_html_e( 'Lifetime spend at which a member is moved up to this level. Use 0 for the base (starting) tier.', 'membership-for-woocommerce' ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th><label for="mfw-level-status"><?php esc_html_e( 'Status', 'membership-for-woocommerce' ); ?></label></th>
			<td>
				<select id="mfw-level-status">
					<option value="active"><?php esc_html_e( 'Active', 'membership-for-woocommerce' ); ?></option>
					<option value="inactive"><?php esc_html_e( 'Inactive', 'membership-for-woocommerce' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Capabilities', 'membership-for-woocommerce' ); ?></th>
			<td>
				<p class="description"><?php esc_html_e( 'These become the WordPress role\'s actual capabilities — unchecking one removes it from the role.', 'membership-for-woocommerce' ); ?></p>
				<p>
					<button type="button" class="mfw-btn mfw-btn__outline" id="mfw-select-all-capabilities"><?php esc_html_e( 'Select All', 'membership-for-woocommerce' ); ?></button>
					<button type="button" class="mfw-btn mfw-btn__outline" id="mfw-deselect-all-capabilities"><?php esc_html_e( 'Deselect All', 'membership-for-woocommerce' ); ?></button>
				</p>
				<div style="max-height:400px;overflow-y:auto;border:1px solid rgba(63,71,86,0.15);padding:12px;border-radius:4px;">
					<?php foreach ( $capability_groups as $group_label => $caps ) : ?>
						<p>
							<label>
								<input type="checkbox" class="mfw-select-all-group" data-group="<?php echo esc_attr( sanitize_title( $group_label ) ); ?>" />
								<strong><?php echo esc_html( $group_label ); ?></strong>
							</label>
						</p>
						<?php foreach ( $caps as $cap_slug => $cap_label ) : ?>
							<label style="display:block;">
								<input type="checkbox" class="mfw-level-capability" data-group="<?php echo esc_attr( sanitize_title( $group_label ) ); ?>" value="<?php echo esc_attr( $cap_slug ); ?>" />
								<?php echo esc_html( $cap_label ); ?>
								<code style="color:#888;"><?php echo esc_html( $cap_slug ); ?></code>
							</label>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</div>
			</td>
		</tr>
	</table>
	<p>
		<button type="button" class="mfw-btn" id="mfw-add-level"><?php esc_html_e( 'Add Level', 'membership-for-woocommerce' ); ?></button>
		<button type="button" class="mfw-btn mfw-btn__outline" id="mfw-cancel-edit-level" style="display:none;"><?php esc_html_e( 'Cancel', 'membership-for-woocommerce' ); ?></button>
	</p>
	</div>
	</div>

	<div class="mfw-tab-panel" data-tab-panel="campaigns">
	<?php if ( isset( $_GET['mfw_campaign'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
		<div class="notice notice-info is-dismissible">
			<p>
				<?php
				$campaign_result = sanitize_text_field( wp_unslash( $_GET['mfw_campaign'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( is_numeric( $campaign_result ) ) {
					printf(
						/* translators: %d: number of emails sent. */
						esc_html( _n( 'Campaign sent to %d recipient.', 'Campaign sent to %d recipients.', (int) $campaign_result, 'membership-for-woocommerce' ) ),
						(int) $campaign_result
					);
				} else {
					esc_html_e( 'Could not send: a subject, a message, and a valid audience are required.', 'membership-for-woocommerce' );
				}
				?>
			</p>
		</div>
	<?php endif; ?>

	<div class="mfw-card">
		<h2><?php esc_html_e( 'Send a Campaign', 'membership-for-woocommerce' ); ?></h2>
		<p>
			<?php
			printf(
				/* translators: 1: member count, 2: non-member count. */
				esc_html__( 'Currently %1$d member(s) and %2$d non-member(s) registered on this site.', 'membership-for-woocommerce' ),
				(int) $member_count,
				(int) $non_member_count
			);
			?>
		</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="mfw_role_based_send_campaign" />
			<?php wp_nonce_field( 'mfw_role_based_admin', 'mfw_campaign_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th><label for="mfw-campaign-name"><?php esc_html_e( 'Campaign Name', 'membership-for-woocommerce' ); ?></label></th>
					<td>
						<input type="text" id="mfw-campaign-name" name="campaign_name" class="regular-text" placeholder="<?php esc_attr_e( 'For your own reference — not shown to recipients', 'membership-for-woocommerce' ); ?>" />
					</td>
				</tr>
				<tr>
					<th><label for="mfw-campaign-audience"><?php esc_html_e( 'Send To', 'membership-for-woocommerce' ); ?></label></th>
					<td>
						<select id="mfw-campaign-audience" name="campaign_audience">
							<option value="<?php echo esc_attr( Mfw_Role_Based_Campaigns::AUDIENCE_MEMBERS ); ?>"><?php esc_html_e( 'Members (holding any role-membership level)', 'membership-for-woocommerce' ); ?></option>
							<option value="<?php echo esc_attr( Mfw_Role_Based_Campaigns::AUDIENCE_NON_MEMBERS ); ?>"><?php esc_html_e( 'Non-Members', 'membership-for-woocommerce' ); ?></option>
							<option value="<?php echo esc_attr( Mfw_Role_Based_Campaigns::AUDIENCE_LEVELS ); ?>"><?php esc_html_e( 'Specific level(s)', 'membership-for-woocommerce' ); ?></option>
							<option value="<?php echo esc_attr( Mfw_Role_Based_Campaigns::AUDIENCE_ALL ); ?>"><?php esc_html_e( 'Everyone', 'membership-for-woocommerce' ); ?></option>
						</select>
						<div id="mfw-campaign-levels-picker" style="display:none;margin-top:8px;">
							<?php foreach ( (array) $levels as $level ) : ?>
								<label style="display:block;">
									<input type="checkbox" name="campaign_level_ids[]" value="<?php echo esc_attr( $level['id'] ); ?>" />
									<?php echo esc_html( $level['name'] ); ?>
								</label>
							<?php endforeach; ?>
							<?php if ( empty( $levels ) ) : ?>
								<p><em><?php esc_html_e( 'No role-membership levels yet.', 'membership-for-woocommerce' ); ?></em></p>
							<?php endif; ?>
						</div>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'When', 'membership-for-woocommerce' ); ?></th>
					<td>
						<select id="mfw-campaign-send-mode" name="campaign_send_mode">
							<option value="<?php echo esc_attr( Mfw_Role_Based_Campaigns::SEND_MODE_NOW ); ?>"><?php esc_html_e( 'Send now', 'membership-for-woocommerce' ); ?></option>
							<option value="<?php echo esc_attr( Mfw_Role_Based_Campaigns::SEND_MODE_SCHEDULED ); ?>"><?php esc_html_e( 'Schedule for later', 'membership-for-woocommerce' ); ?></option>
							<option value="<?php echo esc_attr( Mfw_Role_Based_Campaigns::SEND_MODE_DRIP ); ?>"><?php esc_html_e( 'Drip — send automatically after a level is assigned', 'membership-for-woocommerce' ); ?></option>
						</select>

						<div id="mfw-campaign-scheduled-fields" style="display:none;margin-top:8px;">
							<label>
								<?php esc_html_e( 'Send at', 'membership-for-woocommerce' ); ?>
								<input type="datetime-local" name="campaign_scheduled_at" />
							</label>
						</div>

						<div id="mfw-campaign-drip-fields" style="display:none;margin-top:8px;">
							<label>
								<?php esc_html_e( 'Trigger level', 'membership-for-woocommerce' ); ?>
								<select name="campaign_drip_level_id">
									<?php foreach ( (array) $levels as $level ) : ?>
										<option value="<?php echo esc_attr( $level['id'] ); ?>"><?php echo esc_html( $level['name'] ); ?></option>
									<?php endforeach; ?>
								</select>
							</label>
							<label>
								<?php esc_html_e( 'Days after assignment', 'membership-for-woocommerce' ); ?>
								<input type="number" min="0" name="campaign_drip_delay_days" value="0" style="width:80px;" />
							</label>
							<p class="description"><?php esc_html_e( 'The "Send To" audience above is ignored for a drip campaign — it always sends to whoever is assigned the trigger level, whenever that happens.', 'membership-for-woocommerce' ); ?></p>
						</div>
					</td>
				</tr>
				<tr>
					<th><label for="mfw-campaign-subject"><?php esc_html_e( 'Subject', 'membership-for-woocommerce' ); ?></label></th>
					<td><input type="text" id="mfw-campaign-subject" name="campaign_subject" class="regular-text" required /></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Message', 'membership-for-woocommerce' ); ?></th>
					<td>
						<p class="description">
							<?php esc_html_e( 'Available placeholders: {display_name}, {user_email}, {site_name}.', 'membership-for-woocommerce' ); ?>
						</p>
						<?php
						wp_editor(
							'',
							'campaign_message',
							array(
								'textarea_name' => 'campaign_message',
								'textarea_rows' => 8,
								'media_buttons' => false,
								'quicktags'     => true,
							)
						);
						?>
					</td>
				</tr>
			</table>
			<p>
				<button type="submit" class="mfw-btn" onclick="return window.confirm( <?php echo wp_json_encode( __( 'Send this campaign now? This cannot be undone.', 'membership-for-woocommerce' ) ); ?> );">
					<?php esc_html_e( 'Send Campaign', 'membership-for-woocommerce' ); ?>
				</button>
			</p>
		</form>
	</div>

	<div class="mfw-card">
		<h2><?php esc_html_e( 'Campaign History', 'membership-for-woocommerce' ); ?></h2>
		<div class="mfw-table-wrap">
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Name', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Subject', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Audience', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Recipients', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Status', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Opens / Clicks', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Created', 'membership-for-woocommerce' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$audience_labels = array(
						Mfw_Role_Based_Campaigns::AUDIENCE_MEMBERS     => __( 'Members', 'membership-for-woocommerce' ),
						Mfw_Role_Based_Campaigns::AUDIENCE_NON_MEMBERS => __( 'Non-Members', 'membership-for-woocommerce' ),
						Mfw_Role_Based_Campaigns::AUDIENCE_LEVELS      => __( 'Specific level(s)', 'membership-for-woocommerce' ),
						Mfw_Role_Based_Campaigns::AUDIENCE_ALL         => __( 'Everyone', 'membership-for-woocommerce' ),
					);
					?>
					<?php foreach ( (array) $campaigns as $campaign ) : ?>
						<?php $campaign_stats = Mfw_Role_Based_Repository::get_campaign_stats( $campaign['id'] ); ?>
						<tr>
							<td><?php echo esc_html( $campaign['name'] ); ?></td>
							<td><?php echo esc_html( $campaign['subject'] ); ?></td>
							<td><?php echo esc_html( $audience_labels[ $campaign['audience'] ] ?? $campaign['audience'] ); ?></td>
							<td><?php echo esc_html( ucfirst( $campaign['status'] ) ); ?></td>
							<td><?php echo esc_html( $campaign['recipient_count'] ); ?></td>
							<td>
								<?php
								printf(
									/* translators: 1: unique opens, 2: unique clicks. */
									esc_html__( '%1$d / %2$d', 'membership-for-woocommerce' ),
									(int) $campaign_stats['unique_opens'],
									(int) $campaign_stats['unique_clicks']
								);
								?>
							</td>
							<td>
								<?php
								echo esc_html(
									date_i18n(
										get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
										strtotime( $campaign['created_at'] )
									)
								);
								?>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if ( empty( $campaigns ) ) : ?>
						<tr><td colspan="7"><?php esc_html_e( 'No campaigns sent yet.', 'membership-for-woocommerce' ); ?></td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
	</div>

	<div class="mfw-tab-panel" data-tab-panel="activity">
	<div class="mfw-card">
		<h2><?php esc_html_e( 'Recent Member Logins', 'membership-for-woocommerce' ); ?></h2>
		<div class="mfw-table-wrap">
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Member', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Level', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Logged In', 'membership-for-woocommerce' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( (array) $login_log as $entry ) : ?>
						<?php
						$log_user  = get_userdata( $entry['user_id'] );
						$log_level = $entry['level_id'] ? Mfw_Role_Based_Repository::get_level( $entry['level_id'] ) : null;
						?>
						<tr>
							<td>
								<?php if ( $log_user ) : ?>
									<?php echo esc_html( $log_user->display_name ); ?> (<?php echo esc_html( $log_user->user_email ); ?>)
								<?php else : ?>
									<em><?php esc_html_e( 'Deleted user', 'membership-for-woocommerce' ); ?></em>
								<?php endif; ?>
							</td>
							<td><?php echo esc_html( $log_level ? $log_level['name'] : __( '—', 'membership-for-woocommerce' ) ); ?></td>
							<td>
								<?php
								echo esc_html(
									date_i18n(
										get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
										strtotime( $entry['created_at'] )
									)
								);
								?>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if ( empty( $login_log ) ) : ?>
						<tr><td colspan="3"><?php esc_html_e( 'No member logins recorded yet.', 'membership-for-woocommerce' ); ?></td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>

	</div>

	<div class="mfw-tab-panel" data-tab-panel="advanced">

	<div class="mfw-card">
		<h2><?php esc_html_e( 'Reports', 'membership-for-woocommerce' ); ?></h2>

		<div class="mfw-stat-tiles">
			<div class="mfw-stat-tile">
				<span class="mfw-stat-tile__value"><?php echo esc_html( $member_count ); ?></span>
				<span class="mfw-stat-tile__label"><?php esc_html_e( 'Members', 'membership-for-woocommerce' ); ?></span>
			</div>
			<div class="mfw-stat-tile">
				<span class="mfw-stat-tile__value"><?php echo esc_html( $non_member_count ); ?></span>
				<span class="mfw-stat-tile__label"><?php esc_html_e( 'Non-Members', 'membership-for-woocommerce' ); ?></span>
			</div>
			<div class="mfw-stat-tile">
				<span class="mfw-stat-tile__value"><?php echo esc_html( count( $levels ) ); ?></span>
				<span class="mfw-stat-tile__label"><?php esc_html_e( 'Levels', 'membership-for-woocommerce' ); ?></span>
			</div>
			<div class="mfw-stat-tile">
				<span class="mfw-stat-tile__value"><?php echo esc_html( $campaign_count ); ?></span>
				<span class="mfw-stat-tile__label"><?php esc_html_e( 'Campaigns Sent', 'membership-for-woocommerce' ); ?></span>
			</div>
			<div class="mfw-stat-tile">
				<span class="mfw-stat-tile__value"><?php echo esc_html( $total_campaign_stats['opens'] ); ?></span>
				<span class="mfw-stat-tile__label"><?php esc_html_e( 'Campaign Opens', 'membership-for-woocommerce' ); ?></span>
			</div>
			<div class="mfw-stat-tile">
				<span class="mfw-stat-tile__value"><?php echo esc_html( $total_campaign_stats['clicks'] ); ?></span>
				<span class="mfw-stat-tile__label"><?php esc_html_e( 'Campaign Clicks', 'membership-for-woocommerce' ); ?></span>
			</div>
		</div>

		<?php
		$max_level_count = ! empty( $level_distribution ) ? max( $level_distribution ) : 0;
		?>
		<div class="mfw-chart-section">
			<div class="mfw-chart-section__header">
				<span class="mfw-chart-section__title"><?php esc_html_e( 'Members per level', 'membership-for-woocommerce' ); ?></span>
			</div>
			<div class="mfw-bar-chart">
				<?php foreach ( $level_distribution as $level_name => $count ) : ?>
					<div class="mfw-bar-chart__row">
						<span class="mfw-bar-chart__label" title="<?php echo esc_attr( $level_name ); ?>"><?php echo esc_html( $level_name ); ?></span>
						<span class="mfw-bar-chart__track">
							<span class="mfw-bar-chart__fill" style="width:<?php echo esc_attr( $max_level_count > 0 ? round( ( $count / $max_level_count ) * 100 ) : 0 ); ?>%;"></span>
						</span>
						<span class="mfw-bar-chart__value"><?php echo esc_html( $count ); ?></span>
					</div>
				<?php endforeach; ?>
				<?php if ( empty( $level_distribution ) ) : ?>
					<p><em><?php esc_html_e( 'No levels yet.', 'membership-for-woocommerce' ); ?></em></p>
				<?php endif; ?>
			</div>
		</div>

		<?php
		$max_daily     = ! empty( $signups_per_day ) ? max( $signups_per_day ) : 0;
		$daily_total   = array_sum( $signups_per_day );
		$day_keys      = array_keys( $signups_per_day );
		$first_day     = ! empty( $day_keys ) ? reset( $day_keys ) : '';
		$last_day      = ! empty( $day_keys ) ? end( $day_keys ) : '';
		?>
		<div class="mfw-chart-section">
			<div class="mfw-chart-section__header">
				<span class="mfw-chart-section__title"><?php esc_html_e( 'New assignments — last 30 days', 'membership-for-woocommerce' ); ?></span>
				<span class="mfw-chart-section__summary">
					<?php
					printf(
						/* translators: %d: total new assignments. */
						esc_html( _n( '%d total', '%d total', $daily_total, 'membership-for-woocommerce' ) ),
						(int) $daily_total
					);
					?>
				</span>
			</div>
			<div class="mfw-sparkline-wrap">
				<div class="mfw-sparkline">
					<?php foreach ( $signups_per_day as $day => $count ) : ?>
						<div class="mfw-sparkline__col">
							<span
								class="mfw-sparkline__bar"
								style="height:<?php echo esc_attr( $max_daily > 0 ? max( 2, round( ( $count / $max_daily ) * 100 ) ) : 2 ); ?>%;"
							></span>
							<span class="mfw-chart-tooltip">
								<?php echo esc_html( date_i18n( 'M j', strtotime( $day ) ) . ': ' . $count ); ?>
							</span>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="mfw-sparkline__axis">
					<span><?php echo esc_html( $first_day ? date_i18n( 'M j', strtotime( $first_day ) ) : '' ); ?></span>
					<span><?php echo esc_html( $last_day ? date_i18n( 'M j', strtotime( $last_day ) ) : '' ); ?></span>
				</div>
			</div>
		</div>
	</div>

	<div class="mfw-card">
		<h2><?php esc_html_e( 'Email Template', 'membership-for-woocommerce' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Applies to every role-based email: assign/revoke notifications, expiry reminders, and campaigns.', 'membership-for-woocommerce' ); ?></p>
		<table class="form-table">
			<tr>
				<th><label for="mfw-email-header-color"><?php esc_html_e( 'Header Color', 'membership-for-woocommerce' ); ?></label></th>
				<td><input type="color" id="mfw-email-header-color" value="<?php echo esc_attr( $email_header_color ); ?>" /></td>
			</tr>
			<tr>
				<th><label for="mfw-email-footer-text"><?php esc_html_e( 'Footer Text', 'membership-for-woocommerce' ); ?></label></th>
				<td><textarea id="mfw-email-footer-text" rows="3" class="large-text"><?php echo esc_textarea( $email_footer_text ); ?></textarea></td>
			</tr>
		</table>
		<p><button type="button" class="mfw-btn" id="mfw-save-email-template"><?php esc_html_e( 'Save Email Template', 'membership-for-woocommerce' ); ?></button></p>
	</div>

	<div class="mfw-card">
		<h2><?php esc_html_e( 'Webhooks', 'membership-for-woocommerce' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Fires a non-blocking HTTP POST with a JSON payload to the URL(s) below when each event happens.', 'membership-for-woocommerce' ); ?></p>
		<table class="form-table">
			<tr>
				<th><label for="mfw-webhook-level_assigned"><?php esc_html_e( 'Level Assigned', 'membership-for-woocommerce' ); ?></label></th>
				<td><input type="url" id="mfw-webhook-level_assigned" class="regular-text" value="<?php echo esc_attr( $webhooks['level_assigned'] ?? '' ); ?>" placeholder="https://" /></td>
			</tr>
			<tr>
				<th><label for="mfw-webhook-level_revoked"><?php esc_html_e( 'Level Revoked', 'membership-for-woocommerce' ); ?></label></th>
				<td><input type="url" id="mfw-webhook-level_revoked" class="regular-text" value="<?php echo esc_attr( $webhooks['level_revoked'] ?? '' ); ?>" placeholder="https://" /></td>
			</tr>
			<tr>
				<th><label for="mfw-webhook-campaign_sent"><?php esc_html_e( 'Campaign Sent', 'membership-for-woocommerce' ); ?></label></th>
				<td><input type="url" id="mfw-webhook-campaign_sent" class="regular-text" value="<?php echo esc_attr( $webhooks['campaign_sent'] ?? '' ); ?>" placeholder="https://" /></td>
			</tr>
		</table>
		<p><button type="button" class="mfw-btn" id="mfw-save-webhooks"><?php esc_html_e( 'Save Webhooks', 'membership-for-woocommerce' ); ?></button></p>
	</div>

	<div class="mfw-card">
		<h2><?php esc_html_e( 'REST API & Self-Service', 'membership-for-woocommerce' ); ?></h2>
		<p>
			<?php esc_html_e( 'REST endpoints (requires manage_options — logged-in cookie+nonce or an Application Password):', 'membership-for-woocommerce' ); ?>
		</p>
		<ul style="list-style:disc;padding-left:20px;">
			<li><code>GET /wp-json/mfw-role-based/v1/levels</code></li>
			<li><code>GET /wp-json/mfw-role-based/v1/members/&lt;user_id&gt;</code></li>
			<li><code>POST /wp-json/mfw-role-based/v1/members/&lt;user_id&gt;/assign</code> <?php esc_html_e( '(body: level_id)', 'membership-for-woocommerce' ); ?></li>
			<li><code>POST /wp-json/mfw-role-based/v1/members/&lt;user_id&gt;/revoke</code> <?php esc_html_e( '(body: level_id)', 'membership-for-woocommerce' ); ?></li>
		</ul>
		<p>
			<?php
			printf(
				/* translators: %s: shortcode. */
				esc_html__( 'Public self-service signup: add %s to any page. Only levels with "Allow self-signup" checked (in the Levels & Capabilities tab) appear there and in the My Account "switch level" section.', 'membership-for-woocommerce' ),
				'<code>[wps_role_membership_register]</code>'
			);
			?>
		</p>
	</div>
	</div>

<div class="mfw-tab-panel" data-tab-panel="import-export">
	<div class="mfw-card">
	<h2><?php esc_html_e( 'Import / Export', 'membership-for-woocommerce' ); ?></h2>
	<p class="description"><?php esc_html_e( 'Level definitions (name, role, capabilities, rank, discount, etc.) as a JSON file.', 'membership-for-woocommerce' ); ?></p>
	<div class="mfw-btn-row">
		<a class="mfw-btn mfw-btn__outline mfw-btn--sm" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=mfw_role_based_export_levels' ), 'mfw_role_based_admin', 'mfw_export_nonce' ) ); ?>">
			<?php esc_html_e( 'Export Levels (JSON)', 'membership-for-woocommerce' ); ?>
		</a>
	</div>
	<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="mfw_role_based_import_levels" />
		<?php wp_nonce_field( 'mfw_role_based_admin', 'mfw_import_nonce' ); ?>
		<div class="mfw-btn-row">
			<input type="file" name="mfw_import_file" accept="application/json" class="mfw-file-input" />
			<button type="submit" class="mfw-btn mfw-btn--sm"><?php esc_html_e( 'Import Levels', 'membership-for-woocommerce' ); ?></button>
		</div>
	</form>
	</div>

	<div class="mfw-card">
		<h2><?php esc_html_e( 'Members CSV', 'membership-for-woocommerce' ); ?></h2>
		<?php if ( isset( $_GET['mfw_csv_import'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-info is-dismissible">
				<p>
					<?php
					$csv_result = sanitize_text_field( wp_unslash( $_GET['mfw_csv_import'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					if ( is_numeric( $csv_result ) ) {
						printf(
							/* translators: %d: number of rows processed. */
							esc_html( _n( 'Assigned %d row.', 'Assigned %d rows.', (int) $csv_result, 'membership-for-woocommerce' ) ),
							(int) $csv_result
						);
					} else {
						esc_html_e( 'Import failed: the file was missing or not a valid CSV.', 'membership-for-woocommerce' );
					}
					?>
				</p>
			</div>
		<?php endif; ?>
		<p class="description"><?php esc_html_e( 'Who holds what level, and campaign send history.', 'membership-for-woocommerce' ); ?></p>
		<div class="mfw-btn-row">
			<a class="mfw-btn mfw-btn__outline mfw-btn--sm" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=mfw_role_based_export_members_csv' ), 'mfw_role_based_admin', 'mfw_csv_nonce' ) ); ?>">
				<?php esc_html_e( 'Export Members (CSV)', 'membership-for-woocommerce' ); ?>
			</a>
			<a class="mfw-btn mfw-btn__outline mfw-btn--sm" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=mfw_role_based_export_campaigns_csv' ), 'mfw_role_based_admin', 'mfw_csv_nonce' ) ); ?>">
				<?php esc_html_e( 'Export Campaign History (CSV)', 'membership-for-woocommerce' ); ?>
			</a>
		</div>
		<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="mfw_role_based_import_members_csv" />
			<?php wp_nonce_field( 'mfw_role_based_admin', 'mfw_csv_import_nonce' ); ?>
			<p class="description"><?php esc_html_e( 'Bulk-assign from a CSV with columns: user_email (or user_id) and level_name (or level_id).', 'membership-for-woocommerce' ); ?></p>
			<div class="mfw-btn-row">
				<input type="file" name="mfw_members_csv" accept=".csv,text/csv" class="mfw-file-input" />
				<button type="submit" class="mfw-btn mfw-btn--sm"><?php esc_html_e( 'Bulk-Assign Members', 'membership-for-woocommerce' ); ?></button>
			</div>
		</form>
	</div>
	</div>
</div>
<script>
( function () {
	var nonce = <?php echo wp_json_encode( $nonce ); ?>;
	var roleCapabilitiesMap = <?php echo wp_json_encode( $role_capabilities_map ); ?>;

	function post( action, data ) {
		var body = new FormData();
		body.append( 'action', action );
		body.append( 'nonce', nonce );
		Object.keys( data ).forEach( function ( key ) {
			if ( Array.isArray( data[ key ] ) ) {
				data[ key ].forEach( function ( value ) { body.append( key + '[]', value ); } );
			} else {
				body.append( key, data[ key ] );
			}
		} );
		return fetch( ajaxurl, { method: 'POST', credentials: 'same-origin', body: body } );
	}

	var formTitle  = document.getElementById( 'mfw-level-form-title' );
	var idField    = document.getElementById( 'mfw-level-id' );
	var nameField  = document.getElementById( 'mfw-level-name' );
	var descriptionField = document.getElementById( 'mfw-level-description' );
	var roleField  = document.getElementById( 'mfw-level-role' );
	var newRoleField = document.getElementById( 'mfw-level-new-role' );
	var rankField  = document.getElementById( 'mfw-level-rank' );
	var discountField = document.getElementById( 'mfw-level-discount' );
	var expiryDaysField = document.getElementById( 'mfw-level-expiry-days' );
	var selfSignupField = document.getElementById( 'mfw-level-self-signup' );
	var priceField = document.getElementById( 'mfw-level-price' );
	var upgradeThresholdField = document.getElementById( 'mfw-level-upgrade-threshold' );
	var tierLadderField = document.getElementById( 'mfw-level-tier-ladder' );
	var upgradeThresholdRow = document.getElementById( 'mfw-level-upgrade-threshold-row' );

	// The spend threshold only means something for a level on the ladder.
	function syncTierFields() {
		upgradeThresholdRow.style.display = tierLadderField.checked ? '' : 'none';
	}
	tierLadderField.addEventListener( 'change', syncTierFields );
	syncTierFields();

	var recalculateTiersButton = document.getElementById( 'mfw-recalculate-tiers' );
	if ( recalculateTiersButton ) {
		recalculateTiersButton.addEventListener( 'click', function () {
			var result = document.getElementById( 'mfw-recalculate-tiers-result' );
			recalculateTiersButton.disabled = true;
			result.textContent = <?php echo wp_json_encode( __( 'Checking members…', 'membership-for-woocommerce' ) ); ?>;
			post( 'mfw_role_based_recalculate_tiers', {} )
				.then( function ( response ) { return response.json(); } )
				.then( function ( json ) {
					result.textContent = json && json.data && json.data.message ? json.data.message : <?php echo wp_json_encode( __( 'Something went wrong.', 'membership-for-woocommerce' ) ); ?>;
				} )
				.catch( function () {
					result.textContent = <?php echo wp_json_encode( __( 'Something went wrong.', 'membership-for-woocommerce' ) ); ?>;
				} )
				.finally( function () { recalculateTiersButton.disabled = false; } );
		} );
	}
	var statusField = document.getElementById( 'mfw-level-status' );
	var addButton  = document.getElementById( 'mfw-add-level' );
	var cancelButton = document.getElementById( 'mfw-cancel-edit-level' );

	// Selecting an existing role previews its current capabilities by ticking the matching
	// checkboxes, so the admin can see exactly what that role already grants before deciding
	// whether to map a level onto it. Typing a new role slug doesn't touch this — there's
	// nothing to preview for a role that doesn't exist yet.
	roleField.addEventListener( 'change', function () {
		newRoleField.value = '';
		var caps = roleCapabilitiesMap[ roleField.value ] || [];
		document.querySelectorAll( '.mfw-level-capability' ).forEach( function ( el ) {
			el.checked = caps.indexOf( el.value ) !== -1;
		} );
		syncGroupCheckboxes();
	} );

	function syncGroupCheckboxes() {
		document.querySelectorAll( '.mfw-select-all-group' ).forEach( function ( groupBox ) {
			var group  = groupBox.getAttribute( 'data-group' );
			var caps   = document.querySelectorAll( '.mfw-level-capability[data-group="' + group + '"]' );
			var total  = caps.length;
			var checked = Array.prototype.filter.call( caps, function ( el ) { return el.checked; } ).length;
			groupBox.checked       = total > 0 && checked === total;
			groupBox.indeterminate = checked > 0 && checked < total;
		} );
	}

	function resetForm() {
		idField.value = '0';
		nameField.value = '';
		descriptionField.value = '';
		newRoleField.value = '';
		rankField.value = '0';
		discountField.value = '0';
		expiryDaysField.value = '0';
		selfSignupField.checked = false;
		priceField.value = '0';
		upgradeThresholdField.value = '0';
		tierLadderField.checked = false;
		syncTierFields();
		statusField.value = 'active';
		document.querySelectorAll( '.mfw-level-capability' ).forEach( function ( el ) { el.checked = false; } );
		document.querySelectorAll( '.mfw-level-discount-category' ).forEach( function ( el ) { el.checked = false; } );
		syncGroupCheckboxes();
		formTitle.textContent = <?php echo wp_json_encode( __( 'Add Level', 'membership-for-woocommerce' ) ); ?>;
		addButton.textContent = <?php echo wp_json_encode( __( 'Add Level', 'membership-for-woocommerce' ) ); ?>;
		cancelButton.style.display = 'none';
	}

	function populateForm( level ) {
		idField.value = level.id;
		nameField.value = level.name;
		descriptionField.value = level.description || '';
		roleField.value = level.wp_role;
		newRoleField.value = '';
		rankField.value = level.rank;
		discountField.value = level.discount_percent || 0;
		expiryDaysField.value = level.expiry_days || 0;
		selfSignupField.checked = !! level.self_signup_enabled;
		priceField.value = level.price || 0;
		upgradeThresholdField.value = level.upgrade_spend_threshold || 0;
		tierLadderField.checked = !! level.tier_ladder;
		syncTierFields();
		statusField.value = level.status;
		var caps = level.capabilities || [];
		document.querySelectorAll( '.mfw-level-capability' ).forEach( function ( el ) {
			el.checked = caps.indexOf( el.value ) !== -1;
		} );
		var discountCats = level.discount_category_ids || [];
		document.querySelectorAll( '.mfw-level-discount-category' ).forEach( function ( el ) {
			el.checked = discountCats.indexOf( parseInt( el.value, 10 ) ) !== -1;
		} );
		syncGroupCheckboxes();
		formTitle.textContent = <?php echo wp_json_encode( __( 'Edit Level', 'membership-for-woocommerce' ) ); ?>;
		addButton.textContent = <?php echo wp_json_encode( __( 'Update Level', 'membership-for-woocommerce' ) ); ?>;
		cancelButton.style.display = '';
		formTitle.scrollIntoView( { behavior: 'smooth' } );
	}

	document.getElementById( 'mfw-select-all-capabilities' ).addEventListener( 'click', function () {
		document.querySelectorAll( '.mfw-level-capability' ).forEach( function ( el ) { el.checked = true; } );
		syncGroupCheckboxes();
	} );

	document.getElementById( 'mfw-deselect-all-capabilities' ).addEventListener( 'click', function () {
		document.querySelectorAll( '.mfw-level-capability' ).forEach( function ( el ) { el.checked = false; } );
		syncGroupCheckboxes();
	} );

	document.querySelectorAll( '.mfw-select-all-group' ).forEach( function ( groupBox ) {
		groupBox.addEventListener( 'change', function ( e ) {
			var group = e.currentTarget.getAttribute( 'data-group' );
			document.querySelectorAll( '.mfw-level-capability[data-group="' + group + '"]' ).forEach( function ( el ) {
				el.checked = e.currentTarget.checked;
			} );
		} );
	} );

	document.querySelectorAll( '.mfw-level-capability' ).forEach( function ( el ) {
		el.addEventListener( 'change', syncGroupCheckboxes );
	} );

	addButton.addEventListener( 'click', function () {
		var name    = nameField.value;
		var description = descriptionField.value;
		var newRole = newRoleField.value;
		var role    = newRole || roleField.value;
		var rank          = rankField.value;
		var discount      = discountField.value;
		var expiryDays    = expiryDaysField.value;
		var selfSignup    = selfSignupField.checked ? 1 : 0;
		var status        = statusField.value;
		var caps          = Array.prototype.slice.call( document.querySelectorAll( '.mfw-level-capability:checked' ) ).map( function ( el ) { return el.value; } );
		var discountCats  = Array.prototype.slice.call( document.querySelectorAll( '.mfw-level-discount-category:checked' ) ).map( function ( el ) { return el.value; } );

		if ( ! name ) {
			return;
		}

		post( 'mfw_role_based_save_level', {
			id: idField.value,
			name: name,
			description: description,
			wp_role: role,
			rank: rank,
			discount_percent: discount,
			discount_category_ids: discountCats,
			expiry_days: expiryDays,
			self_signup_enabled: selfSignup,
			price: priceField.value,
			upgrade_spend_threshold: tierLadderField.checked ? upgradeThresholdField.value : 0,
			tier_ladder: tierLadderField.checked ? 1 : 0,
			status: status,
			capabilities: caps,
		} ).then( function () { window.location.reload(); } );
	} );

	cancelButton.addEventListener( 'click', resetForm );

	document.querySelectorAll( '.mfw-edit-level' ).forEach( function ( button ) {
		button.addEventListener( 'click', function ( e ) {
			var row = e.currentTarget.closest( 'tr' );
			populateForm( JSON.parse( row.getAttribute( 'data-level' ) ) );
		} );
	} );

	document.querySelectorAll( '.mfw-delete-level' ).forEach( function ( button ) {
		button.addEventListener( 'click', function ( e ) {
			post( 'mfw_role_based_delete_level', { id: e.currentTarget.getAttribute( 'data-id' ) } ).then( function () { window.location.reload(); } );
		} );
	} );

	var privateSite = document.getElementById( 'mfw-private-site' );
	if ( privateSite ) {
		privateSite.addEventListener( 'change', function ( e ) {
			post( 'mfw_role_based_save_general_settings', { private_site: e.target.checked ? 'on' : 'off' } );
		} );
	}

	var saveLoginDesign = document.getElementById( 'mfw-save-login-design' );
	if ( saveLoginDesign ) {
		saveLoginDesign.addEventListener( 'click', function () {
			var heading = document.getElementById( 'mfw-login-heading' ).value;
			var enabled = document.getElementById( 'mfw-login-notice-enabled' ).checked;
			var notice  = '';

			// wp_editor() may be in Visual (TinyMCE) or Text (plain textarea) mode —
			// read from whichever is actually active.
			if ( typeof tinymce !== 'undefined' && tinymce.get( 'mfw_login_notice' ) && ! tinymce.get( 'mfw_login_notice' ).isHidden() ) {
				notice = tinymce.get( 'mfw_login_notice' ).getContent();
			} else {
				var textarea = document.getElementById( 'mfw_login_notice' );
				notice = textarea ? textarea.value : '';
			}

			post( 'mfw_role_based_save_login_design', { heading: heading, notice: notice, notice_enabled: enabled ? 'on' : 'off' } )
				.then( function () { window.location.reload(); } );
		} );
	}

	var saveEmailTemplate = document.getElementById( 'mfw-save-email-template' );
	if ( saveEmailTemplate ) {
		saveEmailTemplate.addEventListener( 'click', function () {
			post( 'mfw_role_based_save_email_template', {
				header_color: document.getElementById( 'mfw-email-header-color' ).value,
				footer_text: document.getElementById( 'mfw-email-footer-text' ).value,
			} ).then( function () { window.location.reload(); } );
		} );
	}

	var saveWebhooks = document.getElementById( 'mfw-save-webhooks' );
	if ( saveWebhooks ) {
		saveWebhooks.addEventListener( 'click', function () {
			post( 'mfw_role_based_save_webhooks', {
				webhook_level_assigned: document.getElementById( 'mfw-webhook-level_assigned' ).value,
				webhook_level_revoked: document.getElementById( 'mfw-webhook-level_revoked' ).value,
				webhook_campaign_sent: document.getElementById( 'mfw-webhook-campaign_sent' ).value,
			} ).then( function () { window.location.reload(); } );
		} );
	}

	var campaignAudience = document.getElementById( 'mfw-campaign-audience' );
	var campaignLevelsPicker = document.getElementById( 'mfw-campaign-levels-picker' );
	if ( campaignAudience && campaignLevelsPicker ) {
		campaignAudience.addEventListener( 'change', function () {
			campaignLevelsPicker.style.display = <?php echo wp_json_encode( Mfw_Role_Based_Campaigns::AUDIENCE_LEVELS ); ?> === campaignAudience.value ? '' : 'none';
		} );
	}

	var campaignSendMode = document.getElementById( 'mfw-campaign-send-mode' );
	var campaignScheduledFields = document.getElementById( 'mfw-campaign-scheduled-fields' );
	var campaignDripFields = document.getElementById( 'mfw-campaign-drip-fields' );
	if ( campaignSendMode && campaignScheduledFields && campaignDripFields ) {
		campaignSendMode.addEventListener( 'change', function () {
			campaignScheduledFields.style.display = <?php echo wp_json_encode( Mfw_Role_Based_Campaigns::SEND_MODE_SCHEDULED ); ?> === campaignSendMode.value ? '' : 'none';
			campaignDripFields.style.display = <?php echo wp_json_encode( Mfw_Role_Based_Campaigns::SEND_MODE_DRIP ); ?> === campaignSendMode.value ? '' : 'none';
		} );
	}

	// --- Roles tab: tabbed + searchable capability picker, and role save/edit/clone/delete. ---
	(function () {
		var capTabs   = document.querySelectorAll( '.mfw-cap-picker__tab' );
		var capPanels = document.querySelectorAll( '.mfw-cap-picker__panel' );
		var capSearch = document.getElementById( 'mfw-role-cap-search' );

		if ( ! capTabs.length ) {
			return;
		}

		function activateCapTab( tabKey ) {
			capTabs.forEach( function ( tab ) {
				tab.classList.toggle( 'active', tab.getAttribute( 'data-cap-tab' ) === tabKey );
			} );
			capPanels.forEach( function ( panel ) {
				panel.classList.toggle( 'active', panel.getAttribute( 'data-cap-panel' ) === tabKey );
			} );
		}

		capTabs.forEach( function ( tab ) {
			tab.addEventListener( 'click', function () {
				activateCapTab( tab.getAttribute( 'data-cap-tab' ) );
			} );
		} );

		if ( capSearch ) {
			capSearch.addEventListener( 'input', function () {
				var query = capSearch.value.trim().toLowerCase();
				var activeTabHasMatch = false;
				var firstMatchTab = null;

				document.querySelectorAll( '.mfw-cap-picker__item' ).forEach( function ( item ) {
					var matches = ! query || item.getAttribute( 'data-cap-search' ).indexOf( query ) !== -1;
					item.classList.toggle( 'mfw-cap-picker__item--hidden', ! matches );

					if ( matches ) {
						var panel = item.closest( '.mfw-cap-picker__panel' );
						var panelKey = panel.getAttribute( 'data-cap-panel' );
						if ( panel.classList.contains( 'active' ) ) {
							activeTabHasMatch = true;
						}
						if ( ! firstMatchTab ) {
							firstMatchTab = panelKey;
						}
					}
				} );

				// If the current tab has nothing matching but another tab does, jump there
				// automatically — this is what the reference plugin's "N capability match
				// on other tabs" hint is solving for, done here by just switching instead.
				if ( query && ! activeTabHasMatch && firstMatchTab ) {
					activateCapTab( firstMatchTab );
				}
			} );
		}

		function resetCapPicker() {
			document.querySelectorAll( '.mfw-role-capability' ).forEach( function ( el ) { el.checked = false; } );
			if ( capSearch ) {
				capSearch.value = '';
				document.querySelectorAll( '.mfw-cap-picker__item' ).forEach( function ( item ) {
					item.classList.remove( 'mfw-cap-picker__item--hidden' );
				} );
			}
			activateCapTab( capTabs[0].getAttribute( 'data-cap-tab' ) );
		}

		document.querySelectorAll( '.mfw-cap-picker__select-all' ).forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				var group = button.getAttribute( 'data-target' );
				document.querySelectorAll( '.mfw-role-capability[data-group="' + group + '"]' ).forEach( function ( el ) { el.checked = true; } );
			} );
		} );

		document.querySelectorAll( '.mfw-cap-picker__deselect-all' ).forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				var group = button.getAttribute( 'data-target' );
				document.querySelectorAll( '.mfw-role-capability[data-group="' + group + '"]' ).forEach( function ( el ) { el.checked = false; } );
			} );
		} );

		var roleFormTitle  = document.getElementById( 'mfw-role-form-title' );
		var roleSlugField  = document.getElementById( 'mfw-role-slug' );
		var roleNameField  = document.getElementById( 'mfw-role-name' );
		var saveRoleButton = document.getElementById( 'mfw-save-role' );
		var cancelRoleButton = document.getElementById( 'mfw-cancel-edit-role' );

		function populateRoleForm( role ) {
			roleSlugField.value = role.slug;
			roleNameField.value = role.name;
			var caps = role.capabilities || [];
			document.querySelectorAll( '.mfw-role-capability' ).forEach( function ( el ) {
				el.checked = caps.indexOf( el.value ) !== -1;
			} );
			roleFormTitle.textContent = <?php echo wp_json_encode( __( 'Edit Role', 'membership-for-woocommerce' ) ); ?>;
			saveRoleButton.textContent = <?php echo wp_json_encode( __( 'Update Role', 'membership-for-woocommerce' ) ); ?>;
			cancelRoleButton.style.display = '';
			roleFormTitle.scrollIntoView( { behavior: 'smooth' } );
		}

		function resetRoleForm() {
			roleSlugField.value = '';
			roleNameField.value = '';
			resetCapPicker();
			roleFormTitle.textContent = <?php echo wp_json_encode( __( 'Add Role', 'membership-for-woocommerce' ) ); ?>;
			saveRoleButton.textContent = <?php echo wp_json_encode( __( 'Add Role', 'membership-for-woocommerce' ) ); ?>;
			cancelRoleButton.style.display = 'none';
		}

		document.querySelectorAll( '.mfw-edit-role' ).forEach( function ( button ) {
			button.addEventListener( 'click', function ( e ) {
				var row = e.currentTarget.closest( 'tr' );
				populateRoleForm( JSON.parse( row.getAttribute( 'data-role' ) ) );
			} );
		} );

		cancelRoleButton.addEventListener( 'click', resetRoleForm );

		saveRoleButton.addEventListener( 'click', function () {
			var name = roleNameField.value.trim();
			if ( ! name ) {
				return;
			}

			var slug = roleSlugField.value || name.toLowerCase().replace( /[^a-z0-9]+/g, '_' ).replace( /^_+|_+$/g, '' );
			var caps = Array.prototype.slice.call( document.querySelectorAll( '.mfw-role-capability:checked' ) ).map( function ( el ) { return el.value; } );

			post( 'mfw_role_based_save_role', { slug: slug, name: name, capabilities: caps } ).then( function () { window.location.reload(); } );
		} );

		document.querySelectorAll( '.mfw-clone-role' ).forEach( function ( button ) {
			button.addEventListener( 'click', function ( e ) {
				post( 'mfw_role_based_clone_role', { slug: e.currentTarget.getAttribute( 'data-slug' ) } ).then( function () { window.location.reload(); } );
			} );
		} );

		document.querySelectorAll( '.mfw-delete-role' ).forEach( function ( button ) {
			button.addEventListener( 'click', function ( e ) {
				if ( ! window.confirm( <?php echo wp_json_encode( __( 'Delete this role? This cannot be undone.', 'membership-for-woocommerce' ) ); ?> ) ) {
					return;
				}
				post( 'mfw_role_based_delete_role', { slug: e.currentTarget.getAttribute( 'data-slug' ) } ).then( function ( response ) {
					return response.json();
				} ).then( function ( json ) {
					if ( json && json.success ) {
						window.location.reload();
					} else if ( json && json.data && json.data.message ) {
						window.alert( json.data.message );
					}
				} );
			} );
		} );
	})();

	// Tab switching. Runs after everything above (including wp_editor()'s own TinyMCE
	// init script, which appears earlier in the page) has already executed, so the
	// editor always initializes fully visible before any tab gets hidden — hiding a
	// TinyMCE instance that was never visible is a well-known way to break its sizing.
	var tabLinks  = document.querySelectorAll( '.mfw-tab-link' );
	var tabPanels = document.querySelectorAll( '.mfw-tab-panel' );

	function activateTab( tabKey ) {
		tabLinks.forEach( function ( link ) {
			link.classList.toggle( 'active', link.getAttribute( 'data-tab' ) === tabKey );
		} );
		tabPanels.forEach( function ( panel ) {
			panel.style.display = panel.getAttribute( 'data-tab-panel' ) === tabKey ? '' : 'none';
		} );
		history.replaceState( null, '', '#' + tabKey );
	}

	tabLinks.forEach( function ( link ) {
		link.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			activateTab( link.getAttribute( 'data-tab' ) );
		} );
	} );

	var requestedTab = window.location.hash ? window.location.hash.substring( 1 ) : 'general';
	if ( ! document.querySelector( '.mfw-tab-panel[data-tab-panel="' + requestedTab + '"]' ) ) {
		requestedTab = 'general';
	}
	activateTab( requestedTab );
} )();
</script>
