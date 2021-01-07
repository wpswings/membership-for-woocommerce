<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin/partials
 */

// Exit is accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}
//echo '<pre>'; print_r( get_post_meta( 503, 'billing_details', true ) ); echo '</pre>';
///echo '<pre>'; print_r( get_post_meta( 503, 'plan_obj', true ) ); echo '</pre>';
// echo '<pre>'; print_r( MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH . 'includes/mpdf_lib/vendor/autoload.php' ); echo '</pre>';


// require_once MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH . 'includes/mpdf_lib/vendor/autoload.php';

// $mpdf = new \mPDF();

// // Write some HTML code:.
// $mpdf->WriteHTML( '<h1>Hello World</h1><br><p>My first PDF with mPDF</p>' );

// // Output a PDF file directly to the browser.
// $mpdf->Output();
?>

<!-- Heading start -->
<div class="mwb_membership_Overview">
	<h2><?php esc_html_e( 'Membership Overview', 'membership-for-woocommerce' ); ?></h2>
</div>
<!-- Heading end. -->
