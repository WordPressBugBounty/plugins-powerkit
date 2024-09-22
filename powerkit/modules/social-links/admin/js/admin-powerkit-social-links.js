"use strict";

( function( $ ) {

	/*
	* Social Links
	*/
	$( function() {
		/*
		* Tabs
		*/
		$( '.pk-social-links-wrap .pk-social-links-tabs .nav-tab-wrapper .nav-tab' ).on( 'click', function( event ) {

			$( '.pk-social-links-wrap .pk-social-links-tabs .nav-tab-wrapper .nav-tab' ).removeClass( 'nav-tab-active' );

			$( this ).addClass( 'nav-tab-active' );

			// Container
			$( '.pk-social-links-wrap .pk-social-links-tabs .tab-wrap' ).removeClass( 'tab-active' );

			$( $( this ).attr( 'href' ) ).addClass( 'tab-active' );

			$( window ).resize();

			return false;
		} );

		/*
		* jQuery Sortable UI
		*/
		$( '.pk-social-links-wrap .form-table .social-sortable' ).sortable({
			placeholder: 'ui-state-highlight',
		} );

		$( '.pk-social-links-wrap .form-table .social-sortable' ).disableSelection();

		/*
		* Check display fields
		*/
		function powerkitSLinksCheckDisplayFields() {
			$( '.pk-social-links-wrap .powerkit_social_links_multiple_list' ).each( function( index, el ) {

				var item = $( el ).attr( 'data-item' );

				if ( !$( el ).prop( 'checked' ) ) {
					$( '.pk-social-links-wrap .nav-tab.' + item ).hide();

					$( '.pk-social-links-wrap .social-sortable .ui-state-default.' + item ).hide();
				} else {
					$( '.pk-social-links-wrap .nav-tab.' + item ).show();

					$( '.pk-social-links-wrap .social-sortable .ui-state-default.' + item ).show();
				}
			} );
		}

		/*
		* Cick display social
		*/
		$( '.pk-social-links-wrap .powerkit_social_links_multiple_list' ).on( 'click', function( event ) {
			powerkitSLinksCheckDisplayFields();
		} );

		/*
		* INIT
		*/
		powerkitSLinksCheckDisplayFields();
	} );

} )( jQuery );