<?php
/**
 * Social Links block template
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
	'title'    => esc_html__( 'Social Links', 'powerkit' ),
	'template' => $attributes['template'],
	'align'    => isset( $attributes['aligning'] ) ? $attributes['aligning'] : 'default',
	'scheme'   => 'gutenberg-block',
	'maximum'  => $attributes['count'],
	'cache'    => true,
	'labels'   => $attributes['showLabels'],
	'titles'   => $attributes['showTitles'],
	'counts'   => $attributes['showCounts'],
	'mode'     => apply_filters( 'powerkit_social_links_counter_mode', 'mixed' ),
);

echo '<div class="' . esc_attr( $attributes['className'] ) . '" ' . ( isset( $attributes['anchor'] ) ? ' id="' . esc_attr( $attributes['anchor'] ) . '"' : '' ) . '>';

powerkit_social_links_appearance( $params );

echo '</div>';
