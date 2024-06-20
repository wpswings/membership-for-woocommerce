<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for membership using registration form tab.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage admin/partials/templates/membership-registration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$results = get_posts(
	array(
		'post_type' => 'wps_cpt_membership',
		'post_status' => 'publish',
		'numberposts' => -1,

	)
);

?>
<form action method="POST" class="wps-mfw-gen-section-form">
	<div class="mfw-secion-wrap">
		<div class="wps-form-group">
			<div class="wps-form-group__label" >
				<label for="wps_member_user_reg" class="wps-form-label" class="wps-form-label">
					<?php esc_html_e( 'Customer:', 'membership-for-woocommerce' ); ?>
				</label>
			</div>
			<div class="wps-form-group__control">
				<div class="wps-form-select">
					
					<select name="wps_member_user" id="wps_member_user_reg" required class="mdl-textfield__input mdc-text-field__input">
						<option value=""><?php esc_html_e( 'Select User', 'membership-for-woocommerce' ); ?></option>
						<?php
						$all_users = get_users(
							array(
								'fields' => array(
									'ID',
								),
							)
						);

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
								<option  value="<?php echo esc_html( $users->ID ); ?>"><?php echo esc_html( $user_info->user_login ) . '(#' . esc_html( $users->ID ) . ')'; ?></option>
									<?php

							}
						}
						?>
					</select>
				</div>
			</div>
		</div>

		<div class="wps-form-group">
			<div class="wps-form-group__label" >
				<label  class="wps-form-label"><?php esc_html_e( 'Select plan', 'membership-for-woocommerce' ); ?> </label>
			</div>
			<div class="wps-form-group__control">
				<div class="wps-form-select">
					<label class="mdc-text-field mdc-text-field--outlined">
					<span class="mdc-notched-outline mdc-notched-outline--no-label">
							<span class="mdc-notched-outline__leading"></span>
							<span class="mdc-notched-outline__notch"></span>
							<span class="mdc-notched-outline__trailing"></span>
						</span>
			
					<select id="wps_membership_add_member" name="members_plan_assign" required class="mdl-textfield__input mdc-text-field__input">
						<option value=""><?php esc_html_e( 'Select', 'membership-for-woocommerce' ); ?></option>
						<?php

						foreach ( $results as $key => $value ) {
							?>
						<option value="<?php echo esc_attr( $value->ID ); ?>"><?php echo esc_html( $value->post_title ); ?></option>
							<?php
						}

						?>
					</select>
					</label>
				</div>
			</div>
		</div>
		<div class="wps-form-group wps-mfw-text">
			<div class="wps-form-group__label">
				<label for="wps_add_member_name" class="wps-form-label"><?php esc_html_e( 'Name', 'membership-for-woocommerce' ); ?></label>
			</div>
			<div class="wps-form-group__control">
				<label class="mdc-text-field mdc-text-field--outlined">
					<span class="mdc-notched-outline mdc-notched-outline--upgraded">
						<span class="mdc-notched-outline__leading"></span>
						<span class="mdc-notched-outline__notch" style="">
						<span class="mdc-floating-label" id="my-label-id" style=""><?php esc_html_e( 'Enter Full Name', 'membership-for-woocommerce' ); ?></span>
						</span>
						<span class="mdc-notched-outline__trailing"></span>
					</span>
					<input type="text" id="wps_add_member_name" required name="billing_first_name" class="mdc-text-field__input" placeholder="Enter Full Name" >
				</label>
			</div>
		</div>

		<div class="wps-form-group wps-mfw-text">
			<div class="wps-form-group__label">
				<label for="wps_add_member_email" class="wps-form-label"><?php esc_html_e( 'Email', 'membership-for-woocommerce' ); ?></label>
			</div>
			<div class="wps-form-group__control">
				<label class="mdc-text-field mdc-text-field--outlined">
					<span class="mdc-notched-outline mdc-notched-outline--upgraded">
						<span class="mdc-notched-outline__leading"></span>
						<span class="mdc-notched-outline__notch" style="">
							<span class="mdc-floating-label" id="my-label-id" style=""><?php esc_html_e( 'Enter Email', 'membership-for-woocommerce' ); ?></span>
						</span>
						<span class="mdc-notched-outline__trailing"></span>
					</span>
					<input type="email" required id="wps_add_member_name" name="billing_email" class="mdc-text-field__input" placeholder="Enter Email" >
				</label>
			</div>
		</div>
		<div class="wps-form-group wps-mfw-number">
			<div class="wps-form-group__label">
				<label for="wps_add_member_phone"  class="wps-form-label"><?php esc_html_e( 'Phone', 'membership-for-woocommerce' ); ?></label>
			</div>
			<div class="wps-form-group__control">
				<label class="mdc-text-field mdc-text-field--outlined">
				<span class="mdc-notched-outline mdc-notched-outline--no-label">
					<span class="mdc-notched-outline__leading"></span>
					<span class="mdc-notched-outline__notch"></span>
					<span class="mdc-notched-outline__trailing"></span>
				</span>
					<input type="number" pattern="[0-9]" required id="wps_add_member_phone" name="billing_phone" class="mdc-text-field__input" min="0" >
				</label>
			</div>
		</div>
		<div class="wps-form-group wps-mfw-text">
			<div class="wps-form-group__label">
				<label for="wps_add_member_address"  class="wps-form-label"><?php esc_html_e( 'Address', 'membership-for-woocommerce' ); ?></label>
			</div>
			<div class="wps-form-group__control">
				<label class="mdc-text-field mdc-text-field--outlined">
					<span class="mdc-notched-outline mdc-notched-outline--upgraded">
						<span class="mdc-notched-outline__leading"></span>
						<span class="mdc-notched-outline__notch" style="">
						<span class="mdc-floating-label" id="my-label-id" style=""><?php esc_html_e( 'Enter Address', 'membership-for-woocommerce' ); ?></span>
						</span>
						<span class="mdc-notched-outline__trailing"></span>
					</span>
					<input type="text" id="wps_add_member_address" required name="billing_address_1" class="mdc-text-field__input" placeholder="Enter Full Name">
				</label>
			</div>
		</div>
		<?php $nonce = wp_create_nonce( 'wps-form-nonce' ); ?>
			<input type="hidden" name="wps_nonce_name" value="<?php echo esc_attr( $nonce ); ?>" /> 
		<div class="wps-form-group">
			<div class="wps-form-group__label"></div>
			<div class="wps-form-group__control">
				<button id="wps_add_member_button" name="wps_add_member_button" class="mdc-button mdc-button--raised">
					<span class="mdc-button__ripple"></span>
					<span class="mdc-button__label"><?php esc_html_e( 'Add Member', 'membership-for-woocommerce' ); ?></span>	
				</button>
			</div>
		</div>
	</div>
</form>
<?php
