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


global $post;

$mfw_plugins = get_option( 'active_plugins' );


?>

<!-- Plans creation start. -->
<table class="form-table wps_membership_plans_creation_setting">
	<tbody>

		<!-- Nonce Field -->
		<?php wp_nonce_field( 'wps_membership_plans_creation_nonce', 'wps_membership_plans_nonce' ); ?>

		<!-- Memberhship plan price start  -->
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="wps_membership_plan_price"><?php esc_html_e( 'Membership Plan Amount', 'membership-for-woocommerce' ); ?></label>
				<?php
				$description = esc_html__( 'Provide the amount at which Membership Plan will be available for Users.', 'membership-for-woocommerce' );
				$instance->tool_tip( $description );
				?>
			</th>
			<td class="forminp forminp-text">
				<input type="number" required step=".01" min="0" id="wps_membership_plan_price" placeholder="<?php echo esc_attr( $description ); ?>" name="wps_membership_plan_price" value="<?php echo esc_attr( $settings_fields['wps_membership_plan_price'] ); ?>">
			</td>
		</tr>
		<!-- Membership plan price end. -->

		<!-- Access Type start -->
		<tr valign="top">

			<th scope="row" class="titledesc">
				<label for="wps_membership_plan_access_type"><?php esc_html_e( 'Access Type', 'membership-for-woocommerce' ); ?></label>
			<?php
			$description = esc_html__( 'Provide the Access Type of your Membership Plan', 'membership-for-woocommerce' );
			$instance->tool_tip( $description );
			?>
			</th>
			<td class="forminp forminp-text">
				<?php
				$wps_membership_plan_access_type = $settings_fields['wps_membership_plan_name_access_type'];
				?>
				<select id="wps_membership_plan_access_type" name="wps_membership_plan_name_access_type">
					<option <?php selected( $wps_membership_plan_access_type, 'lifetime' ); ?> value="lifetime"><?php esc_html_e( 'Lifetime', 'membership-for-woocommerce' ); ?></option>
					<option <?php selected( $wps_membership_plan_access_type, 'limited' ); ?> value="limited"><?php esc_html_e( 'Limited', 'membership-for-woocommerce' ); ?></option>
				</select>
			</td>
		</tr>
		<!-- Access Type End -->

		<!-- Plan Duration start. -->
		<tr valign="top" id="wps_membership_duration" >

			<th scope="row" class="titledesc">
				<label for="wps_membership_plan_duration"><?php esc_html_e( 'Duration', 'membership-for-woocommerce' ); ?></label>
			<?php
				$description = esc_html__( 'Duration in terms of  \'DAYS\', \'WEEKS\', \'MONTHS\', \'YEARS\' for which the plan will be active.', 'membership-for-woocommerce' );
				$instance->tool_tip( $description );
			?>
			</th>

			<td class="forminp forminp-text">
				<?php
				$wps_membership_plan_duration_type = $settings_fields['wps_membership_plan_duration_type'];
				?>
				<input type="number" min="0" id="wps_membership_plan_duration" maxlenght="4" step="1" pattern="[0-9]" name="wps_membership_plan_duration" value="<?php echo esc_attr( $settings_fields['wps_membership_plan_duration'] ); ?>" >
				<select name="wps_membership_plan_duration_type" id="wps_membership_plan_duration_type">
					<option <?php selected( $wps_membership_plan_duration_type, 'days' ); ?> value="days"><?php esc_html_e( 'Days', 'membership-for-woocommerce' ); ?></option>
					<option <?php selected( $wps_membership_plan_duration_type, 'weeks' ); ?> value="weeks"><?php esc_html_e( 'Weeks', 'membership-for-woocommerce' ); ?></option>
					<option <?php selected( $wps_membership_plan_duration_type, 'months' ); ?> value="months"><?php esc_html_e( 'Months', 'membership-for-woocommerce' ); ?></option>
					<option <?php selected( $wps_membership_plan_duration_type, 'years' ); ?> value="years"><?php esc_html_e( 'Years', 'membership-for-woocommerce' ); ?></option>
				</select>
			</td>
			<?php
			if ( wps_membership_is_plugin_active( 'subscriptions-for-woocommerce/subscriptions-for-woocommerce.php' ) ) {

				?>
				</tr>
				<!-- Plan Duration End. -->

					<!-- Plan subscription start. -->
					<tr valign="top" id="wps_membership_subscription_tr" >
						<th scope="row" class="titledesc">
							<label for="wps_membership_subscription"><?php esc_html_e( 'Enable Subscription Membership', 'membership-for-woocommerce' ); ?></label>
								<?php
								$description = esc_html__( 'Enable it to make membership plan as subscription.', 'membership-for-woocommerce' );
								$instance->tool_tip( $description );
								?>
						</th>

						<td class="forminp forminp-text">
							<?php
							$wps_membership_subscription = $settings_fields['wps_membership_subscription'];
							?>
							<input type="checkbox"  class="wps_membership_subscription_" name="wps_membership_subscription" value="yes" <?php checked( 'yes', $wps_membership_subscription ); ?> >
						</td>
					</tr>
					<!-- Plan subscription End. -->
					<!-- Plan subscription expiry start. -->
					<tr valign="top" id="wps_membership_subscription_expiry_tr" >

					<th scope="row" class="titledesc">
							<label for="wps_membership_subscription_expiry"><?php esc_html_e( 'Subscription expiry', 'membership-for-woocommerce' ); ?></label>
							<?php
							$description = esc_html__( 'Subscription Expiry in terms of  \'DAYS\', \'WEEKS\', \'MONTHS\', \'YEARS\' for which the plan will be active.', 'membership-for-woocommerce' );

							$instance->tool_tip( $description );
							?>
					</th>
					<td class="forminp forminp-text">
						<?php
						$wps_membership_subscription_expiry_type = $settings_fields['wps_membership_subscription_expiry_type'];
						?>
						<input type="number" min="0" id="wps_membership_subscription_expiry" maxlenght="4" step="1" pattern="[0-9]" name="wps_membership_subscription_expiry" value="<?php echo esc_attr( $settings_fields['wps_membership_subscription_expiry'] ); ?>" >
						<select disabled="disabled" name="wps_membership_subscription_expiry_type" id="wps_membership_subscription_expiry_type">
							<option <?php selected( $wps_membership_subscription_expiry_type, 'day' ); ?> value="days"><?php esc_html_e( 'Days', 'membership-for-woocommerce' ); ?></option>
							<option <?php selected( $wps_membership_subscription_expiry_type, 'week' ); ?> value="weeks"><?php esc_html_e( 'Weeks', 'membership-for-woocommerce' ); ?></option>
							<option <?php selected( $wps_membership_subscription_expiry_type, 'month' ); ?> value="months"><?php esc_html_e( 'Months', 'membership-for-woocommerce' ); ?></option>
							<option <?php selected( $wps_membership_subscription_expiry_type, 'year' ); ?> value="years"><?php esc_html_e( 'Years', 'membership-for-woocommerce' ); ?></option>
						</select>
					</td>
					</tr>
					<!-- Plan subscription expiry End -->

					<!-- enter initial fee start -->
					<tr class="wps_msfw_initial_fee">
						<th scope="row" class="titledesc">
							<label for="wps_sfw_subscription_initial_signup_price"><?php esc_html_e( 'Enter Initial Signup fee', 'membership-for-woocommerce' ); ?></label>
							<?php
							$instance->tool_tip( esc_html__( 'Enter the initial signup fee for this membership plan, or leave it empty if there is no initial fee.', 'membership-for-woocommerce' ) );
							?>
						</th>
						<td id="mfw_free_shipping" class="forminp forminp-text">
						<input type="number" min="0" class="wps_membership wps_sfw_subscription_initial_signup_price" id="wps_sfw_subscription_initial_signup_price" name="wps_sfw_subscription_initial_signup_price" value="<?php echo esc_attr( ! empty( $settings_fields['wps_sfw_subscription_initial_signup_price'] ) ? $settings_fields['wps_sfw_subscription_initial_signup_price'] : '' ); ?>">
						</td>
					</tr>
					<!-- enter initial fee end -->

					<!-- INITIAL FEE and FREE TRIAL settings start here. -->
					<tr class="wps_msfw_enable_trial_settings">
						<th scope="row" class="titledesc">
							<label for="wps_mfw_enable_free_trial_settings"><?php esc_html_e( 'Enable Free Trial Settings', 'membership-for-woocommerce' ); ?></label>
							<?php
							$instance->tool_tip( esc_html__( 'Enable these settings to set a free trial and initial fee, allowing users to take advantage', 'membership-for-woocommerce' ) );
							?>
						</th>
						<td id="mfw_free_shipping" class="forminp forminp-text">
							<input type="checkbox"  class="wps_mfw_enable_free_trial_settings" name="wps_mfw_enable_free_trial_settings" value="yes" <?php checked( 'yes', $settings_fields['wps_mfw_enable_free_trial_settings'] ); ?> >
						</td>
					</tr>
					<!-- Enable initial fee and free trial end -->
					
					<!-- enter free trial interval start -->
					<tr class="wps_msfw_free_interval">
						<th scope="row" class="titledesc">
							<label for="wps_sfw_subscription_free_trial_number"><?php esc_html_e( 'Enter the free trial interval', 'membership-for-woocommerce' ); ?></label>
						<?php
						$instance->tool_tip( esc_html__( 'Specify discount applied to orders with this plan.', 'membership-for-woocommerce' ) );
						?>
						</th>
						<td class="forminp forminp-text">
							<input type="number" min="0" class="wps_membership plan_offer_input_type" id="wps_sfw_subscription_free_trial_number" name="wps_sfw_subscription_free_trial_number" value="<?php echo esc_attr( ! empty( $settings_fields['wps_sfw_subscription_free_trial_number'] ) ? $settings_fields['wps_sfw_subscription_free_trial_number'] : '' ); ?>">
							<select name="wps_sfw_subscription_free_trial_interval" id='wps_sfw_subscription_free_trial_interval' >
								<option value="days" <?php selected( $settings_fields['wps_sfw_subscription_free_trial_interval'], 'days' ); ?>><?php esc_html_e( 'Days', 'membership-for-woocommerce' ); ?></option>
								<option value="weeks" <?php selected( $settings_fields['wps_sfw_subscription_free_trial_interval'], 'weeks' ); ?>><?php esc_html_e( 'Weeks', 'membership-for-woocommerce' ); ?></option>
								<option value="months" <?php selected( $settings_fields['wps_sfw_subscription_free_trial_interval'], 'months' ); ?>><?php esc_html_e( 'Months', 'membership-for-woocommerce' ); ?></option>
								<option value="years" <?php selected( $settings_fields['wps_sfw_subscription_free_trial_interval'], 'years' ); ?>><?php esc_html_e( 'Years', 'membership-for-woocommerce' ); ?></option>
							</select>
						</td>
					</tr>
					<?php
			}
			?>
	</tbody>
