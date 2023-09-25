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

$mfw_plugins = get_option( 'active_plugins' );
if ( ! in_array( 'membership-for-woocommerce-pro/membership-for-woocommerce-pro.php', $mfw_plugins ) ) {

	wps_mfw_upgrade_pro_popup();
}

/**
 * Function for popup.
 *
 * @return void
 */
function wps_mfw_upgrade_pro_popup() {
	?>

		<!-- Go pro popup wrap start. -->
	<div class="wps_ubo_lite_go_pro_popup_wrap">
		<!-- Go pro popup main start. -->
		<div class="wps_ubo_lite_go_pro_popup">
			<!-- Main heading. -->
			<div class="wps_ubo_lite_go_pro_popup_head">
				<h2><?php esc_html_e( 'Want More? Go Pro !!', 'membership-for-woocommerce' ); ?></h2>
				<!-- Close button. -->
				<a href="" class="wps_ubo_lite_go_pro_popup_close">
					<span>&times;</span>
				</a>
			</div>  

			<!-- Notice icon. -->
			<div class="wps_ubo_lite_go_pro_popup_head"><img src="<?php echo esc_url( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/pro.png' ); ?> ">
			</div>

			<!-- Notice. -->
			<div class="wps_ubo_lite_go_pro_popup_content">
				<p class="wps_ubo_lite_go_pro_popup_text">
					<?php esc_html_e( 'A straightforward membership plugin that functions seamlessly on your eCommerce business will help you build your community of members with premium features which gives two Free Templates of Comparision and Simple, Create & sort plans, get revenue & detailed reports, give discounts, override access to posts, comments on your protected posts and many more.', 'membership-for-woocommerce' ); ?>
				</p>
			</div>

			<!-- Go pro button. -->
			<div class="wps_ubo_lite_go_pro_popup_button">
				<a class="button wps_ubo_lite_overview_go_pro_button" target="_blank" href="https://wpswings.com/product/membership-for-woocommerce-pro/?utm_source=wpswings-membership-pro&utm_medium=membership-org-backend&utm_campaign=go-pro"><?php echo esc_html__( 'Upgrade to Premium', 'membership-for-woocommerce' ) . ' <span class="dashicons dashicons-arrow-right-alt"></span>'; ?></a>
			</div>
		</div>
		<!-- Go pro popup main end. -->
	</div>
	<!-- Go pro popup wrap end. -->


	<?php
}

$results = get_posts(
	array(
		'post_type' => 'wps_cpt_membership',
		'post_status' => 'publish',
		'numberposts' => -1,

	)
);

?>

<p>
	<?php esc_html_e( 'In this Section whatever the products, categories, tags you will choose, Members will get DISCOUNT on these products.', 'membership-for-woocommerce' ); ?>
</p>

