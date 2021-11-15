<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

?>

<!-- Heading start -->
<div class="mwb_membership_shortcodes">
	<h2><?php esc_html_e( 'Membership Shortcodes', 'membership-for-woocommerce' ); ?></h2>
</div>
<!-- Heading end. -->

<!-- Shortcodes start -->
<div class="mwb_membership_table mwb_membership_shortcodes_table">
	<table class="form-table mwb_membership_plan_shortcodes">
		<tbody>

			<!-- Membership Action Shortcodes start. -->
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label><?php esc_html_e( 'Membership Action Shortcodes', 'membership-for-woocommerce' ); ?></label>
				</th>

				<td class="forminp forminp-text">
					<div class="mwb_membership_shortcode_div">
						<div class="mwb_membership_shortcode">
							<span class="mwb_membership_shortcode_title"><?php esc_html_e( 'Buy Now &rarr;', 'membership-for-woocommerce' ); ?></span>
							<span class="mwb_membership_shortcode_content"><?php echo esc_html__( '[mwb_membership_buy_now]' ); ?></span>
							<?php
							$shortcode_desc = esc_html__( 'This shortcode only returns the buy now button. Use it as [mwb_membership_buy_now plan_id=your plan ID].', 'membership-for-woocommerce' );
							 $instance = Membership_For_Woocommerce_Global_Functions::get();
							 $instance->tool_tip( $shortcode_desc );
							?>
						</div>
						
					</div>

					<div class="mwb_membership_shortcode_div">
						<div class="mwb_membership_shortcode">
							<span class="mwb_membership_shortcode_title"><?php esc_html_e( 'No Thanks &rarr;', 'membership-for-woocommerce' ); ?></span>
							<span class="mwb_membership_shortcode_content"><?php echo esc_html__( '[mwb_membership_no]' ); ?></span>
							<?php
							$shortcode_desc = esc_html__( 'This shortcode only returns no thanks button. Use it as [mwb_membership_no].', 'membership-for-woocommerce' );
							$instance->tool_tip( $shortcode_desc );
							?>
						</div>

					</div>
				</td>

			</tr>
			<!-- Membership Action Shortcodes End. -->

			<!-- Membership Plan Shortcodes Start. -->
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label><?php esc_html_e( 'Membership Plan Shortcodes', 'membership-for-woocommerce' ); ?></label>
				</th>

				<td class="forminp forminp-text">
					<div class="mwb_membership_shortcode_div">
						<div class="mwb_membership_shortcode">
							<span class="mwb_membership_shortcode_title"><?php esc_html_e( 'Membership Plan Title &rarr;', 'membership-for-woocommerce' ); ?></span>
							<span class="mwb_membership_shortcode_content"><?php echo esc_html__( '[mwb_membership_title_name]' ); ?></span>
							<?php
							$shortcode_desc = esc_html__( 'This shortcode returns the title of Membership Plan. Use it as [mwb_membership_title_name plan_id=your plan ID]', 'membership-for-woocommerce' );
							 $instance->tool_tip( $shortcode_desc );
							?>
						</div>
					</div>

					<div class="mwb_membership_shortcode_div">
						<div class="mwb_membership_shortcode">
							<span class="mwb_membership_shortcode_title"><?php esc_html_e( 'Membership Plan Price &rarr;', 'membership-for-woocommerce' ); ?></span>
							<span class="mwb_membership_shortcode_content"><?php echo esc_html__( '[mwb_membership_price]' ); ?></span>
							<?php
							$shortcode_desc = esc_html__( 'This shortcode returns the price of Membership Plan. Use it as [mwb_membership_price plan_id=your plan ID]', 'membership-for-woocommerce' );
							 $instance->tool_tip( $shortcode_desc );
							?>
						</div>
					</div>

					<div class="mwb_membership_shortcode_div">
						<div class="mwb_membership_shortcode">
							<span class="mwb_membership_shortcode_title"><?php esc_html_e( 'Membership Plan Desc &rarr;', 'membership-for-woocommerce' ); ?></span>
							<span class="mwb_membership_shortcode_content"><?php echo esc_html__( '[mwb_membership_desc_data]' ); ?></span>
							<?php
							$shortcode_desc = esc_html__( 'This shortcode returns the description of Membership Plan. Use it as [mwb_membership_desc_data plan_id=your plan ID]', 'membership-for-woocommerce' );
							 $instance->tool_tip( $shortcode_desc );
							?>
						</div>
					</div>

				</td>

			</tr>
			<!-- Membership Plan Shortcodes End. -->
		</tbody>

	</table>

</div>
<!-- Shortcodes end. -->

