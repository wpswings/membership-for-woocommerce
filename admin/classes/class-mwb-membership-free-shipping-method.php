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
		$this->enabled            = 'yes';
		$this->title              = __( 'Membership Shipping', 'membership-for-woocommerce' );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',
		);

		$this->init();
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
		$this->title = $this->get_option( 'title' );
		$this->cost  = $this->get_option( 'cost' );

		// Save settings in admin if you have any defined.
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Creating form fields
	 *
	 * @return void
	 */
	public function init_form_fields() {

		$this->instance_form_fields = array(

			'enabled' => array(
				'title'       => __( 'Enable', 'membership-for-woocommerce' ),
				'type'        => 'checkbox',
				'description' => __( 'Enable the membership shipping method.', 'membership-for-woocommerce' ),
				'default'     => 'yes',
			),

			'title'   => array(
				'title'       => __( 'Title', 'membership-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Title to be displayed on front-end.', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
				'default'     => __( 'Membership Shipping', 'membership-for-woocommerce' ),
			),

			'cost'    => array(
				'title'       => __( 'Cost', 'membership-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Enter cost for mmebership shipping method', 'membership-for-woocommerce' ),
				'desc_tip'    => true,
			),

		);

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
			'id'       => $this->id,
			'label'    => $this->title,
			'cost'     => $this->cost,
			'calc_tax' => 'per_item',
		);

		// Register the rate.
		$this->add_rate( $rate );
	}
}
