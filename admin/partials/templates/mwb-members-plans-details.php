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

// Exit is accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

$plan = get_post_meta( $post->ID, 'plan_obj', true );
echo '<pre>'; print_r( $plan ); echo '</pre>';
?>
<!-- Members metabox start -->
<div class="members_plans_details">

	<div class="mwb_members_plans">
		<table class="form-table">
			<tr>
				<th><label><?php esc_html_e( 'Title', 'membership-for-woocommerce' ); ?></label></th>
				<td>
					<?php echo esc_html( $plan['post_title'] ); ?>
				</td>
			</tr>

			<tr>
				<th><label><?php esc_html_e( 'Price', 'membership-for-woocommerce' ); ?></label></th>
				<td>
					<?php echo sprintf( ' %s %s ', esc_html( get_woocommerce_currency() ), esc_html( $plan['mwb_membership_plan_price'][0] ) ); ?>
				</td>
			</tr>

			<tr>
				<th><label><?php esc_html_e( 'Description', 'membership-for-woocommerce' ); ?></label></th>
				<td>
					<?php echo esc_html( $plan['post_content'] ); ?>
				</td>
			</tr>

			<tr>
				<th><label><?php esc_html_e( 'Plan Type', 'membership-for-woocommerce' ); ?></label></th>
				<td>
					<?php echo esc_html( $plan['mwb_membership_plan_name_access_type'][0] ); ?>
				</td>
			</tr>

			<?php
			switch ( $plan['mwb_membership_plan_name_access_type'][0] ) {

				case 'lifetime':
					break;

				case 'limited':
					echo '<tr>
							<th><label>' . esc_html__( 'Plan Duration', 'membership-for-woocommerce' ) . '</label></th>

							<td>' . sprintf( ' %u %s ', esc_html( $plan['mwb_membership_plan_duration'][0] ), esc_html( $plan['mwb_membership_plan_duration_type'][0] ) ) . '</td>
						</tr>';
					break;

				case 'date_ranged':
					echo '<tr>
							<th><label>' . esc_html__( 'Duration', 'membership-for-woocommerce' ) . '</label></th>

							<td>' . sprintf( ' %s to %s ', esc_html( $plan['mwb_membership_plan_start'][0] ), esc_html( $plan['mwb_membership_plan_end'][0] ) ) . '</td>
						</tr>';
					break;

				default:
					echo '<tr>' . esc_html__( 'Plan duration not defined', 'membership-for-woocommerce' ) . '</tr>';

			}
			?>

			<tr>
				<th><label><?php esc_html_e( 'Plan access', 'membership-for-woocommerce' ); ?></label></th>
				<td>
					<?php echo esc_html( $plan['mwb_membership_plan_user_access'][0] ); ?>
				</td>
			</tr>

			<tr>
				<th><label><?php esc_html_e( 'Access Type', 'membership-for-woocommerce' ); ?></label></th>
				<td>
					<?php echo esc_html( $plan['mwb_membership_plan_access_type'][0] ); ?>
				</td>
			</tr>

			<?php
			switch ( $plan['mwb_membership_plan_access_type'][0] ) {

				case 'immediate_type':
					break;

				case 'delay_type':
					echo '<tr>
							<th><label>' . esc_html__( 'Delay Duration', 'membership-for-woocommerce' ) . '</label></th>

							<td>' . sprintf( ' %u %s ', esc_html( $plan['mwb_membership_plan_time_duration'][0] ), esc_html( $plan['mwb_membership_plan_time_duration_type'][0] ) ) . '</td>
						</tr>';
					break;

				default:
					echo '<tr>' . esc_html__( 'Access type duration not defined', 'membership-for-woocommerce' ) . '</tr>';
			}
			?>

			<tr>
				<th><label><?php esc_html_e( 'Discount', 'membership-for-woocommerce' ); ?></label></th>
				<td>
					<?php echo sprintf( ' %u %s ', esc_html( $plan['mwb_memebership_plan_discount_price'][0] ), esc_html( $plan['mwb_membership_plan_offer_price_type'][0] ) ); ?>
				</td>
			</tr>

			<tr>
				<th><label><?php esc_html_e( 'Free Shipping', 'membership-for-woocommerce' ); ?></label></th>
				<td>
					<?php echo esc_html( $plan['mwb_memebership_plan_free_shipping'][0] ); ?>
				</td>
			</tr>

			<tr>
				<th><label><?php esc_html_e( 'Offered Products', 'membership-for-woocommerce' ); ?></label></th>
				<td>
					<?php
					$prod_ids = maybe_unserialize( $plan['mwb_membership_plan_target_ids'][0] );
					if ( ! empty( $prod_ids ) && is_array( $prod_ids ) ) {
						foreach ( $prod_ids as $ids ) {
							echo( esc_html( $this->global_class->get_product_title( $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
						}
					}
					?>
				</td>
			</tr>

			<tr>
				<th><label><?php esc_html_e( 'Offered Categories', 'membership-for-woocommerce' ); ?></label></th>
				<td>
					<?php
					$cat_ids = maybe_unserialize( $plan['mwb_membership_plan_target_categories'][0] );
					if ( ! empty( $cat_ids ) && is_array( $cat_ids ) ) {
						foreach ( $cat_ids as $ids ) {
							echo( esc_html( $this->global_class->get_category_title( $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
						}
					}
					?>
				</td>
			</tr>

		</table>
	</div>

</div>
<!-- Members metabox end -->


