<?php
/**
 * Membership details/History
 *
 * Shows Membership details on the account page.
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

use Automattic\WooCommerce\Utilities\OrderUtil;

$current_url = ! empty( $_GET['membership'] ) ? sanitize_text_field( wp_unslash( $_GET['membership'] ) ) : '';
$user        = ! empty( $user_id ) ? $user_id : '';
$memberships = ! empty( $membership_ids ) ? $membership_ids : array();

// create user dashboard.
if ( isset( $_GET['view-dashboard'] ) ) {

	// Get active membership plan name.
	$active_plan_name = '';
	if ( ! empty( $memberships ) && is_array( $memberships ) ) {
		foreach ( $memberships as $key => $membership_id ) {

			$membership_data = get_post_meta( $membership_id );
			$membership_plan = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
			// if plan is not exist than continue loop from here.
			if ( empty( $membership_plan ) ) {

				continue;
			}

			$membership_status = wps_membership_get_meta_data( $membership_id, 'member_status', true );
			if ( 'complete' === $membership_status ) {

				$active_plan_name .= ! empty( $membership_plan['post_title'] ) ? $membership_plan['post_title'] : '';
				$active_plan_name .= ' , ';
			}
		}
	}

	// Get active subscription name.
	if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
		$args = array(
			'type'   => 'wps_subscriptions',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'   => 'wps_customer_id',
					'value' => $user_id,
				),
				array(
					'key'   => 'wps_subscription_status',
					'value' => 'active',
				),
			),
			'return' => 'ids',
		);
		$wps_subscriptions = wc_get_orders( $args );
	} else {
		$args = array(
			'numberposts' => -1,
			'post_type'   => 'wps_subscriptions',
			'post_status' => 'wc-wps_renewal',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'   => 'wps_customer_id',
					'value' => $user_id,
				),
				array(
					'key'   => 'wps_subscription_status',
					'value' => 'active',
				),
			),
		);
		$wps_subscriptions = get_posts( $args );
	}

	$active_subs_name = '';
	if ( ! empty( $wps_subscriptions ) && is_array( $wps_subscriptions ) ) {
		foreach ( array_reverse( $wps_subscriptions ) as $subs_id ) {

			$wps_mfw_order = wc_get_order( $subs_id );
			if ( ! empty( $wps_mfw_order ) && is_object( $wps_mfw_order ) ) {

				$active_subs_name .= $wps_mfw_order->get_meta( 'product_name' );
				$active_subs_name .= ' , ';
			}
		}
	}

	// Get user details.
	$user       = get_user_by( 'ID', $user_id );
	$avatar_url = get_avatar_url( $user_id );

	// Get total rewards points and total discount advantages.
	$get_rewards_points            = get_user_meta( $user_id, 'wps_wpr_points', true );
	$wps_mfw_total_discount_amount = get_user_meta( $user_id, 'wps_mfw_total_discount_amount', true );
	$wps_mfw_total_discount_amount = ! empty( $wps_mfw_total_discount_amount ) ? $wps_mfw_total_discount_amount : 0;

	?>
	<div class="wps-msfw_dashboard-main">
		<section class="wps-msfw_dm-sec wps-msfw_dm-sec-header">
			<span class="wps-msfw_dms-role"><?php echo esc_html( $user->roles[0] ); ?></span>
			<div class="wps-msfw_dms-user-details">
				<img src="<?php echo esc_url( $avatar_url ); ?>" alt="User Avatar">
				<div class="wps-msfw_dmsa-in">
					<div class="wps-msfw_dms-name"><?php esc_html_e( 'Hi! ', 'membership-for-woocommerce' ); ?><?php echo esc_html( $user->display_name ); ?></div>
					<div class="wps-msfw_dms-email"><?php echo esc_html( $user->user_email ); ?></div>
				</div>
			</div>
			<div class="wps-msfw_dms-art">
				<strong><?php esc_html_e( 'Total Discount Benefits: ', 'membership-for-woocommerce' ); ?></strong><span><?php echo wp_kses_post( wc_price( $wps_mfw_total_discount_amount ) ); ?></span>
			</div>
		</section>
		<section class="wps-msfw_dm-sec wps-msfw_dm-sec-content">
			<div class="wps-msfw_dmsc-art msfw_am">
				<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M21 25V23C21 21.9391 20.5786 20.9217 19.8284 20.1716C19.0783 19.4214 18.0609 19 17 19H9C7.93913 19 6.92172 19.4214 6.17157 20.1716C5.42143 20.9217 5 21.9391 5 23V25M27 24.9999V22.9999C26.9993 22.1136 26.7044 21.2527 26.1614 20.5522C25.6184 19.8517 24.8581 19.3515 24 19.1299M20 7.12988C20.8604 7.35018 21.623 7.85058 22.1676 8.55219C22.7122 9.2538 23.0078 10.1167 23.0078 11.0049C23.0078 11.8931 22.7122 12.756 22.1676 13.4576C21.623 14.1592 20.8604 14.6596 20 14.8799M17 11C17 13.2091 15.2091 15 13 15C10.7909 15 9 13.2091 9 11C9 8.79086 10.7909 7 13 7C15.2091 7 17 8.79086 17 11Z" stroke="#172D56" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				<div class="wps-msfw_dmsca-in">
					<div class="wps-msfw_dmsca-label"><?php esc_html_e( 'Active Membership ', 'membership-for-woocommerce' ); ?></div>
					<div class="wps-msfw_dmsca-item"><?php echo esc_html( $active_plan_name ); ?></div>
				</div>
			</div>
			<div class="wps-msfw_dmsc-art msfw_as">
				<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M27 14.9943V16.0063C26.9986 18.3784 26.2305 20.6864 24.8103 22.5863C23.39 24.4862 21.3936 25.876 19.1189 26.5486C16.8442 27.2211 14.413 27.1404 12.1879 26.3183C9.96285 25.4963 8.06312 23.977 6.77206 21.9871C5.48099 19.9971 4.86777 17.6432 5.02384 15.2762C5.17992 12.9093 6.09693 10.6563 7.63811 8.85307C9.17929 7.0499 11.2621 5.79322 13.5758 5.27046C15.8895 4.7477 18.3103 4.98687 20.477 5.9523M27 7.20618L16 18.2172L12.7 14.9172" stroke="#172D56" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				<div class="wps-msfw_dmsca-in">
					<div class="wps-msfw_dmsca-label"><?php esc_html_e( 'Active Subscription ', 'membership-for-woocommerce' ); ?></div>
					<div class="wps-msfw_dmsca-item"><?php echo esc_html( $active_subs_name ); ?></div>
				</div>
			</div>
			<?php do_action( 'wps_msfw_extend_members_dashboard', $user_id ); ?>
		</section>
	</div>
	<?php
} elseif ( isset( $_GET['membership'] ) ) {

	// show membership overall details.
	?>
	<div class="wps_msfw__new_layout_billing">
	<?php

		$membership_id      = ! empty( $_GET['membership'] ) ? sanitize_text_field( wp_unslash( $_GET['membership'] ) ) : '';
		$membership_data    = get_post_meta( $membership_id );
		$membership_plan    = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
		$membership_billing = wps_membership_get_meta_data( $membership_id, 'billing_details', true );
		if ( ( ! empty( $membership_plan ) && is_array( $membership_plan ) ) && ( ! empty( $membership_billing ) && is_array( $membership_billing ) ) ) {

			$membership_status  = wps_membership_get_meta_data( $membership_id, 'member_status', true );
			if ( ! array_key_exists( 'payment_method', $membership_billing ) ) {
				$membership_billing['payment_method'] = ! empty( wps_membership_get_meta_data( $membership_id, 'billing_details_payment', true ) ) ? wps_membership_get_meta_data( $membership_id, 'billing_details_payment', true ) : '';
			}
			$temp_array = array(
				'wps_memebership_product_discount_price' => '',
				'wps_membership_subscription' => '',
				'wps_membership_subscription_expiry' => '',
				'wps_membership_plan_target_tags' => '',
				'wps_membership_plan_post_target_ids' => '',
				'wps_membership_plan_target_post_categories' => '',
				'wps_membership_plan_target_post_tags' => '',
				'wps_membership_plan_page_target_ids' => '',
				'wps_membership_plan_target_disc_ids' => '',
				'wps_membership_plan_target_disc_categories' => '',
				'wps_membership_plan_target_disc_tags' => '',
				'wps_memebership_plan_discount_price' => 0,
				'wps_membership_plan_offer_price_type' => '',
				'wps_membership_product_offer_price_type' => '',
				'wps_membership_subscription_expiry_type' => '',
				'wps_membership_plan_target_categories' => '',

			);
			foreach ( $temp_array as $m_keys => $m_values ) {
				if ( ! array_key_exists( $m_keys, $membership_plan ) ) {
					$membership_plan[ $m_keys ] = $m_values;
				}
			}

			$expiry = wps_membership_get_meta_data( $membership_id, 'member_expiry', true );
			if ( ! empty( $membership_plan ) ) {

				$access_type = wps_membership_get_meta_data( $membership_plan['ID'], 'wps_membership_plan_access_type', true );
				if ( 'delay_type' == $access_type ) {

					$time_duration      = wps_membership_get_meta_data( $membership_plan['ID'], 'wps_membership_plan_time_duration', true );
					$time_duration_type = wps_membership_get_meta_data( $membership_plan['ID'], 'wps_membership_plan_time_duration_type', true );
					$current_date       = gmdate( 'Y-m-d' );
					$current_date       = gmdate( 'Y-m-d', strtotime( $current_date . ' + ' . $time_duration . ' ' . $time_duration_type ) );
				}
			}

			$membership_expiry  = wps_membership_get_meta_data( $membership_id, 'member_expiry', true );
			if ( in_array( $membership_status, array( 'hold', 'pending' ) ) ) {
				?>
				<p><?php echo esc_html( 'Membership plan ', 'membership-for-woocommerce' ) . '#'; ?><mark class="order-number"><?php echo esc_html( $membership_id ); ?></mark><?php esc_html_e( ' was placed on ', 'membership-for-woocommerce' ); ?> <mark class="order-date"><?php echo esc_html( get_the_date( 'j F Y', $membership_id ) ); ?></mark><?php esc_html_e( ' and is currently ', 'membership-for-woocommerce' ); ?><mark class="order-status"><?php echo esc_html( $membership_status ); ?></mark>.</p>
				<?php
				return;
			}

			if ( 'Lifetime' == $membership_expiry ) {
				?>
				<p><?php echo esc_html( 'Membership plan ', 'membership-for-woocommerce' ) . '#'; ?><mark class="order-number"><?php echo esc_html( $membership_id ); ?></mark><?php esc_html_e( ' was placed on ', 'membership-for-woocommerce' ); ?> <mark class="order-date"><?php echo esc_html( get_the_date( 'j F Y', $membership_id ) ); ?></mark><?php esc_html_e( ' and is currently ', 'membership-for-woocommerce' ); ?><mark class="order-status"><?php echo esc_html( ucwords( $membership_status ) ); ?></mark>.</p>
				<?php
			} else {

				?>
				<p><?php echo esc_html( 'Membership plan ', 'membership-for-woocommerce' ) . '#'; ?><mark class="order-number"><?php echo esc_html( $membership_id ); ?></mark><?php esc_html_e( ' was placed on ', 'membership-for-woocommerce' ); ?> <mark class="order-date"><?php echo esc_html( get_the_date( 'j F Y', $membership_id ) ); ?></mark><?php esc_html_e( ' and is currently ', 'membership-for-woocommerce' ); ?><mark class="order-status"><?php echo esc_html( ucwords( $membership_status ) ); ?></mark> <?php esc_html_e( ' and will expire on ', 'membership-for-woocommerce' ); ?> <mark> <?php echo esc_html( gmdate( 'j F Y', intval( $membership_expiry ) ) ); ?> </mark>.</p>
				<?php } ?>
				<section class="woocommerce-order-details">
					<h2 class="woocommerce-order-details__title"><?php esc_html_e( 'Membership details', 'membership-for-woocommerce' ); ?></h2>
					<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
						<thead>
							<tr>
								<th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'membership-for-woocommerce' ); ?></th>
								<th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'membership-for-woocommerce' ); ?></th>
							</tr>
						</thead>

						<tbody>
							<tr class="woocommerce-table__line-item order_item">
								<th class="woocommerce-table__product-name product-name">
									<span href="javascript:void(0)" ><?php echo esc_html( $membership_plan['post_name'] ); ?> </span> <strong class="product-quantity">&nbsp;<?php esc_html( 1 ); ?></strong>	</th>
								<td class="woocommerce-table__product-total product-total">
									<span class="woocommerce-Price-amount amount"><?php echo sprintf( ' %s %s ', esc_html( get_woocommerce_currency_symbol() ), esc_html( $membership_plan['wps_membership_plan_price'] ) ); ?></span></td>
							</tr>
						</tbody>

						<tfoot>

							<tr>
								<th scope="row"><?php esc_html_e( 'Payment method', 'membership-for-woocommerce' ); ?></th>
								<td><?php echo esc_html( $instance->get_payment_method_title( $membership_billing['payment_method'] ) ); ?></td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'Total', 'membership-for-woocommerce' ); ?></th>
								<td><span class="woocommerce-Price-amount amount"><?php echo sprintf( ' %s %s ', esc_html( get_woocommerce_currency_symbol() ), esc_html( $membership_plan['wps_membership_plan_price'] ) ); ?></span></td>
							</tr>
						</tfoot>
					</table>
				</section>

				<section class="woocommerce-customer-details">

					<article class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses col2-set addresses">
						<div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-1">

							<h2 class="woocommerce-column__title"><?php esc_html_e( 'Billing address', 'membership-for-woocommerce' ); ?></h2>
							<address>
								<?php echo sprintf( ' %s %s ', esc_html( $membership_billing['membership_billing_first_name'] ), esc_html( $membership_billing['membership_billing_last_name'] ) ); ?></br>
								<?php echo esc_html( $membership_billing['membership_billing_company'] ); ?></br>
								<?php echo sprintf( ' %s %s ', esc_html( $membership_billing['membership_billing_address_1'] ), esc_html( $membership_billing['membership_billing_address_2'] ) ); ?></br>
								<?php echo sprintf( ' %s %s ', esc_html( $membership_billing['membership_billing_city'] ), esc_html( $membership_billing['membership_billing_postcode'] ) ); ?></br>
								<?php echo sprintf( ' %s, %s ', esc_html( $membership_billing['membership_billing_state'] ), esc_html( $membership_billing['membership_billing_country'] ) ); ?>
								<p class="woocommerce-customer-details--phone"><?php echo esc_html( $membership_billing['membership_billing_phone'] ); ?></p>
								<p class="woocommerce-customer-details--email"><?php echo esc_html( $membership_billing['membership_billing_email'] ); ?></p>
							</address>
						</div>

						<div class="woocommerce-column woocommerce-column--2 woocommerce-column--plan-details col-2">
							<h2 class="woocommerce-column__title"><?php esc_html_e( 'Plan details', 'membership-for-woocommerce' ); ?></h2>
							<address>
								<?php echo sprintf( ' %s %s ', esc_html__( 'Plan Name: ', 'membership-for-woocommerce' ), esc_html( $membership_plan['post_title'] ) ); ?></br>
								<?php echo sprintf( ' %s %s ', esc_html__( 'Status: ', 'membership-for-woocommerce' ), esc_html( ucwords( $membership_status ) ) ); ?></br>
								<?php echo sprintf( ' %s %u %s ', esc_html__( 'Discount on cart: ', 'membership-for-woocommerce' ), esc_html( $membership_plan['wps_memebership_plan_discount_price'] ), esc_html( $membership_plan['wps_membership_plan_offer_price_type'] ) ); ?></br>
								<?php echo sprintf( ' %s %u %s ', esc_html__( 'Discount on Product: ', 'membership-for-woocommerce' ), esc_html( $membership_plan['wps_memebership_product_discount_price'] ), esc_html( $membership_plan['wps_membership_product_offer_price_type'] ) ); ?></br>
								<?php echo sprintf( ' %s %s ', esc_html__( 'Subscription Membership: ', 'membership-for-woocommerce' ), esc_html( $membership_plan['wps_membership_subscription'] ) ); ?></br>
								<?php echo sprintf( ' %s %u %s ', esc_html__( 'Subscription Membership Duration: ', 'membership-for-woocommerce' ), esc_html( $membership_plan['wps_membership_subscription_expiry'] ), esc_html( 'days' === $membership_plan['wps_membership_subscription_expiry_type'] ) ? esc_html( $membership_plan['wps_membership_subscription_expiry_type'] ) : esc_html( $membership_plan['wps_membership_subscription_expiry_type'] ) . 's' ); ?></br>
								<?php echo sprintf( ' %s %s ', esc_html__( 'Free Shipping: ', 'membership-for-woocommerce' ), esc_html( ! empty( $membership_plan['wps_memebership_plan_free_shipping'] ) ? 'Yes' : 'No' ) ); ?></br>
							</address>
						</div>
								<table>
								
								<tr>
								<th><label><?php esc_html_e( 'Include Membership', 'membership-for-woocommerce' ); ?></label></th>
								<td>
									<?php
									$club_membership = wps_membership_get_meta_data( $membership_plan['ID'], 'wps_membership_club', true );
									if ( ! empty( $club_membership ) && is_array( $club_membership ) ) {
										foreach ( $club_membership as $ids ) {
											$include_membership_data = get_post( $ids );

											echo( esc_html( $include_membership_data->post_title ) );
										}
									}
									?>
								</td>
							</tr>
									
								<tr>
									<th><label><?php esc_html_e( 'Offered Products: ', 'membership-for-woocommerce' ); ?></label></th>
									<td>
										<?php
											$prod_ids = maybe_unserialize( $membership_plan['wps_membership_plan_target_ids'] );

										if ( ! empty( $prod_ids ) && is_array( $prod_ids ) ) {
											foreach ( $prod_ids as $ids ) {
												echo( esc_html( $instance->get_product_title( $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
											}
										} else {
											esc_html_e( 'No Products Offered in this Plan', 'membership-for-woocommerce' );
										}

										?>
									</td>
								</tr>
								<tr>
									<th><label><?php esc_html_e( 'Offered Products Categories: ', 'membership-for-woocommerce' ); ?></label></th>
									<td>
										<?php

										$cat_ids = maybe_unserialize( $membership_plan['wps_membership_plan_target_categories'] );
										if ( ! empty( $cat_ids ) && is_array( $cat_ids ) ) {
											foreach ( $cat_ids as $ids ) {
												echo( esc_html( $instance->get_category_title( $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
											}
										} else {
											esc_html_e( 'No Product Categories Offered in this Plan', 'membership-for-woocommerce' );
										}
										?>
									</td>
								</tr>
								<?php
								if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
									$check_licence = check_membership_pro_plugin_is_active();
									if ( $check_licence ) {

										?>
								<tr>
									<th><label><?php esc_html_e( 'Offered Product Tags: ', 'membership-for-woocommerce' ); ?></label></th>
									<td>
										<?php
										$tag_ids = maybe_unserialize( $membership_plan['wps_membership_plan_target_tags'] );

										if ( ! empty( $tag_ids ) && is_array( $tag_ids ) ) {
											foreach ( $tag_ids as $ids ) {

												$tagn     = get_term_by( 'id', $ids, 'product_tag' );
												$tag_name = $tagn->name;
												echo( esc_html( $tag_name ) . '(#' . esc_html( $ids ) . ') ' );
											}
										} else {
											esc_html_e( 'No Product Tags Offered in this Plan', 'membership-for-woocommerce' );
										}
										?>
									</td>
								</tr>
								
								<tr>
									<th><label><?php esc_html_e( 'Offered Posts: ', 'membership-for-woocommerce' ); ?></label></th>
								
									<td>
										<?php
										$post_ids = maybe_unserialize( $membership_plan['wps_membership_plan_post_target_ids'] );
										if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
											foreach ( $post_ids as $ids ) {

												echo( esc_html( get_post_field( 'post_title', $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
											}
										} else {
											esc_html_e( 'No Posts Offered in this Plan', 'membership-for-woocommerce' );
										}
										?>
									</td>
								</tr>
								<tr>
									<th><label><?php esc_html_e( 'Offered Posts Categories: ', 'membership-for-woocommerce' ); ?></label></th>
									<td>
										<?php
										$cat_ids = maybe_unserialize( $membership_plan['wps_membership_plan_target_post_categories'] );
										if ( ! empty( $cat_ids ) && is_array( $cat_ids ) ) {
											foreach ( $cat_ids as $ids ) {
												echo( esc_html( $instance->get_category_title( $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
											}
										} else {
											esc_html_e( 'No Product Categories Offered in this Plan', 'membership-for-woocommerce' );
										}
										?>
									</td>
								</tr>

								<tr>
									<th><label><?php esc_html_e( 'Offered Post Tags: ', 'membership-for-woocommerce' ); ?></label></th>
									<td>
										<?php
										$tag_ids = maybe_unserialize( $membership_plan['wps_membership_plan_target_post_tags'] );
										if ( ! empty( $tag_ids ) && is_array( $tag_ids ) ) {
											foreach ( $tag_ids as $ids ) {
												$tagn     = get_term_by( 'id', $ids, 'post_tag' );
												$tag_name = $tagn->name;
												echo( esc_html( $tag_name ) . '(#' . esc_html( $ids ) . ') ' );
											}
										} else {
											esc_html_e( 'No Post Tags Offered in this Plan', 'membership-for-woocommerce' );
										}
										?>
									</td>
								</tr>

								<tr>
									<th><label><?php esc_html_e( 'Offered Pages: ', 'membership-for-woocommerce' ); ?></label></th>
									<td>
										<?php

										$post_ids = maybe_unserialize( $membership_plan['wps_membership_plan_page_target_ids'] );
										if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
											foreach ( $post_ids as $ids ) {

												echo( esc_html( get_post_field( 'post_title', $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
											}
										} else {
											esc_html_e( 'No Page Offered in this Plan', 'membership-for-woocommerce' );
										}
										?>
									</td>
								</tr>

								<tr>
									<th><label><?php esc_html_e( 'Offered Product (under Product discount): ', 'membership-for-woocommerce' ); ?></label></th>
									<td>
										<?php

										$post_ids = maybe_unserialize( $membership_plan['wps_membership_plan_target_disc_ids'] );
										if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
											foreach ( $post_ids as $ids ) {

												echo( esc_html( get_post_field( 'post_title', $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
											}
										} else {
											esc_html_e( 'No Product Offered in this Plan', 'membership-for-woocommerce' );
										}
										?>
									</td>
								</tr>


								<tr>
									<th><label><?php esc_html_e( 'Offered Product Categories (under Product discount): ', 'membership-for-woocommerce' ); ?></label></th>
									<td>
										<?php

										$cat_ids = maybe_unserialize( $membership_plan['wps_membership_plan_target_disc_categories'] );
										if ( ! empty( $cat_ids ) && is_array( $cat_ids ) ) {
											foreach ( $cat_ids as $ids ) {
												echo( esc_html( $instance->get_category_title( $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
											}
										} else {
											esc_html_e( 'No categories Offered in this Plan', 'membership-for-woocommerce' );
										}
										?>
									</td>
								</tr>

								<tr>
									<th><label><?php esc_html_e( 'Offered Product Tags (under Product discount): ', 'membership-for-woocommerce' ); ?></label></th>
									<td>
										<?php

										$tag_ids = maybe_unserialize( $membership_plan['wps_membership_plan_target_disc_tags'] );
										if ( ! empty( $tag_ids ) && is_array( $tag_ids ) ) {
											foreach ( $tag_ids as $ids ) {
												$tagn     = get_term_by( 'id', $ids, 'product_tag' );
												$tag_name = $tagn->name;
												echo( esc_html( $tag_name ) . '(#' . esc_html( $ids ) . ') ' );
											}
										} else {
											esc_html_e( 'No Product Tags Offered in this Plan', 'membership-for-woocommerce' );
										}
										?>
									</td>
								</tr>				
										<?php
									}
								}
								?>
							</table>
						</article>
				</article>
			<?php
		} else {
			?>
			<div class="wps_mfw_no_plan_found_main_wrapper">
				<img class="wps_mfw_no_plan_found_img" src="<?php echo esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/sad-img.svg' ); ?>" alt="No Plan Found">
				<span class="wps_mfw_no_plan_found_msg"><?php esc_html_e( 'No Plan Found', 'membership-for-woocommerce' ); ?></span>
			</div>
			<?php
		}
		?>
	</div>
	<?php
} else {
	
	// Button to show membership dashboard.
	if ( 'on' === get_option( 'wps_msfw_enable_members_dashboard' ) ) {

		echo '<div class="wps-msfw_dash-btn"><strong>' . esc_html__( 'Tap to Reveal Your Membership Details ', 'membership-for-woocommerce' ) . '</strong><a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) . 'wps-membership-tab/?view-dashboard=' . $user_id ) . '" class="woocommerce-button button alt wps_mfw_members_dashboard">' . esc_html__( 'View Dashboard', 'membership-for-woocommerce' ) . '</a></div>';
	}

	// Show all active and cancelled membership.
	if ( $memberships ) : ?>

		<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table wps_msfw__new_layout">
			<thead>
				<tr>
					<?php foreach ( $instance->membership_tab_headers() as $column_id => $column_name ) : ?>
						<th class="woocommerce-orders-table__header woocommerce-orders-table__header-<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
					<?php endforeach; ?>
				</tr>
			</thead>

			<tbody>
				<?php
				foreach ( $memberships as $key => $membership_id ) {

					// Get Saved Plan Details.
					$membership_data   = get_post_meta( $membership_id );
					$membership_plan   = wps_membership_get_meta_data( $membership_id, 'plan_obj', true );
					$membership_status = wps_membership_get_meta_data( $membership_id, 'member_status', true );
					$button_disable   = ( in_array( $membership_status, array( 'pending', 'hold' ) ) ) ? 'disabled' : '';

					// if plan is not exist than continue loop from here.
					if ( empty( $membership_plan ) ) {

						continue;
					}
					?>
					<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr( $membership_status ); ?> order">
						<?php foreach ( $instance->membership_tab_headers() as $column_id => $column_name ) : ?>
							<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">


								<?php if ( 'members-id' === $column_id ) : ?>
									<span >
										<?php echo esc_html( _x( '#', 'hash before member id', 'membership-for-woocommerce' ) . $membership_id ); ?>
								</span>

								<?php elseif ( 'members-date' === $column_id ) : ?>
									<time datetime="<?php echo esc_attr( get_the_date( 'j F Y', $membership_id ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y', $membership_id ) ); ?></time>

								<?php elseif ( 'members-status' === $column_id ) : ?>
									<?php echo esc_html( ucwords( $membership_status ) ); ?>

								<?php elseif ( 'members-total' === $column_id ) : ?>
									<?php
									if ( ! empty( $membership_plan['wps_membership_plan_price'] ) ) {
										/* translators: 1: formatted order total 2: total order items */
										echo sprintf( ' %s %s ', esc_html( get_woocommerce_currency_symbol() ), esc_html( $membership_plan['wps_membership_plan_price'] ) );
									}
									?>

								<?php elseif ( 'members-actions' === $column_id ) : ?>
									<?php

									echo '<a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) . 'wps-membership-tab/?membership=' . $membership_id ) . '" class="woocommerce-button button alt' . esc_attr( $button_disable ) . ' ">' . esc_html__( 'View', 'membership-for-woocommerce' ) . '</a>';
									if ( 'on' == get_option( 'wps_membership_allow_cancel_membership' ) && 'complete' == $membership_status ) {

										echo '<div><button class="button memberhip-cancel-button" data-membership_id="' . esc_html( $membership_id ) . '" >' . esc_html__( 'Cancel', 'membership-for-woocommerce' ) . '</button></div>';
									}

									?>
								<?php endif; ?>
							</td>
						<?php endforeach; ?>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>

	<?php else : ?>
		<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
			<a class="woocommerce-Button button" href="
			<?php
			/**
			 * Filter to shop redirect.
			 *
			 * @since 1.0.0
			 */
			 echo esc_url( apply_filters( 'membership_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) );
			?>
			 "><?php esc_html_e( 'Browse products', 'membership-for-woocommerce' ); ?></a>
			<?php esc_html_e( 'No Membership has been purchased yet.', 'membership-for-woocommerce' ); ?>
		</div>
		<?php
	endif;
}
?>
