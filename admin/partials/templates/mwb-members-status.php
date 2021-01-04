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

// $plan_status = ! empty( $_GET['member_status'] ) ? $_GET['member_status'] : '';

// update_post_meta( $post->ID, 'plan_status', $plan_status );

?>
<ul class="member_status submitbox">

	<li class="wide" id="status">
		<select name="member_status" id="member_status">
			<option value="pending"><?php esc_html_e( 'Pending', 'membership-for-woocommerce' ); ?></option>
			<option value="hold"><?php esc_html_e( 'On-hold', 'membership-for-woocommerce' ); ?></option>
			<option value="complete"><?php esc_html_e( 'Completed', 'membership-for-woocommerce' ); ?></option>
		</select>
	</li>

	<li class="wide">
		<div id="delete-status">
			<a href="<?php echo esc_url( admin_url( 'admin.php?post.php?post=' . $post->ID . '&action=trash' ) ); ?>"><?php esc_html_e( 'Move to trash', 'membership-for-woocommerce' ); ?></a>
		</div>
		<input type="submit" name="save" id="publish" class="button button-primary button-large" value="<?php esc_html_e( 'Update', 'membership-for-woocommerce' ); ?>">
	</li>

</ul>
