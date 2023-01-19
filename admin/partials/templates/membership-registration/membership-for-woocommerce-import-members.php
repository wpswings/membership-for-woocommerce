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
<select id="wps_import_member_select_plan" name="wps_import_member_select_plan">
    <option value=""><?php esc_html_e( 'Select....', 'membership-for-woocommerce' ); ?></option>
    <?php 

foreach( $results as $key => $value  ){ ?>

    <option value="<?php echo esc_attr( $value->ID ); ?>"><?php echo esc_html( $value->post_title ); ?></option>

<?php
}

?>
</select></div>

<div>
    <label for="wps_import_member_file"><?php esc_html_e( 'Upload File' ) ?></label>
    <input type="file" id="wps_import_member_file" name="wps_import_member_file">
</div>
<button id="wps_import_member" class="button"><?php esc_html_e( 'Import Members', 'membership-for-woocommerce' ) ?></button>