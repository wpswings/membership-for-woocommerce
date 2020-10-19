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

?>

<div class="mwb_membership_plans_list">

	<table>
		<tr>
			<th><?php esc_html_e( 'Plan(s)', 'membership-for-woocommerce' ); ?></th>
			<th><?php esc_html_e( 'Status', 'membership-for-woocommerce' ); ?></th>
			<th id="mwb_membership_plan_target_th"><?php esc_html_e( 'Target product(s) & categories' ); ?></th>
			<th><?php esc_html_e( 'Offers', 'membership-for-woocommerce' ); ?></th>
			<th><?php esc_html_e( 'Actions', 'membership-for-woocommerce' ); ?></th>
		</tr>
	</table>

</div>
