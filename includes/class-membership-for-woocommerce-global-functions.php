<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/admin
 */

/**
 * Allowed html for description on admin side
 *
 * @param string $description tooltip message.
 */
function mwb_membership_for_woo_tool_tip( $description = '' ) {

	// Run only if description message is present.
	if ( ! empty( $description ) ) {

		$allowed_html = array(
			'span' => array(
				'class'    => array(),
				'data-tip' => array(),
			),
		);

		echo wp_kses( wc_help_tip( $description ), $allowed_html );
	}
}

/**
 * Returns product name and status.
 *
 * @param string $product_id Product id of a particular product.
 */
function mwb_membership_for_woo_get_product_title( $product_id = '' ) {

	if ( ! empty( $product_id ) ) {

		$result = esc_html__( 'Product not found', 'memberhsip-for-woocommerce' );

		$product = wc_get_product( $product_id );

		if ( ! empty( $product ) ) {

			if ( 'publish' != $product->get_status() ) {

				$result = esc_html__( 'Product unavailable', 'membership-for-woocommerce' );

			} else {

				$result = get_the_title( $product_id );

			}
		}

		return $result;
	}
}

/**
 * Return category name and its existance
 *
 * @param string $cat_id Category ID of a particular category.
 */
function mwb_membership_for_woo_get_category_title( $cat_id = '' ) {

	if ( ! empty( $cat_id ) ) {

		$result = esc_html__( 'Category not found', 'membership-for-woocommerce' );

		$cat_name = get_the_category_by_ID( $cat_id );

		if ( ! empty( $cat_name ) ) {

			$result = $cat_name;

		}

		return $result;
	}
}

/**
 * Membership default global options
 */
function mwb_membership_default_global_options() {

	$default_global_settings = array(

		'mwb_membership_enable_plugin'              => 'on',
		'mwb_membership_manage_content'             => 'hide_for_non_members',
		'mwb_membership_manage_content_display_msg' => '',
	);

	return $default_global_settings;

}

/**
 * Membership plan card template 1. (Default template) (Platinum)
 */
function mwb_membership_plan_card_template_1() {

	// Template 1.
	$mwb_membrship_card_global_css['parent_border_type']      = 'none';
	$mwb_membrship_card_global_css['parent_border_color']     = '';
	$mwb_membrship_card_global_css['top_vertical_spacing']    = '0';
	$mwb_membrship_card_global_css['bottom_vertical_spacing'] = '0';

	// Price section.
	$mwb_membrship_card_global_css['price_section_background_color'] = '#E5E4E2';
	$mwb_membrship_card_global_css['price_section_text_color']       = '#000000';
	$mwb_membrship_card_global_css['price_section_text_size']        = '40';

	// Buy Now button section.
	$mwb_membrship_card_global_css['button_section_background_color'] = '#E5E4E2';
	$mwb_membrship_card_global_css['button_section_text_color']       = '#000000';
	$mwb_membrship_card_global_css['button_section_text_size']        = '30';

	// Plan description section.
	$mwb_membrship_card_global_css['description_section_text_color'] = '#000000';
	$mwb_membrship_card_global_css['description_section_text_size']  = '20';

	return $mwb_membrship_card_global_css;

}

/**
 * Membership plan card template 2. (Gold)
 */
function mwb_membership_plan_card_template_2() {

	// Template 1.
	$mwb_membrship_card_global_css['parent_border_type']      = 'none';
	$mwb_membrship_card_global_css['parent_border_color']     = '';
	$mwb_membrship_card_global_css['top_vertical_spacing']    = '0';
	$mwb_membrship_card_global_css['bottom_vertical_spacing'] = '0';

	// Price section.
	$mwb_membrship_card_global_css['price_section_background_color'] = '#D4AF37';
	$mwb_membrship_card_global_css['price_section_text_color']       = '#000000';
	$mwb_membrship_card_global_css['price_section_text_size']        = '40';

	// Buy Now button section.
	$mwb_membrship_card_global_css['button_section_background_color'] = '#D4AF37';
	$mwb_membrship_card_global_css['button_section_text_color']       = '#000000';
	$mwb_membrship_card_global_css['button_section_text_size']        = '30';

	// Plan description section.
	$mwb_membrship_card_global_css['description_section_text_color'] = '#000000';
	$mwb_membrship_card_global_css['description_section_text_size']  = '20';

	return $mwb_membrship_card_global_css;

}

