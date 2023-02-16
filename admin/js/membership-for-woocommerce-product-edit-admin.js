jQuery(document).ready(function($) { 
    $('.attach-membership_options').addClass('show_if_simple').show();

    $(document).on('click', '.product_data_tabs', function(){
        if( $(this).find('.attach-membership_options').hasClass('active') ) {
            $('.wps_membership_dropdown').removeClass('hidden');
        } else {
            $('.wps_membership_dropdown').addClass('hidden');
        }
    } );

    
    jQuery(document).on( 'click', '#post-' + wps_product_edit_param.prod_id + ' td div.row-actions .trash', function(e){
        if( confirm( 'Please do not delete this product , Membership functionality will not work!' ) ) {
            
        } else {
            e.preventDefault();
        }
    } );

 } );