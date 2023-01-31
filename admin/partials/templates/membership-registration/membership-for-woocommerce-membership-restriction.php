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
                <div class="wps-form-group">
                    <div class="wps-form-group__label">
                    <label class="wps-form-label"><?php esc_html_e( 'Select Product to restrict from non-members for ', 'membership-for-woocommerce') ?><span style="color:red"><?php echo esc_html( $value->post_title );?></label>
                    </div>
                    <div class="wps-form-group__control">
                        <div class="wps-form-select">
                            <select id="wps_membership_plan_target_ids_<?php echo esc_attr( $value->ID );?>" name="wps_membership_plan_target_ids_<?php echo esc_attr( $value->ID );?>[]" class="wc-membership-product-search mdl-textfield__input" multiple="multiple"  data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'membership-for-woocommerce' ); ?>">
                                <?php
                                
                                $wps_membership_plan_target_product_ids = get_post_meta( $value->ID ,'wps_membership_plan_target_ids', true );
                                if ( is_array( $wps_membership_plan_target_product_ids ) && ! empty( $wps_membership_plan_target_product_ids ) ) {

                                    foreach ( $wps_membership_plan_target_product_ids as $wps_membership_plan_single_target_product_ids ) {

                                        $product_name = get_the_title( $wps_membership_plan_single_target_product_ids );
                                        ?>

                                <option value="<?php echo esc_html( $wps_membership_plan_single_target_product_ids ); ?>" <?php echo ( in_array( $wps_membership_plan_single_target_product_ids, $wps_membership_plan_target_product_ids, true ) ? 'selected' : '' ); ?>><?php echo( esc_html( $product_name ) . '(#' . esc_html( $wps_membership_plan_single_target_product_ids ) . ')' ); ?></option>

                                        <?php
                                    }
                                }
                                ?>
                                
                            </select>
                        </div>
                    </div>
                </div>
                <div class="wps-form-group">
                    <div class="wps-form-group__label">
                    <label class="wps-form-label"><?php esc_html_e( 'Select Product Categories to restrict from non-members for ', 'membership-for-woocommerce') ?><span style="color:red"><?php echo esc_html( $value->post_title );?></label>
                    </div>
                    <div class="wps-form-group__control">
                        <div class="wps-form-select">
                            <select id="wps_membership_plan_target_cats_<?php echo esc_attr( $value->ID );?>" name="wps_membership_plan_target_cats_<?php echo esc_attr( $value->ID );?>[]" class="wc-membership-product-category-search mdl-textfield__input" multiple="multiple"  data-placeholder="<?php esc_attr_e( 'Search for a Categories&hellip;', 'membership-for-woocommerce' ); ?>">
                            <?php
                                
                                $wps_membership_plan_target_product_ids = get_post_meta( $value->ID ,'wps_membership_plan_target_categories', true );
                                if ( is_array( $wps_membership_plan_target_product_ids ) && ! empty( $wps_membership_plan_target_product_ids ) ) {

                                    foreach ( $wps_membership_plan_target_product_ids as $wps_membership_plan_single_target_product_ids ) {

                                        $product_name = get_the_category_by_ID( $wps_membership_plan_single_target_product_ids );
                                        ?>

                                <option value="<?php echo esc_html( $wps_membership_plan_single_target_product_ids ); ?>" <?php echo ( in_array( $wps_membership_plan_single_target_product_ids, $wps_membership_plan_target_product_ids, true ) ? 'selected' : '' ); ?>><?php echo( esc_html( $product_name ) . '(#' . esc_html( $wps_membership_plan_single_target_product_ids ) . ')' ); ?></option>

                                        <?php
                                    }
                                }
                                ?>
                            
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="wps-form-group">
                    <div class="wps-form-group__label">
                    <label class="wps-form-label"><?php esc_html_e( 'Select Product tags to restrict from non-members for ', 'membership-for-woocommerce') ?><span style="color:red"><?php echo esc_html( $value->post_title );?></label>
                    </div>
                    <div class="wps-form-group__control">
                        <div class="wps-form-select">
                            <select id="wps_membership_plan_target_tags_<?php echo esc_attr( $value->ID );?>" name="wps_membership_plan_target_tags_<?php echo esc_attr( $value->ID );?>[]" class="wc-membership-product-tag-search mdl-textfield__input" multiple="multiple"  data-placeholder="<?php esc_attr_e( 'Search for a tags&hellip;', 'membership-for-woocommerce' ); ?>">
                            <?php
                                
                                $wps_membership_plan_target_product_ids = get_post_meta( $value->ID ,'wps_membership_plan_target_tags', true );
                                if ( is_array( $wps_membership_plan_target_product_ids ) && ! empty( $wps_membership_plan_target_product_ids ) ) {

                                    foreach ( $wps_membership_plan_target_product_ids as $wps_membership_plan_single_target_product_ids ) {
                                        $tagn     = get_term_by( 'id', $wps_membership_plan_single_target_product_ids, 'product_tag' );
                                        $product_name = $tagn->name;
                                        ?>

                                <option value="<?php echo esc_html( $wps_membership_plan_single_target_product_ids ); ?>" <?php echo ( in_array( $wps_membership_plan_single_target_product_ids, $wps_membership_plan_target_product_ids, true ) ? 'selected' : '' ); ?>><?php echo( esc_html( $product_name ) . '(#' . esc_html( $wps_membership_plan_single_target_product_ids ) . ')' ); ?></option>

                                        <?php
                                    }
                                }
                                ?>
                            
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