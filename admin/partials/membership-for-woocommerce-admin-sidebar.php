<?php
/**
 * Help / services sidebar shared by the product-based settings dashboard and the
 * role-based membership page, so both admin screens show the same right-hand rail.
 *
 * @link  https://wpswings.com/
 * @since 3.1.3
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$mfw_doc_url      = 'https://docs.wpswings.com/membership-for-woocommerce/?utm_source=wpswings-membership-doc&utm_medium=membership-org-backend&utm_campaign=documentation';
$mfw_support_url  = 'https://wpswings.com/submit-query/?utm_source=wpswings-membership-support&utm_medium=membership-org-backend&utm_campaign=support';
$mfw_video_url    = 'https://www.youtube.com/watch?v=DHxo6ojvKmc';
$mfw_plugins_url  = 'https://wpswings.com/product-category/woocommerce-plugins/?utm_source=wpswings-membership&utm_medium=membership-org-backend&utm_campaign=more-plugins';
$mfw_contact_url  = 'https://wpswings.com/submit-query/?utm_source=wpswings-membership-support&utm_medium=membership-org-backend&utm_campaign=contact-us';
$mfw_services_url = 'https://wpswings.com/woocommerce-services/?utm_source=wpswings-membership&utm_medium=membership-pro-backend&utm_campaign=services';
$mfw_services_landing_url = class_exists( 'Membership_For_Woocommerce_Talk_To_Expert_Form' ) ? Membership_For_Woocommerce_Talk_To_Expert_Form::wps_mfw_get_services_landing_url() : $mfw_services_url;
$mfw_marketing_rows = array(
	array(
		'icon'        => 'seo',
		'title'       => esc_html__( 'SEO Services', 'membership-for-woocommerce' ),
		'description' => esc_html__( 'Improve rankings & organic traffic', 'membership-for-woocommerce' ),
	),
	array(
		'icon'        => 'ads',
		'title'       => esc_html__( 'Google Ads Setup And G4 Setup', 'membership-for-woocommerce' ),
		'description' => esc_html__( 'Run profitable ad campaigns', 'membership-for-woocommerce' ),
	),
	array(
		'icon'        => 'speed',
		'title'       => esc_html__( 'Speed Optimization', 'membership-for-woocommerce' ),
		'description' => esc_html__( 'Faster store, happier customers', 'membership-for-woocommerce' ),
	),
	array(
		'icon'        => 'dev',
		'title'       => esc_html__( 'WooCommerce Development Services', 'membership-for-woocommerce' ),
		'description' => esc_html__( 'Custom Solution For your store needs', 'membership-for-woocommerce' ),
	),
);
?>
				<aside class="mfw-redesign-main__sidebar">
					<div class="mfw-redesign-main__sidebar-card">
						<h3><?php esc_html_e( 'Need help with this plugin?', 'membership-for-woocommerce' ); ?></h3>
						<a href="<?php echo esc_url( $mfw_video_url ); ?>" target="_blank"><?php esc_html_e( 'Watch Video', 'membership-for-woocommerce' ); ?></a>
						<a href="<?php echo esc_url( $mfw_doc_url ); ?>" target="_blank"><?php esc_html_e( 'Documentation', 'membership-for-woocommerce' ); ?></a>
						<a href="<?php echo esc_url( $mfw_support_url ); ?>" target="_blank"><?php esc_html_e( 'Support', 'membership-for-woocommerce' ); ?></a>
					</div>
					<div class="mfw-redesign-main__sidebar-card mfw-redesign-main__sidebar-card--services">
						<div class="mfw-service-rail-card__header">
							<h3><?php esc_html_e( 'Grow Your Store With Our Services', 'membership-for-woocommerce' ); ?></h3>
							<span class="mfw-service-rail-card__badge" aria-hidden="true"></span>
						</div>
						<p><?php esc_html_e( "Expert solutions to boost your store's performance.", 'membership-for-woocommerce' ); ?></p>
						<div class="mfw-service-rail">
							<?php foreach ( $mfw_marketing_rows as $mfw_marketing_row ) : ?>
								<a href="<?php echo esc_url( $mfw_services_landing_url ); ?>" target="_blank" rel="noopener noreferrer" class="mfw-service-rail__item">
									<span class="mfw-service-rail__icon mfw-service-rail__icon--<?php echo esc_attr( $mfw_marketing_row['icon'] ); ?>" aria-hidden="true"></span>
									<span class="mfw-service-rail__content">
										<span class="mfw-service-rail__title"><?php echo esc_html( $mfw_marketing_row['title'] ); ?></span>
										<span class="mfw-service-rail__description"><?php echo esc_html( $mfw_marketing_row['description'] ); ?></span>
									</span>
									<span class="mfw-service-rail__arrow" aria-hidden="true">&rsaquo;</span>
								</a>
							<?php endforeach; ?>
						</div>
						<button type="button" class="mfw-service-rail__cta" data-mfw-open-expert-modal><?php esc_html_e( 'Talk to an Expert', 'membership-for-woocommerce' ); ?></button>
						<div class="mfw-service-rail__footer"><?php esc_html_e( 'Services by WP Swings', 'membership-for-woocommerce' ); ?></div>
					</div>
					<div class="mfw-redesign-main__sidebar-card mfw-redesign-main__sidebar-card--accent">
						<h3><?php esc_html_e( 'Still facing problems?', 'membership-for-woocommerce' ); ?></h3>
						<p><?php esc_html_e( 'Get direct help for setup, styling, and integration issues from the support team.', 'membership-for-woocommerce' ); ?></p>
						<a href="<?php echo esc_url( $mfw_contact_url ); ?>" target="_blank" class="mfw-redesign-main__sidebar-cta"><?php esc_html_e( 'Contact Us', 'membership-for-woocommerce' ); ?></a>
					</div>
					<div class="mfw-redesign-main__sidebar-card">
						<h3><?php esc_html_e( 'Explore more plugins', 'membership-for-woocommerce' ); ?></h3>
						<p><?php esc_html_e( 'Discover additional WooCommerce and automation plugins from the same product family.', 'membership-for-woocommerce' ); ?></p>
						<a href="<?php echo esc_url( $mfw_plugins_url ); ?>" target="_blank"><?php esc_html_e( 'View More Plugins', 'membership-for-woocommerce' ); ?></a>
						<a href="<?php echo esc_url( $mfw_services_url ); ?>" target="_blank"><?php esc_html_e( 'Services', 'membership-for-woocommerce' ); ?></a>
					</div>
				</aside>