/**
 * Membership plan card template 3. (Silver)
 */
function mwb_membership_plan_card_template_3() {

	// Template 1.
	$mwb_membrship_card_global_css['parent_border_type']      = 'none';
	$mwb_membrship_card_global_css['parent_border_color']     = '';
	$mwb_membrship_card_global_css['top_vertical_spacing']    = '0';
	$mwb_membrship_card_global_css['bottom_vertical_spacing'] = '0';

	// Price section.
	$mwb_membrship_card_global_css['price_section_background_color'] = '#C0C0C0';
	$mwb_membrship_card_global_css['price_section_text_color']       = '#000000';
	$mwb_membrship_card_global_css['price_section_text_size']        = '40';

	// Buy Now button section.
	$mwb_membrship_card_global_css['button_section_background_color'] = '#C0C0C0';
	$mwb_membrship_card_global_css['button_section_text_color']       = '#000000';
	$mwb_membrship_card_global_css['button_section_text_size']        = '30';

	// Plan description section.
	$mwb_membrship_card_global_css['description_section_text_color'] = '#000000';
	$mwb_membrship_card_global_css['description_section_text_size']  = '20';

	return $mwb_membrship_card_global_css;

}

/**
 * Membership plan card default text feilds.
 */
function mwb_membership_plan_card_default_text() {

	$default_design_text = array(

		'mwb_membership_plan_decsription_text' => esc_html__( 'Get the membership to enjoy Unlimited services.', 'membership-for-woocommerce' ),

		'mwb_membership_plan_title'            => esc_html__( 'Unlock the Membership potential!', 'membership-for-woocommerce' ),
	);
}

/**
 * This function returns just allowed html for membership plans.
 *
 * @since    1.0.0
 */
function mwb_membership_for_woo_allowed_html() {

	// Return the complete html elements defined by us.
	$allowed_html = array(
		'input'  => array(
			'class'   => array(),
			'id'      => array(
				'membership_plan_id',
				'membership_plan_amount',
			),
			'name'    => array(),
			'value'   => array(),
			'type'    => array( 'hidden', 'checkbox' ),
			'checked' => array(),
		),
		'br'     => '',
		'ins'    => '',
		'del'    => '',
		'h2'     => '',
		'h3'     => '',
		'h4'     => '',
		'h5'     => '',
		'div'    => array(
			'class'          => array(
				'mwb_membership_plan_main_wrapper',
				'mwb_membership_plan_parent_wrapper',
				'mwb_membership_plan_price_section',
				'mwb_membership_plan_buy_now_section',
				'mwb_membership_plan_primary_section',
				'mwb_membership_plan_secondary_section',
			),
			'id'             => array(),
			'value'          => array(),
			'data-thumb'     => array(),
			'data-thumb-alt' => array(),
			'data-thumb'     => array(),
		),
		'p'      => array(
			'class' => array(
				'mwb_membership_plan_price',
				'mwb_membership_plan_description',
			),
			'id'    => array(),
			'value' => array(),
		),
		'b'      => '',
		'a'      => array(
			'href'   => array(),
			'class'  => array(
				'button',
			),
			'target' => '_blank',
		),
		'button' => array(
			'autofocus' => '',
			'name'      => array(),
			'type'      => array(),
			'value'     => array(),
			'class'     => array(
				'mwb_membership_plan_buy_now',
			),
			'id'        => array(),
		),

	);

	return $allowed_html;
}


