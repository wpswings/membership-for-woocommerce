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

<div class="wps-form-group">
<div class="wps-form-group__label">
    <label for="wps_membership_content_restriction" class="wps-form-label"><?php esc_html_e('Select plan', 'membership-for-woocommerce');?></label>
</div>
<?php
$results = get_posts(
    array(
        'post_type' => 'wps_cpt_membership',
        'post_status' => 'publish',
        'numberposts' => -1,

    )
);

?>
<div class="wps-form-group__control">
	<div class="wps-form-select">
        <select id="wps_membership_content_restriction" name="wps_membership_content_restriction" class="mdl-textfield__input">
            <option value=""><?php esc_html_e( 'Select....', 'membership-for-woocommerce' ); ?></option>
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
<?php 
foreach( $results as $key => $value  ){  

    $pages = get_posts(
        array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'numberposts' => -1,
    
        )
    );

    foreach( $pages as $index => $values ){
        if( 'Shop' != $values->post_title ){ ?>
      
            <div class="wps-form-group">
                <div class="wps-form-group__label">
                    <label  class="wps-form-label"><?php echo esc_html( $values->post_title ); ?></label>
                </div>
                <div class="wps-form-group__control wps-pl-4">
                    <div class="mdc-form-field">
                        <div class="mdc-checkbox">
                            <input 
                            type="checkbox"
                            class="mdc-checkbox__native-control" value="<?php echo esc_attr( $values->ID ); ?>"
                            />
                            <div class="mdc-checkbox__background">
                                <svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
                                    <path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59"/>
                                </svg>
                                <div class="mdc-checkbox__mixedmark"></div>
                            </div>
                            <div class="mdc-checkbox__ripple"></div>
                        </div>
                    </div>
                </div>
			</div>
            
        <?php }
    }
}
?>

                      

<button class="button"><?php esc_html_e( 'Save', 'membership-for-woocommerce' ) ?></button>

