<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Makewebbetter_Onboarding
 * @subpackage Makewebbetter_Onboarding/admin/onboarding
 */

global $pagenow, $mfw_mwb_mfw_obj;
if ( empty( $pagenow ) || 'plugins.php' != $pagenow ) {
	return false;
}

$mfw_onboarding_form_deactivate = apply_filters( 'mwb_mfw_deactivation_form_fields', array() );
?>
<?php if ( ! empty( $mfw_onboarding_form_deactivate ) ) : ?>
	<div class="mdc-dialog mdc-dialog--scrollable">
		<div class="mwb-mfw-on-boarding-wrapper-background mdc-dialog__container">
			<div class="mwb-mfw-on-boarding-wrapper mdc-dialog__surface" role="alertdialog" aria-modal="true" aria-labelledby="my-dialog-title" aria-describedby="my-dialog-content">
				<div class="mdc-dialog__content">
					<div class="mwb-mfw-on-boarding-close-btn">
						<a href="#">
							<span class="mfw-close-form material-icons mwb-mfw-close-icon mdc-dialog__button" data-mdc-dialog-action="close">clear</span>
						</a>
					</div>

					<h3 class="mwb-mfw-on-boarding-heading mdc-dialog__title"></h3>
					<p class="mwb-mfw-on-boarding-desc"><?php esc_html_e( 'May we have a little info about why you are deactivating?', 'membership-for-woocommerce' ); ?></p>
					<form action="#" method="post" class="mwb-mfw-on-boarding-form">
						<?php 
						$mfw_onboarding_deactive_html = $mfw_mwb_mfw_obj->mwb_mfw_plug_generate_html( $mfw_onboarding_form_deactivate );
						echo esc_html( $mfw_onboarding_deactive_html );
						?>
						<div class="mwb-mfw-on-boarding-form-btn__wrapper mdc-dialog__actions">
							<div class="mwb-mfw-on-boarding-form-submit mwb-mfw-on-boarding-form-verify ">
								<input type="submit" class="mwb-mfw-on-boarding-submit mwb-on-boarding-verify mdc-button mdc-button--raised" value="Send Us">
							</div>
							<div class="mwb-mfw-on-boarding-form-no_thanks">
								<a href="#" class="mwb-deactivation-no_thanks mdc-button"><?php esc_html_e( 'Skip and Deactivate Now', 'membership-for-woocommerce' ); ?></a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="mdc-dialog__scrim"></div>
	</div>
<?php endif; ?>
