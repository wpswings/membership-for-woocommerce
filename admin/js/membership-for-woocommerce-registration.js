jQuery(document).ready(function($){
    var ajaxurl = admin_registration_ajax_obj.ajaxurl;
    $(document).on('change', '#wps_mfw_reg_access_type', function(){
        if( 'limited' == $(this).val() ){

            $('.wps_mfw_plan_expiry_class').removeClass('hidden');
            
        } else {
            $('.wps_mfw_plan_expiry_class').addClass('hidden');
        }
    });

    //Form js
    $("#wps_member_user_reg").select2();

  
    jQuery(document).on('click', '#wps_create_membership_plan_button', function () {
        
        var wps_plan_check = true;
        var plan_title = $('#wps_mfw_reg_plan_name').val();
        var plan_price = $('#wps_mfw_reg_plan_price').val();
        var plan_access_type = $('#wps_mfw_reg_access_type').val();
        var plan_duration = $('#wps_mfw_reg_expiry_num').val();
        var plan_duration_type = $('#wps_mfw_reg_expiry_time').val();

        if( plan_title == '' ){
            wps_plan_check = false;
            alert(admin_registration_ajax_obj.plan_name_error);
        }
        if( plan_price <= 0 ){
            wps_plan_check = false;
            alert(admin_registration_ajax_obj.plan_price_error);
        }
        
        if( plan_access_type == 'limited' ){
            if( plan_duration <= 0 ) {
                wps_plan_check = false;
                alert(admin_registration_ajax_obj.valid_access_msg)
            }
           
        }

        if( wps_plan_check ) {

            $.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: "wps_membership_create_plan_reg",
                    plan_price: plan_price,
                    plan_access_type: plan_access_type,
                    plan_title: plan_title,
                    plan_duration : plan_duration,
                    plan_duration_type : plan_duration_type,
                    nonce : admin_registration_ajax_obj.nonce
                },
    
                success: function (response) {
                    
                   
                    jQuery('<h4 class="wps-plan-msg" >'+ admin_registration_ajax_obj.plan_created_msg +'</h4>').insertBefore(jQuery('#wps_create_membership_plan_button'));
                    setTimeout(function () {
                        window.location.reload(1);
                     }, 3000);
                    
                }
            });
        }
        

    });


$('div .wps_membership_plan_fields').hide();

$(document).on('change','#wps_membership_plan_for_restriction, #wps_membership_plan_for_discount_offer, #wps_membership_content_restriction', function(){
    
    $('div .wps_membership_plan_fields').hide();
    $('div .wps_reg_plan_' + $(this).val()).show();
});

$(document).on('click', '.wps_membership_checkbox', function(){
    if( $(this).is(':checked') ){
        $(this).val('on');
    } else{
        $(this).val('off');
    }
})

    // ======= API feature start here =======

    // Hide ans show generate button, when consumer key is generated.
    var is_api_setting_enable = jQuery(document).find('#wps_membership_enable_api_settings').prop('checked');
    var api_consumer_secret   = jQuery(document).find('#wps_membership_api_consumer_secret_keys').val();

    if ( true == is_api_setting_enable && api_consumer_secret ) {

        jQuery('button#mfw_button_generate_keys_settings').hide();
        jQuery(document).find('#wps_membership_api_consumer_secret_keys').parents('.wps-form-group').show();
    } else if ( true == is_api_setting_enable && ! api_consumer_secret ) {

        jQuery('button#mfw_button_generate_keys_settings').show();
        jQuery(document).find('#wps_membership_api_consumer_secret_keys').parents('.wps-form-group').hide();
    } else if ( false == is_api_setting_enable && api_consumer_secret ) {

        jQuery('button#mfw_button_generate_keys_settings').hide();
        jQuery(document).find('#wps_membership_api_consumer_secret_keys').parents('.wps-form-group').show();
    } else {

        jQuery('button#mfw_button_generate_keys_settings').hide();
        jQuery(document).find('#wps_membership_api_consumer_secret_keys').parents('.wps-form-group').hide();
    }

    // Hide and show button when enable/disabled api settings.
    jQuery(document).on('change', '#wps_membership_enable_api_settings', function(){

        var is_api_setting_enable = jQuery(document).find('#wps_membership_enable_api_settings').prop('checked');
        var api_consumer_secret   = jQuery(document).find('#wps_membership_api_consumer_secret_keys').val().trim();

        
        if ( true == is_api_setting_enable && ! api_consumer_secret ) {
            
            jQuery('button#mfw_button_generate_keys_settings').show();
        } else {

            jQuery('button#mfw_button_generate_keys_settings').hide();
        }
    });

    // Make consumer keys field disabled false when click on submit button.
    jQuery(document).on('click', 'button#mfw_button_api_settings', function(){

        jQuery(document).find('#wps_membership_api_consumer_secret_keys').attr('disabled', false);
    });

    // Make consumer keys field disabled true when key is set.
    if ( admin_registration_ajax_obj.is_consumer_secret_set ) {

        jQuery(document).find('#wps_membership_api_consumer_secret_keys').attr('disabled', true);
    }

    jQuery(document).on('keyup', '#memPlanAmount', function(){

        this.value = this.value.replace(/[^0-9]/g, '');
    });
});
