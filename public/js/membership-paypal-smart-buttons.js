jQuery( document ).ready( function( $ ) {

    // Getting the localized payapl settings object.
    var paypal_settings = paypal_sb_obj.settings;
    var plan_data       = paypal_sb_obj.plan_data;
    var firstname;
    var lastname;
    var ad_line_1;
    var ad_line_2;
    var city;
    var state;
    var zip;
    var country;

    // Getting billing form details here.
    $('.mwb_mfw_btn-next-b').on('click', function(e){
        firstname = $( '#membership_billing_first_name' ).val();
        lastname  = $( '#membership_billing_last_name' ).val();
        ad_line_1 = $( '#membership_billing_address_1' ).val();
        ad_line_2 = $( '#membership_billing_address_2' ).val();
        city      = $( '#membership_billing_city' ).val();
        state     = $( '#membership_billing_state' ).val();
        zip       = $( '#membership_billing_postcode' ).val();
        country   = $( '#membership_billing_country' ).val();
    });

    // Initiate payment on paypal.
    paypal.Buttons({

		style: {
            layout: paypal_settings.button_layout,
            color:  paypal_settings.button_color,
            shape:  paypal_settings.button_shape,
            label:  paypal_settings.button_label,
		},

        createOrder: function(data, actions) {

            return actions.order.create({

                purchase_units: [{
                    amount: {
                        currency_code: 'USD',
                        value: plan_data.price,
                        breakdown: {
                            item_total: {
                                currency_code: 'USD',
                                value: plan_data.price
                            }
                        }
                    },
                    items: [
                        {
                            name: plan_data.name,
                            description: plan_data.desc,
                            unit_amount: {
                                currency_code: 'USD',
                                value: plan_data.price
                            },
                            quantity: "1",
                            category : "DIGITAL_GOODS"
                        },
                    ],
                    shipping: {
                        name: {
                                full_name: firstname + ' ' + lastname,
                            },
                        address: {
                            address_line_1: ad_line_1,
                            address_line_2: ad_line_2,
                            admin_area_2: city,
                            admin_area_1: state,
                            postal_code: zip,
                            country_code: country
                        }
                    }
                }]
            });
        },

        onApprove: function(data, actions) {

            return actions.order.capture().then(function(details) {
                console.log( details );
                alert('Transaction completed by ' + details.payer.name.given_name);
                // Call your server to save the transaction
                // return fetch('/api/paypal-transaction-complete', {
                //     method: 'post',
                //     headers: {
                //         'content-type': 'application/json'
                //     },
                //     body: JSON.stringify({
                //         orderID: data.orderID
                //     })
                // });

                saveTransactionData( details );

            
            });
        }
    }).render( '#paypal-button-container' );

    const saveTransactionData = async( tr_details ) => {

        const response = await jQuery.ajax(
            {
                type : 'POST',
                url  : paypal_sb_obj.ajax_url,
                data : {
                    action : 'membership_save_transaction',
                    nonce : paypal_sb_obj.nonce,
                    details : tr_details,
                   //email : email
                },
                dataType : 'json',
            }
        ).fail(
            ( response ) => {
                console.log( response );
            }
        );

        if ( true == response.status && response.propertyError != true ) {
            jQuery( '#paypal-button-container' ).css( 'display', 'none' );
            jQuery( '#membership_proceed_payment' ).css( 'display', 'block' );
            jQuery( '#membership_proceed_payment' ).val( 'Proceed' );
            jQuery( '#mwb_membership_buy_now_modal_form' ).append( '<div id="mwb_tnx_user"><input type="hidden" name="mwb_tnx_user_id" id="mwb_tnx_user_id" value="' + response.user_id + '"/></div>' );
            
        } else if ( false == response.status || response.propertyError == true ) {
            console.log( response );
        }
            
    }

} ); 