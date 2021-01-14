jQuery( document ).ready( function( $ ){

	$( "#woocommerce_membership-paypal-express-checkout_test_mode" ).on( "change", function( e ){
		e.preventDefault();

		var live = $( "#woocommerce_membership-paypal-express-checkout_api_username, #woocommerce_membership-paypal-express-checkout_api_password, #woocommerce_membership-paypal-express-checkout_api_signature" ).closest( "tr" );
		var	test_sandbox = $( "#woocommerce_membership-paypal-express-checkout_sandbox_api_username, #woocommerce_membership-paypal-express-checkout_sandbox_api_password, #woocommerce_membership-paypal-express-checkout_sandbox_api_signature" ).closest( "tr" );

		if ( $( this ).is( ":checked" ) ) {

            test_sandbox.show();
            $( "#woocommerce_membership-paypal-express-checkout_sandbox_api_credentials" ).show();
            $( "#woocommerce_membership-paypal-express-checkout_sandbox_api_credentials" ).next('p').show();

            live.hide();
            $( "#woocommerce_membership-paypal-express-checkout_api_credentials" ).hide();
            $( "#woocommerce_membership-paypal-express-checkout_api_credentials" ).next('p').hide();


		} else {

            test_sandbox.hide();
            $( "#woocommerce_membership-paypal-express-checkout_sandbox_api_credentials" ).hide();
            $( "#woocommerce_membership-paypal-express-checkout_sandbox_api_credentials" ).next('p').hide();

            live.show();
            $( "#woocommerce_membership-paypal-express-checkout_api_credentials" ).show();
            $( "#woocommerce_membership-paypal-express-checkout_api_credentials" ).next('p').show();
		}
		
	}).change();
});