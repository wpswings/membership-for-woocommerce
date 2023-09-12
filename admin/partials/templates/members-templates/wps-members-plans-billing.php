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
$order_val = new WC_Order( wps_membership_get_meta_data( $post->ID, 'member_order_id', true ) );
$payment = $order_val->get_payment_method_title();
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

// $supported_gateways = $instance->supported_gateways();.


// Creating Instance of the WC_Countries class.
$country_class = new WC_Countries();
$current_user_assigned = wps_membership_get_meta_data( $post->ID, 'wps_member_user', true );
?>

<!-- Members billing metabox start -->
<div class="members_billing_details">

	<h1><?php echo sprintf( 'Member #%u details', esc_html( $post->ID ) ); ?></h1>

	<span class="wps_member_notice">
	<?php
	if ( wps_membership_is_plugin_active( 'subscriptions-for-woocommerce/subscriptions-for-woocommerce.php' ) ) {

		esc_html_e( 'Only membership will be purchased subscription will not be activated if Membership plan assigned from here !!', 'membership-for-woocommerce' );
	}
	?>
	
	</span>

	<div class="members_data_column_container">

		<div class="members_data_column">
			<h3><?php esc_html_e( 'General', 'membership-for-woocommerce' ); ?></h3>

			<p class="form-field membership-customer">
				<label for="member-user">
					<?php esc_html_e( 'Customer:', 'membership-for-woocommerce' ); ?>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_status=all&post_type=wps_cpt_members&post_author=' . $post->post_author ) ); ?>" target="_blank"><?php esc_html_e( 'View other memberships', 'membership-for-woocommerce' ); ?></a>
					<a href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $post->post_author ) ); ?>" target="_blank"><?php esc_html_e( 'Profile', 'membership-for-woocommerce' ); ?></a>
				</label><br>
				<select name="wps_member_user" id="wps_member_user">
					<?php


					if ( ! empty( $all_users ) && is_array( $all_users ) ) {

						foreach ( $all_users as $users ) {
							$user_info  = get_user_by( 'ID', $users->ID );
							$user_meta  = get_userdata( $users->ID );
							$user_roles = $user_meta->roles; // array of roles the user is part of.
							$user_role = '';
							if ( ! empty( $user_roles ) ) {
								$user_role = $user_roles[0];
							}



							?>
							<option <?php echo esc_html( $users->ID === $current_user_assigned ? 'selected' : '' ); ?> value="<?php echo esc_html( $users->ID ); ?>"><?php echo esc_html( $user_info->user_login ) . '(#' . esc_html( $users->ID ) . ')'; ?></option>
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
			<a href="#" class="cancel_member_edit" ><span class="dashicons dashicons-no-alt"></span></a>

			<div class="member_address">
				<p>
				<strong><?php esc_html_e( 'Address :', 'membership-for-woocommerce' ); ?></strong></br>
					<?php
					if ( ! empty( $first_name ) ) {
						?>
						<?php echo sprintf( ' %s %s ', esc_html( $first_name ), esc_html( $last_name ) ); ?></br>
						<?php echo esc_html( $company ); ?></br>
						<?php echo sprintf( ' %s %s ', esc_html( $address_1 ), esc_html( $address_2 ) ); ?></br>
						<?php echo sprintf( ' %s %s ', esc_html( $city ), esc_html( $postcode ) ); ?></br>
						<?php echo sprintf( ' %s, %s ', esc_html( $state ), esc_html( $country ) ); ?>
						<?php
					} else {
						esc_html_e( 'No billing details', 'membership-for-woocommerce' );
					}
					?>
				</p>

				<p>
					<strong><?php esc_html_e( 'Email address :', 'membership-for-woocommerce' ); ?></strong></br>
					<a href="mailto:<?php echo esc_html( $email ); ?>"><?php echo esc_html( $email ); ?></a>
				</p>

				<p>
					<strong><?php esc_html_e( 'Phone :', 'membership-for-woocommerce' ); ?></strong></br>
					<?php echo esc_html( $phone ); ?>
				</p>
					<strong><?php esc_html_e( 'Payment Method', 'membership-for-woocommerce' ); ?></strong></br>
					<?php echo esc_html( $payment ); ?>
			</div>

			<div class="member_edit_address" >

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
							<option <?php echo esc_html( $code === $country ? 'selected' : '' ); ?>  value="<?php echo esc_html( $code ); ?>"><?php echo esc_html( $name ); ?></option>
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

			
			</div>
		</div>

	</div>

</div>
<!-- Members billing metabox end -->
