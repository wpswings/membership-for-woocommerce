jQuery( document ).ready( function( $ ) {

    //console.log( paypal_sb_obj.settings );
    // Getting the localized payapl settings object.
    var paypal_settings = paypal_sb_obj.settings;

    // Getting billing form details here.
    var billing_data = $( "#mwb_membership_buy_now_modal_form" ).serialize();
    //console.log( billing_data );

    $( "#mwb_membership_buy_now_modal_form" ).on( "change",  function() {
    
       validate = $( "form[id='mwb_membership_buy_now_modal_form']" ).validate({

            rules: {
                membership_billing_first_name : "required",
                membership_billing_last_name : "required",
                membership_billing_country : "required",
                membership_billing_address_1 : "required",
                membership_billing_city : "required",
                membership_billing_state : "required",
                membership_billing_postcode : "required",
                membership_billing_phone : "required",
                email: {
                    required: true,
                    email: true
                },
            },
            message : {
                membership_billing_first_name: "Please enter your firstname",
                membership_billing_last_name : "Please enter your lastname",
                membership_billing_country : "Please select a country",
                membership_billing_address_1 : "Please enter your street address",
                membership_billing_city   : "Please enter your city",
                membership_billing_state : "Please select your state",
                membership_billing_postcode : "Please enter your postcode",
                membership_billing_phone : "Please enter your phone number.",
                email: "Please enter a valid email address",
            },
        });

        //console.log( validate.errorList );
    });

    
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
                        value: "100.00",
                        breakdown: {
                            item_total: {
                                currency_code: 'USD',
                                value: "100.00"
                            }
                        }
                    },
                    items: [
                        {
                            name: "Item 1",
                            description: "The best item ever",
                            unit_amount: {
                                currency_code: 'USD',
                                value: "100.00"
                            },
                            quantity: "1",
                            category : "DIGITAL_GOODS"
                        },
                    ],
                    shipping: {
                        name: {
                                full_name: "John Doe",
                            },
                        address: {
                            address_line_1: "123 Townsend St",
                            address_line_2: "Floor 6",
                            admin_area_2: "San Francisco",
                            admin_area_1: "CA",
                            postal_code: "94107",
                            country_code: "US"
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

                // Ajax call to save transaction data.
                $.ajax({
                    url    : paypal_sb_obj.ajax_url,
                    type   : 'post',
                    data   : {
                        action : 'payal_transaction_data_handling',  
                        transaction_details : details,
                        nonce : paypal_sb_obj.nonce,
                    },

                    success : function( response ) {
                        console.log( response );
                    }


                });
            });
        }
    }).render( '#paypal-button-container' );

} ); 