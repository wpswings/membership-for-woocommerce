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

$mfw_default_tabs = $mfw_wps_mfw_obj->wps_mfw_plug_default_tabs();
reset( $mfw_default_tabs );
$mfw_first_tab  = key( $mfw_default_tabs );
$mfw_active_tab = isset( $_GET['mfw_tab'] ) ? sanitize_key( $_GET['mfw_tab'] ) : 'membership-for-woocommerce-general';
$plugin_name    = $mfw_wps_mfw_obj->mfw_get_plugin_name();
$mfw_is_pro     = false;

if ( function_exists( 'check_membership_pro_plugin_is_active' ) ) {
	$mfw_is_pro = check_membership_pro_plugin_is_active();
	if ( $mfw_is_pro ) {
		$plugin_name = $plugin_name . '-pro';
	}
}

if ( ! isset( $mfw_default_tabs[ $mfw_active_tab ] ) ) {
	$mfw_active_tab = isset( $mfw_default_tabs['membership-for-woocommerce-general'] ) ? 'membership-for-woocommerce-general' : $mfw_first_tab;
}

$mfw_base_url      = admin_url( 'admin.php?page=membership_for_woocommerce_menu' );
$mfw_docs_url      = 'https://docs.wpswings.com/membership-for-woocommerce/?utm_source=wpswings-membership-doc&utm_medium=membership-org-backend&utm_campaign=documentation';
$mfw_support_url   = 'https://wpswings.com/submit-query/?utm_source=wpswings-membership-support&utm_medium=membership-org-backend&utm_campaign=support';
$mfw_services_url  = 'https://wpswings.com/woocommerce-services/?utm_source=wpswings-membership&utm_medium=membership-pro-backend&utm_campaign=services';
$mfw_video_url     = 'https://www.youtube.com/watch?v=Yf0pa_Fgn5s';
$mfw_plugins_url   = 'https://wpswings.com/wordpress-plugins/?utm_source=wpswings-membership&utm_medium=membership-backend&utm_campaign=more-plugins';
$mfw_upgrade_url   = 'https://wpswings.com/product/woocommerce-memberships-plugin/?utm_source=wpswings-membership&utm_medium=membership-org-backend&utm_campaign=upgrade-pro';
$mfw_version_label = defined( 'Membership_For_Woocommerce_Pro_VERSION' ) && $mfw_is_pro ? Membership_For_Woocommerce_Pro_VERSION : MEMBERSHIP_FOR_WOOCOMMERCE_VERSION;

