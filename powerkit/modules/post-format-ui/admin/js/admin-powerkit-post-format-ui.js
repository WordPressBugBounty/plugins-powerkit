"use strict";

( function( $ ) {

	/*
	* Post Format UI
	*/
	$( function() {

		/*
		* Post Format Switcher
		*/
		$( document ).on( 'click change', '.post-format, .editor-post-format select', function( event ) {
			$( '.postbox[id*=powerkit-post-format]' ).hide();
			$( '.postbox[id=powerkit-post-format-' + $( this ).val() + ']' ).show();
		});

		/*
		* Media ( Audio / Video ) ------------------------------------------------------------------------
		*/

		/*
		* Media Placeholder
		*/
		$( document ).on( 'keyup', '.pk-post-format-media .search-input', function( event ){

			var canvas = $( this ).closest( '.pk-post-format-media' ).find( '.canvas' );

			$( canvas ).addClass( 'load' ).html( '<span class="spinner is-active"></span>' );

			jQuery.post( powerkit_post_format_ui.url, {
				url: $( this ).val(),
				nonce: powerkit_post_format_ui.nonce,
				action: 'pk_media_oembed'
			}, function( response ) {
				$( canvas ).removeClass( 'load' ).html( response );
			});

			return false;
		});

		/*
		* Gallery ----------------------------------------------------------------------------------
		*/

		/*
		* Gallery Ids Helper
		*/
		function powerkitUIGalleryIds( action, id ) {
			var obj = $( '.pk-post-format-gallery-settings' );
			var ids = $( obj ).val().split( ',' );

			switch ( action ) {
				case 'add':
					ids.push( id.toString() );
					break;
				case 'delete':
					delete ids.splice( ids.indexOf( id.toString() ), 1 );
					break;
				default:
					return ids;
					break;
			}

			$( obj ).val( ids.join( ',' ) );

			return ids;
		}

		/*
		* Gallery Modal
		*/
		var powerkitUIGalleryModal = function(){

			/*
			* Create a new media frame
			*/
			var galleryModal = wp.media( {
				title: $( '.pk-post-format-gallery-settings' ).data( 'title' ),
				button: {
					text: $( '.pk-post-format-gallery-settings' ).data( 'button' ),
				},
				library : { type : 'image' },
				multiple: true
			} );

			// When an image is selected in the media frame...
			galleryModal.on( 'select', function() {
				var attachments = galleryModal.state().get('selection').toJSON();

				for ( var prop in attachments ) {
					if ( typeof attachments[prop].id == 'undefined' ) {
						continue;
					}

					var attachment_id  = attachments[prop].id;
					var attachment_src = attachments[prop].url;

					// Check selected.
					if( typeof attachments[prop].selected != 'undefined' ) {
						continue;
					}

					// Set thumbnail src.
					if ( typeof attachments[prop].sizes.thumbnail != 'undefined' ) {
						attachment_src = attachments[prop].sizes.thumbnail.url;
					}

					// Add id to selected.
					powerkitUIGalleryIds( 'add', attachment_id );

					// Add attachment.
					var attachment = $( '<div class="pk-post-format-gallery-attachment"></div>' ).appendTo( '.pk-post-format-gallery-attachments' );

					$( attachment ).attr( 'data-id', attachment_id );
					$( attachment ).append( '<input name="pk-post-format-gallery[]" type="hidden">' );
					$( attachment ).find( 'input' ).attr( 'value', attachment_id );
					$( attachment ).append( '<div class="thumbnail"><img></div>' );
					$( attachment ).find( 'img' ).attr( 'src', attachment_src );
					$( attachment ).append( '<div class="actions"><a href="#" class="pk-post-format-gallery-remove"></div>' );
					$( attachment ).find( '.actions a' ).attr( 'data-id', attachment_id );
				}

				galleryModal.close();
			});


			/*
			* Library handler
			*/
			var galleryLibrary = wp.media.view.Attachment.Library;

			wp.media.view.Attachment.Library = galleryLibrary.extend({
				render: function() {

					// vars
					var selected = powerkitUIGalleryIds();
					var id = this.model.attributes.id;

					// select
					if( selected && id && selected.indexOf( id.toString() ) > -1 ) {
						this.model.attributes.selected = true;
						this.$el.addClass( 'pk-post-format-selected' );
					}

					return galleryLibrary.prototype.render.apply( this, arguments );
				}
			});

			/*
			* Open modal.
			*/
			galleryModal.open();
		}

		/*
		* Gallery Sortable
		*/
		$( '.pk-post-format-gallery-attachments' ).sortable({
			placeholder: 'pk-post-format-highlight'
		});
		$( '.pk-post-format-gallery-attachments' ).disableSelection();

		/*
		* Gallery Modal Open
		*/
		$( document ).on( 'click', '.pk-post-format-gallery-add', function( event ){
			powerkitUIGalleryModal();

			return false;
		});

		/*
		* Gallery Remove Attachment
		*/
		$( document ).on( 'click', '.pk-post-format-gallery-remove', function( event ){
			var id = $( this ).data( 'id' );

			$( this ).closest( '.pk-post-format-gallery-attachment' ).remove();

			powerkitUIGalleryIds( 'delete', id );

			return false;
		});

		/*
		* Gallery Open Info
		*/
		$( document ).on( 'click', '.pk-post-format-gallery-attachment', function( event ){
			if ( 'none' === $( '.pk-post-format-gallery-side-inner' ).css( 'display' ) ) {
				$( '.pk-post-format-gallery-side-inner' ).animate( { width: 'toggle' } );
			}

			// Get Ajax.
			jQuery.post( powerkit_post_format_ui.url, {
				id: $( this ).data( 'id' ),
				nonce: powerkit_post_format_ui.nonce,
				action: 'pk_gallery_attachment'
			}, function( response ) {
				$( '.pk-post-format-gallery-side-data' ).html( response );
			});

			$( this ).siblings().removeClass(  'pk-post-format-selected' );
			$( this ).addClass( 'pk-post-format-selected' ).parent().addClass( 'pk-post-format-open' );

			return false;
		});

		/*
		* Gallery Update Info
		*/
		$( document ).on( 'click', '.pk-post-format-gallery-update', function( event ){
			var data = $( '.pk-post-format-gallery-side-data' ).find( ':input' ).serialize();

			// Update Ajax.
			$( '.pk-post-format-gallery-side-toolbar .spinner' ).addClass( 'is-active' );

			jQuery.post( powerkit_post_format_ui.url, data, function( response ) {
				$( '.pk-post-format-gallery-side-toolbar .spinner' ).removeClass( 'is-active' );
			});

			return false;
		});

		/*
		* Gallery Close Info
		*/
		$( document ).on( 'click', '.pk-post-format-gallery-close', function( event ){
			if ( 'none' !== $( '.pk-post-format-gallery-side-inner' ).css( 'display' ) ) {
				$( '.pk-post-format-gallery-side-inner' ).animate( { width: 'toggle' } );
			}

			$( '.pk-post-format-gallery-side-data' ).html( '<span class="spinner"></span>' );

			$( '*' ).removeClass( 'pk-post-format-open pk-post-format-selected' );

			return false;
		});

	} );

} )( jQuery );
