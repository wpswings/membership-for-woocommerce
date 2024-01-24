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
if ( ! isset( $mfw_plugins['membership-for-woocommerce-pro/membership-for-woocommerce-pro.php'] ) ) {

	wps_mfw_upgrade_pro_popup();
}


/**
 * Function for popup.
 *
 * @return void
 */
function wps_mfw_upgrade_pro_popup() {
	?>

		<!-- Go pro popup wrap start. -->
	<div class="wps_ubo_lite_go_pro_popup_wrap">
		<!-- Go pro popup main start. -->
		<div class="wps_ubo_lite_go_pro_popup">
			<!-- Main heading. -->
			<div class="wps_ubo_lite_go_pro_popup_head">
				<h2><?php esc_html_e( 'Want More? Go Pro !!', 'membership-for-woocommerce' ); ?></h2>
				<!-- Close button. -->
				<a href="" class="wps_ubo_lite_go_pro_popup_close">
					<span>&times;</span>
				</a>
			</div>  

			<!-- Notice icon. -->
			<div class="wps_ubo_lite_go_pro_popup_head"><img src="<?php echo esc_url( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/pro.png' ); ?> ">
			</div>

			<!-- Notice. -->
			<div class="wps_ubo_lite_go_pro_popup_content">
				<p class="wps_ubo_lite_go_pro_popup_text">
					<?php esc_html_e( 'A straightforward membership plugin that functions seamlessly on your eCommerce business will help you build your community of members with premium features which gives two Free Templates of Comparision and Simple, Create & sort plans, get revenue & detailed reports, give discounts, override access to posts, comments on your protected posts and many more.', 'membership-for-woocommerce' ); ?>
				</p>
			</div>

			<!-- Go pro button. -->
			<div class="wps_ubo_lite_go_pro_popup_button">
				<a class="button wps_ubo_lite_overview_go_pro_button" target="_blank" href="https://wpswings.com/product/membership-for-woocommerce-pro/?utm_source=wpswings-membership-pro&utm_medium=membership-org-backend&utm_campaign=go-pro"><?php echo esc_html__( 'Upgrade to Premium', 'membership-for-woocommerce' ) . ' <span class="dashicons dashicons-arrow-right-alt"></span>'; ?></a>
			</div>
		</div>
		<!-- Go pro popup main end. -->
	</div>
	<!-- Go pro popup wrap end. -->


	<?php
}


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

		<!-- club membership section start -->
		<tr class="wps-membership__plan--pro-disabled">

			<th scope="row" class="titledesc">
				<label for="wps_membership_club"><?php esc_html_e( 'Include other Memberships', 'membership-for-woocommerce' ); ?></label>
				<?php
				$description = esc_html__( 'Select the membership plans you want to include with this membership', 'membership-for-woocommerce' );
				$instance->tool_tip( $description );
				?>
			</th>

			<td class="forminp forminp-text">
				<select id="wps_membership_club" class="wc-membership-search" multiple="multiple" name="wps_membership_club[]" data-placeholder="<?php esc_attr_e( 'Search for a memberships&hellip;', 'membership-for-woocommerce' ); ?>">

					<?php

					$wps_membership_club = '';
					if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
						$check_licence = check_membership_pro_plugin_is_active();
						if ( $check_licence ) {
							if ( ! empty( $settings_fields ) ) {

								$wps_membership_club = is_array( $settings_fields['wps_membership_club'] ) ? array_map( 'absint', $settings_fields['wps_membership_club'] ) : array();

								if ( $wps_membership_club ) {

									foreach ( $wps_membership_club as $wps_membership_club_ids ) {

										$mem_id = $wps_membership_club_ids;

										?>

								<option value="<?php echo esc_html( $wps_membership_club_ids ); ?>" <?php echo ( in_array( $wps_membership_club_ids, $wps_membership_club, true ) ? 'selected' : '' ); ?>><?php echo( esc_html( $mem_id ) . '(#' . esc_html( $wps_membership_club_ids ) . ')' ); ?></option>

										<?php
									}
								}
							}
						}
					}

					?>

				</select>

			</td>	

		</tr>
		<!-- Offer product section End -->

		<!-- Memberhship info start  -->
		<tr valign="top" class="wps-membership__plan--pro-disabled">

			<th scope="row" class="titledesc">
				<label for="wps_membership_plan_info"><?php esc_html_e( 'Membership Info', 'membership-for-woocommerce' ); ?></label>
			<?php
			$description = esc_html__( 'Provide the information related to membership', 'membership-for-woocommerce' );

			$instance->tool_tip( $description );
			?>
			</th>

			<td class="forminp forminp-text">

				<?php
				$wps_membership_plan_info = '';
				if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
					$check_licence = check_membership_pro_plugin_is_active();
					if ( $check_licence ) {
						$wps_membership_plan_info = $settings_fields['wps_membership_plan_info'];
						$settings = array(
							'media_buttons'    => false,
							'drag_drop_upload' => true,
							'dfw'              => true,
							'teeny'            => true,
							'editor_height'    => 200,
							'editor_class'       => 'wps_etmfw_new_woo_ver_style_textarea',
						);

						wp_editor( $wps_membership_plan_info, 'wps_membership_plan_info', $settings );
					}
				} else {
					?>
					<select multiple="multiple">
						<option></option>
						<option></option>
						<option></option>
					</select>
					
					<?php
				}

				?>

			</td>
		</tr>
		<!-- Membership info end. -->

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
					<option <?php echo esc_html( 'lifetime' === $wps_membership_plan_access_type ? 'selected' : '' ); ?> value="lifetime"><?php esc_html_e( 'Lifetime', 'membership-for-woocommerce' ); ?></option>

					<option <?php echo esc_html( 'limited' === $wps_membership_plan_access_type ? 'selected' : '' ); ?> value="limited"><?php esc_html_e( 'Limited', 'membership-for-woocommerce' ); ?></option>
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
					<option <?php echo esc_html( 'days' === $wps_membership_plan_duration_type ? 'selected' : '' ); ?> value="days"><?php esc_html_e( 'Days', 'membership-for-woocommerce' ); ?></option>
					<option <?php echo esc_html( 'weeks' === $wps_membership_plan_duration_type ? 'selected' : '' ); ?> value="weeks"><?php esc_html_e( 'Weeks', 'membership-for-woocommerce' ); ?></option>
					<option <?php echo esc_html( 'months' === $wps_membership_plan_duration_type ? 'selected' : '' ); ?> value="months"><?php esc_html_e( 'Months', 'membership-for-woocommerce' ); ?></option>
					<option <?php echo esc_html( 'years' === $wps_membership_plan_duration_type ? 'selected' : '' ); ?> value="years"><?php esc_html_e( 'Years', 'membership-for-woocommerce' ); ?></option>
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
					<label for="wps_membership_subscription"><?php esc_html_e( 'Subscription Membership', 'membership-for-woocommerce' ); ?></label>
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
		<option <?php echo esc_html( 'day' === $wps_membership_subscription_expiry_type ? 'selected' : '' ); ?> value="day"><?php esc_html_e( 'Days', 'membership-for-woocommerce' ); ?></option>
		<option <?php echo esc_html( 'week' === $wps_membership_subscription_expiry_type ? 'selected' : '' ); ?> value="week"><?php esc_html_e( 'Weeks', 'membership-for-woocommerce' ); ?></option>
		<option <?php echo esc_html( 'month' === $wps_membership_subscription_expiry_type ? 'selected' : '' ); ?> value="month"><?php esc_html_e( 'Months', 'membership-for-woocommerce' ); ?></option>
		<option <?php echo esc_html( 'year' === $wps_membership_subscription_expiry_type ? 'selected' : '' ); ?> value="year"><?php esc_html_e( 'Years', 'membership-for-woocommerce' ); ?></option>
	</select>
