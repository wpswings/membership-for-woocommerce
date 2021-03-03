<?php

/**
 * Email Template. 
 */

?>
<style>
	table,
	tr {
		padding: 15px;
	}

	th {
		border-bottom: 1px solid #1a3365;
		padding: 5px 0px;
	}
</style>
<div style="max-width:1140px;margin:0px auto;font-family:sans-serif;">
	<table style="width:100%;padding:0;color: #fff;">
		<tbody style="background-color: #1a3365;">
			<tr style="display: flex;flex-wrap: wrap;justify-content: center;text-align: center;">
				<td style="width:100%;">
					<h1 style="display:flex;flex-wrap:wrap;justify-content: center;margin:15px 0px 0px;">
						<?php esc_html_e( 'Membership Invoice #', 'membership-for-woocommerce' ); ?><strong
							style="width:100%;padding:15px 0;"><?php echo esc_html( $member_id ); ?></strong>
					</h1>
				</td>
				<td style="max-width:768px;padding:10px;width:100%;line-height: 180%;"><img
						src="<?php echo esc_html( ! empty( $mwb_membership_global_settings['mwb_membership_invoice_logo'] ) ? $mwb_membership_global_settings['mwb_membership_invoice_logo'] : '' ); ?>"
						height="50px" /><br />
					<?php echo esc_html( get_bloginfo( 'name' ) ); ?><br />
					<?php echo esc_html( ! empty( $mwb_membership_global_settings['mwb_membership_invoice_address'] ) ? $mwb_membership_global_settings['mwb_membership_invoice_address'] : $store_details ); ?><br />
					<br />
					<strong><?php echo esc_html( ! empty( $mwb_membership_global_settings['mwb_membership_invoice_phone'] ) ? $mwb_membership_global_settings['mwb_membership_invoice_phone'] : '' ); ?></strong>
					|
					<strong><?php echo esc_html( ! empty( $mwb_membership_global_settings['mwb_membership_invoice_email'] ) ? $mwb_membership_global_settings['mwb_membership_invoice_email'] : get_option( 'woocommerce_email_from_address' ) ); ?></strong>
				</td>
			</tr>
		</tbody>
	</table>

	<table style="width:100%; padding:0;">
		<tbody>
			<tr style="display: flex;flex-wrap: wrap;justify-content: space-between;">
				<td style="flex-grow: 1;line-height:150%;">
					<b><?php esc_html_e( 'Invoice to : ', 'membership-for-woocommerce' ); ?></b><br />
					<strong><?php echo esc_html( $first_name . $last_name ); ?></strong>
					<br />
					<?php echo esc_html( $company ); ?><br />
					<?php echo sprintf( ' %s %s ', esc_html( $address_1 ), esc_html( $address_2 ) ); ?><br />
					<?php echo sprintf( ' %s %s ', esc_html( $city ), esc_html( $postcode ) ); ?><br />
					<?php echo sprintf( ' %s, %s ', esc_html( $state ), esc_html( $country ) ); ?>
					<br />
					<?php echo esc_html( $phone ); ?>
					<br />
					<?php echo esc_html( $email ); ?>
				</td>
				<td style="display: flex;flex-direction: column;text-align: right;padding:15px 0;flex-grow: 1;">
					<strong><?php echo sprintf( ' %s %s ', esc_html__( 'Status : ', 'membership-for-woocommerce' ), esc_html( $status ) ); ?></strong><br />
					<?php echo sprintf( ' %s %s ', esc_html__( 'Invoice Date : ', 'membership-for-woocommerce' ), esc_html( gmdate( 'd-m-Y' ) ) ); ?>
				</td>
			</tr>
		</tbody>
	</table>

	<div style="overflow-x: auto;border:15px solid transparent;">
		<table style="width:100%;line-height: 200%;text-align: center;">
			<thead>
				<tr style="font-weight:bold;">
					<th style="text-align: left;"><?php esc_html_e( 'Item name', 'membership-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Price', 'membership-for-woocommerce' ); ?> </th>
					<th><?php esc_html_e( 'Quantity', 'membership-for-woocommerce' ); ?></th>
					<th style="text-align: right;"><?php esc_html_e( 'Total', 'membership-for-woocommerce' ); ?></th>
				</tr>
			</thead>

			<tbody>
				<tr>
					<td style="text-align: left;">
						<?php echo esc_html( ! empty( $plan_info['post_title'] ) ? $plan_info['post_title'] : '' ); ?>
					</td>
					<td><?php echo esc_html( ! empty( $plan_info['mwb_membership_plan_price'] ) ? get_woocommerce_currency() . ' ' . $plan_info['mwb_membership_plan_price'] : '' ); ?>
					</td>
					<td><?php esc_html_e( '1' ); ?></td>
					<td style="text-align: right;">
						<?php echo esc_html( ! empty( $plan_info['mwb_membership_plan_price'] ) ? get_woocommerce_currency() . ' ' . $plan_info['mwb_membership_plan_price'] : '' ); ?>
					</td>
				</tr>

				<tr align="right">
					<td colspan="4" style="border-top: 1px solid #1a3365;padding:10px 0;">
						<strong><?php echo sprintf( ' %s %s ', esc_html_e( 'Grand total : ', 'membershrip-for-woocommerce' ), esc_html( get_woocommerce_currency() . ' ' . $plan_info['mwb_membership_plan_price'] ) ); ?></strong>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<table style="text-align:center;width:100%;background:#F37E21;color:#ffffff">
		<tbody>
			<tr>
				<td colspan="4" style="line-height: 150%;">
					<h2><?php esc_html_e( 'Thank you for shopping with us', 'membership-for-woocommerce' ); ?></h2>
					<br />
					<strong><?php echo esc_html( get_bloginfo( 'name' ) ); ?><br /></strong>
					<?php echo esc_html( get_bloginfo( 'description' ) ); ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
