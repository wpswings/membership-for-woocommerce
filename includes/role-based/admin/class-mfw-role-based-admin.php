<?php
/**
 * Admin UI for the role-based membership module (WPS-7880).
 *
 * Own settings page, own levels/capabilities manager, own restriction metabox/term UI,
 * export/import, and general settings (Private Site) — no shared screens or logic with
 * the purchase-based flow's admin UI.
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes/role-based/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mfw_Role_Based_Admin.
 */
class Mfw_Role_Based_Admin {

	const PAGE_SLUG = 'mfw_role_based_membership';

	const NONCE_ACTION = 'mfw_role_based_admin';

	const PRIVATE_SITE_OPTION = 'mfw_role_based_private_site';

	const LOGIN_HEADING_OPTION = 'mfw_role_based_login_heading';

	const LOGIN_NOTICE_OPTION = 'mfw_role_based_login_notice';

	const LOGIN_NOTICE_ENABLED_OPTION = 'mfw_role_based_login_notice_enabled';

	/**
	 * The full, grouped capability list for the level editor's capability picker — every
	 * WordPress core capability (matching the reference "Members" plugin's coverage) plus
	 * every real WooCommerce capability, grouped for readability, plus an auto-discovered
	 * "Other" group for anything already present on a registered role that isn't in the
	 * curated list below (a custom post type capability, another plugin's capability, etc.)
	 * — so nothing on the site is ever invisible to this editor.
	 *
	 * @since 3.1.3
	 * @return array<string, array<string,string>> Group label => [cap slug => cap label].
	 */
	public static function capability_groups() {

		$groups = array(
			__( 'General', 'membership-for-woocommerce' )         => array(
				'read'               => __( 'Read', 'membership-for-woocommerce' ),
				'edit_dashboard'     => __( 'Access Dashboard Widgets', 'membership-for-woocommerce' ),
				'customize'          => __( 'Customize Site', 'membership-for-woocommerce' ),
				'delete_site'        => __( 'Delete Site (multisite)', 'membership-for-woocommerce' ),
			),
			__( 'Posts', 'membership-for-woocommerce' )           => array(
				'edit_posts'             => __( 'Edit Posts', 'membership-for-woocommerce' ),
				'edit_others_posts'      => __( 'Edit Others\' Posts', 'membership-for-woocommerce' ),
				'edit_published_posts'   => __( 'Edit Published Posts', 'membership-for-woocommerce' ),
				'edit_private_posts'     => __( 'Edit Private Posts', 'membership-for-woocommerce' ),
				'read_private_posts'     => __( 'Read Private Posts', 'membership-for-woocommerce' ),
				'publish_posts'          => __( 'Publish Posts', 'membership-for-woocommerce' ),
				'delete_posts'           => __( 'Delete Posts', 'membership-for-woocommerce' ),
				'delete_others_posts'    => __( 'Delete Others\' Posts', 'membership-for-woocommerce' ),
				'delete_published_posts' => __( 'Delete Published Posts', 'membership-for-woocommerce' ),
				'delete_private_posts'   => __( 'Delete Private Posts', 'membership-for-woocommerce' ),
			),
			__( 'Pages', 'membership-for-woocommerce' )           => array(
				'edit_pages'             => __( 'Edit Pages', 'membership-for-woocommerce' ),
				'edit_others_pages'      => __( 'Edit Others\' Pages', 'membership-for-woocommerce' ),
				'edit_published_pages'   => __( 'Edit Published Pages', 'membership-for-woocommerce' ),
				'edit_private_pages'     => __( 'Edit Private Pages', 'membership-for-woocommerce' ),
				'read_private_pages'     => __( 'Read Private Pages', 'membership-for-woocommerce' ),
				'publish_pages'          => __( 'Publish Pages', 'membership-for-woocommerce' ),
				'delete_pages'           => __( 'Delete Pages', 'membership-for-woocommerce' ),
				'delete_others_pages'    => __( 'Delete Others\' Pages', 'membership-for-woocommerce' ),
				'delete_published_pages' => __( 'Delete Published Pages', 'membership-for-woocommerce' ),
				'delete_private_pages'   => __( 'Delete Private Pages', 'membership-for-woocommerce' ),
			),
			__( 'Media & Comments', 'membership-for-woocommerce' ) => array(
				'upload_files'       => __( 'Upload Files', 'membership-for-woocommerce' ),
				'unfiltered_upload'  => __( 'Upload Unfiltered File Types', 'membership-for-woocommerce' ),
				'unfiltered_html'    => __( 'Use Unfiltered HTML', 'membership-for-woocommerce' ),
				'moderate_comments'  => __( 'Moderate Comments', 'membership-for-woocommerce' ),
			),
			__( 'Taxonomy & Links', 'membership-for-woocommerce' ) => array(
				'manage_categories' => __( 'Manage Categories', 'membership-for-woocommerce' ),
				'manage_links'      => __( 'Manage Links', 'membership-for-woocommerce' ),
			),
			__( 'Users', 'membership-for-woocommerce' )           => array(
				'list_users'    => __( 'List Users', 'membership-for-woocommerce' ),
				'create_users'  => __( 'Create Users', 'membership-for-woocommerce' ),
				'edit_users'    => __( 'Edit Users', 'membership-for-woocommerce' ),
				'delete_users'  => __( 'Delete Users', 'membership-for-woocommerce' ),
				'promote_users' => __( 'Promote Users', 'membership-for-woocommerce' ),
				'remove_users'  => __( 'Remove Users (multisite)', 'membership-for-woocommerce' ),
			),
			__( 'Plugins & Themes', 'membership-for-woocommerce' ) => array(
				'activate_plugins'   => __( 'Activate Plugins', 'membership-for-woocommerce' ),
				'edit_plugins'       => __( 'Edit Plugins', 'membership-for-woocommerce' ),
				'install_plugins'    => __( 'Install Plugins', 'membership-for-woocommerce' ),
				'update_plugins'     => __( 'Update Plugins', 'membership-for-woocommerce' ),
				'delete_plugins'     => __( 'Delete Plugins', 'membership-for-woocommerce' ),
				'switch_themes'      => __( 'Switch Themes', 'membership-for-woocommerce' ),
				'edit_themes'        => __( 'Edit Themes', 'membership-for-woocommerce' ),
				'install_themes'     => __( 'Install Themes', 'membership-for-woocommerce' ),
				'update_themes'      => __( 'Update Themes', 'membership-for-woocommerce' ),
				'delete_themes'      => __( 'Delete Themes', 'membership-for-woocommerce' ),
				'edit_theme_options' => __( 'Edit Theme Options', 'membership-for-woocommerce' ),
				'edit_files'         => __( 'Edit Files (theme/plugin editor)', 'membership-for-woocommerce' ),
			),
			__( 'Site & Tools', 'membership-for-woocommerce' )    => array(
				'manage_options' => __( 'Manage Options', 'membership-for-woocommerce' ),
				'update_core'    => __( 'Update WordPress', 'membership-for-woocommerce' ),
				'import'         => __( 'Import Content', 'membership-for-woocommerce' ),
				'export'         => __( 'Export Content', 'membership-for-woocommerce' ),
			),
			__( 'WooCommerce', 'membership-for-woocommerce' )     => array(
				'manage_woocommerce'         => __( 'Manage WooCommerce', 'membership-for-woocommerce' ),
				'view_woocommerce_reports'   => __( 'View WooCommerce Reports', 'membership-for-woocommerce' ),
				'edit_products'              => __( 'Edit Products', 'membership-for-woocommerce' ),
				'edit_others_products'       => __( 'Edit Others\' Products', 'membership-for-woocommerce' ),
				'edit_published_products'    => __( 'Edit Published Products', 'membership-for-woocommerce' ),
				'edit_private_products'      => __( 'Edit Private Products', 'membership-for-woocommerce' ),
				'read_private_products'      => __( 'Read Private Products', 'membership-for-woocommerce' ),
				'publish_products'           => __( 'Publish Products', 'membership-for-woocommerce' ),
				'delete_products'            => __( 'Delete Products', 'membership-for-woocommerce' ),
				'delete_others_products'     => __( 'Delete Others\' Products', 'membership-for-woocommerce' ),
				'delete_published_products'  => __( 'Delete Published Products', 'membership-for-woocommerce' ),
				'delete_private_products'    => __( 'Delete Private Products', 'membership-for-woocommerce' ),
				'edit_shop_orders'           => __( 'Edit Shop Orders', 'membership-for-woocommerce' ),
				'edit_others_shop_orders'    => __( 'Edit Others\' Shop Orders', 'membership-for-woocommerce' ),
				'publish_shop_orders'        => __( 'Publish Shop Orders', 'membership-for-woocommerce' ),
				'read_private_shop_orders'   => __( 'Read Private Shop Orders', 'membership-for-woocommerce' ),
				'delete_shop_orders'         => __( 'Delete Shop Orders', 'membership-for-woocommerce' ),
				'edit_shop_coupons'          => __( 'Edit Shop Coupons', 'membership-for-woocommerce' ),
				'publish_shop_coupons'       => __( 'Publish Shop Coupons', 'membership-for-woocommerce' ),
			),
			__( 'Role-Based Membership', 'membership-for-woocommerce' ) => array(
				Mfw_Role_Based_Repository::BYPASS_CAPABILITY => __( 'Bypass All Restrictions', 'membership-for-woocommerce' ),
			),
		);

		$other = self::discover_other_capabilities( $groups );
		if ( ! empty( $other ) ) {
			$groups[ __( 'Other (found on existing roles)', 'membership-for-woocommerce' ) ] = $other;
		}

		return $groups;
	}

