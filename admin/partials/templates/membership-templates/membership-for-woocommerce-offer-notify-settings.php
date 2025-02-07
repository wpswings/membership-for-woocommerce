<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for general tab.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

global $mfw_wps_mfw_obj;
$mfw_whatsapp_api_settings_array = apply_filters( 'mfw_whatsapp_api_settings_array', array() );
$args                            = array(
	'post_type'   => 'wps_cpt_membership',
	'post_status' => array( 'publish' ),
	'numberposts' => -1,
);
$existing_plans                  = get_posts( $args );
$log_url                         = site_url() . '/wp-admin/admin.php?page=wc-status&tab=logs';
$log_url                         = '<a href="' . $log_url . '" class="wps_wpr_create_whatsapp_token_link" target="_blank">Click Here</a>';
$wps_wpr_offer_message           = get_option( 'wps_wpr_offer_message' );
?>

<!--  template file for admin settings. -->
<form action="" method="POST" class="wps-mfw-gen-section-form">
	<div class="mfw-secion-wrap">
	<?php
		do_action( 'mfw_whatsapp_api_settings_before' );
	?>
	<div class="wps-sm__modal"></div>
		<?php
		$mfw_general_html = $mfw_wps_mfw_obj->wps_mfw_plug_generate_html( $mfw_whatsapp_api_settings_array );
		echo esc_html( $mfw_general_html );
		wp_nonce_field( 'admin_save_data', 'wps_tabs_nonce' );
		?>
	</div>
</form>

<!--    +++++++++++    whatsapp notification settings   ++++++++++++     -->
<div class="wps_wpr_offer_notify_main_wrappers">
	<h4 class="wps_wpr_offer_notify_settings_heading"><?php esc_html_e( 'Send Offer Notification', 'membership-for-woocommerce' ); ?></h4>
	<form method="POST" action="" class="wps_wpr_offer_form">
		<main class="wps_wpr_main_offer_wrapper">
            <section>
				<article>
					<label for="wps_wpr_enable_offer_settings"><?php esc_html_e( 'Choose Membership', 'membership-for-woocommerce' ); ?></label>
					<div class="wps_wpr_enable_offer_setting_wrapper">
                        <select class="wps_org_offer_plan_id" multiple>
                            <?php
                            if ( ! empty( $existing_plans ) && is_array( $existing_plans ) ) {
                                foreach ( $existing_plans as $all_plans_obj ) {
                                    ?>
                                    <option value="<?php echo esc_html( $all_plans_obj->ID ); ?>"><?php echo esc_html( $all_plans_obj->post_title ); ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
						<span class="wps_wpr_enable_offer_notices wps_wpr_label_notice"><?php esc_html_e( 'Please select the membership plan name to send the offer message to the user based on their membership plan.', 'membership-for-woocommerce' ); ?></span>
					</div>
				</article>
			</section>
            <section>
				<article>
					<label for="wps_wpr_offer_message"><?php esc_html_e( 'Enter Offer Message', 'membership-for-woocommerce' ); ?></label>
					<div class="wps_wpr_enable_offer_setting_wrapper">
						<textarea class="wps_wpr_offer_message" rows="4" cols="40"><?php echo esc_html( $wps_wpr_offer_message ); ?></textarea>
						<span class="wps_wpr_enable_offer_notices wps_wpr_label_notice">
							<!-- translators: %s: whatsapp notify -->
							<?php echo sprintf( esc_html__( 'This offer message will be sent to the user via WhatsApp. You can check the sending status in the log. To check, %s.', 'membership-for-woocommerce' ), wp_kses_post( $log_url ) ); ?>
						</span>
					</div>
				</article>
			</section>
        </main>
        <div class="wps_wpr_whatsapp_wrappers">
            <input type="button" name="wps_wpr_send_on_whatsap_btn" id="wps_wpr_send_on_whatsap_btn" value="<?php esc_html_e( 'Whatsapp', 'membership-for-woocommerce' ); ?>">
            <span style="display: none;" class="wps_wpr_whatsapp_loader"><img src='<?php echo esc_url( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/loader.gif'; ?>' width="50" height="50" /></span>
			<div style="display: none;" class="wps_wpr_offer_msg_notice"></div>
        </div>
    </form>
</div>

<!-- Whatsapp Sample template -->
 <div class="wps_wpr_preview_template_img" style="display: none;">
	<img src='<?php echo esc_url( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/whatspp-template-sample.png'; ?>'>
 </div>
