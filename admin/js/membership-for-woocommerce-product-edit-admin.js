jQuery(document).ready(function($) { 
    $('.attach-membership_options').addClass('show_if_simple').show();

    $(document).on('click', '.product_data_tabs', function(){
        if( $(this).find('.attach-membership_options').hasClass('active') ) {
            $('.wps_membership_dropdown').removeClass('hidden');
        } else {
            $('.wps_membership_dropdown').addClass('hidden');
        }
    } );

    
    jQuery(document).on( 'click', '#post-' + wps_product_edit_param.prod_id + ' td div.row-actions .trash', function(e){
        if( confirm( 'Please do not delete this product , Membership functionality will not work!' ) ) {
            
        } else {
            e.preventDefault();
        }
    } );

    jQuery('#wps_membership_plan_offer_price').prop( 'max', '100' );
    jQuery(document).on('change', '#wps_membership_plan_offer_price_type_id', function(){
        
        var wps_discount_type = jQuery(this).val();
        if ( 'fixed' == wps_discount_type ) {
            
            jQuery('#wps_membership_plan_offer_price').removeAttr( 'max' );
        } else {

            jQuery('#wps_membership_plan_offer_price').attr( 'max', '100' );
        }
    });

    jQuery('#wps_membership_product_offer_price').prop( 'max', '100' );
    jQuery(document).on('change', '#wps_membership_product_offer_price_type_id', function(){
        
        var wps_discount_type = jQuery(this).val();
        if ( 'fixed' == wps_discount_type ) {
            
            jQuery('#wps_membership_product_offer_price').removeAttr( 'max' );
        } else {

            jQuery('#wps_membership_product_offer_price').attr( 'max', '100' );
        }
    });

    // Unchecked Enable Free Trial Settings when click on Subscription Membership.
    jQuery(document).on('change', 'input[type="checkbox"][class="wps_membership_subscription_"]', function() {

        if ( ! jQuery(this).is(':checked') ) {

            $('.wps_mfw_enable_free_trial_settings').prop( 'checked', false );
        }
    })

    // checked whether his parent settings is enable or not then enable this settings.
    jQuery(document).on('change', 'input[type="checkbox"][class="wps_mfw_enable_free_trial_settings"]', function() {
        if ( ! jQuery('input[type="checkbox"][class="wps_membership_subscription_"]').is(':checked') ) {

            jQuery(this).prop( 'checked', false );
        }
    })

 } );