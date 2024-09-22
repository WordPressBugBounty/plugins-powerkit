"use strict";

( function( $ ) {

	/*
	* Share Buttons
	*/
	$( function() {
		/*
		* jQuery Sortable UI
		*/
		$( '.pk-share-buttons-wrap .social-sortable' ).sortable( {
			placeholder: 'ui-state-highlight',
		} );

		$( '.pk-share-buttons-wrap .social-sortable' ).disableSelection();

		/*
		* Check display fields
		*/
		function powerkitButtonsCheckDisplayFields() {
			$( '.pk-share-buttons-wrap .powerkit_share_buttons_display' ).each( function( index, el ) {

				if ( $( el ).prop( 'checked' ) ) {
					$( el ).parents( '.form-table' ).find( 'tr' ).show();
				} else {
					$( el ).parents( '.form-table' ).find( 'tr' ).not( $( el ).parents( 'tr' ) ).hide();
				}
			} );

			$( '.pk-share-buttons-wrap .powerkit_share_buttons_multiple_list' ).each( function( index, el ) {

				var item = $( el ).attr( 'data-item' );

				if ( !$( el ).prop( 'checked' ) ) {
					$( '.pk-share-buttons-wrap .social-sortable .ui-state-default.' + item ).hide();
				} else {
					$( '.pk-share-buttons-wrap .social-sortable .ui-state-default.' + item ).show();
				}
			} );
		}

		/*
		* Cick display buttons
		*/
		$( '.pk-share-buttons-wrap .powerkit_share_buttons_display' ).on( 'click', function( event ) {
			powerkitButtonsCheckDisplayFields();
		} );

		/*
		* Cick display social
		*/
		$( '.pk-share-buttons-wrap .powerkit_share_buttons_multiple_list' ).on( 'click', function( event ) {
			powerkitButtonsCheckDisplayFields();
		} );

		/*
		* INIT
		*/
		powerkitButtonsCheckDisplayFields();
	} );

} )( jQuery );