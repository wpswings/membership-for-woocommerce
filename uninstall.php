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
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Creating Instance of the global functions class.
$global_class = Membership_For_Woocommerce_Global_Functions::get();

// Getting global options.
$mwb_membership_global_settings = get_option( 'mwb_membership_global_options', $global_class->default_global_options() );

// Delete only if "Delete data at unistall" in Global settings set to true.
if ( ! empty( $mwb_membership_global_settings['mwb_membership_delete_data'] ) && 'on' == $mwb_membership_global_settings['mwb_membership_delete_data'] ) {

	// Deleting membership default page at plugin unistall.
	$mwb_membership_default_page = get_option( 'mwb_membership_default_plans_page', array() );

	if ( is_array( $mwb_membership_default_page ) && ! empty( $mwb_membership_default_page ) ) {

		foreach ( $mwb_membership_default_page as $default_page ) {

			wp_delete_post( $default_page );
		}
	}

	// Deleting member role at plugin uninstall.
	$user_roles = get_option( 'wp_user_roles', array() );

	if ( is_array( $user_roles ) && ! empty( $user_roles ) ) {

		foreach ( $user_roles as $user_role ) {

			if ( 'member' == $user_role ) {

				remove_role( 'member' );
			}
		}
	}

	// Delete options at plugin uninstall.
	$plugin_options = array(
		'mwb_membership_default_plans_page',
		'mwb_membership_global_options',
	);

	foreach ( $plugin_options as $option ) {

		if ( get_option( $option ) ) {

			delete_option( $option );
		}
	}

	// Delete all membership plans at plugin uninstall.
	$mwb_membership_cpt = array(
		'post_type'      => 'mwb_cpt_membership',
		'posts_per_page' => -1,
	);

	$mwb_membership_posts = get_posts( $mwb_membership_cpt );

	if ( is_array( $mwb_membership_posts ) && ! empty( $mwb_membership_posts ) ) {

		foreach ( $mwb_membership_posts as $membership_post ) {

			wp_delete_post( $membership_post->ID, false );
		}
	}
	unregister_post_type( 'mwb_cpt_membership' );

	// Delete all members at plugin uninstall.
	$mwb_members_cpt = array(
		'post_type'      => 'mwb_cpt_members',
		'posts_per_page' => -1,
	);

	$mwb_members_posts = get_posts( $mwb_members_cpt );

	if ( is_array( $mwb_members_posts ) && ! empty( $mwb_members_posts ) ) {

		foreach ( $mwb_members_posts as $members_post ) {

			wp_delete_post( $members_post->ID, false );
		}
	}
	unregister_post_type( 'mwb_cpt_members' );
}
