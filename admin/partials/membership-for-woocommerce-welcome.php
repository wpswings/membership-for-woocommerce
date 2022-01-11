<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link  https://makewebbetter.com/
 * @since 1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}
global $mfw_mwb_mfw_obj;
$mfw_default_tabs = $mfw_mwb_mfw_obj->mwb_mfw_plug_default_tabs();
$mfw_tab_key = '';
?>
<header>
	<?php
	// desc - This hook is used for trial.
	do_action( 'mwb_mfw_settings_saved_notice' );
	?>
	<div class="mwb-header-container mwb-bg-white mwb-r-8">
		<h1 class="mwb-header-title"><?php echo esc_attr( __( 'WP Swings' ) ); ?></h1>
	</div>
</header>
<main class="mwb-main mwb-bg-white mwb-r-8">
	<section class="mwb-section">
		<div>
			<?php
				// desc - This hook is used for trial.
			do_action( 'mwb_mfw_before_common_settings_form' );
				// if submenu is directly clicked on woocommerce.
			$mfw_genaral_settings = apply_filters(
				'mfw_home_settings_array',
				array(
					array(
						'title' => __( 'Enable Tracking', 'membership-for-woocommerce' ),
						'type'  => 'radio-switch',
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
					$mfw_general_html = $mfw_mwb_mfw_obj->mwb_mfw_plug_generate_html( $mfw_genaral_settings );
					echo esc_html( $mfw_general_html );
					wp_nonce_field( 'admin_save_data', 'mwb_tabs_nonce' );
					?>
				</div>
			</form>
			<?php
			do_action( 'mwb_mfw_before_common_settings_form' );
			$all_plugins = get_plugins();
			?>
		</div>
	</section>
	<style type="text/css">
		.cards {
			   display: flex;
			   flex-wrap: wrap;
			   padding: 20px 40px;
		}
		.card {
			flex: 1 0 518px;
			box-sizing: border-box;
			margin: 1rem 3.25em;
			text-align: center;
		}

	</style>
	<div class="centered">
		<section class="cards">
			<?php foreach ( get_plugins() as $key => $value ) : ?>
				<?php if ( 'WP Swings' === $value['Author'] ) : ?>
					<article class="card">
						<div class="container">
							<h4><b><?php echo esc_html( $value['Name'] ); ?></b></h4> 
							<p><?php echo esc_html( $value['Version'] ); ?></p> 
							<p><?php echo esc_html( $value['Description'] ); ?></p>
						</div>
					</article>
				<?php endif; ?>
			<?php endforeach; ?>
		</section>
	</div>
