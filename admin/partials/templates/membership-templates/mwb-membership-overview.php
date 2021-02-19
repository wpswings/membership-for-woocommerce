<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin/partials
 */

// Exit is accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

// echo '<pre>'; print_r( $current_date = gmdate( 'Y-m-d' ) ); echo '</pre>';die;

$plan_obj = get_post_meta( 703, 'plan_obj', true );

$duration = ! empty( $plan_obj['mwb_membership_plan_duration'] ) ? $plan_obj['mwb_membership_plan_duration'] : '4' . ' ' . $plan_obj['mwb_membership_plan_duration_type'];

// Declare a date 
$Date = "2019-06-10"; 
  
// Add days to date and display it 
$date =  gmdate( 'Y-m-d', strtotime( $Date . $duration ) );
$expire_window = date('Y-m-d H:i:s', strtotime('1 day'));

$limited_memberships = get_posts(
	array(
		'numberposts' => -1,
		'fields'      => 'ids', // return only ids.
		//'post_type'   => 'posts',
		'order'       => 'ASC',
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key' => 'start_date',
				'value' => $expire_window,
				'compare' => '<='
			),
			array(
				'key' => 'end_date',
				'value' => date("Y-m-d H:i:s"),
				'compare' => '>=',
				'type' => 'DATETIME'
			)
)
	)
);

echo '<pre>'; print_r( $limited_memberships ); echo '</pre>';

$query = "SELECT COUNT(ID) FROM wp_posts WHERE wp_posts.post_date >= '2020-12-06 15:19:39' AND wp_posts.post_date <= '2017-03-09 16:19:39' AND wp_posts.post_type = 'post' AND wp_posts.post_status = 'publish'"
return;


$plan_obj = get_post_meta( 703, 'plan_obj', true );

$duration = ! empty( $plan_obj['mwb_membership_plan_duration'] ) ? $plan_obj['mwb_membership_plan_duration'] : '4' . ' ' . $plan_obj['mwb_membership_plan_duration_type'];

echo '<pre>'; print_r( $duration ); echo '</pre>';

//echo '<pre>'; print_r( get_post_meta( 703, 'plan_obj', true ) ); echo '</pre>';
//echo '<pre>'; print_r( get_post_meta( 703, 'member_status', true ) ); echo '</pre>';

// $date = date_create( "2019-05-10" ); 
  
// // Use date_add() function to add date object 
// $result = date_add( $date, date_interval_create_from_date_string( "1 days" ) );

// echo '<pre>'; print_r( $result ); echo '</pre>';

// Declare a date 
$Date = "2019-06-10"; 
  
// Add days to date and display it 
$date =  gmdate( 'Y-m-d', strtotime( $Date . $duration ) );

echo '<pre>'; print_r( strtotime( $date  ) ); echo '</pre>';

?>

<!-- Heading start -->
<div class="mwb_membership_Overview">
	<h2><?php esc_html_e( 'Membership Overview', 'membership-for-woocommerce' ); ?></h2>
</div>


