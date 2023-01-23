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
        // alert();
        var plan_title = $('#wps_mfw_reg_plan_name').val();
        var plan_price = $('#wps_mfw_reg_plan_price').val();
        var plan_access_type = $('#wps_mfw_reg_access_type').val();
        var plan_duration = $('#wps_mfw_reg_expiry_num').val();
        var plan_duration_type = $('#wps_mfw_reg_expiry_time').val();
        if( plan_title == '' ){
            alert('Please Enter plan name !');
        }
        if( plan_price <= 0 ){
            alert('Please Enter valid price !');
        }
        if( plan_access_type == '' ){
            alert('Please choose access type !');
        }
        if( plan_access_type == 'limited' ){
            if( plan_duration <= 0 ) {
                alert('Please Enter valid duration !')
            }
           
        }


        $.ajax({
			url: ajaxurl,
			type: "POST",
			data: {
				action: "wps_membership_create_plan_reg",
				plan_price: plan_price,
				plan_access_type: plan_access_type,
				plan_title: plan_title,
                plan_duration : plan_duration,
                plan_duration_type : plan_duration_type
			},

			success: function (response) {
                // jQuery('#wps-form-wps_create_membership_plan_button').parent().append('<h3 style="color:red">Plan is Created Successfully !<h3>');
                setTimeout(function(){
                    window.location.reload(1);
                 }, 5000);
				
			}
        });
        

    });



    $('div .wps_membership_plan_fields').hide();

$(document).on('change','#wps_membership_plan_for_restriction', function(){
    $('div .wps_membership_plan_fields').hide();
    $('div .wps_reg_plan_' + $(this).val()).show();
});



});
