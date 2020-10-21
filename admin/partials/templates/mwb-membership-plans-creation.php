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

// Get all plans list.
$mwb_membership_plans_list = get_option( 'mwb_membership_plans_list', array() );

if ( ! empty( $mwb_membership_plans_list ) ) {

	reset( $mwb_membership_plans_list );

	$mwb_membership_plan_id = key( $mwb_membership_plans_list );

} else {

	// New plan id.
	$mwb_membership_plan_id = 1;
}


// When save changes button is clicked.
if ( isset( $_POST['mwb_membership_plan_creation_setting_save'] ) ) {

	// Nonce verification.
	check_admin_referer( 'mwb_membership_plans_creation_nonce', 'mwb_membership_plans_nonce' );

	// Save memberhsip plan id.
	$mwb_membership_plan_id = ! empty( $_POST['mwb_membership_plan_id'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_plan_id'] ) ) : 1;

	if ( empty( $_POST['mwb_membership_plan_target_categories'] ) ) {

		$_POST['mwb_membership_plan_target_categories'] = array();
	}

	if ( empty( $_POST['mwb_membership_plan_target_ids'] ) ) {

		$_POST['mwb_membership_plan_target_ids'] = array();
	}

	if ( empty( $_POST['mwb_membership_plan_status'] ) ) {

		$_POST['mwb_membership_plan_status'] = 'no';
	}

	if ( empty( $_POST['mwb_memebership_plan_discount_price'] ) ) {

		if ( '' == $_POST['mwb_memebership_plan_discount_price'] ) {

			$_POST['mwb_memebership_plan_discount_price'] = '10';

		} else {

			$_POST['mwb_memebership_plan_discount_price'] = '0';
		}
	}

	// New plan.
	$mwb_membership_new_plan = array();

	// Sanitize and strip slashes for text fields.

	$mwb_membership_new_plan['mwb_membership_plan_status'] = ! empty( $_POST['mwb_membership_plan_status'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_plan_status'] ) ) : '';

	$mwb_membership_new_plan['mwb_membership_plan_name'] = ! empty( $_POST['mwb_membership_plan_name'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_plan_name'] ) ) : '';

	$mwb_membership_new_plan['mwb_membership_plan_name_access_type'] = ! empty( $_POST['mwb_membership_plan_name_access_type'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_plan_name_access_type'] ) ) : '';

	$mwb_membership_new_plan['mwb_membership_plan_duration'] = ! empty( $_POST['mwb_membership_plan_duration'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_plan_duration'] ) ) : '';

	$mwb_membership_new_plan['mwb_membership_plan_duration_type'] = ! empty( $_POST['mwb_membership_plan_duration_type'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_plan_duration_type'] ) ) : '';

	$mwb_membership_new_plan['mwb_membership_plan_start'] = ! empty( $_POST['mwb_membership_plan_start'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_plan_start'] ) ) : '';

	$mwb_membership_new_plan['mwb_membership_plan_end'] = ! empty( $_POST['mwb_membership_plan_end'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_plan_end'] ) ) : '';

	$mwb_membership_new_plan['mwb_membership_plan_user_access'] = ! empty( $_POST['mwb_membership_plan_user_access'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_plan_user_access'] ) ) : '';

	$mwb_membership_new_plan['mwb_membership_plan_access_type'] = ! empty( $_POST['mwb_membership_plan_access_type'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_plan_access_type'] ) ) : '';

	$mwb_membership_new_plan['mwb_membership_plan_time_duration'] = ! empty( $_POST['mwb_membership_plan_time_duration'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_plan_time_duration'] ) ) : '';

	$mwb_membership_new_plan['mwb_membership_plan_time_duration_type'] = ! empty( $_POST['mwb_membership_plan_time_duration_type'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_plan_time_duration_type'] ) ) : '';

	$mwb_membership_new_plan['mwb_membership_plan_offer_price_type'] = ! empty( $_POST['mwb_membership_plan_offer_price_type'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_plan_offer_price_type'] ) ) : '';

	$mwb_membership_new_plan['mwb_memebership_plan_discount_price'] = ! empty( $_POST['mwb_memebership_plan_discount_price'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_memebership_plan_discount_price'] ) ) : '';

	$mwb_membership_new_plan['mwb_memebership_plan_free_shipping'] = ! empty( $_POST['mwb_memebership_plan_free_shipping'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_memebership_plan_free_shipping'] ) ) : '';

	// Sanitize and strip slashes of all arrays.

	$mwb_membership_new_plan['mwb_membership_plan_target_categories'] = ! empty( $_POST['mwb_membership_plan_target_categories'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['mwb_membership_plan_target_categories'] ) ) : '';

	$mwb_membership_new_plan['mwb_membership_plan_target_ids'] = ! empty( $_POST['mwb_membership_plan_target_ids'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['mwb_membership_plan_target_ids'] ) ) : '';

	// Parent array of all plans.
	$mwb_membership_plan_series = array();

	// Post plan data as an array at it's respective id.
	$mwb_membership_plan_series[ $mwb_membership_plan_id ] = $mwb_membership_new_plan;

	echo '<pre>';
	print_r($mwb_membership_plan_series);
	echo '</pre>';

	// Save the plan.
	update_option( 'mwb_membership_plans_list', $mwb_membership_plan_series );

	?>

	<!-- Settings saved notice. -->
	<div class="notice notice-success is-dismissible mwb-notice">
		<p><strong><?php esc_html_e( 'Settings saved', 'membership-for-woocommerce' ); ?></strong></p>
	</div>

	<?php
}

// Get all bump.
$mwb_membership_plans_list = get_option( 'mwb_membership_plans_list', array() );

/**
 * This template is for Membership plans creation as well as Edit/Update plans
 */

?>

<!-- For single membership -->
<form action="" method="POST">
	<div class="mwb_membership_table">
		<table class="form-table mwb_membership_plans_creation_setting">
			<tbody>

				<!-- Nonce Field -->
				<?php wp_nonce_field( 'mwb_membership_plans_creation_nonce', 'mwb_membership_plans_nonce' ); ?>

				<input type="hidden" name="mwb_membership_plan_id" value="<?php echo esc_html( $mwb_membership_plan_id ); ?>">

				<?php

				$membership_plan_name = ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_name'] ) ? sanitize_text_field( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_name'] ) : esc_html__( 'Membership Plan', 'membership-for-woocommerce' ) . " #$mwb_membership_plan_id";

				$membership_plan_status = ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_status'] ) ? sanitize_text_field( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_status'] ) : 'no';

				?>

				<!-- Membership Plans Header start -->
				<div id="mwb_membership_plan_name_heading">
					<h2><?php echo esc_html( $membership_plan_name ); ?></h2>
					<div id="mwb_membership_plan_status" >
						<label>
							<input type="checkbox" id="mwb_membership_plan_status_input" name="mwb_membership_plan_status" value="yes" <?php checked( 'yes', $membership_plan_status ); ?> >
							<span class="mwb_membership_plan_span"></span>
						</label>

						<span class="mwb_membership_plan_status_on <?php echo 'yes' == $membership_plan_status ? 'active' : ''; ?>"><?php esc_html_e( 'Live', 'membership-for-woocommerce' ); ?></span>
						<span class="mwb_membership_plan_status_off <?php echo 'no' == $membership_plan_status ? 'active' : ''; ?>"><?php esc_html_e( 'Sandbox', 'membership-for-woocommerce' ); ?></span>
					</div>
				</div>

				<!-- Membership Plan Name Start. -->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="mwb_membership_plan_name"><?php esc_html_e( 'Membership Plan Name', 'membership-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php

						$description = esc_html__( 'Provide the name of your Membership Plan', 'membership-for-woocommerce' );

						mwb_membership_for_woo_tool_tip( $description );


						?>

						<input type="text" id="mwb_membership_plan_name" name="mwb_membership_plan_name" value="<?php echo esc_attr( $membership_plan_name ); ?>" class="input-text mwb_membership_plan_commone_class" required="" maxlength="30">
					</td>
				</tr>
				<!-- Membership Plan Name End. -->

				<!-- Access Type start -->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="mwb_membership_plan_access_type"><?php esc_html_e( 'Access Type', 'membership-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php

						$description = esc_html__( 'Provide the Access Type of your Membership Plan', 'membership-for-woocommerce' );

						mwb_membership_for_woo_tool_tip( $description );

						$mwb_membership_plan_access_type = ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_name_access_type'] ) ? sanitize_text_field( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_name_access_type'] ) : 'lifetime';

						?>

						<select id="mwb_membership_plan_access_type" name="mwb_membership_plan_name_access_type">
							<option <?php echo esc_html( 'lifetime' == $mwb_membership_plan_access_type ? 'selected' : '' ); ?> value="lifetime"><?php esc_html_e( 'Lifetime', 'membership-for-woocommerce' ); ?></option>

							<option <?php echo esc_html( 'limited' == $mwb_membership_plan_access_type ? 'selected' : '' ); ?> value="limited"><?php esc_html_e( 'Limited', 'membership-for-woocommerce' ); ?></option>

							<option <?php echo esc_html( 'date_ranged' == $mwb_membership_plan_access_type ? 'selected' : '' ); ?> value="date_ranged"><?php esc_html_e( 'Date Ranged', 'membership-for-woocommerce' ); ?></option>
						</select>
					</td>
				</tr>
				<!-- Access Type End -->

				<!-- Plan Duration start. -->
				<tr valign="top" id="mwb_membership_duration" style="display: none;">

					<th scope="row" class="titledesc">
						<label for="mwb_membership_plan_duration"><?php esc_html_e( 'Duration', 'membership-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php

						$description = esc_html__( 'Provide the number of days the plan will be active', 'membership-for-woocommerce' );

						mwb_membership_for_woo_tool_tip( $description );

						$mwb_membership_plan_duration = ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_duration'] ) ? sanitize_text_field( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_duration'] ) : 1;

						$mwb_membership_plan_duration_type = ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_duration_type'] ) ? sanitize_text_field( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_duration_type'] ) : 'days';
						?>

						<input type="number" id="mwb_membership_plan_duration" name="mwb_membership_plan_duration" value="<?php echo esc_attr( $mwb_membership_plan_duration ); ?>" min="1" max="31">
						<select name="mwb_membership_plan_duration_type" id="mwb_membership_plan_duration_type">
							<option <?php echo esc_html( 'days' == $mwb_membership_plan_duration_type ? 'selected' : '' ); ?> value="days"><?php esc_html_e( 'Days', 'membership-for-woocommerce' ); ?></option>
							<option <?php echo esc_html( 'weeks' == $mwb_membership_plan_duration_type ? 'selected' : '' ); ?> value="weeks"><?php esc_html_e( 'Weeks', 'membership-for-woocommerce' ); ?></option>
							<option <?php echo esc_html( 'months' == $mwb_membership_plan_duration_type ? 'selected' : '' ); ?> value="months"><?php esc_html_e( 'Months', 'membership-for-woocommerce' ); ?></option>
							<option <?php echo esc_html( 'years' == $mwb_membership_plan_duration_type ? 'selected' : '' ); ?> value="years"><?php esc_html_e( 'Years', 'membership-for-woocommerce' ); ?></option>
						</select>
					</td>
				</tr>
				<!-- Plan Duration End. -->

				<!-- Plan Date Range start -->
				<tr valign="top" id="mwb_membership_date_range_start" style="display: none;">

					<th scope="row" class="titledesc">
						<label for="mwb_membership_plan_start_date"><?php esc_html_e( 'Start Date', 'membership-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php

						$description = esc_html__( 'Provide the Start date of the plan.', 'membership-for-woocommerce' );

						mwb_membership_for_woo_tool_tip( $description );

						$mwb_membership_plan_start = ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_start'] ) ? sanitize_text_field( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_start'] ) : '';

						?>

						<input type="date" id="mwb_membership_plan_start" name="mwb_membership_plan_start" value="<?php echo esc_attr( $mwb_membership_plan_start ); ?>" >
					</td>
				</tr>
				<tr id="mwb_membership_date_range_end" style="display: none;">
					<th scope="row" class="titledesc">
						<label for="mwb_membership_plan_end_date"><?php esc_html_e( 'End Date', 'membership-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php

						$description = esc_html__( 'Provide the End date of the plan.', 'membership-for-woocommerce' );

						mwb_membership_for_woo_tool_tip( $description );

						$mwb_membership_plan_end = ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_end'] ) ? sanitize_text_field( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_end'] ) : '';

						?>

						<input type="date" id="mwb_membership_plan_end" name="mwb_membership_plan_end" value="<?php echo esc_attr( $mwb_membership_plan_end ); ?>" >
					</td>
				</tr>
				<!-- Plan Date Range End. -->

				<!-- Show history to user start -->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="mwb_membership_plan_user_access"><?php esc_html_e( 'Show History to User', 'membership-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php

						$description = esc_html__( 'This will Enable Users to visit and see Plans Histroy in Membership tab  on My Account page.', 'membership-for-woocommerce' );

						mwb_membership_for_woo_tool_tip( $description );

						$mwb_membership_plan_user_access = ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_user_access'] ) ? sanitize_text_field( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_user_access'] ) : 'no';

						?>

						<input type="checkbox" id="mwb_membership_plan_user_access" name="mwb_membership_plan_user_access" value="yes" <?php checked( 'yes', $mwb_membership_plan_user_access ); ?>>
					</td>
				</tr>
				<!-- Show history to user end -->

			</tbody>
		</table>

		<div class="mwb_membership_plan_products">
			<h1><?php esc_html_e( 'Membership Plan Offers', 'membership-for-woocommerce' ); ?></h1>
		</div>

		<!-- Membership product section starts -->
		<div class="membership-offers">

			<!-- Offer section html start -->
			<div class="new_created_offers mwb_membership_offers" id="new_created_offers" >

				<h2 class="mwb_membership_offer_title" >
					<?php esc_html_e( 'Offer Section', 'membership-for-woocommerce' ); ?>
				</h2>

				<table>
					<!-- Offer Product section start -->
					<tr>

						<th scope="row" class="titledesc">
							<label for="mwb_membership_offer_product_select"><?php esc_html_e( 'Offered Products', 'membership-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">

							<select id="mwb_memberhsip_plan_target_ids_search" class="wc-membership-product-search" multiple="multiple" name="mwb_membership_plan_target_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'membership-for-woocommerce' ); ?>">


								<!-- <option value="" selected="selected"></option>'; -->


							</select>

							<span class="mwb_membership_plan_description mwb_membership_plan_desc_text"><?php esc_html_e( 'Select the products you want to offer in Membership Plan.', 'memberhsip-for-woocommerce' ); ?></span>

						</td>	

					</tr>
					<!-- Offer product section End -->

					<!-- Offer categories section start -->
					<tr>

						<th scope="row" class="titledesc">
							<label for="mwb_membership_offer_category_select"><?php esc_html_e( 'Offered Categories', 'membership-for-woocommerce' ); ?></label>
						</th>

						<td class="forminp forminp-text">

							<select id="mwb_membership_plan_target_categories_search" class="wc-membership-product-category-search" multiple="multiple" name="mwb_membership_plan_target_categories[]" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'membership-for-woocommerce' ); ?>">

								<!-- <option value="" selected="selected"></option>'; -->

							</select>

							<span class="mwb_membership_plan_description mwb_membership_plan_desc_text"><?php esc_html_e( 'Select the categories you want to offer in Membership Plan.', 'memberhsip-for-woocommerce' ); ?></span>

						</td>

					</tr>
					<!-- Offer categories section end. -->

					<!-- Accessibility type start-->
					<tr>
						<th scope="row" class="titledesc">
							<label for="mwb_membership_offer_access_type"><?php esc_html_e( 'Accessibility Type', 'membership-for-woocommerce' ); ?></label>
						</th>

						<td>
							<input type="radio" id="mwb_membership_plan_immediate_type" name="mwb_membership_plan_access_type" value="immediate_type">
							<label for="mwb_membership_plan_immediate_type"><?php esc_html_e( 'Immediately', 'membership-for-woocommerce' ); ?></label>

							<input type="radio" id="mwb_membership_plan_time_type" name="mwb_membership_plan_access_type" value="delay_type">
							<label for="mwb_membership_plan_time_type"><?php esc_html_e( 'Specifiy a time', 'membership-for-woocommerce' ); ?></label>

							<div id="mwb_membership_plan_time_duratin_display" style="display: none;">
								<input type="number" id="mwb_membership_plan_time_duration" name="mwb_membership_plan_time_duration" value="" min="1" max="31" >
								<select name="mwb_membership_plan_time_duration_type" id="mwb_membership_plan_time_duration_type" >
									<option value="days"><?php esc_html_e( 'Days', 'membership-for-woocommerce' ); ?></option>
									<option value="weeks"><?php esc_html_e( 'Weeks', 'membership-for-woocommerce' ); ?></option>
								</select>
							</div>
						</td>

					</tr>
					<!-- Accessibility type end -->
				</table>

			</div>

			<div class="membership-features">

				<!-- Membership features section start -->
				<div class="new_created_offers mwb_membership_offers">

					<h2 class="mwb_membership_offer_title" >
						<?php esc_html_e( 'Mmebership Features Section', 'membership-for-woocommerce' ); ?>
					</h2>

					<table>
						<!-- Discount section start -->
						<tr>
							<th scope="row" class="titledesc">
								<label for="mwb_membership_plan_price_type_id"><?php esc_html_e( 'Offer Price/Discount', 'membership-for-woocommerce' ); ?></label>
							</th>

							<td class="forminp forminp-text">
								<select name="mwb_membership_plan_offer_price_type" id = 'mwb_membership_plan_offer_price_type_id' >

									<option value="%"><?php esc_html_e( 'Discount %', 'membership-for-woocommerce' ); ?></option>

								</select>
								<input type="text" class="mwb_membership plan_offer_input_type" id="mwb_membership_plan_offer_price" name="mwb_memebership_plan_discount_price" value="">
								<span class="mwb_membership_plan_description"><?php esc_html_e( 'Specify discount % offered with this plan.', 'membership-for-woocommerce' ); ?></span>

							</td>
						</tr>
						<!-- Discount section End. -->

						<!-- Fress shipping section start-->
						<tr>
							<th scope="row" class="titledesc">
								<label for="mwb_membership_plan_free_shipping"><?php esc_html_e( 'Allow Free Shipping', 'membership-for-woocommerce' ); ?></label>
							</th>

							<td class="forminp forminp-text">

								<input type="checkbox"  class="mwb_membership_plan_offer_free_shipping" name="mwb_memebership_plan_free_shipping" value="yes">
								<span class="mwb_membership_plan_description"><?php esc_html_e( 'Allow Free Shipping to all the members of this membership plan', 'membership-for-woocommerce' ); ?></span>

							</td>
						</tr>
						<!-- Free shiping section end. -->
					</table>

				</div>

			</div>

		</div>

		<!-- Save Changes for whole membership plan -->
		<p class="submit">
			<input type="submit" value="<?php esc_html_e( 'Save Changes', 'membership-for-woocommerce' ); ?>" class="button-primary woocommerce-save-button" name="mwb_membership_plan_creation_setting_save" id="mwb_membership_plan_creation_setting_save" >
		</p>

	</div>

</form>
