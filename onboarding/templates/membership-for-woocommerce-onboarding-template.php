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

global $mfw_wps_mfw_obj;
  $mfw_onboarding_form_fields =
  apply_filters( 'wps_mfw_on_boarding_form_fields',
  array() );
  ?>

  <?php if ( ! empty( $mfw_onboarding_form_fields ) ) : ?>
        <div class="wps-mfw-onboarding-overlay">
                <div class="wps-mfw-onboarding-card">
                        <div class="wps-mfw-onboarding-head">
                                <h3><?php esc_html_e( 'Welcome to WP Swings', 'membership-
  for-woocommerce' ); ?></h3>
                                <button class="wps-mfw-onboarding-close" aria-label="<?php
  esc_attr_e( 'Close onboarding', 'membership-for-
  woocommerce' ); ?>">
                                        <span class="material-icons">close</span>
                                </button>
                        </div>

                        <p class="wps-mfw-onboarding-intro"><?php esc_html_e( 'We
  love making new friends! Subscribe below and we promise to
  keep you up-to-date with our latest new plugins, updates,
  awesome deals and a few special offers.', 'membership-for-
  woocommerce' ); ?></p>

                        <form class="wps-mfw-onboarding-form" action="#"
  method="post">
                                <?php
                                $mfw_onboarding_html =
  $mfw_wps_mfw_obj->wps_mfw_plug_generate_html( $mfw_onboarding_form_fields );
                                echo wp_kses_post( $mfw_onboarding_html );
                                ?>
                                <div class="wps-mfw-onboarding-actions">
                                        <input type="submit" class="wps-mfw-onboarding-submit"
  value="<?php esc_attr_e( 'Send Us', 'membership-for-
  woocommerce' ); ?>">
                                        <a class="wps-mfw-onboarding-skip" href="#" data-mdc-
  dialog-action="discard"><?php esc_html_e( 'Skip For Now',
  'membership-for-woocommerce' ); ?></a>
                                </div>
                        </form>
                </div>
        </div>
  <?php endif; ?>


