<?php
/**
 * Overview tab redesign template.
 *
 * @package Membership_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wps-overview__wrapper mfw-overview">
	<section class="mfw-overview__hero-card">
		<div class="mfw-overview__hero-badge">MFW</div>
		<p class="mfw-overview__eyebrow"><?php esc_html_e( 'Overview', 'membership-for-woocommerce' ); ?></p>
		<h2><?php esc_html_e( 'Membership For WooCommerce', 'membership-for-woocommerce' ); ?></h2>
		<p class="mfw-overview__lead">
			<?php esc_html_e( 'Membership for WooCommerce allows you to create membership plans for a segment of customers, imposing limitations on services or content while helping you engage users with special coupons and discount updates.', 'membership-for-woocommerce' ); ?>
		</p>
		<p class="mfw-overview__lead">
			<?php esc_html_e( 'With our Membership for WooCommerce plugin, as a store owner you get complete control over member access, discounts, history, and plan management.', 'membership-for-woocommerce' ); ?>
		</p>

		<h3 class="mfw-overview__section-title"><?php esc_html_e( 'The Free And Pro Plugin Benefits', 'membership-for-woocommerce' ); ?></h3>

		<div class="mfw-overview__features-grid">
			<article class="mfw-overview__feature-card">
				<div class="mfw-overview__feature-icon"><span class="dashicons dashicons-id"></span></div>
				<h4><?php esc_html_e( 'Complete Customer History', 'membership-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Admin gets a quick preview section for membership plans on the plans listing page. Users can also view their full membership history from My Account.', 'membership-for-woocommerce' ); ?></p>
			</article>

			<article class="mfw-overview__feature-card">
				<div class="mfw-overview__feature-icon"><span class="dashicons dashicons-admin-users"></span></div>
				<h4><?php esc_html_e( 'Membership Details', 'membership-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Admin can offer products or categories in a membership plan. Membership Details on My Account shows complete plan information to users.', 'membership-for-woocommerce' ); ?></p>
			</article>

			<article class="mfw-overview__feature-card">
				<div class="mfw-overview__feature-icon"><span class="dashicons dashicons-download"></span></div>
				<h4><?php esc_html_e( 'Data Export', 'membership-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Export all membership plans with user data and settings, and import member details in CSV format when needed.', 'membership-for-woocommerce' ); ?></p>
			</article>

			<article class="mfw-overview__feature-card">
				<div class="mfw-overview__feature-icon"><span class="dashicons dashicons-editor-code"></span></div>
				<h4><?php esc_html_e( 'Perfectly Neat Shortcodes For Customization', 'membership-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Use shortcodes on default and custom pages to design and control membership plan presentation with flexibility.', 'membership-for-woocommerce' ); ?></p>
			</article>

			<article class="mfw-overview__feature-card">
				<div class="mfw-overview__feature-icon"><span class="dashicons dashicons-cart"></span></div>
				<h4><?php esc_html_e( 'User Cart Total Discount', 'membership-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Grant fixed or percentage discounts on cart totals and also provide free shipping options based on membership plans.', 'membership-for-woocommerce' ); ?></p>
			</article>

			<article class="mfw-overview__feature-card">
				<div class="mfw-overview__feature-icon"><span class="dashicons dashicons-media-document"></span></div>
				<h4><?php esc_html_e( 'Membership Account Details And Logs', 'membership-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Users can view current and previous membership plans, dates, and accessible content from My Account while admins get logs for tracking.', 'membership-for-woocommerce' ); ?></p>
			</article>

			<article class="mfw-overview__feature-card">
				<div class="mfw-overview__feature-icon"><span class="dashicons dashicons-archive"></span></div>
				<h4><?php esc_html_e( 'Select Membership Product', 'membership-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Assign different products to different memberships and configure plan-based product-level access and discounts.', 'membership-for-woocommerce' ); ?></p>
			</article>

			<article class="mfw-overview__feature-card">
				<div class="mfw-overview__feature-icon"><span class="dashicons dashicons-groups"></span></div>
				<h4><?php esc_html_e( 'Combine Membership Plan Benefits', 'membership-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Allow customers to combine benefits of multiple plans so selected plan advantages can work together.', 'membership-for-woocommerce' ); ?></p>
			</article>

			<article class="mfw-overview__feature-card">
				<div class="mfw-overview__feature-icon"><span class="dashicons dashicons-admin-page"></span></div>
				<h4><?php esc_html_e( 'Access To Membership Pages And Posts', 'membership-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Control page and post access per membership plan and restrict non-members from protected content.', 'membership-for-woocommerce' ); ?></p>
			</article>

			<article class="mfw-overview__feature-card">
				<div class="mfw-overview__feature-icon"><span class="dashicons dashicons-lock"></span></div>
				<h4><?php esc_html_e( 'Access To Membership Products', 'membership-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Include selected product categories and tags in specific membership plans for targeted product access.', 'membership-for-woocommerce' ); ?></p>
			</article>

			<article class="mfw-overview__feature-card">
				<div class="mfw-overview__feature-icon"><span class="dashicons dashicons-admin-settings"></span></div>
				<h4><?php esc_html_e( 'Override Membership Plan Access', 'membership-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Override product-level settings to provide immediate or delayed membership access and regulate discount values for selected plans.', 'membership-for-woocommerce' ); ?></p>
			</article>

			<article class="mfw-overview__feature-card">
				<div class="mfw-overview__feature-icon"><span class="dashicons dashicons-chart-bar"></span></div>
				<h4><?php esc_html_e( 'Better Membership Reports', 'membership-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Track active, pending, and expired memberships with improved reporting visibility for admins.', 'membership-for-woocommerce' ); ?></p>
			</article>

			<article class="mfw-overview__feature-card">
				<div class="mfw-overview__feature-icon"><span class="dashicons dashicons-filter"></span></div>
				<h4><?php esc_html_e( 'Memberships Sorting', 'membership-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Filter users by membership plans on listing screens and sort memberships by year for easier administration.', 'membership-for-woocommerce' ); ?></p>
			</article>
		</div>

		<div class="mfw-overview__support-strip">
			<div>
				<h4><?php esc_html_e( 'Need help with Membership setup?', 'membership-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Our team can help you configure membership plans, content restriction, discounts, and lifecycle workflows.', 'membership-for-woocommerce' ); ?></p>
			</div>
			<a href="<?php echo esc_url( 'https://wpswings.com/submit-query/?utm_source=wpswings-membership-support&utm_medium=membership-org-backend&utm_campaign=support' ); ?>" target="_blank" class="mfw-overview__support-btn"><?php esc_html_e( 'Contact Support', 'membership-for-woocommerce' ); ?></a>
		</div>
	</section>
</div>