	/**
	 * Every capability currently present on any registered WP role that isn't already
	 * covered by the curated groups above — e.g. a custom post type's capabilities, or one
	 * added by another plugin. Keeps the editor "full" without hand-maintaining every
	 * possible site-specific capability.
	 *
	 * @since 3.1.3
	 * @param array $curated_groups The groups already defined in capability_groups().
	 * @return array<string,string> Cap slug => cap label (a humanized version of the slug).
	 */
	private static function discover_other_capabilities( $curated_groups ) {

		$known = array();
		foreach ( $curated_groups as $caps ) {
			$known = array_merge( $known, array_keys( $caps ) );
		}

		$other = array();

		foreach ( wp_roles()->roles as $role ) {
			foreach ( array_keys( $role['capabilities'] ) as $cap ) {
				if ( ! in_array( $cap, $known, true ) && ! isset( $other[ $cap ] ) && ! preg_match( '/^level_\d+$/', $cap ) ) {
					$other[ $cap ] = ucwords( str_replace( array( '_', '-' ), ' ', $cap ) );
				}
			}
		}

		ksort( $other );

		return $other;
	}

	/**
	 * @since 3.1.3
	 * @return string[] Every capability slug across every group (curated + discovered).
	 */
	public static function all_known_capabilities() {

		$all = array();
		foreach ( self::capability_groups() as $caps ) {
			$all = array_merge( $all, array_keys( $caps ) );
		}

		return array_values( array_unique( $all ) );
	}

