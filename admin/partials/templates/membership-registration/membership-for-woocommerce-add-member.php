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

?>
<div class="form-field membership-customer">
<label for="wps_member_user_reg">
					<?php esc_html_e( 'Customer:', 'membership-for-woocommerce' ); ?>
				</label>
				<select name="wps_member_user_reg" id="wps_member_user_reg">
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

<div>
<label><?php esc_html_e('Select plan', 'membership-for-woocommerce');?> </label>
<?php
$results = get_posts(
    array(
        'post_type' => 'wps_cpt_membership',
        'post_status' => 'publish',
        'numberposts' => -1,

    )
);

?>
<select id="wps_membership_add_member" name="wps_membership_add_member">
    <option value=""><?php esc_html_e( 'Select', 'membership-for-woocommerce' ); ?></option>
    <?php 

foreach( $results as $key => $value  ){ ?>

    <option value="<?php echo esc_attr( $value->ID ); ?>"><?php echo esc_html( $value->post_title ); ?></option>

<?php
}

?>
</select></div>
<?php 