<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html for system status.
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
// Template for showing information about system status.
global $mfw_wps_mfw_obj;
$mfw_default_status = $mfw_wps_mfw_obj->wps_mfw_plug_system_status();
$mfw_wordpress_details = is_array( $mfw_default_status['wp'] ) && ! empty( $mfw_default_status['wp'] ) ? $mfw_default_status['wp'] : array();
$mfw_php_details = is_array( $mfw_default_status['php'] ) && ! empty( $mfw_default_status['php'] ) ? $mfw_default_status['php'] : array();
?>
<div class="wps-mfw-table-wrap">
	<div class="wps-col-wrap">
		<div id="wps-mfw-table-inner-container" class="table-responsive ">
			<div class="mdc-data-table__table-container">
				<table class="wps-mfw-table mdc-data-table__table wps-table" id="wps-mfw-wp">
					<thead>
						<tr>
							<th class="mdc-data-table__header-cell"><?php esc_html_e( 'WP Variables', 'membership-for-woocommerce' ); ?></th>
							<th class="mdc-data-table__header-cell"><?php esc_html_e( 'WP Values', 'membership-for-woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody class="mdc-data-table__content">
						<?php if ( is_array( $mfw_wordpress_details ) && ! empty( $mfw_wordpress_details ) ) { ?>
							<?php foreach ( $mfw_wordpress_details as $wp_key => $wp_value ) { ?>
								<?php if ( isset( $wp_key ) && 'wp_users' != $wp_key ) { ?>
									<tr class="mdc-data-table__row">
										<td class="mdc-data-table__cell"><?php echo esc_html( $wp_key ); ?></td>
										<td class="mdc-data-table__cell"><?php echo esc_html( $wp_value ); ?></td>
									</tr>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="wps-col-wrap">
		<div id="wps-mfw-table-inner-container" class="table-responsive ">
			<div class="mdc-data-table__table-container">
				<table class="wps-mfw-table mdc-data-table__table wps-table" id="wps-mfw-sys">
					<thead>
						<tr>
							<th class="mdc-data-table__header-cell"><?php esc_html_e( 'System Variables', 'membership-for-woocommerce' ); ?></th>
							<th class="mdc-data-table__header-cell"><?php esc_html_e( 'System Values', 'membership-for-woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody class="mdc-data-table__content">
						<?php if ( is_array( $mfw_php_details ) && ! empty( $mfw_php_details ) ) { ?>
							<?php foreach ( $mfw_php_details as $php_key => $php_value ) { ?>
								<tr class="mdc-data-table__row">
									<td class="mdc-data-table__cell"><?php echo esc_html( $php_key ); ?></td>
									<td class="mdc-data-table__cell"><?php echo esc_html( $php_value ); ?></td>
								</tr>
							<?php } ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
