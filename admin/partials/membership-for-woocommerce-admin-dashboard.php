<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link  https://makewebbetter.com/
 * @since 1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit(); // Exit if accessed directly.
}

global $mfw_mwb_mfw_obj;




$mfw_active_tab   = isset( $_GET['mfw_tab'] ) ? sanitize_key( $_GET['mfw_tab'] ) : 'membership-for-woocommerce-general';
$mfw_default_tabs = $mfw_mwb_mfw_obj->mwb_mfw_plug_default_tabs();
$plugin_name = $mfw_mwb_mfw_obj->mfw_get_plugin_name();
if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
	$check_licence = check_membership_pro_plugin_is_active();
	if ( $check_licence ) {
		$plugin_name = $plugin_name . '-pro';

	}
}

do_action( 'mwb_mfw_before_general_settings_tab_setting', $mfw_active_tab, $mfw_default_tabs );

?>
<header>
	<?php
		// desc - This hook is used for trial.
		do_action( 'mwb_mfw_settings_saved_notice' );
	?>
	<div class="mwb-header-container mwb-bg-white mwb-r-8">
		<h1 class="mwb-header-title"><?php echo esc_attr( strtoupper( str_replace( '-', ' ', $plugin_name ) ) ); ?>	
		</h1>
		<a href="https://docs.makewebbetter.com/membership-for-woocommerce/?utm_source=MWB-membership-backend&utm_medium=MWB-ORG-Page&utm_campaign=MWB-doc" target="_blank" class="mwb-link"><?php esc_html_e( 'Documentation', 'membership-for-woocommerce' ); ?></a>
		<span>|</span>
		<a href="https://support.makewebbetter.com/wordpress-plugins-knowledge-base/category/membership-for-woocommerce/?utm_source=MWB-membership-backend&utm_medium=MWB-ORG-Page&utm_campaign=MWB-support" target="_blank" class="mwb-link"><?php esc_html_e( 'Support', 'membership-for-woocommerce' ); ?></a>
	</div>
</header>
<main class="mwb-main mwb-bg-white mwb-r-8">
	<nav class="mwb-navbar">
		<ul class="mwb-navbar__items">
			<?php
			if ( is_array( $mfw_default_tabs ) && ! empty( $mfw_default_tabs ) ) {
				foreach ( $mfw_default_tabs as $mfw_tab_key => $mfw_default_tabs ) {

					$mfw_tab_classes = 'mwb-link ';
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
	<section class="mwb-section">
		<div>
			<?php
				// desc - This hook is used for trial.
				do_action( 'mwb_mfw_before_general_settings_form' );
				// if submenu is directly clicked on woocommerce.
			if ( empty( $mfw_active_tab ) ) {
				$mfw_active_tab = 'mwb_mfw_plug_general';
			}

				// look for the path based on the tab id in the admin templates.
				$mfw_default_tabs = $mfw_mwb_mfw_obj->mwb_mfw_plug_default_tabs();
				$mfw_tab_content_path = $mfw_default_tabs[ $mfw_active_tab ]['file_path'];
				$mfw_mwb_mfw_obj->mwb_mfw_plug_load_template( $mfw_tab_content_path );
				// desc - This hook is used for trial.
				do_action( 'mwb_mfw_after_general_settings_form' );
			?>
		</div>
	</section>
