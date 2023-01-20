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
<div class="wps-form-group">
	<div class="wps-form-group__label" >
		<label for="wps_member_user_reg" class="wps-form-label" class="wps-form-label">
			<?php esc_html_e( 'Customer:', 'membership-for-woocommerce' ); ?>
		</label>
	</div>
	<div class="wps-form-group__control">
		<div class="wps-form-select">
			<select name="wps_member_user_reg" id="wps_member_user_reg" class="mdl-textfield__input">
				<option value=""><?php esc_html_e( 'Select User', 'membership-for-woocommerce' ) ?></option>
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
		<label  class="wps-form-label"><?php esc_html_e('Select plan', 'membership-for-woocommerce');?> </label>
	</div>
	<div class="wps-form-group__control">
		<div class="wps-form-select">
			<select id="wps_membership_add_member" name="wps_membership_add_member" class="mdl-textfield__input">
				<option value=""><?php esc_html_e( 'Select', 'membership-for-woocommerce' ); ?></option>
				<?php 

			foreach( $results as $key => $value  ){ ?>

				<option value="<?php echo esc_attr( $value->ID ); ?>"><?php echo esc_html( $value->post_title ); ?></option>

			<?php
			}

			?>
			</select>
		</div>
	</div>
</div>

	<label for="wps_add_member_name"><?php esc_html_e('Name', 'membeship-for-woocommerce'); ?></label>
	<input type="text" id="wps_add_member_name" name="wps_add_member_name" placeholder="Enter Full Name">
</div>
<div>
	<label for="wps_add_member_phone"><?php esc_html_e('Phone', 'membeship-for-woocommerce'); ?></label>
	<input type="number" id="wps_add_member_phone" name="wps_add_member_phone" placeholder="Enter Full Name">
</div>
<div>
	<label for="wps_add_member_address"><?php esc_html_e('Address', 'membeship-for-woocommerce'); ?></label>
	<input type="text" id="wps_add_member_address" name="wps_add_member_address" placeholder="Enter Full Name">
</div>
<button id="wps_add_member_button" class="button"><?php esc_html_e( 'Add Member', 'membership-for-woocommerce' ); ?></button>
<?php 