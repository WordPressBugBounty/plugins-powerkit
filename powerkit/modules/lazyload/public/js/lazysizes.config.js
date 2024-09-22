window.lazySizesConfig = window.lazySizesConfig || {};

// Set Classes.
window.lazySizesConfig.lazyClass    = 'pk-lazyload';
window.lazySizesConfig.loadedClass  = 'pk-lazyloaded';
window.lazySizesConfig.preloadClass = 'pk-lazypreload';
window.lazySizesConfig.loadingClass = 'pk-lazyloading';

// Set Attrs.
window.lazySizesConfig.srcAttr    = 'data-pk-src';
window.lazySizesConfig.srcsetAttr = 'data-pk-srcset';
window.lazySizesConfig.sizesAttr  = 'data-pk-sizes';

// Set sizes if the image is not width.
document.addEventListener( 'lazyloaded', function (e) {
	if ( ! e.target.getAttribute( 'width' ) ) {
		e.target.setAttribute( 'sizes', e.target.getAttribute( 'data-ls-sizes' ) );
	}
} );
