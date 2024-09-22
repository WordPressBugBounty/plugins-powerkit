/**
 * Global Powerkit Scripts
 */

jQuery( document ).ready( function( $ ) {

	// ToolTip.
	$( '.pk-tippy' ).each(function( index, element ) {
		tippy( element, {
			arrow: true,
			interactive: true,
			placement: 'bottom',
			content: $( element ).find( '.pk-alert' ).html()
		});
	});

} );
