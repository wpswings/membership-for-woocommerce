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

$member_details = get_post_meta( $post->ID, 'billing_details', true );

//echo '<pre>'; print_r( $post->post_author ); echo '</pre>';

echo '<pre>'; print_r( $member_details ); echo '</pre>';
//$user = get_user_by( 'email', $member_details['membership_billing_email'] );
//echo '<pre>'; print_r( $user ); echo '</pre>';
//print_r( wp_create_user( 'brijmohan', 'xyzzzzz', 'brij@gmail.com' ) );
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
					<a href="<?php echo esc_url( admin_url( 'admin.php?edit.php?post_status=all&post_type=mwb_cpt_members&post_author=' . $post->post_author ) ); ?>" target="_blank"><?php esc_html_e( 'View other memberships', 'membership-for-woocommerce' ); ?></a>
					<a href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $post->post_author ) ); ?>" target="_blank"><?php esc_html_e( 'Profile', 'membership-for-woocommerce' ); ?></a>
				</label><br>
				<select name="mwb_member_user" id="mwb_member_user">
					<option value=""></option>
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
					<?php echo sprintf( ' %s %s ', esc_html( $member_details['membership_billing_first_name'] ), esc_html( $member_details['membership_billing_last_name'] ) ); ?></br>
					<?php echo esc_html( $member_details['membership_billing_company'] ); ?></br>
					<?php echo sprintf( ' %s %s ', esc_html( $member_details['membership_billing_address_1'] ), esc_html( $member_details['membership_billing_address_2'] ) ); ?></br>
					<?php echo sprintf( ' %s %s ', esc_html( $member_details['membership_billing_city'] ), esc_html( $member_details['membership_billing_postcode'] ) ); ?></br>
					<?php echo sprintf( ' %s, %s ', esc_html( $member_details['membership_billing_state'] ), esc_html( $member_details['membership_billing_country'] ) ); ?>
				</p>

				<p>
					<strong><?php esc_html_e( 'Email address :', 'membership-for-woocommerce' ); ?></strong></br>
					<a href="mailto:<?php echo esc_html( $member_details['membership_billing_email'] ); ?>"><?php echo esc_html( $member_details['membership_billing_email'] ); ?></a>
				</p>

				<p>
					<strong><?php esc_html_e( 'Phone :', 'membership-for-woocommerce' ); ?></strong></br>
					<?php echo esc_html( $member_details['membership_billing_phone'] ); ?>
				</p>

				<p>
					<strong><?php esc_html_e( 'Payment Method', 'membership-for-woocommerce' ); ?></strong></br>
					<?php echo esc_html( $member_details['payment_method'] ); ?>
				</p>

			</div>

			<div class="member_edit_address" style="display: none;">
				<p class="form-field billing_first_name">
					<label for="billing_first_name"><?php esc_html_e( 'First name', 'membership-for-woocommerce' ); ?></label>
					<input type="text" class="short" name="billing_first_name" id="billing_first_name" value="<?php echo esc_html( $member_details['membership_billing_first_name'] ); ?>" >
				</p>

				<p class="form-field billing_last_name">
					<label for="billing_last_name"><?php esc_html_e( 'Last name', 'membership-for-woocommerce' ); ?></label>
					<input type="text" class="short" name="billing_last_name" id="billing_last_name" value="<?php echo esc_html( $member_details['membership_billing_last_name'] ); ?>" >
				</p>

				<p class="form-field billing_company">
					<label for="billing_company"><?php esc_html_e( 'Company', 'membership-for-woocommerce' ); ?></label>
					<input type="text" class="short" name="billing_company" id="billing_company" value="<?php echo esc_html( $member_details['membership_billing_company'] ); ?>" >
				</p>

				<p class="form-field billing_address_1">
					<label for="billing_address_1"><?php esc_html_e( 'Address line 1', 'membership-for-woocommerce' ); ?></label>
					<input type="text" class="short" name="billing_address_1" id="billing_address_1" value="<?php echo esc_html( $member_details['membership_billing_address_1'] ); ?>" >
				</p>

				<p class="form-field billing_address_2">
					<label for="billing_address_2"><?php esc_html_e( 'Address line 2', 'membership-for-woocommerce' ); ?></label>
					<input type="text" class="short" name="billing_address_2" id="billing_address_2" value="<?php echo esc_html( $member_details['membership_billing_address_2'] ); ?>" >
				</p>

				<p class="form-field billing_city">
					<label for="billing_city"><?php esc_html_e( 'City', 'membership-for-woocommerce' ); ?></label>
					<input type="text" class="short" name="billing_city" id="billing_city" value="<?php echo esc_html( $member_details['membership_billing_city'] ); ?>" >
				</p>

				<p class="form-field billing_postcode">
					<label for="billing_postcode"><?php esc_html_e( 'Postcode/ZIP', 'membership-for-woocommerce' ); ?></label>
					<input type="text" class="short" name="billing_postcode" id="billing_postcode" value="<?php echo esc_html( $member_details['membership_billing_postcode'] ); ?>">
				</p>

				<p class="form-field billing_country">
					<label for="billing_country"><?php esc_html_e( 'Country', 'membership-for-woocommerce' ); ?></label>
					<select name="billing_country" id="billing_country">

					</select>
				</p>

				<p class="form-field billing_state">
					<label for="billing_state"><?php esc_html_e( 'State/County', 'membership-for-woocommerce' ); ?></label>
					<select name="billing_state" id="billing_state">

					</select>
				</p>

				<p class="form-field billing_email">
					<label for="billing_email"><?php esc_html_e( 'Email address', 'membership-for-woocommerce' ); ?></label>
					<input type="text" class="short" name="billing_email" id="billing_email" value="<?php echo esc_html( $member_details['membership_billing_email'] ); ?>">
				</p>

				<p class="form-field billing_phone">
					<label for="billing_phone"><?php esc_html_e( 'Phone', 'membership-for-woocommerce' ); ?></label>
					<input type="text" name="billing_phone" id="billing_phone" value="<?php echo esc_html( $member_details['membership_billing_phone'] ); ?>">
				</p>

				<p class="form-field payment_method">
					<strong><?php esc_html_e( 'Payment method', 'membership-for-woocommerce' ); ?></strong></br>
					<?php echo esc_html( $member_details['payment_method'] ); ?><span><?php $this->global_class->tool_tip( 'Manual' ); ?></span>	
				</p>
			</div>
		</div>

	</div>

</div>
<!-- Members billing metabox end -->
