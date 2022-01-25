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

global $mfw_mwb_mfw_obj;
$mfw_onboarding_form_fields =
// desc - filter for trial.
apply_filters( 'mwb_mfw_on_boarding_form_fields', array() );
?>

<?php if ( ! empty( $mfw_onboarding_form_fields ) ) : ?>
	<div class="mdc-dialog mdc-dialog--scrollable <? echo 
	//desc - filter for trial.
	apply_filters('mwb_stand_dialog_classes', 'membership-for-woocommerce' )?>">
		<div class="mwb-mfw-on-boarding-wrapper-background mdc-dialog__container">
			<div class="mwb-mfw-on-boarding-wrapper mdc-dialog__surface" role="alertdialog" aria-modal="true" aria-labelledby="my-dialog-title" aria-describedby="my-dialog-content">
				<div class="mdc-dialog__content">
					<div class="mwb-mfw-on-boarding-close-btn">
						<a href="#"><span class="mfw-close-form material-icons mwb-mfw-close-icon mdc-dialog__button" data-mdc-dialog-action="close">clear</span></a>
					</div>
					<h3 class="mwb-mfw-on-boarding-heading mdc-dialog__title"><?php esc_html_e( 'Welcome to WP Swings', 'membership-for-woocommerce' ); ?> </h3>
					<p class="mwb-mfw-on-boarding-desc"><?php esc_html_e( 'We love making new friends! Subscribe below and we promise to keep you up-to-date with our latest new plugins, updates, awesome deals and a few special offers.', 'membership-for-woocommerce' ); ?></p>

					<form action="#" method="post" class="mwb-mfw-on-boarding-form">
						<?php
						$mfw_onboarding_html = $mfw_mwb_mfw_obj->mwb_mfw_plug_generate_html( $mfw_onboarding_form_fields );
						echo esc_html( $mfw_onboarding_html );
						?>
						<div class="mwb-mfw-on-boarding-form-btn__wrapper mdc-dialog__actions">
							<div class="mwb-mfw-on-boarding-form-submit mwb-mfw-on-boarding-form-verify ">
								<input type="submit" class="mwb-mfw-on-boarding-submit mwb-on-boarding-verify mdc-button mdc-button--raised" value="Send Us">
							</div>
							<div class="mwb-mfw-on-boarding-form-no_thanks">
								<a href="#" class="mwb-mfw-on-boarding-no_thanks mdc-button" data-mdc-dialog-action="discard"><?php esc_html_e( 'Skip For Now', 'membership-for-woocommerce' ); ?></a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="mdc-dialog__scrim"></div>
	</div>
<?php endif; ?>