	/**
	 * Every registered WP role's current capabilities, restricted to the ones our own
	 * capability picker actually shows checkboxes for. Sent to the browser so picking an
	 * existing role in the level form can tick the matching checkboxes — a clear, live
	 * preview of what that role already grants, rather than the admin guessing.
	 *
	 * @since 3.1.3
	 * @return array<string, string[]> Role slug => capability slugs.
	 */
	public static function role_capabilities_map() {

		$known = self::all_known_capabilities();
		$map   = array();

		foreach ( wp_roles()->roles as $role_slug => $role ) {
			$map[ $role_slug ] = array_values( array_intersect( array_keys( $role['capabilities'] ), $known ) );
		}

		return $map;
	}

	/**
	 * @since 3.1.3
	 */
	public static function init() {

		Mfw_Role_Based_Db::maybe_install();

		add_filter( 'wps_add_plugins_menus_array', array( __CLASS__, 'register_menu' ) );

		add_action( 'wp_ajax_mfw_role_based_save_level', array( __CLASS__, 'ajax_save_level' ) );
		add_action( 'wp_ajax_mfw_role_based_delete_level', array( __CLASS__, 'ajax_delete_level' ) );
		add_action( 'wp_ajax_mfw_role_based_save_restriction', array( __CLASS__, 'ajax_save_restriction' ) );
		add_action( 'wp_ajax_mfw_role_based_save_general_settings', array( __CLASS__, 'ajax_save_general_settings' ) );
		add_action( 'wp_ajax_mfw_role_based_save_login_design', array( __CLASS__, 'ajax_save_login_design' ) );
		add_action( 'wp_ajax_mfw_role_based_save_email_template', array( __CLASS__, 'ajax_save_email_template' ) );
		add_action( 'wp_ajax_mfw_role_based_save_webhooks', array( __CLASS__, 'ajax_save_webhooks' ) );

		add_action( 'admin_post_mfw_role_based_export_levels', array( __CLASS__, 'export_levels' ) );
		add_action( 'admin_post_mfw_role_based_import_levels', array( __CLASS__, 'import_levels' ) );
		add_action( 'admin_post_mfw_role_based_send_campaign', array( __CLASS__, 'handle_send_campaign' ) );

		add_action( 'add_meta_boxes', array( __CLASS__, 'register_restriction_metabox' ) );

		foreach ( self::restrictable_taxonomies() as $taxonomy ) {
			add_action( "{$taxonomy}_edit_form_fields", array( __CLASS__, 'render_term_restriction_field' ) );
			add_action( "edited_{$taxonomy}", array( __CLASS__, 'save_term_restriction' ) );
		}

		Mfw_Role_Based_Csv::init();
		Mfw_Role_Based_Roles_Manager::init();
	}

