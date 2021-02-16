<?php
/**
 * Membership details/History
 *
 * Shows Membership details on the account page.
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

$current_url = ! empty( $_GET['membership'] ) ? $_GET['membership'] : '';

if ( ! isset( $_GET['membership'] ) ) {

	$user        = ! empty( $user_id ) ? $user_id : '';
	$memberships = ! empty( $membership_ids ) ? $membership_ids : array();




	if ( $memberships ) : ?>

		<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
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
					$membership_plan   = get_post_meta( $membership_id, 'plan_obj', true );
					$membership_status = get_post_meta( $membership_id, 'member_status', true );

					?>
					<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr( $membership_status ); ?> order">
						<?php foreach ( $instance->membership_tab_headers() as $column_id => $column_name ) : ?>
							<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">


								<?php if ( 'members-id' === $column_id ) : ?>
									<a href="javascript:void(0)">
										<?php echo esc_html( _x( '#', 'hash before member id', 'membership-for-woocommerce' ) . $membership_id ); ?>
									</a>

								<?php elseif ( 'members-date' === $column_id ) : ?>
									<time datetime="<?php echo esc_attr( get_the_date( 'j F Y', $membership_id ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y', $membership_id ) ); ?></time>

								<?php elseif ( 'members-status' === $column_id ) : ?>
									<?php echo esc_html( ucwords( $membership_status ) ); ?>

								<?php elseif ( 'members-total' === $column_id ) : ?>
									<?php
									if ( ! empty( $membership_plan['mwb_membership_plan_price'] ) ) {
										/* translators: 1: formatted order total 2: total order items */
										echo sprintf( ' %s %s ', esc_html( get_woocommerce_currency_symbol() ), esc_html( $membership_plan['mwb_membership_plan_price'] ) );
									}
									?>

								<?php elseif ( 'members-actions' === $column_id ) : ?>
									<?php

									echo '<a href=" ' . esc_url( wc_get_page_permalink( 'myaccount' ) . 'mwb-membership-tab/?membership= ' . $membership_id ) . '" class="woocommerce-button button">' . esc_html( 'View' ) . '</a>';

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
			<a class="woocommerce-Button button" href="<?php echo esc_url( apply_filters( 'membership_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>"><?php esc_html_e( 'Browse products', 'membership-for-woocommerce' ); ?></a>
			<?php esc_html_e( 'No Membership has been purchased yet.', 'membership-for-woocommerce' ); ?>
		</div>
		<?php
	endif;

} elseif ( isset( $_GET['membership'] ) ) {

	$membership_id = ! empty( $_GET['membership'] ) ? $_GET['membership'] : '';

	$membership_data = get_post_meta( $membership_id );
	$membership_plan = get_post_meta( $membership_id, 'plan_obj', true );

	$membership_status  = get_post_meta( $membership_id, 'member_status', true );
	$membership_billing = get_post_meta( $membership_id, 'billing_details', true );


	?>
	<p><?php echo esc_html( 'Membership plan ', 'membership-for-woocommerce' ) . '#'; ?><mark class="order-number"><?php echo esc_html( $membership_id ); ?></mark><?php esc_html_e( ' was placed on ', 'membership-for-woocommerce' ); ?> <mark class="order-date"><?php echo esc_html( get_the_date( 'j F Y', $membership_id ) ); ?></mark><?php esc_html_e( ' and is currently ', 'membership-for-woocommerce' ); ?><mark class="order-status"><?php echo esc_html( ucwords( $membership_status ) ); ?></mark>.</p>

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
					<td class="woocommerce-table__product-name product-name">
						<a href="javascript:void(0)" ><?php echo esc_html( $membership_plan['post_name'] ); ?> </a> <strong class="product-quantity">&times;&nbsp;<?php esc_html( 1 ); ?></strong>	</td>
					<td class="woocommerce-table__product-total product-total">
						<span class="woocommerce-Price-amount amount"><?php echo sprintf( ' %s %s ', esc_html( get_woocommerce_currency_symbol() ), esc_html( $membership_plan['mwb_membership_plan_price'] ) ); ?></span></td>
				</tr>
			</tbody>

			<tfoot>

				<tr>
					<th scope="row"><?php esc_html_e( 'Payment method', 'membership-for-woocommerce' ); ?></th>
					<td><?php echo esc_html( $membership_billing['payment_method'] ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Total', 'membership-for-woocommerce' ); ?></th>
					<td><span class="woocommerce-Price-amount amount"><?php echo sprintf( ' %s %s ', esc_html( get_woocommerce_currency_symbol() ), esc_html( $membership_plan['mwb_membership_plan_price'] ) ); ?></span></td>
				</tr>
			</tfoot>
		</table>

	</section>

	<section class="woocommerce-customer-details">

		<section class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses col2-set addresses">
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
					<?php echo sprintf( ' %s %s ', esc_html__( 'Plan Name : ', 'membership-for-woocommerce' ), esc_html( $membership_plan['post_title'] ) ); ?></br>
					<?php echo sprintf( ' %s %s ', esc_html__( 'Status : ', 'membership-for-woocommerce' ), esc_html( ucwords( $membership_status ) ) ); ?></br>
					<?php echo sprintf( ' %s %u %s ', esc_html__( 'Discount on cart : ', 'membership-for-woocommerce' ), esc_html( $membership_plan['mwb_memebership_plan_discount_price'] ), esc_html( $membership_plan['mwb_membership_plan_offer_price_type'] ) ); ?></br>
					<?php echo sprintf( ' %s %s ', esc_html__( 'Free Shipping : ', 'membership-for-woocommerce' ), esc_html( ! empty( $membership_plan['mwb_memebership_plan_free_shipping'] ) ? 'Yes' : 'No' ) ); ?></br>
					<tr>
						<th><label><?php esc_html_e( 'Offered Products : ', 'membership-for-woocommerce' ); ?></label></th>
						<td>
							<?php
							$prod_ids = maybe_unserialize( $membership_plan['mwb_membership_plan_target_ids'] );

							if ( ! empty( $prod_ids ) && is_array( $prod_ids ) ) {
								foreach ( $prod_ids as $ids ) {
									echo( esc_html( $instance->get_product_title( $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
								}
							} else {
								esc_html_e( 'No Products Offered in this Plan', 'membership-for-woocommerce' );
							}
							?>
						</td>
					</tr></br>

					<tr>
						<th><label><?php esc_html_e( 'Offered Categories : ', 'membership-for-woocommerce' ); ?></label></th>
						<td>
							<?php
							$cat_ids = maybe_unserialize( $membership_plan['mwb_membership_plan_target_categories'] );

							if ( ! empty( $cat_ids ) && is_array( $cat_ids ) ) {
								foreach ( $cat_ids as $ids ) {
									echo( esc_html( $instance->get_category_title( $ids ) ) . '(#' . esc_html( $ids ) . ') ' );
								}
							} else {
								esc_html_e( 'No Categories Offered in this Plan', 'membership-for-woocommerce' );
							}
							?>
						</td>
					</tr>
				</address>
			</div>

		</section>

	</section>
	<?php
}
?>