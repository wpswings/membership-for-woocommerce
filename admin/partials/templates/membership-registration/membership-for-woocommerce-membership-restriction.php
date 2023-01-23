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
            <div class="wps-form-group__label">
                <label class="wps-form-label"><?php esc_html_e('Select plan', 'membership-for-woocommerce');?> </label>
            </div>
            <div class="wps-form-group__control">
                <div class="wps-form-select">
                        <select id="wps_membership_plan_for_restriction" name="wps_membership_plan_for_restriction" class="mdl-textfield__input">
                        <option value=""><?php esc_html_e('Select plan', 'membership-for-woocommerce');?></option>
                            <?php 

                            foreach( $results as $key => $value  ){ 
                                
                                ?>

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
            ?>
            <div  class="wps_membership_plan_fields  wps_reg_plan_<?php echo esc_attr( $value->ID );?>">
                <div class="wps-form-group" >
                    <div class="wps-form-group__label">
                        <label class="wps-form-label"><?php $label = 'Select Type for ' . $value->post_title; echo esc_html( $label ); ?></label>
                        
                    </div>
                    <div class="wps-form-group__control">
                        <div class="wps-form-select">
                            <select class="mdl-textfield__input" id="wps_membership_select_type_reg_<?php echo esc_attr( $value->ID );?>" name="wps_membership_select_type_reg_<?php echo esc_attr( $value->ID );?>">
                                <option value="products"><?php esc_html_e( 'Products', 'membership-for-woocommerce' ) ?></option>
                                <option value="product_cat"><?php esc_html_e( 'Product Categories', 'membership-for-woocommerce' ) ?></option>
                                <option value="product_tag"><?php esc_html_e( 'Product Tags', 'membership-for-woocommerce' ) ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="wps-form-group">
                    <div class="wps-form-group__label">
                    <label class="wps-form-label"><?php $label = 'Select Title to restrict from non-members for ' . $value->post_title; echo esc_html( $label ); ?></label>
                    </div>
                    <div class="wps-form-group__control">
                        <div class="wps-form-select">
                            <select id="wps_membership_plan_target_ids_search_reg_<?php echo esc_attr( $value->ID );?>" name="wps_membership_plan_target_ids_search_reg_<?php echo esc_attr( $value->ID );?>[]" class="wc-membership-product-search mdl-textfield__input" multiple="multiple"  data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'membership-for-woocommerce' ); ?>">

                                <option></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="wps-form-group">
                    <div class="wps-form-group__label">
                        <label class="wps-form-label"><?php $label = 'Select Accessibility for ' . $value->post_title; echo esc_html( $label ); ?></label>
                    </div>
                    <div class="wps-form-group__control">
                        <div class="wps-form-select">
                            <select id="wps_membership_accessibility_type_reg_<?php echo esc_attr( $value->ID );?>" class="mdl-textfield__input" name="wps_membership_accessibility_reg_<?php echo esc_attr( $value->ID );?>" >
                                <option value="immediately"><?php esc_html_e( 'Immediatedly', 'membership-for-woocommerce' ) ?></option>
                                <option value="specify_time"><?php esc_html_e( 'Specify Time', 'membership-for-woocommerce' ) ?></option>
                            </select>
                            <input type="number" id="wps_membership_accessibility_input_<?php echo esc_attr( $value->ID );?>" name="wps_membership_accessibility_input_<?php echo esc_attr( $value->ID );?>" class="mdl-textfield__input">
                            <select id="wps_membership_accessibility_time_span_<?php echo esc_attr( $value->ID );?>" name="wps_membership_accessibility_time_span_<?php echo esc_attr( $value->ID );?>" class="mdl-textfield__input">
                                <option value="days"><?php esc_html_e( 'Days', 'membership-for-woocommerce' ) ?></option>
                                <option value="weeks"><?php esc_html_e( 'Weeks', 'membership-for-woocommerce' ) ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>





        <?php 
        }

        ?>
            <div class="wps-form-group">
                <div class="wps-form-group__control">
                    <button id="wps_membership_restriction_button" name="wps_membership_restriction_button" class="mdc-button mdc-button--raised"><span class="mdc-button__ripple"></span>
                    <span class="mdc-button__label"><?php esc_html_e( 'Save', 'membership-for-woocommerce' ) ?></span>
                    </button>
                </div>
            </div>
        </div>
</form>