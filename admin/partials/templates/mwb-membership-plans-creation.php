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

	unset( $_POST['mwb_membership_plan_creation_setting_save'] );

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

	$mwb_membership_new_plan['mwb_membership_plan_price'] = ! empty( $_POST['mwb_membership_plan_price'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_plan_price'] ) ) : '';

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

	// Card template design settings.
	$card_design_settings_post['parent_border_type']      = ! empty( $_POST['parent_border_type'] ) ? sanitize_text_field( wp_unslash( $_POST['parent_border_type'] ) ) : '';
	$card_design_settings_post['parent_border_color']     = ! empty( $_POST['parent_border_color'] ) ? sanitize_text_field( wp_unslash( $_POST['parent_border_color'] ) ) : '';
	$card_design_settings_post['top_vertical_spacing']    = ! empty( $_POST['top_vertical_spacing'] ) ? sanitize_text_field( wp_unslash( $_POST['top_vertical_spacing'] ) ) : '';
	$card_design_settings_post['bottom_vertical_spacing'] = ! empty( $_POST['bottom_vertical_spacing'] ) ? sanitize_text_field( wp_unslash( $_POST['bottom_vertical_spacing'] ) ) : '';

	unset( $_POST['parent_border_type'] );
	unset( $_POST['parent_border_color'] );
	unset( $_POST['top_vertical_spacing'] );
	unset( $_POST['bottom_vertical_spacing'] );

	// Price section design settings.
	$card_design_settings_post['price_section_background_color'] = ! empty( $_POST['price_section_background_color'] ) ? sanitize_text_field( wp_unslash( $_POST['price_section_background_color'] ) ) : '';
	$card_design_settings_post['price_section_text_color']       = ! empty( $_POST['price_section_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['price_section_text_color'] ) ) : '';
	$card_design_settings_post['price_section_text_size']        = ! empty( $_POST['price_section_text_size'] ) ? sanitize_text_field( wp_unslash( $_POST['price_section_text_size'] ) ) : '';

	unset( $_POST['price_section_background_color'] );
	unset( $_POST['price_section_text_color'] );
	unset( $_POST['price_section_text_size'] );

	// Buy Now button design settings.
	$card_design_settings_post['button_section_background_color'] = ! empty( $_POST['button_section_background_color'] ) ? sanitize_text_field( wp_unslash( $_POST['button_section_background_color'] ) ) : '';
	$card_design_settings_post['button_section_text_color']       = ! empty( $_POST['button_section_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['button_section_text_color'] ) ) : '';
	$card_design_settings_post['button_section_text_size']        = ! empty( $_POST['button_section_text_size'] ) ? sanitize_text_field( wp_unslash( $_POST['button_section_text_size'] ) ) : '';

	unset( $_POST['button_section_background_color'] );
	unset( $_POST['button_section_text_color'] );
	unset( $_POST['button_section_text_size'] );

	// Plan description design settings.
	$card_design_settings_post['description_section_text_color'] = ! empty( $_POST['description_section_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['description_section_text_color'] ) ) : '';
	$card_design_settings_post['description_section_text_size']  = ! empty( $_POST['description_section_text_size'] ) ? sanitize_text_field( wp_unslash( $_POST['description_section_text_size'] ) ) : '';

	unset( $_POST['description_section_text_color'] );
	unset( $_POST['description_section_text_size'] );

	$mwb_membership_new_plan['design_css'] = $card_design_settings_post;

	$card_text_settings_post = array(

		'mwb_membership_plan_decsription_text' => ! empty( $_POST['mwb_membership_plan_decsription_text'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_plan_decsription_text'] ) ) : '',

		'mwb_membership_plan_title'            => ! empty( $_POST['mwb_membership_plan_title'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_membership_plan_title'] ) ) : '',
	);

	unset( $_POST['mwb_membership_plan_decsription_text'] );
	unset( $_POST['mwb_membership_plan_title'] );

	$mwb_membership_new_plan['design_text'] = $card_text_settings_post;

	// Parent array of all plans.
	$mwb_membership_plan_series = array();

	// Post plan data as an array at it's respective id.
	$mwb_membership_plan_series[ $mwb_membership_plan_id ] = $mwb_membership_new_plan;

	// echo '<pre>';
	// print_r($mwb_membership_plan_series);
	// echo '</pre>';

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

echo '<pre>';
print_r($mwb_membership_plans_list);
echo '</pre>';

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

				<!-- Memberhship plan price start  -->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="mwb_membership_plan_price"><?php esc_html_e( 'Membership Plan Amount', 'membership-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php

						$description = esc_html__( 'Provide the amount at which Membership Plan will be available for Users.', 'membership-for-woocommerce' );

						mwb_membership_for_woo_tool_tip( $description );

						$mwb_membership_plan_price = ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_price'] ) ? sanitize_text_field( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_price'] ) : '0';
						?>

						<input type="text" id="mwb_membership_plan_price" name="mwb_membership_plan_price" value="<?php echo esc_attr( $mwb_membership_plan_price ); ?>">
					</td>
				</tr>
				<!-- Membership plan price end. -->

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

								<?php

								if ( ! empty( $mwb_membership_plans_list ) ) {

									$mwb_membership_plan_target_products = isset( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_target_ids'] ) ? $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_target_ids'] : array();
									// echo '<pre>';
									// print_r($mwb_membership_plan_target_products);
									// echo '</pre>';
									$mwb_membership_plan_target_product_ids = ! empty( $mwb_membership_plan_target_products ) ? array_map( 'absint', $mwb_membership_plan_target_products ) : null;

									if ( $mwb_membership_plan_target_product_ids ) {

										foreach ( $mwb_membership_plan_target_product_ids as $mwb_membership_plan_single_target_product_ids ) {

											$product_name = mwb_membership_for_woo_get_product_title( $mwb_membership_plan_single_target_product_ids );
											?>

											<option value="<?php echo esc_html( $mwb_membership_plan_single_target_product_ids ); ?>" selected="selected"><?php echo( esc_html( $product_name ) . '(#' . esc_html( $mwb_membership_plan_single_target_product_ids ) . ')' ); ?></option>

											<?php
										}
									}
								}

								?>

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
								<?php

								if ( ! empty( $mwb_membership_plans_list ) ) {

									$mwb_membership_plan_target_categories = isset( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_target_categories'] ) ? $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_target_categories'] : array();

									$mwb_membership_plan_target_categories = ! empty( $mwb_membership_plan_target_categories ) ? array_map( 'absint', $mwb_membership_plan_target_categories ) : null;

									if ( $mwb_membership_plan_target_categories ) {

										foreach ( $mwb_membership_plan_target_categories as $single_target_category_id ) {

											$category_name = mwb_membership_for_woo_get_category_title( $single_target_category_id );
											?>

											<option value="<?php echo esc_html( $single_target_category_id ); ?>" selected="selected"><?php echo( esc_html( $category_name ) . '(#' . esc_html( $single_target_category_id ) . ')' ); ?></option>

											<?php
										}
									}
								}

								?>

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

						<?php

						$mwb_membership_plan_access_type = ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_access_type'] ) ? sanitize_text_field( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_access_type'] ) : '';

						$mwb_membership_plan_time_duration = ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_time_duration'] ) ? sanitize_text_field( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_time_duration'] ) : 1;

						$mwb_membership_plan_time_duration_type = ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_time_duration_type'] ) ? sanitize_text_field( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_time_duration_type'] ) : 'days';

						?>
							<input type="radio" id="mwb_membership_plan_immediate_type" name="mwb_membership_plan_access_type" value="immediate_type" <?php echo esc_html( 'immediate_type' == $mwb_membership_plan_access_type ? 'checked' : '' ); ?>>
							<label for="mwb_membership_plan_immediate_type"><?php esc_html_e( 'Immediately', 'membership-for-woocommerce' ); ?></label>

							<input type="radio" id="mwb_membership_plan_time_type" name="mwb_membership_plan_access_type" value="delay_type" <?php echo esc_html( 'delay_type' == $mwb_membership_plan_access_type ? 'checked' : '' ); ?>>
							<label for="mwb_membership_plan_time_type"><?php esc_html_e( 'Specifiy a time', 'membership-for-woocommerce' ); ?></label>

							<div id="mwb_membership_plan_time_duratin_display" style="display: none;">
								<input type="number" id="mwb_membership_plan_time_duration" name="mwb_membership_plan_time_duration" value="<?php echo esc_attr( $mwb_membership_plan_time_duration ); ?>" min="1" max="31" >
								<select name="mwb_membership_plan_time_duration_type" id="mwb_membership_plan_time_duration_type" >
									<option <?php echo esc_html( 'days' == $mwb_membership_plan_time_duration_type ? 'selected' : '' ); ?> value="days"><?php esc_html_e( 'Days', 'membership-for-woocommerce' ); ?></option>
									<option <?php echo esc_html( 'weeks' == $mwb_membership_plan_time_duration_type ? 'selected' : '' ); ?> value="weeks"><?php esc_html_e( 'Weeks', 'membership-for-woocommerce' ); ?></option>
								</select>
								<span class="mwb_membership_plan_description mwb_membership_plan_desc_text"><?php esc_html_e( 'Select the delay duration in after which plan offers will be accessible.', 'memberhsip-for-woocommerce' ); ?></span>
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

								<?php

								$mwb_membership_plan_offer_price_type = ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_offer_price_type'] ) ? sanitize_text_field( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_membership_plan_offer_price_type'] ) : '';

								$mwb_membership_plan_discount_price = ( ! empty( $mwb_membership_plans_list ) && '' != $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_memebership_plan_discount_price'] ) ? $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_memebership_plan_discount_price'] : '10';

								?>
								<select name="mwb_membership_plan_offer_price_type" id = 'mwb_membership_plan_offer_price_type_id' >

									<option value="%"><?php esc_html_e( 'Discount %', 'membership-for-woocommerce' ); ?></option>

								</select>
								<input type="text" class="mwb_membership plan_offer_input_type" id="mwb_membership_plan_offer_price" name="mwb_memebership_plan_discount_price" value="<?php echo esc_attr( $mwb_membership_plan_discount_price ); ?>">
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

								<?php

								$mwb_membership_plan_free_shipping = ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_memebership_plan_free_shipping'] ) ? sanitize_text_field( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['mwb_memebership_plan_free_shipping'] ) : 'no';

								?>

								<input type="checkbox"  class="mwb_membership_plan_offer_free_shipping" name="mwb_memebership_plan_free_shipping" value="yes" <?php checked( 'yes', $mwb_membership_plan_free_shipping ); ?> >
								<span class="mwb_membership_plan_description"><?php esc_html_e( 'Allow Free Shipping to all the members of this membership plan', 'membership-for-woocommerce' ); ?></span>

							</td>
						</tr>
						<!-- Free shiping section end. -->
					</table>

				</div>

			</div>

			<!-- Membership Template section start -->

			<div class="mwb_membership_plan_templates"><?php esc_html_e( 'Membership Cards Template', 'membership-for-woocommerce' ); ?></div>

			<!-- Nav starts. -->
			<nav class="nav-tab-wrapper mwb-membership-appearance-nav-tab">
				<a class="nav-tab mwb-membership-appearance-card nav-tab-active" href="javascript:void(0);"><?php esc_html_e( 'Cards', 'membership-for-woocommerce' ); ?></a>
				<a class="nav-tab mwb-membership-design" href="javascript:void(0);"><?php esc_html_e( 'Card Design', 'membership-for-woocommerce' ); ?></a>
				<a class="nav-tab mwb-membership-text" href="javascript:void(0);"><?php esc_html_e( 'Card Content', 'membership-for-woocommerce' ); ?></a>
			</nav>
			<!-- Nav ends. -->

			<!-- Cards appearance start. -->
			<div class="mwb_membership_card_div_wrapper">

				<!-- Card template start. -->
				<div class="mwb-membership-card-template-section">

					<!-- Card image wrapper. -->
					<div class="mwb_membership_temp_class mwb_membership_plan_card_select-wrapper">

						<!-- Card template one -->
						<div class="mwb_membership_plan_card_select">

							<input type="hidden" class="mwb_membership_card_template" name="mwb_membership_card_selected_template" value="">

							<input type="hidden" class="mwb_membership_card_selected_template" name="mwb_membership_card_selected_template" value="">

							<p class="mwb_membership_card_name"><?php esc_html_e( 'Plantinum', 'membership-for-woocommerce' ); ?></p>
							<a href="javascript:void" class="mwb_membership_card_template_link" data_link = '1' >Platinum</a>
						</div>

						<!-- Card template two -->
						<div class="mwb_membership_plan_card_select">

							<p class="mwb_membership_card_name"><?php esc_html_e( 'Gold', 'membership-for-woocommerce' ); ?></p>
							<a href="javascript:void" class="mwb_membership_card_template_link" data_link = '2' >Gold</a>
						</div>

						<!-- Card template three -->
						<div class="mwb_membership_plan_card_select">

							<p class="mwb_membership_card_name"><?php esc_html_e( 'Silver', 'membership-for-woocommerce' ); ?></p>
							<a href="javascript:void" class="mwb_membership_card_template_link" data_link = '3' >Silver</a>
						</div>

					</div>

				</div>
				<!-- Card template end. -->

				<!-- Card Design start -->
				<div class="mwb_membership_card_table_column_wrapper mwb-membership-appearance-section-hidden">

					<div class="mwb_memberhsip_card_table mwb_membership_card_table--border mwb_membership_card_custom_template_settings ">

						<div class="mwb_membership_offer_sections"><?php esc_html_e( 'Membership Card Box', 'membership-for-woocommerce' ); ?></div>
						<table class="form-table mwb_membership_plan_creation_setting">

							<tbody>
								<!-- Border style start. -->
								<tr valign="top">

									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Border type', 'membership-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
											$attribute_description = esc_html__( 'Select among different border types for Bump Offer.', 'membership-for-woocommerce' );

											mwb_membership_for_woo_tool_tip( $attribute_description );

										?>

										<label>

											<!-- Select options for border. -->
											<select name="parent_border_type" class="mwb_membership_preview_select_border_type" >

												<?php

												$border_type_array = array(
													'none' => esc_html__( 'No Border', 'membership-for-woocommerce' ),
													'solid' => esc_html__( 'Solid', 'membership-for-woocommerce' ),
													'dashed' => esc_html__( 'Dashed', 'membership-for-woocommerce' ),
													'double' => esc_html__( 'Double', 'membership-for-woocommerce' ),
													'dotted' => esc_html__( 'Dotted', 'membership-for-woocommerce' ),

												);

												?>
												<option value="" ><?php esc_html_e( '----Select Border Type----', 'membership-for-woocommerce' ); ?></option>

												<?php
												foreach ( $border_type_array as $value => $name ) {
													?>
													<option <?php echo esc_html( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['parent_border_type'] == $value ? 'selected' : '' ); ?> value="<?php echo esc_html( $value ); ?>" ><?php echo esc_html( $name ); ?></option>
												<?php } ?>
											</select>

										</label>		
									</td>
								</tr>
								<!-- Border style end. -->

								<!-- Border color start. -->
								<tr valign="top">

									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Border Color', 'membership-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
									<?php
										$attribute_description = esc_html__( 'Select border color for Bump Offer.', 'membership-for-woocommerce' );

										mwb_membership_for_woo_tool_tip( $attribute_description );
									?>
										<label>
											<!-- Color picker for description background. -->
											<input type="text" name="parent_border_color" class="mwb_membership_colorpicker mwb_membership_preview_select_border_color" value="<?php echo ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['parent_border_color'] ) ? esc_html( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['parent_border_color'] ) : ''; ?>">
										</label>			
									</td>

								</tr>
								<!-- Border color end. -->

								<!-- Top Vertical Spacing control start. -->
								<tr valign="top">

									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Top Vertical Spacing', 'membership-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
											$attribute_description = esc_html__( 'Add top spacing to the Bump Offer Box.', 'membership-for-woocommerce' );

											mwb_membership_for_woo_tool_tip( $attribute_description );
										?>

										<label>
											<!-- Slider for spacing. -->
											<input type="range" min="0" value="<?php echo esc_html( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['top_vertical_spacing'] ); ?>"  max="40" value="" name='top_vertical_spacing' class="mwb_membership_top_vertical_spacing_slider" />
											<span class="mwb_membership_top_spacing_slider_size" ><?php echo esc_html( ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['top_vertical_spacing'] ) ? esc_html( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['top_vertical_spacing'] . 'px' ) : '0px' ); ?></span>
										</label>
									</td>
								</tr>
								<!-- Top Vertical Spacing control ends. -->

								<!-- Bottom Vertical Spacing control start. -->
								<tr valign="top">
									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Bottom Vertical Spacing', 'membership-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
									<?php
										$attribute_description = esc_html__( 'Add bottom spacing to the Bump Offer Box.', 'membership-for-woocommerce' );

										mwb_membership_for_woo_tool_tip( $attribute_description );
									?>
									<label>	
										<!-- Slider for spacing. -->
										<input type="range" value="<?php echo esc_html( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['bottom_vertical_spacing'] ); ?>" min="0" max="40" value="" name='bottom_vertical_spacing' class="mwb_membership_bottom_vertical_spacing_slider" />
										<span class="mwb_membership_bottom_spacing_slider_size"><?php echo esc_html( ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['bottom_vertical_spacing'] ) ? esc_html( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['bottom_vertical_spacing'] . 'px' ) : '0px' ); ?></span>
										</label>		
									</td>
								</tr>
								<!-- Bottom Vertical Spacing control ends. -->


							</tbody>

						</table>

					</div>

					<!-- Membership Price. -->
					<div class="mwb_memberhsip_card_table mwb_membership_card_table--border mwb_membership_card_custom_template_settings ">

						<div class="mwb_membership_offer_sections"><?php esc_html_e( 'Membership Plan Price Section', 'membership-for-woocommerce' ); ?></div>
						<table class="form-table mwb_membership_plan_creation_setting">
							<tbody>

								<!-- Background color start. -->
								<tr valign="top">
									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Background Color', 'membership-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
									<?php
										$attribute_description = esc_html__( 'Select background color for Membership Plan Price.', 'membership-for-woocommerce' );

										mwb_membership_for_woo_tool_tip( $attribute_description );
									?>
										<label>
											<!-- Color picker for description background. -->
											<input type="text" name="price_section_background_color" class="membership_colorpicker mwb_membership_select_price_bcolor" value="<?php echo ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['price_section_background_color'] ) ? esc_html( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['price_section_background_color'] ) : ''; ?>">

										</label>	
									</td>
								</tr>
								<!-- Background color end. -->

								<!-- Text color start. -->
								<tr valign="top">
									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Text Color', 'membership-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
											$attribute_description = esc_html__( 'Select text color for Membershi Plan Price.', 'membership-for-woocommerce' );

											mwb_membership_for_woo_tool_tip( $attribute_description );
										?>
										<label>
											<!-- Color picker for description text. -->
											<input type="text" name="price_section_text_color" class="mwb_membership_colorpicker mwb_membership_select_price_tcolor" value="<?php echo ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['price_section_text_color'] ) ? esc_html( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['price_section_text_color'] ) : ''; ?>">
										</label>			
									</td>

								</tr>
								<!-- Text color end. -->

								<!-- Text size control start. -->
								<tr valign="top">
									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Text Size', 'membership-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
											$attribute_description = esc_html__( 'Select font size for Discount section.', 'membership-for-woocommerce' );

											mwb_membership_for_woo_tool_tip( $attribute_description );
										?>
										<label>
											<!-- Slider for spacing. -->
											<input type="range" min="20" value="<?php echo esc_html( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['price_section_text_size'] ); ?>"  max="50" value="" name = 'price_section_text_size' class="mwb_membership_text_slider mwb_ubo_price_slider" />

											<span class="mwb_membership_slider_size mwb_ubo_price_slider_size" ><?php echo esc_html( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['price_section_text_size'] . 'px' ); ?></span>
										</label>		
									</td>
								</tr>
								<!-- Text size control ends. -->
							</tbody>

						</table>
					</div>

					<!-- Membership Plan buy now section -->
					<div class="mwb_memberhsip_card_table mwb_membership_card_table--border mwb_membership_card_custom_template_settings ">

						<div class="mwb_membership_offer_sections"><?php esc_html_e( 'Membership Plan Buy Now Button', 'membership-for-woocommerce' ); ?></div>

						<table class="form-table mwb_membership_plan_creation_setting">
							<tbody>
								<!-- Background color start. -->
								<tr valign="top">
									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Background Color', 'membership-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
											$attribute_description = esc_html__( 'Select background color for Buy Now button.', 'membership-for-woocommerce' );

											mwb_membership_for_woo_tool_tip( $attribute_description );
										?>
										<label>
											<!-- Color picker for description background. -->
											<input type="text" name="button_section_background_color" class="mwb_membership_colorpicker mwb_membership_select_buy_now_bcolor" value="<?php echo ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['button_section_background_color'] ) ? esc_html( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['button_section_background_color'] ) : ''; ?>">
										</label>			
									</td>
								</tr>
								<!-- Background color end. -->

								<!-- Text color start. -->
								<tr valign="top">
									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Text Color', 'membership-for-woocommerce' ); ?></label>
									</th>

									<td class="forminp forminp-text">
										<?php
											$attribute_description = esc_html__( 'Select text color for Buy Now button.', 'membership-for-woocommerce' );

											mwb_membership_for_woo_tool_tip( $attribute_description );
										?>
										<label>	
											<!-- Color picker for description text. -->
											<input type="text" name="button_section_text_color" class="mwb_membership_colorpicker mwb_membership_select_buy_now_tcolor" value="<?php echo ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['button_section_text_color'] ) ? esc_html( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['button_section_text_color'] ) : ''; ?>">
										</label>			
									</td>
								</tr>
								<!-- Text color end. -->

								<!-- Text size control start. -->
								<tr valign="top">
									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Text Size', 'membership-for-woocommerce' ); ?></label>
									</th>
									<td class="forminp forminp-text">
										<?php
											$attribute_description = esc_html__( 'Select font size for Buy Now button.', 'membership-for-woocommerce' );

											mwb_membership_for_woo_tool_tip( $attribute_description );
										?>
										<label>
											<!-- Slider for spacing. -->
											<input type="range" min="10" value="<?php echo esc_html( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['button_section_text_size'] ); ?>"  max="30" value="" name = 'button_section_text_size' class="mwb_membership_text_slider mwb_membership_buy_now_slider" />
											<span class="mwb_membership_slider_size mwb_membership_buy_now_slider_size" ><?php echo esc_html( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['button_section_text_size'] ) . 'px'; ?></span>
										</label>	
									</td>
								</tr>
								<!-- Text size control ends. -->
							</tbody>

						</table>

					</div>

					<!-- Membership Plan Description -->
					<div class="mwb_memberhsip_card_table mwb_membership_card_table--border mwb_membership_card_custom_template_settings ">

						<div class="mwb_membership_offer_sections"><?php esc_html_e( 'Membership Plan Description Section', 'membership-for-woocommerce' ); ?></div>
						<table class="form-table mwb_membership_plan_creation_setting">

							<tbody>
								<!-- Text color start. -->
								<tr valign="top">
									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Text Color', 'membership-for-woocommerce' ); ?></label>
									</th>
									<td class="forminp forminp-text">
										<?php
											$attribute_description = esc_html__( 'Select text color for Membership Plan Description section.', 'membership-for-woocommerce' );

											mwb_membership_for_woo_tool_tip( $attribute_description );
										?>
										<!-- Color picker for description text. -->
										<input type="text" name="description_section_text_color" class="mwb_membership_colorpicker mwb_membership_select_membership_description_tcolor" value="<?php echo ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['description_section_text_color'] ) ? esc_html( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['description_section_text_color'] ) : ''; ?>">
									</td>
								</tr>
								<!-- Text color end. -->

								<!-- Text size control start -->
								<tr valign="top">
									<th scope="row" class="titledesc">
										<label><?php esc_html_e( 'Select Text Size', 'membership-for-woocommerce' ); ?></label>
									</th>
									<td class="forminp forminp-text">
										<?php
											$attribute_description = esc_html__( 'Select font size for Membership Plan Description section.', 'membership-for-woocommerce' );

											mwb_membership_for_woo_tool_tip( $attribute_description );
										?>
										<!-- Slider for spacing. -->
										<input type="range" min="10" value="<?php echo esc_html( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['description_section_text_size'] ); ?>"  max="30" value="" name = 'description_section_text_size' class="mwb_membership_text_slider mwb_membership_description_slider" />

										<span class="mwb_membership_slider_size mwb_membership_description_slider_size" ><?php echo esc_html( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_css']['description_section_text_size'] ) . 'px'; ?></span>
									</td>
								</tr>
								<!-- Text size control ends. -->
							</tbody>

						</table>
					</div>

				</div>
				<!-- Card Design End. -->

				<!-- Text Section start -->
				<div class="mwb-membership-text-section mwb_membership_card_table--border mwb-membership-appearance-section-hidden mwb_membership_card_table">
					<table>
						<tbody>
							<!-- Plan Description start. -->
							<tr valign="top">
								<th scope="row" class="titledesc">
									<label><?php esc_html_e( 'Membership Plan Description', 'membership-for-woocommerce' ); ?></label>
								</th>

								<td class="forminp forminp-text" >

									<?php
										$attribute_description = esc_html__( 'Membership Plan description content.', 'membership-for-woocommerce' );

										mwb_membership_for_woo_tool_tip( $attribute_description );

									?>

									<textarea class="mwb_textarea_class" text_id ="plan_desc" rows="4" cols="50" name="mwb_membership_plan_decsription_text" ><?php echo ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_text']['mwb_membership_plan_decsription_text'] ) ? esc_html( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_text']['mwb_membership_plan_decsription_text'] ) : ''; ?></textarea>

								</td>
							</tr>
							<!-- Plan Description end. -->

							<!-- Lead Title start. -->
							<tr valign="top">
								<th scope="row" class="titledesc">
									<label><?php esc_html_e( 'Lead Title ', 'membership-for-woocommerce' ); ?></label>
								</th>

								<td class="forminp forminp-text">
									<?php
										$attribute_description = esc_html__( 'Bump offer Lead title content.', 'membership-for-woocommerce' );

										mwb_membership_for_woo_tool_tip( $attribute_description );
									?>

									<input type="text" class="mwb_membership_plan_input_type" name="mwb_membership_plan_title" text_id ="lead" value ="<?php echo ! empty( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_text']['mwb_membership_plan_title'] ) ? esc_html( $mwb_membership_plans_list[ $mwb_membership_plan_id ]['design_text']['mwb_membership_plan_title'] ) : ''; ?>">

								</td>
							</tr>
							<!--Lead Title ends.-->
						</tbody>
					</table>

				</div>
				<!-- Text section end. -->

			</div>

		</div>

		<!-- Save Changes for whole membership plan -->
		<p class="submit">
			<input type="submit" value="<?php esc_html_e( 'Save Changes', 'membership-for-woocommerce' ); ?>" class="button-primary woocommerce-save-button" name="mwb_membership_plan_creation_setting_save" id="mwb_membership_plan_creation_setting_save" >
		</p>

	</div>

</form>
