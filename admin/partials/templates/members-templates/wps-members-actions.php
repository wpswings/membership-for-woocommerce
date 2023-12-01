<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
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

$mem_status = ! empty( $status ) ? sanitize_text_field( wp_unslash( $status ) ) : '';
$mem_action = ! empty( $actions ) ? sanitize_text_field( wp_unslash( $actions ) ) : '';
$plan_id    = ! empty( $plan_id ) ? sanitize_text_field( wp_unslash( $plan_id ) ) : '';

?>
<ul class="member_status submitbox">

	<li class="wide" id="status">
		<label for="member_status"><strong><?php esc_html_e( 'Status', 'membership-for-woocommerce' ); ?></strong></label>
		<select name="member_status" id="member_status">
			<option <?php echo esc_html( 'pending' === $mem_status ? 'selected' : '' ); ?> value="pending"><?php esc_html_e( 'Pending', 'membership-for-woocommerce' ); ?></option>
			<option <?php echo esc_html( 'hold' === $mem_status ? 'selected' : '' ); ?> value="hold"><?php esc_html_e( 'On-hold', 'membership-for-woocommerce' ); ?></option>
			<option <?php echo esc_html( 'complete' === $mem_status ? 'selected' : '' ); ?> value="complete"><?php esc_html_e( 'Completed', 'membership-for-woocommerce' ); ?></option>
			<option <?php echo esc_html( 'cancelled' === $mem_status ? 'selected' : '' ); ?> value="cancelled"><?php esc_html_e( 'Cancelled', 'membership-for-woocommerce' ); ?></option>
		</select>
		<input type="hidden" name="members_plan_assign" value="<?php echo esc_html( $plan_id ); ?>">
	</li>


	<li class="wide">
		<div id="delete-member-action">
		<?php
		if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
			$check_licence = check_membership_pro_plugin_is_active();
			if ( $check_licence ) {
				?>
				<?php
			}
		}
		?>
		</div>
		<input type="submit" name="save" id="publish" class="button button-primary button-large" value="<?php esc_html_e( 'Update', 'membership-for-woocommerce' ); ?>">
	</li>

</ul>
