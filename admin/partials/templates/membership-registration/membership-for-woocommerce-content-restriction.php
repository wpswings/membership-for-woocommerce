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


?>

<p>
	<?php esc_html_e( 'In this Section whatever the pages you will select, ONLY MEMBERS can access the pages.', 'membership-for-woocommerce' ); ?>
</p>
<form action method="POST" class="wps-mfw-gen-section-form">
	<div class="mfw-secion-wrap">
<div class="wps-form-group">
<div class="wps-form-group__label">
	<label for="wps_membership_content_restriction" class="wps-form-label"><?php esc_html_e( 'Select plan', 'membership-for-woocommerce' ); ?></label>
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
	<label class="mdc-text-field mdc-text-field--outlined">
					<span class="mdc-notched-outline mdc-notched-outline--no-label">
							<span class="mdc-notched-outline__leading"></span>
							<span class="mdc-notched-outline__notch"></span>
							<span class="mdc-notched-outline__trailing"></span>
						</span>
		<select id="wps_membership_content_restriction" name="wps_membership_content_restriction" class="mdl-textfield__input mdc-text-field__input">
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

	$_pages = get_posts(
		array(
			'post_type' => 'page',
			'post_status' => 'publish',
			'numberposts' => -1,

		)
	);
	$page_id_array = wps_membership_get_meta_data( $value->ID, 'wps_membership_plan_page_target_ids', true );
	foreach ( $_pages as $index => $values ) {
		if ( 'Shop' != $values->post_title ) {
			?>
		<div  class="wps_membership_plan_fields  wps_reg_plan_<?php echo esc_attr( $value->ID ); ?>">
			<div class="wps-form-group wps-membership__plan--pro-disabled ">
				<div class="wps-form-group__label">
					<label  class="wps-form-label">
					<?php
					echo esc_html( $values->post_title );
					esc_html_e( ' for ', 'membership-for-woocommerce' );
					?>
					<span style="color:red"><?php echo esc_html( $value->post_title ); ?></span></label>
				</div>
				<div class="wps-form-group__control wps-pl-4">
					<div class="mdc-form-field">
						<div class="mdc-checkbox">
							<input 
							type="checkbox"
							class="mdc-checkbox__native-control wps_membership_checkbox" name="wps_membership_pages_<?php echo esc_attr( $value->ID ); ?>_<?php echo esc_attr( $values->ID ); ?>"id="wps_membership_pages_<?php echo esc_attr( $value->ID ); ?>_<?php echo esc_attr( $values->ID ); ?>"
							<?php
							if ( ! empty( $page_id_array ) && is_array( $page_id_array ) ) {

								if ( in_array( $values->ID, $page_id_array ) ) {
									echo esc_attr( 'checked' );
									?>
										 value="<?php echo esc_attr( 'on' ); ?>"
										<?php
								}
							}
							?>
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
		</div>
			
			<?php
		}
	}
}
?>

<?php $nonce = wp_create_nonce( 'wps-form-nonce' ); ?>
		<input type="hidden" name="wps_nonce_name" value="<?php echo esc_attr( $nonce ); ?>" />

			<div class="wps-form-group">
				<div class="wps-form-group__control">
					<button id="wps_membership_content_restriction_button" name="wps_membership_content_restriction_button" class="mdc-button mdc-button--raised"><span class="mdc-button__ripple"></span>
					<span class="mdc-button__label"><?php esc_html_e( 'Save', 'membership-for-woocommerce' ); ?></span>
					</button>
				</div>
			</div>

	</div>
</form>
