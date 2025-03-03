jQuery( document ).ready( function( $ ){

    jQuery(document).find('.wps_org_offer_plan_id').select2();
    // Target products search.
    jQuery( ".wc-membership-product-search" ).select2({

        ajax:{

            url: add_new_obj.ajax_url,
            dataType: "json",
            delay:    200,
            data: function( params ) {
                return {
                    q: params.term,
                    action: "wps_membership_search_products_for_membership",
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
        minimumInputLength: 3 // The minimum number of characters to input to perform a search.
    });


    // Target category search.
    jQuery( ".wc-membership-product-category-search" ).select2({

        ajax:{

            url: add_new_obj.ajax_url,
            dataType: "json",
            delay: 200,
            data: function( params ) {
                return {
                    q: params.term,
                    action: "wps_membership_search_product_categories_for_membership",
                };
            },
            processResults: function( data ) {
                var options = [];
                if ( data ) {

                    $.each( data, function( index, text ) {

                        text[1]+='( #'+text[0]+')';
                        options.push( { id: text[0], text: text[1] } )

                    });
                }
                return {
                    results: options
                };
            },
            cache: true
        },
        minimumInputLength: 3 // The minimum number of characters to input to perform a search.
    });


    // set limit when fixed and discount type is selected in new plan price.
    jQuery(document).on('change', '#wps_membership_plan_for_discount_offer', function(){
        
        var plan_id = jQuery(this).val();
        
        jQuery('#wps_membership_discount_amount_'+plan_id).attr( 'max', '100' );
        jQuery(document).on('change', '#wps_membership_discount_type_'+plan_id, function(){

            var discount_type = jQuery(this).val();
            
            if ( 'fixed' == discount_type ) {
            
                jQuery('#wps_membership_discount_amount_'+plan_id).removeAttr( 'max' );
            } else {
    
                jQuery('#wps_membership_discount_amount_'+plan_id).attr( 'max', '100' );
            }
        });
    });

    // disabled membership template name.
    jQuery(document).find('#wps_wpr_whatsapp_msg_temp_name').prop('disabled', true);
    // send offer notification on whatsapp.
    jQuery(document).on('click', '#wps_wpr_send_on_whatsap_btn', function(){

        var wps_org_offer_plan_id = jQuery('.wps_org_offer_plan_id').val();
        var wps_wpr_offer_message = jQuery('.wps_wpr_offer_message').val();
        if (Array.isArray(wps_org_offer_plan_id) && wps_org_offer_plan_id.length !== 0) {

            jQuery('.wps_wpr_offer_msg_notice').hide();
            jQuery('.wps_wpr_whatsapp_loader').show();
            jQuery('#wps_wpr_send_on_whatsap_btn').prop('disabled', true);
            var data = {
                'action'                : 'send_offer_message_on_whatsapp',
                'nonce'                 : add_new_obj.wps_nonce,
                'wps_org_offer_plan_id' : wps_org_offer_plan_id,
                'wps_wpr_offer_message' : wps_wpr_offer_message,
            };

            jQuery.ajax({
                'method'   : 'POST',
                'url'      : add_new_obj.ajax_url,
                'dataType' : 'json',
                'data'     : data,
                success    : function(response) {

                    jQuery('.wps_wpr_whatsapp_loader').hide();
                    jQuery('#wps_wpr_send_on_whatsap_btn').prop('disabled', false);
                    if ( ( response.messages && Array.isArray(response.messages) ) && ( response.messages[0] && response.messages[0].message_status ) ) {

                        jQuery('.wps_wpr_offer_msg_notice').show();
                        jQuery('.wps_wpr_offer_msg_notice').css('color', 'green');
                        jQuery('.wps_wpr_offer_msg_notice').html(response.messages[0].message_status);
                    } else if ( response.error ) {

                        jQuery('.wps_wpr_offer_msg_notice').show();
                        jQuery('.wps_wpr_offer_msg_notice').css('color', 'red');
                        jQuery('.wps_wpr_offer_msg_notice').html(response.error.message);
                    } else {

                        jQuery('.wps_wpr_offer_msg_notice').show();
                        jQuery('.wps_wpr_offer_msg_notice').css('color', 'red');
                        jQuery('.wps_wpr_offer_msg_notice').html(response.msg);
                    }
                }
            });
        } else {

            jQuery('.wps_wpr_offer_msg_notice').show();
            jQuery('.wps_wpr_offer_msg_notice').css('color', 'red');
            jQuery('.wps_wpr_offer_msg_notice').html('Please choose a plan !!');
            return false;
        }
    });
    
    // open whatsapp sample template.
    jQuery(document).on('click', '.wps_wpr_preview_whatsapp_sample', function(e){

        jQuery(document).find('.wps_wpr_preview_whatsapp_sample').css('color', '#2271b1');
        e.preventDefault();
        jQuery('.wps_wpr_preview_template_img').show();
    });

    // Hide modal when clicking outside the image.
    jQuery(document).on('click', '.wps_wpr_preview_template_img', function(){

        jQuery('.wps_wpr_preview_template_img').hide();
    });

    // make same while clicking on click here url for creating token.
    jQuery(document).on('click', '.wps_wpr_create_whatsapp_token_link', function(){

        jQuery(document).find('.wps_wpr_create_whatsapp_token_link').css('color', '#2271b1');
    });

    // Send SMS notification.
    jQuery(document).on('click', '#wps_wpr_send_on_sms_btn', function(){
        
        var wps_org_offer_plan_id = jQuery('.wps_org_offer_plan_id').val();
        var wps_wpr_offer_message = jQuery('.wps_wpr_offer_message').val();
        if (Array.isArray(wps_org_offer_plan_id) && wps_org_offer_plan_id.length !== 0) {

            jQuery('.wps_wpr_offer_msg_notice').hide();
            jQuery('.wps_wpr_whatsapp_loader').show();
            jQuery('#wps_wpr_send_on_sms_btn').prop('disabled', true);
            var data = {
                'action'                : 'send_offer_message_via_sms',
                'nonce'                 : add_new_obj.wps_nonce,
                'wps_org_offer_plan_id' : wps_org_offer_plan_id,
                'wps_wpr_offer_message' : wps_wpr_offer_message,
            };
            jQuery.ajax({
                'method'   : 'POST',
                'url'      : add_new_obj.ajax_url,
                'dataType' : 'json',
                'data'     : data,
                success    : function(response) {

                    jQuery('.wps_wpr_whatsapp_loader').hide();
                    jQuery('#wps_wpr_send_on_sms_btn').prop('disabled', false);
                    if ( response.result == true ) {

                        jQuery('.wps_wpr_offer_msg_notice').show();
                        jQuery('.wps_wpr_offer_msg_notice').css('color', 'green');
                        jQuery('.wps_wpr_offer_msg_notice').html(response.msg);
                    } else {

                        jQuery('.wps_wpr_offer_msg_notice').show();
                        jQuery('.wps_wpr_offer_msg_notice').css('color', 'red');
                        jQuery('.wps_wpr_offer_msg_notice').html(response.msg);
                    }           
                }
            });
        } else {

            jQuery('.wps_wpr_offer_msg_notice').show();
            jQuery('.wps_wpr_offer_msg_notice').css('color', 'red');
            jQuery('.wps_wpr_offer_msg_notice').html('Please choose a plan !!');
            return false;
        }
    });

    // send offer notify on Mail.
    jQuery(document).on('click', '#wps_wpr_send_on_mail_btn', function(){
        
        var wps_org_offer_plan_id = jQuery('.wps_org_offer_plan_id').val();
        var wps_wpr_offer_message = jQuery('.wps_wpr_offer_message').val();
        if (Array.isArray(wps_org_offer_plan_id) && wps_org_offer_plan_id.length !== 0) {

            jQuery('.wps_wpr_offer_msg_notice').hide();
            jQuery('.wps_wpr_whatsapp_loader').show();
            jQuery('#wps_wpr_send_on_mail_btn').prop('disabled', true);
            var data = {
                'action'                : 'send_offer_messages_via_email',
                'nonce'                 : add_new_obj.wps_nonce,
                'wps_org_offer_plan_id' : wps_org_offer_plan_id,
                'wps_wpr_offer_message' : wps_wpr_offer_message,
            };
            jQuery.ajax({
                'method'   : 'POST',
                'url'      : add_new_obj.ajax_url,
                'dataType' : 'json',
                'data'     : data,
                success    : function(response) {

                    jQuery('.wps_wpr_whatsapp_loader').hide();
                    jQuery('#wps_wpr_send_on_mail_btn').prop('disabled', false);
                    if ( response.result == true ) {

                        jQuery('.wps_wpr_offer_msg_notice').show();
                        jQuery('.wps_wpr_offer_msg_notice').css('color', 'green');
                        jQuery('.wps_wpr_offer_msg_notice').html(response.msg);
                    } else {

                        jQuery('.wps_wpr_offer_msg_notice').show();
                        jQuery('.wps_wpr_offer_msg_notice').css('color', 'red');
                        jQuery('.wps_wpr_offer_msg_notice').html(response.msg);
                    }
                }
            });
        } else {

            jQuery('.wps_wpr_offer_msg_notice').show();
            jQuery('.wps_wpr_offer_msg_notice').css('color', 'red');
            jQuery('.wps_wpr_offer_msg_notice').html('Please choose a plan !!');
            return false;
        }
    });
});
