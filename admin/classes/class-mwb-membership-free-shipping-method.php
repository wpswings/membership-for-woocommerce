<?php
/**
 * Registers a new shipping method for memberships.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes
 */

/**
 * Registers a new shipping method for memberships.
 *
 * This class defines all code necessary to add a new shiiping method.
 *
 * @since      1.0.0
 * @package    Membership_For_Woocommerce
 * @subpackage Membership_For_Woocommerce/includes
 * @author     Make Web Better <plugins@makewebbetter.com>
 */
class Mwb_Membership_Free_Shipping_Method extends WC_Shipping_Method {

	/**
	 * Requires option.
	 *
	 * @var string
	 */
	public $requires = '';

	/**
	 * Creating Instance of the global functions class.
	 *
	 * @var object
	 */
	public $global_class;

	/**
	 * Constructor for your shipping class
	 *
	 * @param mixed $instance_id used to store instance.
	 * @access public
	 * @return void
	 */
	public function __construct( $instance_id = 0 ) {

		$this->id                 = 'mwb_membership_shipping'; // Id for your shipping method. Should be uunique.
		$this->method_title       = __( 'Membership Shipping', 'membership-for-woocommerce' );  // Title shown in admin.
		$this->method_description = __( 'Membership shipping allows free shipping to active members.', 'membership-for-woocommerce' ); // Description shown in admin.
		$this->instance_id        = absint( $instance_id );
		// $this->enabled          = 'yes';
		$this->title              = __( 'Membership Shipping', 'membership-for-woocommerce' );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',
		);

		$this->init();

