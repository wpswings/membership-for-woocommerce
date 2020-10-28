<?php
// echo dirname(MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH);
// die;
include_once(dirname(MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH) . '/woocommerce/woocommerce.php');
include_once(dirname(MEMBERSHIP_FOR_WOOCOMMERCE_DIRPATH) . '/woocommerce/includes/shipping/free-shipping/class-wc-shipping-free-shipping.php');

class MWB_Free_Shipping extends WC_Shipping_Free_Shipping {

	public function init_form_fields() {

		$this->instance_form_fields['requires']['options']['membership'] = __( 'Active Membership', 'woocommerce' );
		
	}
}

$class = new MWB_Free_Shipping();
$class->init_form_fields();
?>