$mfw_tab_meta = array(
	'membership-for-woocommerce-overview' => array(
		'eyebrow'    => esc_html__( 'Overview', 'membership-for-woocommerce' ),
		'title'      => esc_html__( 'Membership overview', 'membership-for-woocommerce' ),
		'desc'       => esc_html__( 'Understand the plugin value, feature set, and upgrade path from a single overview screen.', 'membership-for-woocommerce' ),
		'cta_label'  => esc_html__( 'Watch Video', 'membership-for-woocommerce' ),
		'cta_url'    => $mfw_video_url,
		'show_intro' => false,
	),
	'membership-for-woocommerce-general' => array(
		'eyebrow'    => esc_html__( 'General Settings', 'membership-for-woocommerce' ),
		'title'      => esc_html__( 'General Settings', 'membership-for-woocommerce' ),
		'desc'       => esc_html__( 'Configure the core membership behavior, member lifecycle, and store-wide restrictions.', 'membership-for-woocommerce' ),
		'cta_label'  => esc_html__( 'Read Documentation', 'membership-for-woocommerce' ),
		'cta_url'    => $mfw_docs_url,
		'show_intro' => true,
	),
	'membership-for-woocommerce-membership-using-registration-form' => array(
		'eyebrow'    => esc_html__( 'Membership Flow', 'membership-for-woocommerce' ),
		'title'      => esc_html__( 'Membership Settings', 'membership-for-woocommerce' ),
		'desc'       => esc_html__( 'Manage plans, restrictions, member onboarding, and registration-driven membership rules.', 'membership-for-woocommerce' ),
		'cta_label'  => esc_html__( 'Read Documentation', 'membership-for-woocommerce' ),
		'cta_url'    => $mfw_docs_url,
		'show_intro' => true,
	),
	'membership-for-woocommerce-shortcodes' => array(
		'eyebrow'    => esc_html__( 'Shortcodes', 'membership-for-woocommerce' ),
		'title'      => esc_html__( 'Shortcodes', 'membership-for-woocommerce' ),
		'desc'       => esc_html__( 'Review and copy shortcode blocks for plan display, member account views, and custom page layouts.', 'membership-for-woocommerce' ),
		'cta_label'  => esc_html__( 'Read Documentation', 'membership-for-woocommerce' ),
		'cta_url'    => $mfw_docs_url,
		'show_intro' => true,
	),
	'membership-for-woocommerce-api-settings' => array(
		'eyebrow'    => esc_html__( 'Integration', 'membership-for-woocommerce' ),
		'title'      => esc_html__( 'API Settings', 'membership-for-woocommerce' ),
		'desc'       => esc_html__( 'Generate and manage API credentials used for membership automations and external integrations.', 'membership-for-woocommerce' ),
		'cta_label'  => esc_html__( 'Read Documentation', 'membership-for-woocommerce' ),
		'cta_url'    => $mfw_docs_url,
		'show_intro' => true,
	),
	'membership-for-woocommerce-other-settings' => array(
		'eyebrow'    => esc_html__( 'Display Rules', 'membership-for-woocommerce' ),
		'title'      => esc_html__( 'Layout Settings', 'membership-for-woocommerce' ),
		'desc'       => esc_html__( 'Control membership page layouts, buying experience details, and customer-facing display behavior.', 'membership-for-woocommerce' ),
		'cta_label'  => esc_html__( 'Read Documentation', 'membership-for-woocommerce' ),
		'cta_url'    => $mfw_docs_url,
		'show_intro' => true,
	),
	'membership-for-woocommerce-reports-settings' => array(
		'eyebrow'    => esc_html__( 'Analytics', 'membership-for-woocommerce' ),
		'title'      => esc_html__( 'Reports', 'membership-for-woocommerce' ),
		'desc'       => esc_html__( 'Track plan performance, membership activity, and other operational insights in one place.', 'membership-for-woocommerce' ),
		'cta_label'  => esc_html__( 'Read Documentation', 'membership-for-woocommerce' ),
		'cta_url'    => $mfw_docs_url,
		'show_intro' => true,
	),
	'membership-for-woocommerce-offer-notify-settings' => array(
		'eyebrow'    => esc_html__( 'Notifications', 'membership-for-woocommerce' ),
		'title'      => esc_html__( 'Offer Notification', 'membership-for-woocommerce' ),
		'desc'       => esc_html__( 'Manage WhatsApp, SMS, and email communication settings used to promote membership offers.', 'membership-for-woocommerce' ),
		'cta_label'  => esc_html__( 'Read Documentation', 'membership-for-woocommerce' ),
		'cta_url'    => $mfw_docs_url,
		'show_intro' => true,
	),
	'membership-for-woocommerce-google-recaptcha-settings' => array(
		'eyebrow'    => esc_html__( 'Security', 'membership-for-woocommerce' ),
		'title'      => esc_html__( 'Google reCAPTCHA', 'membership-for-woocommerce' ),
		'desc'       => esc_html__( 'Protect membership registration and purchase flows with Google reCAPTCHA validation.', 'membership-for-woocommerce' ),
		'cta_label'  => esc_html__( 'Read Documentation', 'membership-for-woocommerce' ),
		'cta_url'    => $mfw_docs_url,
		'show_intro' => true,
	),
	'membership-for-woocommerce-email-tab' => array(
		'eyebrow'    => esc_html__( 'Communication', 'membership-for-woocommerce' ),
		'title'      => esc_html__( 'Email Setting', 'membership-for-woocommerce' ),
		'desc'       => esc_html__( 'Configure lifecycle emails for cancellation, expiry reminders, and membership welcome communication.', 'membership-for-woocommerce' ),
		'cta_label'  => esc_html__( 'Read Documentation', 'membership-for-woocommerce' ),
		'cta_url'    => $mfw_docs_url,
		'show_intro' => true,
	),
	'membership-for-woocommerce-licence' => array(
		'eyebrow'    => esc_html__( 'License', 'membership-for-woocommerce' ),
		'title'      => esc_html__( 'License Activation', 'membership-for-woocommerce' ),
		'desc'       => esc_html__( 'Validate your purchase code to unlock the pro capability set and ongoing updates.', 'membership-for-woocommerce' ),
		'cta_label'  => esc_html__( 'Documentation', 'membership-for-woocommerce' ),
		'cta_url'    => $mfw_docs_url,
		'show_intro' => false,
	),
	'membership-for-woocommerce-system-status' => array(
		'eyebrow'    => esc_html__( 'Diagnostics', 'membership-for-woocommerce' ),
		'title'      => esc_html__( 'System Status', 'membership-for-woocommerce' ),
		'desc'       => esc_html__( 'Inspect environment details and plugin health information for debugging and support.', 'membership-for-woocommerce' ),
		'cta_label'  => esc_html__( 'Support', 'membership-for-woocommerce' ),
		'cta_url'    => $mfw_support_url,
		'show_intro' => true,
	),
	'membership-for-woocommerce-developer' => array(
		'eyebrow'    => esc_html__( 'Developer', 'membership-for-woocommerce' ),
		'title'      => esc_html__( 'Developer', 'membership-for-woocommerce' ),
		'desc'       => esc_html__( 'Review hooks, extension points, and technical references needed for custom implementations.', 'membership-for-woocommerce' ),
		'cta_label'  => esc_html__( 'Support', 'membership-for-woocommerce' ),
		'cta_url'    => $mfw_support_url,
		'show_intro' => true,
	),
);

