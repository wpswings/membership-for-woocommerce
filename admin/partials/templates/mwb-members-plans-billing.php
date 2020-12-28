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

$member_details = get_post_meta( $post->ID, 'billing_details' );

//echo '<pre>'; print_r( $member_details ); echo '</pre>';
?>

<!-- Members billing metabox start -->
<div class="members_billing_details">

	<h1><?php echo sprintf( 'Member #%u details', esc_html( $post->ID ) ); ?></h1>

	<div class="members_data_column_container">
		<div class="members_data_column">
			<h3><?php esc_html_e( 'General', 'membership-for-woocommerce' ); ?></h3>

			<p class="form-field membership-customer">
`				<label for="member-user">
					<?php esc_html_e( 'Customer:', 'membership-for-woocommerce' ); ?>
					<a href="<?php echo esc_html( admin_url( 'admin.php?edit.php?post_status=all&post_type=mwb_cpt_members' ) ); ?>" target="_blank"><?php esc_html_e( 'View other memberships', 'membership-for-woocommerce' ); ?></a>
					<a href="<?php echo esc_html( admin_url( 'user-edit.php?user_id=' ) ) ?>" target="_blank"><?php esc_html_e( 'Profile', 'membership-for-woocommerce' ); ?></a>
				</label>
			</p>
		</div>

	</div>

</div>
<!-- Members billing metabox end -->