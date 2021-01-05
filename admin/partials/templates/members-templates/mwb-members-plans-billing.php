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

// Getting member's billing details.
$member_details = get_post_meta( $post->ID, 'billing_details', true );

$first_name = ! empty( $member_details['membership_billing_first_name'] ) ? $member_details['membership_billing_first_name'] : '';
$last_name  = ! empty( $member_details['membership_billing_last_name'] ) ? $member_details['membership_billing_last_name'] : '';
$company    = ! empty( $member_details['membership_billing_company'] ) ? $member_details['membership_billing_company'] : '';
$address_1  = ! empty( $member_details['membership_billing_address_1'] ) ? $member_details['membership_billing_address_1'] : '';
$address_2  = ! empty( $member_details['membership_billing_address_2'] ) ? $member_details['membership_billing_address_2'] : '';
$city       = ! empty( $member_details['membership_billing_city'] ) ? $member_details['membership_billing_city'] : '';
$postcode   = ! empty( $member_details['membership_billing_postcode'] ) ? $member_details['membership_billing_postcode'] : '';
$state      = ! empty( $member_details['membership_billing_state'] ) ? $member_details['membership_billing_state'] : '';
$country    = ! empty( $member_details['membership_billing_country'] ) ? $member_details['membership_billing_country'] : '';
$email      = ! empty( $member_details['membership_billing_email'] ) ? $member_details['membership_billing_email'] : '';
$phone      = ! empty( $member_details['membership_billing_phone'] ) ? $member_details['membership_billing_phone'] : '';
$payment    = ! empty( $member_details['payment_method'] ) ? $member_details['payment_method'] : '';

// Getting all user ID's.
$all_users = get_users(
	array(
		'fields' => array(
			'ID',
		),
	)
);

$wc_gateways      = new WC_Payment_Gateways();
$payment_gateways = $wc_gateways->get_available_payment_gateways();

$supported_gateways = $this->global_class->supported_gateways();


// Creating Instance of the WC_Countries class.
$country_class = new WC_Countries();
?>

<!-- Members billing metabox start -->
<div class="members_billing_details">

	<h1><?php echo sprintf( 'Member #%u details', esc_html( $post->ID ) ); ?></h1>

	<div class="members_data_column_container">

		<div class="members_data_column">
			<h3><?php esc_html_e( 'General', 'membership-for-woocommerce' ); ?></h3>

			<p class="form-field membership-customer">