		$this->global_class = Membership_For_Woocommerce_Global_Functions::get();
	}

	/**
	 * Init your settings
	 *
	 * @access public
	 * @return void
	 */
	public function init() {

		// Load the settings API.
		$this->init_form_fields(); // Override the method to add your own settings.
		$this->init_settings(); // Loads settings you previously init.

		// Define user set variables.
		$this->enabled            = ! empty( $this->get_option( 'enabled' ) ) ? $this->get_option( 'enabled' ) : 'no';
		$this->title              = $this->get_option( 'title' );
		$this->requires           = $this->get_option( 'requires' );
		$this->allowed_membership = $this->get_option( 'allowed_membership' );

		// Save settings in admin if you have any defined.
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'admin_footer', array( 'Mwb_Membership_Free_Shipping_Method', 'mwb_enqueue_admin_js' ), 10 ); // Priority needs to be higher than wc_print_js (25).
	}

	/**
	 * Creating form fields
	 *
	 * @return void
	 */
	public function init_form_fields() {

		$this->instance_form_fields = array(

			'enabled'            => array(
				'title'       => __( 'Enable', 'membership-for-woocommerce' ),
				'type'        => 'checkbox',
				'description' => __( 'Enable the membership shipping method.', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
				'default'     => '',
			),

			'title'              => array(
				'title'       => __( 'Title', 'membership-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Title to be displayed on front-end.', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
				'default'     => __( 'Membership Shipping', 'membership-for-woocommerce' ),
			),

			'requires'           => array(
				'title'       => __( 'Membership Free Shipping Requires', 'membership-for-woocommerce' ),
				'type'        => 'select',
				'class'       => 'wc-enhanced-select',
				'default'     => '',
				'options'     => array(
					''            => __( 'N/A', 'membership-for-woocommerce' ),
					'active_plan' => __( 'An Active Membership', 'memberhsip-for-woocommerce' ),
				),
				'description' => __( 'Enter cost for mmebership shipping method', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
			),

			'allowed_membership' => array(
				'title'             => __( 'Allowed Memberships', 'membership-for-woocommerce' ),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select mwb-membership-shipping-method',
				'default'           => '',
				'description'       => __( 'Select the active membership plans on which you want to offer free shipping', 'membership-for-woocommerce' ),
				'desc_tip'          => true,
				'options'           => array(
					'Hello',
					'world',
				),
				'custom_attributes' => array(
					'data-placeholder' => __( 'Select Membership Plans', 'woocommerce' ),
				),

			),

		);

	}

	/**
	 * Get setting form fields for instances of this shipping method within zones.
	 *
	 * @return array
	 */
	public function get_instance_form_fields() {
		return parent::get_instance_form_fields();
	}

	/**
	 * See if free shipping is available based on the package and cart.
	 *
	 * @param array $package Shipping package.
	 * @return bool
	 */
	public function is_available( $package ) {

		$plan_active = false;

		if ( in_array( $this->requires, array( 'active_plan' ), true ) ) {

			$plan_ids = $this->get_option( 'requires' );

			if ( ! empty( $plan_ids ) && is_array( $plan_ids ) ) {

				foreach ( $plan_ids as $plan_id ) {

					$product_ids       = get_post_meta( $plan_id, 'mwb_membership_plan_target_ids', true );
					$cat_ids           = get_post_meta( $plan_id, 'mwb_membership_plan_target_categories', true );
					$cart_items_ids    = $this->global_class->cart_item_ids();
					$cart_item_cat_ids = $this->global_class->cart_item_cat_ids();

					if ( ! empty( $product_ids ) && is_array( $product_ids ) && ! empty( $cart_items_ids ) && is_array( $cart_items_ids ) ) {

						foreach ( $product_ids as $product_id ) {

							if ( in_array( $product_id, $cart_items_ids ) ) {

								$plan_active = true;
								break;
							}
						}
					}

					if ( ! empty( $cat_ids ) && is_array( $cat_ids ) && ! empty( $cart_item_cat_ids ) && is_array( $cart_item_cat_ids ) ) {

						foreach ( $cat_ids as $cat_id ) {

							if ( in_array( $cat_id, $cart_item_cat_ids ) ) {

								$plan_active = true;
								break;

							}
						}
					}
				}
			}
		}

		switch ( $this->requires ) {

			case 'active_plan':
				$is_available = $plan_active;
				break;

			default:
				$is_available = true;
				break;
		}

		return apply_filters( 'mwb_memberhsip_shipping_' . $this->id . '_is_available', $is_available, $package, $this );
	}

	/**
	 * Available membershi plans.
	 */
	public function mwb_membership_available_plans() {

		$result = array();

		$args = array(
			's'           => ! empty( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '',
			'post_type'   => 'mwb_cpt_membership',
			'post_status' => 'publish',
			'numberposts' => -1,
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {

			while ( $query->have_posts() ) {

				$query->the_post();

				$title = ( mb_strlen( $query->post->post_title ) > 50 ) ? mb_substr( $query->post->post_title, 0, 49 ) . '...' : $query->post->post_title;

				$result[] = array( $query->post->ID, $title );
			}
		}

		echo json_encode( $result );

		wp_die();
	}

	/**
	 * Calculate_shipping function.
	 *
	 * @access public
	 * @param mixed $package used to store package.
	 * @return void
	 */
	public function calculate_shipping( $package = array() ) {

		$rate = array(
			'label'   => $this->title,
			'cost'    => 0,
			'taxes'   => false,
			'package' => $package,
		);

		// Register the rate.
		$this->add_rate( $rate );
	}

	/**
	 * Enqueue JS to handle free shipping options.
	 *
	 * Static so that's enqueued only once.
	 */
	public static function mwb_enqueue_admin_js() {

		wc_enqueue_js(
			"jQuery( function( $ ) {
				function wcFreeShippingShowHideAllowedMembershipField( el ) {
					var form = $( el ).closest( 'form' );
					var allowedmembershipfield = $( '#woocommerce_mwb_membership_shipping_allowed_membership', form ).closest( 'tr' );
					if ( '' === $( el ).val() ) {
						allowedmembershipfield.hide();
						
					} else {
						allowedmembershipfield.show();
					}	
				}

				$( document.body ).on( 'change', '#woocommerce_mwb_membership_shipping_requires', function() {
					wcFreeShippingShowHideAllowedMembershipField( this );
				});

				// Change while load.
				$( '#woocommerce_mwb_membership_shipping_requires' ).change();
				$( document.body ).on( 'wc_backbone_modal_loaded', function( evt, target ) {
					
					if ( 'wc-modal-shipping-method-settings' === target ) {
						wcFreeShippingShowHideAllowedMembershipField( $( '#wc-backbone-modal-dialog #woocommerce_mwb_membership_shipping_requires', evt.currentTarget ) );
					}
				});
			});
			
			jQuery( function( $ ) {
				
				jQuery('.mwb-membership-shipping-method').select2({
							
					ajax:{

						url: ajaxurl,
						dataType: 'json',
						delay: 200,
						data: function( params ) {
							return {
								q: params.term,
								action: 'mwb_membership_available_plans',
							};
						},
						processResults: function( data ) {
							var options = [];
							if ( data ) {
			
								$.each( data, function( index, text ) {
									text[1]+='( #'+text[0]+')';
									options.push( { id: text[0], text: text[1] } );
								});
							}
							return {
								results:options
							};
						},
						cache: true
					},
					minimumInputLength: 3

				});
			});"
		);
	}
}
