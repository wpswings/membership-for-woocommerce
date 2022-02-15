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



});
