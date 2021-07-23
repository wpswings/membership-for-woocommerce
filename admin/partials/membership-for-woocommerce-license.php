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

?>
<div class="mwb-mfw-wrap">
<h2><?php esc_html_e( 'Your License', 'membership-for-woocommerce' ); ?></h2>
<div class="mwb_mfw_license_text">
	<p>
	<?php
	esc_html_e( 'This is the License Activation Panel. After purchasing extension from MakeWebBetter you will get the purchase code of this extension. Please verify your purchase below so that you can use feature of this plugin.', 'membership-for-woocommerce' );
	?>
	</p>
	<form id="mwb_mfw_license_form"> 
		<table class="mwb-mfw-form-table">
			<tr>
			<th scope="row"><label for="puchase-code"><?php esc_html_e( 'Purchase Code : ', 'membership-for-woocommerce' ); ?></label></th>
			<td>
				<input type="text" id="mwb_mfw_license_key" name="purchase-code" required="" size="30" class="mwb-mfw-purchase-code" value="" placeholder="<?php esc_html_e( 'Enter your code here...', 'membership-for-woocommerce' ); ?>">

			</td>
			</tr>
		</table>
		<p id="mwb_mfw_license_activation_status"></p>
		<p class="submit">
		<button id="mwb_mfw_license_activate" required="" class="button-primary woocommerce-save-button" name="mwb_mfw_license_settings"><?php esc_html_e( 'Validate', 'membership-for-woocommerce' ); ?></button>
		</p>
	</form>
</div>
</div>
