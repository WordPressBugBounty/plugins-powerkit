<?php
/**
 * Helpers Retina Images
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Get wp register sizes of images
 */
function powerkit_retina_images_get_register_sizes() {
	// Get Image sizes.
	$default_sizes = array( 'thumbnail', 'medium', 'medium_large', 'large' );
	$images_sizes  = wp_get_additional_image_sizes();

	// Default Image Sizes.
	foreach ( $default_sizes as $_size ) {
		$images_sizes[ $_size ] = array(
			'width'  => get_option( "{$_size}_size_w" ),
			'height' => get_option( "{$_size}_size_h" ),
			'crop'   => (bool) get_option( "{$_size}_crop" ),
		);
	}

	// Exclude unnecessary.
	foreach ( $images_sizes as $_size => $size_data ) {
		if ( preg_match( '/pk-lqip/', $_size ) ) {
			unset( $images_sizes[ $_size ] );
		}
	}

	return $images_sizes;
}
