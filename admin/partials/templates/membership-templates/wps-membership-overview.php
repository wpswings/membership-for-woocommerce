<?php
/**
 * Provide a admin area view for the plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wpswings.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin/partials
 */

?>
<div class="wps-overview__wrapper">
	<div class="wps-overview__banner">
	<?php
	if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
		$check_licence = check_membership_pro_plugin_is_active();
		if ( $check_licence ) {
			?>
		<img src="<?php echo esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/Banner-pro.jpg' ); ?>" alt="banner-image">
			<?php
		}
	} else {
		?>
		<img src="<?php echo esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/resources/icons/org-banner.jpg' ); ?>" alt="banner-image">
		<?php
	}
	?>
	</div>
	<div class="wps-overview__content">
		<div class="wps-overview__content-description">
			<h2><?php echo esc_html_e( 'What Is Membership For WooCommerce?', 'membership-for-woocommerce' ); ?></h2>
			<p>
				<?php
				esc_html_e(
					'Membership for WooCommerce allows you to create membership plans for a segment of customers, thereby
					imposing limitations on your certain services or content. Memberships make it easy to create email
					lists where you can offer users special coupons and discount updates.',
					'membership-for-woocommerce'
				);
				?>
			</p>
			<h3><?php esc_html_e( 'With our Membership for WooCommerce plugin, as a store owner you get:', 'membership-for-woocommerce' ); ?></h3>
			<ul class="wps-overview__features">
				<li><?php esc_html_e( 'Control content access', 'membership-for-woocommerce' ); ?></li>
				<li><?php esc_html_e( 'See complete customer history', 'membership-for-woocommerce' ); ?></li>
				<li><?php esc_html_e( 'Quick preview section for Membership plans on the plans listing page', 'membership-for-woocommerce' ); ?></li>
				<li><?php esc_html_e( 'A different status selection like pending, on hold, or completed as per their payment and plan expiry', 'membership-for-woocommerce' ); ?></li>
				<li><?php esc_html_e( 'Manual assignment of membership to a customer', 'membership-for-woocommerce' ); ?></li>
				<?php
				/**
				 * Hook for li to overview.
				 *
				 * @since 1.0.0
				 */
				do_action( 'wps_membership_li_to_overview' );
				?>
			</ul>
			<iframe width="100%" height="auto" src="https://www.youtube.com/embed/Yf0pa_Fgn5s" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" poster="" allowfullscreen class="mfw_overview-video"></iframe>
		</div>
		<h2> 
		<?php
		if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
			$check_licence = check_membership_pro_plugin_is_active();
			if ( $check_licence ) {
				esc_html_e( 'The Free And Pro Plugin Benefits', 'membership-for-woocommerce' );
			}
		} else {
			esc_html_e( 'The Free Plugin Benefits', 'membership-for-woocommerce' );
		}
		?>
		</h2>
		<div class="wps-overview__keywords">
			<div class="wps-overview__keywords-item">
				<div class="wps-overview__keywords-card">
					<div class="wps-overview__keywords-image">
						<img src="<?php echo esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/Icons_1_Complete Customer History.jpg' ); ?>" alt="Complete Customer History image">
					</div>
					<div class="wps-overview__keywords-text">
						<h3 class="wps-overview__keywords-heading"><?php echo esc_html_e( 'Complete Customer History', 'membership-for-woocommerce' ); ?></h3>
						<p class="wps-overview__keywords-description">
							<?php
							esc_html_e( 'Admin gets a quick preview section for Membership plans on the plans listing page. The users too can see their history in the ‘My Account’ section with entire details of their membership plans.', 'membership-for-woocommerce' );
							?>
						</p>
					</div>
				</div>
			</div>
			<div class="wps-overview__keywords-item">
				<div class="wps-overview__keywords-card">
					<div class="wps-overview__keywords-image">
						<img src="<?php echo esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/Icons_1_Membership Details.jpg' ); ?>" alt="Membership Details image">
					</div>
					<div class="wps-overview__keywords-text">
						<h3 class="wps-overview__keywords-heading"><?php echo esc_html_e( 'Membership Details', 'membership-for-woocommerce' ); ?></h3>
						<p class="wps-overview__keywords-description">
							<?php
							esc_html_e( 'Admin can offer products/categories in a membership plan. Those products and categories can only be accessible to users if they have purchased the plan. Membership Details Tab on My Accounts Page has all the details of the plan.', 'membership-for-woocommerce' );
							?>
						</p>
					</div>
				</div>
			</div>
			<div class="wps-overview__keywords-item">
				<div class="wps-overview__keywords-card">
					<div class="wps-overview__keywords-image">
						<img src="<?php echo esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/Icons_1_Data Export.jpg' ); ?>" alt="Data Export image">
					</div>
					<div class="wps-overview__keywords-text">
						<h3 class="wps-overview__keywords-heading"><?php echo esc_html_e( 'Data Export', 'membership-for-woocommerce' ); ?></h3>
						<p class="wps-overview__keywords-description">
							<?php
							esc_html_e( 'Admin can effortlessly export all the membership plans along with the user data and settings. He can also import the details of all members as a CSV file.', 'membership-for-woocommerce' );
							?>
						</p>
					</div>
				</div>
			</div>
			<div class="wps-overview__keywords-item">
				<div class="wps-overview__keywords-card">
					<div class="wps-overview__keywords-image">
						<img src="<?php echo esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/Icons_1_Perfectly Neat Shortcodes For Customization.jpg' ); ?>" alt="Perfectly Neat Shortcodes For Customization image">
					</div>
					<div class="wps-overview__keywords-text">
						<h3 class="wps-overview__keywords-heading"><?php echo esc_html_e( 'Perfectly Neat Shortcodes For Customization', 'membership-for-woocommerce' ); ?></h3>
						<p class="wps-overview__keywords-description">
							<?php
							esc_html_e( 'The admin is provided with a neat collection of shortcodes that work not only on the purchase of the default membership plan but also on the custom page. These shortcodes let admin to design their plan page at ease.', 'membership-for-woocommerce' );
							?>
						</p>
					</div>
				</div>
			</div>
			<div class="wps-overview__keywords-item">
				<div class="wps-overview__keywords-card">
					<div class="wps-overview__keywords-image">
						<img src="<?php echo esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/Icons_1_User Cart Total Discount.jpg' ); ?>" alt="User Cart Total Discount image">
					</div>
					<div class="wps-overview__keywords-text">
						<h3 class="wps-overview__keywords-heading"><?php echo esc_html_e( 'User Cart Total Discount', 'membership-for-woocommerce' ); ?></h3>
						<p class="wps-overview__keywords-description">
							<?php
							esc_html_e( "Admin can grant discounts to the customers on total cart value. Free shipping option is also available as per their membership plans. The discount can be a fixed amount or percentage discount based on the admin's choice.", 'membership-for-woocommerce' );
							?>
						</p>
					</div>
				</div>
			</div>
			<?php
			/**
			 * Action for add icons.
			 *
			 * @since 1.0.0
			 */
			do_action( 'wps_membership_add_icons_with_desc' );
			?>
		</div>
	</div>
</div>
