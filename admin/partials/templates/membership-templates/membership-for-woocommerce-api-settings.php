<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for general tab.
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
$mfw_genaral_settings = apply_filters( 'mfw_api_settings_array', array() );
?>

<div class="mfw-api-settings-view">
	<section class="mfw-api-settings-view__config">
		<div class="mfw-api-settings-view__config-head">
			<h3><?php esc_html_e( 'API Configuration', 'membership-for-woocommerce' ); ?></h3>
			<p><?php esc_html_e( 'Enable API access, manage your consumer secret, and prepare credentials for external integrations.', 'membership-for-woocommerce' ); ?></p>
		</div>

		<!--  template file for admin settings. -->
		<form action="" method="POST" class="wps-mfw-gen-section-form mfw-api-settings-view__form">
			<div class="mfw-secion-wrap">
			<?php
				do_action( 'mfw_api_settings_before' );
			?>
			<div class="wps-sm__modal"></div>
				<?php
				$mfw_general_html = $mfw_wps_mfw_obj->wps_mfw_plug_generate_html( $mfw_genaral_settings );
				echo esc_html( $mfw_general_html );
				wp_nonce_field( 'admin_save_data', 'wps_tabs_nonce' );
				?>
			</div>
		</form>
	</section>

	<!-- ======== List all API details for admin ========== -->
	<section class="wps_msfw_parent_api_details_wrapper mfw-api-docs">
		<div class="mfw-api-docs__header">
			<h3><?php esc_html_e( 'Plugin API Details', 'membership-for-woocommerce' ); ?></h3>
			<p><?php esc_html_e( 'Use these endpoints to retrieve membership records and user membership information.', 'membership-for-woocommerce' ); ?></p>
		</div>

		<!-- Show Authentication -->
		<div class="mfw-api-docs__block">
			<h4><?php esc_html_e( 'Authentication', 'membership-for-woocommerce' ); ?></h4>
			<div class="wps_msfw_rest_api_response">
				<p>
					<?php
					esc_html_e( 'For authentication you need ', 'membership-for-woocommerce' );
					esc_html_e( ' Consumer Secret ', 'membership-for-woocommerce' );
					echo '<strong>{consumer_secret}</strong>';
					esc_html_e( ' keys. Response on wrong api details:', 'membership-for-woocommerce' );
					?>
				</p>
				<pre>{
"code": "rest_forbidden",
"message": "Sorry, you are not allowed to do that.",
"data": {
	"status": 401
}
}</pre>
			</div>
		</div>

		<!-- To get user points -->
		<div class="mfw-api-docs__block">
			<h4><?php esc_html_e( 'To Retrieve Membership Offers', 'membership-for-woocommerce' ); ?></h4>
			<div class="wps_msfw_rest_api_response">
				<p>
					<?php
					echo '<strong>' . esc_html__( 'Base Url to get membership offers : ', 'membership-for-woocommerce' ) . '</strong>';
					echo '{site_url}/wp-json/wps-mfw/get-membership-offers';
					?>
				</p>
				<p>
					<strong>
					<?php
					esc_html_e( 'Example : ', 'membership-for-woocommerce' );
					echo esc_html( site_url() );
					esc_html_e( '/wp-json/wps-mfw/get-membership-offers', 'membership-for-woocommerce' );
					?>
					</strong>
				</p>
				<p>
					<?php
					esc_html_e( 'Parameters Required : ', 'membership-for-woocommerce' );
					echo '<strong> {consumer_secret}</strong>';
					?>
				</p>
				<p><?php esc_html_e( 'JSON response example:', 'membership-for-woocommerce' ); ?></p>
				<pre>{
	"status": "success",
	"code": 200,
	"data": [
		{
			"membership_id": 71,
			"membership_name": "Silver",
			"plan_type": "lifetime",
			"plan_price": "15",
		},
		{
			"membership_id": 72,
			"membership_name": "Gold",
			"plan_type": "limited",
			"plan_price": "20",
			"plan_duration": "2 years"
		},
	]
}</pre>
			</div>
		</div>

		<div class="mfw-api-docs__block">
			<h4><?php esc_html_e( 'To Retrieve Particular User Membership', 'membership-for-woocommerce' ); ?></h4>
			<div class="wps_msfw_rest_api_response">
				<p>
					<?php
					echo '<strong>' . esc_html__( 'Base Url to get membership offers : ', 'membership-for-woocommerce' ) . '</strong>';
					echo '{site_url}/wp-json/wps-mfw/get-user-membership';
					?>
				</p>
				<p>
					<strong>
					<?php
					esc_html_e( 'Example : ', 'membership-for-woocommerce' );
					echo esc_html( site_url() );
					esc_html_e( '/wp-json/wps-mfw/get-user-membership', 'membership-for-woocommerce' );
					?>
					</strong>
				</p>
				<p>
					<?php
					esc_html_e( 'Parameters Required : ', 'membership-for-woocommerce' );
					echo '<strong> {user_id}</strong>';
					echo '<strong> {consumer_secret}</strong>';
					?>
				</p>
				<p><?php esc_html_e( 'JSON response example:', 'membership-for-woocommerce' ); ?></p>
				<pre>{
	"status": "success",
	"code": "200",
	"data": [
		{
			"membership_id": 72,
			"membership_name": "Gold",
			"plan_price": "20",
			"plan_validity": "limited",
			"plan_duration": "2 years",
			"membership_status": "complete"
		},
		{
			"membership_id": 80,
			"membership_name": "Diamond",
			"plan_price": "30",
			"plan_validity": "lifetime",
			"plan_duration": "---",
			"membership_status": "complete"
		}
	]
}</pre>
			</div>
		</div>

		<div class="mfw-api-docs__extension">
			<?php do_action( 'wps_add_additional_api_details' ); ?>
		</div>
	</section>
</div>
