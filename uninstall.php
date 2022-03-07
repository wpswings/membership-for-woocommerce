<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Getting global options.
$wps_membership_global_settings = get_option( 'wps_membership_global_options' );

// Delete only if "Delete data at unistall" in Global settings set to true.
if ( ! empty( $wps_membership_global_settings['wps_membership_for_woo_delete_data'] ) && 'on' === $wps_membership_global_settings['wps_membership_for_woo_delete_data'] ) {

	// Deleting membership default page at plugin unistall.
	$wps_membership_default_page = get_option( 'wps_membership_default_plans_page' );

	if ( ! empty( $wps_membership_default_page ) ) {

			wp_delete_post( $wps_membership_default_page, true );
	}

	// Deleting "member" user role at plugin uninstall.
	$user_roles = get_option( 'wp_user_roles' );

	if ( is_array( $user_roles ) && ! empty( $user_roles ) ) {

		foreach ( $user_roles as $user_role ) {

			if ( 'Member' === $user_role['name'] ) {

				remove_role( 'member' );
			}
		}
	}

	// Delete all membership plans(post) & unregister post type at plugin uninstall.
	$wps_membership_cpt = array(
		'post_type'      => 'wps_cpt_membership',
		'posts_per_page' => -1,
	);

	$wps_membership_posts = get_posts( $wps_membership_cpt );

	if ( is_array( $wps_membership_posts ) && ! empty( $wps_membership_posts ) ) {

		foreach ( $wps_membership_posts as $membership_post ) {

			wp_delete_post( $membership_post->ID, true );
		}
	}
	unregister_post_type( 'wps_cpt_membership' );

	// Delete all members (post) & unregister post type at plugin uninstall.
	$wps_members_cpt = array(
		'post_type'      => 'wps_cpt_members',
		'posts_per_page' => -1,
	);

	$wps_members_posts = get_posts( $wps_members_cpt );

	if ( is_array( $wps_members_posts ) && ! empty( $wps_members_posts ) ) {

		foreach ( $wps_members_posts as $members_post ) {

			wp_delete_post( $members_post->ID, true );
		}
	}
	unregister_post_type( 'wps_cpt_members' );

	delete_option( 'wps_membership_enable_plugin' );
	delete_option( 'wps_membership_for_woo_delete_data' );
	delete_option( 'wps_membership_plan_user_history' );
	delete_option( 'mfw_mfw_plugin_standard_multistep_done' );

	// Deleting options at last during plugin uninstall.
	$plugin_options = array(
		'wps_membership_default_plans_page',
		'wps_membership_global_options',
	);

	foreach ( $plugin_options as $option ) {

		if ( get_option( $option ) ) {

			delete_option( $option );
		}
	}
}
