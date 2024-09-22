/**
 * Post Views Reset.
 */

 ( function( $ ) {

	$( document ).ready( function() {
		setTimeout(function(){
			$.post( pkPostViews.ajaxurl, {
				'_wpnonce': pkPostViews.nonce,
				'post_id' : pkPostViews.post_id,
			} );
		}, 1500);
	} );

} )( jQuery );
