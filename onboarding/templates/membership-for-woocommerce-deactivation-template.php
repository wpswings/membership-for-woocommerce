<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wpswings.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/onboarding
 */

global $pagenow, $mfw_wps_mfw_obj;
if ( empty( $pagenow ) || 'plugins.php' != $pagenow ) {
	return false;
}
$wps_plugin_name                = ! empty( explode( '/', plugin_basename( __FILE__ ) ) ) ? explode( '/', plugin_basename( __FILE__ ) )[0] : '';
$wps_plugin_deactivation_id     = $wps_plugin_name . '-no_thanks_deactive';
$wps_plugin_onboarding_popup_id = $wps_plugin_name . '-onboarding_popup';
$mfw_onboarding_form_deactivate =

/**
 * Filter for form field.
 *
 * @since 1.0.0
 */
apply_filters( 'wps_mfw_deactivation_form_fields', array() );

?>
<?php if ( ! empty( $mfw_onboarding_form_deactivate ) ) : ?>
	<div id="<?php echo esc_attr( $wps_plugin_onboarding_popup_id ); ?>" class="mdc-dialog mdc-dialog--scrollable <? echo 
	
	apply_filters('wps_stand_dialog_classes', 'membership-for-woocommerce' )?>">
		<div class="wps-mfw-on-boarding-wrapper-background mdc-dialog__container">
			<div class="wps-mfw-on-boarding-wrapper mdc-dialog__surface" role="alertdialog" aria-modal="true" aria-labelledby="my-dialog-title" aria-describedby="my-dialog-content">
				<div class="mdc-dialog__content">
					<div class="wps-mfw-on-boarding-close-btn">
						<a href="#">
							<span class="mfw-close-form material-icons wps-mfw-close-icon mdc-dialog__button" data-mdc-dialog-action="close">clear</span>
						</a>
					</div>

					<h3 class="wps-mfw-on-boarding-heading mdc-dialog__title"></h3>
					<p class="wps-mfw-on-boarding-desc"><?php esc_html_e( 'May we have a little info about why you are deactivating?', 'membership-for-woocommerce' ); ?></p>
					<form action="#" method="post" class="wps-mfw-on-boarding-form">
						<?php
						$mfw_onboarding_deactive_html = $mfw_wps_mfw_obj->wps_mfw_plug_generate_html( $mfw_onboarding_form_deactivate );
						echo esc_html( $mfw_onboarding_deactive_html );
						?>
						<div class="wps-mfw-on-boarding-form-btn__wrapper mdc-dialog__actions">
							<div class="wps-mfw-on-boarding-form-submit wps-mfw-on-boarding-form-verify ">
								<input type="submit" class="wps-mfw-on-boarding-submit wps-on-boarding-verify mdc-button mdc-button--raised" value="Send Us">
							</div>
							<div class="wps-mfw-on-boarding-form-no_thanks">
								<a href="#" id="<?php echo esc_attr( $wps_plugin_deactivation_id ); ?>" class="<? echo 
								//desc - filter for trial.
								apply_filters('wps_stand_no_thank_classes', 'membership-for-woocommerce-no_thanks' )?> mdc-button"><?php esc_html_e( 'Skip and Deactivate Now', 'membership-for-woocommerce' ); ?></a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="mdc-dialog__scrim"></div>
	</div>
<?php endif; ?>
