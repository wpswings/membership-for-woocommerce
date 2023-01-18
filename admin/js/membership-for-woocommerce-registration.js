jQuery(document).ready(function($){
    
    $(document).on('change', '#wps_mfw_reg_access_type', function(){
        if( 'limited' == $(this).val() ){

            $('.wps_mfw_plan_expiry_class').removeClass('hidden');
            
        } else {
            $('.wps_mfw_plan_expiry_class').addClass('hidden');
        }
    });

    //Form js
    $("#wps_member_user_reg").select2();
});
