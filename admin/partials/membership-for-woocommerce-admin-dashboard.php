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
$pro_is_active    = false;
if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {

	$pro_is_active = check_membership_pro_plugin_is_active();
}
$mfw_license_key = function_exists( 'get_option' ) ? (string) get_option( 'wps_mfw_license_key', '' ) : '';
if ( '' === $mfw_license_key && function_exists( 'get_option' ) ) {
	$mfw_license_key = (string) get_option( 'wps_mfwp_license_key', '' );
}
$mfw_has_license_key        = '' !== trim( $mfw_license_key );
$mfw_show_license_notice    = $pro_is_active && ! $mfw_has_license_key;
if ( $pro_is_active && class_exists( 'Membership_For_Woocommerce_Pro' ) ) {
	// Pro renders its own activation notice; suppress the duplicate dashboard notice.
	$mfw_show_license_notice = false;
}
$mfw_license_activation_url = admin_url( 'admin.php?page=membership_for_woocommerce_menu&mfw_tab=membership-for-woocommerce-licence' );
$mfw_status_badge           = $pro_is_active ? esc_html__( 'PRO ACTIVE', 'membership-for-woocommerce' ) : esc_html__( 'LITE ACTIVE', 'membership-for-woocommerce' );
$mfw_status_plugin_label    = $pro_is_active ? esc_html__( 'Membership For WooCommerce Pro', 'membership-for-woocommerce' ) : esc_html__( 'Membership For WooCommerce', 'membership-for-woocommerce' );
$mfw_status_version_label   = 'v' . MEMBERSHIP_FOR_WOOCOMMERCE_VERSION;

$mfw_doc_url      = 'https://docs.wpswings.com/membership-for-woocommerce/?utm_source=wpswings-membership-doc&utm_medium=membership-org-backend&utm_campaign=documentation';
$mfw_support_url  = 'https://wpswings.com/submit-query/?utm_source=wpswings-membership-support&utm_medium=membership-org-backend&utm_campaign=support';
$mfw_video_url    = 'https://www.youtube.com/watch?v=DHxo6ojvKmc';
$mfw_plugins_url  = 'https://wpswings.com/product-category/woocommerce-plugins/?utm_source=wpswings-membership&utm_medium=membership-org-backend&utm_campaign=more-plugins';
$mfw_contact_url  = 'https://wpswings.com/submit-query/?utm_source=wpswings-membership-support&utm_medium=membership-org-backend&utm_campaign=contact-us';
$mfw_services_url = 'https://wpswings.com/woocommerce-services/?utm_source=wpswings-membership&utm_medium=membership-pro-backend&utm_campaign=services';
$mfw_services_landing_url = class_exists( 'Membership_For_Woocommerce_Talk_To_Expert_Form' ) ? Membership_For_Woocommerce_Talk_To_Expert_Form::wps_mfw_get_services_landing_url() : $mfw_services_url;
$mfw_marketing_rows = array(
	array(
		'icon'        => 'seo',
		'title'       => esc_html__( 'SEO Services', 'membership-for-woocommerce' ),
		'description' => esc_html__( 'Improve rankings & organic traffic', 'membership-for-woocommerce' ),
	),
	array(
		'icon'        => 'ads',
		'title'       => esc_html__( 'Google Ads Setup And G4 Setup', 'membership-for-woocommerce' ),
		'description' => esc_html__( 'Run profitable ad campaigns', 'membership-for-woocommerce' ),
	),
	array(
		'icon'        => 'speed',
		'title'       => esc_html__( 'Speed Optimization', 'membership-for-woocommerce' ),
		'description' => esc_html__( 'Faster store, happier customers', 'membership-for-woocommerce' ),
	),
	array(
		'icon'        => 'dev',
		'title'       => esc_html__( 'WooCommerce Development Services', 'membership-for-woocommerce' ),
		'description' => esc_html__( 'Custom Solution For your store needs', 'membership-for-woocommerce' ),
	),
);

