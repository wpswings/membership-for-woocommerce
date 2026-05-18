jQuery( function( $ ) {
	var modalSelector = '[data-mfw-expert-modal]';
	var openTriggerSelector = '[data-mfw-open-expert-modal]';
	var closeTriggerSelector = '[data-mfw-expert-modal-close]';
	var formSelector = '[data-mfw-expert-modal-form]';
	var statusSelector = '[data-mfw-expert-modal-status]';
	var successSelector = '[data-mfw-expert-modal-success]';
	var successMessageSelector = '[data-mfw-expert-modal-success-message]';
	var bodyLockClass = 'mfw-expert-modal-open';
	var successCloseTimer = null;

	function mfwGetExpertModal() {
		return $( modalSelector ).first();
	}

	function mfwSetExpertStatus( $modal, message, statusType ) {
		var $status = $modal.find( statusSelector ).first();

		if ( ! $status.length ) {
			return;
		}

		if ( ! message ) {
			$status.attr( 'hidden', true ).removeClass( 'is-success is-error' ).text( '' );
			return;
		}

		$status.removeAttr( 'hidden' ).removeClass( 'is-success is-error' ).addClass( 'is-' + statusType ).text( message );
	}

	function mfwResetExpertModalState( $modal ) {
		var $form = $modal.find( formSelector ).first();
		var $success = $modal.find( successSelector ).first();
		var $successMessage = $modal.find( successMessageSelector ).first();
		var $submitButton = $form.find( 'button[type="submit"]' ).first();

		if ( $form.length ) {
			if ( $form.get( 0 ) && 'function' === typeof $form.get( 0 ).reset ) {
				$form.get( 0 ).reset();
			}
			$form.removeAttr( 'hidden' ).css( 'display', '' );
		}

		if ( $submitButton.length ) {
			$submitButton.prop( 'disabled', false ).text( $submitButton.attr( 'data-submit-label' ) || 'Submit Request' );
		}

		if ( $success.length ) {
			$success.attr( 'hidden', true ).removeClass( 'is-visible' );
		}

		if ( $successMessage.length ) {
			$successMessage.text( 'Thank you for submitting your request.' );
		}

		mfwSetExpertStatus( $modal, '', '' );
	}

	function mfwShowExpertSuccessState( $modal, message ) {
		var $form = $modal.find( formSelector ).first();
		var $success = $modal.find( successSelector ).first();
		var $successMessage = $modal.find( successMessageSelector ).first();

		if ( $form.length ) {
			$form.attr( 'hidden', true ).css( 'display', 'none' );
		}

		mfwSetExpertStatus( $modal, '', '' );

		if ( $successMessage.length ) {
			$successMessage.text( message );
		}

		if ( $success.length ) {
			$success.removeAttr( 'hidden' );
			window.setTimeout( function() {
				$success.addClass( 'is-visible' );
			}, 80 );
		}
	}

	function mfwToggleExpertModal( shouldOpen ) {
		var $modal = mfwGetExpertModal();

		if ( ! $modal.length ) {
			return;
		}

		if ( successCloseTimer ) {
			window.clearTimeout( successCloseTimer );
			successCloseTimer = null;
		}

		if ( shouldOpen ) {
			$modal.removeAttr( 'hidden' );
			$( 'body' ).addClass( bodyLockClass );
			mfwResetExpertModalState( $modal );
			return;
		}

		$modal.attr( 'hidden', true );
		$( 'body' ).removeClass( bodyLockClass );
		mfwResetExpertModalState( $modal );
	}

	function mfwNormalizeExpertPayload( formElement ) {
		var payload = {};
		var formData = new window.FormData( formElement );

		formData.forEach( function( value, key ) {
			var normalizedKey = key.replace( /\[\]$/, '' );

			if ( Object.prototype.hasOwnProperty.call( payload, normalizedKey ) ) {
				if ( ! Array.isArray( payload[ normalizedKey ] ) ) {
					payload[ normalizedKey ] = [ payload[ normalizedKey ] ];
				}

				payload[ normalizedKey ].push( value );
				return;
			}

			payload[ normalizedKey ] = value;
		} );

		return payload;
	}

	function mfwResolveExpertAjaxConfig( $form ) {
		var localizedObject = window.mfw_admin_param || {};

		return {
			ajaxurl: $form.attr( 'data-mfw-expert-ajaxurl' ) || localizedObject.ajaxurl || window.ajaxurl || '',
			action: $form.attr( 'data-mfw-expert-action' ) || localizedObject.mfw_expert_action || 'wps_mfw_submit_talk_to_expert',
			nonce: $form.attr( 'data-mfw-expert-nonce' ) || localizedObject.mfw_expert_nonce || ''
		};
	}

	$( document ).off( 'click.mfwExpertModalOpen', openTriggerSelector ).on( 'click.mfwExpertModalOpen', openTriggerSelector, function( event ) {
		event.preventDefault();
		mfwToggleExpertModal( true );
	} );

	$( document ).off( 'click.mfwExpertModalClose', closeTriggerSelector ).on( 'click.mfwExpertModalClose', closeTriggerSelector, function( event ) {
		event.preventDefault();
		mfwToggleExpertModal( false );
	} );

	$( document ).off( 'keydown.mfwExpertModal' ).on( 'keydown.mfwExpertModal', function( event ) {
		if ( 'Escape' === event.key ) {
			mfwToggleExpertModal( false );
		}
	} );

	$( document ).off( 'submit.mfwExpertModal', formSelector ).on( 'submit.mfwExpertModal', formSelector, function( event ) {
		var $form = $( this );
		var $modal = $form.closest( modalSelector );
		var $submitButton = $form.find( 'button[type="submit"]' ).first();
		var submitLabel = $submitButton.attr( 'data-submit-label' ) || $submitButton.text();
		var loadingLabel = $submitButton.attr( 'data-loading-label' ) || 'Sending...';
		var requestConfig = mfwResolveExpertAjaxConfig( $form );

		event.preventDefault();
		mfwSetExpertStatus( $modal, '', '' );
		$submitButton.prop( 'disabled', true ).text( loadingLabel );

		if ( ! requestConfig.ajaxurl || ! requestConfig.nonce ) {
			mfwSetExpertStatus( $modal, 'We could not submit your request right now. Please try again.', 'error' );
			$submitButton.prop( 'disabled', false ).text( submitLabel );
			return;
		}

		$.ajax( {
			url: requestConfig.ajaxurl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: requestConfig.action,
				nonce: requestConfig.nonce,
				form_data: JSON.stringify( mfwNormalizeExpertPayload( $form.get( 0 ) ) )
			}
		} ).done( function( response ) {
			var isSuccess = !! ( response && response.success );
			var message = response && response.data && response.data.message ? response.data.message : '';

			if ( ! message ) {
				message = isSuccess ? 'Thank you for submitting your request.' : 'We could not submit your request right now. Please try again.';
			}

			if ( isSuccess ) {
				mfwShowExpertSuccessState( $modal, message );

				if ( successCloseTimer ) {
					window.clearTimeout( successCloseTimer );
				}

				successCloseTimer = window.setTimeout( function() {
					mfwToggleExpertModal( false );
				}, 3000 );
				return;
			}

			mfwSetExpertStatus( $modal, message, 'error' );
		} ).fail( function( xhr ) {
			var message = 'We could not submit your request right now. Please try again.';

			if ( xhr && xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message ) {
				message = xhr.responseJSON.data.message;
			}

			mfwSetExpertStatus( $modal, message, 'error' );
		} ).always( function() {
			$submitButton.prop( 'disabled', false ).text( submitLabel );
		} );
	} );
} );
