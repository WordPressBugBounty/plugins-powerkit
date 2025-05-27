"use strict";

( function( $ ) {

	/*
	 * AJAX subscription
	 */
	$( document ).on( 'submit', '.pk-subscribe-form-wrap form', function( e ) {

		var form = $( this );
		var formWrap = $( form ).closest('.pk-subscribe-form-wrap');
		var service = formWrap.data('service') || 'mailchimp';

		// If form is custom, let the native form submission happen
		if (service === 'custom') {
			// Check privacy first
			var privacy = $( formWrap ).find( 'input[name="pk-privacy"]' );

			if ( $( privacy ).length > 0 && ! $( privacy ).prop( 'checked' ) ) {
				e.preventDefault();
				$( privacy ).parent().after( '<p class="pk-alert pk-alert-warning">' + window.opt_in.warning_privacy + '</p>' );
				return false;
			}

			// Continue with the native form submission
			return true;
		}

		// Remove messages.
		$( formWrap ).find( '.pk-alert' ).remove();

		// Policies.
		var privacy = $( formWrap ).find( 'input[name="pk-privacy"]' );

		if ( $( privacy ).length > 0 && ! $( privacy ).prop( 'checked' ) ) {
			$( privacy ).parent().after( '<p class="pk-alert pk-alert-warning">' + window.opt_in.warning_privacy + '</p>' );

			return false;
		}

		if ( !$( form ).hasClass( 'pk-loading' ) ) {
			$.ajax( {
				type: 'POST',
				url: window.opt_in.ajax_url + '?action=powerkit_subscription',
				data: $( form ).serialize(),
				beforeSend: function() {
					$( form ).addClass( 'pk-loading' );
				},
				success: function( data ) {
					$( form ).removeClass( 'pk-loading' );

					if ( data.success ) {
						$( form ).after( '<p class="pk-alert pk-alert-success">' + data.data + '</p>' );
					} else {
						$( form ).after( '<p class="pk-alert pk-alert-warning">' + data.data + '</p>' );
					}
				},
				error: function() {
					$( form ).after( '<p class="pk-alert pk-alert-warning">Error server!</p>' );

					$( form ).removeClass( 'pk-loading' );
				}
			} );

		}
		return false;
	} );

} )( jQuery );
