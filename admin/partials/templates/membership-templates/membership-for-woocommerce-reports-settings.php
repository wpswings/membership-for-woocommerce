<?php
/**
 * Membership analytics report template.
 *
 * @package Membership_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$global_functions = Membership_For_Woocommerce_Global_Functions::get();
$report           = $global_functions->wps_mfw_build_membership_report_data();

$summary_cards = array(
	array(
		'label' => esc_html__( 'Membership Plans', 'membership-for-woocommerce' ),
		'value' => (int) $report['total_membership_plans'],
	),
	array(
		'label' => esc_html__( 'Total Members', 'membership-for-woocommerce' ),
		'value' => (int) $report['total_members'],
	),
	array(
		'label' => esc_html__( 'Active Members', 'membership-for-woocommerce' ),
		'value' => (int) $report['complete'],
	),
	array(
		'label' => esc_html__( 'Paid Membership Purchases', 'membership-for-woocommerce' ),
		'value' => (int) $report['paid_membership_orders'],
	),
	array(
		'label' => esc_html__( 'Membership Revenue', 'membership-for-woocommerce' ),
		'value' => wc_price( (float) $report['membership_revenue'] ),
	),
	array(
		'label' => esc_html__( 'Average Order Value', 'membership-for-woocommerce' ),
		'value' => wc_price( (float) $report['average_order_value'] ),
	),
	array(
		'label' => esc_html__( 'Cancelled Members', 'membership-for-woocommerce' ),
		'value' => (int) $report['cancelled'],
	),
	array(
		'label' => esc_html__( 'Expired Members', 'membership-for-woocommerce' ),
		'value' => (int) $report['expired'],
	),
);

$status_rows = array(
	array(
		'label' => esc_html__( 'Active', 'membership-for-woocommerce' ),
		'value' => (int) $report['complete'],
	),
	array(
		'label' => esc_html__( 'Pending', 'membership-for-woocommerce' ),
		'value' => (int) $report['pending'],
	),
	array(
		'label' => esc_html__( 'Expired', 'membership-for-woocommerce' ),
		'value' => (int) $report['expired'],
	),
	array(
		'label' => esc_html__( 'Cancelled', 'membership-for-woocommerce' ),
		'value' => (int) $report['cancelled'],
	),
	array(
		'label' => esc_html__( 'Paused', 'membership-for-woocommerce' ),
		'value' => (int) $report['paused'],
	),
	array(
		'label' => esc_html__( 'On Hold', 'membership-for-woocommerce' ),
		'value' => (int) $report['hold'],
	),
);

$activity_rows = array(
	array(
		'label' => esc_html__( 'Today', 'membership-for-woocommerce' ),
		'value' => (int) $report['activity']['today'],
	),
	array(
		'label' => esc_html__( 'Yesterday', 'membership-for-woocommerce' ),
		'value' => (int) $report['activity']['yesterday'],
	),
	array(
		'label' => esc_html__( 'Last 7 Days', 'membership-for-woocommerce' ),
		'value' => (int) $report['activity']['last_7_days'],
	),
	array(
		'label' => esc_html__( 'This Month', 'membership-for-woocommerce' ),
		'value' => (int) $report['activity']['this_month'],
	),
	array(
		'label' => esc_html__( 'Last Month', 'membership-for-woocommerce' ),
		'value' => (int) $report['activity']['last_month'],
	),
	array(
		'label' => esc_html__( 'This Year', 'membership-for-woocommerce' ),
		'value' => (int) $report['activity']['this_year'],
	),
	array(
		'label' => esc_html__( 'Last Year', 'membership-for-woocommerce' ),
		'value' => (int) $report['activity']['last_year'],
	),
);

$top_memberships         = array_slice( $report['top_memberships'], 0, 8 );
$top_discount_products   = array_slice( $report['top_discount_products'], 0, 6 );
$top_discount_categories = array_slice( $report['top_discount_categories'], 0, 6 );
$top_discount_tags       = array_slice( $report['top_discount_tags'], 0, 6 );
$payment_methods         = array_slice( $report['payment_methods'], 0, 6 );
$recent_memberships      = $report['recent_memberships'];
$generated_at            = function_exists( 'wp_date' ) ? wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) : date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), current_time( 'timestamp' ) );
?>

<div class="mfw-report-dashboard">
	<div class="mfw-report-hero">
		<div>
			<span class="mfw-report-hero__eyebrow"><?php esc_html_e( 'Backend Analytics', 'membership-for-woocommerce' ); ?></span>
			<h2 class="mfw-report-hero__title"><?php esc_html_e( 'Membership performance at a glance', 'membership-for-woocommerce' ); ?></h2>
			<p class="mfw-report-hero__desc"><?php esc_html_e( 'Track which memberships sell the most, which discount targets get the most exposure from purchased plans, and how membership revenue is moving across your store.', 'membership-for-woocommerce' ); ?></p>
		</div>
		<div class="mfw-report-hero__actions">
			<div class="mfw-report-hero__meta">
				<span><?php echo esc_html( sprintf( __( 'Generated: %s', 'membership-for-woocommerce' ), $generated_at ) ); ?></span>
			</div>
			<form method="post" class="wps_msfw_membership_report_wrap mfw-report-export">
				<?php wp_nonce_field( 'wps_mfw_export_csv', 'wps_mfw_export_csv_nonce' ); ?>
				<input type="hidden" name="wps_mfw_export_csv" value="1">
				<button class="button mfw-report-export__button">
					<?php esc_html_e( 'Export Membership Report', 'membership-for-woocommerce' ); ?>
				</button>
			</form>
		</div>
	</div>

	<div class="mfw-report-summary-grid">
		<?php foreach ( $summary_cards as $card ) : ?>
			<div class="mfw-report-card mfw-report-card--stat">
				<span class="mfw-report-card__label"><?php echo esc_html( $card['label'] ); ?></span>
				<strong class="mfw-report-card__value"><?php echo wp_kses_post( $card['value'] ); ?></strong>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="mfw-report-grid mfw-report-grid--two">
		<div class="mfw-report-card">
			<div class="mfw-report-card__head">
				<h3><?php esc_html_e( 'Membership Status Mix', 'membership-for-woocommerce' ); ?></h3>
			</div>
			<div class="mfw-report-list">
				<?php foreach ( $status_rows as $row ) : ?>
					<div class="mfw-report-list__row">
						<span><?php echo esc_html( $row['label'] ); ?></span>
						<strong><?php echo esc_html( $row['value'] ); ?></strong>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="mfw-report-card">
			<div class="mfw-report-card__head">
				<h3><?php esc_html_e( 'Purchase Activity', 'membership-for-woocommerce' ); ?></h3>
			</div>
			<div class="mfw-report-list">
				<?php foreach ( $activity_rows as $row ) : ?>
					<div class="mfw-report-list__row">
						<span><?php echo esc_html( $row['label'] ); ?></span>
						<strong><?php echo esc_html( $row['value'] ); ?></strong>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<div class="mfw-report-card">
		<div class="mfw-report-card__head">
			<div>
				<h3><?php esc_html_e( 'Top Membership Purchases', 'membership-for-woocommerce' ); ?></h3>
				<p><?php esc_html_e( 'See which plans drive the most paid purchases and membership revenue.', 'membership-for-woocommerce' ); ?></p>
			</div>
		</div>
		<div class="mfw-report-table-wrap">
			<table class="mfw-report-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Membership', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Paid Purchases', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Revenue', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Active Members', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Total Members', 'membership-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Discount Products', 'membership-for-woocommerce' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( ! empty( $top_memberships ) ) : ?>
						<?php foreach ( $top_memberships as $membership ) : ?>
							<tr>
								<td><?php echo esc_html( $membership['title'] ); ?></td>
								<td><?php echo esc_html( $membership['paid_purchases'] ); ?></td>
								<td><?php echo wp_kses_post( wc_price( (float) $membership['revenue'] ) ); ?></td>
								<td><?php echo esc_html( $membership['active_members'] ); ?></td>
								<td><?php echo esc_html( $membership['total_members'] ); ?></td>
								<td><?php echo esc_html( $membership['discount_products'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="6"><?php esc_html_e( 'No paid membership purchases found yet.', 'membership-for-woocommerce' ); ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="mfw-report-grid mfw-report-grid--three">
		<div class="mfw-report-card">
			<div class="mfw-report-card__head">
				<div>
					<h3><?php esc_html_e( 'Top Discount Products', 'membership-for-woocommerce' ); ?></h3>
					<p><?php esc_html_e( 'Weighted by how many paid memberships unlock each discounted product.', 'membership-for-woocommerce' ); ?></p>
				</div>
			</div>
			<div class="mfw-report-table-wrap">
				<table class="mfw-report-table mfw-report-table--compact">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Product', 'membership-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Exposure', 'membership-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Plans', 'membership-for-woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( ! empty( $top_discount_products ) ) : ?>
							<?php foreach ( $top_discount_products as $product ) : ?>
								<tr>
									<td><?php echo esc_html( $product['title'] ); ?></td>
									<td><?php echo esc_html( $product['membership_purchases'] ); ?></td>
									<td><?php echo esc_html( $product['plans_count'] ); ?></td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td colspan="3"><?php esc_html_e( 'No discount products are linked to paid plans yet.', 'membership-for-woocommerce' ); ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>

		<div class="mfw-report-card">
			<div class="mfw-report-card__head">
				<div>
					<h3><?php esc_html_e( 'Top Discount Categories', 'membership-for-woocommerce' ); ?></h3>
					<p><?php esc_html_e( 'Useful when discounts are applied at category level instead of individual products.', 'membership-for-woocommerce' ); ?></p>
				</div>
			</div>
			<div class="mfw-report-table-wrap">
				<table class="mfw-report-table mfw-report-table--compact">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Category', 'membership-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Exposure', 'membership-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Plans', 'membership-for-woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( ! empty( $top_discount_categories ) ) : ?>
							<?php foreach ( $top_discount_categories as $term ) : ?>
								<tr>
									<td><?php echo esc_html( $term['title'] ); ?></td>
									<td><?php echo esc_html( $term['membership_purchases'] ); ?></td>
									<td><?php echo esc_html( $term['plans_count'] ); ?></td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td colspan="3"><?php esc_html_e( 'No discount categories are linked to paid plans yet.', 'membership-for-woocommerce' ); ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>

		<div class="mfw-report-card">
			<div class="mfw-report-card__head">
				<div>
					<h3><?php esc_html_e( 'Top Discount Tags', 'membership-for-woocommerce' ); ?></h3>
					<p><?php esc_html_e( 'A quick view of which tag-based discounts are attached to your most-purchased memberships.', 'membership-for-woocommerce' ); ?></p>
				</div>
			</div>
			<div class="mfw-report-table-wrap">
				<table class="mfw-report-table mfw-report-table--compact">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Tag', 'membership-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Exposure', 'membership-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Plans', 'membership-for-woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( ! empty( $top_discount_tags ) ) : ?>
							<?php foreach ( $top_discount_tags as $term ) : ?>
								<tr>
									<td><?php echo esc_html( $term['title'] ); ?></td>
									<td><?php echo esc_html( $term['membership_purchases'] ); ?></td>
									<td><?php echo esc_html( $term['plans_count'] ); ?></td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td colspan="3"><?php esc_html_e( 'No discount tags are linked to paid plans yet.', 'membership-for-woocommerce' ); ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="mfw-report-grid mfw-report-grid--two">
		<div class="mfw-report-card">
			<div class="mfw-report-card__head">
				<div>
					<h3><?php esc_html_e( 'Payment Method Split', 'membership-for-woocommerce' ); ?></h3>
					<p><?php esc_html_e( 'See which gateways are driving membership purchases and revenue.', 'membership-for-woocommerce' ); ?></p>
				</div>
			</div>
			<div class="mfw-report-table-wrap">
				<table class="mfw-report-table mfw-report-table--compact">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Payment Method', 'membership-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Purchases', 'membership-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Revenue', 'membership-for-woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( ! empty( $payment_methods ) ) : ?>
							<?php foreach ( $payment_methods as $payment_method ) : ?>
								<tr>
									<td><?php echo esc_html( $payment_method['title'] ); ?></td>
									<td><?php echo esc_html( $payment_method['membership_purchases'] ); ?></td>
									<td><?php echo wp_kses_post( wc_price( (float) $payment_method['revenue'] ) ); ?></td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td colspan="3"><?php esc_html_e( 'No paid membership purchases found yet.', 'membership-for-woocommerce' ); ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>

		<div class="mfw-report-card">
			<div class="mfw-report-card__head">
				<div>
					<h3><?php esc_html_e( 'Recent Membership Purchases', 'membership-for-woocommerce' ); ?></h3>
					<p><?php esc_html_e( 'Latest paid membership orders with plan, status, payment method, and value.', 'membership-for-woocommerce' ); ?></p>
				</div>
			</div>
			<div class="mfw-report-table-wrap">
				<table class="mfw-report-table mfw-report-table--compact">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Date', 'membership-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Plan', 'membership-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Status', 'membership-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Payment', 'membership-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Amount', 'membership-for-woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( ! empty( $recent_memberships ) ) : ?>
							<?php foreach ( $recent_memberships as $recent ) : ?>
								<tr>
									<td>
										<?php if ( ! empty( $recent['order_id'] ) ) : ?>
											<a href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $recent['order_id'] ) . '&action=edit' ) ); ?>"><?php echo esc_html( $recent['created_at'] ); ?></a>
										<?php else : ?>
											<?php echo esc_html( $recent['created_at'] ); ?>
										<?php endif; ?>
									</td>
									<td><?php echo esc_html( $recent['plan_title'] ); ?></td>
									<td><span class="mfw-report-status mfw-report-status--<?php echo esc_attr( $recent['status'] ); ?>"><?php echo esc_html( ucfirst( $recent['status'] ) ); ?></span></td>
									<td><?php echo esc_html( $recent['payment'] ); ?></td>
									<td><?php echo wp_kses_post( wc_price( (float) $recent['amount'] ) ); ?></td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td colspan="5"><?php esc_html_e( 'No recent paid membership purchases found yet.', 'membership-for-woocommerce' ); ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="mfw-report-card">
		<div class="mfw-report-card__head">
			<div>
				<h3><?php esc_html_e( 'Extended Report Hooks', 'membership-for-woocommerce' ); ?></h3>
				<p><?php esc_html_e( 'Additional rows injected by existing report extensions are preserved below.', 'membership-for-woocommerce' ); ?></p>
			</div>
		</div>
		<table class="mfw-report-table mfw-report-table--compact">
			<tbody>
				<?php do_action( 'wps_msfw_extend_report_section', $report['wps_store_member_ids'] ); ?>
			</tbody>
		</table>
	</div>
</div>