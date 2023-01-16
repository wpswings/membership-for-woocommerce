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
<select   id="wps_membership_plan_for_discount_offer" name="wps_membership_plan_for_discount_offer">
    <option value=""><?php esc_html_e( 'Select....', 'membership-for-woocommerce' ); ?></option>
    <?php 

foreach( $results as $key => $value  ){ ?>

    <option value="<?php echo esc_attr( $value->ID ); ?>"><?php echo esc_html( $value->post_title ); ?></option>

<?php
}

?>
</select></div>
<div>
    <?php 

    foreach( $results as $key => $value  ){ ?>
    <div>
        <label><?php esc_html_e( 'Select Type' ); ?></label>
        <select>
            <option value="products"><?php esc_html_e( 'Products', 'membership-for-woocommerce' ) ?></option>
            <option value="product_cat"><?php esc_html_e( 'Product Categories', 'membership-for-woocommerce' ) ?></option>
            <option value="product_tag"><?php esc_html_e( 'Product Tags', 'membership-for-woocommerce' ) ?></option>
        </select>
    </div>
    <div>
        <label><?php esc_html_e( 'Select Title to offer a discount for members ' ); ?></label>
        <select id="wps_membership_plan_target_ids_search_discount_reg" class="wc-membership-product-search" multiple="multiple" name="wps_membership_plan_target_ids_search_discount_reg[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'membership-for-woocommerce' ); ?>">

        <option></option>

        </select>
    </div>

    <div>
        <label><?php esc_html_e( 'Discount type ' ); ?></label>
        <select id="wps_membership_discount_type"   name="wps_membership_discount_type" >

        <option value=""><?php esc_html_e( 'Select....', 'membership-for-woocommerce' ); ?></option>
        <option value="fixed"><?php esc_html_e( 'Fixed', 'membership-for-woocommerce' ); ?></option>
        <option value="percentage"><?php esc_html_e( 'Percentage', 'membership-for-woocommerce' ); ?></option>

        </select>
    </div>
    <div>
    <label><?php esc_html_e( 'Enter Discount ' ); ?></label>
        <input type="number" id="wps_membership_discount_amount" name="wps_membership_discount_amount" placeholder="Enter discount">
    </div>

    <?php };
    ?>
    <button class="button"><?php esc_html_e( 'Save', 'membership-for-woocommerce' ) ?></button>
</div>