`				<label for="member-user">
					<?php esc_html_e( 'Customer:', 'membership-for-woocommerce' ); ?>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_status=all&post_type=mwb_cpt_members&post_author=' . $post->post_author ) ); ?>" target="_blank"><?php esc_html_e( 'View other memberships', 'membership-for-woocommerce' ); ?></a>
					<a href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $post->post_author ) ); ?>" target="_blank"><?php esc_html_e( 'Profile', 'membership-for-woocommerce' ); ?></a>
				</label><br>
				<select name="mwb_member_user" id="mwb_member_user">
					<?php
					if ( ! empty( $all_users ) && is_array( $all_users ) ) {

						foreach ( $all_users as $users ) {
							$user_info = get_user_by( 'ID', $users->ID );
							?>
							<option <?php echo esc_html( $users->ID == $post->post_author ? 'selected' : '' ); ?> value="<?php echo esc_html( $users->ID ); ?>"><?php echo esc_html( $user_info->user_login ) . '(#' . esc_html( $users->ID ) . ')'; ?></option>
							<?php
						}
					}
					?>
				</select>
			</p>
		</div>

		<div class="members_data_column" >
			<h3><?php esc_html_e( 'Billing', 'membership-for-woocommerce' ); ?></h3>
			<a href="#" class="edit_member_address"><span class="dashicons dashicons-edit"></span></a>
			<a href="#" class="cancel_member_edit" style="display: none;"><span class="dashicons dashicons-no-alt"></span></a>

			<div class="member_address">
				<p>
					<strong><?php esc_html_e( 'Address :', 'membership-for-woocommerce' ); ?></strong></br>
					<?php echo sprintf( ' %s %s ', esc_html( $first_name ), esc_html( $last_name ) ); ?></br>
					<?php echo esc_html( $company ); ?></br>
					<?php echo sprintf( ' %s %s ', esc_html( $address_1 ), esc_html( $address_2 ) ); ?></br>
					<?php echo sprintf( ' %s %s ', esc_html( $city ), esc_html( $postcode ) ); ?></br>
					<?php echo sprintf( ' %s, %s ', esc_html( $state ), esc_html( $country ) ); ?>
				</p>

				<p>
					<strong><?php esc_html_e( 'Email address :', 'membership-for-woocommerce' ); ?></strong></br>
					<a href="mailto:<?php echo esc_html( $email ); ?>"><?php echo esc_html( $email ); ?></a>
				</p>

				<p>
					<strong><?php esc_html_e( 'Phone :', 'membership-for-woocommerce' ); ?></strong></br>
					<?php echo esc_html( $phone ); ?>
				</p>

				<p>
					<strong><?php esc_html_e( 'Payment Method', 'membership-for-woocommerce' ); ?></strong></br>
					<?php echo esc_html( $payment ); ?>
				</p>

			</div>

			<div class="member_edit_address" style="display: none;">

				<p class="form-field billing_first_name_field">
					<label for="billing_first_name"><?php esc_html_e( 'First name', 'membership-for-woocommerce' ); ?></label>
					<input type="text" class="short" name="billing_first_name" id="billing_first_name" value="<?php echo esc_html( $first_name ); ?>" >
				</p>

				<p class="form-field billing_last_name_field">
					<label for="billing_last_name"><?php esc_html_e( 'Last name', 'membership-for-woocommerce' ); ?></label>
					<input type="text" class="short" name="billing_last_name" id="billing_last_name" value="<?php echo esc_html( $last_name ); ?>" >
				</p>

				<p class="form-field billing_company_field">
					<label for="billing_company"><?php esc_html_e( 'Company', 'membership-for-woocommerce' ); ?></label>
					<input type="text" class="short" name="billing_company" id="billing_company" value="<?php echo esc_html( $company ); ?>" >
				</p>

				<p class="form-field billing_address_1_field">
					<label for="billing_address_1"><?php esc_html_e( 'Address line 1', 'membership-for-woocommerce' ); ?></label>
					<input type="text" class="short" name="billing_address_1" id="billing_address_1" value="<?php echo esc_html( $address_1 ); ?>" >
				</p>

				<p class="form-field billing_address_2_field">
					<label for="billing_address_2"><?php esc_html_e( 'Address line 2', 'membership-for-woocommerce' ); ?></label>
					<input type="text" class="short" name="billing_address_2" id="billing_address_2" value="<?php echo esc_html( $address_2 ); ?>" >
				</p>

				<p class="form-field billing_city_field">
					<label for="billing_city"><?php esc_html_e( 'City', 'membership-for-woocommerce' ); ?></label>
					<input type="text" class="short" name="billing_city" id="billing_city" value="<?php echo esc_html( $city ); ?>" >
				</p>

				<p class="form-field billing_postcode_field">
					<label for="billing_postcode"><?php esc_html_e( 'Postcode/ZIP', 'membership-for-woocommerce' ); ?></label>
					<input type="text" class="short" name="billing_postcode" id="billing_postcode" value="<?php echo esc_html( $postcode ); ?>">
				</p>

				<p class="form-field billing_country_field">
					<label for="billing_country"><?php esc_html_e( 'Country', 'membership-for-woocommerce' ); ?></label>
					<select name="billing_country" id="billing_country" class="billing_country">
						<?php
						foreach ( $country_class->__get( 'countries' ) as $code => $name ) {
							?>
							<option <?php echo esc_html( $code == $country ? 'selected' : '' ); ?>  value="<?php echo esc_html( $code ); ?>"><?php echo esc_html( $name ); ?></option>
						<?php } ?>
					</select>
				</p>

				<p class="form-field billing_state_field">
					<label for="billing_state"><?php esc_html_e( 'State/County', 'membership-for-woocommerce' ); ?></label>
					<select name="billing_state" id="billing_state">

					</select>
				</p>

				<p class="form-field billing_email_field">
					<label for="billing_email"><?php esc_html_e( 'Email address', 'membership-for-woocommerce' ); ?></label>
					<input type="text" class="short" name="billing_email" id="billing_email" value="<?php echo esc_html( $email ); ?>">
				</p>

				<p class="form-field billing_phone_field">
					<label for="billing_phone"><?php esc_html_e( 'Phone', 'membership-for-woocommerce' ); ?></label>
					<input type="text" name="billing_phone" id="billing_phone" value="<?php echo esc_html( $phone ); ?>">
				</p>

				<p class="form-field payment_method_field">
					<strong><?php esc_html_e( 'Payment method', 'membership-for-woocommerce' ); ?></strong></br>
					<?php if ( ! empty( $payment ) ) { ?>
						<input type="hidden" name="billing_payment" id="billing_payment" value="<?php echo esc_html( $payment ); ?>" >
						<?php echo esc_html( $payment ); ?><span><?php $this->global_class->tool_tip( 'Manual' ); ?></span>	
						<?php
					} else {
						?>
						<select name="payment_gateway_select" id="payment_gateway_select">
							<option value=""><?php esc_html_e( 'Select a payment method', 'membership-for-woocommerce' ); ?></option>
							<?php
							foreach ( $payment_gateways as $gateway ) {

								if ( in_array( $gateway->id, $supported_gateways ) ) {
									?>
								<option value="<?php echo esc_html( $gateway->id ); ?>"><?php echo esc_html( $gateway->id ); ?></option>
									<?php
								}
							}
							?>
						</select>
					<?php } ?>

				</p>
			</div>
		</div>

	</div>

</div>
<!-- Members billing metabox end -->