</td>
</tr>
<!-- Plan subscription expiry End -->
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
			<?php esc_html_e( 'Offer Section', 'membership-for-woocommerce' ); ?>
		</h2>
			<h3>
				<?php esc_html_e( 'In Offer Section whatever the products, categories, tags you will choose, Members will get DISCOUNT on these products.', 'membership-for-woocommerce' ); ?>
			</h3>
		<table>

			<!-- Offer Product section start -->
			<tr class="wps-membership__plan--pro-disabled">
				<th scope="row" class="titledesc">
					<label for="wps_membership_offer_product_select"><?php esc_html_e( 'Offered Products', 'membership-for-woocommerce' ); ?></label>
					<?php
					$description = esc_html__( 'Select the products you want to Offer in Membership Plan.', 'membership-for-woocommerce' );

					$instance->tool_tip( $description );
					?>
				</th>

				<td class="forminp forminp-text">
					<select id="wps_membership_plan_target_ids_search" class="wc-membership-product-search" multiple="multiple" name="wps_membership_plan_target_disc_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'membership-for-woocommerce' ); ?>">

						<?php

						if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
							$check_licence = check_membership_pro_plugin_is_active();
							if ( $check_licence ) {
								if ( ! empty( $settings_fields ) ) {

									$wps_membership_plan_target_product_ids = is_array( $settings_fields['wps_membership_plan_target_disc_ids'] ) ? array_map( 'absint', $settings_fields['wps_membership_plan_target_disc_ids'] ) : array();

									if ( $wps_membership_plan_target_product_ids ) {

										foreach ( $wps_membership_plan_target_product_ids as $wps_membership_plan_single_target_product_ids ) {

											$product_name = $instance->get_product_title( $wps_membership_plan_single_target_product_ids );
											?>

									<option value="<?php echo esc_html( $wps_membership_plan_single_target_product_ids ); ?>" <?php echo ( in_array( $wps_membership_plan_single_target_product_ids, $wps_membership_plan_target_product_ids, true ) ? 'selected' : '' ); ?>><?php echo( esc_html( $product_name ) . '(#' . esc_html( $wps_membership_plan_single_target_product_ids ) . ')' ); ?></option>

											<?php
										}
									}
								}
							}
						}

						?>

					</select>

				</td>	

			</tr>
			<!-- Offer product section End -->

			<!-- Offer categories section start -->
			<tr class="wps-membership__plan--pro-disabled">

				<th scope="row" class="titledesc">
					<label for="wps_membership_offer_category_select"><?php esc_html_e( 'Offered Product Categories', 'membership-for-woocommerce' ); ?></label>
					<?php
					$description = esc_html__( 'Select the categories you want to Offer in Membership Plan.', 'membership-for-woocommerce' );

					$instance->tool_tip( $description );
					?>
				</th>

				<td class="forminp forminp-text">
					<select id="wps_membership_plan_target_categories_search" class="wc-membership-product-category-search" multiple="multiple" name="wps_membership_plan_target_disc_categories[]" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'membership-for-woocommerce' ); ?>">
						<?php
						if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
							$check_licence = check_membership_pro_plugin_is_active();
							if ( $check_licence ) {
								if ( ! empty( $settings_fields ) ) {

									$wps_membership_plan_target_categories = is_array( $settings_fields['wps_membership_plan_target_disc_categories'] ) ? array_map( 'absint', $settings_fields['wps_membership_plan_target_disc_categories'] ) : array();

									if ( $wps_membership_plan_target_categories ) {

										foreach ( $wps_membership_plan_target_categories as $single_target_category_id ) {

											$category_name = $instance->get_category_title( $single_target_category_id );
											?>

									<option value="<?php echo esc_html( $single_target_category_id ); ?>" selected="selected"><?php echo( esc_html( $category_name ) . '(#' . esc_html( $single_target_category_id ) . ')' ); ?></option>

											<?php
										}
									}
								}
							}
						}

						?>

					</select>

				</td>

			</tr>
			<!-- Offer categories section end. -->


			<!-- Offer tags section start -->
			<tr class="wps-membership__plan--pro-disabled">

				<th scope="row" class="titledesc">
					<label for="wps_membership_offer_tag_select"><?php esc_html_e( 'Offered Product Tags', 'membership-for-woocommerce' ); ?></label>
					<?php
					$description = esc_html__( 'Select the tags you want to Offer in Membership Plan.', 'membership-for-woocommerce' );

					$instance->tool_tip( $description );
					?>
				</th>

				<td class="forminp forminp-text">
					<select id="wps_membership_plan_target_tags_search" class="wc-membership-product-tag-search" multiple="multiple" name="wps_membership_plan_target_disc_tags[]" data-placeholder="<?php esc_attr_e( 'Search for a tag&hellip;', 'membership-for-woocommerce' ); ?>">
						<?php
						if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
							$check_licence = check_membership_pro_plugin_is_active();
							if ( $check_licence ) {
								if ( ! empty( $settings_fields ) ) {

									$wps_membership_plan_target_tags = is_array( $settings_fields['wps_membership_plan_target_disc_tags'] ) ? array_map( 'absint', $settings_fields['wps_membership_plan_target_disc_tags'] ) : array();

									if ( $wps_membership_plan_target_tags ) {

										foreach ( $wps_membership_plan_target_tags as $single_target_tag_id ) {
											$tagn     = get_term_by( 'id', $single_target_tag_id, 'product_tag' );
											$tag_name = $tagn->name;
											?>

									<option value="<?php echo esc_html( $single_target_tag_id ); ?>" selected="selected"><?php echo( esc_html( $tag_name ) . '(#' . esc_html( $single_target_tag_id ) . ')' ); ?></option>

											<?php
										}
									}
								}
							}
						}

						?>

					</select>

				</td>

			</tr>
			<!-- Offer tags section end. -->

			<!-- Discount section start -->
			<tr class="wps-membership__plan--pro-disabled">
				<th scope="row" class="titledesc">
					<label for="wps_membership_product_price_type_id"><?php esc_html_e( 'Discount on Products', 'membership-for-woocommerce' ); ?></label>
				<?php
				$description = esc_html__( 'Specify discount applied to products.', 'membership-for-woocommerce' );
				$instance->tool_tip( $description );
				?>
				</th>

				<td class="forminp forminp-text">

					<?php
					$wps_membership_product_offer_price_type = '';
					$wps_membership_product_discount_price  = '';
					if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
						$check_licence = check_membership_pro_plugin_is_active();
						if ( $check_licence ) {
							$wps_membership_product_offer_price_type = $settings_fields['wps_membership_product_offer_price_type'];

							$wps_membership_product_discount_price = $settings_fields['wps_memebership_product_discount_price'];
							if ( empty( $wps_membership_product_discount_price ) ) {

								$wps_membership_product_discount_price = 0;
							}
						}
					}

					?>
					<select name="wps_membership_product_offer_price_type" id='wps_membership_product_offer_price_type_id' >

						<option <?php echo esc_html( '%' === $wps_membership_product_offer_price_type ? 'selected' : '' ); ?> value="%"><?php esc_html_e( 'Discount %', 'membership-for-woocommerce' ); ?></option>

						<option <?php echo esc_html( 'fixed' === $wps_membership_product_offer_price_type ? 'selected' : '' ); ?> value="fixed"><?php esc_html_e( 'Fixed price', 'membership-for-woocommerce' ); ?></option>

					</select>
					<input type="number" min="0" class="wps_membership product_offer_input_type" id="wps_membership_product_offer_price" name="wps_memebership_product_discount_price" value="<?php echo esc_attr( $wps_membership_product_discount_price ); ?>">

				</td>
			</tr>
		</table>
			<!-- Discount section End. -->
		</div>
	</div>


	<!-- Membership product section starts -->
	<div class="membership-offers">

	<!-- Offer section html start -->
	<div class="new_created_offers wps_membership_offers" id="new_created_offers" >

		<h2 class="wps_membership_offer_title" >
			<?php esc_html_e( 'Included Section', 'membership-for-woocommerce' ); ?>
		</h2>
		<h3>
			<?php esc_html_e( 'In Inlucde Section whatever the products, categories, tags you will choose, ONLY MEMBERS can buy these products and can access the pages.', 'membership-for-woocommerce' ); ?>
		</h3>
		<table>

			<!-- Offer Page section start -->
			<tr class="wps-membership__plan--pro-disabled">
				<th scope="row" class="titledesc">
					<label for="wps_membership_offer_page_select"><?php esc_html_e( 'Included pages', 'membership-for-woocommerce' ); ?></label>
					<?php
					$description = esc_html__( 'Select the pages you want to include in Membership Plan.', 'membership-for-woocommerce' );

					$instance->tool_tip( $description );
					?>
				</th>

				<td class="forminp forminp-text">
					<select id="wps_membership_plan_page_target_ids_search" class="wc-membership-page-search" multiple="multiple" name="wps_membership_plan_page_target_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a page&hellip;', 'membership-for-woocommerce' ); ?>">

						<?php
						if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
							$check_licence = check_membership_pro_plugin_is_active();
							if ( $check_licence ) {
								if ( ! empty( $settings_fields ) ) {

									$wps_membership_plan_target_page_ids = is_array( $settings_fields['wps_membership_plan_page_target_ids'] ) ? array_map( 'absint', $settings_fields['wps_membership_plan_page_target_ids'] ) : array();

									if ( $wps_membership_plan_target_page_ids ) {

										foreach ( $wps_membership_plan_target_page_ids as $wps_membership_plan_single_target_page_ids ) {

											$page_name = get_the_title( $wps_membership_plan_single_target_page_ids );
											?>

									<option value="<?php echo esc_html( $wps_membership_plan_single_target_page_ids ); ?>" <?php echo ( in_array( $wps_membership_plan_single_target_page_ids, $wps_membership_plan_target_page_ids, true ) ? 'selected' : '' ); ?>><?php echo( esc_html( $page_name ) . '(#' . esc_html( $wps_membership_plan_single_target_page_ids ) . ')' ); ?></option>

											<?php
										}
									}
								}
							}
						}

						?>

					</select>

				</td>	

			</tr>
			<!-- Offer page section End -->


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
							if ( $wps_membership_plan_target_product_ids ) {

								foreach ( $wps_membership_plan_target_product_ids as $wps_membership_plan_single_target_product_ids ) {

									$product_name = $instance->get_product_title( $wps_membership_plan_single_target_product_ids );
									?>

									<option value="<?php echo esc_html( $wps_membership_plan_single_target_product_ids ); ?>" <?php echo ( in_array( $wps_membership_plan_single_target_product_ids, $wps_membership_plan_target_product_ids, true ) ? 'selected' : '' ); ?>><?php echo( esc_html( $product_name ) . '(#' . esc_html( $wps_membership_plan_single_target_product_ids ) . ')' ); ?></option>

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

							if ( $wps_membership_plan_target_categories ) {

								foreach ( $wps_membership_plan_target_categories as $single_target_category_id ) {

									$category_name = $instance->get_category_title( $single_target_category_id );
									?>

									<option value="<?php echo esc_html( $single_target_category_id ); ?>" selected="selected"><?php echo( esc_html( $category_name ) . '(#' . esc_html( $single_target_category_id ) . ')' ); ?></option>

									<?php
								}
							}
						}

						?>

					</select>

				</td>

			</tr>
			<!-- Offer categories section end. -->


			<!-- Offer tags section start -->
			<tr class="wps-membership__plan--pro-disabled">
				<th scope="row" class="titledesc">
					<label for="wps_membership_offer_tag_select"><?php esc_html_e( 'Included Product Tags', 'membership-for-woocommerce' ); ?></label>
					<?php
					$description = esc_html__( 'Select the tags you want to included in Membership Plan.', 'membership-for-woocommerce' );

					$instance->tool_tip( $description );
					?>
				</th>

				<td class="forminp forminp-text">
					<select id="wps_membership_plan_target_tags_search" class="wc-membership-product-tag-search" multiple="multiple" name="wps_membership_plan_target_tags[]" data-placeholder="<?php esc_attr_e( 'Search for a tag&hellip;', 'membership-for-woocommerce' ); ?>">
						<?php
						if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
							$check_licence = check_membership_pro_plugin_is_active();
							if ( $check_licence ) {
								if ( ! empty( $settings_fields ) ) {

									$wps_membership_plan_target_tags = is_array( $settings_fields['wps_membership_plan_target_tags'] ) ? array_map( 'absint', $settings_fields['wps_membership_plan_target_tags'] ) : array();

									if ( $wps_membership_plan_target_tags ) {

										foreach ( $wps_membership_plan_target_tags as $single_target_tag_id ) {
											$tagn     = get_term_by( 'id', $single_target_tag_id, 'product_tag' );
											$tag_name = $tagn->name;
											?>

									<option value="<?php echo esc_html( $single_target_tag_id ); ?>" selected="selected"><?php echo( esc_html( $tag_name ) . '(#' . esc_html( $single_target_tag_id ) . ')' ); ?></option>

											<?php
										}
									}
								}
							}
						}

						?>

					</select>

				</td>

			</tr>
			<!-- Offer tags section end. -->

			<!-- Offer Post section start -->
			<tr class="wps-membership__plan--pro-disabled">

				<th scope="row" class="titledesc">
					<label for="wps_membership_offer_post_select"><?php esc_html_e( 'Included Posts', 'membership-for-woocommerce' ); ?></label>
					<?php
					$description = esc_html__( 'Select the posts you want to included in Membership Plan.', 'membership-for-woocommerce' );

					$instance->tool_tip( $description );
					?>
				</th>

				<td class="forminp forminp-text">
					<select id="wps_membership_plan_post_target_ids_search" class="wc-membership-post-search" multiple="multiple" name="wps_membership_plan_post_target_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a post&hellip;', 'membership-for-woocommerce' ); ?>">

						<?php
						if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
							$check_licence = check_membership_pro_plugin_is_active();
							if ( $check_licence ) {
								if ( ! empty( $settings_fields ) ) {

									$wps_membership_plan_target_post_ids = is_array( $settings_fields['wps_membership_plan_post_target_ids'] ) ? array_map( 'absint', $settings_fields['wps_membership_plan_post_target_ids'] ) : array();

									if ( $wps_membership_plan_target_post_ids ) {

										foreach ( $wps_membership_plan_target_post_ids as $wps_membership_plan_single_target_post_ids ) {

											$post_name = get_the_title( $wps_membership_plan_single_target_post_ids );
											?>

									<option value="<?php echo esc_html( $wps_membership_plan_single_target_post_ids ); ?>" <?php echo ( in_array( $wps_membership_plan_single_target_post_ids, $wps_membership_plan_target_post_ids, true ) ? 'selected' : '' ); ?>><?php echo( esc_html( $post_name ) . '(#' . esc_html( $wps_membership_plan_single_target_post_ids ) . ')' ); ?></option>

											<?php
										}
									}
								}
							}
						}

						?>

					</select>

				</td>	

			</tr>
			<!-- Offer post section End -->

			<!-- Offer categories section start -->
			<tr class="wps-membership__plan--pro-disabled">

				<th scope="row" class="titledesc">
					<label for="wps_membership_offer_post_category_select"><?php esc_html_e( 'Included Post Categories', 'membership-for-woocommerce' ); ?></label>
					<?php
					$description = esc_html__( 'Select the post categories you want to included in Membership Plan.', 'membership-for-woocommerce' );

					$instance->tool_tip( $description );
					?>
				</th>

				<td class="forminp forminp-text">
					<select id="wps_membership_plan_target_post_categories_search" class="wc-membership-post-category-search" multiple="multiple" name="wps_membership_plan_target_post_categories[]" data-placeholder="<?php esc_attr_e( 'Search for a post category&hellip;', 'membership-for-woocommerce' ); ?>">
						<?php
						if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
							$check_licence = check_membership_pro_plugin_is_active();
							if ( $check_licence ) {
								if ( ! empty( $settings_fields ) ) {

									$wps_membership_plan_target_post_categories = is_array( $settings_fields['wps_membership_plan_target_post_categories'] ) ? array_map( 'absint', $settings_fields['wps_membership_plan_target_post_categories'] ) : array();

									if ( $wps_membership_plan_target_post_categories ) {

										foreach ( $wps_membership_plan_target_post_categories as $single_target_category_id ) {

											$category_name = $instance->get_category_title( $single_target_category_id );
											?>

									<option value="<?php echo esc_html( $single_target_category_id ); ?>" selected="selected"><?php echo( esc_html( $category_name ) . '(#' . esc_html( $single_target_category_id ) . ')' ); ?></option>

											<?php
										}
									}
								}
							}
						}

						?>

					</select>

				</td>

			</tr>
			<!-- Offer categories section end. -->


			<!-- Offer tags section start -->
			<tr class="wps-membership__plan--pro-disabled">
				<th scope="row" class="titledesc">
					<label for="wps_membership_offer_post_tag_select"><?php esc_html_e( 'Included Post Tags', 'membership-for-woocommerce' ); ?></label>
					<?php
					$description = esc_html__( 'Select the post tags you want to included in Membership Plan.', 'membership-for-woocommerce' );

					$instance->tool_tip( $description );
					?>
				</th>

				<td class="forminp forminp-text">

					<select id="wps_membership_plan_target_post_tags_search" class="wc-membership-post-tag-search" multiple="multiple" name="wps_membership_plan_target_post_tags[]" data-placeholder="<?php esc_attr_e( 'Search for a post tag&hellip;', 'membership-for-woocommerce' ); ?>">
						<?php
						if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
							$check_licence = check_membership_pro_plugin_is_active();
							if ( $check_licence ) {
								if ( ! empty( $settings_fields ) ) {

									$wps_membership_plan_target_post_tags = is_array( $settings_fields['wps_membership_plan_target_post_tags'] ) ? array_map( 'absint', $settings_fields['wps_membership_plan_target_post_tags'] ) : array();

									if ( $wps_membership_plan_target_post_tags ) {

										foreach ( $wps_membership_plan_target_post_tags as $single_target_tag_id ) {
											$tagn = get_term_by( 'id', $single_target_tag_id, 'post_tag' );

											$tag_name = $tagn->name;
											?>

									<option value="<?php echo esc_html( $single_target_tag_id ); ?>" selected="selected"><?php echo( esc_html( $tag_name ) . '(#' . esc_html( $single_target_tag_id ) . ')' ); ?></option>

											<?php
										}
									}
								}
							}
						}

						?>

					</select>

				</td>

			</tr>
			<!-- Offer tags section end. -->

			<!-- Accessibility type start-->
			<tr id="mfw_membership_access_type" class="wps-membership__plan--pro-disabled">
				<th scope="row" class="titledesc">
					<label for="wps_membership_offer_access_type"><?php esc_html_e( 'Accessibility Type', 'membership-for-woocommerce' ); ?></label>
					<?php
						$description = esc_html__( 'Select the delay duration after which plan offers will be accessible.', 'membership-for-woocommerce' );

						$instance->tool_tip( $description );
					?>
				</th>

				<td id="mfw_offer_access_type">

				<?php
				$wps_membership_plan_access_type = '';
				$wps_membership_plan_time_duration = '';

				$wps_membership_plan_time_duration_type = '';

				if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
					$check_licence = check_membership_pro_plugin_is_active();
					if ( $check_licence ) {
								$wps_membership_plan_access_type = $settings_fields['wps_membership_plan_access_type'];

								$wps_membership_plan_time_duration = $settings_fields['wps_membership_plan_time_duration'];

								$wps_membership_plan_time_duration_type = $settings_fields['wps_membership_plan_time_duration_type'];

					}
				}
				?>
					<input type="radio" id="wps_membership_plan_immediate_type" name="wps_membership_plan_access_type" value="immediate_type" <?php echo esc_html( 'immediate_type' === $wps_membership_plan_access_type ? 'checked' : '' ); ?>>
					<label for="wps_membership_plan_immediate_type"><?php esc_html_e( 'Immediately', 'membership-for-woocommerce' ); ?></label>

					<input type="radio" id="wps_membership_plan_time_type" name="wps_membership_plan_access_type" value="delay_type" <?php echo esc_html( 'delay_type' === $wps_membership_plan_access_type ? 'checked' : '' ); ?>>
					<label for="wps_membership_plan_time_type"><?php esc_html_e( 'Specify a time', 'membership-for-woocommerce' ); ?></label>

					<div id="wps_membership_plan_time_duratin_display">
						<input type="number" id="wps_membership_plan_time_duration" name="wps_membership_plan_time_duration" value="<?php echo esc_attr( $wps_membership_plan_time_duration ); ?>" min="1" max="31" >
						<select name="wps_membership_plan_time_duration_type" id="wps_membership_plan_time_duration_type" >
							<option <?php echo esc_html( 'days' === $wps_membership_plan_time_duration_type ? 'selected' : '' ); ?> value="days"><?php esc_html_e( 'Days', 'membership-for-woocommerce' ); ?></option>
							<option <?php echo esc_html( 'weeks' === $wps_membership_plan_time_duration_type ? 'selected' : '' ); ?> value="weeks"><?php esc_html_e( 'Weeks', 'membership-for-woocommerce' ); ?></option>
						</select>
					</div>

				</td>

			</tr>
			<!-- Accessibility type end -->
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

						$wps_membership_plan_discount_price = $settings_fields['wps_memebership_plan_discount_price'];
						if ( empty( $wps_membership_plan_discount_price ) ) {

							$wps_membership_plan_discount_price = 0;
						}


						?>
						<select name="wps_membership_plan_offer_price_type" id='wps_membership_plan_offer_price_type_id' >

							<option <?php echo esc_html( '%' === $wps_membership_plan_offer_price_type ? 'selected' : '' ); ?> value="%"><?php esc_html_e( 'Discount %', 'membership-for-woocommerce' ); ?></option>

							<option <?php echo esc_html( 'fixed' === $wps_membership_plan_offer_price_type ? 'selected' : '' ); ?> value="fixed"><?php esc_html_e( 'Fixed price', 'membership-for-woocommerce' ); ?></option>

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

						<input type="checkbox"  class="wps_membership_plan_offer_free_shipping" name="wps_memebership_plan_free_shipping" value="yes" <?php checked( 'yes', $wps_membership_plan_free_shipping ); ?> >

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

				<!-- Hide products section start-->
				<tr class="wps-membership__plan--pro-disabled">
					<th scope="row" class="titledesc">
						<label for="wps_membership_plan_hide_products"><?php esc_html_e( 'Hide Products From non-Members', 'membership-for-woocommerce' ); ?></label>
						<?php
						$description = esc_html__( 'Hide the member exclusive products from non members ', 'membership-for-woocommerce' );
						$instance->tool_tip( $description );
						?>
					</th>

					<td id="mfw_hide_products" class="forminp forminp-text">

						<?php
						$wps_membership_plan_hide_products = '';

						$wps_mfw_disable = 'disabled';
						if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
							$check_licence = check_membership_pro_plugin_is_active();
							if ( $check_licence ) {
												$wps_membership_plan_hide_products = $settings_fields['wps_membership_plan_hide_products'];
												$wps_mfw_disable = '';
							}
						}

						?>


						<input type="checkbox" <?php echo esc_attr( $wps_mfw_disable ); ?> class="wps_membership_plan_hide_products" name="wps_membership_plan_hide_products" value="yes" <?php checked( 'yes', $wps_membership_plan_hide_products ); ?> >
					</td>
				</tr>
				<!-- Hide products section end. -->

				<!-- Show Notice section start-->
				<tr class="wps-membership__plan--pro-disabled">
					<th scope="row" class="titledesc">
						<label for="wps_membership_show_notice"><?php esc_html_e( 'Show Notice to Members', 'membership-for-woocommerce' ); ?></label>
						<?php
						$description = esc_html__( 'This will Show notice to the members at front-end', 'membership-for-woocommerce' );
						$instance->tool_tip( $description );
						?>
					</th>

					<td id="mfw_show_notice" class="forminp forminp-text">

						<?php
						$wps_membership_show_notice = '';
						if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
							$check_licence = check_membership_pro_plugin_is_active();
							if ( $check_licence ) {
												$wps_membership_show_notice = $settings_fields['wps_membership_show_notice'];

							}
						}

						?>

						<input type="checkbox" <?php echo esc_attr( $wps_mfw_disable ); ?>  class="wps_membership_show_notice" name="wps_membership_show_notice" value="yes" <?php checked( 'yes', $wps_membership_show_notice ); ?> >

						<?php
						$wps_membership_notice_message = $settings_fields['wps_membership_notice_message'];
						?>
						<!-- manage free shipping link start. -->
						<div class="wps_membership_notice_message" >
							<p class="wps_membership_notice_message">
								<input type="text" class="wps_membership_notice_message" name="wps_membership_notice_message" value="<?php echo isset( $wps_membership_notice_message ) ? esc_html( $wps_membership_notice_message ) : ''; ?>" >
							</p>
						</div>
						<!-- Manage free shipping link end. -->

					</td>
				</tr>
				<!-- Show Notice section end. -->

			</table>
		</div>
		<!-- PAR Compatible -->
		<?php do_action( 'wps_wpr_extend_membership_metabox_field', $settings_fields, $instance, $post ); ?>
	</div>
</div>
<!-- Plans creation end. -->

