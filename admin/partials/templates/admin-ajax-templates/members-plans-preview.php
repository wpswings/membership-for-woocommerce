<?php
/**
 * Members plans preview template.
 *
 * @link       https://wpswings.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin/partials
 */

// Exit is accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

$output = '';
$order_val = new WC_Order( wps_membership_get_meta_data( $member_id, 'member_order_id', true ) );
$payment = $order_val->get_payment_method_title();
$output .= '<div class="members_preview_content">';

if ( ! empty( $member_id ) ) {

	$billing_info = wps_membership_get_meta_data( $member_id, 'billing_details', true );
	$plan_info    = wps_membership_get_meta_data( $member_id, 'plan_obj', true );
	$plan_status  = wps_membership_get_meta_data( $member_id, 'member_status', true );
	$post_title = '';
	$wps_membership_plan_price = '';
	$post_content = '';
	if ( ! empty( $plan_info ) ) {
		$post_title = $plan_info['post_title'];
		$wps_membership_plan_price = $plan_info['wps_membership_plan_price'];
		$post_content = $plan_info['post_content'];
	}

	$output .= '<div class="members_billing_preview_wrapper">
				<div class="members_billing_preview">
					<h2>' . esc_html__( 'Billing details', 'membership-for-woocommerce' ) . '</h2>';

	if ( ! empty( $billing_info['membership_billing_first_name'] ) ) {
		$output .= esc_html( $billing_info['membership_billing_company'] ) . '
					' . sprintf( ' %s %s ', esc_html( $billing_info['membership_billing_first_name'] ), esc_html( $billing_info['membership_billing_last_name'] ) ) . '
					' . sprintf( ' %s %s ', esc_html( $billing_info['membership_billing_address_1'] ), esc_html( $billing_info['membership_billing_address_2'] ) ) . '
					' . sprintf( ' %s %s ', esc_html( $billing_info['membership_billing_city'] ), esc_html( $billing_info['membership_billing_postcode'] ) ) . '
					' . sprintf( ' %s, %s ', esc_html( $billing_info['membership_billing_state'] ), esc_html( $billing_info['membership_billing_country'] ) ) . '</br>';
	} else {
		$output .= esc_html__( 'No billing details', 'membership-for-woocommerce' ) . '</br>';
	}

	$output .= '<strong>' . esc_html__( 'Email address :', 'membership-for-woocommerce' ) . '</strong>
				<a href="mailto:' . esc_html( $billing_info['membership_billing_email'] ) . '">' . esc_html( $billing_info['membership_billing_email'] ) . '</a></br>
				<strong>' . esc_html__( 'Phone :', 'membership-for-woocommerce' ) . '</strong>
				' . esc_html( $billing_info['membership_billing_phone'] ) . '</br>
				<strong>' . esc_html__( 'Payment Method', 'membership-for-woocommerce' ) . '</strong>
				' . esc_html( $payment );

	$output .= '</div>';

	$output .= '<div class="members_plan_preview" >
					<h2>' . esc_html__( 'Plan details', 'membership-for-woocommerce' ) . '</h2>
					' . esc_html( $post_title ) . '
					' . sprintf( ' %s %s ', esc_html( get_woocommerce_currency_symbol() ), esc_html( $wps_membership_plan_price ) ) . '
					' . wp_kses_post( $post_content ) . '
					<div class="member_plan_status ' . $plan_status . '">' . esc_html( $plan_status ) . '</div>';

	$output .= '</div>
				</div>';

	$output .= '<div class="members_plan_preview_table_wrapper">
					<table class="plan_preview_table">
						<thead>
							<tr>
								<th class="plan_preview_table_product">' . esc_html__( 'Product', 'membership-for-woocommerce' ) . '</th>
								<th class="plan_preview_table_price">' . esc_html__( 'Total', 'membership-for-woocommerce' ) . '</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>' . esc_html( $post_title ) . '</td>
								<td>' . sprintf( ' %s %s ', esc_html( get_woocommerce_currency_symbol() ), esc_html( $wps_membership_plan_price ) ) . '</td>
							</tr>
						</tbody>
					</table>';

	$output .= '<div class="edit_members_action">
					<a class="button button-primary button-large" href="' . admin_url( 'post.php?action=edit&post=' . $member_id ) . '">' . esc_html__( 'Edit', 'membership-for-woocommerce' ) . '</a>
				</div>';

	$output .= '</div>';
}

$output .= '</div>';

echo wp_kses_post( wpautop( wptexturize( $output ) ) . PHP_EOL );
