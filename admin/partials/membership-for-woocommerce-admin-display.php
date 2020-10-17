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

if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

$mwb_membership_active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'plans-list';

do_action( 'mwb_membership_for_woo_tab_active' );

?>
<div class="wrap woocommerce" id="mwb_membership_setting_wrapper">
	<div class="mwb_membership_setting_title">
		<?php echo esc_html( apply_filters( 'mwb_membership_heading', esc_html( 'Membership for Woocommerce', 'membership-for-woocommerce' ) ) ); ?>
		<span class="mwb_membership_setting_version">
			<?php
				esc_html_e( 'v', 'membership-for-woocommerce' );
				echo esc_html( MEMBERSHIP_FOR_WOOCOMMERCE_VERSION );
			?>
		</span>
	</div>

	<nav class="nav-tab-wrapper woo-nav-tab-wrapper">

		<a class="nav-tab <?php echo esc_html( 'plans-create-setting' === $mwb_membership_active_tab ? 'nav-tab-active' : '' ); ?>" href="?page=membership-for-woocommerce-setting&tab=plans-create-setting"><?php esc_html_e( 'Create Membership Plans', 'membership-for-woocommerce' ); ?></a>

		<a class="nav-tab <?php echo esc_html( 'plans-list' === $mwb_membership_active_tab ? 'nav-tab-active' : '' ); ?>" href="?page=membership-for-woocommerce-setting&tab=plans-list"><?php esc_html_e( 'Membership Plans', 'membership-for-woocommerce' ); ?></a>

		<a class="nav-tab <?php echo esc_html( 'global-setting' === $mwb_membership_active_tab ? 'nav-tab-active' : '' ); ?>" href="?page=membership-for-woocommerce-setting&tab=global-setting"><?php esc_html_e( 'Global Settings', 'membership-for-woocommerce' ); ?></a>

		<a class="nav-tab <?php echo esc_html( 'overview' === $mwb_membership_active_tab ? 'nav-tab-active' : '' ); ?>" href="?page=membership-for-woocommerce-setting&tab=overview"><?php esc_html_e( 'Overview', 'membership-for-woocommerce' ); ?></a>

		<?php do_action( 'mwb_membership_for_woo_setting_tab' ); ?>

	</nav>

	<?php

	if ( 'plans-create-setting' === $mwb_membership_active_tab ) {

		include_once 'templates/mwb-membership-plans-creation.php';

	} elseif ( 'plans-list' === $mwb_membership_active_tab ) {

		include_once 'templates/mwb-membership-plans-list.php';

	} elseif ( 'global-setting' === $mwb_membership_active_tab ) {

		include_once 'templates/mwb-membership-global-settings.php';

	} elseif ( 'overview' === $mwb_membership_active_tab ) {

		include_once 'templates/mwb-membership-overview.php';

	}

	do_action( 'mwb_membership_for_woo_setting_tab_html' );
	?>

</div>

<!-- Connect us on skype. -->
<div id="mwb_membership_for_woo_skype_connect_with_us">   
	<div class="mwb_membership_for_woo_skype_connect_title"><?php esc_html_e( 'Connect with Us in one click', 'membership-for-woocommerce' ); ?></div>

	<a class="button" target="_blank" href="https://join.skype.com/invite/IKVeNkLHebpC"><img src="<?php echo esc_url( MEMBERSHIP_FOR_WOOCOMMERCE_URL . 'admin/resources/logo/skype_logo.png' ); ?>"><?php esc_html_e( 'Connect', 'membership-for-woocommerce' ); ?></a>

	<p><?php esc_html_e( 'Regarding any issue, query or feature request for Order Bump Offers.', 'membership-for-woocommerce' ); ?></p>
	<div class="mwb_membership_for_woo_skype_setting"><span class="dashicons dashicons-admin-generic"></span></div>
</div>
