( function( $ ) {
	'use strict';

	$( function() {

		/* Set all variables to be used in scope */
		var powerkitFontsMediaFrame;

		/* Add Font Link */
		$( document ).on( 'click', '.upload-font-container .upload-font-link', function( event ) {

			event.preventDefault();

			var parentContainer = $( this ).parents( '.upload-font-container' );

			// Options.
			var options = {
				title: powerkitFonts.title,
				button: {
					text: powerkitFonts.button,
				},
				library: {
					type: parentContainer.data( 'type' )
				},
				multiple: false
			};

			powerkitFontsMediaFrame = wp.media( options );

			// When an image is selected in the media frame...
			powerkitFontsMediaFrame.on( 'select', function() {

				// Get media attachment details from the frame state.
				var attachment = powerkitFontsMediaFrame.state().get( 'selection' ).first().toJSON();

				parentContainer.find( '.filename' ).val( attachment.filename ).change();
				parentContainer.find( '.uploaded-font-id' ).val( attachment.id ).change();
				parentContainer.find( '.upload-font-link' ).addClass( 'hidden' );
				parentContainer.find( '.delete-font-link' ).removeClass( 'hidden' );

				powerkitFontsMediaFrame.close();
			} );

			powerkitFontsMediaFrame.open();
		} );


		/* Delete Font Link */
		$( document ).on( 'click', '.upload-font-container .delete-font-link', function( event ) {
			event.preventDefault();

			var parentContainer = $( this ).parents( '.upload-font-container' );
			parentContainer.find( '.filename' ).val( '' ).change();
			parentContainer.find( '.uploaded-font-id' ).val( '' ).change();
			parentContainer.find( '.upload-font-link' ).removeClass( 'hidden' );
			parentContainer.find( '.delete-font-link' ).addClass( 'hidden' );
		} );

		/* Delete alert */
		$( document ).on( 'click', '.powerkit-fonts-delete', function( event ) {
			return confirm( powerkitFonts.delete ) ? true : false;
		} );

		/* View code */
		$( document ).find( '.powerkit-fonts-view-code' ).each( function( index, el ) {

			var obj = $( el ).closest( 'tr' ).next();

			// Reset td padding.
			$( obj ).find( 'td' ).css( 'padding', 0 );

			// Show tr.
			$( obj ).removeClass( 'hidden' );

			// Set height textarea.
			$( obj ).find( 'textarea' ).css( 'min-height', '110px' ).height( 110 );
		} );

		/* View code slide toggle */
		$( document ).on( 'click', '.powerkit-fonts-view-code', function( event ) {

			$( this ).closest( 'tr' ).prevAll().find( '.template-box' ).slideUp();
			$( this ).closest( 'tr' ).next().nextAll().find( '.template-box' ).slideUp();

			$( this ).closest( 'tr' ).next().find( '.template-box' ).slideToggle();

			return false;
		} );

		/* View code outside */
		$( document ).on( 'click', function( event ) {

			if ( !$( event.target ).closest( '.wp-list-table' ).length ) {

				$( '.template-box' ).slideUp();
			}
		} );

	} );

} )( jQuery );