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
        var plan_name = $('#wps_mfw_reg_plan_name').val();
        var plan_price = $('#wps_mfw_reg_plan_price').val();
        var plan_access_type = $('#wps_mfw_reg_access_type').val();
        if( plan_name == '' ){
            alert('Please Enter plan name !');
        }
        if( plan_price <= 0 ){
            alert('Please Enter valid price !');
        }
        if( plan_access_type == '' ){
            alert('Please choose access type !');
        }


        $.ajax({
			url: ajaxurl,
			type: "POST",
			data: {
				action: "wps_membership_create_plan_reg",
				plan_price: plan_price,
				plan_id: plan_id,
				plan_title: plan_title,
			},

			success: function (response) {

				
			}
        });
        

    });





});