	/**
	 * @since 3.1.3
	 * @return string[] Public, non-attachment taxonomy names.
	 */
	public static function restrictable_taxonomies() {
		return get_taxonomies( array( 'public' => true ) );
	}

	/**
	 * @since 3.1.3
	 * @return bool
	 */
	public static function is_private_site_enabled() {
		return 'on' === get_option( self::PRIVATE_SITE_OPTION, 'off' );
	}

	/**
	 * @since 3.1.3
	 * @return string Custom heading shown above the membership login form, or ''.
	 */
	public static function get_login_heading() {
		return get_option( self::LOGIN_HEADING_OPTION, '' );
	}

	/**
	 * @since 3.1.3
	 * @return string Admin-designed notice HTML shown on the membership login page, or ''.
	 */
	public static function get_login_notice() {
		return get_option( self::LOGIN_NOTICE_OPTION, '' );
	}

	/**
	 * @since 3.1.3
	 * @return bool Whether the notice should be shown at all.
	 */
	public static function is_login_notice_enabled() {
		return 'on' === get_option( self::LOGIN_NOTICE_ENABLED_OPTION, 'off' );
	}

	/**
	 * @since 3.1.3
	 * @param array $menus Existing menu entries.
	 * @return array
	 */
	public static function register_menu( $menus ) {

		if ( ! Mfw_Mode_Controller::is_role_mode() ) {
			return $menus;
		}

		$menus[] = array(
			'name'      => __( 'Role-Based Membership', 'membership-for-woocommerce' ),
			'slug'      => self::PAGE_SLUG,
			'menu_link' => self::PAGE_SLUG,
			'instance'  => __CLASS__,
			'function'  => 'render_page',
		);

		return $menus;
	}