$mfw_active_meta = isset( $mfw_tab_meta[ $mfw_active_tab ] ) ? $mfw_tab_meta[ $mfw_active_tab ] : array(
	'eyebrow'    => $mfw_default_tabs[ $mfw_active_tab ]['title'],
	'title'      => $mfw_default_tabs[ $mfw_active_tab ]['title'],
	'desc'       => '',
	'cta_label'  => __( 'Read Documentation', 'membership-for-woocommerce' ),
	'cta_url'    => $mfw_docs_url,
	'show_intro' => true,
);

$mfw_tab_limit     = 8;
$mfw_visible_tabs  = array_slice( $mfw_default_tabs, 0, $mfw_tab_limit, true );
$mfw_overflow_tabs = array_slice( $mfw_default_tabs, $mfw_tab_limit, null, true );
$mfw_more_is_active = ! empty( $mfw_overflow_tabs ) && isset( $mfw_overflow_tabs[ $mfw_active_tab ] );

/**
 * Action for before general setting.
 *
 * @since 1.0.0
 */
do_action( 'wps_mfw_before_general_settings_tab_setting', $mfw_active_tab, $mfw_default_tabs );

?>
<div class="mfw-admin-dashboard" data-default-tab="<?php echo esc_attr( $mfw_active_tab ); ?>">
	<div class="mfw-admin-dashboard__promo<?php echo $mfw_is_pro ? ' mfw-admin-dashboard__promo--pro' : ''; ?>">
		<div class="mfw-admin-dashboard__promo-copy">
			<?php if ( $mfw_is_pro ) : ?>
				<span class="mfw-admin-dashboard__promo-badge"><?php esc_html_e( 'Pro Active', 'membership-for-woocommerce' ); ?></span>
				<span class="mfw-admin-dashboard__promo-title"><?php esc_html_e( 'Membership For WooCommerce Pro', 'membership-for-woocommerce' ); ?></span>
			<?php else : ?>
				<span class="mfw-admin-dashboard__promo-badge"><?php esc_html_e( 'Limited Offer', 'membership-for-woocommerce' ); ?></span>
				<p><?php esc_html_e( 'Create a cleaner membership experience with better customer communication and workflow control.', 'membership-for-woocommerce' ); ?></p>
			<?php endif; ?>
		</div>
		<?php if ( ! $mfw_is_pro ) : ?>
			<a class="mfw-admin-dashboard__promo-action" href="<?php echo esc_url( $mfw_upgrade_url ); ?>" target="_blank" rel="noreferrer">
				<?php esc_html_e( 'Upgrade to Pro', 'membership-for-woocommerce' ); ?>
			</a>
		<?php endif; ?>
	</div>

	<div class="mfw-admin-dashboard__frame">
		<?php do_action( 'wps_mfw_settings_saved_notice' ); ?>
		<header class="mfw-admin-dashboard__header">
			<div class="mfw-admin-dashboard__headline">
				<span class="mfw-admin-dashboard__version"><?php echo esc_html( 'v' . $mfw_version_label . ( $mfw_is_pro ? ' Pro' : ' Lite' ) ); ?></span>
			</div>
			<nav class="mfw-admin-dashboard__tabs" aria-label="<?php esc_attr_e( 'Membership settings tabs', 'membership-for-woocommerce' ); ?>">
				<?php foreach ( $mfw_visible_tabs as $mfw_tab_key => $mfw_tab_data ) : ?>
					<?php
					$mfw_panel_meta = isset( $mfw_tab_meta[ $mfw_tab_key ] ) ? $mfw_tab_meta[ $mfw_tab_key ] : array(
						'eyebrow'    => $mfw_tab_data['title'],
						'title'      => $mfw_tab_data['title'],
						'desc'       => '',
						'cta_label'  => __( 'Read Documentation', 'membership-for-woocommerce' ),
						'cta_url'    => $mfw_docs_url,
						'show_intro' => true,
					);
					?>
					<a
						id="<?php echo esc_attr( $mfw_tab_key ); ?>"
						href="<?php echo esc_url( add_query_arg( 'mfw_tab', $mfw_tab_key, $mfw_base_url ) ); ?>"
						class="mfw-admin-tab-link<?php echo $mfw_active_tab === $mfw_tab_key ? ' is-active' : ''; ?>"
						data-top-tab-target="<?php echo esc_attr( $mfw_tab_key ); ?>"
						data-panel-eyebrow="<?php echo esc_attr( $mfw_panel_meta['eyebrow'] ); ?>"
						data-panel-title="<?php echo esc_attr( $mfw_panel_meta['title'] ); ?>"
						data-panel-desc="<?php echo esc_attr( $mfw_panel_meta['desc'] ); ?>"
						data-panel-cta-label="<?php echo esc_attr( $mfw_panel_meta['cta_label'] ); ?>"
						data-panel-cta-url="<?php echo esc_url( $mfw_panel_meta['cta_url'] ); ?>"
						data-panel-show-intro="<?php echo ! empty( $mfw_panel_meta['show_intro'] ) ? '1' : '0'; ?>"
					>
						<?php echo esc_html( $mfw_tab_data['title'] ); ?>
					</a>
				<?php endforeach; ?>
				<?php if ( ! empty( $mfw_overflow_tabs ) ) : ?>
					<div class="mfw-admin-tabs-more<?php echo $mfw_more_is_active ? ' is-active' : ''; ?>">
						<button type="button" class="mfw-admin-tabs-more__toggle<?php echo $mfw_more_is_active ? ' is-active' : ''; ?>" aria-expanded="false">
							<?php esc_html_e( 'More', 'membership-for-woocommerce' ); ?>
						</button>
						<div class="mfw-admin-tabs-more__menu">
							<?php foreach ( $mfw_overflow_tabs as $mfw_tab_key => $mfw_tab_data ) : ?>
								<?php
								$mfw_panel_meta = isset( $mfw_tab_meta[ $mfw_tab_key ] ) ? $mfw_tab_meta[ $mfw_tab_key ] : array(
									'eyebrow'    => $mfw_tab_data['title'],
									'title'      => $mfw_tab_data['title'],
									'desc'       => '',
									'cta_label'  => __( 'Read Documentation', 'membership-for-woocommerce' ),
									'cta_url'    => $mfw_docs_url,
									'show_intro' => true,
								);
								?>
								<a
									id="<?php echo esc_attr( $mfw_tab_key ); ?>"
									href="<?php echo esc_url( add_query_arg( 'mfw_tab', $mfw_tab_key, $mfw_base_url ) ); ?>"
									class="mfw-admin-tab-link<?php echo $mfw_active_tab === $mfw_tab_key ? ' is-active' : ''; ?>"
									data-top-tab-target="<?php echo esc_attr( $mfw_tab_key ); ?>"
									data-panel-eyebrow="<?php echo esc_attr( $mfw_panel_meta['eyebrow'] ); ?>"
									data-panel-title="<?php echo esc_attr( $mfw_panel_meta['title'] ); ?>"
									data-panel-desc="<?php echo esc_attr( $mfw_panel_meta['desc'] ); ?>"
									data-panel-cta-label="<?php echo esc_attr( $mfw_panel_meta['cta_label'] ); ?>"
									data-panel-cta-url="<?php echo esc_url( $mfw_panel_meta['cta_url'] ); ?>"
									data-panel-show-intro="<?php echo ! empty( $mfw_panel_meta['show_intro'] ) ? '1' : '0'; ?>"
								>
									<?php echo esc_html( $mfw_tab_data['title'] ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</nav>
		</header>

		<div class="mfw-admin-dashboard__body">
			<div class="mfw-admin-dashboard__main">
				<?php do_action( 'wps_mfw_before_general_settings_form' ); ?>
				<section class="mfw-admin-panel is-active" id="mfw-admin-active-panel" data-top-panel="<?php echo esc_attr( $mfw_active_tab ); ?>">
					<div class="mfw-admin-panel__intro<?php echo empty( $mfw_active_meta['show_intro'] ) ? ' mfw-admin-panel__intro--hidden' : ''; ?>" id="mfw-admin-panel-intro">
						<div class="mfw-admin-panel__intro-copy">
							<span class="mfw-admin-panel__eyebrow" id="mfw-admin-panel-eyebrow"><?php echo esc_html( $mfw_active_meta['eyebrow'] ); ?></span>
							<h2 id="mfw-admin-panel-title"><?php echo esc_html( $mfw_active_meta['title'] ); ?></h2>
							<p id="mfw-admin-panel-desc"><?php echo esc_html( $mfw_active_meta['desc'] ); ?></p>
						</div>
						<a class="mfw-admin-panel__intro-action" id="mfw-admin-panel-cta" href="<?php echo esc_url( $mfw_active_meta['cta_url'] ); ?>" target="_blank" rel="noreferrer"><?php echo esc_html( $mfw_active_meta['cta_label'] ); ?></a>
					</div>
					<div class="mfw-admin-panel__canvas" id="mfw-admin-panel-canvas">
						<?php $mfw_wps_mfw_obj->wps_mfw_plug_load_template( $mfw_default_tabs[ $mfw_active_tab ]['file_path'] ); ?>
					</div>
				</section>
				<?php do_action( 'wps_mfw_after_general_settings_form' ); ?>
			</div>

			<aside class="mfw-admin-dashboard__sidebar">
				<div class="mfw-admin-sidebar-card">
					<h3><?php esc_html_e( 'Need help with this plugin?', 'membership-for-woocommerce' ); ?></h3>
					<a href="<?php echo esc_url( $mfw_video_url ); ?>" target="_blank" rel="noreferrer"><?php esc_html_e( 'Watch Video', 'membership-for-woocommerce' ); ?></a>
					<a href="<?php echo esc_url( $mfw_docs_url ); ?>" target="_blank" rel="noreferrer"><?php esc_html_e( 'Documentation', 'membership-for-woocommerce' ); ?></a>
					<a href="<?php echo esc_url( $mfw_support_url ); ?>" target="_blank" rel="noreferrer"><?php esc_html_e( 'Support', 'membership-for-woocommerce' ); ?></a>
				</div>
				<div class="mfw-admin-sidebar-card mfw-admin-sidebar-card--tinted">
					<h3><?php esc_html_e( 'Still facing problems?', 'membership-for-woocommerce' ); ?></h3>
					<p><?php esc_html_e( 'We are ready to resolve workflow, styling, and integration issues across your membership setup.', 'membership-for-woocommerce' ); ?></p>
					<a href="<?php echo esc_url( $mfw_services_url ); ?>" target="_blank" rel="noreferrer"><?php esc_html_e( 'Hire Us', 'membership-for-woocommerce' ); ?></a>
				</div>
				<div class="mfw-admin-sidebar-card">
					<h3><?php esc_html_e( 'Explore more plugins', 'membership-for-woocommerce' ); ?></h3>
					<p><?php esc_html_e( 'Discover additional automation and commerce plugins from the same product family.', 'membership-for-woocommerce' ); ?></p>
					<a href="<?php echo esc_url( $mfw_plugins_url ); ?>" target="_blank" rel="noreferrer"><?php esc_html_e( 'View More Plugins', 'membership-for-woocommerce' ); ?></a>
				</div>
			</aside>
		</div>
	</div>
</div>
