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

// Exit is accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

$plan_title            = ! empty( $plan['post_title'] ) ? $plan['post_title'] : '';
$plan_price            = ! empty( $plan['wps_membership_plan_price'] ) ? $plan['wps_membership_plan_price'] : '';
$plan_desc             = ! empty( $plan['post_content'] ) ? $plan['post_content'] : '';
$plan_type             = ! empty( $plan['wps_membership_plan_name_access_type'] ) ? $plan['wps_membership_plan_name_access_type'] : '';
$plan_dura             = ! empty( $plan['wps_membership_plan_duration'] ) ? $plan['wps_membership_plan_duration'] : '';
$dura_type             = ! empty( $plan['wps_membership_plan_duration_type'] ) ? $plan['wps_membership_plan_duration_type'] : '';
$plan_start            = ! empty( $plan['wps_membership_plan_start'] ) ? $plan['wps_membership_plan_start'] : '';
$plan_end              = ! empty( $plan['wps_membership_plan_end'] ) ? $plan['wps_membership_plan_end'] : '';
$plan_access           = ! empty( $plan['wps_membership_plan_user_access'] ) ? $plan['wps_membership_plan_user_access'] : '';
$access_type           = ! empty( $plan['wps_membership_plan_access_type'] ) ? $plan['wps_membership_plan_access_type'] : '';
$delay_dura            = ! empty( $plan['wps_membership_plan_time_duration'] ) ? $plan['wps_membership_plan_time_duration'] : '';
$delay_type            = ! empty( $plan['wps_membership_plan_time_duration_type'] ) ? $plan['wps_membership_plan_time_duration_type'] : '';
$discount              = ! empty( $plan['wps_memebership_plan_discount_price'] ) ? $plan['wps_memebership_plan_discount_price'] : '';
$price_type            = ! empty( $plan['wps_membership_plan_offer_price_type'] ) ? $plan['wps_membership_plan_offer_price_type'] : '';
$shipping              = ! empty( $plan['wps_memebership_plan_free_shipping'] ) ? $plan['wps_memebership_plan_free_shipping'] : '';
$products              = ! empty( $plan['wps_membership_plan_target_ids'] ) ? $plan['wps_membership_plan_target_ids'] : '';
$categories            = ! empty( $plan['wps_membership_plan_target_categories'] ) ? $plan['wps_membership_plan_target_categories'] : '';
$discount_on_product   = ! empty( $plan['wps_memebership_product_discount_price'] ) ? $plan['wps_memebership_product_discount_price'] : '';
$price_type_on_product = ! empty( $plan['wps_membership_product_offer_price_type'] ) ? $plan['wps_membership_product_offer_price_type'] : '';
$plan_subscription = ! empty( $plan['wps_membership_subscription'] ) ? $plan['wps_membership_subscription'] : '';
$plan_subscription_duration = ! empty( $plan['wps_membership_subscription_expiry'] ) ? $plan['wps_membership_subscription_expiry'] : '';
$plan_subscription_duration_type = ! empty( $plan['wps_membership_subscription_expiry_type'] ) ? $plan['wps_membership_subscription_expiry_type'] : '';

$club_membership = wps_membership_get_meta_data( ! empty( $plan['ID'] ) ? $plan['ID'] : '', 'wps_membership_club', true );
$args = array(
	'post_type'   => 'wps_cpt_membership',
	'post_status' => array( 'publish' ),
	'numberposts' => -1,
);


$existing_plans = get_posts( $args );

