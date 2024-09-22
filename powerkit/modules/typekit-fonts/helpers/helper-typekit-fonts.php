<?php
/**
 * Helpers Typekit Fonts
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Convert variant slug to font data.
 *
 * @param string $variant The variant slug.
 * @param bool   $compact The style data return.
 */
function powerkit_typekit_font_convert_format( $variant, $compact = false ) {

	$format = array(
		'n1' => array(
			'weight' => '100',
			'style'  => 'normal',
		),
		'i1' => array(
			'weight' => '100',
			'style'  => 'italic',
		),
		'n2' => array(
			'weight' => '200',
			'style'  => 'normal',
		),
		'i2' => array(
			'weight' => '200',
			'style'  => 'italic',
		),
		'n3' => array(
			'weight' => '300',
			'style'  => 'normal',
		),
		'i3' => array(
			'weight' => '300',
			'style'  => 'italic',
		),
		'n4' => array(
			'weight' => 'normal',
			'style'  => 'normal',
		),
		'i4' => array(
			'weight' => 'normal',
			'style'  => 'italic',
		),
		'n5' => array(
			'weight' => '500',
			'style'  => 'normal',
		),
		'i5' => array(
			'weight' => '500',
			'style'  => 'italic',
		),
		'n6' => array(
			'weight' => '600',
			'style'  => 'normal',
		),
		'i6' => array(
			'weight' => '600',
			'style'  => 'italic',
		),
		'n7' => array(
			'weight' => '700',
			'style'  => 'normal',
		),
		'i7' => array(
			'weight' => '700',
			'style'  => 'italic',
		),
		'n8' => array(
			'weight' => '800',
			'style'  => 'normal',
		),
		'i8' => array(
			'weight' => '800',
			'style'  => 'italic',
		),
		'n9' => array(
			'weight' => '900',
			'style'  => 'normal',
		),
		'i9' => array(
			'weight' => '900',
			'style'  => 'italic',
		),
	);

	if ( isset( $format[ $variant ] ) ) {

		if ( $compact ) {
			return $format[ $variant ]['weight'] . $format[ $variant ]['style'];
		} else {
			return $format[ $variant ];
		}
	}

	return $variant;
}

/**
 * Registers font variations format.
 *
 * @param array $variations If you want to return a specific option.
 * @return array
 */
function powerkit_typekit_font_variations_format( $variations = array() ) {

	if ( $variations && isset( $variations ) ) {
		foreach ( $variations as $key => $item ) {
			$format_compact = powerkit_typekit_font_convert_format( $item, true );

			$format_compact = preg_replace( '/normalnormal/', 'regular', $format_compact );
			$format_compact = preg_replace( '/normalitalic/', 'italic', $format_compact );
			$format_compact = preg_replace( '/(\d*)normal/', '$1', $format_compact );

			$variations[ $key ] = $format_compact;
		}
		return $variations;
	}

	return $format;
}
