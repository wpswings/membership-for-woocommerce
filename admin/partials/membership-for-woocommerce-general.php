<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for general tab.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $mfw_mwb_mfw_obj;
$mfw_genaral_settings =
// desc - filter for trial.
apply_filters( 'mfw_general_settings_array', array() );
?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="mwb-mfw-gen-section-form">
	<div class="mfw-secion-wrap">
<?php
$value = false;
if ( class_exists( 'Invoice_System_For_Woocommerce_Common' ) ) {
	$value = true;
}
if ( $value == false ) {


	?>
	<div class="mwb-c-modal">
		<div class="mwb-c-modal__cover"></div>
		<div class="mwb-c-modal__message">
			<span class="mwb-c-modal__close">+</span>
			<div class="mwb-c-modal__content">
				<span class="mwb-c-modal__content-text"> <?php esc_html_e( 'To use this feature please install Invoice Plugin', 'membership-for-woocommerce' ); ?> <a href="https://wordpress.org/plugins/invoice-system-for-woocommerce/"> <?php esc_html_e( 'click here', 'membership-for-woocommerce' ); ?> </a>   </span>
			</div>
			<div class="mwb-c-modal__confirm">
			<span class="mwb-c-modal__confirm-button mwb-c-modal__yes">Close</span>
			</div>
		</div>
	</div>
	<?php
}

?>

	<div class="mwb-sm__modal"></div>
		<?php
		$mfw_general_html = $mfw_mwb_mfw_obj->mwb_mfw_plug_generate_html( $mfw_genaral_settings );
		echo esc_html( $mfw_general_html );
		wp_nonce_field( 'admin_save_data', 'mwb_tabs_nonce' );
		?>
	</div>
</form>
