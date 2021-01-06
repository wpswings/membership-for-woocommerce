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

//echo '<pre>'; print_r( $plan ); echo '</pre>';

$plan_title  = ! empty( $plan['post_title'] ) ? $plan['post_title'] : '';
$plan_price  = ! empty( $plan['mwb_membership_plan_price'] ) ? $plan['mwb_membership_plan_price'] : '';
$plan_desc   = ! empty( $plan['post_content'] ) ? $plan['post_content'] : '';
$plan_type   = ! empty( $plan['mwb_membership_plan_name_access_type'] ) ? $plan['mwb_membership_plan_name_access_type'] : '';
$plan_dura   = ! empty( $plan['mwb_membership_plan_duration'] ) ? $plan['mwb_membership_plan_duration'] : '';
$dura_type   = ! empty( $plan['mwb_membership_plan_duration_type'] ) ? $plan['mwb_membership_plan_duration_type'] : '';
$plan_start  = ! empty( $plan['mwb_membership_plan_start'] ) ? $plan['mwb_membership_plan_start'] : '';
$plan_end    = ! empty( $plan['mwb_membership_plan_end'] ) ? $plan['mwb_membership_plan_end'] : '';
$plan_access = ! empty( $plan['mwb_membership_plan_user_access'] ) ? $plan['mwb_membership_plan_user_access'] : '';
$access_type = ! empty( $plan['mwb_membership_plan_access_type'] ) ? $plan['mwb_membership_plan_access_type'] : '';
$delay_dura  = ! empty( $plan['mwb_membership_plan_time_duration'] ) ? $plan['mwb_membership_plan_time_duration'] : '';
$delay_type  = ! empty( $plan['mwb_membership_plan_time_duration_type'] ) ? $plan['mwb_membership_plan_time_duration_type'] : '';
$discount    = ! empty( $plan['mwb_memebership_plan_discount_price'] ) ? $plan['mwb_memebership_plan_discount_price'] : '';
$price_type  = ! empty( $plan['mwb_membership_plan_offer_price_type'] ) ? $plan['mwb_membership_plan_offer_price_type'] : '';
$shipping    = ! empty( $plan['mwb_memebership_plan_free_shipping'] ) ? $plan['mwb_memebership_plan_free_shipping'] : '';
$products    = ! empty( $plan['mwb_membership_plan_target_ids'] ) ? $plan['mwb_membership_plan_target_ids'] : '';
$categories  = ! empty( $plan['mwb_membership_plan_target_categories'] ) ? $plan['mwb_membership_plan_target_categories'] : '';


$args = array(
	'post_type'   => 'mwb_cpt_membership',
	'post_status' => array( 'publish' ),
	'numberposts' => -1,
);

$existing_plans = get_posts( $args );
//echo '<pre>'; print_r( $existing_plans ); echo '</pre>';
?>
<!-- Members metabox start -->
<div class="members_plans_details">
	<?php
	if ( ! empty( $plan ) ) {
	?>
		<div class="mwb_members_plans">
			<table class="form-table">
				<tr>
					<th><label><?php esc_html_e( 'Title', 'membership-for-woocommerce' ); ?></label></th>
					<td>
						<?php echo esc_html( $plan_title ); ?>
					</td>
				</tr>

				<tr>
					<th><label><?php esc_html_e( 'Price', 'membership-for-woocommerce' ); ?></label></th>
					<td>
						<?php echo sprintf( ' %s %s ', esc_html( get_woocommerce_currency() ), esc_html( $plan_price ) ); ?>
					</td>
				</tr>

				<tr>
					<th><label><?php esc_html_e( 'Description', 'membership-for-woocommerce' ); ?></label></th>
					<td>
						<?php echo esc_html( $plan_desc ); ?>
					</td>
				</tr>

				<tr>
					<th><label><?php esc_html_e( 'Plan Type', 'membership-for-woocommerce' ); ?></label></th>
					<td>
						<?php echo esc_html( $plan_type ); ?>
					</td>
				</tr>

				<?php
				switch ( $plan_type ) {

					case 'lifetime':
						break;

					case 'limited':
						echo '<tr>
								<th><label>' . esc_html__( 'Plan Duration', 'membership-for-woocommerce' ) . '</label></th>

								<td>' . sprintf( ' %u %s ', esc_html( $plan_dura ), esc_html( $dura_type ) ) . '</td>
							</tr>';
						break;

					case 'date_ranged':
						echo '<tr>
								<th><label>' . esc_html__( 'Duration', 'membership-for-woocommerce' ) . '</label></th>

								<td>' . sprintf( ' %s to %s ', esc_html( $plan_start ), esc_html( $plan_end ) ) . '</td>
							</tr>';
						break;

					default:
						echo '<tr>' . esc_html__( 'Plan duration not defined', 'membership-for-woocommerce' ) . '</tr></br>';

				}
				?>

				<tr>
					<th><label><?php esc_html_e( 'Plan access', 'membership-for-woocommerce' ); ?></label></th>
					<td>
						<?php echo esc_html( $plan_access ); ?>
					</td>
				</tr>

				<tr>
					<th><label><?php esc_html_e( 'Access Type', 'membership-for-woocommerce' ); ?></label></th>
					<td>
						<?php echo esc_html( $access_type ); ?>
					</td>
				</tr>

				<?php
				switch ( $access_type ) {

					case 'immediate_type':
						break;

					case 'delay_type':
						echo '<tr>
								<th><label>' . esc_html__( 'Delay Duration', 'membership-for-woocommerce' ) . '</label></th>

								<td>' . sprintf( ' %u %s ', esc_html( $delay_dura ), esc_html( $delay_type ) ) . '</td>
							</tr>';
						break;

					default:
						echo '<tr>' . esc_html__( 'Access type duration not defined', 'membership-for-woocommerce' ) . '</tr></br>';
				}
				?>

				<tr>
					<th><label><?php esc_html_e( 'Discount', 'membership-for-woocommerce' ); ?></label></th>
					<td>
						<?php echo sprintf( ' %u %s ', esc_html( $discount ), esc_html( $price_type ) ); ?>
					</td>
				</tr>

				<tr>
					<th><label><?php esc_html_e( 'Free Shipping', 'membership-for-woocommerce' ); ?></label></th>
					<td>
						<?php echo esc_html( $shipping ); ?>
					</td>
				</tr>

				<tr>
					<th><label><?php esc_html_e( 'Offered Products', 'membership-for-woocommerce' ); ?></label></th>
					<td>
						<?php
						$prod_ids = maybe_unserialize( $products );
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
						$cat_ids = maybe_unserialize( $categories );
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
	<?php } else { ?>

		<div class="mwb_members_plan_select">

			<p><strong><?php esc_html_e( 'No membership details found', 'membership-for-woocommerce' ); ?></strong></p>
			<select name="members_plan_assign" id="members_plan_assign">
				<option value=""><?php esc_html_e( 'Select a plan', 'membership-for-woocommerce' ); ?></option>
				<?php
				foreach ( $existing_plans as $plan ) {
					?>
					<option value="<?php echo esc_html( $plan->ID ); ?>"><?php echo esc_html( $plan->post_title ); ?></option>
					<?php
				}
				?>
			</select>
		</div>
	<?php } ?>

</div>
<!-- Members metabox end -->