$mfw_tab_title_aliases = array(
	'membership-for-woocommerce-general'                              => esc_html__( 'General', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-membership-using-registration-form'  => esc_html__( 'Membership', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-shortcodes'                           => esc_html__( 'Shortcodes', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-api-settings'                         => esc_html__( 'API', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-other-settings'                       => esc_html__( 'Layout', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-reports-settings'                     => esc_html__( 'Reports', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-offer-notify-settings'                => esc_html__( 'Offers', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-google-recaptcha-settings'            => esc_html__( 'reCaptcha', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-email-tab'                            => esc_html__( 'Emails', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-system-status'                        => esc_html__( 'System', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-developer'                            => esc_html__( 'Developer', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-licence'                              => esc_html__( 'License', 'membership-for-woocommerce' ),
);

$mfw_more_tab_title_aliases = array(
	'membership-for-woocommerce-google-recaptcha-settings' => esc_html__( 'Google reCaptcha', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-email-tab'                 => esc_html__( 'Email Setting', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-licence'                   => esc_html__( 'License', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-system-status'             => esc_html__( 'System Status', 'membership-for-woocommerce' ),
);

$mfw_tab_intro_map = array(
	'membership-for-woocommerce-overview'                           => esc_html__( 'Track key plugin capabilities, onboarding status, and the latest feature highlights from one dashboard.', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-general'                            => esc_html__( 'Control base plugin behavior, lifecycle settings, member flow defaults, and order-connected automation.', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-membership-using-registration-form' => esc_html__( 'Create plans, define access restrictions, add members, and manage communication from the registration workflow.', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-shortcodes'                         => esc_html__( 'Use ready-to-paste shortcodes to embed plan widgets and member-facing templates across your site.', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-api-settings'                       => esc_html__( 'Manage API credentials, integrations, and service-level connectivity used by your membership automation.', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-other-settings'                     => esc_html__( 'Configure layout behavior, visual preferences, and interface-level rules applied to member interactions.', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-reports-settings'                   => esc_html__( 'Review membership performance metrics, active plan trends, and customer-level activity insights.', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-offer-notify-settings'              => esc_html__( 'Configure WhatsApp, SMS, and email notification templates used for promotional and lifecycle messages.', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-google-recaptcha-settings'          => esc_html__( 'Protect registration and member actions with Google reCAPTCHA verification controls.', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-email-tab'                          => esc_html__( 'Configure pro email templates, message subjects, and lifecycle communication defaults for members.', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-system-status'                      => esc_html__( 'Inspect environment diagnostics, server details, and plugin-level health information for troubleshooting.', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-developer'                          => esc_html__( 'Review available hooks and extension points to customize Membership for WooCommerce behavior.', 'membership-for-woocommerce' ),
	'membership-for-woocommerce-licence'                            => esc_html__( 'Activate and manage your pro license key to keep premium features and updates enabled.', 'membership-for-woocommerce' ),
);

$mfw_full_width_tabs = array(
	'membership-for-woocommerce-overview',
	'membership-for-woocommerce-membership-using-registration-form',
	'membership-for-woocommerce-shortcodes',
	'membership-for-woocommerce-reports-settings',
	'membership-for-woocommerce-system-status',
	'membership-for-woocommerce-developer',
	'membership-for-woocommerce-licence',
);

$mfw_layout_class      = in_array( $mfw_active_tab, $mfw_full_width_tabs, true ) ? 'mfw-redesign-shell--full' : '';
$mfw_current_tab_title = isset( $mfw_default_tabs[ $mfw_active_tab ]['title'] ) ? $mfw_default_tabs[ $mfw_active_tab ]['title'] : esc_html__( 'General Settings', 'membership-for-woocommerce' );
$mfw_current_tab_desc  = isset( $mfw_tab_intro_map[ $mfw_active_tab ] ) ? $mfw_tab_intro_map[ $mfw_active_tab ] : esc_html__( 'Configure this module using the available controls below.', 'membership-for-woocommerce' );
$mfw_visible_tab_limit = 8;
$mfw_visible_tabs      = array_slice( $mfw_default_tabs, 0, $mfw_visible_tab_limit, true );
$mfw_more_tabs         = array_slice( $mfw_default_tabs, $mfw_visible_tab_limit, null, true );
$mfw_more_tab_active   = ! empty( $mfw_more_tabs ) && array_key_exists( $mfw_active_tab, $mfw_more_tabs );
?>

<div class="mfw-redesign-shell <?php echo esc_attr( $mfw_layout_class ); ?>">
	<div class="mfw-redesign-shell__top-notices" id="mfw-redesign-top-notices"></div>
	<div class="mfw-redesign-shell__license-notices" id="mfw-redesign-license-notices">
		<?php if ( $mfw_show_license_notice ) : ?>
			<div class="notice notice-warning thirty-days-notice" id="membership-for-woocommerce-pro-thirty-days-notify">
				<p>
					<a href="<?php echo esc_url( $mfw_license_activation_url ); ?>"><?php esc_html_e( 'Activate', 'membership-for-woocommerce' ); ?></a>
					<?php esc_html_e( 'the license key before 21 days or you may risk losing data and the plugin will also become dysfunctional.', 'membership-for-woocommerce' ); ?>
				</p>
			</div>
		<?php endif; ?>
	</div>
	<?php

	/**
	 * Action for setting save.
	 *
	 * @since 1.0.0
	 */
	do_action( 'wps_mfw_settings_saved_notice' );
	?>

	<div class="mfw-redesign-shell__status-strip">
		<span class="mfw-redesign-shell__status-badge"><?php echo esc_html( $mfw_status_badge ); ?></span>
		<span class="mfw-redesign-shell__status-text"><?php echo esc_html( $mfw_status_plugin_label ); ?></span>
		<span class="mfw-redesign-shell__status-version"><?php echo esc_html( $mfw_status_version_label ); ?></span>
	</div>

	<?php
	/**
	 * Action for before general setting.
	 *
	 * @since 1.0.0
	 */
	do_action( 'wps_mfw_before_general_settings_tab_setting', $mfw_active_tab, $mfw_default_tabs );
	?>

	<main class="wps-main wps-bg-white wps-r-8 mfw-redesign-main">
		<div class="mfw-redesign-main__tabs-wrap">
			<nav class="wps-navbar mfw-redesign-main__navbar">
				<ul class="wps-navbar__items">
					<?php
					if ( is_array( $mfw_visible_tabs ) && ! empty( $mfw_visible_tabs ) ) {
						foreach ( $mfw_visible_tabs as $mfw_tab_key => $mfw_tab_data ) {
							$mfw_tab_classes = 'wps-link';
							if ( ! empty( $mfw_active_tab ) && $mfw_active_tab === $mfw_tab_key ) {
								$mfw_tab_classes .= ' active';
							}

							$mfw_menu_title = isset( $mfw_tab_title_aliases[ $mfw_tab_key ] ) ? $mfw_tab_title_aliases[ $mfw_tab_key ] : $mfw_tab_data['title'];
							?>
							<li>
								<a id="<?php echo esc_attr( $mfw_tab_key ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=membership_for_woocommerce_menu' ) . '&mfw_tab=' . esc_attr( $mfw_tab_key ) ); ?>" class="<?php echo esc_attr( $mfw_tab_classes ); ?>"><?php echo esc_html( $mfw_menu_title ); ?></a>
							</li>
							<?php
						}
						if ( ! empty( $mfw_more_tabs ) ) {
							?>
							<li class="mfw-redesign-main__more <?php echo $mfw_more_tab_active ? 'active' : ''; ?>">
								<button type="button" class="wps-link mfw-redesign-main__more-toggle <?php echo $mfw_more_tab_active ? 'active' : ''; ?>" aria-expanded="false">
									<?php esc_html_e( 'More', 'membership-for-woocommerce' ); ?>
								</button>
								<ul class="mfw-redesign-main__more-menu">
									<?php
									foreach ( $mfw_more_tabs as $mfw_tab_key => $mfw_tab_data ) {
										$mfw_tab_classes = 'wps-link';
										if ( ! empty( $mfw_active_tab ) && $mfw_active_tab === $mfw_tab_key ) {
											$mfw_tab_classes .= ' active';
										}

											$mfw_menu_title = isset( $mfw_more_tab_title_aliases[ $mfw_tab_key ] ) ? $mfw_more_tab_title_aliases[ $mfw_tab_key ] : ( isset( $mfw_tab_title_aliases[ $mfw_tab_key ] ) ? $mfw_tab_title_aliases[ $mfw_tab_key ] : $mfw_tab_data['title'] );
										?>
										<li>
											<a id="<?php echo esc_attr( $mfw_tab_key ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=membership_for_woocommerce_menu' ) . '&mfw_tab=' . esc_attr( $mfw_tab_key ) ); ?>" class="<?php echo esc_attr( $mfw_tab_classes ); ?>"><?php echo esc_html( $mfw_menu_title ); ?></a>
										</li>
										<?php
									}
									?>
								</ul>
							</li>
							<?php
						}
					}
					?>
				</ul>
			</nav>
		</div>

		<section class="wps-section mfw-redesign-main__section">
			<div class="mfw-redesign-main__content-wrap">
				<div class="mfw-redesign-main__content-col">
					<div class="mfw-redesign-main__hero">
						<div class="mfw-redesign-main__hero-copy">
							<p class="mfw-redesign-main__hero-eyebrow"><?php esc_html_e( 'Settings', 'membership-for-woocommerce' ); ?></p>
							<h2 class="mfw-redesign-main__hero-title"><?php echo esc_html( $mfw_current_tab_title ); ?></h2>
							<p class="mfw-redesign-main__hero-description"><?php echo esc_html( $mfw_current_tab_desc ); ?></p>
						</div>
					</div>

					<div class="mfw-redesign-main__tab-content">
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
						$mfw_tab_content_path = isset( $mfw_default_tabs[ $mfw_active_tab ] ) ? $mfw_default_tabs[ $mfw_active_tab ]['file_path'] : $mfw_default_tabs['membership-for-woocommerce-general']['file_path'];
						$mfw_wps_mfw_obj->wps_mfw_plug_load_template( $mfw_tab_content_path );

						/**
						 * Action for general setting form.
						 *
						 * @since 1.0.0
						 */
						do_action( 'wps_mfw_after_general_settings_form' );
						?>
					</div>
				</div>

				<aside class="mfw-redesign-main__sidebar">
					<div class="mfw-redesign-main__sidebar-card">
						<h3><?php esc_html_e( 'Need help with this plugin?', 'membership-for-woocommerce' ); ?></h3>
						<a href="<?php echo esc_url( $mfw_video_url ); ?>" target="_blank"><?php esc_html_e( 'Watch Video', 'membership-for-woocommerce' ); ?></a>
						<a href="<?php echo esc_url( $mfw_doc_url ); ?>" target="_blank"><?php esc_html_e( 'Documentation', 'membership-for-woocommerce' ); ?></a>
						<a href="<?php echo esc_url( $mfw_support_url ); ?>" target="_blank"><?php esc_html_e( 'Support', 'membership-for-woocommerce' ); ?></a>
					</div>
					<div class="mfw-redesign-main__sidebar-card mfw-redesign-main__sidebar-card--services">
						<div class="mfw-service-rail-card__header">
							<h3><?php esc_html_e( 'Grow Your Store With Our Services', 'membership-for-woocommerce' ); ?></h3>
							<span class="mfw-service-rail-card__badge" aria-hidden="true"></span>
						</div>
						<p><?php esc_html_e( "Expert solutions to boost your store's performance.", 'membership-for-woocommerce' ); ?></p>
						<div class="mfw-service-rail">
							<?php foreach ( $mfw_marketing_rows as $mfw_marketing_row ) : ?>
								<a href="<?php echo esc_url( $mfw_services_landing_url ); ?>" target="_blank" rel="noopener noreferrer" class="mfw-service-rail__item">
									<span class="mfw-service-rail__icon mfw-service-rail__icon--<?php echo esc_attr( $mfw_marketing_row['icon'] ); ?>" aria-hidden="true"></span>
									<span class="mfw-service-rail__content">
										<span class="mfw-service-rail__title"><?php echo esc_html( $mfw_marketing_row['title'] ); ?></span>
										<span class="mfw-service-rail__description"><?php echo esc_html( $mfw_marketing_row['description'] ); ?></span>
									</span>
									<span class="mfw-service-rail__arrow" aria-hidden="true">&rsaquo;</span>
								</a>
							<?php endforeach; ?>
						</div>
						<button type="button" class="mfw-service-rail__cta" data-mfw-open-expert-modal><?php esc_html_e( 'Talk to an Expert', 'membership-for-woocommerce' ); ?></button>
						<div class="mfw-service-rail__footer"><?php esc_html_e( 'Services by WP Swings', 'membership-for-woocommerce' ); ?></div>
					</div>
					<div class="mfw-redesign-main__sidebar-card mfw-redesign-main__sidebar-card--accent">
						<h3><?php esc_html_e( 'Still facing problems?', 'membership-for-woocommerce' ); ?></h3>
						<p><?php esc_html_e( 'Get direct help for setup, styling, and integration issues from the support team.', 'membership-for-woocommerce' ); ?></p>
						<a href="<?php echo esc_url( $mfw_contact_url ); ?>" target="_blank" class="mfw-redesign-main__sidebar-cta"><?php esc_html_e( 'Contact Us', 'membership-for-woocommerce' ); ?></a>
					</div>
					<div class="mfw-redesign-main__sidebar-card">
						<h3><?php esc_html_e( 'Explore more plugins', 'membership-for-woocommerce' ); ?></h3>
						<p><?php esc_html_e( 'Discover additional WooCommerce and automation plugins from the same product family.', 'membership-for-woocommerce' ); ?></p>
						<a href="<?php echo esc_url( $mfw_plugins_url ); ?>" target="_blank"><?php esc_html_e( 'View More Plugins', 'membership-for-woocommerce' ); ?></a>
						<a href="<?php echo esc_url( $mfw_services_url ); ?>" target="_blank"><?php esc_html_e( 'Services', 'membership-for-woocommerce' ); ?></a>
					</div>
				</aside>
			</div>
			</section>
		</main>
	</div>
<?php
if ( class_exists( 'Membership_For_Woocommerce_Talk_To_Expert_Form' ) ) {
	Membership_For_Woocommerce_Talk_To_Expert_Form::wps_mfw_render_modal();
}
?>
