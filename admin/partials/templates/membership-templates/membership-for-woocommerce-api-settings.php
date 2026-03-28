<?php
/**
 * Provide a admin area view for the plugin.
 *
 * This file is used to markup the html field for API settings.
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

$mfw_auth_response = <<<'JSON'
{
    "code": "rest_forbidden",
    "message": "Sorry, you are not allowed to do that.",
    "data": {
        "status": 401
    }
}
JSON;

$mfw_membership_offers_response = <<<'JSON'
{
    "status": "success",
    "code": 200,
    "data": [
        {
            "membership_id": 71,
            "membership_name": "Silver",
            "plan_type": "lifetime",
            "plan_price": "15"
        },
        {
            "membership_id": 72,
            "membership_name": "Gold",
            "plan_type": "limited",
            "plan_price": "20",
            "plan_duration": "2 years"
        }
    ]
}
JSON;

$mfw_user_membership_response = <<<'JSON'
{
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
}
JSON;
?>

<form action="" method="POST" class="wps-mfw-gen-section-form">
	<div class="mfw-secion-wrap">
		<?php do_action( 'mfw_api_settings_before' ); ?>
		<div class="wps-sm__modal"></div>
		<?php
		$mfw_general_html = $mfw_wps_mfw_obj->wps_mfw_plug_generate_html( $mfw_genaral_settings );
		echo esc_html( $mfw_general_html );
		wp_nonce_field( 'admin_save_data', 'wps_tabs_nonce' );
		?>
	</div>
</form>

<div class="wps_msfw_parent_api_details_wrapper mfw-api-docs">
	<div class="mfw-settings-card__header mfw-api-docs__header">
		<span class="mfw-admin-panel__eyebrow"><?php esc_html_e( 'API Reference', 'membership-for-woocommerce' ); ?></span>
		<h3><?php esc_html_e( 'Plugin API Details', 'membership-for-woocommerce' ); ?></h3>
		<p><?php esc_html_e( 'Use the authentication key and endpoint examples below to connect your membership data safely.', 'membership-for-woocommerce' ); ?></p>
	</div>

	<h4 class="mfw-api-docs__section is-open"><?php esc_html_e( 'Authentication', 'membership-for-woocommerce' ); ?></h4>
	<div class="wps_msfw_rest_api_response mfw-api-docs__response">
		<p>
			<?php esc_html_e( 'For authentication you need Consumer Secret', 'membership-for-woocommerce' ); ?>
			<strong>{consumer_secret}</strong>
			<?php esc_html_e( 'keys. Response on wrong api details:', 'membership-for-woocommerce' ); ?>
		</p>
		<pre><?php echo esc_html( $mfw_auth_response ); ?></pre>
	</div>

	<h4 class="mfw-api-docs__section"><?php esc_html_e( 'To Retrieve Membership Offers', 'membership-for-woocommerce' ); ?></h4>
	<div class="wps_msfw_rest_api_response mfw-api-docs__response">
		<p>
			<strong><?php esc_html_e( 'Base URL:', 'membership-for-woocommerce' ); ?></strong>
			<?php echo esc_html( '{site_url}/wp-json/wps-mfw/get-membership-offers' ); ?>
		</p>
		<p>
			<strong><?php esc_html_e( 'Example:', 'membership-for-woocommerce' ); ?></strong>
			<?php echo esc_html( trailingslashit( site_url() ) . 'wp-json/wps-mfw/get-membership-offers' ); ?>
		</p>
		<p>
			<strong><?php esc_html_e( 'Parameters Required:', 'membership-for-woocommerce' ); ?></strong>
			<strong>{consumer_secret}</strong>
		</p>
		<p><?php esc_html_e( 'JSON response example:', 'membership-for-woocommerce' ); ?></p>
		<pre><?php echo esc_html( $mfw_membership_offers_response ); ?></pre>
	</div>

	<h4 class="mfw-api-docs__section"><?php esc_html_e( 'To Retrieve Particular User Membership', 'membership-for-woocommerce' ); ?></h4>
	<div class="wps_msfw_rest_api_response mfw-api-docs__response">
		<p>
			<strong><?php esc_html_e( 'Base URL:', 'membership-for-woocommerce' ); ?></strong>
			<?php echo esc_html( '{site_url}/wp-json/wps-mfw/get-user-membership' ); ?>
		</p>
		<p>
			<strong><?php esc_html_e( 'Example:', 'membership-for-woocommerce' ); ?></strong>
			<?php echo esc_html( trailingslashit( site_url() ) . 'wp-json/wps-mfw/get-user-membership' ); ?>
		</p>
		<p>
			<strong><?php esc_html_e( 'Parameters Required:', 'membership-for-woocommerce' ); ?></strong>
			<strong>{user_id}</strong>
			<strong>{consumer_secret}</strong>
		</p>
		<p><?php esc_html_e( 'JSON response example:', 'membership-for-woocommerce' ); ?></p>
		<pre><?php echo esc_html( $mfw_user_membership_response ); ?></pre>
	</div>

	<?php do_action( 'wps_add_additional_api_details' ); ?>
</div>
