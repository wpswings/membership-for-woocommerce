<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for membership using registration form tab.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $mfw_wps_mfw_obj;
?>

<div class="wrap mwb_bfw_config_tab">
	<?php
	if ( class_exists( 'Membership_For_Woocommerce_Admin' ) ) {

		$wps_mfw_sub_tabs_array = $mfw_wps_mfw_obj->wps_mfw_plug_config_sub_tabs();
		$active_sub_tab         = isset( $_REQUEST['mfw_reg_sub_nav'] ) ? sanitize_key( wp_unslash( $_REQUEST['mfw_reg_sub_nav'] ) ) : 'membership-for-woocommerce-add-plans'; // phpcs:ignore
		reset( $wps_mfw_sub_tabs_array );
		$mfw_default_sub_tab = key( $wps_mfw_sub_tabs_array );

		if ( ! isset( $wps_mfw_sub_tabs_array[ $active_sub_tab ] ) ) {
			$active_sub_tab = $mfw_default_sub_tab;
		}
		?>
		<div class="mfw-admin-subtabs" data-default-subtab="<?php echo esc_attr( $active_sub_tab ); ?>" data-active-subtab="<?php echo esc_attr( $active_sub_tab ); ?>">
			<div class="mfw-admin-subtabs__nav" role="tablist" aria-label="<?php esc_attr_e( 'Membership settings sections', 'membership-for-woocommerce' ); ?>">
				<?php foreach ( $wps_mfw_sub_tabs_array as $mwb_sub_tab_title => $taxonomy_slug ) : ?>
					<a
						href="<?php echo esc_url( add_query_arg( array( 'page' => 'membership_for_woocommerce_menu', 'mfw_tab' => 'membership-for-woocommerce-membership-using-registration-form', 'mfw_reg_sub_nav' => $mwb_sub_tab_title ), admin_url( 'admin.php' ) ) ); ?>"
						class="mfw-admin-subtab-link<?php echo $active_sub_tab === $mwb_sub_tab_title ? ' is-active' : ''; ?>"
						data-subtab-target="<?php echo esc_attr( $mwb_sub_tab_title ); ?>"
					>
						<?php echo esc_html( $taxonomy_slug['title'] ); ?>
					</a>
				<?php endforeach; ?>
			</div>

			<div class="mfw-admin-subtabs__content" data-subtab-content>
				<?php $mfw_wps_mfw_obj->wps_mfw_plug_load_template( $wps_mfw_sub_tabs_array[ $active_sub_tab ]['file_path'] ); ?>
			</div>
		</div>
		<?php
	}
	?>
</div>
