<?php
/**
 * Shortcode Share Buttons
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    PowerKit
 * @subpackage PowerKit/shortcodes
 */

/**
 * Share Buttons Shortcode
 *
 * @param array  $atts      User defined attributes in shortcode tag.
 * @param string $content   Shorcode tag content.
 * @return string           Shortcode result HTML.
 */
function powerkit_share_buttons_shortcode( $atts, $content = '' ) {

	$params = powerkit_shortcode_atts( shortcode_atts( array(
		'accounts'       => '',
		'total'          => true,
		'icons'          => true,
		'labels'         => true,
		'counts'         => true,
		'titles'         => false,
		'title_location' => 'inside',
		'label_location' => 'inside',
		'count_location' => 'inside',
		'mode'           => 'mixed',
		'layout'         => 'default',
		'scheme'         => 'default',
	), $atts ) );

	$params['total']  = filter_var( $params['total'], FILTER_VALIDATE_BOOLEAN );
	$params['labels'] = filter_var( $params['labels'], FILTER_VALIDATE_BOOLEAN );
	$params['counts'] = filter_var( $params['counts'], FILTER_VALIDATE_BOOLEAN );

	ob_start();

	// Accounts.
	if ( $params['accounts'] ) {
		$params['accounts'] = explode( ',', $params['accounts'] );

		if ( $params['accounts'] ) {
			foreach ( $params['accounts'] as $key => $val ) {
				$params['accounts'][ $key ] = trim( $val );
			}
		}
	}

	// Get Shares.
	powerkit_share_buttons( $params['accounts'], $params['total'], $params['icons'], $params['titles'], $params['labels'], $params['counts'], $params['title_location'], $params['label_location'], $params['count_location'], $params['mode'], $params['layout'], $params['scheme'], '' );

	return ob_get_clean();
}
add_shortcode( 'powerkit_share_buttons', 'powerkit_share_buttons_shortcode' );
