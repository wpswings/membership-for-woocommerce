jQuery( document ).ready( function( $ ) {

    // Display edit fields on edit click
    $( ".members_data_column" ).on( "click", ".edit_member_address", function( e ) {
        e.preventDefault();

        $( ".edit_member_address" ).hide();
        $( ".member_address" ).hide();
        $( ".member_edit_address" ).show();
        $( ".cancel_member_edit" ).show();
    });

    // Hide edit fields on edit cancel.
    $( ".members_data_column" ).on( "click", ".cancel_member_edit", function( e ) {
        e.preventDefault();

        $( ".edit_member_address" ).show();
        $( ".member_address" ).show();
        $( ".member_edit_address" ).hide();
        $( ".cancel_member_edit" ).hide();
    });

    // Ajax cal for states as per country on page ready.
    get_states_for_country();

    // Ajax call function for states as per country.
    function get_states_for_country() {

        $.ajax({
            url  : members_admin_obj.ajaxurl,
            type : "POST",
            data : {
                action  : "membership_get_states",
                country : $( "#billing_country" ).val(),
                nonce   : members_admin_obj.nonce,
            },

            success: function( response ) {

                if ( response.length > 1 ) {
                    $( ".billing_state_field" ).show();
                    $( "#billing_state" ).html( response );

                } else {

                    $( ".billing_state_field" ).hide();
                }
            }
        });
    }

    // Ajax call for states as per country change.
    $( "#billing_country" ).on( "change", function() {
        get_states_for_country();
    });

    $( "#billing_country" ).select2();
    $( "#billing_state" ).select2();
    $( "#mwb_member_user" ).select2();

});