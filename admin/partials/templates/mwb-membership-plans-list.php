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

/**
 * This template is for Membership plans list as well as Edit/Update and Delete options.
 */

// Delete membership plan.
if ( isset( $_GET['del_plan_id'] ) ) {

	$membership_plan_id = sanitize_text_field( wp_unslash( $_GET['del_plan_id'] ) );

	// Get all membership plans.
	$mwb_membership_plans = get_option( 'mwb_membership_plans_list' );

	foreach ( $mwb_membership_plans as $single_plan => $data ) {

		if ( $membership_plan_id == $single_plan ) {

			unset( $mwb_membership_plans[ $single_plan ] );
			break;
		}
	}

	update_option( 'mwb_membership_plans_list', $mwb_membership_plans );

	wp_redirect( admin_url( 'admin.php' ) . '?page=membership-for-woocommerce-setting&tab=plans-list' );

	exit;
}

// Get all membership plans list.
$mwb_membership_plans_list = get_option( 'mwb_membership_plans_list' );

?>

<div class="mwb_membership_plans_list">

	<?php if ( empty( $mwb_membership_plans_list ) ) { ?>

		<p class="mwb_membership_no_plans"><?php esc_html_e( 'No Memberhsip plans created', 'membership-for-woocommerce' ); ?></p>

	<?php } ?>



		<table>
			<tr>
				<th><?php esc_html_e( 'Plan(s)', 'membership-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Status', 'membership-for-woocommerce' ); ?></th>
				<th id="mwb_membership_plan_target_th"><?php esc_html_e( 'Target product(s) & categories' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'membership-for-woocommerce' ); ?></th>
			</tr>

			<!-- Membership plans listing start -->
			<?php

			foreach ( $mwb_membership_plans_list as $key => $value ) {

				?>

				<tr>
					<!-- Membership plan name. -->
					<td>
						<a class="mwb_membership_plans_list_name" href="?page=membership-for-woocommerce-setting&tab=plans-create-setting&plan_id=<?php echo esc_html( $key ); ?>"><?php echo esc_html( $value['mwb_membership_plan_name'] ); ?></a>
					</td>

					<!-- Membership plan status -->
					<td>
						<?php

						$membership_plan_status = ! empty( $value['mwb_membership_plan_status'] ) ? $value['mwb_membership_plan_status'] : 'no';

						if ( 'yes' == $membership_plan_status ) {

							echo '<span class="mwb_membership_plans_list_live"></span><span clas="mwb_membership_plans_list_live_name">' . esc_html__( 'Live', 'membership-for-woocommerce' ) . '</span>';

						} else {

							echo '<span class="mwb_membership_plans_list_sandbox"></span><span clas="mwb_membership_plans_list_sandbox_name">' . esc_html__( 'Sandbox', 'membership-for-woocommerce' ) . '</span>';

						}

						?>

					</td>

					<!-- Membership target products -->
					<td>
						<?php

						// Target Products.
						if ( ! empty( $value['mwb_membership_plan_target_ids'] ) ) {

							echo '<div class="mwb_membership_plans_list_targets">';

							foreach ( $value['mwb_membership_plan_target_ids'] as $single_target_product_id ) {
								?>
								<p><?php echo esc_html( mwb_membership_for_woo_get_product_title( $single_target_product_id ) . "( #$single_target_product_id )" ); ?></p>
								<?php
							}

							echo '</div>';

						} else {

							?>
							<p><i><?php esc_html_e( 'No Product(s) added', 'membership-for-woocommerce' ); ?></i></p>
							<?php
						}

						echo '<hr>';

						// Target Categories.

						if ( ! empty( $value['mwb_membership_plan_target_categories'] ) ) {

							echo '<p><i>' . esc_html__( 'Target Categories -', 'membership-for-woocommerce' ) . '</i></p>';

							echo '<div class="mwb_membership_plans_list_targets">';

							foreach ( $value['mwb_membership_plan_target_categories'] as $single_target_category_id ) {

								?>
								<p><?php echo esc_html( mwb_membership_for_woo_get_category_title( $single_target_category_id ) . "( #$single_target_category_id )" ); ?></p>
								<?php
							}

							echo '</div>';

						} else {

							?>
							<p><i><?php esc_html_e( 'No Categories added', 'membership-for-woocommerce' ); ?></i></p>
							<?php
						}

						?>
					</td>

					<!-- Membership plans action -->
					<td>
						<!-- Plans View/Edit link. -->
						<a class="mwb_membership_plans_links" href="?page=membership-for-woocommerce-setting&tab=plans-create-setting&plan_id=<?php echo esc_html( $key ); ?>"><?php esc_html_e( 'View / Edit', 'membership-for-woocommerce' ); ?></a>

						<!-- Plans Delete link. -->
						<a class="mwb_membership_plans_links" href="?page=membership-for-woocommerce-setting&tab=plans-list&del_plan_id=<?php echo esc_html( $key ); ?>"><?php esc_html_e( 'Delete', 'membership-for-woocommerce' ); ?></a>
					</td>
					<?php do_action( 'mwb_membership_for_woo_add_more_col_data' ); ?>
				</tr>
			<?php } ?>
		</table>

</div>

<!-- Create New Membership plan. -->
<div class="mwb_membership_plan_create_new_plan">
	<a href="?page=membership-for-woocommerce-setting&tab=plans-create-setting&plan_id=1" class="mwb_membership_plan_create_button" ><?php esc_html_e( '+Create New Membership Plan', 'membership-for-woocommerce' ); ?></a>
</div>
