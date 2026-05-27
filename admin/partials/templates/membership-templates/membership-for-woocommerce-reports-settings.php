<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wpswings.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce_Pro
 * @subpackage Membership_For_Woocommerce_Pro/admin/partials
 */

// Exit is accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$report_data            = Membership_For_Woocommerce_Global_Functions::get()->wps_mfw_build_membership_report_data();
$total_membership_plans = ! empty( $report_data['total_membership_plans'] ) ? absint( $report_data['total_membership_plans'] ) : 0;
$total_members          = ! empty( $report_data['total_members'] ) ? absint( $report_data['total_members'] ) : 0;
$complete               = ! empty( $report_data['complete'] ) ? absint( $report_data['complete'] ) : 0;
$pending                = ! empty( $report_data['pending'] ) ? absint( $report_data['pending'] ) : 0;
$expired                = ! empty( $report_data['expired'] ) ? absint( $report_data['expired'] ) : 0;
$wps_store_member_ids   = ! empty( $report_data['wps_store_member_ids'] ) && is_array( $report_data['wps_store_member_ids'] ) ? $report_data['wps_store_member_ids'] : array();
$activity               = ! empty( $report_data['activity'] ) && is_array( $report_data['activity'] ) ? $report_data['activity'] : array();

?>
<form method="post" class="wps_msfw_membership_report_wrap">
	<?php wp_nonce_field( 'wps_mfw_export_csv', 'wps_mfw_export_csv_nonce' ); ?>
	<input type="hidden" name="wps_mfw_export_csv" value="1">
	<button class="button button-primary">
		<?php esc_html_e( 'Export Membership Report', 'membership-for-woocommerce' ); ?>
	</button>
</form>

<div class="membership_report">
	<div class="wps_members_plans">
		<h2 class="wps-members__plans--title"><?php esc_html_e( 'membership report', 'membership-for-woocommerce' ); ?></h2>
		<table class="form-table">
			<tbody class="wps-member__plan--card-wrap">
				<tr class="wps-member__plan--card">
					<th><label><?php esc_html_e( 'Membership Plans', 'membership-for-woocommerce' ); ?></label></th>
					<td><?php echo esc_html( $total_membership_plans ); ?></td>
				</tr>

				<tr class="wps-member__plan--card">
					<th><label><?php esc_html_e( 'Total Members', 'membership-for-woocommerce' ); ?></label></th>
					<td><?php echo esc_html( $total_members ); ?></td>
				</tr>

				<tr class="wps-member__plan--card">
					<th><label><?php esc_html_e( 'Active Members', 'membership-for-woocommerce' ); ?></label></th>
					<td><?php echo esc_html( $complete ); ?></td>
				</tr>

				<tr class="wps-member__plan--card">
					<th><label><?php esc_html_e( 'Pending Members', 'membership-for-woocommerce' ); ?></label></th>
					<td><?php echo esc_html( $pending ); ?></td>
				</tr>

				<tr class="wps-member__plan--card">
					<th><label><?php esc_html_e( 'Expired Members', 'membership-for-woocommerce' ); ?></label></th>
					<td><?php echo esc_html( $expired ); ?></td>
				</tr>
				<?php do_action( 'wps_msfw_extend_report_section', $wps_store_member_ids ); ?>
			</tbody>
		</table>
			<?php
			$today       = ! empty( $activity['today'] ) ? absint( $activity['today'] ) : 0;
			$yesterday   = ! empty( $activity['yesterday'] ) ? absint( $activity['yesterday'] ) : 0;
			$last_7_days = ! empty( $activity['last_7_days'] ) ? absint( $activity['last_7_days'] ) : 0;
			$this_month  = ! empty( $activity['this_month'] ) ? absint( $activity['this_month'] ) : 0;
			$last_month  = ! empty( $activity['last_month'] ) ? absint( $activity['last_month'] ) : 0;
			$this_year   = ! empty( $activity['this_year'] ) ? absint( $activity['this_year'] ) : 0;
			$last_year   = ! empty( $activity['last_year'] ) ? absint( $activity['last_year'] ) : 0;
			?>
		<div class="wps-last_actived-members" > 
			<h4 style="margin-left:120px;font-family:Helvetica, sans-serif;color:black; font-weight:bolder "><?php esc_html_e( 'Last Actived Members', 'membership-for-woocommerce' ); ?></h4>
			<table class="wps-mfwp-reports-table-membership">
				<tbody>
					<tr>
						<th><?php esc_html_e( 'Today', 'membership-for-woocommerce' ); ?></th>
						<td><?php echo esc_html( $today ); ?></td>
					</tr>

					<tr>
						<th><?php esc_html_e( 'Yesterday', 'membership-for-woocommerce' ); ?></th>
						<td><?php echo esc_html( $yesterday ); ?></td>
					</tr>

					<tr>
						<th><?php esc_html_e( 'Last 7 days', 'membership-for-woocommerce' ); ?></th>
						<td><?php echo esc_html( $last_7_days ); ?></td>
					</tr>

					<tr>
						<th><?php esc_html_e( 'This month', 'membership-for-woocommerce' ); ?></th>
						<td><?php echo esc_html( $this_month ); ?></td>
					</tr>

					<tr>
						<th><?php esc_html_e( 'Last month', 'membership-for-woocommerce' ); ?></th>
						<td><?php echo esc_html( $last_month ); ?></td>
					</tr>

					<tr>
						<th><?php esc_html_e( 'This year', 'membership-for-woocommerce' ); ?></th>
						<td><?php echo esc_html( $this_year ); ?></td>
					</tr>

					<tr>
						<th><?php esc_html_e( 'Last year', 'membership-for-woocommerce' ); ?></th>
						<td><?php echo esc_html( $last_year ); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
