<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wpswings.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin/partials
 */

/**
 * Template Name: WPS Membership For Woocommerce Template.
 * This template will only display the content you entered in the page editor
 */

// Exit is accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

$wps_mfw_selected_template = get_option( 'wps_membership_plan_page_temp', '' );
$wps_mfw_plan_count_obj    = wp_count_posts( 'wps_cpt_membership' );
$wps_mfw_active_plans      = ! empty( $wps_mfw_plan_count_obj->publish ) ? absint( $wps_mfw_plan_count_obj->publish ) : 0;
$wps_mfw_home_url          = home_url( '/' );
$wps_mfw_shop_url          = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
$wps_mfw_shop_url          = ! empty( $wps_mfw_shop_url ) ? $wps_mfw_shop_url : $wps_mfw_home_url;
$wps_mfw_template_class    = ! empty( $wps_mfw_selected_template ) ? sanitize_html_class( $wps_mfw_selected_template ) : 'default';
?>

<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php wp_head(); ?>
	</head>
	<?php
	// show header on membership page.
	if ( 'on' === get_option( 'wps_show_header_on_membership_page' ) ) {
		get_header();
	}
	?>
	<body class="<?php echo esc_attr( 'wps_mfw_template_layout_page wps_mfw_template_v4_page wps_mfw_selected_' . $wps_mfw_template_class ); ?>">
		<?php
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open();
		}
		?>
				<div class="<?php echo esc_attr( 'wps_mfw_template_layout wps_mfw_template_v4 wps_mfw_selected_' . $wps_mfw_template_class ); ?>">
				<div class="wps_mfw_template_v4_stage">
					<section class="wps_mfw_template_v4_hero" aria-label="<?php esc_attr_e( 'Membership Plans Overview', 'membership-for-woocommerce' ); ?>">
						<?php if ( 'temp1' === $wps_mfw_template_class ) : ?>
							<span class="dashicons dashicons-awards wps_mfw_template_v4_hero_icon" aria-hidden="true"></span>
						<?php endif; ?>
						<div class="wps_mfw_template_v4_hero_card">
							<div class="wps_mfw_template_v4_hero_grid">
								<div class="wps_mfw_template_v4_hero_content">
									<p class="wps_mfw_template_v4_eyebrow"><?php esc_html_e( 'Membership Plans', 'membership-for-woocommerce' ); ?></p>
									<h1 class="wps_mfw_template_v4_title"><?php esc_html_e( 'Choose the plan that fits your store strategy', 'membership-for-woocommerce' ); ?></h1>
									<p class="wps_mfw_template_v4_desc"><?php esc_html_e( 'Compare plan value, member perks, and access rules in one place. Each option is purchase-ready and built to route customers through the normal WooCommerce checkout flow.', 'membership-for-woocommerce' ); ?></p>
								</div>
								<div class="wps_mfw_template_v4_stat">
									<p class="wps_mfw_template_v4_stat_title">
										<strong><?php echo esc_html( $wps_mfw_active_plans ); ?></strong>
										<span><?php esc_html_e( 'Active Plans', 'membership-for-woocommerce' ); ?></span>
									</p>
									<p class="wps_mfw_template_v4_stat_desc"><?php esc_html_e( 'Use the plan details panel on each card to review duration, discounts, access logic, and included benefits before checkout.', 'membership-for-woocommerce' ); ?></p>
								</div>
							</div>
							<div class="wps_mfw_template_v4_actions">
								<a class="wps_mfw_template_v4_action_home" href="<?php echo esc_url( $wps_mfw_home_url ); ?>"><?php esc_html_e( 'Back to Home', 'membership-for-woocommerce' ); ?></a>
								<a class="wps_mfw_template_v4_action_shop" href="<?php echo esc_url( $wps_mfw_shop_url ); ?>"><?php esc_html_e( 'Go to Shop', 'membership-for-woocommerce' ); ?></a>
							</div>
						</div>
					</section>
					<div class="wps_mfw_template_v4_content">
						<?php
						while ( have_posts() ) :
							the_post();
							the_content();
						endwhile;
						?>
					</div>
				</div>
			</div>
		<?php
		?>
		<?php wp_footer(); ?>
	</body>
	<?php
	// show footer on membership page.
	if ( 'on' === get_option( 'wps_show_footer_on_membership_page' ) ) {
		get_footer();
	}
	?>
</html>
