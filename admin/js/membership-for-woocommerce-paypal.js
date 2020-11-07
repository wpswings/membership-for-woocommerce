jQuery(document).ready( function($){
	jQuery('#woocommerce_membership-for-woo-paypal-gateway_testmode').on("change",function(e){
		e.preventDefault();
		var live = jQuery('#woocommerce_membership-for-woo-paypal-gateway_api_username,#woocommerce_membership-for-woo-paypal-gateway_api_password, #woocommerce_membership-for-woo-paypal-gateway_api_signature ' ).closest('tr');
		var	test_sandbox = jQuery('#woocommerce_membership-for-woo-paypal-gateway_sandbox_api_username, #woocommerce_membership-for-woo-paypal-gateway_sandbox_api_password, #woocommerce_membership-for-woo-paypal-gateway_sandbox_api_signature' ).closest('tr');
		
		if($(this).is(':checked'))
		{
			test_sandbox.show();
			live.hide();
		}
		else
		{
			test_sandbox.hide();
			live.show();
		}
	}).change();
});