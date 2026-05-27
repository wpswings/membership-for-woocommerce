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

if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

$instance = Membership_For_Woocommerce_Global_Functions::get();

$mfw_shortcode_groups = array(
	array(
		'title'  => esc_html__( 'Membership Action Shortcodes', 'membership-for-woocommerce' ),
		'layout' => 'split',
		'items'  => array(
			array(
				'label'       => esc_html__( 'Buy Now', 'membership-for-woocommerce' ),
				'code'        => '[wps_membership_buy_now plan_id=278]',
				'description' => esc_html__( 'This shortcode only returns the buy now button. Use it as [wps_membership_buy_now plan_id=your plan ID].', 'membership-for-woocommerce' ),
			),
			array(
				'label'       => esc_html__( 'No Thanks', 'membership-for-woocommerce' ),
				'code'        => '[wps_membership_no]',
				'description' => esc_html__( 'This shortcode only returns no thanks button. Use it as [wps_membership_no].', 'membership-for-woocommerce' ),
			),
		),
	),
	array(
		'title'  => esc_html__( 'Membership Plan Shortcodes', 'membership-for-woocommerce' ),
		'layout' => 'split',
		'items'  => array(
			array(
				'label'       => esc_html__( 'Membership Plan Title', 'membership-for-woocommerce' ),
				'code'        => '[wps_membership_title_name plan_id=278]',
				'description' => esc_html__( 'This shortcode returns the title of Membership Plan. Use it as [wps_membership_title_name plan_id=your plan ID]', 'membership-for-woocommerce' ),
			),
			array(
				'label'       => esc_html__( 'Membership Plan Price', 'membership-for-woocommerce' ),
				'code'        => '[wps_membership_price plan_id=278]',
				'description' => esc_html__( 'This shortcode returns the price of Membership Plan. Use it as [wps_membership_price plan_id=your plan ID]', 'membership-for-woocommerce' ),
			),
			array(
				'label'       => esc_html__( 'Membership Plan Desc', 'membership-for-woocommerce' ),
				'code'        => '[wps_membership_desc_data plan_id=278]',
				'description' => esc_html__( 'This shortcode returns the description of Membership Plan. Use it as [wps_membership_desc_data plan_id=your plan ID]', 'membership-for-woocommerce' ),
			),
		),
	),
	array(
		'title'  => esc_html__( 'Membership Registration Shortcodes', 'membership-for-woocommerce' ),
		'layout' => 'full',
		'items'  => array(
			array(
				'label'       => esc_html__( 'Membership Registration Form', 'membership-for-woocommerce' ),
				'code'        => '[wps_membership_registration_form]',
				'description' => esc_html__( 'This shortcode returns the Membership Registration Form. Use it as [wps_membership_registration_form]', 'membership-for-woocommerce' ),
			),
		),
	),
);
?>

<div class="wps_membership_shortcodes mfw-shortcodes-view">
	<div class="mfw-shortcodes-view__header">
		<div class="mfw-shortcodes-view__title-wrap">
			<h2><?php esc_html_e( 'Membership Shortcodes', 'membership-for-woocommerce' ); ?></h2>
			<p><?php esc_html_e( 'Ready-to-use snippets for plans, registration flow, and action buttons. Copy and paste where needed.', 'membership-for-woocommerce' ); ?></p>
		</div>
		<span class="mfw-shortcodes-view__meta"><?php esc_html_e( 'Quick Embed Library', 'membership-for-woocommerce' ); ?></span>
	</div>

	<div class="mfw-shortcodes-grid">
		<?php foreach ( $mfw_shortcode_groups as $mfw_shortcode_group ) : ?>
			<section class="mfw-shortcodes-card <?php echo 'full' === $mfw_shortcode_group['layout'] ? 'mfw-shortcodes-card--full' : ''; ?>">
				<div class="mfw-shortcodes-card__head">
					<h3><?php echo esc_html( $mfw_shortcode_group['title'] ); ?></h3>
					<span class="mfw-shortcodes-card__badge">
						<?php
						/* translators: %d: number of shortcodes in section. */
						echo esc_html( sprintf( _n( '%d shortcode', '%d shortcodes', count( $mfw_shortcode_group['items'] ), 'membership-for-woocommerce' ), count( $mfw_shortcode_group['items'] ) ) );
						?>
					</span>
				</div>
				<div class="mfw-shortcodes-list">
					<?php foreach ( $mfw_shortcode_group['items'] as $mfw_shortcode_item ) : ?>
						<div class="mfw-shortcodes-item">
							<div class="mfw-shortcodes-item__content">
								<span class="mfw-shortcodes-item__title"><?php echo esc_html( $mfw_shortcode_item['label'] ); ?></span>
								<code class="mfw-shortcodes-item__code"><?php echo esc_html( $mfw_shortcode_item['code'] ); ?></code>
							</div>
							<div class="mfw-shortcodes-item__help">
								<?php $instance->tool_tip( $mfw_shortcode_item['description'] ); ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endforeach; ?>
	</div>

	<div class="mfw-shortcodes-extension">
		<?php
		/**
		 * Hook to fetch html from pro.
		 *
		 * @since 1.0.0
		 */
		do_action( 'wps_membership_column_wise_template_shortcode', $instance );
		?>
	</div>
</div>
