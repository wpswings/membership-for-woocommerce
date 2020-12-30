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

?>
<!-- Schedule metabox start -->
<div class="members-metabox_wrapper">

	<div class="member_billing_schedule">
		<div class="member_billing_schedule_edit">

			<p class="form-field billing_interval_field">
				<label for="billing_interval_field"><strong><?php esc_html_e( 'Payment', 'membership-for-woocommerce' ); ?></strong></label>
				<select name="billing_interval_field" id="billing_interval_field">
					<option value="1" ><?php esc_html_e( 'every', 'membership-for-woocommerce' ); ?></option>
				</select>
			</p>

			<p class="form-field billing_period_field">
				<select name="billing_period_field" id="billing_period_field">
					<option value="day"><?php esc_html_e( 'Day', 'membership-for-woocommerce' ); ?></option>
					<option value="week"><?php esc_html_e( 'Week', 'membership-for-woocommerce' ); ?></option>
					<option value="month"><?php esc_html_e( 'Month', 'membership-for-woocommerce' ); ?></option>
					<option value="year"><?php esc_html_e( 'Year', 'membership-for-woocommerce' ); ?></option>
				</select>
			</p>

		</div>
	</div>

	<div id="members_start_date" class="date-fields">
		<strong><?php esc_html_e( 'Start date', 'membership-for-woocommerce' ); ?></strong>
		<input type="hidden" class="start_timestamp_utc" name="start_timestamp_utc" id="start_timestamp_utc" value=""> 
		<?php echo esc_html( get_the_date( 'F j, Y', $post ) ); ?>
	</div>

	<div id="members_next_payment" class="date-fields">
		<strong><?php esc_html_e( 'Next Payment', 'membership-for-woocommerce' ); ?></strong>
		<input type="hidden" class="nxt-payment_timestamp_utc" name="nxt-payment_timestamp_utc" id="nxt-payment_timestamp_utc" value="">
		<div class="members-date-field">
			<input type="date" class="nxt_payment_date" name="nxt_payment_date" id="nxt_payment_date" placeholder="YYYY-MM-DD" maxlength="10" value="">&#64;
			<input type="text" class="hour" name="nxt_payment_hour" id="nxt_payment_hour" placeholder="HH" maxlength="2" size="2" value="" >&#58;
			<input type="text" class="minute" name="nxt_payment_minute" id="nxt_payment_minute" placeholder="MM" maxlength="2" size="2" value="">
		</div>
	</div>

	<div id="members_end_date" class="date-fields">
		<strong><?php esc_html_e( 'End Date', 'membership-for-woocommerce' ); ?></strong>
		<input type="hidden" class="end-date_timestamp_utc" name="end-date_timestamp_utc" id="end-date_timestamp_utc" value="">
		<div class="members-date-field">
			<input type="date" class="members_end_date" name="members_end_date" id="members_end_date" placeholder="YYYY-MM-DD" maxlength="10" value="">&#64;
			<input type="text" class="hour" name="end_date_hour" id="end_date_hour" placeholder="HH" maxlength="2" size="2" value="" >&#58;
			<input type="text" class="minute" name="end_date_minute" id="end_date_minute" placeholder="MM" maxlength="2" size="2" value="">
		</div>
	</div>

	<p>
		<?php esc_html_e( 'Timezone :', 'membership-for-woocommerce' ); ?>
		<span id="members-timezone">
			<?php echo esc_html( wp_timezone_string() ); ?>
		</span>
	</p>

</div>
<!-- schedule metabox end -->
