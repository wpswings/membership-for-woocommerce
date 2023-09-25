jQuery( document ).ready( function( $ ){

    // Target products search.
    jQuery( ".wc-membership-product-search" ).select2({

        ajax:{

            url: add_new_obj.ajax_url,
            dataType: "json",
            delay:    200,
            data: function( params ) {
                return {
                    q: params.term,
                    action: "wps_membership_search_products_for_membership",
                };
            },
            processResults: function( data ) {

                var options = [];
                if ( data ) {

                    $.each( data, function( index, text ) {
                        text[1]+='( #'+text[0]+')';
                        options.push( { id: text[0], text: text[1] } );
                    });
                }
                return {
                    results:options
                };
            },
            cache: true
        },
        minimumInputLength: 3 // The minimum number of characters to input to perform a search.
    });


    // Target category search.
    jQuery( ".wc-membership-product-category-search" ).select2({

        ajax:{

            url: add_new_obj.ajax_url,
            dataType: "json",
            delay: 200,
            data: function( params ) {
                return {
                    q: params.term,
                    action: "wps_membership_search_product_categories_for_membership",
                };
            },
            processResults: function( data ) {
                var options = [];
                if ( data ) {

                    $.each( data, function( index, text ) {

                        text[1]+='( #'+text[0]+')';
                        options.push( { id: text[0], text: text[1] } )

                    });
                }
                return {
                    results: options
                };
            },
            cache: true
        },
        minimumInputLength: 3 // The minimum number of characters to input to perform a search.
    });


    // set limit when fixed and discount type is selected in new plan price.
    jQuery(document).on('change', '#wps_membership_plan_for_discount_offer', function(){
        
        var plan_id = jQuery(this).val();
        
        jQuery('#wps_membership_discount_amount_'+plan_id).attr( 'max', '100' );
        jQuery(document).on('change', '#wps_membership_discount_type_'+plan_id, function(){

            var discount_type = jQuery(this).val();
            
            if ( 'fixed' == discount_type ) {
            
                jQuery('#wps_membership_discount_amount_'+plan_id).removeAttr( 'max' );
            } else {
    
                jQuery('#wps_membership_discount_amount_'+plan_id).attr( 'max', '100' );
            }
        });
    });
});
