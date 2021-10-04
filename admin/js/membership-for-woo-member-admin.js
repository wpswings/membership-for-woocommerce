jQuery(document).ready(function($) {

  

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
                action: "mwb_membership_get_states",
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
    $("#mwb_member_user").select2();
    $("#members_plan_assign").select2();
    $("#payment_gateway_select").select2();

    // Applying script to admin part in all Members list page
    var mwb_status = $('.members_status, .member_plan_status').each(function() {

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
            $(this).closest('.mwb_membership_plan_gateways tr').css('background','red');
        }
});




jQuery('button').on('click', function() {
    jQuery('.mwb-c-modal__cover').addClass('show-c_modal_cover');
    jQuery('.mwb-c-modal__message').addClass('show-c_modal_message');
    jQuery('.mwb-c-modal__cover, .mwb-c-modal__close,.mwb-c-modal__confirm-button').on('click', function() {
        jQuery('.mwb-c-modal__cover').removeClass('show-c_modal_cover');
        jQuery('.mwb-c-modal__message').removeClass('show-c_modal_message');
    });
})