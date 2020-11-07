/* global wc_stripe_params */

jQuery( function( $ ) {
	'use strict';

	try {
		var stripe = Stripe( wc_stripe_params.key, {
			betas: [ 'payment_intent_beta_3' ],
		} );
	} catch( error ) {
		console.log( error );
		return;
	}

	/**
	 * Object to handle Stripe elements payment form.
	 */
	var wc_stripe_form = {
		/**
		 * Get WC AJAX endpoint URL.
		 *
		 * @param  {String} endpoint Endpoint.
		 * @return {String}
		 */
		getAjaxURL: function( endpoint ) {
			return wc_stripe_params.ajaxurl
				.toString()
				.replace( '%%endpoint%%', 'wc_stripe_' + endpoint );
		},
		
		/**
		 * Handles changes in the hash in order to show a modal for PaymentIntent confirmations.
		 *
		 * Listens for `hashchange` events and checks for a hash in the following format:
		 * #confirm-pi-<intentClientSecret>:<successRedirectURL>
		 *
		 * If such a hash appears, the partials will be used to call `stripe.handleCardPayment`
		 * in order to allow customers to confirm an 3DS/SCA authorization.
		 *
		 * Those redirects/hashes are generated in `WC_Gateway_Stripe::process_payment`.
		 */
		onHashChange: function() {
			var partials = window.location.hash.match( /^#?confirm-pi-([^:]+):(.+)$/ );

			if ( ! partials || 3 > partials.length ) {
				return;
			}

			var intentClientSecret = partials[1];
			var redirectURL        = decodeURIComponent( partials[2] );

			// Cleanup the URL
			window.location.hash = '';

			wc_stripe_form.openIntentModal( intentClientSecret, redirectURL );
		},

		maybeConfirmIntent: function() {
			if ( ! $( '#stripe-intent-id' ).length || ! $( '#stripe-intent-return' ).length ) {
				return;
			}

			var intentSecret = $( '#stripe-intent-id' ).val();
			var returnURL    = $( '#stripe-intent-return' ).val();

			wc_stripe_form.openIntentModal( intentSecret, returnURL, true );
		},

		/**
		 * Opens the modal for PaymentIntent authorizations.
		 *
		 * @param {string}  intentClientSecret The client secret of the intent.
		 * @param {string}  redirectURL        The URL to ping on fail or redirect to on success.
		 * @param {boolean} alwaysRedirect     If set to true, an immediate redirect will happen no matter the result.
		 *                                     If not, an error will be displayed on failure.
		 */
		openIntentModal: function( intentClientSecret, redirectURL, alwaysRedirect ) {
			stripe.handleCardPayment( intentClientSecret )
				.then( function( response ) {
					if ( response.error ) {
						throw response.error;
					}

					if ( 'requires_capture' !== response.paymentIntent.status && 'succeeded' !== response.paymentIntent.status ) {
						return;
					}

					window.location = redirectURL;
				} )
				.catch( function( error ) {
					if ( alwaysRedirect ) {
						return window.location = redirectURL;
					}

					$( document.body ).trigger( 'stripeError', { error: error } );
					wc_stripe_form.form && wc_stripe_form.form.removeClass( 'processing' );

					// Report back to the server.
					$.get( redirectURL + '&is_ajax' );
				} );
		}
	};

	wc_stripe_form.init();
} );