</table>

<div class="wps_membership_plan_products">
	<h1><?php esc_html_e( 'Membership Plan Offers', 'membership-for-woocommerce' ); ?></h1>
</div>


	<!-- Membership product section starts -->
	<div class="membership-offers">
		<!-- Offer section html start -->
		<div class="new_created_offers wps_membership_offers" id="new_created_offers" >

			<h2 class="wps_membership_offer_title" >
				<?php esc_html_e( 'Included Section', 'membership-for-woocommerce' ); ?>
			</h2>
			<h3>
				<?php esc_html_e( 'In Include Section whatever the products, categories, tags you will choose, ONLY MEMBERS can buy these products and can access the pages.', 'membership-for-woocommerce' ); ?>
			</h3>
			<table>

			<!-- Offer Product section start -->
			<tr>
				<th scope="row" class="titledesc">
					<label for="wps_membership_offer_product_select"><?php esc_html_e( 'Included Products', 'membership-for-woocommerce' ); ?></label>
					<?php
					$description = esc_html__( 'Select the products you want to included in Membership Plan.', 'membership-for-woocommerce' );
					$instance->tool_tip( $description );
					?>
				</th>

				<td class="forminp forminp-text">
					<select id="wps_membership_plan_target_ids_search" class="wc-membership-product-search" multiple="multiple" name="wps_membership_plan_target_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'membership-for-woocommerce' ); ?>">
						<?php
						if ( ! empty( $settings_fields ) ) {

							$wps_membership_plan_target_product_ids = is_array( $settings_fields['wps_membership_plan_target_ids'] ) ? array_map( 'absint', $settings_fields['wps_membership_plan_target_ids'] ) : array();
							$demo_plan_array = wps_membership_get_meta_data( $post->ID, 'wps_membership_plan_target_ids_search', true );
							if ( ! empty( $demo_plan_array ) ) {

								$demo_plan_array = wps_membership_get_meta_data( $post->ID, 'wps_membership_plan_target_ids_search', true );
								$wps_membership_plan_target_product_ids = array_merge( $wps_membership_plan_target_product_ids, $demo_plan_array );
							}

							if ( ! empty( $wps_membership_plan_target_product_ids ) && is_array( $wps_membership_plan_target_product_ids ) ) {
								foreach ( $wps_membership_plan_target_product_ids as $wps_membership_plan_single_target_product_ids ) {

									$product_name = $instance->get_product_title( $wps_membership_plan_single_target_product_ids );
									?>
									<option value="<?php echo esc_html( $wps_membership_plan_single_target_product_ids ); ?>" <?php selected( in_array( $wps_membership_plan_single_target_product_ids, $wps_membership_plan_target_product_ids, true ) ); ?>><?php echo( esc_html( $product_name ) . '(#' . esc_html( $wps_membership_plan_single_target_product_ids ) . ')' ); ?></option>
									<?php
								}
							}
						}
						?>
					</select>
				</td>
			</tr>
			<!-- Offer product section End -->

			<!-- Offer categories section start -->
			<tr>
				<th scope="row" class="titledesc">
					<label for="wps_membership_offer_category_select"><?php esc_html_e( 'Included Product Categories', 'membership-for-woocommerce' ); ?></label>
					<?php
					$description = esc_html__( 'Select the categories you want to include in Membership Plan.', 'membership-for-woocommerce' );
					$instance->tool_tip( $description );
					?>
				</th>
				<td class="forminp forminp-text">
					<select id="wps_membership_plan_target_categories_search" class="wc-membership-product-category-search" multiple="multiple" name="wps_membership_plan_target_categories[]" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'membership-for-woocommerce' ); ?>">
						<?php

						if ( ! empty( $settings_fields ) ) {

							$wps_membership_plan_target_categories = is_array( $settings_fields['wps_membership_plan_target_categories'] ) ? array_map( 'absint', $settings_fields['wps_membership_plan_target_categories'] ) : array();
							if ( ! empty( $wps_membership_plan_target_categories ) && is_array( $wps_membership_plan_target_categories ) ) {

								foreach ( $wps_membership_plan_target_categories as $single_target_category_id ) {

									$category_name = $instance->get_category_title( $single_target_category_id );
									?>
									<option value="<?php echo esc_html( $single_target_category_id ); ?>"<?php selected( in_array( $single_target_category_id, $wps_membership_plan_target_categories, true ) ); ?>><?php echo( esc_html( $category_name ) . '(#' . esc_html( $single_target_category_id ) . ')' ); ?></option>
									<?php
								}
							}
						}
						?>
					</select>
				</td>
			</tr>
			<!-- Offer categories section end. -->
		</table>
	</div>

	<div class="membership-features">
		<!-- Membership features section start -->
		<div class="new_created_offers wps_membership_offers">

			<h2 class="wps_membership_offer_title" >
				<?php esc_html_e( 'Membership Features Section', 'membership-for-woocommerce' ); ?>
			</h2>
			<h3>
				<?php esc_html_e( 'According to this discount setting , Members will get discount on cart whatever the products they purchase no matter.', 'membership-for-woocommerce' ); ?>
			</h3>
			<table>
				<!-- Discount section start -->
				<tr>
					<th scope="row" class="titledesc">
						<label for="wps_membership_plan_price_type_id"><?php esc_html_e( 'Discount on Cart', 'membership-for-woocommerce' ); ?></label>
					<?php
					$description = esc_html__( 'Specify discount applied to orders with this plan.', 'membership-for-woocommerce' );
					$instance->tool_tip( $description );
					?>
					</th>
					<td class="forminp forminp-text">
						<?php
						$wps_membership_plan_offer_price_type = $settings_fields['wps_membership_plan_offer_price_type'];
						$wps_membership_plan_discount_price   = $settings_fields['wps_memebership_plan_discount_price'];
						if ( empty( $wps_membership_plan_discount_price ) ) {

							$wps_membership_plan_discount_price = 0;
						}
						?>
						<select name="wps_membership_plan_offer_price_type" id='wps_membership_plan_offer_price_type_id' >
							<option <?php selected( $wps_membership_plan_offer_price_type, '%', true ); ?> value="%"><?php esc_html_e( 'Discount %', 'membership-for-woocommerce' ); ?></option>
							<option <?php selected( $wps_membership_plan_offer_price_type, 'fixed', true ); ?> value="fixed"><?php esc_html_e( 'Fixed price', 'membership-for-woocommerce' ); ?></option>
						</select>
						<input type="number" min="0" class="wps_membership plan_offer_input_type" id="wps_membership_plan_offer_price" name="wps_memebership_plan_discount_price" value="<?php echo esc_attr( $wps_membership_plan_discount_price ); ?>">
					</td>
				</tr>
				<!-- Discount section End. -->

				<!-- Fress shipping section start-->
				<tr>
					<th scope="row" class="titledesc">
						<label for="wps_membership_plan_free_shipping"><?php esc_html_e( 'Allow Free Shipping', 'membership-for-woocommerce' ); ?></label>
					<?php
					$description = esc_html__( 'Allow Free Shipping to all the members of this membership plan', 'membership-for-woocommerce' );
					$instance->tool_tip( $description );
					?>
					</th>
					<td id="mfw_free_shipping" class="forminp forminp-text">
						<?php
						$wps_membership_plan_free_shipping = $settings_fields['wps_memebership_plan_free_shipping'];
						?>
						<input type="checkbox"  class="wps_membership_plan_offer_free_shipping" name="wps_memebership_plan_free_shipping" value="yes" <?php checked( $wps_membership_plan_free_shipping, 'yes', true ); ?>>
						<!-- manage free shipping link start. -->
						<div class="wps_membership_free_shipping_link" >
							<p class="wps_membership_free_shipping">
								<a class="button" target="_blank" href="<?php echo esc_html( admin_url( 'admin.php' ) . '?page=wc-settings&tab=shipping' ); ?>"><?php esc_html_e( 'Manage Free shipping', 'membership-for-woocommerce' ); ?></a>
							</p>
						</div>
						<!-- Manage free shipping link end. -->
					</td>
				</tr>
				<!-- Free shiping section end. -->

				 <!-- Restrict maximum product purchase limit. -->
				<tr>
					<th scope="row" class="titledesc">
						<label for="wps_set_maximum_product_purchase_limit"><?php esc_html_e( 'Set Maximum Purchase Quantity for Products', 'membership-for-woocommerce' ); ?></label>
					<?php
					$description = esc_html__( 'Set the maximum product quantity a member is allowed to purchase under this membership plan.', 'membership-for-woocommerce' );
					$instance->tool_tip( $description );
					?>
					</th>
					<td id="mfw_set_product_limit" class="forminp forminp-text">
						<?php
						$wps_set_maximum_product_purchase_limit = $settings_fields['wps_set_maximum_product_purchase_limit'];
						?>
						<input type="number" name="wps_set_maximum_product_purchase_limit" value="<?php echo esc_html( $wps_set_maximum_product_purchase_limit ); ?>" min="0">
					</td>
				</tr>
				<!-- Restrict maximum product purchase limit end. -->
			</table>
		</div>
		<!-- PAR Compatible -->
		<?php do_action( 'wps_wpr_extend_membership_metabox_field', $settings_fields, $instance, $post ); ?>
	</div>
</div>
<!-- Plans creation end. -->