	/**
	 * @since 3.1.3
	 */
	public static function render_page() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$levels               = Mfw_Role_Based_Repository::get_levels();
		$wp_roles             = wp_roles();
		$nonce                = wp_create_nonce( self::NONCE_ACTION );
		$capability_groups    = self::capability_groups();
		$role_capabilities_map = self::role_capabilities_map();
		$private_site         = self::is_private_site_enabled();
		$login_heading        = self::get_login_heading();
		$login_notice         = self::get_login_notice();
		$login_notice_enabled = self::is_login_notice_enabled();
		$login_page_url       = Mfw_Role_Based_Login::get_login_page_url();
		$login_log            = Mfw_Role_Based_Repository::get_login_log( 50 );
		$campaigns            = Mfw_Role_Based_Repository::get_campaigns( 50 );
		$member_count         = count( Mfw_Role_Based_Repository::get_member_user_ids() );
		$non_member_count     = count( Mfw_Role_Based_Repository::get_non_member_user_ids() );
		$email_header_color   = Mfw_Role_Based_Email_Template::get_header_color();
		$email_footer_text    = Mfw_Role_Based_Email_Template::get_footer_text();
		$webhooks             = Mfw_Role_Based_Webhooks::get_webhooks();
		$product_categories   = taxonomy_exists( 'product_cat' ) ? get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) ) : array();
		$level_distribution   = Mfw_Role_Based_Repository::get_level_distribution();
		$signups_per_day      = Mfw_Role_Based_Repository::get_signups_per_day( 30 );
		$total_campaign_stats = Mfw_Role_Based_Repository::get_total_campaign_stats();
		$campaign_count       = count( $campaigns );
		$role_user_counts     = Mfw_Role_Based_Roles_Manager::get_role_user_counts();

		include dirname( __FILE__ ) . '/partials/role-based-settings.php';
	}

	/**
	 * @since 3.1.3
	 */
	public static function register_restriction_metabox() {

		if ( ! Mfw_Mode_Controller::is_role_mode() ) {
			return;
		}

		$screens = array( 'post', 'page', 'product' );

		foreach ( $screens as $screen ) {
			add_meta_box(
				'mfw_role_based_restriction',
				__( 'Role-Based Membership Restriction', 'membership-for-woocommerce' ),
				array( __CLASS__, 'render_restriction_metabox' ),
				$screen,
				'side'
			);
		}
	}

	/**
	 * @since 3.1.3
	 * @param WP_Post $post Current post.
	 */
	public static function render_restriction_metabox( $post ) {

		$levels            = Mfw_Role_Based_Repository::get_levels();
		$object_type       = get_post_type( $post );
		$restriction       = Mfw_Role_Based_Repository::get_restriction( $object_type, $post->ID );
		$restricted_message = get_post_meta( $post->ID, 'mfw_role_membership_restricted_message', true );
		$nonce             = wp_create_nonce( self::NONCE_ACTION );

		include dirname( __FILE__ ) . '/partials/role-based-restriction-metabox.php';
	}

	/**
	 * Restriction UI on taxonomy term edit screens (WPS-7880 gap-analysis item 4).
	 *
	 * @since 3.1.3
	 * @param WP_Term $term Current term.
	 */
	public static function render_term_restriction_field( $term ) {

		if ( ! Mfw_Mode_Controller::is_role_mode() ) {
			return;
		}

		$levels      = Mfw_Role_Based_Repository::get_levels();
		$restriction = Mfw_Role_Based_Repository::get_restriction( 'term', $term->term_id );
		$nonce       = wp_create_nonce( self::NONCE_ACTION );

		include dirname( __FILE__ ) . '/partials/role-based-term-restriction.php';
	}

	/**
	 * @since 3.1.3
	 * @param int $term_id Term id.
	 */
	public static function save_term_restriction( $term_id ) {

		if ( ! isset( $_POST['mfw_role_based_term_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mfw_role_based_term_nonce'] ) ), self::NONCE_ACTION )
			|| ! current_user_can( 'manage_categories' ) ) {
			return;
		}

		$level_ids = isset( $_POST['mfw_role_membership_term_level_ids'] )
			? array_map( 'absint', (array) wp_unslash( $_POST['mfw_role_membership_term_level_ids'] ) )
			: array();

		Mfw_Role_Based_Repository::set_restriction( 'term', $term_id, $level_ids );
	}

	/**
	 * @since 3.1.3
	 */
	public static function ajax_save_level() {

		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'membership-for-woocommerce' ) ), 403 );
		}

		$data = array(
			'id'                    => isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0,
			'name'                  => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
			'description'           => isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '',
			'wp_role'               => isset( $_POST['wp_role'] ) ? sanitize_key( wp_unslash( $_POST['wp_role'] ) ) : '',
			'capabilities'          => isset( $_POST['capabilities'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['capabilities'] ) ) : array(),
			'rank'                  => isset( $_POST['rank'] ) ? absint( $_POST['rank'] ) : 0,
			'discount_percent'      => isset( $_POST['discount_percent'] ) ? (float) $_POST['discount_percent'] : 0,
			'discount_category_ids' => isset( $_POST['discount_category_ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['discount_category_ids'] ) ) : array(),
			'expiry_days'           => isset( $_POST['expiry_days'] ) ? absint( $_POST['expiry_days'] ) : 0,
			'self_signup_enabled'   => ! empty( $_POST['self_signup_enabled'] ),
			'price'                 => isset( $_POST['price'] ) ? (float) $_POST['price'] : 0,
			'upgrade_spend_threshold' => isset( $_POST['upgrade_spend_threshold'] ) ? (float) $_POST['upgrade_spend_threshold'] : 0,
			'tier_ladder'           => ! empty( $_POST['tier_ladder'] ),
			'status'                => isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : 'active',
		);

		$level_id = Mfw_Role_Based_Repository::save_level( $data, self::all_known_capabilities() );

		if ( false === $level_id ) {
			wp_send_json_error( array( 'message' => __( 'Please provide a name and a WordPress role.', 'membership-for-woocommerce' ) ) );
		}

		wp_send_json_success( array( 'id' => $level_id ) );
	}

	/**
	 * @since 3.1.3
	 */
	public static function ajax_delete_level() {

		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'membership-for-woocommerce' ) ), 403 );
		}

		$level_id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		Mfw_Role_Based_Repository::delete_level( $level_id );

		wp_send_json_success();
	}

	/**
	 * @since 3.1.3
	 */
	public static function ajax_save_restriction() {

		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'membership-for-woocommerce' ) ), 403 );
		}

		$object_type = isset( $_POST['object_type'] ) ? sanitize_key( wp_unslash( $_POST['object_type'] ) ) : '';
		$object_id   = isset( $_POST['object_id'] ) ? absint( $_POST['object_id'] ) : 0;
		$level_ids   = isset( $_POST['level_ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['level_ids'] ) ) : array();
		$min_rank    = ( isset( $_POST['min_rank'] ) && '' !== $_POST['min_rank'] ) ? absint( $_POST['min_rank'] ) : null;
		$drip_days   = isset( $_POST['drip_days'] ) ? absint( $_POST['drip_days'] ) : 0;
		$message     = isset( $_POST['restricted_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['restricted_message'] ) ) : '';

		if ( ! $object_type || ! $object_id || ! current_user_can( 'edit_post', $object_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Missing object reference.', 'membership-for-woocommerce' ) ) );
		}

		Mfw_Role_Based_Repository::set_restriction( $object_type, $object_id, $level_ids, $min_rank, $drip_days );

		if ( '' === $message ) {
			delete_post_meta( $object_id, 'mfw_role_membership_restricted_message' );
		} else {
			update_post_meta( $object_id, 'mfw_role_membership_restricted_message', $message );
		}

		wp_send_json_success();
	}

	/**
	 * @since 3.1.3
	 */
	public static function ajax_save_general_settings() {

		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'membership-for-woocommerce' ) ), 403 );
		}

		$private_site = ( isset( $_POST['private_site'] ) && 'on' === $_POST['private_site'] ) ? 'on' : 'off';
		update_option( self::PRIVATE_SITE_OPTION, $private_site );

		wp_send_json_success();
	}

	/**
	 * Saves the admin's design of the membership login page: a plain-text heading and a
	 * rich-text notice (TinyMCE, via wp_editor() on the settings page — this is the "design"
	 * control, letting the admin format/link/style the notice rather than only plain text).
	 *
	 * @since 3.1.3
	 */
	public static function ajax_save_login_design() {

		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'membership-for-woocommerce' ) ), 403 );
		}

		$heading = isset( $_POST['heading'] ) ? sanitize_text_field( wp_unslash( $_POST['heading'] ) ) : '';
		$notice  = isset( $_POST['notice'] ) ? wp_kses_post( wp_unslash( $_POST['notice'] ) ) : '';
		$enabled = ( isset( $_POST['notice_enabled'] ) && 'on' === $_POST['notice_enabled'] ) ? 'on' : 'off';

		update_option( self::LOGIN_HEADING_OPTION, $heading );
		update_option( self::LOGIN_NOTICE_OPTION, $notice );
		update_option( self::LOGIN_NOTICE_ENABLED_OPTION, $enabled );

		self::maybe_register_wpml_string( self::LOGIN_HEADING_OPTION, __( 'Membership login heading', 'membership-for-woocommerce' ), $heading );
		self::maybe_register_wpml_string( self::LOGIN_NOTICE_OPTION, __( 'Membership login notice', 'membership-for-woocommerce' ), $notice );

		wp_send_json_success();
	}

	/**
	 * Registers a saved string with WPML's String Translation (if WPML is active) so
	 * multilingual sites can translate admin-authored copy like the login heading/notice —
	 * this plugin already ships a wpml-config.xml, so multilingual sites are a real audience.
	 * A no-op when WPML isn't installed.
	 *
	 * @since 3.3.0
	 * @param string $name  A stable identifier for this string.
	 * @param string $label Human-readable label shown in the WPML String Translation UI.
	 * @param string $value The string's current value.
	 */
	private static function maybe_register_wpml_string( $name, $label, $value ) {
		if ( function_exists( 'icl_object_id' ) || defined( 'ICL_SITEPRESS_VERSION' ) ) {
			do_action( 'wpml_register_single_string', 'membership-for-woocommerce', $label, $value, false, null, $name ); // phpcs:ignore
		}
	}

	/**
	 * @since 3.3.0
	 */
	public static function ajax_save_email_template() {

		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'membership-for-woocommerce' ) ), 403 );
		}

		$header_color = isset( $_POST['header_color'] ) ? sanitize_text_field( wp_unslash( $_POST['header_color'] ) ) : '#2196f3';
		$footer_text  = isset( $_POST['footer_text'] ) ? wp_kses_post( wp_unslash( $_POST['footer_text'] ) ) : '';

		Mfw_Role_Based_Email_Template::save_settings( $header_color, $footer_text );

		wp_send_json_success();
	}

	/**
	 * @since 3.3.0
	 */
	public static function ajax_save_webhooks() {

		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'membership-for-woocommerce' ) ), 403 );
		}

		$webhooks = array();
		foreach ( Mfw_Role_Based_Webhooks::events() as $event ) {
			$webhooks[ $event ] = isset( $_POST[ 'webhook_' . $event ] ) ? esc_url_raw( wp_unslash( $_POST[ 'webhook_' . $event ] ) ) : '';
		}

		Mfw_Role_Based_Webhooks::save_webhooks( $webhooks );

		wp_send_json_success();
	}

	/**
	 * Downloads all levels as a JSON file. Our own simple format — not a copy of any
	 * other plugin's export schema.
	 *
	 * @since 3.1.3
	 */
	public static function export_levels() {

		if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( self::NONCE_ACTION, 'mfw_export_nonce' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'membership-for-woocommerce' ) );
		}

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="role-membership-levels.json"' );

		echo wp_json_encode( Mfw_Role_Based_Repository::export_levels(), JSON_PRETTY_PRINT );
		exit;
	}

	/**
	 * Imports levels from an uploaded JSON file in the same format export_levels() produces.
	 *
	 * @since 3.1.3
	 */
	public static function import_levels() {

		if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( self::NONCE_ACTION, 'mfw_import_nonce' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'membership-for-woocommerce' ) );
		}

		$redirect = admin_url( 'admin.php?page=' . self::PAGE_SLUG );

		if ( empty( $_FILES['mfw_import_file']['tmp_name'] ) ) {
			wp_safe_redirect( add_query_arg( 'mfw_import', 'missing', $redirect ) );
			exit;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$contents = file_get_contents( sanitize_text_field( wp_unslash( $_FILES['mfw_import_file']['tmp_name'] ) ) );
		$levels   = json_decode( $contents, true );

		if ( ! is_array( $levels ) ) {
			wp_safe_redirect( add_query_arg( 'mfw_import', 'invalid', $redirect ) );
			exit;
		}

		$imported = Mfw_Role_Based_Repository::import_levels( $levels, self::all_known_capabilities() );

		wp_safe_redirect( add_query_arg( 'mfw_import', $imported, $redirect ) );
		exit;
	}

	/**
	 * Sends a composed campaign to the chosen audience. A plain form submit (not AJAX) so
	 * WordPress's own editor.js syncs the wp_editor() content to its textarea automatically
	 * before this request, and so a slow send (many recipients) isn't constrained by a
	 * fetch()'s own timeout expectations.
	 *
	 * @since 3.1.3
	 */
	public static function handle_send_campaign() {

		if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( self::NONCE_ACTION, 'mfw_campaign_nonce' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'membership-for-woocommerce' ) );
		}

		$redirect = admin_url( 'admin.php?page=' . self::PAGE_SLUG . '#campaigns' );

		$name       = isset( $_POST['campaign_name'] ) ? sanitize_text_field( wp_unslash( $_POST['campaign_name'] ) ) : '';
		$subject    = isset( $_POST['campaign_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['campaign_subject'] ) ) : '';
		$message    = isset( $_POST['campaign_message'] ) ? wp_kses_post( wp_unslash( $_POST['campaign_message'] ) ) : '';
		$audience   = isset( $_POST['campaign_audience'] ) ? sanitize_key( wp_unslash( $_POST['campaign_audience'] ) ) : '';
		$level_ids  = isset( $_POST['campaign_level_ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['campaign_level_ids'] ) ) : array();
		$send_mode  = isset( $_POST['campaign_send_mode'] ) ? sanitize_key( wp_unslash( $_POST['campaign_send_mode'] ) ) : Mfw_Role_Based_Campaigns::SEND_MODE_NOW;

		if ( ! $subject || ! $message || ! in_array( $audience, Mfw_Role_Based_Campaigns::audiences(), true ) ) {
			wp_safe_redirect( add_query_arg( 'mfw_campaign', 'invalid', $redirect ) );
			exit;
		}

		$args = array(
			'name'      => $name ? $name : $subject,
			'subject'   => $subject,
			'message'   => $message,
			'audience'  => $audience,
			'level_ids' => $level_ids,
			'actor_id'  => get_current_user_id(),
			'send_mode' => $send_mode,
		);

		if ( Mfw_Role_Based_Campaigns::SEND_MODE_SCHEDULED === $send_mode ) {
			$args['scheduled_at'] = isset( $_POST['campaign_scheduled_at'] ) ? gmdate( 'Y-m-d H:i:s', strtotime( sanitize_text_field( wp_unslash( $_POST['campaign_scheduled_at'] ) ) ) ) : null;
			if ( ! $args['scheduled_at'] ) {
				wp_safe_redirect( add_query_arg( 'mfw_campaign', 'invalid', $redirect ) );
				exit;
			}
		} elseif ( Mfw_Role_Based_Campaigns::SEND_MODE_DRIP === $send_mode ) {
			$args['drip_level_id']   = isset( $_POST['campaign_drip_level_id'] ) ? absint( $_POST['campaign_drip_level_id'] ) : 0;
			$args['drip_delay_days'] = isset( $_POST['campaign_drip_delay_days'] ) ? absint( $_POST['campaign_drip_delay_days'] ) : 0;
			if ( ! $args['drip_level_id'] ) {
				wp_safe_redirect( add_query_arg( 'mfw_campaign', 'invalid', $redirect ) );
				exit;
			}
		}

		$result = Mfw_Role_Based_Campaigns::submit( $args );

		$notice = Mfw_Role_Based_Campaigns::SEND_MODE_NOW === $send_mode ? $result : 'scheduled';

		wp_safe_redirect( add_query_arg( 'mfw_campaign', $notice, $redirect ) );
		exit;
	}
}
