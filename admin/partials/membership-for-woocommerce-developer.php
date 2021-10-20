<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to list all the hooks and filter with their descriptions.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $mfw_mwb_mfw_obj;
$mfw_developer_admin_hooks =
// desc - filter for trial.
apply_filters( 'mfw_developer_admin_hooks_array', array() );
$count_admin = filtered_array( $mfw_developer_admin_hooks );
$mfw_developer_public_hooks =
// desc - filter for trial.
apply_filters( 'mfw_developer_public_hooks_array', array() );
$count_public = filtered_array( $mfw_developer_public_hooks );
?>
<!--  template file for admin settings. -->
<div class="mfw-section-wrap">
	<div class="mwb-col-wrap">
		<div id="admin-hooks-listing" class="table-responsive ">
			<table class="mwb-mfw-table mdc-data-table__table mwb-table"  id="mwb-mfw-wp">
				<thead>
				<tr><th class="mdc-data-table__header-cell"><?php esc_html_e( 'Admin Hooks', 'membership-for-woocommerce' ); ?></th></tr>
				<tr>
					<th class="mdc-data-table__header-cell"><?php esc_html_e( 'Type of Hook', 'membership-for-woocommerce' ); ?></th>
					<th class="mdc-data-table__header-cell"><?php esc_html_e( 'Hooks', 'membership-for-woocommerce' ); ?></th>
					<th class="mdc-data-table__header-cell"><?php esc_html_e( 'Hooks description', 'membership-for-woocommerce' ); ?></th>
				</tr>
				</thead>
				<tbody class="mdc-data-table__content">
				<?php
				if ( ! empty( $count_admin ) ) {
					foreach ( $count_admin as $k => $v ) {
						if ( isset( $v['action_hook'] ) ) {
							?>
						<tr class="mdc-data-table__row"><td class="mdc-data-table__cell"><?php esc_html_e( 'Action Hook', 'membership-for-woocommerce' ); ?></td><td class="mdc-data-table__cell"><?php echo esc_html( $v['action_hook'] ); ?></td><td class="mdc-data-table__cell"><?php echo esc_html( $v['desc'] ); ?></td></tr>
							<?php
						} else {
							?>
							<tr class="mdc-data-table__row"><td class="mdc-data-table__cell"><?php esc_html_e( 'Filter Hook', 'membership-for-woocommerce' ); ?></td><td class="mdc-data-table__cell"><?php echo esc_html( $v['filter_hook'] ); ?></td><td class="mdc-data-table__cell"><?php echo esc_html( $v['desc'] ); ?></td></tr>
							<?php
						}
					}
				} else {
					?>
					<tr class="mdc-data-table__row"><td><?php esc_html_e( 'No Hooks Found', 'membership-for-woocommerce' ); ?><td></tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="mwb-col-wrap">
		<div id="public-hooks-listing" class="table-responsive ">
			<table class="mwb-mfw-table mdc-data-table__table mwb-table" id="mwb-mfw-sys">
				<thead>
				<tr><th class="mdc-data-table__header-cell"><?php esc_html_e( 'Public Hooks', 'membership-for-woocommerce' ); ?></th></tr>
				<tr>
					<th class="mdc-data-table__header-cell"><?php esc_html_e( 'Type of Hook', 'membership-for-woocommerce' ); ?></th>
					<th class="mdc-data-table__header-cell"><?php esc_html_e( 'Hooks', 'membership-for-woocommerce' ); ?></th>
					<th class="mdc-data-table__header-cell"><?php esc_html_e( 'Hooks description', 'membership-for-woocommerce' ); ?></th>
				</tr>
				</thead>
				<tbody class="mdc-data-table__content">
				<?php
				if ( ! empty( $count_public ) ) {
					foreach ( $count_public as $k => $v ) {
						if ( isset( $v['action_hook'] ) ) {
							?>
						<tr class="mdc-data-table__row"><td class="mdc-data-table__cell"><?php esc_html_e( 'Action Hook', 'membership-for-woocommerce' ); ?></td><td class="mdc-data-table__cell"><?php echo esc_html( $v['action_hook'] ); ?></td><td class="mdc-data-table__cell"><?php echo esc_html( $v['desc'] ); ?></td></tr>
							<?php
						} else {
							?>
							<tr class="mdc-data-table__row"><td class="mdc-data-table__cell"><?php esc_html_e( 'Filter Hook', 'membership-for-woocommerce' ); ?></td><td class="mdc-data-table__cell"><?php echo esc_html( $v['filter_hook'] ); ?></td><td class="mdc-data-table__cell"><?php echo esc_html( $v['desc'] ); ?></td></tr>
							<?php
						}
					}
				} else {
					?>
					<tr class="mdc-data-table__row"><td><?php esc_html_e( 'No Hooks Found', 'membership-for-woocommerce' ); ?><td></tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php
$mwb_tracking_fields_array = apply_filters(
	'mwb_tracking_fields_array',
	array(
		array(
			'title' => __( 'Enable Tracking', 'membership-for-woocommerce' ),
			'type'  => 'radio-switch',
			'description'  => __( 'Allow usage of this plugin to be tracked', 'membership-for-woocommerce' ),
			'id'    => 'mfw_enable_tracking',
			'value' => get_option( 'mfw_enable_tracking' ),
			'class' => 'mfw-radio-switch-class',
			'options' => array(
				'yes' => __( 'YES', 'membership-for-woocommerce' ),
				'no' => __( 'NO', 'membership-for-woocommerce' ),
			),
		),
		array(
			'type'  => 'button',
			'id'    => 'mfw_button_demo',
			'button_text' => __( 'Save', 'membership-for-woocommerce' ),
			'class' => 'mfw-button-class',
		),
	)
);
?>
<form action="" method="POST" class="mwb-mfw-gen-section-form">
	<div class="mfw-secion-wrap">
		<?php
		$mfw_tracking_html = $mfw_mwb_mfw_obj->mwb_mfw_plug_generate_html( $mwb_tracking_fields_array );
		echo esc_html( $mfw_tracking_html );
		wp_nonce_field( 'admin_save_data', 'mwb_tabs_nonce' );
		?>
	</div>
</form>


<?php
/**
 * Function to filter array.
 *
 * @param [type] $argu is the argument.
 * @return array
 */
function filtered_array( $argu ) {
	$count_admin = array();
	foreach ( $argu as $key => $value ) {
		foreach ( $value as $k => $originvalue ) {
			if ( isset( $originvalue['action_hook'] ) ) {
				$val = str_replace( ' ', '', $originvalue['action_hook'] );
				$val = str_replace( "do_action('", '', $val );
				$val = str_replace( "');", '', $val );
				$count_admin[ $k ]['action_hook'] = $val;
			}
			if ( isset( $originvalue['filter_hook'] ) ) {
				$val = str_replace( ' ', '', $originvalue['filter_hook'] );
				$val = str_replace( "apply_filters('", '', $val );
				$val = str_replace( "',array());", '', $val );
				$count_admin[ $k ]['filter_hook'] = $val;
			}
			$vale = str_replace( '//desc - ', '', $originvalue['desc'] );
			$count_admin[ $k ]['desc'] = $vale;
		}
	}
	return $count_admin;
}
