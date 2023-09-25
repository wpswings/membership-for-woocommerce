<?php
/**
 * Registers a new shipping method for memberships.
 *
 * @link       https://wpswings.com
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
 */
class WPS_Membership_Free_Shipping_Method extends WC_Shipping_Method {

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
	 *
	 * @return void
	 */
	public function __construct( $instance_id = 0 ) {

		$this->id                 = 'wps_membership_shipping'; // Id for your shipping method. Should be uunique.
		$this->method_title       = __( 'Membership Shipping', 'membership-for-woocommerce' );  // Title shown in admin.
		$this->method_description = __( 'Membership shipping allows free shipping to active members.', 'membership-for-woocommerce' ); // Description shown in admin.
		$this->instance_id        = absint( $instance_id );
		$this->title              = __( 'Membership Shipping', 'membership-for-woocommerce' );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',
		);

		$this->global_class = Membership_For_Woocommerce_Global_Functions::get();
		$this->init();
	}

	/**
	 * Init your settings.
	 *
	 * @return void
	 */
	public function init() {

		// Load the settings API.
		$this->init_form_fields(); // Override the method to add your own settings.
		$this->init_settings(); // Loads settings you previously init.

		// Save settings in admin if you have any defined.
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'admin_footer', array( 'wp_Membership_Free_Shipping_Method', 'wps_enqueue_admin_js' ), 10 ); // Priority needs to be higher than wc_print_js (25).

		// Define user set variables.
		$this->enabled            = ! empty( $this->get_option( 'enabled' ) ) ? $this->get_option( 'enabled' ) : 'no';
		$this->title              = $this->get_option( 'title' );
		$this->requires           = $this->get_option( 'requires' );
		$this->allowed_membership = $this->get_option( 'allowed_membership' );
	}

	/**
	 * Creating form fields
	 *
	 * @return void
	 */
	public function init_form_fields() {

		$all_memberships = $this->global_class->format_all_membership();

		$this->instance_form_fields = array(

			'title'              => array(
				'title'       => __( 'Title', 'membership-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Title to be displayed on front-end.', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
				'default'     => __( 'Membership Shipping', 'membership-for-woocommerce' ),
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

		if ( in_array( $this->requires, array( 'active_plan' ) ) ) {

			$plan_ids = $this->get_option( 'allowed_membership' );

			if ( ! empty( $plan_ids ) && is_array( $plan_ids ) ) {

				foreach ( $plan_ids as $plan_id ) {

					$product_ids       = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_target_ids', true );
					$cat_ids           = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_target_categories', true );
					$tag_ids           = wps_membership_get_meta_data( $plan_id, 'wps_membership_plan_target_tags', true );
					$cart_items_ids    = $this->global_class->cart_item_ids();
					$cart_item_cat_ids = $this->global_class->cart_item_cat_ids();
					$cart_item_tag_ids = $this->global_class->cart_item_tag_ids();

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

					if ( ! empty( $tag_ids ) && is_array( $tag_ids ) && ! empty( $cart_item_tag_ids ) && is_array( $cart_item_tag_ids ) ) {

						foreach ( $tag_ids as $tag_id ) {

							if ( in_array( $tag_id, $cart_item_tag_ids ) ) {

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

		/**
		 * Filter for shipping availability.
		 *
		 * @since 1.0.0
		 */
		return apply_filters( 'wps_membership_shipping_' . $this->id . '_is_available', $is_available, $package, $this );
	}

	/**
	 * Calculate_shipping function.
	 *
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
	public static function wps_enqueue_admin_js() {

		wc_enqueue_js(
			"jQuery( function( $ ) {
				function wcFreeShippingShowHideAllowedMembershipField( el ) {
					var form = $( el ).closest( 'form' );
					var allowedmembershipfield = $( '#woocommerce_wps_membership_shipping_allowed_membership', form ).closest( 'tr' );
					if ( '' === $( el ).val() ) {
						allowedmembershipfield.hide();
						
					} else {
						allowedmembershipfield.show();
					}	
				}

				$( document.body ).on( 'change', '#woocommerce_wps_membership_shipping_requires', function() {
					wcFreeShippingShowHideAllowedMembershipField( this );
				});

				// Change while load.
				$( '#woocommerce_wps_membership_shipping_requires' ).change();
				$( document.body ).on( 'wc_backbone_modal_loaded', function( evt, target ) {
					
					if ( 'wc-modal-shipping-method-settings' === target ) {
						wcFreeShippingShowHideAllowedMembershipField( $( '#wc-backbone-modal-dialog #woocommerce_wps_membership_shipping_requires', evt.currentTarget ) );
					}
				});
			});
			
			jQuery( function( $ ) {
				jQuery( document.body ).on( 'click', '.wc-shipping-zone-method-settings', function () {
					jQuery('.wps-membership-shipping-method').select2();
				});
			});"
		);
	}
}
