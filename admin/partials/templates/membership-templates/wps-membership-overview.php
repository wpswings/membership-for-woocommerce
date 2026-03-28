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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mfw_overview_icon = MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/resources/icons/mfw12.png';
$mfw_feature_cards = array(
	array(
		'icon'  => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/resources/icons/mfw1.png',
		'title' => __( 'Flexible plan creation', 'membership-for-woocommerce' ),
		'desc'  => __( 'Create pricing plans, set durations, and define access rules for the right customer segment.', 'membership-for-woocommerce' ),
	),
	array(
		'icon'  => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/resources/icons/mfw5.png',
		'title' => __( 'Content and product restriction', 'membership-for-woocommerce' ),
		'desc'  => __( 'Control who can purchase products, access pages, and unlock member-only benefits across the store.', 'membership-for-woocommerce' ),
	),
	array(
		'icon'  => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/resources/icons/mfw10.png',
		'title' => __( 'Member lifecycle tracking', 'membership-for-woocommerce' ),
		'desc'  => __( 'Review activity, expiry, and membership history to manage renewals and retention with less effort.', 'membership-for-woocommerce' ),
	),
);

$mfw_benefit_cards = array(
	array(
		'icon'  => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/resources/icons/mfw14.png',
		'title' => __( 'Exclusive pricing', 'membership-for-woocommerce' ),
		'desc'  => __( 'Offer plan-based discounts, shipping perks, and member-only purchase advantages.', 'membership-for-woocommerce' ),
	),
	array(
		'icon'  => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/resources/icons/mfw16.png',
		'title' => __( 'Custom member journeys', 'membership-for-woocommerce' ),
		'desc'  => __( 'Build different buying and onboarding flows for members, trial users, and restricted audiences.', 'membership-for-woocommerce' ),
	),
	array(
		'icon'  => MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/resources/icons/mfw18.png',
		'title' => __( 'Operational visibility', 'membership-for-woocommerce' ),
		'desc'  => __( 'Use reports, exports, and member history views to keep operations predictable.', 'membership-for-woocommerce' ),
	),
);

?>
<div class="mfw-overview-page">
	<section class="mfw-overview-hero">
		<div class="mfw-overview-hero__icon">
			<img src="<?php echo esc_url( $mfw_overview_icon ); ?>" alt="<?php esc_attr_e( 'Membership icon', 'membership-for-woocommerce' ); ?>">
		</div>
		<h2><?php esc_html_e( 'Membership Management for WooCommerce', 'membership-for-woocommerce' ); ?></h2>
		<p><?php esc_html_e( 'Design recurring membership experiences, protect premium content, and manage member journeys from one workflow-oriented dashboard.', 'membership-for-woocommerce' ); ?></p>
	</section>

	<section class="mfw-overview-feature-grid">
		<?php foreach ( $mfw_feature_cards as $mfw_feature_card ) : ?>
			<article class="mfw-overview-feature-card">
				<img src="<?php echo esc_url( $mfw_feature_card['icon'] ); ?>" alt="">
				<h3><?php echo esc_html( $mfw_feature_card['title'] ); ?></h3>
				<p><?php echo esc_html( $mfw_feature_card['desc'] ); ?></p>
			</article>
		<?php endforeach; ?>
	</section>

	<section class="mfw-overview-support-strip">
		<strong><?php esc_html_e( 'Facing issues?', 'membership-for-woocommerce' ); ?></strong>
		<p><?php esc_html_e( 'We are ready to resolve your workflow and membership configuration problems.', 'membership-for-woocommerce' ); ?></p>
		<a href="https://wpswings.com/woocommerce-services/?utm_source=wpswings-membership&utm_medium=membership-pro-backend&utm_campaign=services" target="_blank" rel="noreferrer"><?php esc_html_e( 'Hire Us', 'membership-for-woocommerce' ); ?></a>
	</section>

	<section class="mfw-overview-benefits">
		<div class="mfw-overview-benefits__header">
			<h3><?php esc_html_e( 'Why teams use this plugin', 'membership-for-woocommerce' ); ?></h3>
			<p><?php esc_html_e( 'A few of the highest-impact capabilities available across the membership workflow.', 'membership-for-woocommerce' ); ?></p>
		</div>
		<div class="mfw-overview-benefits__grid">
			<?php foreach ( $mfw_benefit_cards as $mfw_benefit_card ) : ?>
				<article class="mfw-overview-benefit-card">
					<img src="<?php echo esc_url( $mfw_benefit_card['icon'] ); ?>" alt="">
					<h4><?php echo esc_html( $mfw_benefit_card['title'] ); ?></h4>
					<p><?php echo esc_html( $mfw_benefit_card['desc'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="mfw-overview-details">
		<div class="mfw-overview-details__copy">
			<h3><?php esc_html_e( 'Store owner outcomes', 'membership-for-woocommerce' ); ?></h3>
			<ul>
				<li><?php esc_html_e( 'Control content access and premium product visibility', 'membership-for-woocommerce' ); ?></li>
				<li><?php esc_html_e( 'Track complete customer history and membership lifecycle', 'membership-for-woocommerce' ); ?></li>
				<li><?php esc_html_e( 'Configure manual assignment, renewals, and role-based benefits', 'membership-for-woocommerce' ); ?></li>
				<li><?php esc_html_e( 'Use reports and exports for operations and support workflows', 'membership-for-woocommerce' ); ?></li>
				<?php do_action( 'wps_membership_li_to_overview' ); ?>
			</ul>
		</div>
		<div class="mfw-overview-details__media">
			<iframe src="https://www.youtube.com/embed/Yf0pa_Fgn5s" title="<?php esc_attr_e( 'Membership video overview', 'membership-for-woocommerce' ); ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		</div>
	</section>
</div>
