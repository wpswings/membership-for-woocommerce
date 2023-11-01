jQuery(document).ready(function($) {
    
    
    $('#message').hide();
    // Display edit fields on edit click
    $(".members_data_column").on("click", ".edit_member_address", function(e) {
        e.preventDefault();

        $(".edit_member_address").hide('500');
        $(".member_address").hide('500');
        $(".member_edit_address").show('500');
        $(".cancel_member_edit").show('500');
    });

    // Hide edit fields on edit cancel.
    $(".members_data_column").on("click", ".cancel_member_edit", function(e) {
        e.preventDefault();

        $(".edit_member_address").show('500');
        $(".member_address").show('500');
        $(".member_edit_address").hide('500');
        $(".cancel_member_edit").hide('500');
    });

    // Ajax cal for states as per country on page ready.
    get_states_for_country();

    // Ajax call function for states as per country.
    function get_states_for_country() {

        $.ajax({
            url: members_admin_obj.ajaxurl,
            type: "POST",
            data: {
                action: "wps_membership_get_states",
                country: $("#billing_country").val(),
                nonce: members_admin_obj.nonce,
            },

            success: function(response) {

                if (response.length > 1) {
                    $(".billing_state_field").show('500');
                    $("#billing_state").html(response);

                } else {

                    $(".billing_state_field").hide('500');
                    $(".billing_state_field").empty();
                }
            }
        });
    }

    // Ajax call for states as per country change.
    $("#billing_country").on("change", function() {
        get_states_for_country();
    });

    $("#billing_country").select2();
    $("#billing_state").select2();
    $("#wps_member_user").select2();
    $("#members_plan_assign").select2();
    $("#payment_gateway_select").select2();

    // Applying script to admin part in all Members list page
    var wps_status = $('.members_status, .member_plan_status').each(function() {

        if ($(this).text() == 'complete') {
            $(this).css({ 'background-color': '#c6e1c6', 'color': '#5b841b' }); // green
        } else if ($(this).text() == 'hold') {
            $(this).css({ 'background-color': '#f8dda7', 'color': '#94660c' }); // yellow
        } else if ($(this).text() == 'pending') {
            $(this).css({ 'background-color': '#e5e5e5', 'color': '#777' }); // grey
        } else if ($(this).text() == 'expired') {
            $(this).css({ 'background-color': '#d46363', 'color': '#777' }); //red
        } else if ($(this).text() == 'cancelled') {
            $(this).css({ 'background-color': '#ffc863', 'color': '#777' }); //red
        }

    });
        if ($('a').has('.disabled')) {
            $(this).closest('.wps_membership_plan_gateways tr').css('background','red');
        }


        $(document).on('change', '#filter_member_status', function(e) {
            var filtered_status = jQuery('#filter_member_status').val();
             var member_ststus_td = jQuery('.members_status');
             var wps_statuses = ['complete', 'expired', 'cancelled','pending', 'hold'];
             var changed_value = $(this).val();
            for( let i=0;i<wps_statuses.length;i++ ) {
                if( changed_value == 'All' ){
                     jQuery('.wps_hide_hold' ).parent().parent().show();
                     jQuery('.wps_hide_' + wps_statuses[i] ).parent().parent().show();
                   
                } else if( changed_value == wps_statuses[i]  ){
                    jQuery('.wps_hide_' + wps_statuses[i] ).parent().parent().show();
                    
                  
                } else{
                    jQuery('.wps_hide_' + wps_statuses[i] ).parent().parent().hide();
                   
                }
                if( changed_value == 'pending' ) {

                    jQuery('.wps_hide_hold' ).parent().parent().show();
                }
            }
            
        });

        $(document).on('change', '#filter_membership_name', function(e) {
            var filtered_status = jQuery('#filter_membership_name').val();
             var member_ststus_td = jQuery('.membership_plan_associated');
            
             for (let index = 0; index < member_ststus_td.length; index++) {
                 if (filtered_status == jQuery(jQuery('.membership_plan_associated')[index]).html() || filtered_status == 'All' ) {
                    jQuery(jQuery('.membership_plan_associated')[index]).parent().show();
                 } else {
                    jQuery(jQuery('.membership_plan_associated')[index]).parent().hide();
                 }
             }
            
        });


        	// update wallet and status on changing status of wallet request
		$(document).on( 'change', 'select#wps-wpg-gen-table_status', function() {
			var user_id = $('#wps-wpg-gen-table_status').attr('user_id');
            var post_id = $('#wps-wpg-gen-table_status').attr('post_id_value');
            var plan_id = $('#wps-wpg-gen-table_status').attr('plan_id');
			var status = $(this).find(":selected").val();
			var loader = $(this).siblings('#overlay');
			loader.show();
			$.ajax({
				type: 'POST',
				url: members_admin_obj.ajaxurl,
				data: {
					action: 'wps_membership_save_member_status',
					nonce: members_admin_obj.nonce,
					post_id: post_id,
					user_id: user_id,
					member_status: status,
                    members_plan_assign:plan_id,
					
				},
				datatType: 'JSON',
				success: function( response ) {
					$( '.wps-wpg-withdrawal-section-table' ).before('<div class="notice notice-' + response.msgType + ' is-dismissible wps-errorr-8"><p>' + response.msg + '</p></div>');		
					loader.hide();
					setTimeout(function () {
						location.reload();
					}, 1000);
					

				},

			})
			.fail(function ( response ) {
				$( '.wps-wpg-withdrawal-section-table' ).before('<div class="notice notice-error is-dismissible wps-errorr-8"><p>' + wsfw_admin_param.wsfw_ajax_error + '</p></div>');		
				loader.hide();
			});
        });
    
   
});




