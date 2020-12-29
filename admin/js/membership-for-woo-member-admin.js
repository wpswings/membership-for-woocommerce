jQuery( document ).ready( function( $ ) {

    $( ".members_data_column" ).on( "click", ".edit_member_address", function( e ) {
        e.preventDefault();

        //alert('hi');
        $( ".edit_member_address" ).hide();
        $( ".member_address" ).hide();
        $( ".member_edit_address" ).show();
        $( ".cancel_member_edit" ).show();
    });

    $( ".members_data_column" ).on( "click", ".cancel_member_edit", function( e ) {
        e.preventDefault();

        $( ".edit_member_address" ).show();
        $( ".member_address" ).show();
        $( ".member_edit_address" ).hide();
        $( ".cancel_member_edit" ).hide();
    });
});