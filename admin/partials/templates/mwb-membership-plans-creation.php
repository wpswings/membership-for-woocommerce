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

				<input type="hidden" name="mwb_membership_plan_id" value="">

				<!-- Membership Plans Header start -->
				<div id="mwb_membership_plan_name_heading">
					<h2><?php echo esc_html( 'Plan Name' ); ?></h2>
					<div id="mwb_membership_plan_status" >
						<label>
							<input type="checkbox" id="mwb_membership_plan_status_input" name="mwb_membership_plan_status" value="" >
							<span class="mwb_membership_plan_span"></span>
						</label>

						<span class="mwb_membership_plan_status_on "><?php esc_html_e( 'Live', 'membership-for-woocommerce' ); ?></span>
						<span class="mwb_membership_plan_status_off "><?php esc_html_e( 'Sandbox', 'membership-for-woocommerce' ); ?></span>
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
						echo $description;

						?>

						<input type="text" id="mwb_membership_plan_name" name="mwb_membership_plan_name" value="" class="input-text mwb_membership_plan_commone_class" required="" maxlength="30">
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
						echo $description;

						?>

						<select id="mwb_membership_plan_access_type" name="mwb_membership_plan_name_access_type" value="" class="input-text mwb_membership_plan_commone_class" required="">
							<option value="lifetime"><?php esc_html_e( 'Lifetime', 'membership-for-woocommerce' ); ?></option>
							<option value="limited"><?php esc_html_e( 'Limited', 'membership-for-woocommerce' ); ?></option>
							<option value="date_ranged"><?php esc_html_e( 'Date Ranged', 'membership-for-woocommerce' ); ?></option>
						</select>
					</td>
				</tr>
				<!-- Access Type End -->

				<!-- Plan Duration start. -->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="mwb_membership_plan_duration"><?php esc_html_e( 'Duration', 'membership-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php

						$description = esc_html__( 'Provide the number of days the plan will be active', 'membership-for-woocommerce' );
						echo $description;

						?>

						<input type="number" id="mwb_membership_plan_duration" name="mwb_membership_plan_duration" value="" min="1" max="31">
						<select name="mwb_membership_plan_duration_type" id="mwb_membership_plan_duration_type">
							<option value="days"><?php esc_html_e( 'Days', 'membership-for-woocommerce' ); ?></option>
							<option value="weeks"><?php esc_html_e( 'Weeks', 'membership-for-woocommerce' ); ?></option>
							<option value="months"><?php esc_html_e( 'Months', 'membership-for-woocommerce' ); ?></option>
							<option value="years"><?php esc_html_e( 'Years', 'membership-for-woocommerce' ); ?></option>
						</select>
					</td>
				</tr>
				<!-- Plan Duration End. -->

				<!-- Plan Date Range start -->
				<tr valign="top">

					<th scope="row" class="titledesc">
						<label for="mwb_membership_plan_start_date"><?php esc_html_e( 'Start Date', 'membership-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php

						$description = esc_html__( 'Provide the Start date of the plan.', 'membership-for-woocommerce' );
						echo $description;

						?>

						<input type="text" id="mwb_membership_plan_start" name="mwb_membership_plan_start" value="" class="hasDatepicker">
					</td>
				</tr>
				<tr>
					<th scope="row" class="titledesc">
						<label for="mwb_membership_plan_end_date"><?php esc_html_e( 'End Date', 'membership-for-woocommerce' ); ?></label>
					</th>

					<td class="forminp forminp-text">

						<?php

						$description = esc_html__( 'Provide the End date of the plan.', 'membership-for-woocommerce' );
						echo $description;

						?>

						<input type="text" id="mwb_membership_plan_end" name="mwb_membership_plan_end" value="" class="hasDatepicker">
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
						echo $description;

						?>

						<input type="checkbox" id="mwb_membership_plan_user_access" name="mwb_membership_plan_user_access" value="">
					</td>
				</tr>
				<!-- Show history to user end -->

			</tbody>
		</table>
	</div>

</form>