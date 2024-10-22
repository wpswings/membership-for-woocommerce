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

$total_membership_plans = 0;
$array_of_ids           = array();
$args                   = array(
	'post_type'      => 'wps_cpt_membership',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
);

$loop = new WP_Query( $args );
while ( $loop->have_posts() ) {
	$loop->the_post();
	$total_membership_plans ++;
}

$total_members = 0;
$complete      = 0;
$pending       = 0;
$expired       = 0;
$args          = get_posts(
	array(
		'post_type'   => 'wps_cpt_members',
		'post_status' => 'publish',
		'numberposts' => -1,
		'fields'      => 'ids',
	)
);

$wps_store_member_ids = array();
foreach ( $args as $key => $value ) {

	array_push( $wps_store_member_ids, $value );

	$member_status = wps_membership_get_meta_data( $value, 'member_status', true );
	$mfw_id        = $value - 1;
	$orders        = wc_get_order( $mfw_id );
	if ( empty( $orders ) ) {
		continue;
	}

	$items = $orders->get_items();
	foreach ( $items as $item ) {
		if ( ! empty( $item->get_meta_data()[1] ) ) {

			if ( '_member_id' === $item->get_meta_data()[1]->key ) {
				if ( 'complete' === $member_status ) {

					$complete ++;
					if ( ! in_array( $mfw_id, $array_of_ids ) ) {

						$array_of_ids[] = $mfw_id;
					}
				}

				if ( 'pending' === $member_status ) {
					$pending ++;
				}

				if ( 'expired' === $member_status ) {
					$expired ++;
					if ( ! in_array( $mfw_id, $array_of_ids ) ) {

						$array_of_ids[] = $mfw_id;
					}
				}
				$total_members ++;
			}
		}
	}
}

?>
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
			$today                 = 0;
			$yesterday             = 0;
			$last_7_days           = 0;
			$this_month            = 0;
			$last_month            = 0;
			$this_year             = 0;
			$last_year             = 0;
			$today_timestamp       = strtotime( 'now' );
			$today_start           = strtotime( 'today' );
			$this_year_timestamp   = strtotime( 'first day of january this year ' );
			$last_year_start       = strtotime( 'first day of january previous year ' );
			$last_year_end         = strtotime( 'last day of december previous year ' );
			$last_7_days_timestamp = strtotime( 'today' ) - 7 * 86400;
			$yesterday_timestamp   = strtotime( 'yesterday' );
			$this_month_first      = strtotime( 'first day of this month ' );
			$last_month_first      = strtotime( 'first day of previous month ' );
			$last_month_last       = strtotime( 'last day of previous month ' );

			$order_ids = get_posts(
				array(
					'post_type'      => 'shop_order',
					'post_status'    => 'completed',
					'numberposts' => -1,
					'fields'   => 'ids',
				)
			);
			$result    = array();
			foreach ( $order_ids as $key => $value ) {

				$result[]['order_id'] = $value;
			}

			if ( ! empty( $result ) && is_array( $result ) ) {
				foreach ( $result as $key => $value ) {

					$notes = wc_get_order_notes( $value );
					if ( empty( $notes ) ) {

						continue;
					}

					$order_timestamp = strtotime( $notes[0]->date_created->date( 'Y-m-d H:i:s' ) );
					if ( empty( wc_get_order( $value['order_id'] ) ) ) {

						continue;
					}

					$items = wc_get_order( $value['order_id'] )->get_items();
					if ( in_array( $value['order_id'], $array_of_ids ) ) {
						foreach ( $items as $item ) {

							if ( '_member_id' === $item->get_meta_data()[1]->key ) {

								if ( $yesterday_timestamp <= $order_timestamp && $order_timestamp < $today_start ) {
									$yesterday++;
								}

								if ( $today_start <= $order_timestamp && $order_timestamp <= $today_timestamp ) {
									$today++;
								}

								if ( $last_7_days_timestamp <= $order_timestamp && $order_timestamp <= $today_timestamp ) {
									$last_7_days++;
								}

								if ( $this_month_first <= $order_timestamp && $order_timestamp <= $today_timestamp ) {
									$this_month++;
								}

								if ( $last_month_first <= $order_timestamp && $order_timestamp <= $last_month_last ) {
									$last_month++;
								}

								if ( $last_year_start <= $order_timestamp && $order_timestamp <= $last_year_end ) {
									$last_year++;
								}

								if ( $this_year_timestamp <= $order_timestamp && $order_timestamp <= $today_timestamp ) {
									$this_year++;
								}
							}
						}
					}
				}
			}
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
