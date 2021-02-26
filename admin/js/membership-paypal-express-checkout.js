jQuery( document ).ready( function( $ ){

	$( "#woocommerce_membership-paypal-smart-buttons_test_mode" ).on( "change", function( e ){
		e.preventDefault();

		var live = $( "#woocommerce_membership-paypal-smart-buttons_live_client_id" ).closest( "tr" );
		var	test_sandbox = $( "#woocommerce_membership-paypal-smart-buttons_sb_client_id" ).closest( "tr" );

		if ( $( this ).is( ":checked" ) ) {

            test_sandbox.show();
            $( "#woocommerce_membership-paypal-smart-buttons_sandbox_credentials" ).show(500);
            $( "#woocommerce_membership-paypal-smart-buttons_sandbox_credentials" ).next('p').show(500);

            live.hide();
            $( "#woocommerce_membership-paypal-smart-buttons_live_credentials" ).hide(500);
            $( "#woocommerce_membership-paypal-smart-buttons_live_credentials" ).next('p').hide(500);


		} else {

            test_sandbox.hide();
            $( "#woocommerce_membership-paypal-smart-buttons_sandbox_credentials" ).hide(500);
            $( "#woocommerce_membership-paypal-smart-buttons_sandbox_credentials" ).next('p').hide(500);

            live.show();
            $( "#woocommerce_membership-paypal-smart-buttons_live_credentials" ).show(500);
            $( "#woocommerce_membership-paypal-smart-buttons_live_credentials" ).next('p').show(500);
		}
		
	}).change();
});