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

require_once MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH . 'resources/tcpdf_min/tcpdf.php';

//var_dump( class_exists( 'TCPDF' ) );
if( ! class_exists( 'TCPDF' ) ) {
	return;
}
$member_id = 503;
$plan_info = get_post_meta( $member_id, 'plan_obj', true );
$billing = get_post_meta( $member_id, 'billing_details', true );

$first_name = ! empty( $billing['membership_billing_first_name'] ) ? $billing['membership_billing_first_name'] : '';
$last_name  = ! empty( $billing['membership_billing_last_name'] ) ? $billing['membership_billing_last_name'] : '';
$company    = ! empty( $billing['membership_billing_company'] ) ? $billing['membership_billing_company'] : '';
$address_1  = ! empty( $billing['membership_billing_address_1'] ) ? $billing['membership_billing_address_1'] : '';
$address_2  = ! empty( $billing['membership_billing_address_2'] ) ? $billing['membership_billing_address_2'] : '';
$city       = ! empty( $billing['membership_billing_city'] ) ? $billing['membership_billing_city'] : '';
$postcode   = ! empty( $billing['membership_billing_postcode'] ) ? $billing['membership_billing_postcode'] : '';
$state      = ! empty( $billing['membership_billing_state'] ) ? $billing['membership_billing_state'] : '';
$country    = ! empty( $billing['membership_billing_country'] ) ? $billing['membership_billing_country'] : '';
$email      = ! empty( $billing['membership_billing_email'] ) ? $billing['membership_billing_email'] : '';
$phone      = ! empty( $billing['membership_billing_phone'] ) ? $billing['membership_billing_phone'] : '';

ob_start();
			?>

			<div class="membership_invoice_wrapper">
				<div class="invoice_info">
					<p>
						<strong><?php esc_html_e( 'Invoice no. :', 'membership-for-woccommerce' ); ?></strong><?php echo esc_html( '#INV' . $member_id ); ?></br>
						<strong><?php esc_html_e( 'Invoice date :', 'membership-for-woccommerce' ); ?></strong><?php echo esc_html( current_time( 'Y-m-d' ) ); ?>
					</p>
				</div>

				<div class="invoice_billing">
					<h3><?php esc_html_e( 'Bill to', 'membership-for-woocommerce' ); ?></h3>
					<p>
						<?php echo sprintf( ' %s %s ', esc_html( $first_name ), esc_html( $last_name ) ); ?></br>
						<?php echo esc_html( $company ); ?></br>
						<?php echo sprintf( ' %s %s ', esc_html( $address_1 ), esc_html( $address_2 ) ); ?></br>
						<?php echo sprintf( ' %s %s ', esc_html( $city ), esc_html( $postcode ) ); ?></br>
						<?php echo sprintf( ' %s, %s ', esc_html( $state ), esc_html( $country ) ); ?></br>
						<?php echo esc_html( $phone ); ?></br>
						<?php echo esc_html( $email ); ?></br>
					</p>
				</div>

				<div class="membership_inv_table_wrapper">
					<table class="membership_inv_table">
						<thead>
							<tr>
								<th class="inv_table_slno"><?php echo esc_html__( 'SNo.', 'membership-for-woocommerce' ); ?></th>
								<th class="inv_table_product"><?php echo esc_html__( 'Product', 'membership-for-woocommerce' ); ?></th>
								<th class="inv_table_total"><?php echo esc_html__( 'Amount', 'membership-for-woocommerce' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php esc_html( '1.' ); ?></td>
								<td><?php esc_html( $plan_info['post_title'] ); ?></td>
								<td><?php echo sprintf( ' %s %s ', esc_html( get_woocommerce_currency() ), esc_html( $plan_info['mwb_membership_plan_price'] ) ); ?></td>
							</tr>
						</tbody>
					</table>
					<p class="membership_inv_total">
						<strong><?php esc_html_e( 'Total : ', 'membership-for-woocommerce' ); ?></strong><?php echo sprintf( ' %s %s ', esc_html( get_woocommerce_currency() ), esc_html( $plan_info['mwb_membership_plan_price'] ) ); ?>
					</p>
				</div>
			</div>
			<footer>
				<div class="membership_inv_footer">
					<p>
						<strong><?php echo esc_html( get_bloginfo( 'name' ) ); ?></strong></br>
						<?php echo esc_html( get_bloginfo( 'description' ) ); ?>
					</p>
				</div>
			</footer>

			<?php

			$content = ob_get_clean();

			$content = iconv( 'UTF-8', 'UTF-8//IGNORE', $content );

$pdf = new TCPDF();

$pdf->SetCreator( PDF_CREATOR );
$pdf->SetAuthor( 'Nicola Asuni' );
$pdf->SetTitle( 'TCPDF Example 006' );
$pdf->SetSubject( 'TCPDF Tutorial' );
$pdf->SetKeywords( 'TCPDF, PDF, example, test, guide' );
// add a page
$pdf->AddPage();

//$pdf// output the HTML content
$pdf->writeHTML( $content, true, false, true, false, '' );

try {

	$path = wp_upload_dir( '', 'membership-for-woo-invoices', true );
	echo '<pre>'; print_r( WP_CONTENT_DIR.'/uploads' ); echo '</pre>';die;
	
$pdf->Output( $path . '/example_006.pdf', 'F');
} catch (Exception $e) {
	echo $e->getMessage();
}

?>

<!-- Heading start -->
<div class="mwb_membership_Overview">
	<h2><?php esc_html_e( 'Membership Overview', 'membership-for-woocommerce' ); ?></h2>
</div>
<!-- Heading end. -->
