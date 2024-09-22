"use strict";

var powerkit_basic_shortcodes;
var powerkit_basic_shortcodes_content;

( function() {
	tinymce.create( 'tinymce.plugins.powerkit_basic_shortcodes', {
		init: function( ed, url ) {
			ed.addButton( 'powerkit_basic_shortcodes_button', {
				title: 'Basic Shortcodes',
				image: url.substring( 0, url.length - 3 ) + '/images/icon.png',
				onclick: function() {

					powerkit_basic_shortcodes = ed.selection;
					powerkit_basic_shortcodes_content = ed.selection.getContent();

					var width = jQuery( window ).width(),
							H = jQuery( window ).height(),
							W = ( 720 < width ) ? 720 : width;
						W = W - 80;
						H = H - 84;


					var shortcodes_loaded = jQuery( '#powerkit_basic_shortcodes_holder' ).length;

					if ( shortcodes_loaded ) {

						tb_show( 'Basic Shortcodes', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=powerkit_basic_shortcodes' );
						jQuery( '#TB_window' ).addClass( 'powerkit_basic_shortcodes_window' );

					} else {

						jQuery( "body" ).append( '<div id="powerkit_basic_shortcodes_holder" style="display: none;"><div id="powerkit_basic_shortcodes"></div></div>' );

						jQuery.get( 'admin-ajax.php?action=powerkit_basic_shortcodes_sections', function( data ) {
							jQuery( '#powerkit_basic_shortcodes' ).html( data );
							tb_show( 'Basic Shortcodes', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=powerkit_basic_shortcodes' );
							jQuery( '#TB_window' ).addClass( 'powerkit_basic_shortcodes_window' );
						} );
					}
				}
			} );
		},
		createControl: function( n, cm ) {
			return null;
		},
	} );
	tinymce.PluginManager.add( 'powerkit_basic_shortcodes', tinymce.plugins.powerkit_basic_shortcodes );
} )();