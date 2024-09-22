<?php
/**
 * Helpers Lazy Load
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Generation placeholder.
 *
 * @param int  $width  Width of image.
 * @param int  $height Height of image.
 * @param bool $cached Сache the result.
 */
function powerkit_lazy_get_image_placeholder( $width = 1, $height = 1, $cached = false ) {

	$transient = sprintf( 'pk_image_placeholder_%s_%s', $width, $height );

	$placeholder_image = $cached ? get_transient( $transient ) : false;

	if ( false === $placeholder_image ) {

		if ( function_exists( 'imagecreate') ) {
			$placeholder_code = ob_start();

			$image      = imagecreate( $width, $height );
			$background = imagecolorallocatealpha( $image, 0, 0, 255, 127 );

			imagepng( $image, null, 9 );
			imagecolordeallocate( $image, $background );
			imagedestroy( $image );

			$placeholder_code = ob_get_clean();

			$placeholder_image = 'data:image/png;base64,' . base64_encode( $placeholder_code );

		} else {
			$placeholder_image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAP+KeNJXAAAAAXRSTlMAQObYZgAAAAlwSFlzAAAOxAAADsQBlSsOGwAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=';
		}

		if ( $cached ) {
			set_transient( $transient, $placeholder_image );
		}
	}

	return $placeholder_image;
}

/**
 * Get the available image sizes
 */
function powerkit_lazy_get_available_image_sizes() {
	$wais = & $GLOBALS['_wp_additional_image_sizes'];

	$sizes       = array();
	$image_sizes = get_intermediate_image_sizes();

	if ( is_array( $image_sizes ) && $image_sizes ) {
		foreach ( $image_sizes as $size ) {
			if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ), true ) ) {
				$sizes[ $size ] = array(
					'width'  => get_option( "{$size}_size_w" ),
					'height' => get_option( "{$size}_size_h" ),
					'crop'   => (bool) get_option( "{$size}_crop" ),
				);
			} elseif ( isset( $wais[ $size ] ) ) {
				$sizes[ $size ] = array(
					'width'  => $wais[ $size ]['width'],
					'height' => $wais[ $size ]['height'],
					'crop'   => $wais[ $size ]['crop'],
				);
			}

			// Size registered, but has 0 width and height.
			if ( 0 === (int) $sizes[ $size ]['width'] || 0 === (int) $sizes[ $size ]['height'] ) {
				unset( $sizes[ $size ] );
			}
		}
	}

	return $sizes;
}

/**
 * Gets the data of a specific image size.
 *
 * @param string $size Name of the size.
 */
function powerkit_lazy_get_image_size( $size ) {
	if ( ! is_string( $size ) ) {
		return;
	}

	$sizes = powerkit_lazy_get_available_image_sizes();

	return isset( $sizes[ $size ] ) ? $sizes[ $size ] : false;
}

/**
 * Tries to convert an attachment IMG attr into a post object.
 *
 * @param string $attr The img attr.
 */
function powerkit_lazy_attachment_attr_to_object( $attr ) {
	if ( ! isset( $attr['src'] ) ) {
		return;
	}

	// Set ID by class.
	if ( isset( $attr['class'] ) && preg_match( '/wp-image-(\d*)/i', $attr['class'], $matche ) ) {
		return array(
			'ID' => $matche[1],
		);
	}

	// Remove the thumbnail size.
	$src = preg_replace( '~-[0-9]+x[0-9]+(?=\..{2,6})~', '', $attr['src'] );

	// Set ID by src.
	return array(
		'ID' => attachment_url_to_postid( $src ),
	);
}

/**
 * Tries to convert an attachment IMG attr into a image size.
 *
 * @param string $attr    The img attr.
 * @param string $content The all content.
 */
function powerkit_attachment_attr_to_size( $attr, $content = false ) {
	if ( ! isset( $attr['class'] ) ) {
		return;
	}

	// Set ID by class.
	if ( preg_match( '/size-(\S*)/i', $attr['class'], $matche ) ) {
		return $matche[1];
	}

	// Set ID by parent class.
	if ( isset( $attr['src'] ) && $attr['src'] ) {
		$clear_content = str_replace( array( "\r\n", "\r", "\n" ), '', $content );

		if ( preg_match( '#<figure class="[^>]*size-(\S*)">[^<]*<img[^>]*src="' . $attr['src'] . '"[^>]*>#', $clear_content, $matche ) ) {
			return $matche[1];
		}
	}
}
