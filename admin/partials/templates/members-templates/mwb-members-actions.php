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

// Getting post meta values for actions metabox.
$actions = get_post_meta( $post->ID, 'member_actions', true );

$mem_status = ! empty( $actions['member_status'] ) ? sanitize_text_field( wp_unslash( $actions['member_status'] ) ) : '';
$mem_action = ! empty( $actions['member_actions'] ) ? sanitize_text_field( wp_unslash( $actions['member_actions'] ) ) : '';
//echo '<pre>'; print_r( $actions ); echo '</pre>';

?>
<ul class="member_status submitbox">

	<li class="wide" id="status">
		<label for="member_status"><strong><?php esc_html_e( 'Status', 'membership-for-woocommerce' ); ?></strong></label>
		<select name="member_status" id="member_status">
			<option <?php echo esc_html( 'pending' == $mem_status ? 'selected' : '' ); ?> value="pending"><?php esc_html_e( 'Pending', 'membership-for-woocommerce' ); ?></option>
			<option <?php echo esc_html( 'hold' == $mem_status ? 'selected' : '' ); ?> value="hold"><?php esc_html_e( 'On-hold', 'membership-for-woocommerce' ); ?></option>
			<option <?php echo esc_html( 'complete' == $mem_status ? 'selected' : '' ); ?> value="complete"><?php esc_html_e( 'Completed', 'membership-for-woocommerce' ); ?></option>
		</select>
	</li>

	<li class="wide" id="actions">
		<label for="member_actions"><strong><?php esc_html_e( 'Actions', 'membership-for-woocommerce' ); ?></strong></label>
		<select name="member_actions" id="member_actions">
			<option <?php echo esc_html( 'email' == $mem_action ? 'selected' : '' ); ?> value="email_invoice"><?php esc_html_e( 'Email invoice to customer', 'membership-for-woocommerce' ); ?></option>
			<option <?php echo esc_html( 'resend_notif' == $mem_action ? 'selected' : '' ); ?> value="resend_notif"><?php esc_html_e( 'Resend invoice', 'membership-for-woocommerce' ); ?></option>
		</select>
	</li>

	<li class="wide">
		<div id="delete-member-action">
			<a href="<?php echo esc_url( admin_url( 'admin.php?post.php?post=' . $post->ID . '&action=trash' ) ); ?>"><?php esc_html_e( 'Move to trash', 'membership-for-woocommerce' ); ?></a>
		</div>
		<input type="submit" name="save" id="publish" class="button button-primary button-large" value="<?php esc_html_e( 'Update', 'membership-for-woocommerce' ); ?>">
	</li>

</ul>
