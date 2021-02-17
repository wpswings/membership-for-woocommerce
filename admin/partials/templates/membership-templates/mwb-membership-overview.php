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

?>

<!-- Heading start -->
<div class="mwb_membership_Overview">
	<h2><?php esc_html_e( 'Membership Overview', 'membership-for-woocommerce' ); ?></h2>
</div>
<!-- Heading end. -->

<?php
function create_pdf( $html ) {
	require_once MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH . 'resources/tcpdf_min/tcpdf.php';
	$pdf = new TCPDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
	$pdf->SetMargins( -1, 0, -1 );
	$pdf->setPrintHeader( false );
	$pdf->setPrintFooter( false );
	$pdf->SetFont( 'times', '', 12, '', false );
	$pdf->SetAutoPageBreak( true, PDF_MARGIN_BOTTOM );
	// $pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );
	// $pdf->setFontSubsetting( true );
	$pdf->AddPage();
	$pdf->writeHTMLCell( 0, 0, '', '', $html, 0, 0, 0, true, '', true );
	$pdf->lastPage();
	ob_end_clean();
	$pdf->Output( 'mydoc.pdf', 'I' );
}
//if ( isset( $_POST['createpdf'] ) ) {
	
	// custom header html.
	$html = '
		<style>
			table, tr, td {
			padding: 15px;
			}
		</style>
		<table style="background-color: #222222; color: #fff">
			<tbody>
			<tr>
				<td><h1>Memberhsip Invoice<strong> #123</strong></h1></td>
				<td align="right"><br/>
					Makewebbetter<br/>
					<br/>
				<strong>9911906197</strong> | <strong>brijmohanpandey@cedcoss.com </strong>
				</td>
			</tr>
		</tbody>
		</table>';
	// custom body for pdf.
	$html .= '
		<table>
			<tbody>
				<tr>
					<td><b>Invoice to</b><br/>
					<strong> brij</strong>
					<br/>
					dkjbsdfdsf
					<br/>
					9911916197
					<br/>
					brijmohan@gmail.com
					</td>
					<td align="right">
						<strong>status: Paid</strong><br/>
						Invoice Date: ' . date('d-m-Y') . '
					</td>
				</tr>
			</tbody>
		</table>';
	// custom product listing table.
	$html .= '
	<table>
		<thead>
			<tr style="font-weight:bold;">
				<th>Item name</th>
				<th>Price</th>
				<th>Quantity</th>
				<th>Total (Rs.) </th>
			</tr>
		</thead>
	<tbody>';
	$html .=	'<tr>
				<td>Product name</td>
				<td>50</td>
				<td>1</td>
				<td>50</td>
			</tr>
			';
	// price total and terms and conditions.
	$html .= '
	<tr align="right">
		<td colspan="4" style="border-top: 1px solid #222"><strong>Grand total: (Rs.) 50</strong></td>
		</tr>
		<tr>
		<td colspan="4">
			<h2>Thank you for your business.</h2><br/>
			<strong>Terms and conditions:<br/></strong>
			terms and conditions
		</td>
	</tr>
	</tbody>
	</table>';
	create_pdf( $html );
//}
?>
