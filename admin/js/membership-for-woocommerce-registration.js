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


    jQuery(document).on('click', '#wps_create_membership_plan_button', function(){
        var wps_plan_check = true;
        var plan_title = $('#wps_mfw_reg_plan_name').val();
        var plan_price = $('#wps_mfw_reg_plan_price').val();
        var plan_access_type = $('#wps_mfw_reg_access_type').val();
        var plan_duration = $('#wps_mfw_reg_expiry_num').val();
        var plan_duration_type = $('#wps_mfw_reg_expiry_time').val();

        if( plan_title == '' ){
            wps_plan_check = false;
            alert('Please Enter plan name !');
        }
        if( plan_price <= 0 ){
            wps_plan_check = false;
            alert('Please Enter valid price !');
        }
        if( plan_access_type == '' ){
            wps_plan_check = false;
            alert('Please choose access type !');
        }
        if( plan_access_type == 'limited' ){
            if( plan_duration <= 0 ) {
                wps_plan_check = false;
                alert('Please Enter valid duration !')
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
                    
                    setTimeout(function(){
                        window.location.reload(1);
                     }, 5000);
                    
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

});