<form action method="POST" class="wps-mfw-gen-section-form">
	<div class="mfw-secion-wrap">
		<div class="wps-form-group">
			<div class="wps-form-group__label">
				<label class="wps-form-label"><?php esc_html_e( 'Select plan', 'membership-for-woocommerce' ); ?> </label>
			</div>
			<div class="wps-form-group__control">
				<div class="wps-form-select">
				<label class="mdc-text-field mdc-text-field--outlined">
					<span class="mdc-notched-outline mdc-notched-outline--no-label">
							<span class="mdc-notched-outline__leading"></span>
							<span class="mdc-notched-outline__notch"></span>
							<span class="mdc-notched-outline__trailing"></span>
						</span>
					<select   id="wps_membership_plan_for_discount_offer" name="wps_membership_plan_for_discount_offer" class="mdl-textfield__input mdc-text-field__input">
						<option value=""><?php esc_html_e( 'Select....', 'membership-for-woocommerce' ); ?></option>
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

			<?php

			foreach ( $results as $key => $value ) {
				?>
			<div  class="wps_membership_plan_fields  wps_reg_plan_<?php echo esc_attr( $value->ID ); ?>">
				<div class="wps-form-group wps-membership__plan--pro-disabled">
					<div class="wps-form-group__label">
						<label class="wps-form-label"><?php esc_html_e( 'Select Products to offer a discount for ', 'membership-for-woocommerce' ); ?><span style="color:red"><?php echo esc_html( $value->post_title ); ?></span></label>
					</div>
					<div class="wps-form-group__control">
						<div class="wps-form-select">
							<select id="wps_membership_plan_target_ids_search_discount_reg_<?php echo esc_html( $value->ID ); ?>"  class="wc-membership-product-search mdl-textfield__input" multiple="multiple" name="wps_membership_plan_target_ids_search_discount_reg_<?php echo esc_html( $value->ID ); ?>[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'membership-for-woocommerce' ); ?>">
							<?php

								$wps_membership_plan_target_product_ids = wps_membership_get_meta_data( $value->ID, 'wps_membership_plan_target_disc_ids', true );
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
				<div class="wps-form-group wps-membership__plan--pro-disabled">
					<div class="wps-form-group__label">
						<label class="wps-form-label"><?php esc_html_e( 'Select Product Categories to offer a discount for  ', 'membership-for-woocommerce' ); ?><span style="color:red"><?php echo esc_html( $value->post_title ); ?></span></label>
					</div>
					<div class="wps-form-group__control">
						<div class="wps-form-select">
							<select id="wps_membership_plan_target_ids_search_discount_cat_reg_<?php echo esc_html( $value->ID ); ?>"  class="wc-membership-product-category-search mdl-textfield__input" multiple="multiple" name="wps_membership_plan_target_ids_search_discount_cat_reg_<?php echo esc_html( $value->ID ); ?>[]" data-placeholder="<?php esc_attr_e( 'Search for a product category&hellip;', 'membership-for-woocommerce' ); ?>">
							<?php

								$wps_membership_plan_target_product_ids = wps_membership_get_meta_data( $value->ID, 'wps_membership_plan_target_disc_categories', true );
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
				<div class="wps-form-group wps-membership__plan--pro-disabled">
					<div class="wps-form-group__label">
						<label class="wps-form-label"><?php esc_html_e( 'Select Prodcucts tags to offer a discount for ', 'membership-for-woocommerce' ); ?><span style="color:red"><?php echo esc_html( $value->post_title ); ?></span></label>
					</div>
					<div class="wps-form-group__control">
						<div class="wps-form-select">
							<select id="wps_membership_plan_target_ids_search_discount_tag_reg_<?php echo esc_html( $value->ID ); ?>"  class="wc-membership-product-tag-search mdl-textfield__input" multiple="multiple" name="wps_membership_plan_target_ids_search_discount_tag_reg_<?php echo esc_html( $value->ID ); ?>[]" data-placeholder="<?php esc_attr_e( 'Search for a product tag&hellip;', 'membership-for-woocommerce' ); ?>">
							<?php

								$wps_membership_plan_target_product_ids = wps_membership_get_meta_data( $value->ID, 'wps_membership_plan_target_disc_tags', true );
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

				<div class="wps-form-group wps-membership__plan--pro-disabled">
					<div class="wps-form-group__label">
					<label  class="wps-form-label"><?php esc_html_e( 'Discount type ', 'membership-for-woocommerce' ); ?><span style="color:red"><?php echo esc_html( $value->post_title ); ?></span></label>
					</div>
					<div class="wps-form-group__control">
						<div class="wps-form-select">
							<select id="wps_membership_discount_type_<?php echo esc_html( $value->ID ); ?>"   name="wps_membership_discount_type_<?php echo esc_html( $value->ID ); ?>" class="mdl-textfield__input" >
								<?php
								$product_discount_type = wps_membership_get_meta_data( $value->ID, 'wps_membership_product_offer_price_type', true );
								?>
								
								<option value="fixed" 
								<?php
								if ( 'fixed' == $product_discount_type ) {
									esc_attr_e( 'selected', 'membership-for-woocommerce' );
								}
								?>
								><?php esc_html_e( 'Fixed', 'membership-for-woocommerce' ); ?></option>
								<option value="%"  
								<?php
								if ( '%' == $product_discount_type ) {
									esc_attr_e( 'selected', 'membership-for-woocommerce' );
								}
								?>
								><?php esc_html_e( 'Percentage', 'membership-for-woocommerce' ); ?></option>

							</select>
						</div>
					</div>
				</div>
				<div class="wps-form-group wps-membership__plan--pro-disabled ">
					<div class="wps-form-group__label">
						<label  class="wps-form-label"><?php esc_html_e( 'Enter Discount ', 'membership-for-woocommerce' ); ?><span style="color:red"><?php echo esc_html( $value->post_title ); ?></span></label>
					</div>
					<div class="wps-form-group__control">
						<label class="mdc-text-field mdc-text-field--outlined wps_admin_membership_price_wrapper">
							<span class="mdc-notched-outline mdc-notched-outline--no-label">
								<span class="mdc-notched-outline__leading"></span>
								<span class="mdc-notched-outline__notch"></span>
								<span class="mdc-notched-outline__trailing"></span>
							</span>
							<input required type="number" id="wps_membership_discount_amount_<?php echo esc_html( $value->ID ); ?>" class="mdc-text-field__input wps_membership_discount__amount" name="wps_membership_discount_amount_<?php echo esc_html( $value->ID ); ?>" min="0" placeholder="Enter discount" value="<?php echo esc_attr( ! empty( wps_membership_get_meta_data( $value->ID, 'wps_memebership_product_discount_price', true ) ) ? wps_membership_get_meta_data( $value->ID, 'wps_memebership_product_discount_price', true ) : 0 ); ?>">
						</label>
					</div>
				</div>
			</div>
				<?php
			};
			?>
		<?php $nonce = wp_create_nonce( 'wps-form-nonce' ); ?>
		<input type="hidden" name="wps_nonce_name" value="<?php echo esc_attr( $nonce ); ?>" /> 
		<div class="wps-form-group ">
			<div class="wps-form-group__control">
				<button id="wps_membership_discount_button" name="wps_membership_discount_button" class="mdc-button mdc-button--raised"><span class="mdc-button__ripple"></span>
				<span class="mdc-button__label"><?php esc_html_e( 'Save', 'membership-for-woocommerce' ); ?></span>
				</button>
			</div>
		</div>
	</div>
</form>

