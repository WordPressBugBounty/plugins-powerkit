/**
 * Posts
 */
( function( $ ) {

	function powerkitInitPostsMasonry() {
		$( '.pk-block-posts-layout-masonry:not(.pk-block-posts-layout-masonry-colcade-ready)' )
			.addClass( 'pk-block-posts-layout-masonry-colcade-ready' )
			.each( function() {
				new Colcade( this, {
					columns: '.pk-block-post-grid-col',
					items: '.pk-block-post-grid-item'
				});
			} );
	}

	$( document ).ready( function() {
		powerkitInitPostsMasonry();
		$( document.body ).on( 'post-load', function() {
			powerkitInitPostsMasonry();
		} );
	} );

} )( jQuery );