?>
<!-- Members metabox start -->
<div class="members_plans_details">
	<?php
	if ( ! empty( $plan ) ) {
		?>
		<div class="wps_members_plans">
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
						<?php echo sprintf( ' %s %s ', esc_html( get_woocommerce_currency_symbol() ), esc_html( $plan_price ) ); ?>
					</td>
				</tr>

				<tr>
					<th><label><?php esc_html_e( 'Description', 'membership-for-woocommerce' ); ?></label></th>
					<td>
						<?php echo wp_kses_post( $plan_desc ); ?>
					</td>
				</tr>

				<tr>
					<th><label><?php esc_html_e( 'Access Type', 'membership-for-woocommerce' ); ?></label></th>
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
					<th><label><?php esc_html_e( 'Subscription Membership', 'membership-for-woocommerce' ); ?></label></th>
					<td>
						<?php echo esc_html( $plan_subscription ); ?>
					</td>
				</tr>
				
				<tr>
					<th><label><?php esc_html_e( 'Subscription Membership Duration', 'membership-for-woocommerce' ); ?></label></th>
					<td>
						<?php echo sprintf( ' %u %s ', esc_html( $plan_subscription_duration ), esc_html( $plan_subscription_duration_type ) ); ?>
					</td>
				</tr>

				<?php

				if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
					$check_licence = check_membership_pro_plugin_is_active();
					if ( $check_licence ) {
						?>
				<tr>
					<th><label><?php esc_html_e( 'Plan access', 'membership-for-woocommerce' ); ?></label></th>
					<td>
						<?php echo esc_html( $plan_access ); ?>
					</td>
				</tr>
						<?php
					}
				}
				?>
				<tr>
					<th><label><?php esc_html_e( 'Accessibility Type', 'membership-for-woocommerce' ); ?></label></th>
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
					<th><label><?php esc_html_e( 'Discount on cart', 'membership-for-woocommerce' ); ?></label></th>
					<td>
						<?php echo sprintf( ' %u %s ', esc_html( $discount ), esc_html( $price_type ) ); ?>
					</td>
				</tr>

				<tr>
					<th><label><?php esc_html_e( 'Discount on Product', 'membership-for-woocommerce' ); ?></label></th>
					<td>
						<?php echo sprintf( ' %u %s ', esc_html( $discount_on_product ), esc_html( $price_type_on_product ) ); ?>
					</td>  
				</tr>


				
				<tr>
					<th><label><?php esc_html_e( 'Include Membership', 'membership-for-woocommerce' ); ?></label></th>
					<td>
					<?php

					if ( ! empty( $club_membership ) && is_array( $club_membership ) ) {
						foreach ( $club_membership as $ids ) {
							echo( esc_html( get_the_title( $ids ) ) );
						}
					}
					?>
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
								echo( esc_html( $instance->get_product_title( $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
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
								echo( esc_html( $instance->get_category_title( $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
							}
						} else {
							esc_html_e( 'No categories Offered in this Plan', 'membership-for-woocommerce' );
						}
						?>
					</td>
				</tr>
				<?php
				if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
					$check_licence = check_membership_pro_plugin_is_active();
					if ( $check_licence ) {

						?>
					<tr>
						<th><label><?php esc_html_e( 'Offered Product Tags: ', 'membership-for-woocommerce' ); ?></label></th>
						<td>
							<?php
							$tag_ids = maybe_unserialize( ! empty( $plan['wps_membership_plan_target_tags'] ) ? $plan['wps_membership_plan_target_tags'] : array() );

							if ( ! empty( $tag_ids ) && is_array( $tag_ids ) ) {
								foreach ( $tag_ids as $ids ) {
									$tagn     = get_term_by( 'id', $ids, 'product_tag' );
									$tag_name = $tagn->name;
									echo( esc_html( $tag_name ) . '(#' . esc_html( $ids ) . ') ' );
								}
							} else {
								esc_html_e( 'No Product Tags Offered in this Plan', 'membership-for-woocommerce' );
							}
							?>
						</td>
					</tr></br>
					
					<tr>
						<th><label><?php esc_html_e( 'Offered Posts: ', 'membership-for-woocommerce' ); ?></label></th>
					
						<td>
							<?php
							$post_ids = maybe_unserialize( ! empty( $plan['wps_membership_plan_post_target_ids'] ) ? $plan['wps_membership_plan_post_target_ids'] : array() );

							if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
								foreach ( $post_ids as $ids ) {

									echo( esc_html( get_post_field( 'post_title', $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
								}
							} else {
								esc_html_e( 'No Posts Offered in this Plan', 'membership-for-woocommerce' );
							}
							?>
						</td>
					</tr>
					<tr>
						<th><label><?php esc_html_e( 'Offered Posts Categories: ', 'membership-for-woocommerce' ); ?></label></th>
						<td>
							<?php
							$cat_ids = maybe_unserialize( ! empty( $plan['wps_membership_plan_target_post_categories'] ) ? $plan['wps_membership_plan_target_post_categories'] : array() );

							if ( ! empty( $cat_ids ) && is_array( $cat_ids ) ) {
								foreach ( $cat_ids as $ids ) {
									echo( esc_html( $instance->get_category_title( $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
								}
							} else {
								esc_html_e( 'No Product Categories Offered in this Plan', 'membership-for-woocommerce' );
							}
							?>
						</td>
					</tr>
					<tr>
						<th><label><?php esc_html_e( 'Offered Post Tags: ', 'membership-for-woocommerce' ); ?></label></th>
						<td>
							<?php
							$tag_ids = maybe_unserialize( ! empty( $plan['wps_membership_plan_target_post_tags'] ) ? $plan['wps_membership_plan_target_post_tags'] : array() );

							if ( ! empty( $tag_ids ) && is_array( $tag_ids ) ) {
								foreach ( $tag_ids as $ids ) {
									$tagn     = get_term_by( 'id', $ids, 'post_tag' );
									$tag_name = $tagn->name;
									echo( esc_html( $tag_name ) . '(#' . esc_html( $ids ) . ') ' );
								}
							} else {
								esc_html_e( 'No Post Tags Offered in this Plan', 'membership-for-woocommerce' );
							}
							?>
						</td>
					</tr>

					<tr>
						<th><label><?php esc_html_e( 'Offered Pages: ', 'membership-for-woocommerce' ); ?></label></th>
						<td>
							<?php

							$post_ids = maybe_unserialize( ! empty( $plan['wps_membership_plan_page_target_ids'] ) ? $plan['wps_membership_plan_page_target_ids'] : array() );

							if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
								foreach ( $post_ids as $ids ) {

									echo( esc_html( get_post_field( 'post_title', $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
								}
							} else {
								esc_html_e( 'No Page Offered in this Plan', 'membership-for-woocommerce' );
							}
							?>
						</td>
					</tr>

					<tr>
						<th><label><?php esc_html_e( 'Offered Product (under Product discount): ', 'membership-for-woocommerce' ); ?></label></th>
						<td>
							<?php

							$post_ids = maybe_unserialize( ! empty( $plan['wps_membership_plan_target_disc_ids'] ) ? $plan['wps_membership_plan_target_disc_ids'] : array() );

							if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
								foreach ( $post_ids as $ids ) {

									echo( esc_html( get_post_field( 'post_title', $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
								}
							} else {
								esc_html_e( 'No Product Offered in this Plan', 'membership-for-woocommerce' );
							}
							?>
						</td>
					</tr>


					<tr>
						<th><label><?php esc_html_e( 'Offered Product Categories (under Product discount): ', 'membership-for-woocommerce' ); ?></label></th>
						<td>
							<?php

							$cat_ids = maybe_unserialize( ! empty( $plan['wps_membership_plan_target_disc_categories'] ) ? $plan['wps_membership_plan_target_disc_categories'] : array() );

							if ( ! empty( $cat_ids ) && is_array( $cat_ids ) ) {
								foreach ( $cat_ids as $ids ) {
									echo( esc_html( $instance->get_category_title( $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
								}
							} else {
								esc_html_e( 'No categories Offered in this Plan', 'membership-for-woocommerce' );
							}
							?>
						</td>
					</tr>

					<tr>
						<th><label><?php esc_html_e( 'Offered Product Tags (under Product discount): ', 'membership-for-woocommerce' ); ?></label></th>
						<td>
							<?php

							$tag_ids = maybe_unserialize( ! empty( $plan['wps_membership_plan_target_disc_tags'] ) ? $plan['wps_membership_plan_target_disc_tags'] : array() );

							if ( ! empty( $tag_ids ) && is_array( $tag_ids ) ) {
								foreach ( $tag_ids as $ids ) {
									$tagn     = get_term_by( 'id', $ids, 'product_tag' );
									if ( ! empty( $tagn ) ) {
										$tag_name = $tagn->name;
										echo( esc_html( $tag_name ) . '(#' . esc_html( $ids ) . ') ' );
									}
								}
							} else {
								esc_html_e( 'No Product Tags Offered in this Plan', 'membership-for-woocommerce' );
							}
							?>
						</td>
					</tr>


						<?php

					}
				}
				?>
			</table>
		</div>
	<?php } else { ?>

		<div class="wps_members_plan_select">

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
