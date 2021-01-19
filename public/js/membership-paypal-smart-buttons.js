jQuery( document ).ready( function( $ ) {

    console.log( paypal_sb_obj.settings );
    // Getting the localized payapl settings object.
    var paypal_settings = paypal_sb_obj.settings;
    
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
                        currency_code: paypal_settings.currency_code,
                        value: "100.00",
                        breakdown: {
                            item_total: {
                                currency_code: paypal_settings.currency_code,
                                value: "100.00"
                            }
                        }
                    },
                    items: [
                        {
                            name: "Item 1",
                            description: "The best item ever",
                            unit_amount: {
                                currency_code: paypal_settings.currency_code,
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
            });
        }
    }).render( '#paypal-button-container' );

} ); 