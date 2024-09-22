<?php
/**
 * Helpers Lightbox
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Lightbox process selectors
 *
 * @param string $selectors List selectors.
 */
function powerkit_lightbox_process_selectors( $selectors = array() ) {

	if ( ! $selectors ) {
		$selectors = array();
	}

	if ( is_string( $selectors ) ) {
		$selectors = str_replace( "\r\n", ',', $selectors );

		$selectors = explode( ',', $selectors );
	}

	$selectors = array_filter( $selectors, 'strlen' );

	return $selectors;
}
