<?php
/**
 * Provide a public area view for the plugin
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

// Creating Instance of the WC_Countries class.
$country_class = new WC_Countries();

// Getting user id.
$user_id = get_current_user_id();

// Getting usermeta as per user id.
$first_name = ! empty( get_user_meta( $user_id, 'billing_first_name', true ) ) ? get_user_meta( $user_id, 'billing_first_name', true ) : '';
$last_name  = ! empty( get_user_meta( $user_id, 'billing_last_name', true ) ) ? get_user_meta( $user_id, 'billing_last_name', true ) : '';
$company    = ! empty( get_user_meta( $user_id, 'billing_company', true ) ) ? get_user_meta( $user_id, 'billing_company', true ) : '';
$address_1  = ! empty( get_user_meta( $user_id, 'billing_address_1', true ) ) ? get_user_meta( $user_id, 'billing_address_1', true ) : '';
$address_2  = ! empty( get_user_meta( $user_id, 'billing_address_2', true ) ) ? get_user_meta( $user_id, 'billing_address_2', true ) : '';
$city       = ! empty( get_user_meta( $user_id, 'billing_city', true ) ) ? get_user_meta( $user_id, 'billing_city', true ) : '';
$post_code  = ! empty( get_user_meta( $user_id, 'billing_postcode', true ) ) ? get_user_meta( $user_id, 'billing_postcode', true ) : '';
$country    = ! empty( get_user_meta( $user_id, 'billing_country', true ) ) ? get_user_meta( $user_id, 'billing_country', true ) : '';
$state      = ! empty( get_user_meta( $user_id, 'billing_state', true ) ) ? get_user_meta( $user_id, 'billing_state', true ) : '';
$phone      = ! empty( get_user_meta( $user_id, 'billing_phone', true ) ) ? get_user_meta( $user_id, 'billing_phone', true ) : '';
$email      = ! empty( get_user_meta( $user_id, 'billing_email', true ) ) ? get_user_meta( $user_id, 'billing_email', true ) : '';

?>
<!-- progress bar for multi-level form -->
<div class="wps_mfw_progress-bar-wrapper">
	<ul class="wps_mfw_progress-bar">
		<li class="wps_mfw_progress-bar-step" id="wps_mfw_progress-bar-a">
			<i class="fas fa-check-circle wps_mfw_progress-bar_done"></i>
		</li>
		<li class="wps_mfw_progress-bar-step" id="wps_mfw_progress-bar-b">
			<div class="wps_mfw_progress-line"><i class="fas fa-check-circle wps_mfw_progress-bar_done"></i></div>
		</li>
		<li class="wps_mfw_progress-bar-step" id="wps_mfw_progress-bar-c">
			<div class="wps_mfw_progress-line"><i class="fas fa-check-circle wps_mfw_progress-bar_done"></i></div>
		</li>
	</ul>
</div>

<div class="wps_mfw_multi-level-form">
	<div class="wps_mfw_billing-heading">
		<h3><?php esc_html_e( 'Billing details', 'membership-for-woocommerce' ); ?></h3>
	</div>

	<div class="membership_customer_details" id="membership_customer_details" >

		<div class="membership_billing_fields" >

			<div class="membership_billing_fields_wrapper">

				<!-- personal details -->
				<div class="wps_mfw_form-field-wrapper-part-a">

					<input type="hidden" name="plan_id" id="membership_plan_id" value="<?php echo esc_html( $plan_id ); ?>" >
					<div class="wps_mfw_complete-name">
						<p class="form-row validate-required wps_mfw_form_field-wrapper" id="wps_billing_first_name_field">
							<label for="membership_billing_first_name"><?php esc_html_e( 'First name&nbsp;', 'membership-for-woocommerce' ); ?><abbr class="required" title="required"><?php esc_html_e( '*', 'membership-for-woocommerce' ); ?></abbr></label>
							<span class="membership-input-wrapper">
								<input type="text" class="input-text wps_mfw_form_field" name="membership_billing_first_name" id="membership_billing_first_name" value="<?php echo esc_html( $first_name ); ?>" placeholder="<?php esc_html_e( 'First name&hellip;', 'membership-for-woocommerce' ); ?>" required>
							</span>
						</p>
						<p class="form-row validate-required wps_mfw_form_field-wrapper" id="wps_billing_last_name_field">
							<label for="membership_billing_last_name"><?php esc_html_e( 'Last name&nbsp;', 'membership-for-woocommerce' ); ?><abbr class="required" title="required"><?php esc_html_e( '*', 'membership-for-woocommerce' ); ?></abbr></label>
							<span class="membership-input-wrapper">
								<input type="text" class="input-text wps_mfw_form_field" name="membership_billing_last_name" id="membership_billing_last_name" value="<?php echo esc_html( $last_name ); ?>" placeholder="<?php esc_html_e( 'Last name&hellip;', 'membership-for-woocommerce' ); ?>" required>
							</span>
						</p>
					</div>
					<p class="form-row wps_mfw_form_field-wrapper" id="wps_billing_company_field">
						<label for="membership_billing_company"><?php esc_html_e( 'Company name&nbsp;', 'membership-for-woocommerce' ); ?><span class="optional"><?php esc_html_e( '(Optional)', 'membership-for-woocommerce' ); ?></span></label>
						<span class="membership-input-wrapper">
							<input type="text" class="input-text wps_mfw_form_field" name="membership_billing_company" id="membership_billing_company" value="<?php echo esc_html( $company ); ?>" placeholder="<?php esc_html_e( 'Company name', 'membership-for-woocommerce' ); ?>">
						</span>
					</p>
					<p class="form-row validate-required wps_mfw_form_field-wrapper" id="wps_billing_phone_field">
						<label for="membership_billing_phone"><?php esc_html_e( 'Phone&nbsp;', 'membership-for-woocommerce' ); ?><abbr class="required" title="required"><?php esc_html_e( '*', 'membership-for-woocommerce' ); ?></abbr></label>
						<span class="membership-input-wrapper">
							<input type="tel" class="input-text wps_mfw_form_field" name="membership_billing_phone" id="membership_billing_phone" value="<?php echo esc_html( $phone ); ?>" placeholder="<?php esc_html_e( 'Phone no.', 'membership-for-woocommerce' ); ?>" required>
						</span>
					</p>
					<div class="form-row validate-required wps_mfw_form_field-wrapper" id="wps_billing_email_field">
						<label for="membership_billing_email"><?php esc_html_e( 'E-mail&nbsp;', 'membership-for-woocommerce' ); ?><abbr class="required" title="required"><?php esc_html_e( '*', 'membership-for-woocommerce' ); ?></abbr></label>
						<span class="membership-input-wrapper">
							<input type="email" class="input-text wps_mfw_form_field" name="membership_billing_email" id="membership_billing_email" value="<?php echo esc_html( $email ); ?>" placeholder="<?php esc_html_e( 'Email', 'membership-for-woocommerce' ); ?>" required>
						</span>
						<div class="tooltip">
								<span class="tooltiptext"><?php esc_html_e( 'Please enter a valid email.', 'membership-for-woocommerce' ); ?></span>
						</div>
					</div>
				</div>
				<!-- address details -->
				<div class="wps_mfw_form-field-wrapper-part-b">
					<p class="form-row validate-required wps_mfw_form_field-wrapper" id="wps_billing_address_1_field">
						<label for="membership_billing_address_1"><?php esc_html_e( 'Street address&nbsp;', 'membership-for-woocommerce' ); ?><abbr class="required" title="required"><?php esc_html_e( '*', 'membership-for-woocommerce' ); ?></abbr></label>
						<span class="membership-input-wrapper">
							<input type="text" class="input-text wps_mfw_form_field" name="membership_billing_address_1" id="membership_billing_address_1" value="<?php echo esc_html( $address_1 ); ?>" placeholder="<?php esc_html_e( 'House number and street name', 'membership-for-woocommerce' ); ?>" required>
						</span>
					</p>
					<p class="form-row wps_mfw_form_field-wrapper" id="wps_billing_address_2_field">	
						<span class="membership-input-wrapper">
							<input type="text" class="input-text wps_mfw_form_field" name="membership_billing_address_2" id="membership_billing_address_2" value="<?php echo esc_html( $address_2 ); ?>" placeholder="<?php esc_html_e( 'Apartment, suit, unit, etc. (Optional)', 'membership-for-woocommerce' ); ?>">
						</span>
					</p>
					<p class="form-row validate-required wps_mfw_form_field-wrapper" id="wps_billing_city_field">
						<label for="membership_billing_city"><?php esc_html_e( 'Town/City&nbsp;', 'membership-for-woocommerce' ); ?><abbr class="required" title="required"><?php esc_html_e( '*', 'membership-for-woocommerce' ); ?></abbr></label>
						<span class="membership-input-wrapper">
							<input type="text" class="input-text wps_mfw_form_field" name="membership_billing_city" id="membership_billing_city" value="<?php echo esc_html( $city ); ?>" placeholder="<?php esc_html_e( 'Town/City', 'membership-for-woocommerce' ); ?>" required>
						</span>
					</p>
					<p class="form-row wps_mfw_form_field-wrapper" id="wps_billing_country_field">
						<label for="membership_billing_country"><?php esc_html_e( 'Country/Region&nbsp;', 'membership-for-woocommerce' ); ?><abbr class="required" title="required"><?php esc_html_e( '*', 'membership-for-woocommerce' ); ?></abbr></label>
						<span class="membership-input-wrapper">
							<select name="membership_billing_country" id="membership_billing_country" class="wc-enhanced-select wps_country_select wps_mfw_form_field" required>
								<option value=""><?php esc_html_e( 'Select a Country', 'membership-for-woocommerce' ); ?></option>
								<?php
								foreach ( $country_class->__get( 'countries' ) as $code => $name ) {
									?>
									<option <?php echo esc_html( $code == $country ? 'selected' : '' ); ?>  value="<?php echo esc_html( $code ); ?>"><?php echo esc_html( $name ); ?></option>
								<?php } ?>
							</select>
						</span>
					</p>
					<p class="form-row validate-required wps_mfw_form_field-wrapper" id="wps_billing_state_field">
						<label for="membership_billing_state"><?php esc_html_e( 'State&nbsp;', 'membership-for-woocommerce' ); ?><abbr class="required" title="required"><?php esc_html_e( '*', 'membership-for-woocommerce' ); ?></abbr></label>
						<span class="membership-input-wrapper">
							<select name="membership_billing_state" id="membership_billing_state" class="wps_state_select wps_mfw_form_field">
								<option value=""><?php esc_html_e( 'Select a state', 'membership-for-woocommerce' ); ?></option>
							</select>
						</span>
					</p>
					<p class="form-row validate-required wps_mfw_form_field-wrapper" id="wps_billing_postcode_field">
						<label for="membership_billing_postcode"><?php esc_html_e( 'Pin code&nbsp;', 'membership-for-woocommerce' ); ?><abbr class="required" title="required"><?php esc_html_e( '*', 'membership-for-woocommerce' ); ?></abbr></label>
						<span class="membership-input-wrapper">
							<input type="text" class="input-text wps_mfw_form_field" name="membership_billing_postcode" id="membership_billing_postcode" value="<?php echo esc_html( $post_code ); ?>" placeholder="<?php esc_html_e( 'Pin code', 'membership-for-woocommerce' ); ?>" required>
						</span>
					</p>
				</div>
			</div>
		</div>
	</div>

	<!-- additional HTML part for multilevel form-->
	<div class="wps_mfw_pre-next-btn">
		<div class="wps_mfw_previous">
			<button type="button" class="wps_mfw_btn-back-a wps_mfw_button-main"><?php esc_html_e( 'Previous Step', 'membership-for-woocommerce' ); ?></button>
			<button type="button" class="wps_mfw_btn-back-b wps_mfw_button-main"><?php esc_html_e( 'Previous Step', 'membership-for-woocommerce' ); ?></button>
		</div>
		<div class="wps_mfw_next">
			<button type="button" class="wps_mfw_btn-next-a wps_mfw_button-main"><?php esc_html_e( 'Next Step', 'membership-for-woocommerce' ); ?></button>
			<button type="button" class="wps_mfw_btn-next-b wps_mfw_button-main"><?php esc_html_e( 'Next Step', 'membership-for-woocommerce' ); ?></button>
			<!-- proceed to payment button -->
			<p class="form-row" id="wps_proceed_payment">
				<span class="membership-input-wrapper">
					<input type="submit" class="button alt" name="membership_proceed_payment" id="membership_proceed_payment" value="<?php esc_html_e( 'Proceed for Payment', 'membership-for-woocommerce' ); ?>">
				</span>
			</p>	
		</div>
	</div>

	<div class="wps_mfw_order-confirm"><span><?php esc_html_e( 'Your order is confirmed.', 'membership-for-woocommerce' ); ?></span>
		<button type="button" class="wps_mfw_purchase-again wps_mfw_button-main"><?php esc_html_e( 'Shop more', 'membership-for-woocommerce' ); ?></button>
	</div>
</div>
