<?php
/**
 * Twitter block template
 *
 * @var        $attributes - block attributes
 * @var        $options - layout options
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    PowerKit
 * @subpackage PowerKit/templates
 */

$params = array(
	'title'       => esc_html__( 'Twitter Feed', 'powerkit' ),
	'number'      => $attributes['number'],
	'header'      => $attributes['showHeader'],
	'button'      => $attributes['showFollowButton'],
	'template'    => 'default',
	'is_block'    => true,
	'block_attrs' => $attributes,
);

echo '<div class="' . esc_attr( $attributes['className'] ) . '" ' . ( isset( $attributes['anchor'] ) ? ' id="' . esc_attr( $attributes['anchor'] ) . '"' : '' ) . '>';

// Twitter output.
powerkit_twitter_get_recent( $params );

echo '</div>';
