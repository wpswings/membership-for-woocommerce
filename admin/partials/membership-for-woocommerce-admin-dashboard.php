<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link  https://wpswings.com/
 * @since 1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit();
}

global $mfw_wps_mfw_obj;

if ( ! wps_mfw_standard_check_multistep() ) {
	?>
	<div id="react-app"></div>
	<?php
	return;
}

$mfw_active_tab   = isset( $_GET['mfw_tab'] ) ? sanitize_key( $_GET['mfw_tab'] ) : 'membership-for-woocommerce-general';
$mfw_default_tabs = $mfw_wps_mfw_obj->wps_mfw_plug_default_tabs();
$plugin_name = $mfw_wps_mfw_obj->mfw_get_plugin_name();
if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
	$check_licence = check_membership_pro_plugin_is_active();
	if ( $check_licence ) {
		$plugin_name = $plugin_name . '-pro';

	}
}

/**
 * Action for before general setting.
 *
 * @since 1.0.0
 */
do_action( 'wps_mfw_before_general_settings_tab_setting', $mfw_active_tab, $mfw_default_tabs );

?>
<header>
	<?php

	/**
	 * Action for setting save.
	 *
	 * @since 1.0.0
	 */
	do_action( 'wps_mfw_settings_saved_notice' );
	?>
	<div class="wps-header-container wps-bg-white wps-r-8">
		<h1 class="wps-header-title"><?php echo esc_attr( strtoupper( str_replace( '-', ' ', $plugin_name ) ) ); ?>	
		</h1>
		<a href="https://docs.wpswings.com/membership-for-woocommerce/?utm_source=wpswings-membership-doc&utm_medium=membership-org-backend&utm_campaign=documentation" target="_blank" class="wps-link"><?php esc_html_e( 'Documentation', 'membership-for-woocommerce' ); ?></a>
		<span>|</span>
		<a href="https://wpswings.com/submit-query/?utm_source=wpswings-membership-support&utm_medium=membership-org-backend&utm_campaign=support" target="_blank" class="wps-link"><?php esc_html_e( 'Support', 'membership-for-woocommerce' ); ?></a>
		<span>|</span>
		<a href="https://wa.me/message/JSDF7KNKMUSKA1" target="_blank" class="wps-link"><img src="<?php echo esc_url( MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/whatsapp.png' ); ?>" height="26" width="26"></a>
		<span>|</span>
		<div><b><?php echo esc_html( 'v' . MEMBERSHIP_FOR_WOOCOMMERCE_VERSION ); ?></b></div>
	</div>
</header>

<main class="wps-main wps-bg-white wps-r-8">
	<nav class="wps-navbar">
		<ul class="wps-navbar__items">
			<?php
			if ( is_array( $mfw_default_tabs ) && ! empty( $mfw_default_tabs ) ) {
				foreach ( $mfw_default_tabs as $mfw_tab_key => $mfw_default_tabs ) {

					$mfw_tab_classes = 'wps-link ';
					if ( ! empty( $mfw_active_tab ) && $mfw_active_tab === $mfw_tab_key ) {
						$mfw_tab_classes .= 'active';
					}
					?>
					<li>
						<a id="<?php echo esc_attr( $mfw_tab_key ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=membership_for_woocommerce_menu' ) . '&mfw_tab=' . esc_attr( $mfw_tab_key ) ); ?>" class="<?php echo esc_attr( $mfw_tab_classes ); ?>"><?php echo esc_html( $mfw_default_tabs['title'] ); ?></a>
					</li>
					<?php
				}
			}
			?>
		</ul>
	</nav>
	<section class="wps-section">
		<div>
			<?php

			/**
			 * Action for before genral setting.
			 *
			 * @since 1.0.0
			 */
			do_action( 'wps_mfw_before_general_settings_form' );

			// if submenu is directly clicked on woocommerce.
			if ( empty( $mfw_active_tab ) ) {

				$mfw_active_tab = 'wps_mfw_plug_general';
			}

			// look for the path based on the tab id in the admin templates.
			$mfw_default_tabs     = $mfw_wps_mfw_obj->wps_mfw_plug_default_tabs();
			$mfw_tab_content_path = isset( $mfw_default_tabs[ $mfw_active_tab ] ) ? $mfw_default_tabs[ $mfw_active_tab ]['file_path'] : $mfw_default_tabs[ 'membership-for-woocommerce-general' ]['file_path'];
			$mfw_wps_mfw_obj->wps_mfw_plug_load_template( $mfw_tab_content_path );

			/**
			 * Action for general setting form.
			 *
			 * @since 1.0.0
			 */
			do_action( 'wps_mfw_after_general_settings_form' );
			?>
		</div>
	</section>
</main>
