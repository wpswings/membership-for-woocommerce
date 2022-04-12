<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wpswings.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin/partials
 */

/**
 * Template Name: WPS Membership For Woocommerce Template.
 * This template will only display the content you entered in the page editor
 */

if ( ! is_user_logged_in() ) {

	wp_safe_redirect( wc_get_page_permalink( 'shop' ) );

} elseif ( ! isset( $_GET['plan_id'] ) && ! isset( $_GET['prod_id'] ) ) {

	wp_safe_redirect( wc_get_page_permalink( 'shop' ) );
}


?>

<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php wp_head(); ?>

		<?php // Add tracking scripts. ?>
	</head>

	<body>
		<?php
		while ( have_posts() ) :
			the_post();
			the_content();
			endwhile;
		?>
		<?php wp_footer(); ?>
	</body>
</html>
